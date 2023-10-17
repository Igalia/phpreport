from decouple import config
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker


def get_url():
    user = config("DB_USER")
    password = config("DB_PASSWORD")
    server = config("DB_HOST")
    db = config("DB_NAME")
    return f"postgresql://{user}:{password}@{server}/{db}"


SQLALCHEMY_DATABASE_URL = get_url()

engine = create_engine(SQLALCHEMY_DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine, expire_on_commit=False)


def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
