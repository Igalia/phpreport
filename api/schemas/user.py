from pydantic import BaseModel


class User(BaseModel):
    id: int
    login: str

    class Config:
        orm_mode = True


class UserGroup(BaseModel):
    id: int
    name: str

    class Config:
        orm_mode = True


class UserRoles(BaseModel):
    role: UserGroup

    class Config:
        orm_mode = True
