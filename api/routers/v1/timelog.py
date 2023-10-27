from typing import List
from datetime import date
from fastapi import APIRouter, Depends, Query, HTTPException
from fastapi.encoders import jsonable_encoder
from sqlalchemy.orm import Session

from schemas.timelog import (
    TaskTypeItem,
    Template as TemplateSchema,
    TemplateNew as TemplateNewSchema,
    TemplateUpdate as TemplateUpdateSchema,
    Task as TaskSchema,
    TaskNew as TaskNewSchema,
    TaskUpdate as TaskUpdateSchema,
)
from schemas.validation import ValidatedObject
from services.timelog import TaskTypeService, TemplateService, TaskService
from services.projects import ProjectService
from services.config import ConfigService
from db.db_connection import get_db
from auth.auth_bearer import BearerToken
from dependencies import get_current_user, AppUser
from helpers.time import time_string_to_int

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
    Create a user or global template. Required fields are: `name` and `is_global`.
    In case the template is not global, the `user_id` field is also mandatory.
    \nBoth `start` and `end` times are in 24h time notation (HH:mm).
    """
    validated_template = validate_template(template, db, current_user)
    if not validated_template.is_valid:
        raise HTTPException(status_code=422, detail=validated_template.message)
    result = TemplateService(db).create_template(template)
    return result


@router.put("/templates/{template_id}", response_model=TemplateSchema)
async def update_template(
    template_id: int,
    template: TemplateUpdateSchema,
    current_user=Depends(get_current_user),
    db: Session = Depends(get_db),
):
    """
    Update a template. Required fields are: `name` and `is_global`.
    In case the template is not global, the `user_id` field is also mandatory.
    \nBoth `start` and `end` times are in 24h time notation (HH:mm).
    """
    existing_template = TemplateService(db).get_template(template_id)
    if not existing_template:
        raise HTTPException(status_code=404, detail=f"Template with id {template_id} not found")
    validated_template = validate_template(template, db, current_user)
    if not validated_template.is_valid:
        raise HTTPException(status_code=422, detail=validated_template.message)
    result = TemplateService(db).update_template(existing_template, template)
    return result


@router.delete("/templates/{template_id}", status_code=204)
async def delete_template(template_id: int, current_user=Depends(get_current_user), db: Session = Depends(get_db)):
    template = TemplateService(db).get_template(template_id)
    if not template:
        raise HTTPException(status_code=404, detail=f"Template with id {template.id} not found.")
    if template.is_global and "manager" not in current_user.roles:
        raise HTTPException(status_code=403, detail="You are not authorized to delete global templates")
    else:
        if template.user_id and template.user_id != current_user.id:
            raise HTTPException(status_code=403, detail="You are not authorized to delete templates for this user")
    TemplateService(db).delete_template(template_id)
    return


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
    Create a user task. Required fields are: `user_id`, `date`, `project_id`, `start_time` and `end_time`.
    \nBoth `start` and `end` times are in 24h time notation (HH:mm).
    """
    if current_user.id != task.user_id:
        raise HTTPException(status_code=403, detail="You are not authorized to create tasks for this user.")
    validated_task = validate_task(task, db)
    if not validated_task.is_valid:
        raise HTTPException(status_code=422, detail=validated_task.message)
    result = TaskService(db).create_task(task)
    return result


@router.put("/tasks/{task_id}", response_model=TaskSchema)
async def update_task(
    task_id: int, task: TaskUpdateSchema, current_user=Depends(get_current_user), db: Session = Depends(get_db)
):
    """
    Update a user task. Required fields are: `user_id`, `date`, `project_id`, `start_time` and `end_time`.
    \nBoth `start` and `end` times are in 24h time notation (HH:mm).
    """
    existing_task = TaskService(db).get_task(task_id)
    if not existing_task:
        raise HTTPException(status_code=404, detail=f"Task with id {task_id} not found")
    if current_user.id != existing_task.user_id:
        raise HTTPException(status_code=403, detail="You are not authorized to update tasks for this user.")
    if task.start_time:
        task.init = time_string_to_int(task.start_time)
    if task.end_time:
        task.end = time_string_to_int(task.end_time)
    existing_data = jsonable_encoder(existing_task)
    update_data = task.model_dump(exclude_unset=True)
    for field in existing_data:
        if field in update_data:
            setattr(existing_task, field, update_data[field])
    validated_task = validate_task(existing_task, db)
    if not validated_task.is_valid:
        raise HTTPException(status_code=422, detail=validated_task.message)
    result = TaskService(db).update_task(existing_task, task)
    return result


@router.delete("/tasks/{task_id}", status_code=204)
async def delete_task(task_id: int, current_user=Depends(get_current_user), db: Session = Depends(get_db)):
    task = TaskService(db).get_task(task_id)
    if not task:
        raise HTTPException(status_code=404, detail=f"Task with id {task_id} not found")
    if current_user.id != task.user_id:
        raise HTTPException(status_code=403, detail="You are not authorized to delete tasks for this user")
    TaskService(db).delete_task(task_id)
    return


def validate_task(task_to_validate: TaskSchema, db: Session):
    validated = ValidatedObject(is_valid=False, message="")
    user_can_create_tasks = ConfigService(db).can_user_edit_task(task_to_validate.date)
    if not user_can_create_tasks:
        validated.message += "You cannot create or edit a task for this date - it is outside the allowed range."
        return validated
    if task_to_validate.task_type == "":
        task_to_validate.task_type = None
    if task_to_validate.task_type:
        task_type_valid = TaskTypeService(db).slug_is_valid(task_to_validate.task_type)
        if not task_type_valid:
            validated.message += f"Task type {task_to_validate.task_type} does not exist."
            return validated
    project_active = ProjectService(db).is_project_active(task_to_validate.project_id)
    if not project_active:
        validated.message += "You cannot add a task for an inactive project."
        return validated
    overlapping_tasks = TaskService(db).check_task_for_overlap(task_to_validate)
    if not overlapping_tasks.is_valid:
        validated.message += overlapping_tasks.message
        return validated
    validated.is_valid = True
    return validated


def validate_template(template: TemplateSchema, db: Session, current_user: AppUser):
    validated = ValidatedObject(is_valid=False, message="")
    if template.is_global and "manager" not in current_user.roles:
        raise HTTPException(status_code=403, detail="You are not authorized to create or update global templates")
    else:
        if template.user_id and template.user_id != current_user.id:
            raise HTTPException(
                status_code=403, detail="You are not authorized to create or update templates for this user"
            )
    if template.task_type:
        task_type_valid = TaskTypeService(db).slug_is_valid(template.task_type)
        if not task_type_valid:
            validated.message += f"Task type {template.task_type} does not exist."
            return validated
    if template.project_id:
        project_active = ProjectService(db).is_project_active(template.project_id)
        if not project_active:
            validated.message += "You cannot add a template for an inactive project."
            return validated
    validated.is_valid = True
    return validated
