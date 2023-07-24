from datetime import date

from pydantic import BaseModel
from typing import Optional


class Project(BaseModel):
    id: str
    activation: bool
    init: Optional[date]
    end: Optional[date]
    invoice: Optional[float]
    estimated_hours: Optional[float]
    moved_hours: Optional[float]
    description: Optional[str]
    project_type: Optional[str]
    schedule_type: Optional[str]
    customer_id: int
    area_id: int

    class Config:
        orm_mode = True
