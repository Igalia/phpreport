from typing import List, Optional
from pydantic import ConfigDict, BaseModel


class User(BaseModel):
    id: int
    login: str
    model_config = ConfigDict(from_attributes=True)


class UserGroup(BaseModel):
    id: int
    name: str
    model_config = ConfigDict(from_attributes=True)


class UserRoles(BaseModel):
    role: UserGroup
    model_config = ConfigDict(from_attributes=True)


class AppUser(BaseModel):
    id: Optional[int] = None
    username: Optional[str] = None
    email: Optional[str] = None
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    roles: Optional[List[str]] = None
