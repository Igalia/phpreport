--
-- This is the minimum set of data for the application to work
--


--------------------------------------------------------------


--
-- Data for Name: user_group; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO user_group VALUES (1, 'staff');
INSERT INTO user_group VALUES (2, 'admin');

-- advance two steps the sequence which generates group ids
SELECT nextval(pg_get_serial_sequence('user_group', 'id'));
SELECT nextval(pg_get_serial_sequence('user_group', 'id'));

--
-- Data for Name: usr; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO usr VALUES (1, md5('user'), 'user');
INSERT INTO usr VALUES (2, md5('admin'), 'admin');

-- advance two steps the sequence which generates user ids
SELECT nextval(pg_get_serial_sequence('usr', 'id'));
SELECT nextval(pg_get_serial_sequence('usr', 'id'));

--
-- Data for Name: belongs; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO belongs VALUES (1, 1);
INSERT INTO belongs VALUES (1, 2);
INSERT INTO belongs VALUES (2, 2);

--
-- Data for Name: area; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO area VALUES (1, 'internal');

-- advance one step the sequence which generates area ids
SELECT nextval(pg_get_serial_sequence('area', 'id'));

--
-- Data for Name: project; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO project (description, areaid) VALUES ('Holidays', 1);
