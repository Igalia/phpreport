from sqlalchemy import  Double, Date, Column, Integer, Date, String, Boolean, ForeignKey, CheckConstraint, UniqueConstraint, text
from sqlalchemy.orm import relationship
from sqlalchemy.schema import UniqueConstraint
from sqlalchemy.dialects import postgresql

from db.base_class import Base


class Task(Base):
    __tablename__ = 'task'

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    date = Column('_date', Date, nullable=False)
    init = Column(Integer, nullable=False)
    end = Column('_end', Integer, nullable=False)
    story = Column(String(length=80), nullable=True)
    telework = Column(Boolean, nullable=True)
    text = Column(String(length=8192), nullable=True)
    ttype = Column(String(length=40), nullable=True)
    phase = Column(String(length=15), nullable=True)
    onsite = Column(Boolean, default=False, nullable=False)
    updated_at = Column(postgresql.TIMESTAMP(), nullable=True)
    end_after_init_task = CheckConstraint('_end >= init AND init >= 0', name='end_after_init_task')
    customer = Column('customerid', Integer, ForeignKey("customer.id"), nullable=True)
    user = Column('usrid', Integer, ForeignKey("usr.id"), nullable=False)
    project = Column('projectid', Integer, ForeignKey("project.id"), nullable=False)

    __table_args__ = (UniqueConstraint('usrid', 'init', '_date', name='unique_task_usr_time'),)

class ExtraHour(Base):
    __tablename__ = 'extra_hour'

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    date = Column('_date', Date, nullable=False)
    hours = Column(Double, nullable=False)
    user_id = Column('usrid', Integer, ForeignKey("usr.id"), nullable=False)
    comment = Column(String(length=256), nullable=True)

    __table_args__ = (UniqueConstraint('usrid', '_date', name='unique_extra_hour_user_date'),)


class Template(Base):
    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    name = Column(String(length=80), nullable=False)
    story = Column(String(length=80), nullable=True)
    telework = Column(Boolean, nullable=True)
    onsite = Column(Boolean, nullable=True)
    text = Column(String(length=8192), nullable=True)
    ttype = Column(String(length=40), nullable=True)
    init_time = Column(Integer, nullable=True)
    end_time = Column(Integer, nullable=True)
    customer = Column('customerid', Integer, ForeignKey("customer.id"), nullable=True)
    user = Column('usrid', Integer, ForeignKey("usr.id"), nullable=False)
    project = Column('projectid', Integer, ForeignKey("project.id"), nullable=True)

class TotalHoursOverride(Base):
    __tablename__ = 'user_goals'

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    init_date = Column(Date, nullable=False)
    end_date = Column(Date, nullable=False)
    hours = Column('extra_hours', Double(precision=53), nullable=False)
    user = Column('usrid', Integer, ForeignKey("usr.id"), nullable=False)
