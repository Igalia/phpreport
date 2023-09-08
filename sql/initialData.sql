--
-- This is the minimum set of data for the application to work
--


--------------------------------------------------------------


--
-- Data for Name: user_group; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO user_group VALUES (1, 'staff');
INSERT INTO user_group VALUES (2, 'admin');
INSERT INTO user_group VALUES (3,'manager');

-- advance two steps the sequence which generates group ids
SELECT nextval(pg_get_serial_sequence('user_group', 'id'));
SELECT nextval(pg_get_serial_sequence('user_group', 'id'));
SELECT nextval(pg_get_serial_sequence('user_group', 'id'));
--
-- Data for Name: usr; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO usr VALUES (1, md5('user'), 'user');
INSERT INTO usr VALUES (2, md5('admin'), 'admin');
INSERT INTO usr VALUES (3, md5('manager'), 'manager');
INSERT INTO usr VALUES (4, md5('phpreport'), 'phpreport-test');


-- advance two steps the sequence which generates user ids
SELECT nextval(pg_get_serial_sequence('usr', 'id'));
SELECT nextval(pg_get_serial_sequence('usr', 'id'));
SELECT nextval(pg_get_serial_sequence('usr', 'id'));
SELECT nextval(pg_get_serial_sequence('usr', 'id'));

--
-- Data for Name: belongs; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO belongs VALUES (1, 1);
INSERT INTO belongs VALUES (1, 2);
INSERT INTO belongs VALUES (2, 2);
INSERT INTO belongs VALUES (1, 3);
INSERT INTO belongs VALUES (3, 3);
INSERT INTO belongs VALUES (1, 4);
INSERT INTO belongs VALUES (2, 4);
--
-- Data for Name: area; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO area VALUES (1, 'internal');

-- advance one step the sequence which generates area ids
SELECT nextval(pg_get_serial_sequence('area', 'id'));

INSERT INTO sector VALUES (1, 'tech');
SELECT nextval(pg_get_serial_sequence('sector', 'id'));

INSERT INTO customer (name, type, sectorid) VALUES ('Internal', 'Small', 1);
INSERT INTO project (description, areaid, customerid) VALUES ('Holidays', 1, 1);
