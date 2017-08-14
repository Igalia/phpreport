--
-- Added the 'empty' project if there is tasks without project assigned
--
insert into project (description, areaid) 
    select 'empty', 1 where exists (
        select * from task where projectid is null);

--
-- Set already created task without project assigned to the 'empty' project
--
update task set projectid=(
        select id from project where description = 'empty'
    ) where projectid is null;

--
-- projectid is not null
--
--
alter table task alter column projectid  set not null;
