from decouple import config
from http import HTTPStatus
from fastapi.testclient import TestClient
from typing import Dict

API_BASE_URL = config("API_BASE_URL")


def test_get_task_types_authenticated(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    expected_types = [
        {"slug": "meeting", "name": "Meeting", "active": True},
        {"slug": "deprecated", "name": "Deprecated Type", "active": False},
        {"slug": "project", "name": "Project time", "active": True},
    ]

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/task_types/",
        headers=get_regular_user_token_headers,
    )
    assert response.status_code == HTTPStatus.OK
    task_types = response.json()
    assert task_types == expected_types


def test_get_task_types_no_scope(client: TestClient, get_user_missing_scopes_token_headers: Dict[str, str]) -> None:
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/task_types/",
        headers=get_user_missing_scopes_token_headers,
    )
    assert response.status_code == HTTPStatus.FORBIDDEN
    res = response.json()
    assert res["detail"] == "You do not have permission to perform this action."


def test_get_user_cannot_get_templates_from_other_user(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/",
        headers=get_regular_user_token_headers,
        params={"user_id": 2},
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
            "taskType": "meeting",
            "startTime": "0:00",
            "endTime": "7:00",
            "userId": 1,
            "isGlobal": False,
            "projectId": None,
        },
        {
            "id": 3,
            "name": "Working at night",
            "story": None,
            "description": "Working late",
            "taskType": "meeting",
            "startTime": "20:00",
            "endTime": "22:00",
            "userId": None,
            "isGlobal": True,
            "projectId": None,
        },
    ]

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
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
        "startTime": "9:00",
        "endTime": "17:00",
        "taskType": "meeting",
        "projectId": 1,
        "userId": 1,
        "isGlobal": False,
    }
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
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
        "startTime": "9:00",
        "endTime": "17:00",
        "projectId": 1,
        "userId": 1,
        "isGlobal": False,
        "taskType": "meeting",
    }
    assert response.json() == expected_response

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
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
        "startTime": "0:00",
        "endTime": "0:00",
        "projectId": 1,
        "isGlobal": True,
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
        "startTime": "0:00",
        "endTime": "0:00",
        "projectId": 1,
        "isGlobal": True,
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


def test_update_user_template(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    template_payload = {
        "name": "Coffee Break updated",
        "story": "coffee time",
        "description": "Need to recharge again",
        "taskType": "meeting",
        "startTime": "15:00",
        "endTime": "15:30",
        "projectId": 1,
        "userId": 1,
    }

    response = client.put(
        f"{API_BASE_URL}/v1/timelog/templates/1",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=template_payload,
    )
    assert response.status_code == HTTPStatus.OK

    expected_template = {
        "id": 1,
        "name": "Coffee Break updated",
        "story": "coffee time",
        "description": "Need to recharge again",
        "taskType": "meeting",
        "startTime": "15:00",
        "endTime": "15:30",
        "projectId": 1,
        "isGlobal": False,
        "userId": 1,
    }

    res = response.json()
    assert res == expected_template


def test_update_nonexistent_template(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    template_payload = {
        "name": "Fake template",
        "userId": 1,
    }

    response = client.put(
        f"{API_BASE_URL}/v1/timelog/templates/100",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=template_payload,
    )
    assert response.status_code == HTTPStatus.NOT_FOUND

    res = response.json()
    assert res["detail"] == "Template with id 100 not found"


def test_cannot_update_other_users_template(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    template_payload = {
        "name": "Series time",
        "description": "Watching Tuca and Bertie",
        "userId": 2,
        "isGlobal": False,
    }

    response = client.put(
        f"{API_BASE_URL}/v1/timelog/templates/2",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=template_payload,
    )
    assert response.status_code == HTTPStatus.FORBIDDEN
    res = response.json()
    assert res["detail"] == "You are not authorized to create or update templates for this user"


def test_delete_template(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    # Check existing templates first
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.OK
    templates = response.json()
    assert len(templates) == 2

    response = client.delete(
        f"{API_BASE_URL}/v1/timelog/templates/1",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.NO_CONTENT

    # There should be only one template now
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/templates/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.OK
    templates = response.json()
    assert len(templates) == 1


def test_cannot_delete_other_users_template(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    response = client.delete(
        f"{API_BASE_URL}/v1/timelog/templates/2",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.FORBIDDEN
    res = response.json()
    assert res["detail"] == "You are not authorized to delete templates for this user"


def test_cannot_delete_other_global_template(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    response = client.delete(
        f"{API_BASE_URL}/v1/timelog/templates/3",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.FORBIDDEN
    res = response.json()
    assert res["detail"] == "You are not authorized to delete global templates"


def test_get_user_tasks(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    expected_tasks = [
        {
            "id": 1,
            "userId": 1,
            "date": "2023-10-20",
            "description": "Working in that awesome project",
            "startTime": "20:00",
            "endTime": "22:00",
            "projectId": 2,
            "projectName": "Internal",
            "customerName": "Internal Customer",
            "story": "that project",
            "taskType": "project",
        },
    ]

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/tasks/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1, "start": "2023-10-20", "end": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.OK
    tasks = response.json()
    assert tasks == expected_tasks


def test_regular_user_cannot_get_other_users_tasks(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/tasks/",
        headers=get_regular_user_token_headers,
        params={"user_id": 2, "start": "2023-10-20", "end": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.FORBIDDEN
    res = response.json()
    assert res["detail"] == "You are not authorized to view tasks for this user"


def test_user_without_any_roles_cannot_get_tasks(
    client: TestClient, get_user_without_roles_token_headers: Dict[str, str]
) -> None:
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/tasks/",
        headers=get_user_without_roles_token_headers,
        params={"user_id": 6, "start": "2023-10-20", "end": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.UNAUTHORIZED
    res = response.json()
    assert res["detail"] == "You have not been assigned any roles in the application. Please speak to your sysadmin."


def test_create_task(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    task_payload = {
        "date": "2023-10-21",
        "story": "project",
        "description": "Adding tests",
        "taskType": "project",
        "projectId": 1,
        "userId": 1,
        "startTime": "08:30",
        "endTime": "14:00",
    }
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/tasks/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1, "start": "2023-10-21", "end": "2023-10-21"},
    )
    assert response.status_code == HTTPStatus.OK
    tasks = response.json()
    assert tasks == []

    response = client.post(
        f"{API_BASE_URL}/v1/timelog/tasks",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=task_payload,
    )
    assert response.status_code == HTTPStatus.CREATED

    expected_response = {
        "id": 3,
        "date": "2023-10-21",
        "story": "project",
        "description": "Adding tests",
        "taskType": "project",
        "projectId": 1,
        "userId": 1,
        "startTime": "8:30",
        "endTime": "14:00",
        "projectName": "Holidays",
        "customerName": "Internal Customer",
    }
    assert response.json() == expected_response

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/tasks/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1, "start": "2023-10-21", "end": "2023-10-21"},
    )
    assert response.status_code == HTTPStatus.OK
    tasks = response.json()
    assert tasks == [expected_response]


def test_regular_user_cannot_create_tasks_for_other_users(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    task_payload = {
        "date": "2023-10-21",
        "story": "project",
        "description": "Adding tests",
        "taskType": "project",
        "projectId": 1,
        "userId": 2,
        "startTime": "08:30",
        "endTime": "14:00",
    }
    response = client.post(
        f"{API_BASE_URL}/v1/timelog/tasks",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=task_payload,
    )
    assert response.status_code == HTTPStatus.FORBIDDEN
    res = response.json()
    assert res["detail"] == "You are not authorized to create tasks for this user."


def test_update_task(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    task_payload = {
        "date": "2023-10-22",
        "startTime": "10:00",
        "endTime": "12:00",
        "userId": 1,
    }

    response = client.put(
        f"{API_BASE_URL}/v1/timelog/tasks/1",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=task_payload,
    )

    assert response.status_code == HTTPStatus.OK

    expected_task = {
        "id": 1,
        "userId": 1,
        "date": "2023-10-22",
        "description": "Working in that awesome project",
        "startTime": "10:00",
        "endTime": "12:00",
        "projectId": 2,
        "projectName": "Internal",
        "customerName": "Internal Customer",
        "story": "that project",
        "taskType": "project",
    }

    res = response.json()
    assert res == expected_task


def test_delete_task(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    # Check existing tasks first
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/tasks/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1, "start": "2023-10-20", "end": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.OK
    tasks = response.json()
    assert len(tasks) == 1

    response = client.delete(
        f"{API_BASE_URL}/v1/timelog/tasks/1",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.NO_CONTENT

    # After the deletion there should be no task for this user in this day
    response = client.get(
        f"{API_BASE_URL}/v1/timelog/tasks/",
        headers=get_regular_user_token_headers,
        params={"user_id": 1, "start": "2023-10-20", "end": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.OK
    tasks = response.json()
    assert tasks == []


def test_regular_user_cannot_delete_other_users_tasks(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    response = client.delete(
        f"{API_BASE_URL}/v1/timelog/tasks/2",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.FORBIDDEN
    res = response.json()
    assert res["detail"] == "You are not authorized to delete tasks for this user"


def test_delete_nonexistent_task(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    response = client.delete(
        f"{API_BASE_URL}/v1/timelog/tasks/123",
        headers=get_regular_user_token_headers,
        params={"user_id": 1, "start": "2023-10-20", "end": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.NOT_FOUND

    res = response.json()
    assert res["detail"] == "Task with id 123 not found"


def test_user_cannot_create_task_with_overlapping_hours(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    task_payload = {
        "date": "2023-10-20",
        "story": "project",
        "description": "Overlapping time with task 1",
        "taskType": "project",
        "projectId": 1,
        "userId": 1,
        "startTime": "20:30",
        "endTime": "23:00",
    }

    response = client.post(
        f"{API_BASE_URL}/v1/timelog/tasks",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=task_payload,
    )
    assert response.status_code == HTTPStatus.UNPROCESSABLE_ENTITY
    error_message = response.json()
    assert error_message["detail"] == "Task from 20:30 to 23:00 overlaps an existing task from 20:00 to 22:00."
