--
-- Add customerId to projects table to link projects to clients
--
ALTER TABLE project
ADD COLUMN customerId integer;

--
-- Foreign key constraint for customers
--
ALTER TABLE project
ADD FOREIGN KEY(customerId) REFERENCES customer(id);