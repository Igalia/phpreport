from pydantic import BaseModel
from typing import Optional


class Customer(BaseModel):
    id: int
    name: str
    customer_type: str
    url: Optional[str]
    sector_id: int

    class Config:
        orm_mode = True
