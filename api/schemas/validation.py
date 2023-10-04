from pydantic import BaseModel
from typing import Optional


class ValidatedObject(BaseModel):
    is_valid: bool
    message: Optional[str] = None
