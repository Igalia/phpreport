from sqlalchemy import Boolean, Column, ForeignKey, Integer, String, Date, DateTime, Double
from sqlalchemy.orm import relationship, Mapped
from sqlalchemy.ext.hybrid import hybrid_property

from db.base_class import Base
from helpers.time import total_hours_between_dates
from models.customer import Customer
from models.user import User


class Project(Base):
    id = Column(
        Integer,
        primary_key=True,
        autoincrement=True,
        nullable=False,
    )
    is_active = Column("activation", Boolean, default=True, nullable=False)
    init = Column(Date, nullable=True)
    end = Column("_end", Date, nullable=True)
    invoice = Column(Double, nullable=True)
    estimated_hours = Column("est_hours", Double, nullable=True)
    moved_hours = Column(Double, nullable=True)
    description = Column(String(length=256), nullable=True)
    project_type = Column("type", String(length=256), nullable=True)
    schedule_type = Column("sched_type", String(length=256), nullable=True)
    customer_id = Column("customerid", ForeignKey("customer.id"))
    area_id = Column("areaid", ForeignKey("area.id"), nullable=False)
    customer: Mapped["Customer"] = relationship()

    @hybrid_property
    def customer_name(self):
        return self.customer.name


class ProjectAssignment(Base):
    __tablename__ = "project_usr"

    user = Column("usrid", Integer, ForeignKey("usr.id"), nullable=False, primary_key=True)
    project = Column("projectid", Integer, ForeignKey("project.id"), nullable=False, primary_key=True)


class ProjectAllocation(Base):
    __tablename__ = "project_allocation"

    id = Column(
        Integer,
        primary_key=True,
        autoincrement=True,
        nullable=False,
    )
    user_id = Column(Integer, ForeignKey("usr.id"), nullable=False)
    project_id = Column(Integer, ForeignKey("project.id"), nullable=False)
    start_date = Column(Date, nullable=False)
    end_date = Column(Date, nullable=False)
    hours_per_day = Column(Double(precision=8), nullable=False)
    fte = Column(Double(precision=8), nullable=False)
    is_tentative = Column(Boolean)
    is_billable = Column(Boolean)
    notes = Column("text", String)
    created_at = Column(DateTime)
    created_by = Column(String(length=100))
    modified_at = Column(DateTime)
    modified_by = Column(String(length=100))
    user: Mapped["User"] = relationship(foreign_keys=[user_id])

    @hybrid_property
    def username(self):
        return self.user.login

    @property
    def total_hours(self):
        return total_hours_between_dates(self.start_date, self.end_date, self.hours_per_day)
