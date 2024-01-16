from typing import List
from datetime import date, datetime, timedelta
from helpers.time import total_hours_between_dates
from services.main import AppService
from models.config import Config
from models.timelog import Task
from models.project import Project, ProjectAllocation
from schemas.project import BaseProjectAllocation, ProjectAllocationPerUser


def group_allocations_per_user(allocations):
    allocations_grouped_per_user = {}

    for allocation in allocations:
        username = allocation.username
        current_date = allocation.start_date
        while current_date <= allocation.end_date:
            iso_week = current_date.isocalendar()[1]
            month_name = current_date.strftime("%b")
            day_of_week = current_date.weekday()
            days_until_monday = (day_of_week - 0) % 7
            first_day_of_week = current_date - timedelta(days=days_until_monday)
            week_key = f"{first_day_of_week.year}-{iso_week}"
            if username not in allocations_grouped_per_user:
                allocations_grouped_per_user[username] = {"username": username, "hours": {}}
            if week_key not in allocations_grouped_per_user[username]["hours"]:
                allocations_grouped_per_user[username]["hours"][week_key] = {
                    "days": [],
                    "ISOWeek": iso_week,
                    "month": month_name,
                    "totalHours": 0,
                    "project": allocation.project_id,
                    "isLeave": False,  # TODO Replace with the logic to determine if it's leave
                }
            if day_of_week < 5:
                allocations_grouped_per_user[username]["hours"][week_key]["totalHours"] += allocation.hours_per_day
            allocations_grouped_per_user[username]["hours"][week_key]["days"].append(str(current_date))

            current_date += timedelta(days=1)
    return list(allocations_grouped_per_user.values())


class ProjectService(AppService):
    def get_items(self, offset, limit, status) -> List[Project]:
        query = self.db.query(Project)
        if status == "active":
            query = query.filter(Project.activation is True)
        if status == "inactive":
            query = query.filter(Project.activation is False)
        return query.offset(offset).limit(limit).all() or []

    def is_project_active(self, project_id) -> bool:
        project = self.db.query(Project).where(Project.id == project_id).first() or None
        if project is not None:
            return project.is_active
        return False

    def get_project_allocations(
        self, project_id: int, start: date = None, end: date = None
    ) -> List[ProjectAllocationPerUser]:
        # TODO improve the date filter logic to get more precise results
        query = self.db.query(ProjectAllocation).where(
            ProjectAllocation.project_id == project_id, ProjectAllocation.start_date.between(start, end)
        )
        allocations = query.all() or []

        return group_allocations_per_user(allocations)

    def create_project_allocation(self, allocation: BaseProjectAllocation, created_by: str) -> ProjectAllocation:
        new_allocation = ProjectAllocation(
            user_id=allocation.user_id,
            project_id=allocation.project_id,
            start_date=allocation.start_date,
            end_date=allocation.end_date,
            hours_per_day=allocation.hours_per_day,
            fte=allocation.fte,
            is_tentative=allocation.is_tentative,
            is_billable=allocation.is_billable,
            notes=allocation.notes,
            created_at=datetime.now(),
            created_by=created_by,
        )
        self.db.add(new_allocation)
        self.db.commit()
        self.db.refresh(new_allocation)
        return new_allocation

    def get_project_stats(
        self, project_id: int, start: date = None, end: date = None
    ) -> List[ProjectAllocationPerUser]:
        query = self.db.query(Task).where(Task.project_id == project_id, Task.date.between(start, end))
        tasks = query.all() or []
        logged_hours = round((sum(task.end - task.init for task in tasks) / 60), 2)
        query = self.db.query(ProjectAllocation).where(
            ProjectAllocation.project_id == project_id, ProjectAllocation.start_date.between(start, end)
        )
        allocations = query.all() or []
        plannedHours = sum(
            total_hours_between_dates(allocation.start_date, allocation.end_date, allocation.hours_per_day)
            for allocation in allocations
        )
        config = self.db.query(Config).first()
        company_fte_per_day = (config.company_fte) / 5 if config else 8
        avg_fte = round(plannedHours / total_hours_between_dates(start, end, float(company_fte_per_day)), 2)

        return {"loggedHours": logged_hours, "plannedHours": plannedHours, "avgFTE": avg_fte}
