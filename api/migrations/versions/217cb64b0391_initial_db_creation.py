"""Initial db creation

Revision ID: 217cb64b0391
Revises:
Create Date: 2023-06-21 17:53:19.687876

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import postgresql
from sqlalchemy import text

# revision identifiers, used by Alembic.
revision = '217cb64b0391'
down_revision = None
branch_labels = None
depends_on = None


def table_exists(table_name):
    connection = op.get_bind()
    query = text("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = :table)")
    result = connection.execute(query.params(table=table_name))
    return result.scalar()


def upgrade() -> None:
    if not table_exists('area'):
        op.create_table('area',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('name', sa.String(length=256), nullable=False),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('name')
        )
    if not table_exists('city'):
        op.create_table('city',
        sa.Column('id', sa.Integer(), server_default=sa.text("nextval('city_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.String(length=30), nullable=False),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('name')
        )
    if not table_exists('config'):
        op.create_table('config',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('version', sa.String(length=20), nullable=True),
        sa.Column('block_tasks_by_time_enabled', sa.Boolean(), nullable=False),
        sa.Column('block_tasks_by_time_number_of_days', sa.Integer(), nullable=True),
        sa.Column('block_tasks_by_day_limit_enabled', sa.Boolean(), nullable=False),
        sa.Column('block_tasks_by_day_limit_number_of_days', sa.Integer(), nullable=True),
        sa.Column('block_tasks_by_date_enabled', sa.Boolean(), nullable=False),
        sa.Column('block_tasks_by_date_date', sa.Date(), nullable=True),
        sa.PrimaryKeyConstraint('id')
        )
    if not table_exists('sector'):
        op.create_table('sector',
        sa.Column('id', sa.Integer(), server_default=sa.text("nextval('sector_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.String(length=256), nullable=False),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('name')
        )
    if not table_exists('user_group'):
        op.create_table('user_group',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('name', sa.String(length=128), nullable=True),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('name')
        )
    if not table_exists('usr'):
        op.create_table('usr',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('password', sa.String(length=256), nullable=True),
        sa.Column('login', sa.String(length=20), nullable=False),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('login')
        )
    if not table_exists('area_history'):
        op.create_table('area_history',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('init_date', sa.Date(), nullable=False),
        sa.Column('end_date', sa.Date(), nullable=True),
        sa.Column('areaid', sa.Integer(), nullable=False),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.CheckConstraint('end_date IS NULL OR end_date >= init_date', name='end_after_init_area_history'),
        sa.ForeignKeyConstraint(['areaid'], ['area.id'], ),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('usrid', 'init_date', name='unique_area_history_user_date')
        )
    if not table_exists('belongs'):
        op.create_table('belongs',
        sa.Column('user_groupid', sa.Integer(), nullable=False),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.ForeignKeyConstraint(['user_groupid'], ['user_group.id'], ),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.PrimaryKeyConstraint('user_groupid')
        )
    if not table_exists('city_history'):
        op.create_table('city_history',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('cityid', sa.Integer(), nullable=False),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.Column('init_date', sa.Date(), nullable=False),
        sa.Column('end_date', sa.Date(), nullable=True),
        sa.CheckConstraint('end_date IS NULL OR end_date >= init_date', name='end_after_init_city_history'),
        sa.ForeignKeyConstraint(['cityid'], ['city.id'], ),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('usrid', 'init_date', name='unique_city_history_user_date')
        )
    if not table_exists('common_event'):
        op.create_table('common_event',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('_date', sa.Date(), nullable=False),
        sa.Column('cityid', sa.Integer(), nullable=False),
        sa.ForeignKeyConstraint(['cityid'], ['city.id'], ),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('cityid', '_date', name='unique_common_event_city_date')
        )
    if not table_exists('customer'):
        op.create_table('customer',
        sa.Column('id', sa.Integer(), server_default=sa.text("nextval('customer_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.String(length=256), nullable=False),
        sa.Column('type', sa.String(length=256), nullable=False),
        sa.Column('url', sa.String(length=8192), nullable=True),
        sa.Column('sectorid', sa.Integer(), nullable=False),
        sa.ForeignKeyConstraint(['sectorid'], ['sector.id'], ),
        sa.PrimaryKeyConstraint('id')
        )
    if not table_exists('extra_hour'):
        op.create_table('extra_hour',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('_date', sa.Date(), nullable=False),
        sa.Column('hours', sa.Double(), nullable=False),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.Column('comment', sa.String(length=256), nullable=True),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('usrid', '_date', name='unique_extra_hour_user_date')
        )
    if not table_exists('hour_cost_history'):
        op.create_table('hour_cost_history',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('hour_cost', sa.Numeric(precision=8, scale=4), nullable=False),
        sa.Column('init_date', sa.Date(), nullable=False),
        sa.Column('end_date', sa.Date(), nullable=True),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.CheckConstraint('end_date IS NULL OR end_date >= init_date', name='end_after_init_hour_cost_history'),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('usrid', 'init_date', name='unique_hour_cost_history_user_date')
        )
    if not table_exists('journey_history'):
        op.create_table('journey_history',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('journey', sa.Numeric(precision=8, scale=4), nullable=False),
        sa.Column('init_date', sa.Date(), nullable=False),
        sa.Column('end_date', sa.Date(), nullable=True),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.CheckConstraint('end_date IS NULL OR end_date >= init_date', name='end_after_init_journey_history'),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('usrid', 'init_date', name='unique_journey_history_user_date')
        )
    if not table_exists('user_goals'):
        op.create_table('user_goals',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('init_date', sa.Date(), nullable=False),
        sa.Column('end_date', sa.Date(), nullable=False),
        sa.Column('extra_hours', sa.Double(precision=53), nullable=False),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.PrimaryKeyConstraint('id')
        )
    if not table_exists('project'):
        op.create_table('project',
        sa.Column('id', sa.Integer(), server_default=sa.text("nextval('project_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('activation', sa.Boolean(), nullable=False),
        sa.Column('init', sa.Date(), nullable=True),
        sa.Column('_end', sa.Date(), nullable=True),
        sa.Column('invoice', sa.Double(), nullable=True),
        sa.Column('est_hours', sa.Double(), nullable=True),
        sa.Column('moved_hours', sa.Double(), nullable=True),
        sa.Column('description', sa.String(length=256), nullable=True),
        sa.Column('type', sa.String(length=256), nullable=True),
        sa.Column('sched_type', sa.String(length=256), nullable=True),
        sa.Column('customerid', sa.Integer(), nullable=True),
        sa.Column('areaid', sa.Integer(), nullable=False),
        sa.ForeignKeyConstraint(['areaid'], ['area.id'], ),
        sa.ForeignKeyConstraint(['customerid'], ['customer.id'], ),
        sa.PrimaryKeyConstraint('id')
        )
    if not table_exists('project_usr'):
        op.create_table('project_usr',
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.Column('projectid', sa.Integer(), nullable=False),
        sa.ForeignKeyConstraint(['projectid'], ['project.id'], ),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.PrimaryKeyConstraint('usrid')
        )

    # ----------------------------------------------------------------------
    # Deprecated models that should be in the initial migration
    # for compatibility with the current production DB state
    if not table_exists('iteration'):
        op.create_table('iteration',
        sa.Column('id', sa.INTEGER(), server_default=sa.text("nextval('iteration_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
        sa.Column('summary', sa.VARCHAR(length=256), autoincrement=False, nullable=True),
        sa.Column('init', sa.DATE(), autoincrement=False, nullable=False),
        sa.Column('_end', sa.DATE(), autoincrement=False, nullable=True),
        sa.Column('projectid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.CheckConstraint('_end >= init', name='end_after_init_iteration'),
        sa.ForeignKeyConstraint(['projectid'], ['project.id'], name='plans'),
        sa.PrimaryKeyConstraint('id', name='pk_iteration'),
        sa.UniqueConstraint('projectid', 'name', name='unique_iteration_project_name'),
        postgresql_ignore_search_path=False
        )
    if not table_exists('story'):
        op.create_table('story',
        sa.Column('id', sa.INTEGER(), server_default=sa.text("nextval('story_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
        sa.Column('accepted', sa.BOOLEAN(), autoincrement=False, nullable=True),
        sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.Column('iterationid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.Column('storyid', sa.INTEGER(), autoincrement=False, nullable=True),
        sa.ForeignKeyConstraint(['iterationid'], ['iteration.id'], name='contains'),
        sa.ForeignKeyConstraint(['storyid'], ['story.id'], name='next'),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], name='leads_st'),
        sa.PrimaryKeyConstraint('id', name='pk_story'),
        sa.UniqueConstraint('iterationid', 'name', name='unique_story_iteration_name'),
        postgresql_ignore_search_path=False
        )
    if not table_exists('module'):
        op.create_table('module',
        sa.Column('id', sa.INTEGER(), server_default=sa.text("nextval('module_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
        sa.Column('summary', sa.VARCHAR(length=256), autoincrement=False, nullable=True),
        sa.Column('init', sa.DATE(), autoincrement=False, nullable=False),
        sa.Column('_end', sa.DATE(), autoincrement=False, nullable=True),
        sa.Column('projectid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.CheckConstraint('_end >= init', name='end_after_init_iteration'),
        sa.ForeignKeyConstraint(['projectid'], ['project.id'], name='analyzes'),
        sa.PrimaryKeyConstraint('id', name='pk_module'),
        sa.UniqueConstraint('projectid', 'name', name='unique_module_project_name'),
        postgresql_ignore_search_path=False
        )
    if not table_exists('section'):
        op.create_table('section',
        sa.Column('id', sa.INTEGER(), server_default=sa.text("nextval('section_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
        sa.Column('accepted', sa.BOOLEAN(), autoincrement=False, nullable=True),
        sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.Column('moduleid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.Column('text', sa.VARCHAR(length=8192), autoincrement=False, nullable=True),
        sa.ForeignKeyConstraint(['moduleid'], ['module.id'], name='describes'),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], name='leads_sc'),
        sa.PrimaryKeyConstraint('id', name='pk_section'),
        sa.UniqueConstraint('moduleid', 'name', name='unique_section_iteration_name'),
        postgresql_ignore_search_path=False
        )
    if not table_exists('task_section'):
        op.create_table('task_section',
        sa.Column('id', sa.INTEGER(), server_default=sa.text("nextval('task_section_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
        sa.Column('est_hours', sa.DOUBLE_PRECISION(precision=53), autoincrement=False, nullable=False),
        sa.Column('sectionid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.Column('risk', sa.INTEGER(), autoincrement=False, nullable=True),
        sa.CheckConstraint('est_hours >= 0::double precision', name='positive_est_hours_task_story'),
        sa.ForeignKeyConstraint(['sectionid'], ['section.id'], name='shapes'),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], name='assigned_to'),
        sa.PrimaryKeyConstraint('id', name='pk_task_section'),
        sa.UniqueConstraint('sectionid', 'name', name='unique_task_section_section_name'),
        postgresql_ignore_search_path=False
        )
    if not table_exists('task_story'):
        op.create_table('task_story',
        sa.Column('id', sa.INTEGER(), server_default=sa.text("nextval('task_story_id_seq'::regclass)"), autoincrement=True, nullable=False),
        sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
        sa.Column('risk', sa.INTEGER(), autoincrement=False, nullable=True),
        sa.Column('est_hours', sa.DOUBLE_PRECISION(precision=53), autoincrement=False, nullable=False),
        sa.Column('init', sa.DATE(), autoincrement=False, nullable=False),
        sa.Column('_end', sa.DATE(), autoincrement=False, nullable=True),
        sa.Column('est_end', sa.DATE(), autoincrement=False, nullable=False),
        sa.Column('storyid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.Column('to_do', sa.DOUBLE_PRECISION(precision=53), autoincrement=False, nullable=True),
        sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
        sa.Column('task_sectionid', sa.INTEGER(), autoincrement=False, nullable=True),
        sa.CheckConstraint('_end IS NULL OR _end >= init', name='end_after_init_task_story'),
        sa.CheckConstraint('est_end IS NULL OR est_end >= init', name='est_end_after_init_task_story'),
        sa.CheckConstraint('est_hours >= 0::double precision', name='positive_est_hours_task_story'),
        sa.ForeignKeyConstraint(['storyid'], ['story.id'], name='constitutes'),
        sa.ForeignKeyConstraint(['task_sectionid'], ['task_section.id'], name='develops'),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], name='developer'),
        sa.PrimaryKeyConstraint('id', name='pk_task_story'),
        sa.UniqueConstraint('storyid', 'name', name='unique_task_story_story_name'),
        postgresql_ignore_search_path=False
        )
        # ----------------------------------------------------------------------

    if not table_exists('task'):
        op.create_table('task',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('_date', sa.Date(), nullable=False),
        sa.Column('init', sa.Integer(), nullable=False),
        sa.Column('_end', sa.Integer(), nullable=False),
        sa.Column('story', sa.String(length=80), nullable=True),
        sa.Column('telework', sa.Boolean(), nullable=True),
        sa.Column('text', sa.String(length=8192), nullable=True),
        sa.Column('ttype', sa.String(length=40), nullable=True),
        sa.Column('phase', sa.String(length=15), nullable=True),
        sa.Column('onsite', sa.Boolean(), nullable=False),
        sa.Column('updated_at', postgresql.TIMESTAMP(), nullable=True),
        sa.Column('customerid', sa.Integer(), nullable=True),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.Column('task_storyid', sa.INTEGER(), autoincrement=False, nullable=True),
        sa.Column('projectid', sa.Integer(), nullable=False),
        sa.ForeignKeyConstraint(['customerid'], ['customer.id'], ),
        sa.ForeignKeyConstraint(['projectid'], ['project.id'], ),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.ForeignKeyConstraint(['task_storyid'], ['task_story.id'], name='includes'),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('usrid', 'init', '_date', name='unique_task_usr_time')
        )
    if not table_exists('template'):
        op.create_table('template',
        sa.Column('id', sa.Integer(), autoincrement=True, nullable=False),
        sa.Column('name', sa.String(length=80), nullable=False),
        sa.Column('story', sa.String(length=80), nullable=True),
        sa.Column('telework', sa.Boolean(), nullable=True),
        sa.Column('onsite', sa.Boolean(), nullable=True),
        sa.Column('text', sa.String(length=8192), nullable=True),
        sa.Column('ttype', sa.String(length=40), nullable=True),
        sa.Column('init_time', sa.Integer(), nullable=True),
        sa.Column('end_time', sa.Integer(), nullable=True),
        sa.Column('customerid', sa.Integer(), nullable=True),
        sa.Column('usrid', sa.Integer(), nullable=False),
        sa.Column('task_storyid', sa.INTEGER(), autoincrement=False, nullable=True),
        sa.Column('projectid', sa.Integer(), nullable=True),
        sa.ForeignKeyConstraint(['customerid'], ['customer.id'], ),
        sa.ForeignKeyConstraint(['projectid'], ['project.id'], ),
        sa.ForeignKeyConstraint(['usrid'], ['usr.id'], ),
        sa.ForeignKeyConstraint(['task_storyid'], ['task_story.id'], name='includes'),
        sa.PrimaryKeyConstraint('id')
        )
        # ### end Alembic commands ###


def downgrade() -> None:
    # ### commands auto generated by Alembic - please adjust! ###
    op.drop_table('template')
    op.drop_table('task')
    op.drop_table('project_usr')
    op.drop_table('project')
    op.drop_table('user_goals')
    op.drop_table('journey_history')
    op.drop_table('hour_cost_history')
    op.drop_table('extra_hour')
    op.drop_table('customer')
    op.drop_table('common_event')
    op.drop_table('city_history')
    op.drop_table('belongs')
    op.drop_table('area_history')
    op.drop_table('usr')
    op.drop_table('user_group')
    op.drop_table('sector')
    op.drop_table('config')
    op.drop_table('city')
    op.drop_table('area')
    # ### end Alembic commands ###
