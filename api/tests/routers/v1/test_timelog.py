from decouple import config
from http import HTTPStatus
from fastapi.testclient import TestClient
from typing import Dict

API_BASE_URL = config("API_BASE_URL")


def test_get_task_types_authenticated(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    expected_types = [
        {"slug": "meeting", "name": "Meeting", "active": True},
        {"slug": "deprecated", "name": "Deprecated Type", "active": False},
    ]

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/task_types/",
        headers=get_regular_user_token_headers,
    )
    assert response.status_code == HTTPStatus.OK
    task_types = response.json()
    assert task_types == expected_types


def test_get_user_cannot_get_templates_from_other_user(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/", headers=get_regular_user_token_headers, params={"user_id": 2}
    )
    assert response.status_code == HTTPStatus.FORBIDDEN
    content = response.json()
    assert content["detail"] == "You are not authorized to see templates for this user"


def test_get_user_and_global_templates(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    expected_templates = [
        {
            "id": 1,
            "name": "Coffee Break",
            "story": "coffee",
            "description": "Need to recharge",
            "task_type": "meeting",
            "start_time": "0:00",
            "end_time": "7:00",
            "user_id": 1,
            "is_global": False,
            "project_id": None,
        },
        {
            "id": 3,
            "name": "Working at night",
            "story": None,
            "description": "Working late",
            "task_type": "meeting",
            "start_time": "20:00",
            "end_time": "22:00",
            "user_id": None,
            "is_global": True,
            "project_id": None,
        },
    ]

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/", headers=get_regular_user_token_headers, params={"user_id": 1}
    )
    assert response.status_code == HTTPStatus.OK
    templates = response.json()
    assert len(templates) == 2
    assert templates == expected_templates


def test_create_user_template(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    template_payload = {
        "name": "Full work day",
        "story": "work",
        "description": "Just a regular day",
        "start_time": "9:00",
        "end_time": "17:00",
        "task_type": "meeting",
        "project_id": 1,
        "user_id": 1,
        "is_global": False,
    }
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/", headers=get_regular_user_token_headers, params={"user_id": 1}
    )
    assert response.status_code == HTTPStatus.OK
    templates = response.json()
    assert len(templates) == 2

    response = client.post(
        f"{API_BASE_URL}/v1/timelog/templates",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=template_payload,
    )
    assert response.status_code == HTTPStatus.CREATED

    expected_response = {
        "id": 4,
        "name": "Full work day",
        "story": "work",
        "description": "Just a regular day",
        "start_time": "9:00",
        "end_time": "17:00",
        "project_id": 1,
        "user_id": 1,
        "is_global": False,
        "task_type": "meeting",
    }
    assert response.json() == expected_response

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/", headers=get_regular_user_token_headers, params={"user_id": 1}
    )
    assert response.status_code == HTTPStatus.OK
    templates = response.json()
    assert len(templates) == 3


def test_regular_user_cannot_create_global_template(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    template_payload = {
        "name": "TGIF",
        "story": "no work",
        "description": "No work on Friday",
        "start_time": "0:00",
        "end_time": "0:00",
        "project_id": 1,
        "is_global": True,
    }

    response = client.post(
        f"{API_BASE_URL}/v1/timelog/templates",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=template_payload,
    )
    assert response.status_code == HTTPStatus.FORBIDDEN

    res = response.json()
    assert res["detail"] == "You are not authorized to create or update global templates"


def test_regular_user_cannot_create_template_for_another_user(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    template_payload = {
        "name": "TGIF",
        "story": "no work",
        "description": "No work on Friday",
        "start_time": "0:00",
        "end_time": "0:00",
        "project_id": 1,
        "is_global": True,
    }

    response = client.post(
        f"{API_BASE_URL}/v1/timelog/templates",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=template_payload,
    )
    assert response.status_code == HTTPStatus.FORBIDDEN

    res = response.json()
    assert res["detail"] == "You are not authorized to create or update global templates"
