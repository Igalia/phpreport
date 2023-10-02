from sqlalchemy import (
    Double,
    Date,
    Column,
    Integer,
    String,
    Boolean,
    ForeignKey,
    CheckConstraint,
    UniqueConstraint,
)
from sqlalchemy.dialects import postgresql
from sqlalchemy.orm import relationship, Mapped
from sqlalchemy.ext.hybrid import hybrid_property
from models.project import Project
from models.user import User
from helpers.time import int_to_time_string

from db.base_class import Base


class Task(Base):
    __tablename__ = "task"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    date = Column("_date", Date, nullable=False)
    init = Column(Integer, nullable=False)
    end = Column("_end", Integer, nullable=False)
    story = Column(String(length=80), nullable=True)
    telework = Column(Boolean, nullable=True)
    description = Column("text", String(length=8192), nullable=True)
    task_type = Column("ttype", String, ForeignKey("task_type.slug"), nullable=True)
    phase = Column(String(length=15), nullable=True)
    onsite = Column(Boolean, default=False, nullable=False)
    updated_at = Column(postgresql.TIMESTAMP(), nullable=True)
    end_after_init_task = CheckConstraint("_end >= init AND init >= 0", name="end_after_init_task")
    user_id = Column("usrid", Integer, ForeignKey("usr.id"), nullable=False)
    project_id = Column("projectid", Integer, ForeignKey("project.id"), nullable=False)
    project: Mapped["Project"] = relationship()

    @hybrid_property
    def project_name(self):
        return self.project.description

    @hybrid_property
    def customer_name(self):
        return self.project.customer_name

    @hybrid_property
    def start_time(self):
        return int_to_time_string(self.init)

    @hybrid_property
    def end_time(self):
        return int_to_time_string(self.end)

    __table_args__ = (UniqueConstraint("usrid", "init", "_date", name="unique_task_usr_time"),)


class TaskType(Base):
    __tablename__ = "task_type"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    active = Column(Boolean, nullable=True, default=True)
    name = Column(String, nullable=True)
    slug = Column(String, nullable=False, unique=True)


class ExtraHour(Base):
    __tablename__ = "extra_hour"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    date = Column("_date", Date, nullable=False)
    hours = Column(Double, nullable=False)
    user_id = Column("usrid", Integer, ForeignKey("usr.id"), nullable=False)
    comment = Column(String(length=256), nullable=True)

    __table_args__ = (UniqueConstraint("usrid", "_date", name="unique_extra_hour_user_date"),)


class Template(Base):
    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    name = Column(String(length=80), nullable=False)
    story = Column(String(length=80), nullable=True)
    telework = Column(Boolean, nullable=True)
    onsite = Column(Boolean, nullable=True)
    description = Column("text", String(length=8192), nullable=True)
    task_type = Column("ttype", String(length=40), nullable=True)
    init = Column("init_time", Integer, nullable=True)
    end = Column("end_time", Integer, nullable=True)
    customer_id = Column("customerid", Integer, ForeignKey("customer.id"), nullable=True)
    user_id = Column("usrid", Integer, ForeignKey(User.id), nullable=True)
    project_id = Column("projectid", Integer, ForeignKey("project.id"), nullable=True)
    is_global = Column(Boolean, nullable=False, default=False)

    @hybrid_property
    def start_time(self):
        return int_to_time_string(self.init) if self.init else None

    @hybrid_property
    def end_time(self):
        return int_to_time_string(self.end) if self.end else None


class TotalHoursOverride(Base):
    __tablename__ = "user_goals"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    init_date = Column(Date, nullable=False)
    end_date = Column(Date, nullable=False)
    hours = Column("extra_hours", Double(precision=53), nullable=False)
    user = Column("usrid", Integer, ForeignKey("usr.id"), nullable=False)
