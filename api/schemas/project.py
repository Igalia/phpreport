from datetime import date

from pydantic import ConfigDict, BaseModel
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
    area_id: int
    model_config = ConfigDict(from_attributes=True)
