from datetime import date
from typing import List
from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session

from models.project import Project
from schemas.project import Project as ProjectSchema, ProjectAllocationInDb, BaseProjectAllocation
from services.projects import ProjectService

from db.db_connection import get_db
from dependencies import get_current_user
from auth.auth_bearer import BearerToken

router = APIRouter(
    prefix="/projects",
    tags=["projects"],
    responses={
        status.HTTP_403_FORBIDDEN: {"description": "Forbidden"},
        status.HTTP_404_NOT_FOUND: {"description": "Not found"},
    },
    dependencies=[Depends(BearerToken())],
)


@router.get("/", response_model=List[ProjectSchema])
async def get_projects(db: Session = Depends(get_db), offset: int = 0, limit: int = 100, status: str = None):
    return ProjectService(db).get_items(offset, limit, status)


@router.get("/{project_id}", response_model=ProjectSchema)
async def get_project(project_id: int, db: Session = Depends(get_db)):
    return db.query(Project).filter(Project.id == project_id).first()


@router.get("/{project_id}/allocations")
async def get_project_allocations(
    project_id: int,
    start: date = date.today(),
    end: date = date.today(),
    current_user=Depends(get_current_user),
    db: Session = Depends(get_db),
):
    return ProjectService(db).get_project_allocations(project_id, start, end)


@router.get("/{project_id}/stats")
async def get_project_stats(
    project_id: int,
    start: date = date.today(),
    end: date = date.today(),
    current_user=Depends(get_current_user),
    db: Session = Depends(get_db),
):
    return ProjectService(db).get_project_stats(project_id, start, end)


@router.post(
    "/{project_id}/allocations",
    status_code=status.HTTP_201_CREATED,
    response_model=ProjectAllocationInDb,
)
async def add_project_allocations(
    project_id: int,
    allocation: BaseProjectAllocation,
    current_user=Depends(get_current_user),
    db: Session = Depends(get_db),
):
    """
    Create a project allocation. Required fields are: `projecId` that comes from the url,
    `userId`, `hoursPerDay`, `startDate`, and `endDate`.
    """
    # TODO we need to validate for overlapping, overbooking, etc
    result = ProjectService(db).create_project_allocation(allocation, current_user.username)
    return result
