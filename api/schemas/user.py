from typing import List, Optional
from decimal import Decimal
from pydantic import ConfigDict, BaseModel, Field
from pydantic.alias_generators import to_camel
from datetime import date


class UserCapacity(BaseModel):
    capacity: Decimal = Field(max_digits=4, decimal_places=2)
    start: Optional[date] = None
    end: Optional[date] = None
    is_current: Optional[bool] = None
    model_config = ConfigDict(alias_generator=to_camel, populate_by_name=True)


class User(BaseModel):
    id: int
    login: str
    capacities: Optional[List[UserCapacity]] = None
    model_config = ConfigDict(alias_generator=to_camel, populate_by_name=True)


class UserGroup(BaseModel):
    id: int
    name: str
    model_config = ConfigDict(alias_generator=to_camel, populate_by_name=True)


class UserRoles(BaseModel):
    role: UserGroup
    model_config = ConfigDict(alias_generator=to_camel, populate_by_name=True)


class AppUser(BaseModel):
    model_config = ConfigDict(alias_generator=to_camel, populate_by_name=True)
    id: Optional[int] = None
    username: Optional[str] = None
    email: Optional[str] = None
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    roles: Optional[List[str]] = None
    authorized_scopes: Optional[List[str]] = None
    capacities: Optional[List[UserCapacity]] = None
