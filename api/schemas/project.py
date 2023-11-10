from datetime import date
from pydantic import ConfigDict, BaseModel
from pydantic.alias_generators import to_camel
from typing import Optional


class Project(BaseModel):
    id: int
    is_active: bool
    init: Optional[date] = None
    end: Optional[date] = None
    invoice: Optional[float] = None
    estimated_hours: Optional[float] = None
    moved_hours: Optional[float] = None
    description: Optional[str] = None
    project_type: Optional[str] = None
    schedule_type: Optional[str] = None
    customer_id: int
    customer_name: Optional[str] = None
    area_id: int
    model_config = ConfigDict(alias_generator=to_camel, populate_by_name=True)
