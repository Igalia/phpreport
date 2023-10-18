from datetime import timedelta


def time_string_to_int(time_string: str) -> int:
    hours, minutes = time_string.split(":")
    return int(hours) * 60 + int(minutes)


def int_to_time_string(time_minutes: int) -> str:
    # Drop the seconds as the default format we are using is HH:mm
    return str(timedelta(minutes=time_minutes))[:-3]
