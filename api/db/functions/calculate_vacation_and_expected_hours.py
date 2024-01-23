from alembic_utils.pg_function import PGFunction

calculate_vacation_and_expected_hours = PGFunction(
    schema="public",
    signature="calculate_vacation_and_expected_hours()",
    definition="""
        /*
         * Author: Danielle Mayabb, Dec 2023
         * Purpose: On insert/update of capacity (journey_history), we run this function to calculate two values:
         * 1. The total available hours for a user inside each calendar year in capacity range
         * 2. The total hours a user is expected to work in a capacity range given their daily capacity
         */
         RETURNS trigger
         LANGUAGE plpgsql
         AS $function$
            declare init_year text := cast(date_part('year', NEW.init_date) as text);
            declare end_year text := cast(date_part('YEAR', NEW.end_date) as text);
            declare yearly_vacation_hours int := (select config.yearly_vacation_hours from public.config limit 1);

            BEGIN
            --first check to see if journey is within same year; if so, do a simple insert
            IF (select date_trunc('YEAR', NEW.init_date::date) = date_trunc('YEAR', NEW.end_date::date)) = true THEN
                NEW.yearly_expected_and_vacation := jsonb_build_array(
                        jsonb_build_object(init_year,
                            jsonb_build_object(
                                    'expectedHours',
                                    NEW.journey *
                                    (select get_workday_count(NEW.init_date, NEW.end_date)),
                                    'availableVacation',
                                    (select get_vacation_for_period(NEW.init_date, NEW.end_date, NEW.journey))
                            )
                        ));
            RETURN NEW;

            ELSEIF (select date_trunc('YEAR', NEW.init_date::date) = date_trunc('YEAR', NEW.end_date::date))
              = false THEN
                -- if many years are in range, we need to do a little more work
                -- first, for each year in the range we need to get the dates inside each year as a range
                --and hold onto them in a temp table
                create temp table ranges(
                    year int,
                    range_start date,
                    range_end date,
                    range_in_year daterange
                );
                WITH
                  t(start_date,end_date) AS (VALUES (NEW.init_date::date, NEW.end_date::date)),
                  u AS (
                    SELECT
                      GENERATE_SERIES(EXTRACT(YEAR FROM start_date)::INT, EXTRACT(YEAR FROM end_date)::INT) AS year,
                      daterange(start_date,end_date) *
                      daterange(make_date(
                        GENERATE_SERIES(
                          EXTRACT(YEAR FROM start_date)::INT,
                          EXTRACT(YEAR FROM end_date)::INT),1,1)::date,
                          make_date(GENERATE_SERIES(EXTRACT(YEAR FROM start_date)::INT,
                          EXTRACT(YEAR FROM end_date)::INT),12,31)::date) as range_in_year
                    FROM
                      t)
                        insert into ranges select year,
                        lower(range_in_year),
                        upper(range_in_year),
                        range_in_year from u;
                        -- now we need to get all the rows from our temp table
                        -- and aggregate them into a json array
                         NEW.yearly_expected_and_vacation := (select jsonb_agg(
                            jsonb_build_object(
                                ranges.year::text,
                                jsonb_build_object(
                                  'expectedHours',
                                  NEW.journey * (select get_workday_count(ranges.range_start, ranges.range_end)),
                                  'availableVacation',
                                  (select get_vacation_for_period(ranges.range_start, ranges.range_end, NEW.journey))
                                )
                            )) from ranges
                          );
                        -- finally, we can get rid of our temp table
                        drop table ranges;
            RETURN NEW;

                    END IF;

                    END;
                    $function$
        ;
        """,
)
