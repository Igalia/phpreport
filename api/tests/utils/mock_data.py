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
            {"login": "human_resources", "password": "human_resources"},
            {"login": "project_manager", "password": "project_manager"},
            {"login": "no_roles", "password": "no_roles"},
            {"login": "missing_scope", "password": "missing_scope"},
        ],
    ),
    (
        UserGroup,
        [
            {
                "id": 1,
                "name": "staff",
                "scopes": (
                    "task:create-own,task:read-own,task:update-own,task:delete-own,task_type:read,"
                    "template:create-own,template:update-own,template:read-own,template:read-global,template:delete-own"
                ),
            },
            {
                "id": 2,
                "name": "admin",
                "scopes": (
                    "task:create-own,task:read-own,task:update-own,task:delete-own,task_type:read,"
                    "template:create-own,template:update-own,template:read-own,template:read-global,"
                    "template:delete-own,template:create-global,template:update-global,template:delete-global"
                ),
            },
            {
                "id": 3,
                "name": "manager",
                "scopes": (
                    "task:create-own,task:read-own,task:update-own,task:delete-own,task_type:read,"
                    "template:create-own,template:update-own,template:read-own,template:read-global,"
                    "template:delete-own,template:create-global,template:update-global,template:delete-global"
                ),
            },
            {
                "id": 4,
                "name": "human resources",
                "scopes": (
                    "task:create-own,task:read-own,task:update-own,task:delete-own,task_type:read,"
                    "template:create-own,template:update-own,template:read-own,template:read-global,"
                    "template:delete-own"
                ),
            },
            {
                "id": 5,
                "name": "project manager",
                "scopes": "task:create-own,task:read-own,task:update-own,task:delete-own,task_type:read,"
                "template:create-own,template:update-own,template:read-own,template:read-global,"
                "template:delete-own",
            },
            {"id": 6, "name": "few scopes", "scopes": "task:read-own"},
        ],
    ),
    (
        UserRoles,
        [
            {"group_id": 1, "user_id": 1},
            {"group_id": 1, "user_id": 2},
            {"group_id": 2, "user_id": 2},
            {"group_id": 1, "user_id": 3},
            {"group_id": 3, "user_id": 3},
            {"group_id": 1, "user_id": 4},
            {"group_id": 4, "user_id": 4},
            {"group_id": 1, "user_id": 5},
            {"group_id": 5, "user_id": 5},
            {"group_id": 6, "user_id": 7},
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
            {
                "description": "Holidays",
                "area_id": 1,
                "customer_id": 1,
                "is_active": True,
            },
            {
                "description": "Internal",
                "area_id": 1,
                "customer_id": 1,
                "is_active": True,
            },
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
