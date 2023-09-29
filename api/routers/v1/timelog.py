from typing import List
from datetime import date
from fastapi import APIRouter, Depends, Query, HTTPException
from sqlalchemy.orm import Session

from schemas.timelog import (
    TaskTypeItem,
    Template as TemplateSchema,
    TemplateNew as TemplateNewSchema,
    Task as TaskSchema,
    TaskNew as TaskNewSchema,
    TaskValidate,
)
from services.timelog import TaskTypeService, TemplateService, TaskService
from services.projects import ProjectService
from services.config import ConfigService
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
async def get_task_types(
    current_user=Depends(get_current_user), db: Session = Depends(get_db), skip: int = 0, limit: int = 100
):
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
    - **start time**: the task start time
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


@router.post("/tasks", response_model=TaskSchema, status_code=201)
async def add_task(task: TaskNewSchema, current_user=Depends(get_current_user), db: Session = Depends(get_db)):
    """
    Create a task with the following data:

    - **user id**: the user id
    - **project_id**: the project the task is associated with
    - **story**: the task story
    - **description**: the task description
    - **task type**: the task type
    - **date**: the task date
    - **start time**: the task start time
    - **end time**: the task end time

    \f
    :param item: User input.
    """
    if current_user.id != task.user_id:
        raise HTTPException(status_code=403, detail="You are not authorized to create tasks for this user.")
    validated_task = validate_task(task, db)
    if not validated_task.is_task_valid:
        raise HTTPException(status_code=422, detail=validated_task.message)
    result = TaskService(db).create_task(task)
    return result


def validate_task(task: TaskSchema, db: Session):
    validated = TaskValidate(is_task_valid=False, message="")
    user_can_create_tasks = ConfigService(db).can_user_edit_task(task.date)
    if not user_can_create_tasks:
        validated.message += "You cannot create or edit a task for this date - it is outside the allowed range."
        return validated
    if task.task_type:
        task_type_valid = TaskTypeService(db).slug_is_valid(task.task_type)
        if not task_type_valid:
            validated.message += f"Task type {task.task_type} does not exist."
            return validated
    project_active = ProjectService(db).is_project_active(task.project_id)
    if not project_active:
        validated.message += "You cannot add a task for an inactive project."
        return validated
    overlapping_tasks = TaskService(db).check_task_for_overlap(task)
    if not overlapping_tasks.is_task_valid:
        validated.message += overlapping_tasks.message
        return validated
    validated.is_task_valid = True
    return validated
