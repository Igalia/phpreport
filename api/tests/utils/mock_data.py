from models.area import Area
from models.customer import Customer
from models.project import Project
from models.timelog import TaskType, Template
from models.user import User, UserGroup, UserRoles
from models.sector import Sector


DATA = [
    (
        User,
        [
            {"id": 1, "login": "user", "password": "user"},
            {"id": 2, "login": "admin", "password": "admin"},
            {"id": 3, "login": "manager", "password": "manager"},
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
        [{"id": 1, "name": "internal"}],
    ),
    (
        Sector,
        [{"id": 1, "name": "tech"}],
    ),
    (
        Customer,
        [{"id": 1, "name": "Internal Customer", "customer_type": "Small", "sector_id": 1}],
    ),
    (
        Project,
        [{"id": 1, "description": "Holidays", "area_id": 1, "customer_id": 1, "is_active": True}],
    ),
    (
        TaskType,
        [
            {"id": 1, "active": True, "name": "Meeting", "slug": "meeting"},
            {"id": 2, "active": False, "name": "Deprecated Type", "slug": "deprecated"},
        ],
    ),
    (
        Template,
        [
            {
                "id": 1,
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
                "id": 2,
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
                "id": 3,
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
]
