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
        [{"description": "Holidays", "area_id": 1, "customer_id": 1, "is_active": True}],
    ),
    (
        TaskType,
        [
            {"active": True, "name": "Meeting", "slug": "meeting"},
            {"active": False, "name": "Deprecated Type", "slug": "deprecated"},
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
]
