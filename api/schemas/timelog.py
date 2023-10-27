from datetime import date
from pydantic import StringConstraints, ConfigDict, BaseModel, computed_field, model_validator
from typing import Optional, Any
from typing_extensions import Annotated
from helpers.time import time_string_to_int


class TaskTypeItem(BaseModel):
    slug: str
    name: str
    active: bool
    model_config = ConfigDict(from_attributes=True)


# Shared properties
class TemplateBase(BaseModel):
    name: Optional[Annotated[str, StringConstraints(max_length=80)]] = None
    story: Optional[Annotated[str, StringConstraints(max_length=80)]] = None
    description: Optional[Annotated[str, StringConstraints(max_length=8192)]] = None
    task_type: Optional[Annotated[str, StringConstraints(max_length=40)]] = None
    user_id: Optional[int] = None
    project_id: Optional[int] = None
    is_global: Optional[bool] = None
    start_time: Optional[str] = None
    end_time: Optional[str] = None

    @model_validator(mode="after")
    @classmethod
    def user_template_not_global(cls, data: Any) -> Any:
        if data.is_global and data.user_id is not None:
            raise ValueError("Global templates should not have a user_id.")
        if not data.is_global and not data.user_id:
            raise ValueError("Private templates should have a user_id.")
        return data


# Properties to receive on creation
class TemplateNew(TemplateBase):
    name: Annotated[str, StringConstraints(max_length=80)]
    is_global: bool = False

    @computed_field
    @property
    def init(self) -> Optional[int]:
        if not self.start_time:
            return None

        return time_string_to_int(self.start_time)

    @computed_field
    @property
    def end(self) -> Optional[int]:
        if not self.end_time:
            return None

        return time_string_to_int(self.end_time)


class TemplateUpdate(TemplateNew):
    name: Optional[Annotated[str, StringConstraints(max_length=80)]] = None
    is_global: Optional[bool] = None


# Properties shared by models stored in db
class TemplateInDb(TemplateBase):
    id: int
    init: Optional[int]
    end: Optional[int]
    model_config = ConfigDict(from_attributes=True)


# Properties to return to client
class Template(TemplateBase):
    id: int
    pass


# Shared properties
class TaskBase(BaseModel):
    date: Optional[date]
    story: Optional[Annotated[str, StringConstraints(max_length=80)]] = None
    description: Optional[Annotated[str, StringConstraints(max_length=8192)]] = None
    task_type: Optional[Annotated[str, StringConstraints(max_length=40)]] = None
    project_id: Optional[int] = None
    user_id: Optional[int] = None
    start_time: Optional[str] = None
    end_time: Optional[str] = None


class TaskCreationBase(TaskBase):
    @model_validator(mode="after")
    @classmethod
    def end_after_init_task(cls, data: Any) -> Any:
        end_time = data.end
        init_time = data.init
        if end_time and init_time:
            if end_time < init_time:
                raise ValueError("End time must be greater than or equal to start time.")
            if init_time < 0:
                raise ValueError("Start time must be greater than or equal to 0.")
            if end_time > 1439:
                raise ValueError("End time must be less than or equal to 1439 (23:59).")
            return data
        return data


# Properties to receive on creation
class TaskNew(TaskCreationBase):
    date: date
    project_id: int
    user_id: int
    start_time: Annotated[str, StringConstraints(pattern=r"^([01]?[0-9]|2[0-3]):[0-5][0-9]")]
    end_time: Annotated[str, StringConstraints(pattern=r"^([01]?[0-9]|2[0-3]):[0-5][0-9]")]

    @computed_field
    @property
    def init(self) -> int:
        return time_string_to_int(self.start_time)

    @computed_field
    @property
    def end(self) -> int:
        return time_string_to_int(self.end_time)


class TaskUpdate(TaskCreationBase):
    start_time: Optional[Annotated[str, StringConstraints(pattern=r"^([01]?[0-9]|2[0-3]):[0-5][0-9]")]] = None
    end_time: Optional[Annotated[str, StringConstraints(pattern=r"^([01]?[0-9]|2[0-3]):[0-5][0-9]")]] = None
    project_name: Optional[str] = None
    init: Optional[int] = None
    end: Optional[int] = None


# Properties shared by models stored in db
class TaskInDb(TaskBase):
    id: int
    init: int
    end: int
    date: date
    model_config = ConfigDict(from_attributes=True)


# Properties to return to client
class Task(TaskBase):
    id: int
    project_name: str
    customer_name: Optional[str] = None
