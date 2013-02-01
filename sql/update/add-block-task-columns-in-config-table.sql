--
-- Add new columns in config table:
-- * block_tasks_by_time_enabled: enable/disable the block of tasks in the past.
-- * block_tasks_by_time_number_of_days: how far in the past will tasks be blocked.
--

ALTER TABLE config ADD COLUMN block_tasks_by_time_enabled BOOLEAN
    NOT NULL DEFAULT false;
ALTER TABLE config ADD COLUMN block_tasks_by_time_number_of_days INTEGER;
