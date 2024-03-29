"""Remove customer from templates

Revision ID: 8c480b641f22
Revises: 39723f474338
Create Date: 2023-10-19 15:10:52.957877

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = '8c480b641f22'
down_revision = '39723f474338'
branch_labels = None
depends_on = None


def upgrade() -> None:
    # ### commands auto generated by Alembic - please adjust! ###
    op.drop_constraint('template_customerid_fkey', 'template', type_='foreignkey')
    op.drop_column('template', 'customerid')
    # ### end Alembic commands ###


def downgrade() -> None:
    # ### commands auto generated by Alembic - please adjust! ###
    op.add_column('template', sa.Column('customerid', sa.INTEGER(), autoincrement=False, nullable=True))
    op.create_foreign_key('template_customerid_fkey', 'template', 'customer', ['customerid'], ['id'])
    # ### end Alembic commands ###
