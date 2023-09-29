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
