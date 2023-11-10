from pydantic import ConfigDict, BaseModel
from pydantic.alias_generators import to_camel
from typing import Optional


class Customer(BaseModel):
    id: int
    name: str
    customer_type: str
    url: Optional[str] = None
    sector_id: int
    model_config = ConfigDict(alias_generator=to_camel, populate_by_name=True)
