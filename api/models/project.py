from sqlalchemy import Boolean, Column, ForeignKey, Integer, String, Date, Double
import sqlalchemy as sa
from sqlalchemy.orm import relationship, Mapped
from sqlalchemy.ext.hybrid import hybrid_property
from models.customer import Customer

from db.base_class import Base


class Project(Base):
    id = Column(
        Integer,
        primary_key=True,
        server_default=sa.text("nextval('project_id_seq'::regclass)"),
        autoincrement=True,
        nullable=False,
    )
    activation = Column(Boolean, default=True, nullable=False)
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
    project = Column("projectid", Integer, ForeignKey("project.id"), nullable=False)
