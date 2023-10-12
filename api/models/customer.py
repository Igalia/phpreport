from sqlalchemy import Column, ForeignKey, Integer, String

from db.base_class import Base


class Customer(Base):
    id = Column(
        Integer,
        primary_key=True,
        autoincrement=True,
        nullable=False,
    )
    name = Column(String(length=256), nullable=False)
    customer_type = Column("type", String(length=256), nullable=False)
    url = Column(String(length=8192), nullable=True)
    sector_id = Column("sectorid", Integer, ForeignKey("sector.id"), nullable=False)
