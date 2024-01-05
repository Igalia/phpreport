from typing import Annotated, List
from fastapi import Depends, HTTPException, status
from sqlalchemy.orm import Session
from fastapi.security import OAuth2PasswordBearer
from decouple import config
from db.db_connection import get_db
from auth.auth_handler import decode_token
from schemas.user import AppUser, UserCapacity
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
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="You are not an authorized user.",
        )
    user = AppUser(
        id=user_in_db.id,
        username=username,
        email=decoded["email"],
        first_name=decoded["given_name"],
        last_name=decoded["family_name"],
        roles=[],
        authorized_scopes=[],
        capacities=[],
    )
    for c in user_in_db.capacities:
        cap = UserCapacity(
            capacity=c.capacity, start=c.start, end=c.end, is_current=c.is_current, aggregates=c.aggregates
        )
        user.capacities.append(cap)

    if USE_OIDC_ROLES:
        user.roles = decoded[OIDC_ROLES_PROPERTY].copy()
        user.authorized_scopes = decoded["scopes"].copy()
    else:
        user_roles = UserService(db).get_user_roles(user.id)
        scopes = []
        for user_role in user_roles:
            user.roles.append(user_role.role.name)
            scopes += user_role.role.scopes.split(",")
        user.authorized_scopes = list(set(scopes))
    return user


class PermissionsValidator:
    def __init__(self, required_permissions: List[str]):
        self.required_permissions = required_permissions

    def __call__(
        self,
        current_user: Annotated[AppUser, Depends(get_current_user)],
    ):
        required_permissions_set = set(self.required_permissions)

        if not current_user.roles:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="You have not been assigned any roles in the application. Please speak to your sysadmin.",
            )

        if not any(x in required_permissions_set for x in current_user.authorized_scopes):
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="You do not have permission to perform this action.",
            )
