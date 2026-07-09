--
-- PostgreSQL database dump
--

\restrict ZsBhTYPDoZ3U3lP9pnIpjv6hgWNl8N4ehi5vwknqYfcKizWKWLbBnq29JvU7KRf

-- Dumped from database version 16.13
-- Dumped by pg_dump version 16.13

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
-- Name: activity_logs; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.activity_logs (
    id bigint NOT NULL,
    user_id bigint,
    user_name character varying(255) DEFAULT NULL::character varying,
    action character varying(100) NOT NULL,
    entity_type character varying(50) DEFAULT NULL::character varying,
    entity_id bigint,
    table_name character varying(50) DEFAULT NULL::character varying,
    record_id bigint,
    description text,
    changes text,
    ip_address character varying(45) DEFAULT NULL::character varying,
    user_agent text,
    metadata text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.activity_logs OWNER TO laravel;

--
-- Name: activity_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.activity_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.activity_logs_id_seq OWNER TO laravel;

--
-- Name: activity_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.activity_logs_id_seq OWNED BY public.activity_logs.id;


--
-- Name: assignment_submissions; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.assignment_submissions (
    id bigint NOT NULL,
    assignment_id bigint NOT NULL,
    student_name character varying(255) NOT NULL,
    student_class character varying(255) NOT NULL,
    file_path character varying(255) NOT NULL,
    file_name character varying(255) NOT NULL,
    file_size character varying(255) DEFAULT NULL::character varying,
    file_ext character varying(255) DEFAULT NULL::character varying,
    status character varying(50) DEFAULT 'submitted'::character varying NOT NULL,
    grade numeric(5,2) DEFAULT NULL::numeric,
    feedback text,
    submitted_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.assignment_submissions OWNER TO laravel;

--
-- Name: assignment_submissions_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.assignment_submissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.assignment_submissions_id_seq OWNER TO laravel;

--
-- Name: assignment_submissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.assignment_submissions_id_seq OWNED BY public.assignment_submissions.id;


--
-- Name: assignments; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.assignments (
    id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    organization_id bigint,
    title character varying(255) NOT NULL,
    description text,
    subject_name character varying(255) NOT NULL,
    class_name character varying(255) NOT NULL,
    deadline timestamp without time zone NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    attachment_path character varying(255) DEFAULT NULL::character varying,
    attachment_name character varying(255) DEFAULT NULL::character varying,
    attachment_size character varying(255) DEFAULT NULL::character varying,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.assignments OWNER TO laravel;

--
-- Name: assignments_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.assignments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.assignments_id_seq OWNER TO laravel;

--
-- Name: assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.assignments_id_seq OWNED BY public.assignments.id;


--
-- Name: bookings; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.bookings (
    id bigint NOT NULL,
    session_id character(36) DEFAULT NULL::bpchar,
    teacher_id bigint,
    resource_id bigint NOT NULL,
    time_slot_id bigint NOT NULL,
    user_id bigint,
    organization_id bigint NOT NULL,
    booking_date date NOT NULL,
    teacher_name character varying(255) NOT NULL,
    teacher_phone character varying(50) NOT NULL,
    class_name character varying(255) DEFAULT NULL::character varying,
    subject_name character varying(255) DEFAULT NULL::character varying,
    title character varying(500) NOT NULL,
    description text,
    participant_count integer DEFAULT 0,
    status character varying(50) DEFAULT 'pending'::character varying,
    approved_by bigint,
    approved_at timestamp without time zone,
    notes text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.bookings OWNER TO laravel;

--
-- Name: bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.bookings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bookings_id_seq OWNER TO laravel;

--
-- Name: bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.bookings_id_seq OWNED BY public.bookings.id;


--
-- Name: classes; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.classes (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    pin character(6) DEFAULT NULL::bpchar,
    organization_id bigint NOT NULL,
    grade_level character varying(50) DEFAULT NULL::character varying,
    major character varying(100) DEFAULT NULL::character varying,
    student_count integer DEFAULT 0,
    academic_year character varying(20) DEFAULT NULL::character varying,
    semester character varying(10) DEFAULT NULL::character varying,
    metadata text,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp without time zone
);


ALTER TABLE public.classes OWNER TO laravel;

--
-- Name: classes_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.classes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.classes_id_seq OWNER TO laravel;

--
-- Name: classes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.classes_id_seq OWNED BY public.classes.id;


--
-- Name: holidays; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.holidays (
    id bigint NOT NULL,
    date date NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(50) DEFAULT NULL::character varying,
    description text,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.holidays OWNER TO laravel;

--
-- Name: holidays_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.holidays_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.holidays_id_seq OWNER TO laravel;

--
-- Name: holidays_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.holidays_id_seq OWNED BY public.holidays.id;


--
-- Name: important_schedules; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.important_schedules (
    id bigint NOT NULL,
    resource_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    type character varying(255) DEFAULT 'event'::character varying NOT NULL,
    date date NOT NULL,
    is_full_day boolean DEFAULT false NOT NULL,
    start_slot_id bigint,
    end_slot_id bigint,
    description text,
    color character varying(7) DEFAULT '#EF4444'::character varying NOT NULL,
    created_by bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    start_date date,
    end_date date,
    CONSTRAINT important_schedules_type_check CHECK (((type)::text = ANY ((ARRAY['exam'::character varying, 'olympiad'::character varying, 'event'::character varying, 'other'::character varying])::text[])))
);


ALTER TABLE public.important_schedules OWNER TO laravel;

--
-- Name: important_schedules_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.important_schedules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.important_schedules_id_seq OWNER TO laravel;

--
-- Name: important_schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.important_schedules_id_seq OWNED BY public.important_schedules.id;


--
-- Name: inventory_maintenance_logs; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.inventory_maintenance_logs (
    id bigint NOT NULL,
    lab_inventory_id bigint NOT NULL,
    user_id bigint NOT NULL,
    maintenance_date date NOT NULL,
    maintenance_type character varying(255) NOT NULL,
    description text NOT NULL,
    cost numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.inventory_maintenance_logs OWNER TO laravel;

--
-- Name: inventory_maintenance_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.inventory_maintenance_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.inventory_maintenance_logs_id_seq OWNER TO laravel;

--
-- Name: inventory_maintenance_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.inventory_maintenance_logs_id_seq OWNED BY public.inventory_maintenance_logs.id;


--
-- Name: lab_inventory; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.lab_inventory (
    id bigint NOT NULL,
    resource_id bigint NOT NULL,
    item_name character varying(255) NOT NULL,
    category character varying(50) DEFAULT 'other'::character varying NOT NULL,
    brand character varying(100) DEFAULT NULL::character varying,
    model character varying(100) DEFAULT NULL::character varying,
    serial_number character varying(100) DEFAULT NULL::character varying,
    specifications text,
    condition character varying(50) DEFAULT 'good'::character varying NOT NULL,
    status character varying(50) DEFAULT 'active'::character varying NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    quantity_good integer DEFAULT 0 NOT NULL,
    quantity_broken integer DEFAULT 0 NOT NULL,
    quantity_backup integer DEFAULT 0 NOT NULL,
    notes text,
    created_by bigint,
    updated_by bigint,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp without time zone
);


ALTER TABLE public.lab_inventory OWNER TO laravel;

--
-- Name: lab_inventory_history; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.lab_inventory_history (
    id bigint NOT NULL,
    inventory_id bigint NOT NULL,
    action_type character varying(50) NOT NULL,
    old_condition character varying(50) DEFAULT NULL::character varying,
    new_condition character varying(50) DEFAULT NULL::character varying,
    old_status character varying(50) DEFAULT NULL::character varying,
    new_status character varying(50) DEFAULT NULL::character varying,
    old_quantity integer,
    new_quantity integer,
    description text,
    cost numeric(15,2) DEFAULT NULL::numeric,
    performed_by bigint,
    performed_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.lab_inventory_history OWNER TO laravel;

--
-- Name: lab_inventory_history_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.lab_inventory_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lab_inventory_history_id_seq OWNER TO laravel;

--
-- Name: lab_inventory_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.lab_inventory_history_id_seq OWNED BY public.lab_inventory_history.id;


--
-- Name: lab_inventory_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.lab_inventory_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lab_inventory_id_seq OWNER TO laravel;

--
-- Name: lab_inventory_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.lab_inventory_id_seq OWNED BY public.lab_inventory.id;


--
-- Name: lab_sessions; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.lab_sessions (
    id bigint NOT NULL,
    token character varying(12) NOT NULL,
    lab_key character varying(20) NOT NULL,
    resource_id bigint NOT NULL,
    source_type character varying(50) NOT NULL,
    source_id bigint,
    teacher_name character varying(255) NOT NULL,
    teacher_phone character varying(255) DEFAULT NULL::character varying,
    session_start timestamp without time zone NOT NULL,
    session_end timestamp without time zone NOT NULL,
    used_at timestamp without time zone,
    invalidated_at timestamp without time zone,
    is_active boolean DEFAULT true NOT NULL,
    invalidated_reason character varying(255) DEFAULT NULL::character varying,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.lab_sessions OWNER TO laravel;

--
-- Name: lab_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.lab_sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lab_sessions_id_seq OWNER TO laravel;

--
-- Name: lab_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.lab_sessions_id_seq OWNED BY public.lab_sessions.id;


--
-- Name: maintenance_records; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.maintenance_records (
    id bigint NOT NULL,
    resource_id bigint NOT NULL,
    type character varying(50) DEFAULT NULL::character varying,
    title character varying(500) NOT NULL,
    description text,
    scheduled_date date NOT NULL,
    completed_date date,
    technician character varying(255) DEFAULT NULL::character varying,
    technician_phone character varying(50) DEFAULT NULL::character varying,
    cost numeric(15,2) DEFAULT 0.00,
    status character varying(50) DEFAULT 'scheduled'::character varying,
    notes text,
    created_by bigint,
    metadata text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.maintenance_records OWNER TO laravel;

--
-- Name: maintenance_records_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.maintenance_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_records_id_seq OWNER TO laravel;

--
-- Name: maintenance_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.maintenance_records_id_seq OWNED BY public.maintenance_records.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO laravel;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO laravel;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO laravel;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO laravel;

--
-- Name: notifications; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.notifications (
    id bigint NOT NULL,
    user_id bigint,
    type character varying(50) NOT NULL,
    title character varying(500) NOT NULL,
    message text,
    action_url character varying(500) DEFAULT NULL::character varying,
    reference_type character varying(50) DEFAULT NULL::character varying,
    reference_id bigint,
    priority character varying(50) DEFAULT 'normal'::character varying,
    is_read boolean DEFAULT false,
    read_at timestamp without time zone,
    metadata text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.notifications OWNER TO laravel;

--
-- Name: notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.notifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.notifications_id_seq OWNER TO laravel;

--
-- Name: notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.notifications_id_seq OWNED BY public.notifications.id;


--
-- Name: organizations; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.organizations (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(50) DEFAULT NULL::character varying,
    parent_id bigint,
    address text,
    phone character varying(50) DEFAULT NULL::character varying,
    email character varying(255) DEFAULT NULL::character varying,
    metadata text,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp without time zone
);


ALTER TABLE public.organizations OWNER TO laravel;

--
-- Name: organizations_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.organizations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.organizations_id_seq OWNER TO laravel;

--
-- Name: organizations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.organizations_id_seq OWNED BY public.organizations.id;


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.permissions OWNER TO laravel;

--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permissions_id_seq OWNER TO laravel;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: procurement_requests; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.procurement_requests (
    id bigint NOT NULL,
    lab_id bigint NOT NULL,
    item_name character varying(255) NOT NULL,
    category character varying(50) NOT NULL,
    quantity integer NOT NULL,
    estimated_price numeric(15,2) NOT NULL,
    priority character varying(50) DEFAULT 'medium'::character varying NOT NULL,
    justification text NOT NULL,
    specifications text,
    preferred_brand character varying(100) DEFAULT NULL::character varying,
    notes text,
    status character varying(50) DEFAULT 'pending'::character varying NOT NULL,
    requested_by bigint NOT NULL,
    requested_at timestamp without time zone NOT NULL,
    reviewed_by bigint,
    reviewed_at timestamp without time zone,
    review_notes text,
    completed_at timestamp without time zone,
    procurement_notes text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.procurement_requests OWNER TO laravel;

--
-- Name: procurement_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.procurement_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.procurement_requests_id_seq OWNER TO laravel;

--
-- Name: procurement_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.procurement_requests_id_seq OWNED BY public.procurement_requests.id;


--
-- Name: resource_user; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.resource_user (
    resource_id bigint NOT NULL,
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.resource_user OWNER TO laravel;

--
-- Name: resources; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.resources (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(50) NOT NULL,
    parent_id bigint,
    organization_id bigint,
    building character varying(100) DEFAULT NULL::character varying,
    floor integer,
    room_number character varying(50) DEFAULT NULL::character varying,
    capacity integer,
    status character varying(50) DEFAULT 'active'::character varying NOT NULL,
    metadata text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp without time zone
);


ALTER TABLE public.resources OWNER TO laravel;

--
-- Name: resources_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.resources_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.resources_id_seq OWNER TO laravel;

--
-- Name: resources_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.resources_id_seq OWNED BY public.resources.id;


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO laravel;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.roles OWNER TO laravel;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO laravel;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: schedules; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.schedules (
    id bigint NOT NULL,
    teacher_id bigint,
    resource_id bigint NOT NULL,
    time_slot_id bigint NOT NULL,
    class_id bigint NOT NULL,
    user_id bigint,
    day_of_week character varying(20) NOT NULL,
    teacher_name character varying(255) NOT NULL,
    subject_name character varying(255) DEFAULT NULL::character varying,
    notes text,
    academic_year character varying(20) DEFAULT NULL::character varying,
    semester character varying(10) DEFAULT NULL::character varying,
    start_date date,
    end_date date,
    status character varying(50) DEFAULT 'active'::character varying,
    metadata text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp without time zone
);


ALTER TABLE public.schedules OWNER TO laravel;

--
-- Name: schedules_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.schedules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.schedules_id_seq OWNER TO laravel;

--
-- Name: schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.schedules_id_seq OWNED BY public.schedules.id;


--
-- Name: sunday_bookings; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.sunday_bookings (
    id bigint NOT NULL,
    teacher_id bigint,
    resource_id bigint NOT NULL,
    organization_id bigint NOT NULL,
    approved_by bigint,
    booking_date date NOT NULL,
    teacher_name character varying(255) NOT NULL,
    teacher_phone character varying(50) NOT NULL,
    class_name character varying(255) DEFAULT NULL::character varying,
    subject_name character varying(255) DEFAULT NULL::character varying,
    title character varying(500) NOT NULL,
    description text,
    participant_count integer DEFAULT 0 NOT NULL,
    status character varying(50) DEFAULT 'pending'::character varying NOT NULL,
    approved_at timestamp without time zone,
    notes text,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.sunday_bookings OWNER TO laravel;

--
-- Name: sunday_bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.sunday_bookings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sunday_bookings_id_seq OWNER TO laravel;

--
-- Name: sunday_bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.sunday_bookings_id_seq OWNED BY public.sunday_bookings.id;


--
-- Name: teachers; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.teachers (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    phone character varying(255) DEFAULT NULL::character varying,
    token character varying(10) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.teachers OWNER TO laravel;

--
-- Name: teachers_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.teachers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.teachers_id_seq OWNER TO laravel;

--
-- Name: teachers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.teachers_id_seq OWNED BY public.teachers.id;


--
-- Name: telescope_entries; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.telescope_entries (
    sequence bigint NOT NULL,
    uuid uuid NOT NULL,
    batch_id uuid NOT NULL,
    family_hash character varying(255),
    should_display_on_index boolean DEFAULT true NOT NULL,
    type character varying(20) NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.telescope_entries OWNER TO laravel;

--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.telescope_entries_sequence_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telescope_entries_sequence_seq OWNER TO laravel;

--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.telescope_entries_sequence_seq OWNED BY public.telescope_entries.sequence;


--
-- Name: telescope_entries_tags; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.telescope_entries_tags (
    entry_uuid uuid NOT NULL,
    tag character varying(255) NOT NULL
);


ALTER TABLE public.telescope_entries_tags OWNER TO laravel;

--
-- Name: telescope_monitoring; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.telescope_monitoring (
    tag character varying(255) NOT NULL
);


ALTER TABLE public.telescope_monitoring OWNER TO laravel;

--
-- Name: time_slots; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.time_slots (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    start_time time without time zone NOT NULL,
    end_time time without time zone NOT NULL,
    day_of_week smallint,
    slot_order integer,
    is_break boolean DEFAULT false,
    metadata text,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.time_slots OWNER TO laravel;

--
-- Name: time_slots_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.time_slots_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.time_slots_id_seq OWNER TO laravel;

--
-- Name: time_slots_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.time_slots_id_seq OWNED BY public.time_slots.id;


--
-- Name: user_sessions; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.user_sessions (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    token character varying(255) NOT NULL,
    ip_address character varying(45) DEFAULT NULL::character varying,
    user_agent text,
    metadata text,
    expires_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.user_sessions OWNER TO laravel;

--
-- Name: user_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.user_sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_sessions_id_seq OWNER TO laravel;

--
-- Name: user_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.user_sessions_id_seq OWNED BY public.user_sessions.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: laravel
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    username character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password_hash character varying(255) NOT NULL,
    full_name character varying(255) NOT NULL,
    phone character varying(50) DEFAULT NULL::character varying,
    role character varying(50) DEFAULT 'user'::character varying,
    organization_id bigint,
    metadata text,
    is_active boolean DEFAULT true,
    remember_token character varying(100) DEFAULT NULL::character varying,
    email_verified_at timestamp without time zone,
    last_login_at timestamp without time zone,
    last_login_ip character varying(45) DEFAULT NULL::character varying,
    failed_login_attempts integer DEFAULT 0,
    locked_until timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp without time zone
);


ALTER TABLE public.users OWNER TO laravel;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: laravel
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO laravel;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: laravel
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: activity_logs id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.activity_logs ALTER COLUMN id SET DEFAULT nextval('public.activity_logs_id_seq'::regclass);


--
-- Name: assignment_submissions id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.assignment_submissions ALTER COLUMN id SET DEFAULT nextval('public.assignment_submissions_id_seq'::regclass);


--
-- Name: assignments id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.assignments ALTER COLUMN id SET DEFAULT nextval('public.assignments_id_seq'::regclass);


--
-- Name: bookings id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.bookings ALTER COLUMN id SET DEFAULT nextval('public.bookings_id_seq'::regclass);


--
-- Name: classes id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.classes ALTER COLUMN id SET DEFAULT nextval('public.classes_id_seq'::regclass);


--
-- Name: holidays id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.holidays ALTER COLUMN id SET DEFAULT nextval('public.holidays_id_seq'::regclass);


--
-- Name: important_schedules id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.important_schedules ALTER COLUMN id SET DEFAULT nextval('public.important_schedules_id_seq'::regclass);


--
-- Name: inventory_maintenance_logs id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.inventory_maintenance_logs ALTER COLUMN id SET DEFAULT nextval('public.inventory_maintenance_logs_id_seq'::regclass);


--
-- Name: lab_inventory id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory ALTER COLUMN id SET DEFAULT nextval('public.lab_inventory_id_seq'::regclass);


--
-- Name: lab_inventory_history id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory_history ALTER COLUMN id SET DEFAULT nextval('public.lab_inventory_history_id_seq'::regclass);


--
-- Name: lab_sessions id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_sessions ALTER COLUMN id SET DEFAULT nextval('public.lab_sessions_id_seq'::regclass);


--
-- Name: maintenance_records id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.maintenance_records ALTER COLUMN id SET DEFAULT nextval('public.maintenance_records_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: notifications id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.notifications ALTER COLUMN id SET DEFAULT nextval('public.notifications_id_seq'::regclass);


--
-- Name: organizations id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.organizations ALTER COLUMN id SET DEFAULT nextval('public.organizations_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: procurement_requests id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.procurement_requests ALTER COLUMN id SET DEFAULT nextval('public.procurement_requests_id_seq'::regclass);


--
-- Name: resources id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.resources ALTER COLUMN id SET DEFAULT nextval('public.resources_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: schedules id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.schedules ALTER COLUMN id SET DEFAULT nextval('public.schedules_id_seq'::regclass);


--
-- Name: sunday_bookings id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.sunday_bookings ALTER COLUMN id SET DEFAULT nextval('public.sunday_bookings_id_seq'::regclass);


--
-- Name: teachers id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.teachers ALTER COLUMN id SET DEFAULT nextval('public.teachers_id_seq'::regclass);


--
-- Name: telescope_entries sequence; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.telescope_entries ALTER COLUMN sequence SET DEFAULT nextval('public.telescope_entries_sequence_seq'::regclass);


--
-- Name: time_slots id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.time_slots ALTER COLUMN id SET DEFAULT nextval('public.time_slots_id_seq'::regclass);


--
-- Name: user_sessions id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.user_sessions ALTER COLUMN id SET DEFAULT nextval('public.user_sessions_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: activity_logs activity_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_pkey PRIMARY KEY (id);


--
-- Name: assignment_submissions assignment_submissions_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.assignment_submissions
    ADD CONSTRAINT assignment_submissions_pkey PRIMARY KEY (id);


--
-- Name: assignments assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_pkey PRIMARY KEY (id);


--
-- Name: bookings bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_pkey PRIMARY KEY (id);


--
-- Name: classes classes_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.classes
    ADD CONSTRAINT classes_pkey PRIMARY KEY (id);


--
-- Name: holidays holidays_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.holidays
    ADD CONSTRAINT holidays_pkey PRIMARY KEY (id);


--
-- Name: important_schedules important_schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_pkey PRIMARY KEY (id);


--
-- Name: inventory_maintenance_logs inventory_maintenance_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.inventory_maintenance_logs
    ADD CONSTRAINT inventory_maintenance_logs_pkey PRIMARY KEY (id);


--
-- Name: lab_inventory_history lab_inventory_history_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory_history
    ADD CONSTRAINT lab_inventory_history_pkey PRIMARY KEY (id);


--
-- Name: lab_inventory lab_inventory_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory
    ADD CONSTRAINT lab_inventory_pkey PRIMARY KEY (id);


--
-- Name: lab_sessions lab_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_sessions
    ADD CONSTRAINT lab_sessions_pkey PRIMARY KEY (id);


--
-- Name: maintenance_records maintenance_records_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.maintenance_records
    ADD CONSTRAINT maintenance_records_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: organizations organizations_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: procurement_requests procurement_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.procurement_requests
    ADD CONSTRAINT procurement_requests_pkey PRIMARY KEY (id);


--
-- Name: resource_user resource_user_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.resource_user
    ADD CONSTRAINT resource_user_pkey PRIMARY KEY (resource_id, user_id);


--
-- Name: resources resources_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.resources
    ADD CONSTRAINT resources_pkey PRIMARY KEY (id);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: schedules schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_pkey PRIMARY KEY (id);


--
-- Name: sunday_bookings sunday_bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_pkey PRIMARY KEY (id);


--
-- Name: teachers teachers_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT teachers_pkey PRIMARY KEY (id);


--
-- Name: telescope_entries telescope_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.telescope_entries
    ADD CONSTRAINT telescope_entries_pkey PRIMARY KEY (sequence);


--
-- Name: telescope_entries_tags telescope_entries_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.telescope_entries_tags
    ADD CONSTRAINT telescope_entries_tags_pkey PRIMARY KEY (entry_uuid, tag);


--
-- Name: telescope_entries telescope_entries_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.telescope_entries
    ADD CONSTRAINT telescope_entries_uuid_unique UNIQUE (uuid);


--
-- Name: telescope_monitoring telescope_monitoring_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.telescope_monitoring
    ADD CONSTRAINT telescope_monitoring_pkey PRIMARY KEY (tag);


--
-- Name: time_slots time_slots_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.time_slots
    ADD CONSTRAINT time_slots_pkey PRIMARY KEY (id);


--
-- Name: user_sessions user_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.user_sessions
    ADD CONSTRAINT user_sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: assignment_submissions_assignment_id_foreign; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX assignment_submissions_assignment_id_foreign ON public.assignment_submissions USING btree (assignment_id);


--
-- Name: assignments_teacher_id_foreign; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX assignments_teacher_id_foreign ON public.assignments USING btree (teacher_id);


--
-- Name: bookings_session_id_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX bookings_session_id_index ON public.bookings USING btree (session_id);


--
-- Name: bookings_teacher_id_foreign; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX bookings_teacher_id_foreign ON public.bookings USING btree (teacher_id);


--
-- Name: classes_pin_unique; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX classes_pin_unique ON public.classes USING btree (pin);


--
-- Name: fk_bookings_approved_by; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX fk_bookings_approved_by ON public.bookings USING btree (approved_by);


--
-- Name: fk_bookings_organization; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX fk_bookings_organization ON public.bookings USING btree (organization_id);


--
-- Name: fk_bookings_resource; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX fk_bookings_resource ON public.bookings USING btree (resource_id);


--
-- Name: fk_bookings_time_slot; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX fk_bookings_time_slot ON public.bookings USING btree (time_slot_id);


--
-- Name: fk_bookings_user; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX fk_bookings_user ON public.bookings USING btree (user_id);


--
-- Name: fk_maintenance_created_by; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX fk_maintenance_created_by ON public.maintenance_records USING btree (created_by);


--
-- Name: idx_action_type; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_action_type ON public.lab_inventory_history USING btree (action_type);


--
-- Name: idx_activity_logs_action; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_activity_logs_action ON public.activity_logs USING btree (action);


--
-- Name: idx_activity_logs_created; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_activity_logs_created ON public.activity_logs USING btree (created_at);


--
-- Name: idx_activity_logs_entity; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_activity_logs_entity ON public.activity_logs USING btree (entity_type, entity_id);


--
-- Name: idx_activity_logs_lookup; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_activity_logs_lookup ON public.activity_logs USING btree (user_id, action, created_at);


--
-- Name: idx_activity_logs_table; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_activity_logs_table ON public.activity_logs USING btree (table_name, record_id);


--
-- Name: idx_activity_logs_user; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_activity_logs_user ON public.activity_logs USING btree (user_id);


--
-- Name: idx_booking_conflict; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_booking_conflict ON public.bookings USING btree (resource_id, booking_date, time_slot_id, status);


--
-- Name: idx_category; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_category ON public.lab_inventory USING btree (category);


--
-- Name: idx_classes_active; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_classes_active ON public.classes USING btree (is_active);


--
-- Name: idx_classes_deleted; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_classes_deleted ON public.classes USING btree (deleted_at);


--
-- Name: idx_classes_grade; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_classes_grade ON public.classes USING btree (grade_level);


--
-- Name: idx_classes_organization; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_classes_organization ON public.classes USING btree (organization_id);


--
-- Name: idx_condition; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_condition ON public.lab_inventory USING btree (condition);


--
-- Name: idx_created_by; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_created_by ON public.lab_inventory USING btree (created_by);


--
-- Name: idx_deleted_at; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_deleted_at ON public.lab_inventory USING btree (deleted_at);


--
-- Name: idx_holidays_active; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_holidays_active ON public.holidays USING btree (is_active);


--
-- Name: idx_holidays_type; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_holidays_type ON public.holidays USING btree (type);


--
-- Name: idx_inventory_id; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_inventory_id ON public.lab_inventory_history USING btree (inventory_id);


--
-- Name: idx_is_resource_date; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_is_resource_date ON public.important_schedules USING btree (resource_id, date);


--
-- Name: idx_lab_id; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_lab_id ON public.procurement_requests USING btree (lab_id);


--
-- Name: idx_maintenance_dates; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_maintenance_dates ON public.maintenance_records USING btree (scheduled_date, completed_date);


--
-- Name: idx_maintenance_resource; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_maintenance_resource ON public.maintenance_records USING btree (resource_id);


--
-- Name: idx_maintenance_status; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_maintenance_status ON public.maintenance_records USING btree (status);


--
-- Name: idx_maintenance_type; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_maintenance_type ON public.maintenance_records USING btree (type);


--
-- Name: idx_notifications_created; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_notifications_created ON public.notifications USING btree (created_at);


--
-- Name: idx_notifications_priority; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_notifications_priority ON public.notifications USING btree (priority);


--
-- Name: idx_notifications_read; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_notifications_read ON public.notifications USING btree (is_read);


--
-- Name: idx_notifications_type; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_notifications_type ON public.notifications USING btree (type);


--
-- Name: idx_notifications_user; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_notifications_user ON public.notifications USING btree (user_id);


--
-- Name: idx_organizations_active; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_organizations_active ON public.organizations USING btree (is_active);


--
-- Name: idx_organizations_deleted; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_organizations_deleted ON public.organizations USING btree (deleted_at);


--
-- Name: idx_organizations_parent; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_organizations_parent ON public.organizations USING btree (parent_id);


--
-- Name: idx_organizations_type; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_organizations_type ON public.organizations USING btree (type);


--
-- Name: idx_performed_at; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_performed_at ON public.lab_inventory_history USING btree (performed_at);


--
-- Name: idx_performed_by; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_performed_by ON public.lab_inventory_history USING btree (performed_by);


--
-- Name: idx_priority; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_priority ON public.procurement_requests USING btree (priority);


--
-- Name: idx_quantity; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_quantity ON public.lab_inventory USING btree (quantity);


--
-- Name: idx_requested_at; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_requested_at ON public.procurement_requests USING btree (requested_at);


--
-- Name: idx_requested_by; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_requested_by ON public.procurement_requests USING btree (requested_by);


--
-- Name: idx_resource_id; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_resource_id ON public.lab_inventory USING btree (resource_id);


--
-- Name: idx_resources_deleted; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_resources_deleted ON public.resources USING btree (deleted_at);


--
-- Name: idx_resources_organization; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_resources_organization ON public.resources USING btree (organization_id);


--
-- Name: idx_resources_parent; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_resources_parent ON public.resources USING btree (parent_id);


--
-- Name: idx_resources_status; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_resources_status ON public.resources USING btree (status);


--
-- Name: idx_resources_type; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_resources_type ON public.resources USING btree (type);


--
-- Name: idx_reviewed_by; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_reviewed_by ON public.procurement_requests USING btree (reviewed_by);


--
-- Name: idx_schedules_class; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_schedules_class ON public.schedules USING btree (class_id);


--
-- Name: idx_schedules_day; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_schedules_day ON public.schedules USING btree (day_of_week);


--
-- Name: idx_schedules_deleted; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_schedules_deleted ON public.schedules USING btree (deleted_at);


--
-- Name: idx_schedules_lookup; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_schedules_lookup ON public.schedules USING btree (resource_id, day_of_week, status, deleted_at);


--
-- Name: idx_schedules_resource; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_schedules_resource ON public.schedules USING btree (resource_id);


--
-- Name: idx_schedules_status; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_schedules_status ON public.schedules USING btree (status);


--
-- Name: idx_schedules_time_slot; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_schedules_time_slot ON public.schedules USING btree (time_slot_id);


--
-- Name: idx_schedules_user; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_schedules_user ON public.schedules USING btree (user_id);


--
-- Name: idx_status; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_status ON public.lab_inventory USING btree (status);


--
-- Name: idx_status_deleted_cat; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_status_deleted_cat ON public.lab_inventory USING btree (status, deleted_at, category, item_name);


--
-- Name: idx_status_name; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_status_name ON public.resources USING btree (status, name);


--
-- Name: idx_status_procurement; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_status_procurement ON public.procurement_requests USING btree (status);


--
-- Name: idx_time_slots_active; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_time_slots_active ON public.time_slots USING btree (is_active);


--
-- Name: idx_time_slots_day; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_time_slots_day ON public.time_slots USING btree (day_of_week);


--
-- Name: idx_time_slots_order; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_time_slots_order ON public.time_slots USING btree (slot_order);


--
-- Name: idx_updated_by; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_updated_by ON public.lab_inventory USING btree (updated_by);


--
-- Name: idx_user_sessions_expires; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_user_sessions_expires ON public.user_sessions USING btree (expires_at);


--
-- Name: idx_user_sessions_user; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_user_sessions_user ON public.user_sessions USING btree (user_id);


--
-- Name: idx_users_active; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_users_active ON public.users USING btree (is_active);


--
-- Name: idx_users_deleted; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_users_deleted ON public.users USING btree (deleted_at);


--
-- Name: idx_users_organization; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_users_organization ON public.users USING btree (organization_id);


--
-- Name: idx_users_role; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX idx_users_role ON public.users USING btree (role);


--
-- Name: lab_sessions_resource_id_session_start_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX lab_sessions_resource_id_session_start_index ON public.lab_sessions USING btree (resource_id, session_start);


--
-- Name: lab_sessions_token_is_active_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX lab_sessions_token_is_active_index ON public.lab_sessions USING btree (token, is_active);


--
-- Name: lab_sessions_token_unique; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX lab_sessions_token_unique ON public.lab_sessions USING btree (token);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: permissions_name_guard_name_unique; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX permissions_name_guard_name_unique ON public.permissions USING btree (name, guard_name);


--
-- Name: role_has_permissions_role_id_foreign; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX role_has_permissions_role_id_foreign ON public.role_has_permissions USING btree (role_id);


--
-- Name: roles_name_guard_name_unique; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX roles_name_guard_name_unique ON public.roles USING btree (name, guard_name);


--
-- Name: schedules_teacher_id_foreign; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX schedules_teacher_id_foreign ON public.schedules USING btree (teacher_id);


--
-- Name: sunday_bookings_approved_by_foreign; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX sunday_bookings_approved_by_foreign ON public.sunday_bookings USING btree (approved_by);


--
-- Name: sunday_bookings_organization_id_foreign; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX sunday_bookings_organization_id_foreign ON public.sunday_bookings USING btree (organization_id);


--
-- Name: sunday_bookings_teacher_id_foreign; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX sunday_bookings_teacher_id_foreign ON public.sunday_bookings USING btree (teacher_id);


--
-- Name: teachers_token_unique; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX teachers_token_unique ON public.teachers USING btree (token);


--
-- Name: telescope_entries_batch_id_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX telescope_entries_batch_id_index ON public.telescope_entries USING btree (batch_id);


--
-- Name: telescope_entries_created_at_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX telescope_entries_created_at_index ON public.telescope_entries USING btree (created_at);


--
-- Name: telescope_entries_family_hash_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX telescope_entries_family_hash_index ON public.telescope_entries USING btree (family_hash);


--
-- Name: telescope_entries_tags_tag_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX telescope_entries_tags_tag_index ON public.telescope_entries_tags USING btree (tag);


--
-- Name: telescope_entries_type_should_display_on_index_index; Type: INDEX; Schema: public; Owner: laravel
--

CREATE INDEX telescope_entries_type_should_display_on_index_index ON public.telescope_entries USING btree (type, should_display_on_index);


--
-- Name: uk_classes_name_org; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_classes_name_org ON public.classes USING btree (name, organization_id, deleted_at);


--
-- Name: uk_holidays_date; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_holidays_date ON public.holidays USING btree (date);


--
-- Name: uk_organizations_name; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_organizations_name ON public.organizations USING btree (name);


--
-- Name: uk_resources_name; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_resources_name ON public.resources USING btree (name, deleted_at);


--
-- Name: uk_schedules_unique; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_schedules_unique ON public.schedules USING btree (day_of_week, time_slot_id, resource_id, deleted_at);


--
-- Name: uk_sunday_resource_date; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_sunday_resource_date ON public.sunday_bookings USING btree (resource_id, booking_date);


--
-- Name: uk_teachers_phone; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_teachers_phone ON public.teachers USING btree (phone);


--
-- Name: uk_time_slots_name; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_time_slots_name ON public.time_slots USING btree (name);


--
-- Name: uk_user_sessions_token; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_user_sessions_token ON public.user_sessions USING btree (token);


--
-- Name: uk_users_email; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_users_email ON public.users USING btree (email);


--
-- Name: uk_users_username; Type: INDEX; Schema: public; Owner: laravel
--

CREATE UNIQUE INDEX uk_users_username ON public.users USING btree (username);


--
-- Name: assignment_submissions assignment_submissions_assignment_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.assignment_submissions
    ADD CONSTRAINT assignment_submissions_assignment_id_foreign FOREIGN KEY (assignment_id) REFERENCES public.assignments(id) ON DELETE CASCADE;


--
-- Name: assignments assignments_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE CASCADE;


--
-- Name: bookings bookings_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE SET NULL;


--
-- Name: activity_logs fk_activity_logs_user; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: bookings fk_bookings_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_approved_by FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: bookings fk_bookings_organization; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: bookings fk_bookings_resource; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_resource FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: bookings fk_bookings_time_slot; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_time_slot FOREIGN KEY (time_slot_id) REFERENCES public.time_slots(id);


--
-- Name: bookings fk_bookings_user; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: classes fk_classes_organization; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.classes
    ADD CONSTRAINT fk_classes_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: lab_inventory_history fk_inventory_history_inventory; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory_history
    ADD CONSTRAINT fk_inventory_history_inventory FOREIGN KEY (inventory_id) REFERENCES public.lab_inventory(id) ON DELETE CASCADE;


--
-- Name: lab_inventory_history fk_inventory_history_user; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory_history
    ADD CONSTRAINT fk_inventory_history_user FOREIGN KEY (performed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: lab_inventory fk_lab_inventory_created_by; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory
    ADD CONSTRAINT fk_lab_inventory_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: lab_inventory fk_lab_inventory_resource; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory
    ADD CONSTRAINT fk_lab_inventory_resource FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: lab_inventory fk_lab_inventory_updated_by; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_inventory
    ADD CONSTRAINT fk_lab_inventory_updated_by FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: maintenance_records fk_maintenance_created_by; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.maintenance_records
    ADD CONSTRAINT fk_maintenance_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: maintenance_records fk_maintenance_resource; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.maintenance_records
    ADD CONSTRAINT fk_maintenance_resource FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: notifications fk_notifications_user; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: organizations fk_organizations_parent; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT fk_organizations_parent FOREIGN KEY (parent_id) REFERENCES public.organizations(id) ON DELETE SET NULL;


--
-- Name: resources fk_resources_organization; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.resources
    ADD CONSTRAINT fk_resources_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE SET NULL;


--
-- Name: resources fk_resources_parent; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.resources
    ADD CONSTRAINT fk_resources_parent FOREIGN KEY (parent_id) REFERENCES public.resources(id) ON DELETE SET NULL;


--
-- Name: schedules fk_schedules_class; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT fk_schedules_class FOREIGN KEY (class_id) REFERENCES public.classes(id) ON DELETE CASCADE;


--
-- Name: schedules fk_schedules_resource; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT fk_schedules_resource FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: schedules fk_schedules_time_slot; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT fk_schedules_time_slot FOREIGN KEY (time_slot_id) REFERENCES public.time_slots(id);


--
-- Name: schedules fk_schedules_user; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT fk_schedules_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: user_sessions fk_user_sessions_user; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.user_sessions
    ADD CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: users fk_users_organization; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT fk_users_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE SET NULL;


--
-- Name: important_schedules important_schedules_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: important_schedules important_schedules_end_slot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_end_slot_id_foreign FOREIGN KEY (end_slot_id) REFERENCES public.time_slots(id) ON DELETE SET NULL;


--
-- Name: important_schedules important_schedules_resource_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: important_schedules important_schedules_start_slot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_start_slot_id_foreign FOREIGN KEY (start_slot_id) REFERENCES public.time_slots(id) ON DELETE SET NULL;


--
-- Name: inventory_maintenance_logs inventory_maintenance_logs_lab_inventory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.inventory_maintenance_logs
    ADD CONSTRAINT inventory_maintenance_logs_lab_inventory_id_foreign FOREIGN KEY (lab_inventory_id) REFERENCES public.lab_inventory(id) ON DELETE CASCADE;


--
-- Name: inventory_maintenance_logs inventory_maintenance_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.inventory_maintenance_logs
    ADD CONSTRAINT inventory_maintenance_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: lab_sessions lab_sessions_resource_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.lab_sessions
    ADD CONSTRAINT lab_sessions_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: resource_user resource_user_resource_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.resource_user
    ADD CONSTRAINT resource_user_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: resource_user resource_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.resource_user
    ADD CONSTRAINT resource_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: schedules schedules_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE SET NULL;


--
-- Name: sunday_bookings sunday_bookings_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: sunday_bookings sunday_bookings_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: sunday_bookings sunday_bookings_resource_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: sunday_bookings sunday_bookings_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE SET NULL;


--
-- Name: telescope_entries_tags telescope_entries_tags_entry_uuid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: laravel
--

ALTER TABLE ONLY public.telescope_entries_tags
    ADD CONSTRAINT telescope_entries_tags_entry_uuid_foreign FOREIGN KEY (entry_uuid) REFERENCES public.telescope_entries(uuid) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict ZsBhTYPDoZ3U3lP9pnIpjv6hgWNl8N4ehi5vwknqYfcKizWKWLbBnq29JvU7KRf

