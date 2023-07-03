from sqlalchemy import Column, ForeignKey, Integer, String
import sqlalchemy as sa

from db.base_class import Base


class Customer(Base):
    id = Column(
        Integer,
        primary_key=True,
        server_default=sa.text("nextval('customer_id_seq'::regclass)"),
        autoincrement=True,
        nullable=False,
    )
    name = Column(String(length=256), nullable=False)
    customer_type = Column("type", String(length=256), nullable=False)
    url = Column(String(length=8192), nullable=True)
    sector_id = Column("sectorid", Integer, ForeignKey("sector.id"), nullable=False)
