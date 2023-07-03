from os import getenv
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker


def get_url():
    user = getenv("DB_USER", "phpreport")
    password = getenv("DB_PASSWORD", "phpreport")
    server = getenv("DB_HOST", "localhost")
    db = getenv("DB_NAME", "phpreport")
    return f"postgresql://{user}:{password}@{server}/{db}"


SQLALCHEMY_DATABASE_URL = get_url()

engine = create_engine(SQLALCHEMY_DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)


def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
