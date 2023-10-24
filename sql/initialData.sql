--
-- This is the minimum set of data for the application to work
--


--------------------------------------------------------------


--
-- Data for Name: user_group; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO user_group VALUES (DEFAULT, 'staff');
INSERT INTO user_group VALUES (DEFAULT, 'admin');
INSERT INTO user_group VALUES (DEFAULT,'manager');
INSERT INTO user_group VALUES (DEFAULT, 'human resources');
INSERT INTO user_group VALUES (DEFAULT, 'project manager');

--
-- Data for Name: usr; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO usr VALUES (DEFAULT, md5('user'), 'user');
INSERT INTO usr VALUES (DEFAULT, md5('admin'), 'admin');
INSERT INTO usr VALUES (DEFAULT, md5('manager'), 'manager');
INSERT INTO usr VALUES (DEFAULT, md5('phpreport'), 'phpreport-test');



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

INSERT INTO area VALUES (DEFAULT, 'internal');


INSERT INTO sector VALUES (DEFAULT, 'tech');

INSERT INTO customer (name, type, sectorid) VALUES ('Internal', 'Small', 1);
INSERT INTO project (description, areaid, customerid, activation) VALUES ('Holidays', 1, 1, true);
