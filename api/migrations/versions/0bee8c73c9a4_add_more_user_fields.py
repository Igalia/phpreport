"""add more user fields

Revision ID: 0bee8c73c9a4
Revises: fb8c0e665690
Create Date: 2024-01-23 10:54:52.530939

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = '0bee8c73c9a4'
down_revision = 'fb8c0e665690'
branch_labels = None
depends_on = None


def upgrade() -> None:
    # ### commands auto generated by Alembic - please adjust! ###
    op.add_column('usr', sa.Column('email', sa.String(length=100), nullable=True))
    op.add_column('usr', sa.Column('first_name', sa.String(length=100), nullable=True))
    op.add_column('usr', sa.Column('last_name', sa.String(length=100), nullable=True))
    op.add_column('usr', sa.Column('avatar_url', sa.String(length=500), nullable=True))
    op.add_column('usr', sa.Column('is_active', sa.Boolean()))
    op.create_unique_constraint('uq_user_email', 'usr', ['email'])
    # ### end Alembic commands ###


def downgrade() -> None:
    # ### commands auto generated by Alembic - please adjust! ###
    op.drop_constraint('uq_user_email', 'usr', type_='unique')
    op.drop_column('usr', 'is_active')
    op.drop_column('usr', 'last_name')
    op.drop_column('usr', 'first_name')
    op.drop_column('usr', 'email')
    op.drop_column('usr', 'avatar_url')
    # ### end Alembic commands ###
