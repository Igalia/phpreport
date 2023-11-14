from sqlalchemy import Column, Integer, String, Boolean, JSON, DateTime
from db.base_class import Base


class Archive(Base):
    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    table_name = Column(String(length=100), nullable=True)
    record_type = Column(String(length=100), nullable=True)
    record_id = Column(Integer, nullable=True)
    operation = Column(String(length=50), nullable=True)
    old_values = Column(JSON, nullable=True)
    new_values = Column(JSON, nullable=True)
    most_recent = Column(Boolean, nullable=True)
    recorded_at = Column(DateTime, nullable=True)
