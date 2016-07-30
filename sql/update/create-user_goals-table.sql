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
-- It has the same relations as the usr table.
--

alter table user_goals add constraint does
  foreign key (usrId)
  references usr (id)  ;