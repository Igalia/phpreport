from alembic_utils.pg_trigger import PGTrigger


trg_update_yearly_expected_and_vacation = PGTrigger(
    schema="public",
    signature="trg_update_yearly_expected_and_vacation",
    on_entity="public.journey_history",
    definition="""
        before insert or update on
            public.journey_history for each row execute procedure calculate_vacation_and_expected_hours();
        """,
)
