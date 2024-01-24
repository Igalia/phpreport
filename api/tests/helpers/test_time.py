from datetime import datetime

from api.helpers.time import (
    total_hours_between_dates,
    time_string_to_int,
    int_to_time_string,
    int_to_time_long_string,
    vacation_int_to_string,
    get_start_and_end_date_of_isoweek,
)


def test_total_hours_between_dates() -> None:
    start_date = datetime(2024, 1, 1)
    end_date = datetime(2024, 1, 14)
    hours_per_weekday = 8.0

    # There are 10 weekdays in the period set above, so 10 * 8 == 80
    assert total_hours_between_dates(start_date, end_date, hours_per_weekday) == 80.0


def test_time_string_to_int() -> None:
    time_string = "4:30"

    assert time_string_to_int(time_string) == 270


def test_into_to_time_string() -> None:
    time_minutes = 400

    assert int_to_time_string(time_minutes) == "6:40"


def test_int_to_time_long_string() -> None:
    time_minutes = 870

    assert int_to_time_long_string(time_minutes) == "14h 30m"


def test_vacation_int_to_string() -> None:
    time_minutes = 1285
    user_capacity = 8.0

    assert vacation_int_to_string(time_minutes, user_capacity) == "2 days 5 h 25 m (21 h 25 m)"


def test_get_start_and_end_date_of_week() -> None:
    current_date = datetime(2024, 1, 10)
    start_and_end = get_start_and_end_date_of_isoweek(current_date)

    assert start_and_end[0] == datetime(2024, 1, 7)
    assert start_and_end[1] == datetime(2024, 1, 13)
