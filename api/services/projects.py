from typing import List

from services.main import AppService
from models.project import Project


class ProjectService(AppService):
    def get_items(self) -> List[Project]:
        return self.db.query(Project).all() or []
