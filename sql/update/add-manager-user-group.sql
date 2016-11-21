--
-- Add new manager user group to the user_group table
-- Restricts access to default staff profile
--
INSERT INTO user_group(name) VALUES ('manager');

--
-- Add any admin user to the newly created manager group
--
INSERT INTO belongs(usrid, user_groupid)
    SELECT usrid,
        (SELECT id FROM user_group WHERE name='manager') AS user_groupid
        FROM belongs
        WHERE user_groupid=(SELECT id FROM user_group WHERE name='admin');
