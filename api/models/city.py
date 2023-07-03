from sqlalchemy import Column, ForeignKey, Integer, String, Date
import sqlalchemy as sa
from sqlalchemy.schema import UniqueConstraint

from db.base_class import Base


class City(Base):
    id = Column(
        Integer,
        primary_key=True,
        server_default=sa.text("nextval('city_id_seq'::regclass)"),
        autoincrement=True,
        nullable=False,
    )
    name = Column(String(length=30), nullable=False, unique=True)


class BankHoliday(Base):
    __tablename__ = "common_event"

    id = Column(Integer, primary_key=True, autoincrement=True, nullable=False)
    date = Column("_date", Date, nullable=False)
    city = Column("cityid", Integer, ForeignKey("city.id"), nullable=False)

    __table_args__ = (UniqueConstraint("cityid", "_date", name="unique_common_event_city_date"),)
