--
-- PostgreSQL database dump
--

-- Dumped from database version 13.9 (Debian 13.9-1.pgdg110+1)
-- Dumped by pg_dump version 13.9 (Debian 13.9-1.pgdg110+1)
SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;
SET default_tablespace = '';
SET default_table_access_method = heap;
--
-- Name: alembic_version; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.alembic_version (version_num character varying(32) NOT NULL);
ALTER TABLE public.alembic_version OWNER TO phpreport;
--
-- Name: area; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.area (
    id integer NOT NULL,
    name character varying(256) NOT NULL
);
ALTER TABLE public.area OWNER TO phpreport;
--
-- Name: area_history; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.area_history (
    id integer NOT NULL,
    init_date date NOT NULL,
    end_date date,
    areaid integer NOT NULL,
    usrid integer NOT NULL,
    CONSTRAINT end_after_init_area_history CHECK (
        (
            (end_date IS NULL)
            OR (end_date >= init_date)
        )
    )
);
ALTER TABLE public.area_history OWNER TO phpreport;
--
-- Name: area_history_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.area_history_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.area_history_id_seq OWNER TO phpreport;
--
-- Name: area_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.area_history_id_seq OWNED BY public.area_history.id;
--
-- Name: area_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.area_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.area_id_seq OWNER TO phpreport;
--
-- Name: area_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.area_id_seq OWNED BY public.area.id;
--
-- Name: belongs; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.belongs (
    user_groupid integer NOT NULL,
    usrid integer NOT NULL
);
ALTER TABLE public.belongs OWNER TO phpreport;
--
-- Name: city; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.city (
    id integer NOT NULL,
    name character varying(30) NOT NULL
);
ALTER TABLE public.city OWNER TO phpreport;
--
-- Name: city_history; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.city_history (
    id integer NOT NULL,
    cityid integer NOT NULL,
    usrid integer NOT NULL,
    init_date date NOT NULL,
    end_date date,
    CONSTRAINT end_after_init_city_history CHECK (
        (
            (end_date IS NULL)
            OR (end_date >= init_date)
        )
    )
);
ALTER TABLE public.city_history OWNER TO phpreport;
--
-- Name: city_history_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.city_history_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.city_history_id_seq OWNER TO phpreport;
--
-- Name: city_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.city_history_id_seq OWNED BY public.city_history.id;
--
-- Name: city_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.city_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.city_id_seq OWNER TO phpreport;
--
-- Name: city_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.city_id_seq OWNED BY public.city.id;
--
-- Name: common_event; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.common_event (
    id integer NOT NULL,
    _date date NOT NULL,
    cityid integer NOT NULL
);
ALTER TABLE public.common_event OWNER TO phpreport;
--
-- Name: common_event_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.common_event_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.common_event_id_seq OWNER TO phpreport;
--
-- Name: common_event_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.common_event_id_seq OWNED BY public.common_event.id;
--
-- Name: config; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.config (
    id integer NOT NULL,
    version character varying(20),
    block_tasks_by_time_enabled boolean NOT NULL,
    block_tasks_by_time_number_of_days integer,
    block_tasks_by_day_limit_enabled boolean NOT NULL,
    block_tasks_by_day_limit_number_of_days integer,
    block_tasks_by_date_enabled boolean NOT NULL,
    block_tasks_by_date_date date
);
ALTER TABLE public.config OWNER TO phpreport;
--
-- Name: config_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.config_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.config_id_seq OWNER TO phpreport;
--
-- Name: config_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.config_id_seq OWNED BY public.config.id;
--
-- Name: customer; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.customer (
    id integer NOT NULL,
    name character varying(256) NOT NULL,
    type character varying(256) NOT NULL,
    url character varying(8192),
    sectorid integer NOT NULL
);
ALTER TABLE public.customer OWNER TO phpreport;
--
-- Name: customer_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.customer_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_id_seq OWNER TO phpreport;
--
-- Name: customer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.customer_id_seq OWNED BY public.customer.id;
--
-- Name: extra_hour; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.extra_hour (
    id integer NOT NULL,
    _date date NOT NULL,
    hours double precision NOT NULL,
    usrid integer NOT NULL,
    comment character varying(256)
);
ALTER TABLE public.extra_hour OWNER TO phpreport;
--
-- Name: extra_hour_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.extra_hour_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.extra_hour_id_seq OWNER TO phpreport;
--
-- Name: extra_hour_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.extra_hour_id_seq OWNED BY public.extra_hour.id;
--
-- Name: hour_cost_history; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.hour_cost_history (
    id integer NOT NULL,
    hour_cost numeric(8, 4) NOT NULL,
    init_date date NOT NULL,
    end_date date,
    usrid integer NOT NULL,
    CONSTRAINT end_after_init_hour_cost_history CHECK (
        (
            (end_date IS NULL)
            OR (end_date >= init_date)
        )
    )
);
ALTER TABLE public.hour_cost_history OWNER TO phpreport;
--
-- Name: hour_cost_history_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.hour_cost_history_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.hour_cost_history_id_seq OWNER TO phpreport;
--
-- Name: hour_cost_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.hour_cost_history_id_seq OWNED BY public.hour_cost_history.id;
--
-- Name: journey_history; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.journey_history (
    id integer NOT NULL,
    journey numeric(8, 4) NOT NULL,
    init_date date NOT NULL,
    end_date date,
    usrid integer NOT NULL,
    CONSTRAINT end_after_init_journey_history CHECK (
        (
            (end_date IS NULL)
            OR (end_date >= init_date)
        )
    )
);
ALTER TABLE public.journey_history OWNER TO phpreport;
--
-- Name: journey_history_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.journey_history_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.journey_history_id_seq OWNER TO phpreport;
--
-- Name: journey_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.journey_history_id_seq OWNED BY public.journey_history.id;
--
-- Name: project; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.project (
    id integer NOT NULL,
    activation boolean NOT NULL,
    init date,
    _end date,
    invoice double precision,
    est_hours double precision,
    moved_hours double precision,
    description character varying(256),
    type character varying(256),
    sched_type character varying(256),
    customerid integer,
    areaid integer NOT NULL
);
ALTER TABLE public.project OWNER TO phpreport;
--
-- Name: project_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.project_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.project_id_seq OWNER TO phpreport;
--
-- Name: project_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.project_id_seq OWNED BY public.project.id;
--
-- Name: project_usr; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.project_usr (
    usrid integer NOT NULL,
    projectid integer NOT NULL
);
ALTER TABLE public.project_usr OWNER TO phpreport;
--
-- Name: sector; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.sector (
    id integer NOT NULL,
    name character varying(256) NOT NULL
);
ALTER TABLE public.sector OWNER TO phpreport;
--
-- Name: sector_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.sector_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.sector_id_seq OWNER TO phpreport;
--
-- Name: sector_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.sector_id_seq OWNED BY public.sector.id;
--
-- Name: task; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.task (
    id integer NOT NULL,
    _date date NOT NULL,
    init integer NOT NULL,
    _end integer NOT NULL,
    story character varying(80),
    telework boolean,
    text character varying(8192),
    ttype character varying,
    phase character varying(15),
    onsite boolean NOT NULL,
    updated_at timestamp without time zone,
    usrid integer NOT NULL,
    projectid integer NOT NULL
);
ALTER TABLE public.task OWNER TO phpreport;
--
-- Name: task_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.task_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.task_id_seq OWNER TO phpreport;
--
-- Name: task_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.task_id_seq OWNED BY public.task.id;
--
-- Name: task_type; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.task_type (
    id integer NOT NULL,
    active boolean,
    name character varying,
    slug character varying NOT NULL
);
ALTER TABLE public.task_type OWNER TO phpreport;
--
-- Name: task_type_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.task_type_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.task_type_id_seq OWNER TO phpreport;
--
-- Name: task_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.task_type_id_seq OWNED BY public.task_type.id;
--
-- Name: template; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.template (
    id integer NOT NULL,
    name character varying(80) NOT NULL,
    story character varying(80),
    telework boolean,
    onsite boolean,
    text character varying(8192),
    ttype character varying(40),
    init_time integer,
    end_time integer,
    customerid integer,
    usrid integer,
    projectid integer,
    is_global boolean NOT NULL
);
ALTER TABLE public.template OWNER TO phpreport;
--
-- Name: template_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.template_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.template_id_seq OWNER TO phpreport;
--
-- Name: template_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.template_id_seq OWNED BY public.template.id;
--
-- Name: user_goals; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.user_goals (
    id integer NOT NULL,
    init_date date NOT NULL,
    end_date date NOT NULL,
    extra_hours double precision NOT NULL,
    usrid integer NOT NULL
);
ALTER TABLE public.user_goals OWNER TO phpreport;
--
-- Name: user_goals_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.user_goals_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.user_goals_id_seq OWNER TO phpreport;
--
-- Name: user_goals_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.user_goals_id_seq OWNED BY public.user_goals.id;
--
-- Name: user_group; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.user_group (
    id integer NOT NULL,
    name character varying(128)
);
ALTER TABLE public.user_group OWNER TO phpreport;
--
-- Name: user_group_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.user_group_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.user_group_id_seq OWNER TO phpreport;
--
-- Name: user_group_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.user_group_id_seq OWNED BY public.user_group.id;
--
-- Name: usr; Type: TABLE; Schema: public; Owner: phpreport
--

CREATE TABLE public.usr (
    id integer NOT NULL,
    password character varying(256),
    login character varying(100) NOT NULL
);
ALTER TABLE public.usr OWNER TO phpreport;
--
-- Name: usr_id_seq; Type: SEQUENCE; Schema: public; Owner: phpreport
--

CREATE SEQUENCE public.usr_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.usr_id_seq OWNER TO phpreport;
--
-- Name: usr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: phpreport
--

ALTER SEQUENCE public.usr_id_seq OWNED BY public.usr.id;
--
-- Name: area id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.area
ALTER COLUMN id
SET DEFAULT nextval('public.area_id_seq'::regclass);
--
-- Name: area_history id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.area_history
ALTER COLUMN id
SET DEFAULT nextval('public.area_history_id_seq'::regclass);
--
-- Name: city id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.city
ALTER COLUMN id
SET DEFAULT nextval('public.city_id_seq'::regclass);
--
-- Name: city_history id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.city_history
ALTER COLUMN id
SET DEFAULT nextval('public.city_history_id_seq'::regclass);
--
-- Name: common_event id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.common_event
ALTER COLUMN id
SET DEFAULT nextval('public.common_event_id_seq'::regclass);
--
-- Name: config id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.config
ALTER COLUMN id
SET DEFAULT nextval('public.config_id_seq'::regclass);
--
-- Name: customer id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.customer
ALTER COLUMN id
SET DEFAULT nextval('public.customer_id_seq'::regclass);
--
-- Name: extra_hour id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.extra_hour
ALTER COLUMN id
SET DEFAULT nextval('public.extra_hour_id_seq'::regclass);
--
-- Name: hour_cost_history id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.hour_cost_history
ALTER COLUMN id
SET DEFAULT nextval('public.hour_cost_history_id_seq'::regclass);
--
-- Name: journey_history id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.journey_history
ALTER COLUMN id
SET DEFAULT nextval('public.journey_history_id_seq'::regclass);
--
-- Name: project id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.project
ALTER COLUMN id
SET DEFAULT nextval('public.project_id_seq'::regclass);
--
-- Name: sector id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.sector
ALTER COLUMN id
SET DEFAULT nextval('public.sector_id_seq'::regclass);
--
-- Name: task id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.task
ALTER COLUMN id
SET DEFAULT nextval('public.task_id_seq'::regclass);
--
-- Name: task_type id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.task_type
ALTER COLUMN id
SET DEFAULT nextval('public.task_type_id_seq'::regclass);
--
-- Name: template id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.template
ALTER COLUMN id
SET DEFAULT nextval('public.template_id_seq'::regclass);
--
-- Name: user_goals id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.user_goals
ALTER COLUMN id
SET DEFAULT nextval('public.user_goals_id_seq'::regclass);
--
-- Name: user_group id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.user_group
ALTER COLUMN id
SET DEFAULT nextval('public.user_group_id_seq'::regclass);
--
-- Name: usr id; Type: DEFAULT; Schema: public; Owner: phpreport
--

ALTER TABLE ONLY public.usr
ALTER COLUMN id
SET DEFAULT nextval('public.usr_id_seq'::regclass);
