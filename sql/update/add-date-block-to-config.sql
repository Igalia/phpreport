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
