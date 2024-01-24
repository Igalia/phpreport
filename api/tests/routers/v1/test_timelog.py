from decouple import config
from http import HTTPStatus
from fastapi.testclient import TestClient
from typing import Dict

API_BASE_URL = config("API_BASE_URL")


def test_get_task_types_authenticated(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    expected_types = [
        {"slug": "meeting", "name": "Meeting", "active": True},
        {"slug": "project", "name": "Project time", "active": True},
    ]

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/task_types",
        headers=get_regular_user_token_headers,
    )
    assert response.status_code == HTTPStatus.OK
    task_types = response.json()
    assert task_types == expected_types


def test_get_task_types_including_inactive_ones(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    expected_types = [
        {"slug": "deprecated", "name": "Deprecated Type", "active": False},
        {"slug": "meeting", "name": "Meeting", "active": True},
        {"slug": "project", "name": "Project time", "active": True},
    ]

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/task_types",
        headers=get_regular_user_token_headers,
        params={"active": False},
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
        "id": 4,
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


def test_get_summary_fulltime_worker_entire_year(
    client: TestClient, get_regular_user_token_headers: Dict[str, str]
) -> None:
    # there are 260 work days in 2023, user_id 1 has a capacity of 8.0 hrs/day
    # so 260 * 8 = 2080, which would be expected work hours for year
    # the config entry in mock_data sets company alloted vacation to 200 hours
    # The user has the 8.0 hr/day capacity for all of 2023, so they would receive
    # the full 200 hours of vacation as alloted by the company
    # 200 * 60 = 12000, so this user should have 12000 vacation minutes
    # There are 210 work days between 2023-01-01 and 2023-10-20 (the ref_date we use here)
    # 210 * 8  = 1680, which would be the expected hours to date
    expected_summary = {
        "today": 120,
        "week": 120,
        "todayText": "2h 00m",
        "weekText": "2h 00m",
        "projectSummaries": [
            {
                "projectId": 2,
                "project": "Internal",
                "todayTotal": 120,
                "todayText": "2h 0m",
                "weekTotal": 120,
                "weekText": "2h 0m",
                "isVacation": False,
            }
        ],
        "vacationAvailable": 12000,
        "vacationAvailableText": "25 days (200 h)",
        "vacationUsed": 0,
        "vacationUsedText": "None",
        "vacationScheduled": 0,
        "vacationScheduledText": "None",
        "vacationPending": 12000,
        "vacationPendingText": "25 days (200 h)",
        "expectedHoursYear": 2080.0,
        "expectedHoursToDate": 1680.0,
        "expectedHoursWeek": 40.0,
        "workedHoursYear": 2.0,
    }

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/summary",
        headers=get_regular_user_token_headers,
        params={"ref_date": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.OK
    summary = response.json()
    assert summary == expected_summary


def test_get_summary_parttime_worker_entire_year(
    client: TestClient, get_admin_user_token_headers: Dict[str, str]
) -> None:
    # there are 260 work days in 2023, user_id 2 has a capacity of 6.0 hrs/day
    # so 260 * 6 = 1560, which would be expected work hours for year
    # the config entry in mock_data sets company alloted vacation to 200 hours
    # The user has the 6.0 hr/day capacity for all of 2023, so they would receive
    # workable days user has in capacity / workable days for year
    # * (daily capacity * 5 / company_fte) * company_vacation
    # (260/260) * (6.0 * 5 / 40) * 200  = 150
    # 150 * 60 = 9000, so this user should have 9000 vacation minutes
    # There are 210 work days between 2023-01-01 and 2023-10-20 (the ref_date we use here)
    # 210 * 6  = 1260, which would be the expected hours to date
    expected_summary = {
        "today": 120,
        "week": 120,
        "todayText": "2h 00m",
        "weekText": "2h 00m",
        "projectSummaries": [
            {
                "projectId": 2,
                "project": "Internal",
                "todayTotal": 120,
                "todayText": "2h 0m",
                "weekTotal": 120,
                "weekText": "2h 0m",
                "isVacation": False,
            }
        ],
        "vacationAvailable": 9000,
        "vacationAvailableText": "25 days (150 h)",
        "vacationUsed": 0,
        "vacationUsedText": "None",
        "vacationScheduled": 0,
        "vacationScheduledText": "None",
        "vacationPending": 9000,
        "vacationPendingText": "25 days (150 h)",
        "expectedHoursYear": 1560.0,
        "expectedHoursToDate": 1260.0,
        "expectedHoursWeek": 30.0,
        "workedHoursYear": 2.0,
    }

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/summary",
        headers=get_admin_user_token_headers,
        params={"ref_date": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.OK
    summary = response.json()
    assert summary == expected_summary


def test_get_summary_fulltime_worker_partial_year(
    client: TestClient, get_manager_user_token_headers: Dict[str, str]
) -> None:
    # there are 130 work days in 2023 for the Manager's capacity (H2 2023)
    # so 130 * 8 = 1040, which would be expected work hours for year
    # the config entry in mock_data sets company alloted vacation to 200 hours
    # The user has the 8.0 hr/day capacity for part of 2023, so they would receive
    # workable days user has in capacity / workable days for year
    # * (daily capacity * 5 / company_fte) * company_vacation
    # (130/260) * (8.0 * 5 / 40) * 200  = 100.82
    # 100.82 * 60 = 6049, so this user should have 6049 vacation minutes
    # There are 80 work days between 2023-07-01 9start of user capacity) and 2023-10-20 (the ref_date we use here)
    # 80 * 8  = 640, which would be the expected hours to date
    expected_summary = {
        "today": 120,
        "week": 120,
        "todayText": "2h 00m",
        "weekText": "2h 00m",
        "projectSummaries": [
            {
                "projectId": 2,
                "project": "Internal",
                "todayTotal": 120,
                "todayText": "2h 0m",
                "weekTotal": 120,
                "weekText": "2h 0m",
                "isVacation": False,
            }
        ],
        "vacationAvailable": 6049,
        "vacationAvailableText": "12 days 4 h 49 m (100 h 49 m)",
        "vacationUsed": 0,
        "vacationUsedText": "None",
        "vacationScheduled": 0,
        "vacationScheduledText": "None",
        "vacationPending": 6049,
        "vacationPendingText": "12 days 4 h 49 m (100 h 49 m)",
        "expectedHoursYear": 1040.0,
        "expectedHoursToDate": 640,
        "expectedHoursWeek": 40.0,
        "workedHoursYear": 2.0,
    }

    response = client.get(
        f"{API_BASE_URL}/v1/timelog/summary",
        headers=get_manager_user_token_headers,
        params={"ref_date": "2023-10-20"},
    )
    assert response.status_code == HTTPStatus.OK
    summary = response.json()
    assert summary == expected_summary
