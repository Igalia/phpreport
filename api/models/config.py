from sqlalchemy import Column, Integer, String, Boolean, Date

from db.base_class import Base


class Config(Base):
    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    version = Column(String(length=20), nullable=True)
    block_tasks_by_time_enabled = Column(Boolean, nullable=False, default=False)
    block_tasks_by_time_number_of_days = Column(Integer)
    block_tasks_by_day_limit_enabled = Column(Boolean, nullable=False, default=False)
    block_tasks_by_day_limit_number_of_days = Column(Integer, nullable=True)
    block_tasks_by_date_enabled = Column(Boolean, default=False, nullable=False)
    block_tasks_by_date_date = Column(Date, default=False, nullable=True)
