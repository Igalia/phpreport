from datetime import date
from pydantic import BaseModel
from typing import Optional


class TaskTypeItem(BaseModel):
    slug: str
    name: str
    active: bool

    class Config:
        orm_mode = True


class Template(BaseModel):
    id: int
    name: str
    story: Optional[str]
    description: Optional[str]
    task_type: Optional[str]
    init_time: Optional[int]
    end_time: Optional[int]
    customer_id: Optional[int]
    user_id: Optional[int]
    project_id: Optional[int]
    is_global: bool

    class Config:
        orm_mode = True


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
