--
-- Allow DELETE CASCADE of user assignments to projects.
--
ALTER TABLE project_usr DROP CONSTRAINT project_usr_fk_ProjectId;
ALTER TABLE project_usr ADD CONSTRAINT project_usr_fk_ProjectId
  FOREIGN KEY (projectId)
  REFERENCES project (id)
  ON DELETE CASCADE;
