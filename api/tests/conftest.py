from typing import Dict, Generator
import pytest
from fastapi.testclient import TestClient
import sqlalchemy as sa
from sqlalchemy.orm import sessionmaker
from alembic.config import Config
from alembic.command import upgrade
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


@pytest.fixture()
def init_db():
    alembic_config = Config("alembic.ini")
    upgrade(alembic_config, "head")
    yield


@pytest.fixture(autouse=True)
def add_data(init_db):
    db = TestingSessionLocal()

    # Clean up tables before importing the initial data
    for table in reversed(Base.metadata.sorted_tables):
        clear_and_reseed = sa.text(f"TRUNCATE {table.name} RESTART IDENTITY CASCADE")
        db.execute(clear_and_reseed)
        db.commit()

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


@pytest.fixture(scope="module")
def get_user_without_roles_token_headers(client: TestClient) -> Dict[str, str]:
    user = {
        "aud": "account",
        "roles": [],
        "name": "Danaerys",
        "preferred_username": "no_roles",
        "given_name": "Danaerys",
        "family_name": "Targaryen",
        "email": "mother_of_dragons@westeros.com",
    }
    token = create_access_token(user)
    headers = {"Authorization": f"Bearer {token}"}
    return headers


@pytest.fixture(scope="module")
def get_user_missing_scopes_token_headers(client: TestClient) -> Dict[str, str]:
    user = {
        "aud": "account",
        "roles": ["few scopes"],
        "name": "Leslie",
        "preferred_username": "missing_scope",
        "given_name": "Leslie",
        "family_name": "Knope",
        "email": "knope@pawnee.gov",
    }
    token = create_access_token(user)
    headers = {"Authorization": f"Bearer {token}"}
    return headers
