--
-- These are the changes you need in database to upgrade PhpReport 2.1 to 2.x
--
--------------------------------------------------------------------------------

--
-- Add new column in task table:
-- * onsite: whether a task was onsite or not
--

ALTER TABLE tasks ADD COLUMN onsite BOOLEAN
    NOT NULL DEFAULT false;
