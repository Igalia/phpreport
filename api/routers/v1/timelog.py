from typing import List
from datetime import date
from fastapi import APIRouter, Depends, Query
from sqlalchemy.orm import Session

from schemas.timelog import TaskTypeItem, Template as TemplateSchema, Task as TaskSchema
from services.timelog import TaskTypeService, TemplateService, TaskService

from db.db_connection import get_db
from auth.auth_bearer import BearerToken

router = APIRouter(prefix="/timelog", tags=["timelog"])


@router.get("/task_types/", dependencies=[Depends(BearerToken())], response_model=List[TaskTypeItem])
async def get_task_types(db: Session = Depends(get_db), skip: int = 0, limit: int = 100):
    items = TaskTypeService(db).get_items()
    return items


@router.get("/templates/{user_id}", dependencies=[Depends(BearerToken())], response_model=List[TemplateSchema])
async def get_user_templates(user_id: int, db: Session = Depends(get_db)):
    templates = TemplateService(db).get_user_templates(user_id)
    return templates


@router.get("/tasks", dependencies=[Depends(BearerToken())], response_model=List[TaskSchema])
async def get_user_tasks(
    user_id: int,
    db: Session = Depends(get_db),
    offset: int = Query(0, ge=0),
    limit: int = Query(25, ge=0, le=500),
    start: date = date.today(),
    end: date = date.today(),
):
    tasks = TaskService(db).get_user_tasks(user_id, offset, limit, start, end)
    return tasks
