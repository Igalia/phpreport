from pydantic import ConfigDict, BaseModel
from typing import Optional


class Customer(BaseModel):
    id: int
    name: str
    customer_type: str
    url: Optional[str] = None
    sector_id: int
    model_config = ConfigDict(from_attributes=True)
