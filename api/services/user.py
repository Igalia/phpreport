from typing import List

from services.main import AppService
from models.user import User, UserRoles
from schemas.user import AppUser, UserCapacity


class UserProfile:
    def __init__(self, user_raw_data):
        self.id = user_raw_data.id
        self.username = user_raw_data.login
        self.email = user_raw_data.email
        self.first_name = user_raw_data.first_name
        self.last_name = user_raw_data.last_name
        self.avatar_url = user_raw_data.avatar_url
        self.roles = []
        self.authorized_scopes = []
        self.capacities = [
            UserCapacity(
                capacity=c.capacity,
                start=c.start,
                end=c.end,
                user_id=c.user_id,
                is_current=c.is_current,
                yearly_expected_and_vacation=c.yearly_expected_and_vacation,
            )
            for c in user_raw_data.capacities
        ]


class UserService(AppService):
    def get_users(self, active) -> List[User]:
        query = self.db.query(User)
        if active:
            query = query.filter(User.is_active)
        users = query.all() or []
        return [UserProfile(user) for user in users]

    def get_user(self, username: str) -> AppUser:
        user_in_db = self.db.query(User).filter(User.login == username).first() or None
        return UserProfile(user_in_db)

    def get_user_roles(self, user_id: int) -> List[UserRoles]:
        roles = self.db.query(UserRoles).filter(UserRoles.user_id == user_id).all() or []
        return roles

    def update_user(self, username: str, **kwargs) -> AppUser:
        user_in_db = self.db.query(User).filter(User.login == username).first() or None
        for param, value in kwargs.items():
            setattr(user_in_db, param, value)
        self.db.commit()
        self.db.refresh(user_in_db)
        return UserProfile(user_in_db)
