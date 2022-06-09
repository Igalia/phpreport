--
-- Add updated_at to task table so we can track when a task (specially leave tasks) was updated.
--
ALTER TABLE task
ADD COLUMN updated_at timestamp;
