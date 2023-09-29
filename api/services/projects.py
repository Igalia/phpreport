from typing import List

from services.main import AppService
from models.project import Project


class ProjectService(AppService):
    def get_items(self, offset, limit, status) -> List[Project]:
        query = self.db.query(Project)
        if status == "active":
            query = query.filter(Project.activation is True)
        if status == "inactive":
            query = query.filter(Project.activation is False)
        return query.offset(offset).limit(limit).all() or []

    def is_project_active(self, project_id) -> bool:
        project = self.db.query(Project).where(Project.id == project_id).first() or None
        if project is not None:
            return project.is_active
        return False
