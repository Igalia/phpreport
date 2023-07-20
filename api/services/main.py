from sqlalchemy.orm import Session


class AppService:
    def __init__(self, db: Session):
        self.db = db
