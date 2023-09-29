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
    name: Optional[Annotated[str, StringConstraints(max_length=80)]]
    story: Optional[Annotated[str, StringConstraints(max_length=80)]]
    description: Optional[Annotated[str, StringConstraints(max_length=8192)]]
    task_type: Optional[Annotated[str, StringConstraints(max_length=40)]]
    customer_id: Optional[int]
    user_id: Optional[int]
    project_id: Optional[int]
    is_global: Optional[bool]
    start_time: Optional[str]
    end_time: Optional[str]


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


# Properties shared by models stored in db
class TemplateInDb(TemplateBase):
    id: int
    init: Optional[int]
    end: Optional[int]
    model_config = ConfigDict(from_attributes=True)


# Properties to return to client
class Template(TemplateInDb):
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
class TaskNew(TaskBase):
    date: date
    project_id: int
    user_id: int
    start_time: Annotated[str, StringConstraints(pattern=r"^[0-9]{2}:[0-9]{2}")]
    end_time: Annotated[str, StringConstraints(pattern=r"^[0-9]{2}:[0-9]{2}")]

    @computed_field
    @property
    def init(self) -> int:
        return time_string_to_int(self.start_time)

    @computed_field
    @property
    def end(self) -> int:
        return time_string_to_int(self.end_time)


# Properties shared by models stored in db
class TaskInDb(TaskBase):
    id: int
    init: int
    end: int
    model_config = ConfigDict(from_attributes=True)


# Properties to return to client
class Task(TaskInDb):
    project_name: str
    customer_name: Optional[str] = None


class TaskValidate(BaseModel):
    is_task_valid: bool
    message: Optional[str] = None
