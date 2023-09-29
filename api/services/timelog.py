from typing import List
from datetime import date
from sqlalchemy import or_

from services.main import AppService
from models.timelog import TaskType, Template, Task
from schemas.timelog import TemplateNew


class TaskTypeService(AppService):
    def get_items(self) -> List[TaskType]:
        task_types = self.db.query(TaskType).all() or []
        return task_types


class TemplateService(AppService):
    def get_user_templates(self, user_id: int) -> List[Template]:
        templates = (
            self.db.query(Template).filter(or_(Template.user_id == user_id, Template.is_global is True)).all() or []
        )
        return templates

    def create_template(self, template: TemplateNew) -> Template:
        new_template = Template(
            name=template.name,
            story=template.story,
            task_type=template.task_type,
            init_time=template.init_time,
            end_time=template.end_time,
            user_id=template.user_id,
            project_id=template.project_id,
            is_global=template.is_global,
        )
        self.db.add(new_template)
        self.db.commit()
        self.db.refresh(new_template)
        return new_template


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
