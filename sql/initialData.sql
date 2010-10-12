--
-- This is the minimum set of data for the application to work
--


--------------------------------------------------------------


--
-- Data for Name: user_group; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO user_group VALUES (1, 'staff');
INSERT INTO user_group VALUES (2, 'admin');


--
-- Data for Name: usr; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO usr VALUES (1, md5('user'), 'user');
INSERT INTO usr VALUES (2, md5('admin'), 'admin');


--
-- Data for Name: belongs; Type: TABLE DATA; Schema: public; Owner: phpreport
--

INSERT INTO belongs VALUES (1, 1);
INSERT INTO belongs VALUES (1, 2);
INSERT INTO belongs VALUES (2, 2);
