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
alter table template add constraint finances
  foreign key (customerId)
  references customer (id)  ;
alter table template add constraint includes
  foreign key (task_storyId)
  references task_story (id)  ;
