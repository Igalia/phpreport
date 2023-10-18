from typing import Dict, Generator
import pytest
from fastapi.testclient import TestClient
from sqlalchemy.orm import sessionmaker

from api.main import app
from auth.auth_handler import create_access_token
from db import base  # noqa
from db.base_class import Base
from db.db_connection import engine
from tests.utils.mock_data import DATA

test_engine = engine

TestingSessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=test_engine)


@pytest.fixture(scope="session")
def db() -> Generator:
    try:
        db = TestingSessionLocal()
        yield db
    finally:
        db.close()


@pytest.fixture(scope="module")
def client() -> Generator:
    with TestClient(app) as c:
        yield c


@pytest.fixture(autouse=True)
def init_db():
    # Drop and create all the tables to start
    # with the db clean before the tests
    Base.metadata.drop_all(bind=test_engine)
    Base.metadata.create_all(bind=test_engine)
    db = TestingSessionLocal()

    # Clean up tables before importing the initial data
    for model, entries in DATA:
        # Insert the data
        for entry in entries:
            new_record = model(**entry)
            db.add(new_record)
            db.commit()
            db.refresh(new_record)

    yield

    db.close()


@pytest.fixture(scope="module")
def get_regular_user_token_headers(client: TestClient) -> Dict[str, str]:
    user = {
        "aud": "account",
        "roles": ["Regular User"],
        "name": "Yaphit",
        "preferred_username": "user",
        "given_name": "Yaphit",
        "family_name": "Green",
        "email": "yaphit@theorville.tv",
    }
    token = create_access_token(user)
    headers = {"Authorization": f"Bearer {token}"}
    return headers
