CREATE LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION control_task_overlapping() RETURNS trigger AS '
DECLARE
  overlap boolean;
BEGIN
  SELECT INTO overlap exists(SELECT * FROM task WHERE _date=new._date AND usrid=new.usrid AND NOT ((init<new.init AND _end<=new.init) OR (_end>new._end AND init>=new._end)) AND (id != new.id) );
  IF overlap THEN RETURN NULL;
  END IF;
  RETURN new;
END
' LANGUAGE plpgsql;

CREATE TRIGGER control_task_overlapping BEFORE INSERT OR UPDATE ON task
FOR EACH ROW EXECUTE PROCEDURE control_task_overlapping();
