from fastapi import APIRouter

router = APIRouter(
    prefix="/projects",
    tags=["projects"]
)

@router.get("/")
async def read_projects():
    return [{"project": "Test"}, {"project": "Test2"}]


@router.get("/{project_id}")
async def read_project(project_id: int):
    return {"project_id": project_id}

