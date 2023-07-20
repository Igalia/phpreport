from pydantic import BaseModel


class TaskTypeItem(BaseModel):
    slug: str
    name: str
    active: bool

    class Config:
        orm_mode = True
