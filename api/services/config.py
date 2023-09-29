from datetime import date, timedelta
from services.main import AppService
from models.config import Config


class ConfigService(AppService):
    def can_user_edit_task(self, task_date: date) -> bool:
        config = self.db.query(Config).first() or None
        if not config:
            return True
        day_limit_date = task_date
        date_limit_date = task_date
        day_limit_enabled = config.block_tasks_by_day_limit_enabled
        date_limit_enabled = config.block_tasks_by_date_enabled
        number_of_days_limit = config.block_tasks_by_day_limit_number_of_days
        cutoff_date = config.block_tasks_by_date_date
        # if no limits enabled, just allow editing
        if not date_limit_enabled and not day_limit_enabled:
            return True
        if day_limit_enabled and number_of_days_limit is not None and number_of_days_limit > 0:
            day_limit_date = date.today() + timedelta(days=number_of_days_limit)
        if date_limit_enabled and cutoff_date is not None:
            date_limit_date = cutoff_date
        return task_date > day_limit_date or task_date > date_limit_date
