from datetime import date

from pydantic import BaseModel


class Project(BaseModel):
    id: str
    activation: bool
    init: date | None
    end: date | None
    invoice: float | None
    estimated_hours: float | None
    moved_hours: float | None
    description: str | None
    project_type: str | None
    schedule_type: str | None
    customer_id: int
    area_id: int

    class Config:
        orm_mode = True
