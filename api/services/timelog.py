from typing import List
from datetime import date, datetime
from sqlalchemy import or_
from fastapi.encoders import jsonable_encoder

from services.main import AppService
from models.timelog import TaskType, Template, Task
from schemas.timelog import TemplateNew, TemplateUpdate, TaskNew, TaskUpdate
from schemas.validation import ValidatedObject


class TaskTypeService(AppService):
    def get_items(self) -> List[TaskType]:
        task_types = self.db.query(TaskType).all() or []
        return task_types

    def slug_is_valid(self, slug: str) -> bool:
        return any(self.db.query(TaskType).where(TaskType.slug == slug))


class TemplateService(AppService):
    def get_user_templates(self, user_id: int) -> List[Template]:
        templates = (
            self.db.query(Template).filter(or_(Template.user_id == user_id, Template.is_global is True)).all() or []
        )
        return templates

    def get_template(self, template_id: int) -> Template:
        template = self.db.query(Template).where(Template.id == template_id).first() or None
        return template

    def create_template(self, template: TemplateNew) -> Template:
        new_template = Template(
            name=template.name,
            story=template.story,
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
        update_data = template_updates.dict(exclude_unset=True)
        for field in existing_data:
            if field in update_data:
                setattr(existing_template, field, update_data[field])
        self.db.add(existing_template)
        self.db.commit()
        self.db.refresh(existing_template)
        return existing_template


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
        update_data = task_updates.dict(exclude_unset=True)
        for field in existing_data:
            if field in update_data:
                setattr(existing_task, field, update_data[field])
        self.db.add(existing_task)
        self.db.commit()
        self.db.refresh(existing_task)
        return existing_task

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
