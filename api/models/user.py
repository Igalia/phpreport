from typing import List
from sqlalchemy import Date, Column, ForeignKey, Integer, String, Numeric, UniqueConstraint, CheckConstraint
from sqlalchemy.dialects.postgresql import JSONB
from sqlalchemy.orm import relationship, Mapped, mapped_column
from sqlalchemy.ext.hybrid import hybrid_property
from db.base_class import Base
from datetime import date


class User(Base):
    __tablename__ = "usr"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    password = Column(String(length=256), nullable=True)
    login = Column(String(length=100), nullable=False, unique=True)
    capacities: Mapped[List["UserCapacity"]] = relationship()


class UserGroup(Base):
    __tablename__ = "user_group"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    name = Column(String(length=128), nullable=True, unique=True)
    scopes = Column(String(length=1000), nullable=True)


class UserRoles(Base):
    __tablename__ = "belongs"
    group_id: Mapped[int] = mapped_column("user_groupid", ForeignKey("user_group.id"), nullable=False, primary_key=True)
    user_id: Mapped[int] = mapped_column("usrid", ForeignKey("usr.id"), nullable=False, primary_key=True)
    role: Mapped["UserGroup"] = relationship()
    user: Mapped["User"] = relationship()


class UserLocation(Base):
    __tablename__ = "city_history"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    city = Column("cityid", Integer, ForeignKey("city.id"), nullable=False)
    user = Column("usrid", Integer, ForeignKey("usr.id"), nullable=False)
    init_date = Column(Date, nullable=False)
    end_date = Column(Date, nullable=True)

    __table_args__ = (
        UniqueConstraint("usrid", "init_date", name="unique_city_history_user_date"),
        CheckConstraint("end_date IS NULL OR end_date >= init_date", name="end_after_init_city_history"),
    )


class UserHourCost(Base):
    __tablename__ = "hour_cost_history"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    hour_cost = Column(Numeric(precision=8, scale=4), nullable=False)
    init_date = Column(Date, nullable=False)
    end_date = Column(Date, nullable=True)
    user = Column("usrid", Integer, ForeignKey("usr.id"), nullable=False)

    __table_args__ = (
        UniqueConstraint("usrid", "init_date", name="unique_hour_cost_history_user_date"),
        CheckConstraint("end_date IS NULL OR end_date >= init_date", name="end_after_init_hour_cost_history"),
    )


class UserCapacity(Base):
    __tablename__ = "journey_history"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    capacity = Column("journey", Numeric(precision=8, scale=4), nullable=False)
    start = Column("init_date", Date, nullable=False)
    end = Column("end_date", Date, nullable=True)
    user_id = Column("usrid", Integer, ForeignKey("usr.id"), nullable=False)
    yearly_expected_and_vacation = Column("yearly_expected_and_vacation", JSONB, nullable=True)

    @hybrid_property
    def is_current(self):
        if not self.end:
            return date.today() >= self.start
        return date.today() >= self.start and date.today() <= self.end

    __table_args__ = (
        UniqueConstraint("usrid", "init_date", name="unique_journey_history_user_date"),
        CheckConstraint("end_date IS NULL OR end_date >= init_date", name="end_after_init_journey_history"),
    )


class UserArea(Base):
    __tablename__ = "area_history"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    init_date = Column(Date, nullable=False)
    end_date = Column(Date, nullable=True)
    area = Column("areaid", Integer, ForeignKey("area.id"), nullable=False)
    user = Column("usrid", Integer, ForeignKey("usr.id"), nullable=False)

    __table_args__ = (
        UniqueConstraint("usrid", "init_date", name="unique_area_history_user_date"),
        CheckConstraint("end_date IS NULL OR end_date >= init_date", name="end_after_init_area_history"),
    )
