from typing import List
from datetime import date
from fastapi import APIRouter, Depends, Query, HTTPException
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
from dependencies import get_current_user

router = APIRouter(
    prefix="/timelog",
    tags=["timelog"],
    responses={403: {"description": "Forbidden"}, 404: {"description": "Not found"}},
    dependencies=[Depends(BearerToken())],
)


@router.get("/task_types/", response_model=List[TaskTypeItem])
async def get_task_types(db: Session = Depends(get_db), skip: int = 0, limit: int = 100):
    items = TaskTypeService(db).get_items()
    return items


@router.get("/templates", response_model=List[TemplateSchema])
async def get_user_templates(user_id: int, current_user=Depends(get_current_user), db: Session = Depends(get_db)):
    if user_id != current_user.id:
        raise HTTPException(status_code=403, detail="You are not authorized to see templates for this user")
    templates = TemplateService(db).get_user_templates(user_id)
    return templates


@router.post("/templates", response_model=TemplateSchema, status_code=201)
async def add_template(
    template: TemplateNewSchema, current_user=Depends(get_current_user), db: Session = Depends(get_db)
):
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
    if template.is_global and "manager" not in current_user.roles:
        raise HTTPException(status_code=403, detail="You are not authorized to create global templates")
    else:
        if template.user_id and template.user_id != current_user.id:
            raise HTTPException(status_code=403, detail="You are not authorized to create templates for this user")
    result = TemplateService(db).create_template(template)
    return result


@router.get("/tasks", response_model=List[TaskSchema])
async def get_user_tasks(
    user_id: int,
    current_user=Depends(get_current_user),
    db: Session = Depends(get_db),
    offset: int = Query(0, ge=0),
    limit: int = Query(25, ge=0, le=500),
    start: date = date.today(),
    end: date = date.today(),
):
    if current_user.id != user_id and "manager" not in current_user.roles:
        raise HTTPException(status_code=403, detail="You are not authorized to view tasks for this user")
    tasks = TaskService(db).get_user_tasks(user_id, offset, limit, start, end)
    return tasks
