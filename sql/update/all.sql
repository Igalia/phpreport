--------------------------------------------------------------------------------
--
-- These are all the changes required to upgrade the DB from 2.0 to the latest
-- release, to be included by the installation scripts.
--
--------------------------------------------------------------------------------


--
-- New table for application configuration: config
--
-- The table will only have one row. To ensure that, there is a primary key row
-- which is set to 1 by default and at the same time has a restriction to check
-- the only posible value of that row is 1.
-- See: http://archives.postgresql.org/pgsql-general/2004-08/msg01569.php
--
-- Rows:
-- * id: primary key, one-row restriction.
-- * version: contains the version number to ease automated upgrades
--

create table config (
  id                        INT PRIMARY KEY NOT NULL DEFAULT(1) CHECK (id = 1),
  version                   varchar(20)
) ;

--
-- Insert first row of the config table. From now on, only UPDATE statements
-- should be used on this table.
-- Set version number to 2.1
--

INSERT INTO config(version) VALUES ('2.1');

--
-- Add new columns in config table:
-- * block_tasks_by_time_enabled: enable/disable the block of tasks in the past.
-- * block_tasks_by_time_number_of_days: how far in the past will tasks be blocked.
--

ALTER TABLE config ADD COLUMN block_tasks_by_time_enabled BOOLEAN
    NOT NULL DEFAULT false;
ALTER TABLE config ADD COLUMN block_tasks_by_time_number_of_days INTEGER;

--
-- Add new column in task table:
-- * onsite: whether a task was onsite or not
--

ALTER TABLE task ADD COLUMN onsite BOOLEAN
    NOT NULL DEFAULT false;

--
-- Add new column in extra_hour table:
-- * comment: free text to optinally add a comment on each entry in the table.
--

ALTER TABLE extra_hour ADD COLUMN comment VARCHAR(256);

--
-- Set database version to 2.16
--

UPDATE config SET version='2.16';

--
-- New table for task templates.
--
-- It contains the same rows as the tasks table, with just some changes:
-- * removed init and _end rows.
-- * added name row.
--

create table template (
  id                        serial not null primary key,
  name                      varchar(80) not null,
  story                     varchar(80),
  telework                  boolean,
  onsite                    boolean,
  text                      varchar(8192),
  ttype                     varchar(40),
  usrId                     integer not null,
  projectId                 integer,
  task_storyId              integer
) ;

--
-- Foreign key constraints for task templates table.
--
-- It has the same relations as the tasks table.
--

alter table template add constraint does
  foreign key (usrId)
  references usr (id)  ;
alter table template add constraint makeUp
  foreign key (projectId)
  references project (id)  ;
alter table template add constraint includes
  foreign key (task_storyId)
  references task_story (id)  ;

--
-- Add new manager user group to the user_group table
-- Restricts access to default staff profile
--

INSERT INTO user_group VALUES (3,'manager');
SELECT nextval(pg_get_serial_sequence('user_group', 'id'));
--
-- Add admin user to the manager group
--

INSERT INTO belongs VALUES (3, 2);

--
-- Create manager user and give proper permissions (staff and manager)
--

INSERT INTO usr VALUES (3, md5('manager'), 'manager');
SELECT nextval(pg_get_serial_sequence('usr', 'id'));
INSERT INTO belongs VALUES (1, 3);
INSERT INTO belongs VALUES (3, 3);

--
-- New table for user goals templates.
--

create table user_goals (
  id                        serial not null primary key,
  init_date                 date not null,
  end_date                  date not null,
  usrId                     integer not null,
  extra_hours               double precision not null
);

--
-- Foreign key constraints for goals table.
--

alter table user_goals add constraint does
  foreign key (usrId)
  references usr (id)  ;

--
-- Add customerId to projects table to link projects to clients
--

ALTER TABLE project
ADD COLUMN customerId integer;

--
-- Foreign key constraint for customers
--

ALTER TABLE project
ADD FOREIGN KEY(customerId) REFERENCES customer(id);

--
-- Set database version to 2.17
--

UPDATE config SET version='2.17';

--
-- projectid is not null in task table
--

alter table task alter column projectid  set not null;

--
-- Rename existing task block columns; change block_tasks_by_time_ prefix to
-- block_tasks_by_day_limit_.
--

ALTER TABLE config RENAME COLUMN block_tasks_by_time_enabled TO block_tasks_by_day_limit_enabled;
ALTER TABLE config RENAME COLUMN block_tasks_by_time_number_of_days TO block_tasks_by_day_limit_number_of_days;

--
-- Add new columns in config table:
-- * block_tasks_by_date_enabled: enable/disable the block of tasks by date.
-- * block_tasks_by_date_date: task before which tasks be blocked.
--

ALTER TABLE config ADD COLUMN block_tasks_by_date_enabled BOOLEAN
    NOT NULL DEFAULT false;
ALTER TABLE config ADD COLUMN block_tasks_by_date_date DATE;

--
-- Set database version to 2.18
--

UPDATE config SET version='2.18';


--
-- Add init_time to template table to copy the task init value
--
ALTER TABLE template
ADD COLUMN init_time integer;

--
-- Add end_time to template table to copy the task _end value
--
ALTER TABLE template
ADD COLUMN end_time integer;

--
-- Set database version to 2.21
--

UPDATE config SET version='2.21';


--
-- Add updated_at to task table so we can track when a task (specially leave tasks) was updated.
--
ALTER TABLE task
ADD COLUMN updated_at timestamp;

--
-- Set database version to 2.22
--

UPDATE config SET version='2.22';

--
-- Allow DELETE CASCADE of user assignments to projects.
--
ALTER TABLE project_usr DROP CONSTRAINT project_usr_fk_ProjectId;
ALTER TABLE project_usr ADD CONSTRAINT project_usr_fk_ProjectId
  FOREIGN KEY (projectId)
  REFERENCES project (id)
  ON DELETE CASCADE;

--
-- Set database version to 2.23
--

UPDATE config SET version='2.23';
