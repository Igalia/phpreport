from datetime import timedelta


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
