from alembic_utils.pg_function import PGFunction


get_user_project_summaries = PGFunction(
    schema="public",
    signature="get_user_project_summaries(user_id integer, current date)",
    definition="""
        RETURNS TABLE (
            project_id INT,
            project VARCHAR,
            today_total INT,
            today_text VARCHAR,
            week_total INT,
            week_text VARCHAR,
            is_vacation BOOLEAN
        )
        LANGUAGE plpgsql
        AS $function$
        DECLARE vacation_project_id INT := (SELECT vacation_project_id from config);

        BEGIN
            RETURN QUERY
            with today as (
                select project.id as project_id, project.description,
                sum(task.task_total_minutes) as total
                from public.task
                inner join public.project
                on public.task.projectid = public.project.id
                where public.task._date = current
                and public.task.usrid = user_id
                group by project.id
            ),
            week as (
                select project.id as project_id, project.description,
                sum(task.task_total_minutes) as total
                from public.task
                inner join public.project
                on public.task.projectid = public.project.id
                where public.task._date between date_trunc('week', current)
                  and (date_trunc('week', current) + interval '6 days')
                  and public.task.usrid = user_id
                group by project.id
            )
            select
            week.project_id,
            week.description as project,
            today.total::int as today_total,
            case
                when today.total is not null then
                cast(
                concat(
                    extract(hour from make_interval(mins:=cast(today.total as int))), 'h ',
                    extract(minutes from make_interval(mins:=cast(today.total as int))), 'm')
                as varchar)
            end as today_text,
            week.total::int as week_total,
            case
                when week.total is not null then
                cast(
                concat(
                    extract(hour from make_interval(mins:=cast(week.total as int))), 'h ',
                    extract(minutes from make_interval(mins:=cast(week.total as int))), 'm')
                as varchar)
            end as week_text,
            case
                when week.project_id = vacation_project_id
                then (select true)
                else (select false)
            end as is_vacation
            from week
            left join today on week.project_id = today.project_id;
            END; $function$
        """,
)
