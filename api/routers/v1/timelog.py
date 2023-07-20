from typing import List
from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from schemas.timelog import TaskTypeItem
from services.timelog import TaskTypeService

from db.db_connection import get_db
from auth.auth_bearer import BearerToken

router = APIRouter(prefix="/timelog", tags=["timelog"])


@router.get("/task_types/", dependencies=[Depends(BearerToken())], response_model=List[TaskTypeItem])
async def get_task_types(db: Session = Depends(get_db), skip: int = 0, limit: int = 100):
    items = TaskTypeService(db).get_items()
    return items
