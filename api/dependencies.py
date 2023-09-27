from typing import Annotated, Optional, List
from fastapi import Depends, HTTPException
from sqlalchemy.orm import Session
from fastapi.security import OAuth2PasswordBearer
from pydantic import BaseModel
from decouple import config
from db.db_connection import get_db
from auth.auth_handler import decode_token
from services.user import UserService


OIDC_TOKEN_ENDPOINT = config("OIDC_TOKEN_ENDPOINT")
OIDC_USERNAME_PROPERTY = config("OIDC_USERNAME_PROPERTY")
USE_OIDC_ROLES = config("USE_OIDC_ROLES")
OIDC_ROLES_PROPERTY = config("OIDC_ROLES_PROPERTY")

oauth2_scheme = OAuth2PasswordBearer(tokenUrl=OIDC_TOKEN_ENDPOINT)


class AppUser(BaseModel):
    id: Optional[int]
    username: Optional[str]
    email: Optional[str]
    first_name: Optional[str]
    last_name: Optional[str]
    roles: Optional[List[str]]


async def get_current_user(token: Annotated[str, Depends(oauth2_scheme)], db: Session = Depends(get_db)):
    decoded = decode_token(token)
    user_in_db = UserService(db).get_user(decoded[OIDC_USERNAME_PROPERTY])
    if not user_in_db:
        raise HTTPException(status_code=401, detail="You are not an authorized user.")
    user = AppUser(
        id=user_in_db.id,
        email=decoded["email"],
        first_name=decoded["given_name"],
        last_name=decoded["family_name"],
        roles=[],
    )
    if USE_OIDC_ROLES:
        user.roles = decoded[OIDC_ROLES_PROPERTY].copy()
    else:
        user_roles = UserService(db).get_user_roles(user.id)
        for user_role in user_roles:
            user.roles.append(user_role.role.name)
    return user
