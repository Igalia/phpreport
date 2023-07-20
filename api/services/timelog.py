from typing import List

from services.main import AppService
from models.timelog import TaskType


class TaskTypeService(AppService):
    def get_items(self) -> List[TaskType]:
        task_types = self.db.query(TaskType).all() or []
        return task_types
