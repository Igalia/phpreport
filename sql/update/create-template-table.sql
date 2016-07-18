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
  customerId                integer,
  task_storyId              integer
) ;
