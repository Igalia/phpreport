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