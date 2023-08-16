from typing import List
from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from models.project import Project
from schemas.project import Project as ProjectSchema
from services.projects import ProjectService

from db.db_connection import get_db
from auth.auth_bearer import BearerToken

router = APIRouter(prefix="/projects", tags=["projects"])


# TODO implement pagination
@router.get("/", dependencies=[Depends(BearerToken())], response_model=List[ProjectSchema])
async def get_projects(db: Session = Depends(get_db), skip: int = 0, limit: int = 100):
    return ProjectService(db).get_items()


@router.get("/{project_id}", dependencies=[Depends(BearerToken())], response_model=ProjectSchema)
async def get_project(project_id: int, db: Session = Depends(get_db)):
    return db.query(Project).filter(Project.id == project_id).first()
