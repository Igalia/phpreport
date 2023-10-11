from fastapi.testclient import TestClient


def test_status_enpoint(client: TestClient) -> None:
    response = client.get(
        "/status",
    )
    assert response.status_code == 200
    content = response.json()
    assert content["message"] == "OK"
