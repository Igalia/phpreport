--
-- Add new column in task table:
-- * onsite: whether a task was onsite or not
--

ALTER TABLE task ADD COLUMN onsite BOOLEAN
    NOT NULL DEFAULT false;
