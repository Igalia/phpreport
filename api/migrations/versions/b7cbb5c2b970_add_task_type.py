"""Add task type

Revision ID: b7cbb5c2b970
Revises: ff0037261dd9
Create Date: 2023-07-13 18:06:28.663432

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = "b7cbb5c2b970"
down_revision = "ff0037261dd9"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.alter_column("task", "ttype", nullable=True, new_column_name="ttype_tmp")

    op.create_table(
        "task_type",
        sa.Column("id", sa.Integer(), autoincrement=True, nullable=False),
        sa.Column("active", sa.Boolean(), nullable=True),
        sa.Column("name", sa.String(), nullable=True),
        sa.Column("slug", sa.String(), nullable=False),
        sa.PrimaryKeyConstraint("id"),
        sa.UniqueConstraint("slug"),
    )
    op.add_column("task", sa.Column("ttype", sa.String(), nullable=True))
    op.create_foreign_key("task_task_type_fkey", "task", "task_type", ["ttype"], ["slug"])

    conn = op.get_bind()

    # Clean up blank task types
    query = sa.text("UPDATE task SET ttype_tmp = NULL WHERE ttype_tmp in ('', ' ', 'NULL')")
    res = conn.execute(query)

    # Add existing task types
    query = sa.text(
        "INSERT INTO task_type(slug, name, active) \
        VALUES \
        ('administration', 'Administration', true), \
        ('analysis', 'Analysis', true), \
        ('community', 'Community', true), \
        ('coordination', 'Coordination', true), \
        ('demonstration', 'Demonstration', true), \
        ('deployment', 'Deployment', true), \
        ('design', 'Design', true), \
        ('documentation', 'Documentation', true), \
        ('environment', 'Environment', true), \
        ('implementation', 'Implementation', true), \
        ('maintenance', 'Maintenance', true), \
        ('publication', 'Publication', false), \
        ('requirements', 'Requirements', true), \
        ('sales', 'Sales', true), \
        ('sys_maintenance', 'Systems maintenance', true), \
        ('teaching', 'Teaching', true), \
        ('technology', 'Technology', true), \
        ('test', 'Test', true), \
        ('training', 'Training & Internal Events', true), \
        ('traveling', 'Travel', true), \
        ('deprecated_type', 'Deprecated Type', false)\
    "
    )
    res = conn.execute(query)

    # Migrate data from ttype to task_type, this might take a while
    query = sa.text(
        "UPDATE task SET ttype = ttype_tmp WHERE ttype_tmp is not null and ttype_tmp in (SELECT slug FROM task_type)"
    )
    res = conn.execute(query)

    query = sa.text("ALTER TABLE task DROP COLUMN ttype_tmp CASCADE")
    res = conn.execute(query)


def downgrade() -> None:
    op.alter_column("task", "ttype", nullable=True, new_column_name="ttype_tmp")

    op.add_column("task", sa.Column("ttype", sa.VARCHAR(length=40), autoincrement=False, nullable=True))
    conn = op.get_bind()
    query = sa.text("UPDATE task SET ttype = ttype_tmp")
    res = conn.execute(query)

    op.drop_constraint("task_task_type_fkey", "task", type_="foreignkey")
    op.drop_column("task", "ttype_tmp")
    op.drop_table("task_type")
