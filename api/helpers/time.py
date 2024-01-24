from datetime import timedelta, date
import math


def time_string_to_int(time_string: str) -> int:
    hours, minutes = time_string.split(":")
    return int(hours) * 60 + int(minutes)


def int_to_time_string(time_minutes: int) -> str:
    # Drop the seconds as the default format we are using is HH:mm
    return str(timedelta(minutes=time_minutes))[:-3]


def total_hours_between_dates(start_date, end_date, hours_per_weekday):
    current_date = start_date
    total_hours = 0

    while current_date <= end_date:
        if current_date.weekday() < 5:
            total_hours += hours_per_weekday
        current_date += timedelta(days=1)

    return total_hours


def int_to_time_long_string(time_minutes: int) -> str:
    return int_to_time_string(time_minutes).replace(":", "h ") + "m"


def vacation_int_to_string(time_minutes: int, user_capacity: float) -> str:
    if time_minutes == 0:
        return "None"

    hours_total = math.floor(time_minutes / 60)
    remaining_total_minutes = time_minutes % 60
    days = math.floor(time_minutes / (user_capacity * 60))
    leftover_minutes = time_minutes % (user_capacity * 60)
    hours = math.floor(leftover_minutes / 60)
    mins = time_minutes - (days * (user_capacity * 60)) - (hours * 60)

    vacation_time_string = ""
    if days > 0:
        vacation_time_string += f"{days} days"
    if hours > 0:
        vacation_time_string += " " if days > 0 else ""
        vacation_time_string += f"{hours} h"
    if mins > 0:
        vacation_time_string += " " if hours > 0 or days > 0 else ""
        vacation_time_string += f"{mins:.0f} m"
    if hours_total > 0 or remaining_total_minutes > 0:
        hours_total_string = f"{hours_total} h" if hours_total > 0 else ""
        remaining_mins_string = f" {remaining_total_minutes} m" if remaining_total_minutes > 0 else ""
        vacation_time_string += f" ({hours_total_string}{remaining_mins_string})"

    return f"{vacation_time_string}"

def get_start_and_end_date_of_isoweek(current_date: date) -> []:
    weekday = current_date.isoweekday()
    start = current_date - timedelta(days=weekday)
    end = start + timedelta(days=6)
    return [start, end]
