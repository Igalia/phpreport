from alembic_utils.pg_function import PGFunction


get_vacation_for_period = PGFunction(
    schema="public",
    signature="get_vacation_for_period(start_date date,end_date date,capacity numeric)",
    definition="""
        /*
         * Author: Danielle Mayabb, Dec 2023
         * Purpose: Provided a time range and daily capacity, gets the amount of vacation hours
         * available to a user
         * Notes: When getting the difference of dates, the end date is not included,
         * so 1 day is added to the calculation
         * Simplified formula is:
         * (Number of days in period) / (Number of days in year)
         * * yearly_vacation_hours * (daily_capacity * 5 / company_fte)
         * So we determine the fraction of the year for which someone is working (it will be <=1)
         * Multiply that by total available vacation hours so that if someone is only working part of the year,
         * they get the correct proportion of vacation
         * Next, we need to get a fraction for the capacity which is daily_capacity * 5 (for weekly capacity)
         * divided by company_fte.
         * This is the percentage of time of FTE someone works.
         */
        returns numeric(8,4)
        as $$
            /*
             * First, we need to get some constants:
             * 1. The amount of yearly vacation hours for the company
             * 2. What is considered FTE for the company (per week)
             */
            declare yearly_vacation_hours int := (select config.yearly_vacation_hours from public.config limit 1);
            declare company_fte_weekly int := (select config.company_fte from public.config limit 1);
            BEGIN
                return(
                    select (end_date - start_date + 1) /
                        (
                        make_date(date_part('year', end_date::date)::int, 12, 31) -
                        make_date(date_part('year', start_date::date)::int, 01, 01) + 1
                        )::float
                    * yearly_vacation_hours
                    * (capacity * 5 / company_fte_weekly::float)
                );
            end;
            $$
        LANGUAGE plpgsql
        ;
""",
)
