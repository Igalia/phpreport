"""Add column to template table

Revision ID: 39723f474338
Revises: 3721dd962b84
Create Date: 2023-09-12 12:34:48.059196

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = "39723f474338"
down_revision = "3721dd962b84"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.alter_column("template", "usrid", nullable=True)
    op.add_column("template", sa.Column("is_global", sa.Boolean(), nullable=False, server_default=sa.false()))


def downgrade() -> None:
    # first, if we have any rows where usrid = null, set value to 0
    template = sa.sql.table("template")
    op.execute(template.update().where(template.c.usrid is None).values({"usrid": 0}))
    # now set the column back to not nullable
    op.alter_column("template", "usrid", nullable=False)
    # remove column we added
    op.drop_column("template", "is_global")
