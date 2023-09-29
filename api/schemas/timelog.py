from datetime import date
from pydantic import BaseModel, root_validator, constr
from typing import Optional


class TaskTypeItem(BaseModel):
    slug: str
    name: str
    active: bool

    class Config:
        orm_mode = True


# Shared properties
class TemplateBase(BaseModel):
    name: Optional[constr(max_length=80)]
    story: Optional[constr(max_length=80)]
    description: Optional[constr(max_length=8192)]
    task_type: Optional[constr(max_length=40)]
    init_time: Optional[int]
    end_time: Optional[int]
    customer_id: Optional[int]
    user_id: Optional[int]
    project_id: Optional[int]
    is_global: Optional[bool]

    @root_validator(pre=True)
    def user_template_not_global(cls, values):
        user_id, is_global = values.get("user_id"), values.get("is_global")
        if is_global and user_id is not None:
            raise ValueError("Global templates should not have a user_id.")
        if not is_global and not user_id:
            raise ValueError("Private templates should have a user_id.")
        return values


# Properties to receive on creation
class TemplateNew(TemplateBase):
    name: constr(max_length=80)
    is_global: bool = False


# Properties shared by models stored in db
class TemplateInDb(TemplateBase):
    id: int

    class Config:
        orm_mode = True


# Properties to return to client
class Template(TemplateInDb):
    pass


class Task(BaseModel):
    id: int
    date: date
    init: int
    end: int
    story: Optional[str]
    description: Optional[str]
    task_type: Optional[str]
    project_id: int
    project_name: str
    customer_name: Optional[str]

    class Config:
        orm_mode = True
