from typing import List
from datetime import date
from fastapi import APIRouter, Depends, Query
from sqlalchemy.orm import Session

from schemas.timelog import (
    TaskTypeItem,
    Template as TemplateSchema,
    TemplateNew as TemplateNewSchema,
    Task as TaskSchema,
)
from services.timelog import TaskTypeService, TemplateService, TaskService

from db.db_connection import get_db
from auth.auth_bearer import BearerToken

router = APIRouter(prefix="/timelog", tags=["timelog"], responses={"404": {"description": "Not found"}})


@router.get("/task_types/", dependencies=[Depends(BearerToken())], response_model=List[TaskTypeItem])
async def get_task_types(db: Session = Depends(get_db), skip: int = 0, limit: int = 100):
    items = TaskTypeService(db).get_items()
    return items


@router.get("/templates/{user_id}", dependencies=[Depends(BearerToken())], response_model=List[TemplateSchema])
async def get_user_templates(user_id: int, db: Session = Depends(get_db)):
    templates = TemplateService(db).get_user_templates(user_id)
    return templates


@router.post("/templates", dependencies=[Depends(BearerToken())], response_model=TemplateSchema, status_code=201)
async def add_template(template: TemplateNewSchema, db: Session = Depends(get_db)):
    """
    Create a template with all the information:

    - **name***: each template must have a name
    - **story**: the task story
    - **description**: the task description
    - **task type**: the task type
    - **init time**: the task start time
    - **end time**: the task end time
    - **user id**: the user id (global templates should leave this null; user template should fill)
    - **project id**: the project id
    - **is global***: whether or not this template is global for all users (required)
    \f
    :param item: User input.
    """
    result = TemplateService(db).create_template(template)
    return result


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
