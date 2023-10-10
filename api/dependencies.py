from typing import Annotated
from fastapi import Depends, HTTPException
from sqlalchemy.orm import Session
from fastapi.security import OAuth2PasswordBearer
from decouple import config
from db.db_connection import get_db
from auth.auth_handler import decode_token
from schemas.user import AppUser
from services.user import UserService


OIDC_TOKEN_ENDPOINT = config("OIDC_TOKEN_ENDPOINT")
OIDC_USERNAME_PROPERTY = config("OIDC_USERNAME_PROPERTY")
USE_OIDC_ROLES = config("USE_OIDC_ROLES")
OIDC_ROLES_PROPERTY = config("OIDC_ROLES_PROPERTY")

oauth2_scheme = OAuth2PasswordBearer(tokenUrl=OIDC_TOKEN_ENDPOINT)


async def get_current_user(token: Annotated[str, Depends(oauth2_scheme)], db: Session = Depends(get_db)) -> AppUser:
    decoded = decode_token(token)
    username = decoded[OIDC_USERNAME_PROPERTY]
    user_in_db = UserService(db).get_user(username=username)
    if not user_in_db:
        raise HTTPException(status_code=401, detail="You are not an authorized user.")
    user = AppUser(
        id=user_in_db.id,
        username=username,
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
