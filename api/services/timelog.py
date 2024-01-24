from typing import List
from datetime import date, datetime, timedelta
from sqlalchemy import or_, func
from fastapi.encoders import jsonable_encoder

from services.main import AppService
from models.timelog import TaskType, Template, Task
from models.config import Config
from schemas.timelog import TemplateNew, TemplateUpdate, TaskNew, TaskUpdate, ProjectTaskSummary
from schemas.validation import ValidatedObject


class TaskTypeService(AppService):
    def get_items(self, active) -> List[TaskType]:
        query = self.db.query(TaskType)
        if active:
            query = query.filter(TaskType.active)
        task_types = query.order_by(TaskType.name).all() or []
        return task_types

    def slug_is_valid(self, slug: str) -> bool:
        return any(self.db.query(TaskType).where(TaskType.slug == slug))


class TemplateService(AppService):
    def get_user_templates(self, user_id: int) -> List[Template]:
        templates = self.db.query(Template).filter(or_(Template.user_id == user_id, Template.is_global)).all() or []
        return templates

    def get_template(self, template_id: int) -> Template:
        template = self.db.query(Template).where(Template.id == template_id).first() or None
        return template

    def create_template(self, template: TemplateNew) -> Template:
        new_template = Template(
            name=template.name,
            story=template.story,
            description=template.description,
            task_type=template.task_type,
            init=template.init,
            end=template.end,
            user_id=template.user_id,
            project_id=template.project_id,
            is_global=template.is_global,
        )
        self.db.add(new_template)
        self.db.commit()
        self.db.refresh(new_template)
        return new_template

    def update_template(self, existing_template: Template, template_updates: TemplateUpdate) -> Template:
        existing_data = jsonable_encoder(existing_template)
        update_data = template_updates.model_dump(exclude_unset=True)
        for field in existing_data:
            if field in update_data:
                setattr(existing_template, field, update_data[field])
        self.db.add(existing_template)
        self.db.commit()
        self.db.refresh(existing_template)
        return existing_template

    def delete_template(self, template_id: int):
        template = self.get_template(template_id)
        self.db.delete(template)
        self.db.commit()


class TaskService(AppService):
    def get_user_tasks(self, user_id: int, offset: int, limit: int, start: date, end: date) -> List[Task]:
        tasks = (
            self.db.query(Task)
            .where(Task.user_id == user_id, Task.date.between(start, end))
            .offset(offset)
            .limit(limit)
            .all()
            or []
        )
        return tasks

    def get_task(self, task_id: int) -> Task:
        task = self.db.query(Task).where(Task.id == task_id).first() or None
        return task

    def create_task(self, task: TaskNew) -> Task:
        new_task = Task(
            date=task.date,
            init=task.init,
            end=task.end,
            story=task.story,
            description=task.description,
            task_type=task.task_type,
            updated_at=datetime.now(),
            user_id=task.user_id,
            project_id=task.project_id,
        )
        self.db.add(new_task)
        self.db.commit()
        self.db.refresh(new_task)
        return new_task

    def update_task(self, existing_task: Task, task_updates: TaskUpdate) -> Task:
        existing_data = jsonable_encoder(existing_task)
        update_data = task_updates.model_dump(exclude_unset=True)
        for field in existing_data:
            if field in update_data:
                setattr(existing_task, field, update_data[field])
        self.db.add(existing_task)
        self.db.commit()
        self.db.refresh(existing_task)
        return existing_task

    def delete_task(self, task_id: int):
        task = self.get_task(task_id)
        self.db.delete(task)
        self.db.commit()

    def check_task_for_overlap(self, task: Task) -> ValidatedObject:
        validated_task = ValidatedObject(is_valid=True, message="")
        user_tasks_for_day = self.db.query(Task).where(Task.user_id == task.user_id, Task.date == task.date)
        # if existing task is being validated, we don't want to check it for overlap with itself
        if hasattr(task, "id"):
            user_tasks_for_day = user_tasks_for_day.filter(Task.id != task.id).all() or []
        else:
            user_tasks_for_day = user_tasks_for_day.all() or []
        if len(user_tasks_for_day) <= 0:
            return validated_task
        for user_task_for_day in user_tasks_for_day:
            if task.init == user_task_for_day.init:
                validated_task.is_valid = False
                validated_task.message += f"You have already logged a task beginning at {user_task_for_day.start_time}."
            if task.end == user_task_for_day.end:
                validated_task.is_valid = False
                validated_task.message += f"You have already logged a task ending at {user_task_for_day.end_time}."
            if task.end > user_task_for_day.init and task.init < user_task_for_day.end:
                validated_task.is_valid = False
                validated_task.message += (
                    f"Task from {task.start_time} to {task.end_time} overlaps an existing task from"
                    f" {user_task_for_day.start_time} to {user_task_for_day.end_time}."
                )
        return validated_task

    def get_tasks_sum(self, user_id: int, start: date, end: date) -> int:
        task_sum = (
            self.db.query(func.sum(Task.task_total_minutes).label("task_sum"))
            .filter(Task.user_id == user_id, Task.date.between(start, end))
            .first()[0]
        )
        if task_sum is None:
            task_sum = 0
        return task_sum

    def get_task_totals_projects(self, user_id: int, current_date: date) -> List[ProjectTaskSummary]:
        totals = self.db.query(
            func.public.get_user_project_summaries(user_id, current_date).table_valued(
                "project_id", "project", "today_total", "today_text", "week_total", "week_text", "is_vacation"
            )
        ).all()
        project_totals_list = []

        for total in totals:
            project_totals_list.append(
                ProjectTaskSummary(
                    project_id=total[0],
                    project=total[1],
                    today_total=total[2] or 0,
                    today_text=total[3],
                    week_total=total[4] or 0,
                    week_text=total[5],
                    is_vacation=total[6],
                )
            )

        return project_totals_list

    def get_vacation_used(self, user_id: int, ref_date: date) -> int:
        config = self.db.query(Config).first()
        year_start = ref_date.replace(month=1, day=1)
        used = (
            self.db.query(func.sum(Task.task_total_minutes).label("vacation_used"))
            .filter(
                Task.user_id == user_id,
                Task.project_id == config.vacation_project_id,
                Task.date.between(year_start, ref_date),
            )
            .first()[0]
        )
        if used is None:
            used = 0
        return used

    def get_vacation_scheduled(self, user_id: int, ref_date: date) -> int:
        config = self.db.query(Config).first()
        tomorrow = ref_date + timedelta(days=1)
        yearEnd = ref_date.replace(month=12, day=31)
        scheduled = (
            self.db.query(func.sum(Task.task_total_minutes).label("vacation_sum"))
            .filter(
                Task.user_id == user_id,
                Task.project_id == config.vacation_project_id,
                Task.date.between(tomorrow, yearEnd),
            )
            .first()[0]
        )
        if scheduled is None:
            scheduled = 0
        return scheduled
