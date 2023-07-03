from sqlalchemy import Column, Integer, String
import sqlalchemy as sa

from db.base_class import Base


class Sector(Base):
    id = Column(
        Integer,
        primary_key=True,
        server_default=sa.text("nextval('sector_id_seq'::regclass)"),
        autoincrement=True,
        nullable=False,
    )
    name = Column(String(length=256), nullable=False, unique=True)
