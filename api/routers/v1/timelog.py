from typing import List
from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from schemas.timelog import TaskTypeItem, Template as TemplateSchema
from services.timelog import TaskTypeService, TemplateService

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
