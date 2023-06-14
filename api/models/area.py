from sqlalchemy import Column, ForeignKey, Integer, String
from sqlalchemy.orm import relationship

from db.base_class import Base

class Area(Base):
    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    name = Column(String(length=256), nullable=False, unique=True)
