"""Drop deprecated models and fields

Revision ID: ff0037261dd9
Revises: 217cb64b0391
Create Date: 2023-06-22 14:36:11.425774

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy import text

# revision identifiers, used by Alembic.
revision = 'ff0037261dd9'
down_revision = '217cb64b0391'
branch_labels = None
depends_on = None


def table_exists(table_name):
    connection = op.get_bind()
    query = text("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = :table)")
    result = connection.execute(query.params(table=table_name))
    return result.scalar()


def upgrade() -> None:
    # All the tables below were deprecated but were left in the production database,
    # so let's clean them up.
    op.drop_constraint('includes', 'task', type_='foreignkey')
    op.drop_column('task', 'task_storyid')
    op.drop_constraint('includes', 'template', type_='foreignkey')
    op.drop_column('template', 'task_storyid')

    if table_exists('task_story'):
        op.drop_table('task_story')
    if table_exists('custom_event'):
        op.drop_table('custom_event')
    if table_exists('relation_tasks_external_phpreport'):
        op.drop_table('relation_tasks_external_phpreport')
    if table_exists('project_schedule'):
        op.drop_table('project_schedule')
    if table_exists('works'):
        op.drop_table('works')
    if table_exists('task_section'):
        op.drop_table('task_section')
    if table_exists('story'):
        op.drop_table('story')
    if table_exists('iteration'):
        op.drop_table('iteration')
    if table_exists('section'):
        op.drop_table('section')
    if table_exists('requests'):
        op.drop_table('requests')
    if table_exists('module'):
        op.drop_table('module')


def downgrade() -> None:
    op.add_column('template', sa.Column('task_storyid', sa.INTEGER(), autoincrement=False, nullable=True))
    op.create_foreign_key('includes', 'template', 'task_story', ['task_storyid'], ['id'])
    op.add_column('task', sa.Column('task_storyid', sa.INTEGER(), autoincrement=False, nullable=True))
    op.create_foreign_key('includes', 'task', 'task_story', ['task_storyid'], ['id'])
    op.create_table('requests',
    sa.Column('customerid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('projectid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.ForeignKeyConstraint(['customerid'], ['customer.id'], name='requests_fk_customerid'),
    sa.ForeignKeyConstraint(['projectid'], ['project.id'], name='requests_fk_projectid'),
    sa.PrimaryKeyConstraint('customerid', 'projectid', name='pk_requests')
    )
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
    op.create_table('task_story',
    sa.Column('id', sa.INTEGER(), autoincrement=True, nullable=False),
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
    sa.UniqueConstraint('storyid', 'name', name='unique_task_story_story_name')
    )
    op.create_table('story',
    sa.Column('id', sa.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
    sa.Column('accepted', sa.BOOLEAN(), autoincrement=False, nullable=True),
    sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('iterationid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('storyid', sa.INTEGER(), autoincrement=False, nullable=True),
    sa.ForeignKeyConstraint(['iterationid'], ['iteration.id'], name='contains'),
    sa.ForeignKeyConstraint(['storyid'], ['story.id'], name='next'),
    sa.ForeignKeyConstraint(['usrid'], ['usr.id'], name='leads_st'),
    sa.PrimaryKeyConstraint('id', name='pk_story'),
    sa.UniqueConstraint('iterationid', 'name', name='unique_story_iteration_name')
    )
    op.create_table('task_section',
    sa.Column('id', sa.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
    sa.Column('est_hours', sa.DOUBLE_PRECISION(precision=53), autoincrement=False, nullable=False),
    sa.Column('sectionid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('risk', sa.INTEGER(), autoincrement=False, nullable=True),
    sa.CheckConstraint('est_hours >= 0::double precision', name='positive_est_hours_task_story'),
    sa.ForeignKeyConstraint(['sectionid'], ['section.id'], name='shapes'),
    sa.ForeignKeyConstraint(['usrid'], ['usr.id'], name='assigned_to'),
    sa.PrimaryKeyConstraint('id', name='pk_task_section'),
    sa.UniqueConstraint('sectionid', 'name', name='unique_task_section_section_name')
    )
    op.create_table('project_schedule',
    sa.Column('id', sa.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('weekly_load', sa.DOUBLE_PRECISION(precision=53), autoincrement=False, nullable=False),
    sa.Column('init_week', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('init_year', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('end_week', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('end_year', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('projectid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.CheckConstraint('init_year < end_year OR init_year = end_year AND init_week <= end_week', name='end_after_init_project_schedule'),
    sa.ForeignKeyConstraint(['usrid', 'projectid'], ['works.usrid', 'works.projectid'], name='project_schedule_fk_usrid'),
    sa.PrimaryKeyConstraint('id', name='pk_project_schedule'),
    sa.UniqueConstraint('usrid', 'projectid', 'init_year', 'init_week', name='unique_project_schedule_user_project_date')
    )
    op.create_table('module',
    sa.Column('id', sa.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
    sa.Column('summary', sa.VARCHAR(length=256), autoincrement=False, nullable=True),
    sa.Column('init', sa.DATE(), autoincrement=False, nullable=False),
    sa.Column('_end', sa.DATE(), autoincrement=False, nullable=True),
    sa.Column('projectid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.CheckConstraint('_end >= init', name='end_after_init_iteration'),
    sa.ForeignKeyConstraint(['projectid'], ['project.id'], name='analyzes'),
    sa.PrimaryKeyConstraint('id', name='pk_module'),
    sa.UniqueConstraint('projectid', 'name', name='unique_module_project_name')
    )
    op.create_table('works',
    sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('projectid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.ForeignKeyConstraint(['projectid'], ['project.id'], name='works_fk_projectid'),
    sa.ForeignKeyConstraint(['usrid'], ['usr.id'], name='works_fk_usrid'),
    sa.PrimaryKeyConstraint('usrid', 'projectid', name='pk_works')
    )
    op.create_table('relation_tasks_external_phpreport',
    sa.Column('internalid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('externalid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.PrimaryKeyConstraint('internalid', 'externalid', name='pk_relation_tasks_external_phpreport')
    )
    op.create_table('iteration',
    sa.Column('id', sa.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('name', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
    sa.Column('summary', sa.VARCHAR(length=256), autoincrement=False, nullable=True),
    sa.Column('init', sa.DATE(), autoincrement=False, nullable=False),
    sa.Column('_end', sa.DATE(), autoincrement=False, nullable=True),
    sa.Column('projectid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.CheckConstraint('_end >= init', name='end_after_init_iteration'),
    sa.ForeignKeyConstraint(['projectid'], ['project.id'], name='plans'),
    sa.PrimaryKeyConstraint('id', name='pk_iteration'),
    sa.UniqueConstraint('projectid', 'name', name='unique_iteration_project_name')
    )
    op.create_table('custom_event',
    sa.Column('id', sa.INTEGER(), autoincrement=True, nullable=False),
    sa.Column('_date', sa.DATE(), autoincrement=False, nullable=False),
    sa.Column('hours', sa.DOUBLE_PRECISION(precision=53), autoincrement=False, nullable=False),
    sa.Column('usrid', sa.INTEGER(), autoincrement=False, nullable=False),
    sa.Column('type', sa.VARCHAR(length=256), autoincrement=False, nullable=False),
    sa.CheckConstraint('hours >= 0::double precision', name='positive_hours_custom'),
    sa.ForeignKeyConstraint(['usrid'], ['usr.id'], name='appointment'),
    sa.PrimaryKeyConstraint('id', name='pk_custom_event'),
    sa.UniqueConstraint('usrid', '_date', name='unique_custom_event_user_date')
    )
