from models.area import Area
from models.customer import Customer
from models.project import Project
from models.timelog import Task, TaskType, Template
from models.user import User, UserGroup, UserRoles
from models.sector import Sector


DATA = [
    (
        User,
        [
            {"login": "user", "password": "user"},
            {"login": "admin", "password": "admin"},
            {"login": "manager", "password": "manager"},
        ],
    ),
    (UserGroup, [{"id": 1, "name": "staff"}, {"id": 2, "name": "admin"}, {"id": 3, "name": "manager"}]),
    (
        UserRoles,
        [
            {"group_id": 1, "user_id": 1},
            {"group_id": 1, "user_id": 2},
            {"group_id": 2, "user_id": 2},
            {"group_id": 1, "user_id": 3},
            {"group_id": 3, "user_id": 3},
        ],
    ),
    (
        Area,
        [{"name": "internal"}],
    ),
    (
        Sector,
        [{"name": "tech"}],
    ),
    (
        Customer,
        [{"name": "Internal Customer", "customer_type": "Small", "sector_id": 1}],
    ),
    (
        Project,
        [
            {"description": "Holidays", "area_id": 1, "customer_id": 1, "is_active": True},
            {"description": "Internal", "area_id": 1, "customer_id": 1, "is_active": True},
        ],
    ),
    (
        TaskType,
        [
            {"active": True, "name": "Meeting", "slug": "meeting"},
            {"active": False, "name": "Deprecated Type", "slug": "deprecated"},
            {"active": True, "name": "Project time", "slug": "project"},
        ],
    ),
    (
        Template,
        [
            {
                "name": "Coffee Break",
                "story": "coffee",
                "description": "Need to recharge",
                "task_type": "meeting",
                "init": 0,
                "end": 420,
                "user_id": 1,
                "is_global": False,
            },
            {
                "name": "Series time",
                "story": None,
                "description": "Watching The Orville",
                "task_type": "meeting",
                "init": 0,
                "end": 210,
                "user_id": 2,
                "is_global": False,
            },
            {
                "name": "Working at night",
                "story": None,
                "description": "Working late",
                "task_type": "meeting",
                "init": 1200,
                "end": 1320,
                "user_id": None,
                "is_global": True,
            },
        ],
    ),
    (
        Task,
        [
            {
                "date": "2023-10-20",
                "init": 1200,
                "end": 1320,
                "story": "that project",
                "task_type": "project",
                "description": "Working in that awesome project",
                "user_id": 1,
                "project_id": 2,
            },
            {
                "date": "2023-10-20",
                "init": 1200,
                "end": 1320,
                "story": "that project",
                "task_type": "project",
                "description": "Doing some stuff",
                "user_id": 2,
                "project_id": 2,
            },
        ],
    ),
]
