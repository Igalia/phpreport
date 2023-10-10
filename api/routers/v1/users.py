from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from db.db_connection import get_db
from auth.auth_bearer import BearerToken
from dependencies import get_current_user
from schemas.user import AppUser

router = APIRouter(
    prefix="/users",
    tags=["users"],
    responses={403: {"description": "Forbidden"}, 404: {"description": "Not found"}},
    dependencies=[Depends(BearerToken())],
)


@router.get("/users/me", response_model=AppUser)
async def get_current_user_profile(current_user=Depends(get_current_user), db: Session = Depends(get_db)):
    return current_user
