--
-- Add new column in extra_hour table:
-- * comment: free text to optinally add a comment on each entry in the table.
--

ALTER TABLE extra_hour ADD COLUMN comment VARCHAR(256);
