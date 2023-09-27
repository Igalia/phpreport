from typing import List

from services.main import AppService
from models.user import User, UserRoles


class UserService(AppService):
    def get_users(self) -> List[User]:
        users = self.db.query(User).all() or []
        return users

    def get_user(self, username: str) -> User:
        user = self.db.query(User).filter(User.login == username).first() or None
        return user

    def get_user_roles(self, user_id: int) -> List[UserRoles]:
        roles = self.db.query(UserRoles).filter(UserRoles.user_id == user_id).all() or []
        return roles
