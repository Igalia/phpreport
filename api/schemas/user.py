from typing import List, Optional
from pydantic import ConfigDict, BaseModel
from datetime import date


class UserCapacity(BaseModel):
    capacity: Optional[float] = None
    start: Optional[date] = None
    end: Optional[date] = None
    is_current: Optional[bool] = None
    model_config = ConfigDict(from_attributes=True)


class User(BaseModel):
    id: int
    login: str
    capacities: Optional[List[UserCapacity]] = None
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
    capacities: Optional[List[UserCapacity]] = None
    model_config = ConfigDict(from_attributes=True)
