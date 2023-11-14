"""Add archive table and triggers

Revision ID: dc2b619f65a9
Revises: 72e6af46be22
Create Date: 2023-11-14 11:17:57.848580

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import postgresql
from alembic_utils.pg_function import PGFunction
from alembic_utils.pg_trigger import PGTrigger


# revision identifiers, used by Alembic.
revision = 'dc2b619f65a9'
down_revision = '72e6af46be22'
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table('archive',
    sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
    sa.Column('table_name', sa.VARCHAR(length=100), nullable=True),
    sa.Column('record_type', sa.VARCHAR(length=100), nullable=True),
    sa.Column('record_id', sa.INTEGER(), nullable=True),
    sa.Column('operation', sa.VARCHAR(length=50), nullable=True),
    sa.Column('old_values', postgresql.JSON(astext_type=sa.Text()), nullable=True),
    sa.Column('new_values', postgresql.JSON(astext_type=sa.Text()), nullable=True),
    sa.Column('most_recent', sa.BOOLEAN(), nullable=True),
    sa.Column('recorded_at', postgresql.TIMESTAMP(), nullable=True),
    sa.PrimaryKeyConstraint('id'))

    make_archive_of_changes = PGFunction(
            schema="public",
            signature="make_archive_of_changes()",
            definition="""
            RETURNS trigger
            LANGUAGE plpgsql
            AS $function$
            -- Expects one argument, the record_type
            -- It's the stringified ActiveRecord class name
            -- For example 'User', or 'Task'
            BEGIN
                UPDATE archive
                SET most_recent = FALSE
                WHERE
                table_name = TG_TABLE_NAME
                AND most_recent = TRUE
                AND record_type = record_type
                AND record_id = (
                    CASE WHEN TG_OP = 'DELETE'
                    THEN OLD.id
                    ELSE NEW.id
                    END
                );


                IF TG_OP = 'INSERT' THEN
                INSERT INTO archive (
                    table_name, record_type, record_id, operation, new_values, most_recent, recorded_at
                )
                VALUES (
                    TG_TABLE_NAME, TG_ARGV[0], NEW.id, TG_OP, row_to_json(NEW), TRUE, now()
                );
                RETURN NEW;

                ELSIF TG_OP = 'UPDATE' THEN
                INSERT INTO archive (
                    table_name, record_type, record_id, operation, new_values, old_values, most_recent, recorded_at
                )
                VALUES (
                    TG_TABLE_NAME, TG_ARGV[0], NEW.id, TG_OP, row_to_json(NEW), row_to_json(OLD), TRUE, now()
                );
                RETURN NEW;

                ELSIF TG_OP = 'DELETE' THEN
                INSERT INTO archive (
                    table_name, record_type, record_id, operation, old_values, most_recent, recorded_at
                )
                VALUES (
                    TG_TABLE_NAME, TG_ARGV[0], OLD.id, TG_OP, row_to_json(OLD), TRUE, now()
                );
                RETURN OLD;

                END IF;
            END;
            $function$
            ;
            """
    )

    op.create_entity(make_archive_of_changes)

    project_trigger = PGTrigger(
        schema="public",
        signature="trg_make_archive_of_changes_for_projects",
        on_entity="public.project",
        definition="""
        after insert or delete or update on
            public.project for each row execute function make_archive_of_changes('Project')
        """
    )
    op.create_entity(project_trigger)
    # ### end Alembic commands ###


def downgrade() -> None:
    op.drop_table('archive')
    # remove triggers
    project_trigger = PGTrigger(
        schema="public",
        signature="trg_make_archive_of_changes_for_projects",
        on_entity="public.project",
        definition="# Not used"
    )
    op.drop_entity(project_trigger)
    # remove function
    make_archive_of_changes = PGFunction(
        schema="public",
        signature="make_archive_of_changes()",
        definition="# Not Used"
    )
    op.drop_entity(make_archive_of_changes)

    # ### end Alembic commands ###
