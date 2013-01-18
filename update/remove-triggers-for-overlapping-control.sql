--
-- Remove function and trigger that activated every time a row in task table
-- was being modified. It was used to check there weren't overlapping tasks,
-- but it produced false positives. Now that check is done in the PHP code
-- before running the create or update operations on DB, so we don't need them.
--

DROP TRIGGER control_task_overlapping ON task;
DROP FUNCTION control_task_overlapping();
