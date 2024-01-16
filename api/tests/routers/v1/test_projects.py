from decouple import config
from http import HTTPStatus
from fastapi.testclient import TestClient
from typing import Dict

API_BASE_URL = config("API_BASE_URL")


def test_create_project_allocation(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    response = client.get(
        f"{API_BASE_URL}/v1/projects/1/allocations?start=2024-01-01",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.OK
    allocations = response.json()

    allocation_payload = {
        "userId": 1,
        "projectId": 1,
        "startDate": "2024-01-08",
        "endDate": "2024-01-10",
        "hoursPerDay": 8.0,
        "fte": 1,
        "isTentative": False,
        "isBillable": True,
        "notes": "test",
    }
    response = client.post(
        f"{API_BASE_URL}/v1/projects/1/allocations",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=allocation_payload,
    )
    assert response.status_code == HTTPStatus.CREATED
    expected_new_allocation = {
        "id": 2,
        "userId": 1,
        "projectId": 1,
        "username": "user",
        "startDate": "2024-01-08",
        "endDate": "2024-01-10",
        "hoursPerDay": 8.0,
        "totalHours": 24.0,
        "fte": 1.0,
        "isTentative": False,
        "isBillable": True,
        "notes": "test",
    }
    assert response.json() == expected_new_allocation

    response = client.get(
        f"{API_BASE_URL}/v1/projects/1/allocations?start=2024-01-01&end=2024-12-01",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.OK
    allocations = response.json()
    expected_response = [
        {
            "username": "user",
            "hours": {
                "2024-1": {
                    "days": [
                        "2024-01-01",
                        "2024-01-02",
                        "2024-01-03",
                        "2024-01-04",
                        "2024-01-05",
                    ],
                    "ISOWeek": 1,
                    "month": "Jan",
                    "totalHours": 40.0,
                    "project": 1,
                    "isLeave": False,
                },
                "2024-2": {
                    "days": ["2024-01-08", "2024-01-09", "2024-01-10"],
                    "ISOWeek": 2,
                    "month": "Jan",
                    "totalHours": 24.0,
                    "project": 1,
                    "isLeave": False,
                },
            },
        }
    ]
    assert allocations == expected_response


def test_get_project_stats(client: TestClient, get_regular_user_token_headers: Dict[str, str]) -> None:
    # Add full year allocation for 1 FTE
    allocation_payload = {
        "userId": 1,
        "projectId": 2,
        "startDate": "2024-01-01",
        "endDate": "2024-12-31",
        "hoursPerDay": 8.0,
        "fte": 1,
        "isTentative": False,
        "isBillable": True,
        "notes": "test",
    }
    response = client.post(
        f"{API_BASE_URL}/v1/projects/2/allocations",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
        json=allocation_payload,
    )
    assert response.status_code == HTTPStatus.CREATED

    response = client.get(
        f"{API_BASE_URL}/v1/projects/2/stats?start=2024-01-01&end=2024-12-31",
        headers=get_regular_user_token_headers,
        params={"user_id": 1},
    )
    assert response.status_code == HTTPStatus.OK
    allocations = response.json()
    expected_response = {"avgFTE": 1.0, "loggedHours": 0.0, "plannedHours": 2096.0}
    assert allocations == expected_response
