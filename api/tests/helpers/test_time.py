from datetime import datetime

from api.helpers.time import total_hours_between_dates


def test_total_hours_between_dates() -> None:
    start_date = datetime(2024, 1, 1)
    end_date = datetime(2024, 1, 14)
    hours_per_weekday = 8.0

    # There are 10 weekdays in the period set above, so 10 * 8 == 80
    assert total_hours_between_dates(start_date, end_date, hours_per_weekday) == 80.0
