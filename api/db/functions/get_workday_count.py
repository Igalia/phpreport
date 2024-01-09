from alembic_utils.pg_function import PGFunction


get_workday_count = PGFunction(
    schema="public",
    signature="get_workday_count(start_date date, end_date date)",
    definition="""
            /*
            * Author: Danielle Mayabb, Dec 2023
            * Purpose: Provided a time range, gets the number of work days for the period,
            * i.e., gets the count of non-weekend days
            */
            RETURNS integer
            as $$
            BEGIN
                return (
                select count(generated_date::date)
                from generate_series(
                        start_date,
                        end_date,
                        interval '1 day') as generated_date
                where extract(isodow from generated_date) < 6);
            END;
            $$
            LANGUAGE plpgsql
            ;
""",
)
