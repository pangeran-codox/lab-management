--
-- PostgreSQL database dump
--

\restrict jfOHLJOObcgytb0F8DkNJq8Cc8leBz2MCr0GPf1I0L9ro3uLHrG4bz02ZvHDcz4

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
-- Name: activity_logs; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: activity_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.activity_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: activity_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.activity_logs_id_seq OWNED BY public.activity_logs.id;


--
-- Name: assignment_submissions; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: assignment_submissions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.assignment_submissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: assignment_submissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.assignment_submissions_id_seq OWNED BY public.assignment_submissions.id;


--
-- Name: assignments; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: assignments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.assignments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.assignments_id_seq OWNED BY public.assignments.id;


--
-- Name: bookings; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.bookings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.bookings_id_seq OWNED BY public.bookings.id;


--
-- Name: classes; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: classes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.classes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: classes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.classes_id_seq OWNED BY public.classes.id;


--
-- Name: holidays; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: holidays_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.holidays_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: holidays_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.holidays_id_seq OWNED BY public.holidays.id;


--
-- Name: important_schedules; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: important_schedules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.important_schedules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: important_schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.important_schedules_id_seq OWNED BY public.important_schedules.id;


--
-- Name: inventory_maintenance_logs; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: inventory_maintenance_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.inventory_maintenance_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inventory_maintenance_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.inventory_maintenance_logs_id_seq OWNED BY public.inventory_maintenance_logs.id;


--
-- Name: lab_inventory; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: lab_inventory_history; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: lab_inventory_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lab_inventory_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lab_inventory_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lab_inventory_history_id_seq OWNED BY public.lab_inventory_history.id;


--
-- Name: lab_inventory_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lab_inventory_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lab_inventory_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lab_inventory_id_seq OWNED BY public.lab_inventory.id;


--
-- Name: lab_sessions; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: lab_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lab_sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lab_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lab_sessions_id_seq OWNED BY public.lab_sessions.id;


--
-- Name: maintenance_records; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: maintenance_records_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.maintenance_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: maintenance_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.maintenance_records_id_seq OWNED BY public.maintenance_records.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.notifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.notifications_id_seq OWNED BY public.notifications.id;


--
-- Name: organizations; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: organizations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.organizations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: organizations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.organizations_id_seq OWNED BY public.organizations.id;


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: procurement_requests; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: procurement_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.procurement_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: procurement_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.procurement_requests_id_seq OWNED BY public.procurement_requests.id;


--
-- Name: resource_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.resource_user (
    resource_id bigint NOT NULL,
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: resources; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: resources_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.resources_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: resources_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.resources_id_seq OWNED BY public.resources.id;


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: schedules; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: schedules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.schedules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.schedules_id_seq OWNED BY public.schedules.id;


--
-- Name: sunday_bookings; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: sunday_bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sunday_bookings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sunday_bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sunday_bookings_id_seq OWNED BY public.sunday_bookings.id;


--
-- Name: teachers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.teachers (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    phone character varying(255) DEFAULT NULL::character varying,
    token character varying(10) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    weekly_quota integer DEFAULT 5 NOT NULL
);


--
-- Name: teachers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.teachers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: teachers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.teachers_id_seq OWNED BY public.teachers.id;


--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.telescope_entries_sequence_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: telescope_monitoring; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.telescope_monitoring (
    tag character varying(255) NOT NULL
);


--
-- Name: time_slots; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: time_slots_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.time_slots_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: time_slots_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.time_slots_id_seq OWNED BY public.time_slots.id;


--
-- Name: user_sessions; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: user_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_sessions_id_seq OWNED BY public.user_sessions.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
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
    deleted_at timestamp without time zone,
    weekly_quota integer DEFAULT 5 NOT NULL
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: activity_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.activity_logs ALTER COLUMN id SET DEFAULT nextval('public.activity_logs_id_seq'::regclass);


--
-- Name: assignment_submissions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assignment_submissions ALTER COLUMN id SET DEFAULT nextval('public.assignment_submissions_id_seq'::regclass);


--
-- Name: assignments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assignments ALTER COLUMN id SET DEFAULT nextval('public.assignments_id_seq'::regclass);


--
-- Name: bookings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bookings ALTER COLUMN id SET DEFAULT nextval('public.bookings_id_seq'::regclass);


--
-- Name: classes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classes ALTER COLUMN id SET DEFAULT nextval('public.classes_id_seq'::regclass);


--
-- Name: holidays id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.holidays ALTER COLUMN id SET DEFAULT nextval('public.holidays_id_seq'::regclass);


--
-- Name: important_schedules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.important_schedules ALTER COLUMN id SET DEFAULT nextval('public.important_schedules_id_seq'::regclass);


--
-- Name: inventory_maintenance_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_maintenance_logs ALTER COLUMN id SET DEFAULT nextval('public.inventory_maintenance_logs_id_seq'::regclass);


--
-- Name: lab_inventory id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory ALTER COLUMN id SET DEFAULT nextval('public.lab_inventory_id_seq'::regclass);


--
-- Name: lab_inventory_history id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory_history ALTER COLUMN id SET DEFAULT nextval('public.lab_inventory_history_id_seq'::regclass);


--
-- Name: lab_sessions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_sessions ALTER COLUMN id SET DEFAULT nextval('public.lab_sessions_id_seq'::regclass);


--
-- Name: maintenance_records id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_records ALTER COLUMN id SET DEFAULT nextval('public.maintenance_records_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: notifications id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications ALTER COLUMN id SET DEFAULT nextval('public.notifications_id_seq'::regclass);


--
-- Name: organizations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organizations ALTER COLUMN id SET DEFAULT nextval('public.organizations_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: procurement_requests id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.procurement_requests ALTER COLUMN id SET DEFAULT nextval('public.procurement_requests_id_seq'::regclass);


--
-- Name: resources id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.resources ALTER COLUMN id SET DEFAULT nextval('public.resources_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: schedules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.schedules ALTER COLUMN id SET DEFAULT nextval('public.schedules_id_seq'::regclass);


--
-- Name: sunday_bookings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sunday_bookings ALTER COLUMN id SET DEFAULT nextval('public.sunday_bookings_id_seq'::regclass);


--
-- Name: teachers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teachers ALTER COLUMN id SET DEFAULT nextval('public.teachers_id_seq'::regclass);


--
-- Name: time_slots id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.time_slots ALTER COLUMN id SET DEFAULT nextval('public.time_slots_id_seq'::regclass);


--
-- Name: user_sessions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_sessions ALTER COLUMN id SET DEFAULT nextval('public.user_sessions_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: activity_logs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.activity_logs (id, user_id, user_name, action, entity_type, entity_id, table_name, record_id, description, changes, ip_address, user_agent, metadata, created_at) FROM stdin;
1	\N	System	CREATE	class	1	\N	\N	Kelas baru ditambahkan: 8-A - Kelas 8 A	\N	170.1.0.6	\N	\N	2026-01-13 03:48:19
2	\N	System	CREATE	class	2	\N	\N	Kelas baru ditambahkan: 8-B - Kelas 8 B	\N	170.1.0.6	\N	\N	2026-01-13 03:48:44
3	\N	System	CREATE	class	3	\N	\N	Kelas baru ditambahkan: 8-C - Kelas 8 C	\N	170.1.0.6	\N	\N	2026-01-13 03:49:00
4	\N	System	CREATE	class	4	\N	\N	Kelas baru ditambahkan: 8-D - Kelas 8 D	\N	170.1.0.6	\N	\N	2026-01-13 03:50:20
5	\N	System	CREATE	class	5	\N	\N	Kelas baru ditambahkan: 8-E - Kelas 8 E	\N	170.1.0.6	\N	\N	2026-01-13 03:50:33
6	\N	System	CREATE	class	6	\N	\N	Kelas baru ditambahkan: 8-F - Kelas 8 F	\N	170.1.0.6	\N	\N	2026-01-13 03:50:55
7	\N	System	CREATE	class	7	\N	\N	Kelas baru ditambahkan: 8-G - Kelas 8 G	\N	170.1.0.6	\N	\N	2026-01-13 03:52:36
8	\N	System	CREATE	class	8	\N	\N	Kelas baru ditambahkan: 8-H - Kelas 8 H	\N	170.1.0.6	\N	\N	2026-01-13 03:53:31
9	\N	System	CREATE	class	9	\N	\N	Kelas baru ditambahkan: 8-I - Kelas 8 I	\N	170.1.0.6	\N	\N	2026-01-13 03:53:48
10	\N	System	CREATE	class	10	\N	\N	Kelas baru ditambahkan: 9-A - Kelas 9 A	\N	170.1.0.6	\N	\N	2026-01-13 03:54:08
11	\N	System	CREATE	class	11	\N	\N	Kelas baru ditambahkan: 9-B - Kelas 9 B	\N	170.1.0.6	\N	\N	2026-01-13 03:54:27
12	\N	System	CREATE	class	12	\N	\N	Kelas baru ditambahkan: 9-C - Kelas 9 C	\N	170.1.0.6	\N	\N	2026-01-13 03:54:49
13	\N	System	CREATE	class	13	\N	\N	Kelas baru ditambahkan: 9-D - Kelas 9 D	\N	170.1.0.6	\N	\N	2026-01-13 03:55:08
14	\N	System	CREATE	class	14	\N	\N	Kelas baru ditambahkan: 9-E - Kelas 9 E	\N	170.1.0.6	\N	\N	2026-01-13 03:55:26
15	\N	System	CREATE	class	15	\N	\N	Kelas baru ditambahkan: 9-F - Kelas 9 F	\N	170.1.0.6	\N	\N	2026-01-13 03:55:37
16	\N	System	CREATE	class	16	\N	\N	Kelas baru ditambahkan: 9-G - Kelas 9 G	\N	170.1.0.6	\N	\N	2026-01-13 03:56:01
17	\N	System	CREATE	class	17	\N	\N	Kelas baru ditambahkan: 9-H - Kelas 9 H	\N	170.1.0.6	\N	\N	2026-01-13 03:56:28
18	\N	System	CREATE	class	18	\N	\N	Kelas baru ditambahkan: 9-I - Kelas 9 I	\N	170.1.0.6	\N	\N	2026-01-13 03:56:41
19	\N	System	CREATE	class	19	\N	\N	Kelas baru ditambahkan: XII-IPA-1 - Kelas XII IPA 1	\N	170.1.0.6	\N	\N	2026-01-13 04:00:07
20	\N	System	CREATE	class	20	\N	\N	Kelas baru ditambahkan: XII-IPA-2 - Kelas XII IPA 2	\N	170.1.0.6	\N	\N	2026-01-13 04:00:36
21	\N	System	CREATE	class	21	\N	\N	Kelas baru ditambahkan: XII-IPA-3 - Kelas XII IPA 3	\N	170.1.0.6	\N	\N	2026-01-13 04:01:00
22	\N	System	CREATE	class	22	\N	\N	Kelas baru ditambahkan: XII-IPS-1 - Kelas XII IPS 1	\N	170.1.0.6	\N	\N	2026-01-13 04:01:27
23	\N	System	CREATE	class	23	\N	\N	Kelas baru ditambahkan: XII-IPS-2 - Kelas XII IPS 2	\N	170.1.0.6	\N	\N	2026-01-13 04:01:52
24	\N	System	create	schedule	1	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.0.6	\N	\N	2026-01-13 04:32:11
25	\N	System	create	schedule	2	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 13:56:47
26	\N	System	create	schedule	3	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:01:09
27	\N	System	create	schedule	4	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:01:36
28	\N	System	create	schedule	5	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:02:06
29	\N	System	create	schedule	6	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:02:26
30	\N	System	CREATE	class	24	\N	\N	Kelas baru ditambahkan: EXC - Kelas EXC	\N	170.1.1.1	\N	\N	2026-01-15 14:10:11
31	\N	System	create	schedule	7	\N	\N	Jadwal baru ditambahkan: Tentor - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:11:14
32	\N	System	create	schedule	8	\N	\N	Jadwal baru ditambahkan: Tentor - KIR	\N	170.1.1.1	\N	\N	2026-01-15 14:12:51
33	\N	System	create	schedule	9	\N	\N	Jadwal baru ditambahkan: Tentor - KIR	\N	170.1.1.1	\N	\N	2026-01-15 14:13:35
34	\N	System	create	schedule	10	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:17:11
35	\N	System	create	schedule	11	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:17:32
36	\N	System	create	schedule	12	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:19:01
37	\N	System	create	schedule	13	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:19:19
38	\N	System	create	schedule	14	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:20:15
39	\N	System	create	schedule	15	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:20:47
40	\N	System	create	schedule	16	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:22:03
41	\N	System	create	schedule	17	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:22:27
42	\N	System	create	schedule	18	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:23:25
43	\N	System	create	schedule	19	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:23:44
44	\N	System	create	schedule	20	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:24:19
45	\N	System	create	schedule	21	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:25:06
46	\N	System	create	schedule	22	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:28:21
47	\N	System	create	schedule	23	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:28:50
48	\N	System	create	schedule	24	\N	\N	Jadwal baru ditambahkan: Tentor - KIR	\N	170.1.1.1	\N	\N	2026-01-15 14:32:19
49	\N	System	create	schedule	25	\N	\N	Jadwal baru ditambahkan: Bu Husnul - KIR	\N	170.1.1.1	\N	\N	2026-01-15 14:32:42
50	\N	System	create	schedule	26	\N	\N	Jadwal baru ditambahkan: Tentor - KIR	\N	170.1.1.1	\N	\N	2026-01-15 14:34:32
51	\N	System	create	schedule	27	\N	\N	Jadwal baru ditambahkan: Tentor - KIR	\N	170.1.1.1	\N	\N	2026-01-15 14:34:53
52	\N	System	create	schedule	28	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:35:38
53	\N	System	create	schedule	29	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:35:57
54	\N	System	create	schedule	30	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:37:15
55	\N	System	create	schedule	31	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:37:37
56	\N	System	create	schedule	32	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:37:52
57	\N	System	create	schedule	33	\N	\N	Jadwal baru ditambahkan: Bu Husnul - TIK	\N	170.1.1.1	\N	\N	2026-01-15 14:38:18
58	\N	dasd	CREATE	booking	1	\N	\N	Booking baru dibuat: BKG-20260202-0001 - asa	\N	\N	\N	\N	2026-02-02 13:56:40
59	\N	Admin	APPROVE	booking	1	\N	\N	Booking disetujui: asa	\N	\N	\N	\N	2026-02-03 13:19:48
60	\N	dasdad	CREATE	booking	2	\N	\N	Booking baru dibuat: BKG-20260203-0002 - adas	\N	\N	\N	\N	2026-02-03 13:24:39
61	\N	dasdad	CREATE	booking	3	\N	\N	Booking baru dibuat: BKG-20260203-0003 - adas	\N	\N	\N	\N	2026-02-03 13:24:39
62	\N	Admin	APPROVE	booking	2	\N	\N	Booking disetujui: adas	\N	\N	\N	\N	2026-02-03 13:24:57
63	\N	Admin	APPROVE	booking	3	\N	\N	Booking disetujui: adas	\N	\N	\N	\N	2026-02-03 13:25:00
64	\N	System	CREATE	resource	3	\N	\N	Lab baru ditambahkan: Lab Komputer 1	\N	103.164.212.121	\N	\N	2026-02-05 04:59:15
65	\N	System	CREATE	resource	4	\N	\N	Lab baru ditambahkan: Lab Komputer 2	\N	103.164.212.121	\N	\N	2026-02-05 05:00:42
66	\N	System	CREATE	resource	5	\N	\N	Lab baru ditambahkan: Lab Komputer SMP	\N	103.164.212.121	\N	\N	2026-02-05 05:04:29
67	\N	System	CREATE	resource	6	\N	\N	Lab baru ditambahkan: Lab Komputer 3	\N	103.164.212.121	\N	\N	2026-02-06 03:23:27
68	\N	System	CREATE	resource	7	\N	\N	Lab baru ditambahkan: Lab Komputer 4	\N	103.164.212.121	\N	\N	2026-02-06 03:25:17
69	\N	System	CREATE	resource	8	\N	\N	Lab baru ditambahkan: Lab Fiber Optic	\N	103.164.212.121	\N	\N	2026-02-06 03:26:32
70	\N	System	CREATE	role	\N	\N	\N	Role admin dan teknisi ditambahkan via setup script	\N	\N	\N	\N	2026-02-20 13:59:27
71	\N	System	CREATE	user	\N	\N	\N	User teknisi1 ditambahkan sebagai PIC Lab Komputer 7 & 8	\N	\N	\N	\N	2026-02-20 13:59:27
72	\N	System	CREATE	role	\N	\N	\N	Role admin & teknisi ditambahkan	\N	\N	\N	\N	2026-02-20 14:08:02
73	\N	System	CREATE	user	\N	\N	\N	User teknisi1 dibuat, assigned PIC Lab 7 & Lab 8	\N	\N	\N	\N	2026-02-20 14:08:02
\.


--
-- Data for Name: assignment_submissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.assignment_submissions (id, assignment_id, student_name, student_class, file_path, file_name, file_size, file_ext, status, grade, feedback, submitted_at, created_at, updated_at) FROM stdin;
1	1	bang ucup	xii ipa	submissions/1/4mgS3noIUF0klrNtHqkb6icCMu9ozx6fO4eKlnjL.xlsx	Inventaris_Semua_Lab_2026-02-07_184029 (1).xlsx	19.3 KB	xlsx	graded	11.00	\N	2026-02-27 21:27:23	2026-02-27 21:27:23	2026-02-28 12:39:06
2	2	margono	X PK 1	submissions/2/vJJRDHiKR1AEkPmtkXwE3OxbmXosjPWCxp2llkg0.docx	Dafa Farhan Aldisa.docx	10 KB	docx	submitted	\N	\N	2026-07-02 11:38:51	2026-07-02 11:38:51	2026-07-02 11:38:51
\.


--
-- Data for Name: assignments; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.assignments (id, teacher_id, organization_id, title, description, subject_name, class_name, deadline, is_active, attachment_path, attachment_name, attachment_size, created_at, updated_at) FROM stdin;
1	1	\N	sad	asdasd	TIK	X ipa 1	2026-02-28 21:14:00	t	attachments/XKDazzd0Wgih9nHXKckzIw7DUXVPb36KYff2cOqv.xlsx	Inventaris_Semua_Lab_2026-02-07_184029.xlsx	19.3 KB	2026-02-27 21:14:44	2026-02-27 21:14:44
2	29	3	ngaji	ngaji sambil kayang	tata boga	X PK 1	2026-07-08 10:00:00	t	attachments/krFBV41g8OGRh8pfH4kT5S7IoR5FuhChpqNPsIFS.pdf	CV ATS.pdf	358.1 KB	2026-07-02 11:36:39	2026-07-02 11:36:39
\.


--
-- Data for Name: bookings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.bookings (id, session_id, teacher_id, resource_id, time_slot_id, user_id, organization_id, booking_date, teacher_name, teacher_phone, class_name, subject_name, title, description, participant_count, status, approved_by, approved_at, notes, created_at, updated_at) FROM stdin;
1	24d5d847-20df-44b2-9f64-f1909e97a581	\N	1	5	\N	1	2026-02-03	dasd	083265485687	X ipa 1	TIK	asa	saa	10	approved	1	2026-02-03 13:19:48	\N	2026-02-02 13:56:40	2026-05-16 06:51:43
2	1388a485-3b23-4bab-9095-18059ad5e7fe	\N	2	1	\N	5	2026-02-04	dasdad	083265485687	12	TIK	adas	sad	7	approved	1	2026-02-03 13:24:57	\N	2026-02-03 13:24:39	2026-05-16 06:51:43
3	1388a485-3b23-4bab-9095-18059ad5e7fe	\N	2	2	\N	5	2026-02-04	dasdad	083265485687	12	TIK	adas	sad	7	approved	1	2026-02-03 13:25:00	\N	2026-02-03 13:24:39	2026-05-16 06:51:43
4	8d082824-bea5-4c64-9f7a-1d5e33953286	\N	1	1	\N	1	2026-02-23	bang ucup	081217574441	Kelas XII IPA 1	tik	grafik	\N	10	approved	5	2026-02-21 12:53:49	\N	2026-02-21 12:52:57	2026-05-16 06:51:43
5	8d082824-bea5-4c64-9f7a-1d5e33953286	\N	1	2	\N	1	2026-02-23	bang ucup	081217574441	Kelas XII IPA 1	tik	grafik	\N	10	approved	5	2026-02-21 12:53:46	\N	2026-02-21 12:52:57	2026-05-16 06:51:43
6	543bd2d1-98c2-470e-93cd-6dd0f5a96197	\N	1	3	\N	2	2026-02-23	pak guru	083265485687	Kelas 9 G	TIK	ada	\N	2	approved	5	2026-02-21 13:18:04	\N	2026-02-21 13:17:42	2026-05-16 06:51:43
7	d8825c01-ab38-42f5-9a63-4177e7c98ff2	\N	2	3	\N	2	2026-02-26	bang ucup	081217574441	Kelas 9 E	tik	tes	\N	10	approved	1	2026-02-26 00:51:39	\N	2026-02-26 00:51:09	2026-05-16 06:51:43
8	d8825c01-ab38-42f5-9a63-4177e7c98ff2	\N	2	4	\N	2	2026-02-26	bang ucup	081217574441	Kelas 9 E	tik	tes	\N	10	approved	1	2026-02-26 00:51:37	\N	2026-02-26 00:51:09	2026-05-16 06:51:43
9	547e0a43-046c-401f-8fb1-72bc071786ae	\N	1	4	\N	1	2026-02-27	Bang Ucup	6281217574441	Kelas XII IPA 2	inggris	praktik	\N	12	approved	1	2026-02-27 01:54:35	\N	2026-02-27 01:54:00	2026-05-16 06:51:43
10	7da70b5d-fbe5-435f-beb6-1372eb042ba6	\N	1	5	\N	1	2026-02-27	Bang Ucup	6281217574441	Kelas XII IPA 1	inggris	asd	\N	3	approved	1	2026-02-27 01:57:12	\N	2026-02-27 01:56:55	2026-05-16 06:51:43
11	db9914e9-a85b-4ded-88ae-0728dba3044d	\N	2	8	\N	2	2026-02-27	Bang Ucup	6281217574441	Kelas 9 H	tik	asaddf	\N	2	approved	1	2026-02-27 02:17:43	\N	2026-02-27 02:16:50	2026-05-16 06:51:43
12	1dca1c5f-5c90-445b-984d-001b2e9ad00d	\N	3	8	\N	2	2026-02-27	Alip	6283112088830	Kelas 9 G	tik	vdgfd	\N	4	approved	1	2026-02-27 03:44:33	\N	2026-02-27 03:43:48	2026-05-16 06:51:43
13	1dca1c5f-5c90-445b-984d-001b2e9ad00d	\N	3	9	\N	2	2026-02-27	Alip	6283112088830	Kelas 9 G	tik	vdgfd	\N	4	approved	1	2026-02-27 03:44:36	\N	2026-02-27 03:43:48	2026-05-16 06:51:43
14	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	1	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:06	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
15	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	2	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:11	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
16	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	3	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:15	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
17	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	4	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:19	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
18	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	5	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:34	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
19	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	7	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:44	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
20	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	8	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:47	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
21	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	9	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:49	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
22	1b544aaa-72e0-44e3-82fc-bfe0908f57e4	\N	4	10	\N	1	2026-03-07	Bu Husnul	6281217574441	Kelas XII IPA 2	aswaja	ffd	\N	2	approved	1	2026-02-28 12:35:52	\N	2026-02-28 12:33:51	2026-05-16 06:51:43
23	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	1	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
24	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	2	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
25	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	3	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
26	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	4	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
27	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	5	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
28	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	7	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
29	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	8	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
30	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	9	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
31	baa65b30-a806-470a-aba6-3bed3cf59891	\N	4	10	\N	2	2026-03-06	Bu Husnul	6281217574441	Kelas 9 G	tik	ada saja	\N	2	approved	1	2026-02-28 18:59:22	\N	2026-02-28 18:58:48	2026-05-16 06:51:43
32	a016a684-232f-4b9b-a037-833780c46e12	\N	3	1	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:35	2026-05-16 06:51:43
33	a016a684-232f-4b9b-a037-833780c46e12	\N	3	2	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:35	2026-05-16 06:51:43
34	a016a684-232f-4b9b-a037-833780c46e12	\N	3	3	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:35	2026-05-16 06:51:43
35	a016a684-232f-4b9b-a037-833780c46e12	\N	3	4	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:35	2026-05-16 06:51:43
36	a016a684-232f-4b9b-a037-833780c46e12	\N	3	5	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:35	2026-05-16 06:51:43
37	a016a684-232f-4b9b-a037-833780c46e12	\N	3	7	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:35	2026-05-16 06:51:43
38	a016a684-232f-4b9b-a037-833780c46e12	\N	3	8	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:35	2026-05-16 06:51:43
39	a016a684-232f-4b9b-a037-833780c46e12	\N	3	9	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:35	2026-05-16 06:51:43
40	a016a684-232f-4b9b-a037-833780c46e12	\N	3	10	\N	2	2026-03-02	Bu Husnul	6281217574441	Kelas 8 H	tik	tes	\N	3	approved	1	2026-02-28 19:20:59	\N	2026-02-28 19:20:36	2026-05-16 06:51:43
41	df24ad54-52a0-481c-a45f-7bc7bc174e1b	\N	2	4	\N	2	2026-05-01	Bang Ucup	6281217574441	Kelas 9 H	TIK	sasdas	\N	2	rejected	\N	\N	asdfas	2026-04-30 13:33:32	2026-05-16 06:51:43
42	b3e4e237-1c14-4534-a78c-664bbb116d29	\N	2	3	\N	2	2026-05-01	Bang Ucup	6281217574441	Kelas 9 G	TIK	ada	\N	3	rejected	\N	\N	sdfasd	2026-05-01 00:05:35	2026-05-16 06:51:43
43	7e34fea1-513d-4cde-9f30-a482b086a9d1	\N	2	5	\N	2	2026-05-02	Bang Ucup	6281217574441	Kelas 8 H	TIK	adasd	\N	3	rejected	\N	\N	dasda	2026-05-01 00:10:22	2026-05-16 06:51:43
44	ba66ea7b-b0d4-4086-8e51-421c04f8de18	\N	1	2	\N	2	2026-05-01	Bang Ucup	6281217574441	Kelas 9 H	TIK	dasdasdf	\N	2	rejected	\N	\N	asdas	2026-05-01 00:16:48	2026-05-16 06:51:43
45	ba66ea7b-b0d4-4086-8e51-421c04f8de18	\N	1	3	\N	2	2026-05-01	Bang Ucup	6281217574441	Kelas 9 H	TIK	dasdasdf	\N	2	rejected	\N	\N	sdasd	2026-05-01 00:16:48	2026-05-16 06:51:43
46	b4b152b9-cc5a-44d2-9cf8-dcd64b8f7c5b	\N	3	2	\N	2	2026-05-15	Bang Ucup	6281217574441	Kelas 9 H	bahasa inggris	baca buku	\N	12	rejected	\N	\N	ada aja	2026-05-13 13:30:48	2026-05-16 12:52:39
47	4642cd94-41e0-4c2c-9c8d-a4c32be11cad	28	1	1	\N	1	2026-06-05	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	percobaan	hehe	3	approved	1	2026-06-05 06:44:06	\N	2026-06-05 06:33:35	2026-06-05 06:44:06
48	4642cd94-41e0-4c2c-9c8d-a4c32be11cad	28	1	2	\N	1	2026-06-05	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	percobaan	hehe	3	approved	1	2026-06-05 06:44:06	\N	2026-06-05 06:33:35	2026-06-05 06:44:06
49	4642cd94-41e0-4c2c-9c8d-a4c32be11cad	28	1	3	\N	1	2026-06-05	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	percobaan	hehe	3	approved	1	2026-06-05 06:44:06	\N	2026-06-05 06:33:35	2026-06-05 06:44:06
54	624d3393-8e7e-4b05-9199-64dff163695f	28	2	3	\N	2	2026-06-09	Bang Ucup	6281217874441	Kelas 9 I	tik	asdasdasd	\N	4	approved	1	2026-06-07 21:54:33	\N	2026-06-07 21:49:34	2026-06-07 21:54:33
55	624d3393-8e7e-4b05-9199-64dff163695f	28	2	4	\N	2	2026-06-09	Bang Ucup	6281217874441	Kelas 9 I	tik	asdasdasd	\N	4	approved	1	2026-06-07 21:54:33	\N	2026-06-07 21:49:34	2026-06-07 21:54:33
56	624d3393-8e7e-4b05-9199-64dff163695f	28	2	5	\N	2	2026-06-09	Bang Ucup	6281217874441	Kelas 9 I	tik	asdasdasd	\N	4	approved	1	2026-06-07 21:54:33	\N	2026-06-07 21:49:34	2026-06-07 21:54:33
75	4c5098b3-acdc-4fd7-9459-82665179554a	28	3	4	\N	2	2026-06-10	Bang Ucup	6281217874441	Kelas 9 F	TIK	asd	asdas	1	rejected	\N	\N	fdfsadf	2026-06-08 20:06:52	2026-06-08 20:14:00
74	4c5098b3-acdc-4fd7-9459-82665179554a	28	3	3	\N	2	2026-06-10	Bang Ucup	6281217874441	Kelas 9 F	TIK	asd	asdas	1	rejected	\N	\N	adsdfsdf	2026-06-08 20:06:52	2026-06-08 20:14:08
73	8506bf82-0469-47e1-89fd-9f343e7f348c	28	3	2	\N	3	2026-06-10	Bang Ucup	6281217874441	X PK 1	tik	asdasd	adasd	3	rejected	\N	\N	adfasdff	2026-06-08 20:06:01	2026-06-08 20:14:15
72	8506bf82-0469-47e1-89fd-9f343e7f348c	28	3	1	\N	3	2026-06-10	Bang Ucup	6281217874441	X PK 1	tik	asdasd	adasd	3	rejected	\N	\N	sdafdfad	2026-06-08 20:06:01	2026-06-08 20:14:22
69	20ac0b3c-2a7b-42f0-8436-29c6990b74d0	28	3	3	\N	1	2026-06-11	Bang Ucup	6281217874441	Kelas XII IPA 3	bahasa inggris	asdd	sdf	2	rejected	\N	\N	asdfasdfsdf	2026-06-08 19:49:40	2026-06-08 20:14:29
71	20ac0b3c-2a7b-42f0-8436-29c6990b74d0	28	3	5	\N	1	2026-06-11	Bang Ucup	6281217874441	Kelas XII IPA 3	bahasa inggris	asdd	sdf	2	rejected	\N	\N	dasfasdf	2026-06-08 19:49:40	2026-06-08 20:14:35
68	f17ad1fa-5ed4-4274-9519-a5637507fba7	28	3	2	\N	1	2026-06-11	Bang Ucup	6281217874441	Kelas XII IPS 1	TIK	dfhdfhdfh	dfd	1	rejected	\N	\N	dsafasdfsd	2026-06-08 06:54:32	2026-06-08 20:14:42
70	20ac0b3c-2a7b-42f0-8436-29c6990b74d0	28	3	4	\N	1	2026-06-11	Bang Ucup	6281217874441	Kelas XII IPA 3	bahasa inggris	asdd	sdf	2	rejected	\N	\N	dsafsadf	2026-06-08 19:49:40	2026-06-08 20:14:49
67	52c605fb-80f9-4d48-8cb7-41c1ea8a3c4f	28	3	1	\N	1	2026-06-11	Bang Ucup	6281217874441	Kelas XII IPS 2	TIK	asdfaaf	adsfasd	4	rejected	\N	\N	asdfasf	2026-06-08 06:43:07	2026-06-08 20:14:56
84	04d270b6-b274-4393-af6a-23f466c3aea0	28	3	4	\N	1	2026-06-09	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	asdasds	sdas	1	rejected	\N	\N	dasdasd	2026-06-08 20:32:20	2026-06-08 20:42:39
81	04d270b6-b274-4393-af6a-23f466c3aea0	28	3	1	\N	1	2026-06-09	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	asdasds	sdas	1	rejected	\N	\N	asdsa	2026-06-08 20:32:20	2026-06-08 20:43:11
85	04d270b6-b274-4393-af6a-23f466c3aea0	28	3	5	\N	1	2026-06-09	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	asdasds	sdas	1	rejected	\N	\N	asdas	2026-06-08 20:32:20	2026-06-08 20:43:45
91	20973855-9f16-43da-b00d-3f96555b4e3c	28	3	4	\N	1	2026-06-10	Bang Ucup	6281217874441	Kelas XII IPA 3	Brainly	ada apa	\N	9	rejected	\N	\N	,kjgjkhgk	2026-06-09 20:49:40	2026-06-09 20:50:37
89	20973855-9f16-43da-b00d-3f96555b4e3c	28	3	2	\N	1	2026-06-10	Bang Ucup	6281217874441	Kelas XII IPA 3	Brainly	ada apa	\N	9	rejected	\N	\N	rfddf	2026-06-09 20:49:40	2026-06-09 20:50:52
88	20973855-9f16-43da-b00d-3f96555b4e3c	28	3	1	\N	1	2026-06-10	Bang Ucup	6281217874441	Kelas XII IPA 3	Brainly	ada apa	\N	9	rejected	\N	\N	fddff	2026-06-09 20:49:40	2026-06-09 20:51:05
90	20973855-9f16-43da-b00d-3f96555b4e3c	28	3	3	\N	1	2026-06-10	Bang Ucup	6281217874441	Kelas XII IPA 3	Brainly	ada apa	\N	9	rejected	\N	\N	xcvcvcvbvbvb	2026-06-09 20:49:40	2026-06-09 20:51:33
92	20973855-9f16-43da-b00d-3f96555b4e3c	28	3	5	\N	1	2026-06-10	Bang Ucup	6281217874441	Kelas XII IPA 3	Brainly	ada apa	\N	9	rejected	\N	\N	zxzxzx	2026-06-09 20:49:40	2026-06-09 20:51:42
87	3d4327f7-032c-4fec-b4de-7192730cacc1	28	3	2	\N	2	2026-06-09	Bang Ucup	6281217874441	Kelas 9 F	TIK	asdasd	as	2	rejected	\N	\N	xcxcxcxcxc	2026-06-08 20:44:32	2026-06-09 20:51:49
86	3d4327f7-032c-4fec-b4de-7192730cacc1	28	3	1	\N	2	2026-06-09	Bang Ucup	6281217874441	Kelas 9 F	TIK	asdasd	as	2	rejected	\N	\N	xffxx	2026-06-08 20:44:31	2026-06-09 20:51:56
93	8606fa18-bcf0-47d9-9ced-74a110d06566	28	3	1	\N	2	2026-06-10	Bang Ucup	6281217874441	Kelas 9 C	Brainly	hvjkh	\N	9	rejected	\N	\N	likhlihlkih	2026-06-09 20:52:47	2026-06-09 20:53:44
95	1de9ba2f-a953-46cf-8aea-6b4c32a72bee	28	3	1	\N	1	2026-06-10	Bang Ucup	6281217874441	Kelas XII IPS 1	Brainly	adakah	djdb	3	rejected	\N	\N	bnmbnm	2026-06-09 20:59:03	2026-06-09 20:59:44
94	d8e8d1b4-a136-4301-a47c-7863031fe504	28	3	1	\N	2	2026-06-11	Bang Ucup	6281217874441	Kelas 8 H	Brainly	hgkv	high	6	rejected	\N	\N	mbmnvb	2026-06-09 20:54:41	2026-06-09 20:59:51
100	4cdf558e-4899-4371-9199-bf00d374c8da	28	3	5	\N	2	2026-06-17	Bang Ucup	6281217874441	Kelas 9 F	bahasa inggris	sdasasassassa	\N	3	rejected	\N	\N	dasdas	2026-06-16 08:37:40	2026-06-17 21:53:20
96	4cdf558e-4899-4371-9199-bf00d374c8da	28	3	1	\N	2	2026-06-17	Bang Ucup	6281217874441	Kelas 9 F	bahasa inggris	sdasasassassa	\N	3	rejected	\N	\N	asdfasdfadf	2026-06-16 08:37:40	2026-06-17 21:58:11
97	4cdf558e-4899-4371-9199-bf00d374c8da	28	3	2	\N	2	2026-06-17	Bang Ucup	6281217874441	Kelas 9 F	bahasa inggris	sdasasassassa	\N	3	rejected	\N	\N	dfafdasf	2026-06-16 08:37:40	2026-06-17 21:58:58
98	4cdf558e-4899-4371-9199-bf00d374c8da	28	3	3	\N	2	2026-06-17	Bang Ucup	6281217874441	Kelas 9 F	bahasa inggris	sdasasassassa	\N	3	rejected	\N	\N	adfadf	2026-06-16 08:37:40	2026-06-17 21:59:12
99	4cdf558e-4899-4371-9199-bf00d374c8da	28	3	4	\N	2	2026-06-17	Bang Ucup	6281217874441	Kelas 9 F	bahasa inggris	sdasasassassa	\N	3	rejected	\N	\N	dfadfd	2026-06-16 08:37:40	2026-06-17 21:59:20
101	48ef0a94-cfb8-4c9e-a0a4-9632abf7274f	28	3	1	\N	1	2026-06-18	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	asdasd	sdas	2	approved	1	2026-06-17 21:59:38	\N	2026-06-17 21:44:28	2026-06-17 21:59:38
102	48ef0a94-cfb8-4c9e-a0a4-9632abf7274f	28	3	2	\N	1	2026-06-18	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	asdasd	sdas	2	approved	1	2026-06-17 21:59:38	\N	2026-06-17 21:44:28	2026-06-17 21:59:38
103	48ef0a94-cfb8-4c9e-a0a4-9632abf7274f	28	3	3	\N	1	2026-06-18	Bang Ucup	6281217874441	Kelas XII IPA 1	TIK	asdasd	sdas	2	approved	1	2026-06-17 21:59:38	\N	2026-06-17 21:44:28	2026-06-17 21:59:38
106	\N	\N	1	1	\N	1	2026-06-18	Test Guru	08123456789	10 IPA 1	Matematika	Praktikum Algoritma	\N	0	rejected	\N	\N	jkgjgk	2026-06-17 22:56:41	2026-06-17 22:59:28
107	5bb2980d-94c8-41f2-8278-35a966dbcb52	28	3	1	\N	1	2026-06-20	Bang Ucup	6281217874441	Kelas XII IPS 1	Brainly	ada aja	aa	65	approved	1	2026-06-17 23:01:07	\N	2026-06-17 23:00:41	2026-06-17 23:01:07
108	ce28e842-63ad-4c1e-8768-5e622eba6146	28	3	2	\N	3	2026-06-20	Bang Ucup	6281217874441	X PK 1	tik	asdasd	asdasdasd	21	approved	1	2026-06-18 13:27:16	\N	2026-06-17 23:04:06	2026-06-18 13:27:16
109	90c7fa0d-e38a-47ba-946c-78715c73f405	28	3	3	\N	1	2026-06-20	Bang Ucup	6281217874441	Kelas XII IPA 3	ada	asdas	asdasd	3	approved	1	2026-06-18 13:27:16	\N	2026-06-17 23:42:33	2026-06-18 13:27:16
110	896ed65a-8c7a-4e43-8698-34f474f00a57	28	3	4	\N	2	2026-06-20	Bang Ucup	6281217874441	Kelas 9 G	TIK	asdasdasd	asdasdasdsd	3	approved	1	2026-06-18 13:27:16	\N	2026-06-18 13:25:52	2026-06-18 13:27:16
111	7d799cf1-7fa3-4033-80d8-e0f056ea2d85	28	3	7	\N	1	2026-06-20	Bang Ucup	6281217874441	Kelas XII IPS 2	TIK	ada	\N	2	approved	1	2026-06-18 13:36:03	\N	2026-06-18 13:35:25	2026-06-18 13:36:03
112	d6e14f28-086a-430f-95ee-f69ddf0234ee	28	3	5	\N	2	2026-06-20	Bang Ucup	6281217574441	Kelas 9 G	tik	as	sa	2	approved	1	2026-06-18 13:54:17	\N	2026-06-18 13:45:11	2026-06-18 13:54:17
113	c2bc6155-86b1-4c89-befb-42a743608a40	28	3	8	\N	1	2026-06-20	Bang Ucup	6281217574441	Kelas XII IPS 1	Brainly	adakah	\N	36	rejected	\N	\N	jhkjh	2026-06-18 14:02:50	2026-06-18 14:03:51
114	c2bc6155-86b1-4c89-befb-42a743608a40	28	3	9	\N	1	2026-06-20	Bang Ucup	6281217574441	Kelas XII IPS 1	Brainly	adakah	\N	36	rejected	\N	\N	jhkjh	2026-06-18 14:02:50	2026-06-18 14:03:51
115	c2bc6155-86b1-4c89-befb-42a743608a40	28	3	10	\N	1	2026-06-20	Bang Ucup	6281217574441	Kelas XII IPS 1	Brainly	adakah	\N	36	rejected	\N	\N	jhkjh	2026-06-18 14:02:50	2026-06-18 14:03:51
\.


--
-- Data for Name: classes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.classes (id, name, pin, organization_id, grade_level, major, student_count, academic_year, semester, metadata, is_active, created_at, updated_at, deleted_at) FROM stdin;
1	Kelas 8 A	668268	2	VIII		0	2026/2027		\N	t	2026-01-13 03:48:19	2026-05-18 07:01:25	\N
2	Kelas 8 B	545151	2	VIII		0	2026/2027		\N	t	2026-01-13 03:48:44	2026-05-18 07:01:25	\N
3	Kelas 8 C	121357	2	VIII		0	2026/2027		\N	t	2026-01-13 03:49:00	2026-05-18 07:01:25	\N
4	Kelas 8 D	617570	2	VIII		0	2026/2027		\N	t	2026-01-13 03:50:20	2026-05-18 07:01:25	\N
5	Kelas 8 E	187472	2	VIII		0	2026/2027		\N	t	2026-01-13 03:50:33	2026-05-18 07:01:25	\N
6	Kelas 8 F	616552	2	VIII		0	2026/2027		\N	t	2026-01-13 03:50:55	2026-05-18 07:01:25	\N
7	Kelas 8 G	792691	2	VIII		0	2026/2027		\N	t	2026-01-13 03:52:36	2026-05-18 07:01:25	\N
8	Kelas 8 H	014757	2	VIII		0	2026/2027		\N	t	2026-01-13 03:53:31	2026-05-18 07:01:25	\N
9	Kelas 8 I	065917	2	VIII		0	2026/2027		\N	t	2026-01-13 03:53:48	2026-05-18 07:01:25	\N
10	Kelas 9 A	942042	2	IX		0	2026/2027		\N	t	2026-01-13 03:54:08	2026-05-18 07:01:25	\N
11	Kelas 9 B	419269	2	IX		0	2026/2027		\N	t	2026-01-13 03:54:27	2026-05-18 07:01:25	\N
12	Kelas 9 C	225511	2	IX		0	2026/2027		\N	t	2026-01-13 03:54:49	2026-05-18 07:01:25	\N
13	Kelas 9 D	065476	2	IX		0	2026/2027		\N	t	2026-01-13 03:55:08	2026-05-18 07:01:25	\N
14	Kelas 9 E	112656	2	IX		0	2026/2027		\N	t	2026-01-13 03:55:26	2026-05-18 07:01:25	\N
15	Kelas 9 F	919582	2	IX		0	2026/2027		\N	t	2026-01-13 03:55:37	2026-05-18 07:01:25	\N
16	Kelas 9 G	698955	2	IX		0	2026/2027		\N	t	2026-01-13 03:56:01	2026-05-18 07:01:25	\N
17	Kelas 9 H	443523	2	IX		0	2026/2027		\N	t	2026-01-13 03:56:28	2026-05-18 07:01:25	\N
18	Kelas 9 I	007426	2	IX		0	2026/2027		\N	t	2026-01-13 03:56:41	2026-05-18 07:01:26	\N
19	Kelas XII IPA 1	838475	1	XII	IPA	0	2026/2027		\N	t	2026-01-13 04:00:07	2026-05-18 07:01:26	\N
20	Kelas XII IPA 2	051685	1	XII	IPA	0	2026/2027		\N	t	2026-01-13 04:00:36	2026-05-18 07:01:26	\N
21	Kelas XII IPA 3	003888	1	XII	IPA	0	2026/2027		\N	t	2026-01-13 04:01:00	2026-05-18 07:01:26	\N
22	Kelas XII IPS 1	461550	1	XII	IPS	0	2026/2027		\N	t	2026-01-13 04:01:27	2026-05-18 07:01:26	\N
23	Kelas XII IPS 2	252591	1	XII	IPS	0	2026/2027		\N	t	2026-01-13 04:01:52	2026-05-18 07:01:26	\N
24	Kelas EXC	174046	5			0	2026/2027		\N	t	2026-01-15 14:10:11	2026-05-18 07:01:26	\N
25	X PK 1	285078	3	X	PK	0	2026/2027	\N	\N	t	2026-05-12 14:32:25	2026-05-18 07:01:26	\N
26	X - 1	134887	1	X	\N	0	2026/2027	\N	\N	t	2026-05-18 22:45:06	2026-05-18 22:45:06	\N
\.


--
-- Data for Name: holidays; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.holidays (id, date, name, type, description, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: important_schedules; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.important_schedules (id, resource_id, title, type, date, is_full_day, start_slot_id, end_slot_id, description, color, created_by, created_at, updated_at, start_date, end_date) FROM stdin;
2	3	ada deh	event	2026-06-19	t	\N	\N	fasdfad	#EF4444	1	2026-06-17 22:16:09	2026-06-17 22:16:09	\N	\N
\.


--
-- Data for Name: inventory_maintenance_logs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.inventory_maintenance_logs (id, lab_inventory_id, user_id, maintenance_date, maintenance_type, description, cost, status, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: lab_inventory; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.lab_inventory (id, resource_id, item_name, category, brand, model, serial_number, specifications, condition, status, quantity, quantity_good, quantity_broken, quantity_backup, notes, created_by, updated_by, created_at, updated_at, deleted_at) FROM stdin;
1	1	Smartboard	computer	HISENSE	android	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-01-28 14:10:59	2026-02-04 04:48:14	\N
2	2	mikrotik x86	network	mikrotik	x86	\N	SSD 64 GB	good	active	1	1	0	0	\N	\N	\N	2026-02-01 14:49:08	2026-02-02 12:48:36	\N
3	1	switch hub	network	D link	\N	\N	24 port	good	active	2	2	0	0	\N	\N	\N	2026-02-02 02:30:37	2026-02-02 12:48:36	\N
4	2	switch hub	network	D link	\N	\N	24 port	good	active	2	2	0	0	\N	\N	\N	2026-02-02 02:56:06	2026-02-02 12:48:36	\N
6	2	air conditioner	furniture	midea	\N	\N	1,5 pk	good	active	1	1	0	0	\N	\N	\N	2026-02-02 05:08:21	2026-02-04 04:45:47	\N
9	1	Lemari Etalase	furniture	\N	\N	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-02-02 13:14:49	2026-02-02 13:14:49	\N
10	2	Lemari Etalase	furniture	\N	\N	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-02-02 13:15:16	2026-02-02 13:15:16	\N
11	1	Monitor	peripheral	Pixel	\N	\N	\N	good	active	23	20	3	0	\N	\N	\N	2026-02-02 13:20:15	2026-02-02 13:20:15	\N
13	1	air conditioner	furniture	midea	\N	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-02-04 04:53:34	2026-02-04 04:53:34	\N
15	1	keyboard	peripheral	\N	\N	\N	\N	good	active	30	20	10	0	\N	\N	\N	2026-02-04 06:03:23	2026-02-04 06:03:23	\N
16	1	mouse	peripheral	\N	\N	\N	\N	good	active	30	19	11	0	\N	\N	\N	2026-02-04 06:05:09	2026-02-04 06:05:09	\N
18	1	meja komputer	furniture	\N	\N	\N	\N	good	active	30	25	5	0	\N	\N	\N	2026-02-04 06:10:02	2026-02-04 06:10:02	\N
19	1	meja guru	furniture	\N	\N	\N	\N	good	active	2	2	0	0	\N	\N	\N	2026-02-04 06:11:42	2026-02-04 06:11:42	\N
20	1	kursi kayu	furniture	\N	\N	\N	\N	good	active	7	7	0	0	\N	\N	\N	2026-02-04 06:12:44	2026-02-04 06:12:44	\N
21	1	kursi plastik	furniture	\N	\N	\N	\N	good	active	21	19	2	0	\N	\N	\N	2026-02-04 06:14:15	2026-02-04 06:14:15	\N
22	1	kabel LAN	network	\N	\N	\N	\N	good	active	30	29	1	0	\N	\N	\N	2026-02-04 06:16:18	2026-02-04 06:16:18	\N
23	1	lampu	furniture	\N	\N	\N	\N	good	active	6	4	2	0	\N	\N	\N	2026-02-04 06:18:27	2026-02-04 06:18:27	\N
24	1	papan tulis	furniture	\N	\N	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-02-04 06:19:16	2026-02-04 06:19:16	\N
25	2	air conditioner	furniture	sharp	\N	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-02-04 06:29:40	2026-02-04 06:29:40	\N
26	2	keyboard	peripheral	\N	\N	\N	\N	good	active	27	27	0	0	\N	\N	\N	2026-02-04 06:35:53	2026-02-04 06:35:53	\N
27	2	mouse	peripheral	\N	\N	\N	\N	good	active	27	22	5	0	\N	\N	\N	2026-02-04 06:37:32	2026-02-04 06:37:32	\N
29	2	kabel LAN	network	\N	\N	\N	\N	good	active	33	33	0	0	\N	\N	\N	2026-02-04 06:40:29	2026-02-04 06:40:29	\N
30	2	meja komputer	furniture	\N	\N	\N	\N	good	active	30	30	0	0	\N	\N	\N	2026-02-04 06:41:34	2026-02-04 06:41:34	\N
31	2	meja guru	furniture	\N	\N	\N	\N	good	active	2	2	0	0	\N	\N	\N	2026-02-04 06:42:16	2026-02-04 06:42:16	\N
32	2	kursi kayu	furniture	\N	\N	\N	\N	good	active	20	20	0	0	\N	\N	\N	2026-02-04 06:43:13	2026-02-04 06:43:13	\N
33	2	kursi plastik	furniture	\N	\N	\N	\N	good	active	12	11	1	0	\N	\N	\N	2026-02-04 06:44:06	2026-02-04 06:44:06	\N
34	2	papan tulis	furniture	\N	\N	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-02-04 06:44:30	2026-02-04 06:44:30	\N
35	2	lampu	furniture	\N	\N	\N	\N	good	active	6	5	1	0	\N	\N	\N	2026-02-04 06:45:37	2026-02-04 06:45:37	\N
36	2	modem wifi	network	\N	\N	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-02-04 06:46:15	2026-02-04 06:46:15	\N
37	2	pc server	computer	\N	\N	\N	\N	good	active	1	1	0	0	\N	\N	\N	2026-02-05 01:00:21	2026-02-05 01:00:21	\N
38	6	Komputer	computer	\N	\N	\N	\N	good	active	18	8	10	0	\N	\N	\N	2026-02-06 04:16:28	2026-02-06 04:16:28	\N
8	2	PC	computer	venom	\N	\N	\N	poor	active	30	24	6	0	\N	\N	1	2026-02-02 13:13:48	2026-07-03 10:59:39	\N
12	2	Monitor	peripheral	\N	\N	\N	\N	good	active	30	26	4	0	\N	\N	1	2026-02-02 13:21:01	2026-07-03 11:01:57	\N
7	1	PC	computer	venom	\N	\N	\N	good	active	21	19	2	0	\N	\N	1	2026-02-02 13:12:35	2026-07-03 11:03:16	\N
5	1	air conditioner	furniture	crystal	\N	\N	1 pk	good	active	1	1	0	0	tidak dingin tidak ada remote	\N	1	2026-02-02 05:04:38	2026-07-03 11:04:01	\N
14	1	monitor	peripheral	\N	\N	\N	\N	fair	active	26	20	6	0	\N	\N	1	2026-02-04 06:00:41	2026-07-03 11:06:16	\N
17	1	stopkontak	peripheral	\N	\N	\N	\N	good	active	30	29	1	0	\N	\N	1	2026-02-04 06:07:16	2026-07-03 22:45:18	\N
28	2	stopkontak	peripheral	\N	\N	\N	\N	good	active	30	29	1	0	\N	\N	1	2026-02-04 06:39:51	2026-07-04 06:27:04	\N
\.


--
-- Data for Name: lab_inventory_history; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.lab_inventory_history (id, inventory_id, action_type, old_condition, new_condition, old_status, new_status, old_quantity, new_quantity, description, cost, performed_by, performed_at) FROM stdin;
1	1	add	\N	good	\N	active	\N	\N	Item inventaris baru ditambahkan: Smartboard	\N	\N	2026-01-28 14:10:59
2	2	add	\N	good	\N	active	\N	\N	Item inventaris baru ditambahkan: mikrotik x86	\N	\N	2026-02-01 14:49:08
3	3	add	\N	good	\N	active	\N	\N	Item inventaris baru ditambahkan: switch hub 24 port	\N	\N	2026-02-02 02:30:37
4	4	add	\N	good	\N	active	\N	2	Item inventaris baru ditambahkan: switch hub 24 port (Jumlah: 2 unit)	\N	\N	2026-02-02 02:56:06
5	5	add	\N	good	\N	active	\N	2	Item inventaris baru ditambahkan: ac 1,5 pk (Jumlah: 2 unit)	\N	\N	2026-02-02 05:04:38
6	6	add	\N	good	\N	active	\N	1	Item inventaris baru ditambahkan: air conditioner (Jumlah: 1 unit)	\N	\N	2026-02-02 05:08:21
\.


--
-- Data for Name: lab_sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.lab_sessions (id, token, lab_key, resource_id, source_type, source_id, teacher_name, teacher_phone, session_start, session_end, used_at, invalidated_at, is_active, invalidated_reason, created_at, updated_at) FROM stdin;
1	FPJY-VUGH	lab7	1	manual	\N	Test Guru	08123456789	2026-02-25 22:57:38	2026-02-26 00:57:38	2026-02-25 22:58:43	2026-02-26 01:00:01	f	expired	2026-02-25 22:57:38	2026-02-26 01:00:01
2	UNLF-ZKFJ	lab8	2	booking	8	bang ucup	\N	2026-02-26 08:45:00	2026-02-26 09:20:00	\N	2026-02-26 09:20:01	f	expired	2026-02-26 00:51:37	2026-02-26 09:20:01
3	RFKE-50FY	lab8	2	booking	7	bang ucup	\N	2026-02-26 08:10:00	2026-02-26 08:45:00	\N	2026-02-26 08:45:01	f	expired	2026-02-26 00:51:39	2026-02-26 08:45:01
4	XDTE-HGSA	lab7	1	manual	\N	Test Guru	08123456789	2026-02-26 03:31:30	2026-02-26 05:31:30	2026-02-26 03:32:06	2026-02-26 05:35:01	f	expired	2026-02-26 03:31:30	2026-02-26 05:35:01
5	PFFJ-43WA	lab7	1	manual	\N	Test Guru	08123456789	2026-02-26 04:24:34	2026-02-26 06:24:34	2026-02-26 04:25:16	2026-02-26 06:25:01	f	expired	2026-02-26 04:24:34	2026-02-26 06:25:01
6	LRL2-BUZZ	lab7	1	manual	\N	Test Guru	\N	2026-02-26 12:13:46	2026-02-26 14:13:46	2026-02-26 12:14:50	2026-02-26 14:15:01	f	expired	2026-02-26 12:13:46	2026-02-26 14:15:01
7	ZQEL-A7AT	lab7	1	schedule	1	Bu Husnul	6281217574441	2026-02-26 11:35:00	2026-02-26 12:10:00	\N	2026-02-26 13:50:02	f	expired	2026-02-26 13:48:07	2026-02-26 13:50:02
8	5CDE-SUQA	lab7	1	schedule	1	Bu Husnul	6281217574441	2026-02-26 11:35:00	2026-02-26 12:10:00	\N	2026-02-26 13:55:02	f	expired	2026-02-26 13:51:53	2026-02-26 13:55:02
9	DBER-K2E4	lab7	1	booking	9	Bang Ucup	\N	2026-02-27 08:45:00	2026-02-27 09:20:00	\N	2026-02-27 09:20:01	f	expired	2026-02-27 01:54:35	2026-02-27 09:20:01
10	F4DY-WYUT	lab7	1	booking	10	Bang Ucup	\N	2026-02-27 09:20:00	2026-02-27 09:55:00	\N	2026-02-27 09:55:01	f	expired	2026-02-27 01:57:12	2026-02-27 09:55:01
11	KR8M-9VUC	lab8	2	booking	11	Bang Ucup	6281217574441	2026-02-27 11:00:00	2026-02-27 11:35:00	\N	2026-02-27 11:35:01	f	expired	2026-02-27 02:17:43	2026-02-27 11:35:01
12	XTQO-7CPT	lab1	3	booking	12	Alip	6283112088830	2026-02-27 11:00:00	2026-02-27 11:35:00	\N	2026-02-27 11:35:01	f	expired	2026-02-27 06:26:27	2026-02-27 11:35:01
13	UQFP-BPDU	lab1	3	manual	\N	Test Guru	6281217574441	2026-02-27 06:40:38	2026-02-27 07:40:38	2026-02-27 06:40:58	2026-02-27 07:45:01	f	expired	2026-02-27 06:40:38	2026-02-27 07:45:01
14	RG4S-WSBB	lab7	1	manual	\N	Test Guru	6281217574441	2026-02-27 06:50:38	2026-02-27 07:50:38	2026-02-27 06:50:50	2026-02-27 07:55:02	f	expired	2026-02-27 06:50:38	2026-02-27 07:55:02
15	MFLP-GDSD	lab8	2	schedule	28	Bu Husnul	6281217574441	2026-02-27 07:00:00	2026-02-27 07:35:00	\N	2026-02-27 07:35:02	f	expired	2026-02-27 06:53:01	2026-02-27 07:35:02
16	RZH7-ZBPX	lab1	3	manual	\N	Test Guru	6281217574441	2026-02-27 07:15:16	2026-02-27 08:15:16	2026-02-27 07:15:28	2026-02-27 08:20:02	f	expired	2026-02-27 07:15:16	2026-02-27 08:20:02
17	NBDV-XNUX	lab7	1	manual	\N	Test Guru	6281217574441	2026-02-27 07:16:03	2026-02-27 08:16:03	2026-02-27 07:16:14	2026-02-27 08:20:02	f	expired	2026-02-27 07:16:03	2026-02-27 08:20:02
18	BYIA-WZFG	lab7	1	manual	\N	Test Guru	6281217574441	2026-02-27 07:24:04	2026-02-27 08:24:04	2026-02-27 07:24:21	2026-02-27 08:25:01	f	expired	2026-02-27 07:24:04	2026-02-27 08:25:01
19	K0QB-OKP7	lab8	2	schedule	29	Bu Husnul	6281217574441	2026-02-27 07:35:00	2026-02-27 08:10:00	2026-02-27 07:30:18	2026-02-27 08:10:01	f	expired	2026-02-27 07:28:01	2026-02-27 08:10:01
20	MZAN-2ZBE	lab8	2	schedule	42	Pak Adhi	\N	2026-02-27 09:20:00	2026-02-27 09:55:00	\N	2026-02-27 09:55:01	f	expired	2026-02-27 09:13:01	2026-02-27 09:55:01
21	XP43-JVY1	lab8	2	schedule	43	Pak Adhi	\N	2026-02-27 10:25:00	2026-02-27 11:00:00	\N	2026-02-27 11:00:01	f	expired	2026-02-27 10:18:02	2026-02-27 11:00:01
22	RWTE-EHJD	lab7	1	manual	\N	Test Guru	6281217574441	2026-02-27 12:02:23	2026-02-27 13:02:23	2026-02-27 12:02:30	2026-02-27 19:10:01	f	expired	2026-02-27 12:02:23	2026-02-27 19:10:01
23	Z2RM-UMSZ	lab7	1	manual	\N	Test Guru	6281217574441	2026-02-27 19:26:23	2026-02-27 20:26:23	2026-02-27 19:26:31	2026-02-27 20:30:01	f	expired	2026-02-27 19:26:23	2026-02-27 20:30:01
24	DYIA-XK31	lab7	1	schedule	7	Tentor	\N	2026-02-28 07:00:00	2026-02-28 07:35:00	\N	2026-02-28 07:35:01	f	expired	2026-02-28 06:53:01	2026-02-28 07:35:01
25	E66F-OGOA	lab8	2	schedule	24	Tentor	\N	2026-02-28 07:00:00	2026-02-28 07:35:00	\N	2026-02-28 07:35:01	f	expired	2026-02-28 06:53:01	2026-02-28 07:35:01
26	GTNF-Q9IB	lab7	1	schedule	8	Tentor	\N	2026-02-28 07:35:00	2026-02-28 08:10:00	\N	2026-02-28 08:10:01	f	expired	2026-02-28 07:28:01	2026-02-28 08:10:01
27	T5MT-2SMU	lab8	2	schedule	25	Bu Husnul	6281217574441	2026-02-28 07:35:00	2026-02-28 08:10:00	2026-02-28 07:57:28	2026-02-28 08:10:01	f	expired	2026-02-28 07:28:01	2026-02-28 08:10:01
28	PIGY-9KMZ	lab7	1	schedule	9	Tentor	6281217574441	2026-02-28 08:10:00	2026-02-28 08:45:00	\N	2026-02-28 08:45:02	f	expired	2026-02-28 08:03:02	2026-02-28 08:45:02
29	PCBU-W46Z	lab8	2	schedule	27	Tentor	6281217574441	2026-02-28 08:10:00	2026-02-28 08:45:00	\N	2026-02-28 08:45:02	f	expired	2026-02-28 08:03:02	2026-02-28 08:45:02
30	TR0T-Y6TS	lab8	2	schedule	26	Tentor	6281217574441	2026-02-28 08:45:00	2026-02-28 09:20:00	\N	2026-02-28 09:20:02	f	expired	2026-02-28 08:38:01	2026-02-28 09:20:02
31	CEGL-KFDH	lab8	2	schedule	30	Bu Husnul	6281217574441	2026-02-28 10:25:00	2026-02-28 11:00:00	2026-02-28 10:50:55	2026-02-28 11:00:01	f	expired	2026-02-28 10:18:01	2026-02-28 11:00:01
32	JZYE-L4SY	lab8	2	schedule	31	Bu Husnul	6281217574441	2026-02-28 11:00:00	2026-02-28 11:35:00	2026-02-28 10:57:57	2026-02-28 11:35:01	f	expired	2026-02-28 10:53:01	2026-02-28 11:35:01
33	BNRY-K9ML	lab8	2	schedule	32	Bu Husnul	6281217574441	2026-02-28 11:35:00	2026-02-28 12:10:00	2026-02-28 11:38:05	2026-02-28 12:10:01	f	expired	2026-02-28 11:28:01	2026-02-28 12:10:01
34	OCHQ-E4CB	lab8	2	schedule	33	Bu Husnul	6281217574441	2026-02-28 12:10:00	2026-02-28 12:45:00	\N	2026-02-28 12:45:01	f	expired	2026-02-28 12:03:01	2026-02-28 12:45:01
35	IDOV-8ZCO	lab2	4	booking	14	Bu Husnul	6281217574441	2026-03-07 07:00:00	2026-03-07 12:45:00	\N	\N	t	\N	2026-02-28 12:35:06	2026-02-28 12:35:52
36	EKKF-YS4E	lab2	4	booking	23	Bu Husnul	6281217574441	2026-03-06 07:00:00	2026-03-06 12:45:00	\N	\N	t	\N	2026-02-28 18:59:22	2026-02-28 18:59:23
37	7QPU-JLEI	lab1	3	booking	32	Bu Husnul	6281217574441	2026-03-02 07:00:00	2026-03-02 12:45:00	\N	\N	t	\N	2026-02-28 19:20:59	2026-02-28 19:20:59
40	SDEE-OSD7	lab8	2	booking	54	Bang Ucup	6281217874441	2026-06-09 08:10:00	2026-06-09 09:55:00	\N	\N	t	\N	2026-06-07 21:54:33	2026-06-07 21:54:33
38	K54X-HGE8	lab7	1	booking	47	Bang Ucup	6281217874441	2026-06-05 07:00:00	2026-06-05 08:45:00	\N	\N	t	\N	2026-06-05 06:44:06	2026-06-05 06:44:06
39	2B4L-CAJD	lab1	3	booking	50	Bang Ucup	6281217874441	2026-06-08 07:00:00	2026-06-08 09:20:00	\N	\N	t	\N	2026-06-07 05:55:34	2026-06-07 05:55:34
43	PO7A-TVM7	lab1	3	booking	107	Bang Ucup	6281217874441	2026-06-20 07:00:00	2026-06-20 11:00:00	\N	\N	t	\N	2026-06-17 23:01:07	2026-06-18 13:36:03
41	OI4F-HRJA	lab1	3	booking	76	Bang Ucup	6281217874441	2026-06-09 07:00:00	2026-06-09 09:55:00	\N	\N	t	\N	2026-06-08 20:17:51	2026-06-08 20:17:51
42	XLTX-EMVF	lab1	3	booking	101	Bang Ucup	6281217874441	2026-06-18 07:00:00	2026-06-18 09:55:00	\N	\N	t	\N	2026-06-17 21:59:38	2026-06-17 21:59:39
\.


--
-- Data for Name: maintenance_records; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.maintenance_records (id, resource_id, type, title, description, scheduled_date, completed_date, technician, technician_phone, cost, status, notes, created_by, metadata, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2019_12_14_000001_create_personal_access_tokens_table	1
2	2026_02_18_225406_create_permission_tables	1
3	2026_02_25_000001_add_target_phones_to_wa_settings	2
4	2026_02_25_062107_create_teachers_table	3
5	2026_02_25_062137_add_teacher_id_to_schedules_table	3
6	2026_02_25_062147_add_teacher_id_to_bookings_table	3
7	2026_02_25_062157_create_assignments_table	3
8	2026_02_25_062208_create_assignment_submissions_table	3
9	2026_02_25_160622_create_lab_sessions_table	4
10	2026_02_27_204126_add_attachment_to_assignments_table	5
11	2026_02_27_212100_add_organization_id_to_assignments_table	6
12	2026_05_01_075148_create_sunday_bookings_table	7
13	2026_05_16_000001_add_session_id_to_bookings_table	8
14	2026_05_16_000001_add_composite_indexes	9
15	2026_05_18_000001_add_pin_to_classes_table	10
16	2026_05_18_203616_add_remember_token_to_users_table	11
17	2026_05_23_222531_fix_teachers_phone_unique_and_booking_indexes	12
18	2026_05_31_000001_create_important_schedules_table	13
19	2026_06_06_000000_add_start_end_date_to_important_schedules_table	14
20	2026_06_10_000001_create_inventory_maintenance_logs_table	15
21	2018_08_08_100000_create_telescope_entries_table	16
22	2026_06_20_000001_create_resource_user_table	17
23	2024_01_01_000026_add_weekly_quota_to_teachers_and_users	18
\.


--
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.model_has_permissions (permission_id, model_type, model_id) FROM stdin;
\.


--
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.model_has_roles (role_id, model_type, model_id) FROM stdin;
1	App\\\\Models\\\\User	1
2	App\\\\Models\\\\User	3
2	App\\\\Models\\\\User	5
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.notifications (id, user_id, type, title, message, action_url, reference_type, reference_id, priority, is_read, read_at, metadata, created_at) FROM stdin;
\.


--
-- Data for Name: organizations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.organizations (id, name, type, parent_id, address, phone, email, metadata, is_active, created_at, updated_at, deleted_at) FROM stdin;
1	SMA NURIS JEMBER	SMA	\N	\N	\N	\N	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51	\N
2	MTS UNGGULAN NURIS	MTS	\N	\N	\N	\N	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51	\N
3	MA UNGGULAN NURIS	MA	\N	\N	\N	\N	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51	\N
4	SMP NURIS	SMP	\N	\N	\N	\N	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51	\N
5	Ekstrakurikuler	EXCUL	\N	\N	\N	\N	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51	\N
6	SMK NURIS JEMBER	SMK	\N	\N	\N	\N	\N	t	2026-04-23 13:21:34	2026-04-23 13:21:34	\N
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.permissions (id, name, guard_name, created_at, updated_at) FROM stdin;
1	booking.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
2	booking.view	web	2026-02-20 13:59:27	2026-02-20 14:08:02
3	booking.create	web	2026-02-20 13:59:27	2026-02-20 14:08:02
4	booking.edit	web	2026-02-20 13:59:27	2026-02-20 14:08:02
5	booking.approve	web	2026-02-20 13:59:27	2026-02-20 14:08:02
6	booking.reject	web	2026-02-20 13:59:27	2026-02-20 14:08:02
7	booking.delete	web	2026-02-20 13:59:27	2026-02-20 14:08:02
8	schedule.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
9	schedule.view	web	2026-02-20 13:59:27	2026-02-20 14:08:02
10	schedule.create	web	2026-02-20 13:59:27	2026-02-20 14:08:02
11	schedule.edit	web	2026-02-20 13:59:27	2026-02-20 14:08:02
12	schedule.delete	web	2026-02-20 13:59:27	2026-02-20 14:08:02
13	inventory.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
14	inventory.view	web	2026-02-20 13:59:27	2026-02-20 14:08:02
15	inventory.create	web	2026-02-20 13:59:27	2026-02-20 14:08:02
16	inventory.edit	web	2026-02-20 13:59:27	2026-02-20 14:08:02
17	inventory.delete	web	2026-02-20 13:59:27	2026-02-20 14:08:02
18	maintenance.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
19	maintenance.view	web	2026-02-20 13:59:27	2026-02-20 14:08:02
20	maintenance.create	web	2026-02-20 13:59:27	2026-02-20 14:08:02
21	maintenance.edit	web	2026-02-20 13:59:27	2026-02-20 14:08:02
22	maintenance.delete	web	2026-02-20 13:59:27	2026-02-20 14:08:02
23	procurement.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
24	procurement.view	web	2026-02-20 13:59:27	2026-02-20 14:08:02
25	procurement.create	web	2026-02-20 13:59:27	2026-02-20 14:08:02
26	procurement.edit	web	2026-02-20 13:59:27	2026-02-20 14:08:02
27	procurement.approve	web	2026-02-20 13:59:27	2026-02-20 14:08:02
28	procurement.reject	web	2026-02-20 13:59:27	2026-02-20 14:08:02
29	procurement.delete	web	2026-02-20 13:59:27	2026-02-20 14:08:02
30	resource.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
31	resource.view	web	2026-02-20 13:59:27	2026-02-20 14:08:02
32	resource.create	web	2026-02-20 13:59:27	2026-02-20 14:08:02
33	resource.edit	web	2026-02-20 13:59:27	2026-02-20 14:08:02
34	resource.delete	web	2026-02-20 13:59:27	2026-02-20 14:08:02
35	report.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
36	report.view	web	2026-02-20 13:59:27	2026-02-20 14:08:02
37	report.export	web	2026-02-20 13:59:27	2026-02-20 14:08:02
38	user.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
39	user.create	web	2026-02-20 13:59:27	2026-02-20 14:08:02
40	user.edit	web	2026-02-20 13:59:27	2026-02-20 14:08:02
41	user.delete	web	2026-02-20 13:59:27	2026-02-20 14:08:02
42	class.viewAny	web	2026-02-20 13:59:27	2026-02-20 14:08:02
43	class.create	web	2026-02-20 13:59:27	2026-02-20 14:08:02
44	class.edit	web	2026-02-20 13:59:27	2026-02-20 14:08:02
45	class.delete	web	2026-02-20 13:59:27	2026-02-20 14:08:02
46	system.settings	web	2026-02-20 13:59:27	2026-02-20 14:08:02
47	system.logs	web	2026-02-20 13:59:27	2026-02-20 14:08:02
\.


--
-- Data for Name: procurement_requests; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.procurement_requests (id, lab_id, item_name, category, quantity, estimated_price, priority, justification, specifications, preferred_brand, notes, status, requested_by, requested_at, reviewed_by, reviewed_at, review_notes, completed_at, procurement_notes, created_at, updated_at) FROM stdin;
1	1	Komputer PC Desktop	computer	10	8000000.00	high	Komputer yang ada sudah tidak mendukung software terbaru dan sering hang. Diperlukan upgrade untuk mendukung kegiatan pembelajaran yang lebih efektif.	Processor: Intel Core i5 Gen 11 atau AMD Ryzen 5\\nRAM: 16GB DDR4\\nStorage: 512GB NVMe SSD\\nMonitor: 24 inch Full HD\\nGraphics: Integrated\\nOS: Windows 11 Pro	Dell Optiplex atau HP ProDesk	\N	pending	1	2026-02-01 15:07:28	\N	\N	\N	\N	\N	2026-02-01 15:07:28	2026-02-01 15:07:28
2	1	Mouse Wireless	peripheral	20	150000.00	low	Beberapa mouse sudah rusak dan perlu diganti. Mouse wireless akan lebih rapi dan mengurangi kabel yang berantakan.	Mouse wireless dengan baterai rechargeable\\nDPI adjustable\\nErgonomic design	Logitech	\N	approved	1	2026-01-29 15:07:28	1	2026-01-30 15:07:28	Disetujui. Segera lakukan proses procurement.	\N	\N	2026-01-29 15:07:28	2026-02-01 15:07:28
3	2	Switch Network 24 Port	network	2	3500000.00	medium	Untuk meningkatkan koneksi jaringan di Lab Komputer 8. Switch yang ada sudah penuh dan butuh ekspansi.	Gigabit Ethernet\\nManaged Switch\\n24 Port RJ45\\nRack mountable	TP-Link atau Cisco	\N	pending	1	2026-01-31 15:07:28	\N	\N	\N	\N	\N	2026-01-31 15:07:28	2026-01-31 15:07:28
4	1	Kursi Komputer Ergonomis	furniture	15	1200000.00	medium	Kursi yang ada sudah banyak yang rusak dan tidak ergonomis, menyebabkan siswa tidak nyaman saat praktikum.	Kursi ergonomis dengan sandaran punggung\\nTinggi adjustable\\nRoda berkualitas\\nMaterial breathable	IKEA atau Informa	\N	rejected	2	2026-01-27 15:07:28	1	2026-01-28 15:07:28	Ditolak karena anggaran tahun ini sudah habis. Bisa diajukan lagi tahun depan.	\N	\N	2026-01-27 15:07:28	2026-02-01 15:07:28
\.


--
-- Data for Name: resource_user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.resource_user (resource_id, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: resources; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.resources (id, name, type, parent_id, organization_id, building, floor, room_number, capacity, status, metadata, created_at, updated_at, deleted_at) FROM stdin;
1	Lab Komputer 7	lab	\N	\N	Gedung SMA	\N	\N	30	active	{\\"floor\\": 2, \\"room_number\\": \\"201\\", \\"pic_role\\": \\"teknisi\\", \\"pic_username\\": \\"teknisi1\\", \\"pic_name\\": \\"Teknisi Lab\\", \\"pic_phone\\": \\"08129876543\\"}	2026-01-13 02:19:51	2026-02-20 13:59:27	\N
2	Lab Komputer 8	lab	\N	\N	Gedung SMA	\N	\N	35	active	{\\"floor\\": 2, \\"room_number\\": \\"202\\", \\"pic_role\\": \\"teknisi\\", \\"pic_username\\": \\"teknisi1\\", \\"pic_name\\": \\"Teknisi Lab\\", \\"pic_phone\\": \\"08129876543\\"}	2026-01-13 02:19:51	2026-02-20 13:59:27	\N
3	Lab Komputer 1	lab	\N	\N	gedung lab	\N		25	active	\N	2026-02-05 04:59:15	2026-02-05 04:59:15	\N
4	Lab Komputer 2	lab	\N	\N	gedung lab	\N		25	active	\N	2026-02-05 05:00:42	2026-02-05 05:00:42	\N
5	Lab Komputer SMP	lab	\N	\N	Nuris 3	2		14	active	{\\"floor\\":2}	2026-02-05 05:04:29	2026-02-05 05:04:29	\N
6	Lab Komputer 3	lab	\N	\N	gedung lab SMK	\N		30	active	\N	2026-02-06 03:23:27	2026-02-06 03:23:27	\N
7	Lab Komputer 4	lab	\N	\N	gedung lab SMK	\N		30	active	\N	2026-02-06 03:25:17	2026-02-06 03:25:17	\N
8	Lab Fiber Optic	lab	\N	\N	gedung lab SMK	\N		\N	nonactive	\N	2026-02-06 03:26:32	2026-02-28 05:43:28	\N
\.


--
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
1	1
2	1
3	1
4	1
5	1
6	1
7	1
8	1
9	1
10	1
11	1
12	1
13	1
14	1
15	1
16	1
17	1
18	1
19	1
20	1
21	1
22	1
23	1
24	1
25	1
26	1
27	1
28	1
29	1
30	1
31	1
32	1
33	1
34	1
35	1
36	1
37	1
38	1
39	1
40	1
41	1
42	1
43	1
44	1
45	1
46	1
47	1
1	2
2	2
3	2
4	2
5	2
6	2
7	2
8	2
9	2
10	2
11	2
12	2
13	2
14	2
15	2
16	2
17	2
18	2
19	2
20	2
21	2
22	2
23	2
24	2
25	2
26	2
27	2
28	2
29	2
30	2
31	2
32	2
33	2
34	2
35	2
36	2
37	2
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.roles (id, name, guard_name, created_at, updated_at) FROM stdin;
1	admin	web	2026-02-20 13:59:27	2026-02-20 14:08:02
2	teknisi	web	2026-02-20 13:59:27	2026-02-20 14:08:02
\.


--
-- Data for Name: schedules; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.schedules (id, teacher_id, resource_id, time_slot_id, class_id, user_id, day_of_week, teacher_name, subject_name, notes, academic_year, semester, start_date, end_date, status, metadata, created_at, updated_at, deleted_at) FROM stdin;
1	1	1	9	2	\N	Monday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-13 04:32:11	2026-02-25 06:37:00	\N
2	1	1	10	2	\N	Monday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 13:56:47	2026-02-25 06:37:00	\N
3	1	1	1	14	\N	Wednesday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:01:09	2026-04-29 12:09:48	2026-04-29 12:09:48
4	1	1	2	14	\N	Wednesday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:01:36	2026-02-25 06:37:00	\N
5	1	1	1	5	\N	Thursday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:02:06	2026-02-25 06:37:00	\N
6	1	1	2	5	\N	Thursday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:02:26	2026-02-25 06:37:00	\N
7	2	1	1	24	\N	Saturday	Tentor	KIR		\N	\N	\N	\N	active	\N	2026-01-15 14:11:14	2026-02-25 06:37:00	\N
8	2	1	2	24	\N	Saturday	Tentor	KIR		\N	\N	\N	\N	active	\N	2026-01-15 14:12:51	2026-02-25 06:37:00	\N
9	2	1	3	24	\N	Saturday	Tentor	KIR		\N	\N	\N	\N	active	\N	2026-01-15 14:13:35	2026-02-25 06:37:00	\N
10	1	2	1	17	\N	Monday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:17:11	2026-02-25 06:37:00	\N
11	1	2	2	17	\N	Monday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:17:32	2026-02-25 06:37:00	\N
12	1	2	4	16	\N	Monday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:19:01	2026-02-25 06:37:00	\N
13	1	2	5	16	\N	Monday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:19:19	2026-02-25 06:37:00	\N
14	1	2	7	4	\N	Monday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:20:15	2026-02-25 06:37:00	\N
15	1	2	8	4	\N	Monday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:20:47	2026-02-25 06:37:00	\N
16	1	2	1	7	\N	Tuesday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:22:03	2026-02-25 06:37:00	\N
17	1	2	2	7	\N	Tuesday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:22:27	2026-02-25 06:37:00	\N
18	1	2	7	8	\N	Tuesday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:23:25	2026-02-25 06:37:00	\N
19	1	2	8	8	\N	Tuesday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:23:44	2026-02-25 06:37:00	\N
20	1	2	9	11	\N	Tuesday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:24:19	2026-02-25 06:37:00	\N
21	1	2	10	11	\N	Tuesday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:25:06	2026-02-25 06:37:00	\N
22	1	2	9	3	\N	Thursday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:28:21	2026-02-25 06:37:00	\N
23	1	2	10	3	\N	Thursday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:28:50	2026-02-25 06:37:00	\N
24	2	2	1	24	\N	Saturday	Tentor	KIR		\N	\N	\N	\N	active	\N	2026-01-15 14:32:19	2026-02-25 06:37:00	\N
25	1	2	2	24	\N	Saturday	Bu Husnul	KIR		\N	\N	\N	\N	active	\N	2026-01-15 14:32:42	2026-02-25 06:37:00	\N
26	2	2	4	24	\N	Saturday	Tentor	KIR		\N	\N	\N	\N	active	\N	2026-01-15 14:34:32	2026-02-25 06:37:00	\N
27	2	2	3	24	\N	Saturday	Tentor	KIR		\N	\N	\N	\N	active	\N	2026-01-15 14:34:53	2026-02-25 06:37:00	\N
28	1	2	1	6	\N	Friday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:35:38	2026-02-25 06:37:00	\N
29	1	2	2	6	\N	Friday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:35:57	2026-02-25 06:37:00	\N
30	1	2	7	13	\N	Saturday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:37:15	2026-02-25 06:37:00	\N
31	1	2	8	13	\N	Saturday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:37:37	2026-02-25 06:37:00	\N
32	1	2	9	18	\N	Saturday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:37:52	2026-02-25 06:37:00	\N
33	1	2	10	18	\N	Saturday	Bu Husnul	TIK		\N	\N	\N	\N	active	\N	2026-01-15 14:38:18	2026-02-25 06:37:00	\N
34	3	2	9	23	\N	Monday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:21:41	2026-04-29 12:39:27	2026-04-29 12:39:27
36	3	2	7	22	\N	Wednesday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:23:10	2026-02-25 06:37:00	\N
37	3	2	8	22	\N	Wednesday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:24:03	2026-02-25 06:37:00	\N
38	3	2	9	19	\N	Wednesday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:24:25	2026-02-25 06:37:00	\N
39	3	2	10	19	\N	Wednesday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:24:49	2026-02-25 06:37:00	\N
40	3	2	1	21	\N	Thursday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:25:28	2026-02-25 06:37:00	\N
41	3	2	2	21	\N	Thursday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:26:04	2026-02-25 06:37:00	\N
42	3	2	5	20	\N	Friday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:26:34	2026-02-25 06:37:00	\N
43	3	2	7	20	\N	Friday	Pak Adhi	TIK		\N	\N	\N	\N	active	\N	2026-01-21 13:27:28	2026-02-25 06:37:00	\N
44	\N	2	10	1	1	Monday	tes	TIK	\N	\N	\N	\N	\N	active	\N	2026-04-29 11:39:34	2026-04-29 12:37:50	2026-04-29 12:37:50
\.


--
-- Data for Name: sunday_bookings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sunday_bookings (id, teacher_id, resource_id, organization_id, approved_by, booking_date, teacher_name, teacher_phone, class_name, subject_name, title, description, participant_count, status, approved_at, notes, created_at, updated_at) FROM stdin;
1	1	2	2	\N	2026-05-03	Bang Ucup	6281217574441	Kelas 9 A	TIK	adas	\N	2	rejected	\N	asfsaf	2026-05-01 01:27:42	2026-05-02 12:07:48
2	28	3	2	1	2026-06-07	Bang Ucup	6281217874441	Kelas 9 C	TIK	dad	wifi	4	approved	2026-06-07 05:55:16	\N	2026-06-07 05:35:31	2026-06-07 05:55:16
\.


--
-- Data for Name: teachers; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.teachers (id, name, phone, token, is_active, created_at, updated_at, weekly_quota) FROM stdin;
2	Tentor	628883147162	GRU-002	t	2026-02-25 06:37:00	2026-05-12 03:07:16	5
3	Pak Adhi	6281217574123	GRU-003	t	2026-02-25 06:37:00	2026-02-28 00:58:58	5
9	Alip	6283112088830	GRU-005	t	2026-02-27 03:43:48	2026-02-26 20:43:48	5
10	Husnul	6285832553627	GRU-006	t	2026-04-27 01:16:25	2026-04-27 01:16:25	5
11	Bu Annisa	6282302454519	GRU-007	t	2026-05-05 01:09:26	2026-05-05 01:09:26	5
12	Nabila Fatmawati	6281235706327	GRU-008	t	2026-05-05 03:43:53	2026-05-05 03:43:53	5
15	Fiki Dwi Sembilan	6281357118518	GRU-011	t	2026-05-07 02:15:21	2026-05-07 02:15:21	5
16	Laili Bk	6285230779470	GRU-012	t	2026-05-07 09:22:59	2026-05-07 09:22:59	5
17	Kartika	6289526636020	GRU-013	t	2026-05-09 00:51:44	2026-05-09 00:51:44	5
19	Fitria Saqinah Damayanti	6281615400260	GRU-015	t	2026-05-12 01:15:21	2026-05-12 01:15:21	5
20	Atik Robbana	6289606805479	GRU-016	t	2026-05-15 00:48:08	2026-05-15 00:48:08	5
21	Nurma	6282329441631	GRU-017	t	2026-05-18 01:31:47	2026-05-18 01:31:47	5
23	Devita Wulansari	6284782576484	GRU-019	t	2026-05-18 01:37:54	2026-05-18 01:37:54	5
24	Dimas Abdur Rozaq	6282228299594	GRU-020	t	2026-05-18 01:54:28	2026-05-18 01:54:28	5
25	Iin	6285259384695	GRU-021	t	2026-05-19 01:57:03	2026-05-19 01:57:03	5
27	Rina Yuastri	6289607148707	GRU-023	t	2026-05-20 04:01:27	2026-05-20 04:01:27	5
1	Bu Husnul	6281217174441	GRU-001	t	2026-02-25 06:37:00	2026-02-26 13:25:34	5
28	Bang Ucup	6281217574441	GRU-024	t	2026-06-05 06:33:35	2026-06-05 06:33:35	5
29	Ikan	6282332671812	GRU-025	t	2026-07-02 11:18:43	2026-07-02 11:18:43	5
\.


--
-- Data for Name: telescope_monitoring; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.telescope_monitoring (tag) FROM stdin;
\.


--
-- Data for Name: time_slots; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.time_slots (id, name, start_time, end_time, day_of_week, slot_order, is_break, metadata, is_active, created_at, updated_at) FROM stdin;
1	Slot 1	07:00:00	07:35:00	\N	1	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
2	Slot 2	07:35:00	08:10:00	\N	2	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
3	Slot 3	08:10:00	08:45:00	\N	3	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
4	Slot 4	08:45:00	09:20:00	\N	4	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
5	Slot 5	09:20:00	09:55:00	\N	5	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
6	Istirahat	09:55:00	10:25:00	\N	6	t	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
7	Slot 6	10:25:00	11:00:00	\N	7	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
8	Slot 7	11:00:00	11:35:00	\N	8	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
9	Slot 8	11:35:00	12:10:00	\N	9	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
10	Slot 9	12:10:00	12:45:00	\N	10	f	\N	t	2026-01-13 02:19:51	2026-01-13 02:19:51
\.


--
-- Data for Name: user_sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_sessions (id, user_id, token, ip_address, user_agent, metadata, expires_at, created_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, username, email, password_hash, full_name, phone, role, organization_id, metadata, is_active, remember_token, email_verified_at, last_login_at, last_login_ip, failed_login_attempts, locked_until, created_at, updated_at, deleted_at, weekly_quota) FROM stdin;
1	admin	admin@labsystem.com	$2y$12$/PF5hhL/rbnYK.okF9A1wO0aGnv/rmRolOlDnvwX2AZEEPqIpdH/y	Administrator	08123456789	admin	\N	\N	t	\N	\N	\N	\N	0	\N	2026-01-13 02:19:51	2026-02-21 03:54:05	\N	5
2	operator1	operator1@labsystem.com	$2y$12$l7Oz/OC0e0Z29XmRyV8BM.g4I42EusOS7JoBQ1I2kYFI64AegOubK	Operator Lab	08123456790	operator	\N	\N	t	\N	\N	\N	\N	0	\N	2026-01-13 02:19:51	2026-02-21 03:54:06	\N	5
3	teknisi1	teknisi1@labsystem.com	$2y$12$ICiywQpi8K/aiJqMBkqesOzE.FYS0JYXy8fPqmd8nVYz/aH1bi3pG	Teknisi Lab	08129876543	teknisi	\N	{\\"allowed_resources\\":[3,4]}	t	\N	\N	\N	\N	0	\N	2026-02-20 13:59:27	2026-02-21 11:23:11	\N	5
5	teknisi2	teknisi2@labsystem.com	$2y$12$ICiywQpi8K/aiJqMBkqesOzE.FYS0JYXy8fPqmd8nVYz/aH1bi3pG	Teknisi Lab 2	08129876544	teknisi	\N	{\\"allowed_resources\\":[1,2]}	t	\N	\N	\N	\N	0	\N	2026-02-21 12:33:46	2026-02-21 12:33:46	\N	5
\.


--
-- Name: activity_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.activity_logs_id_seq', 74, false);


--
-- Name: assignment_submissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.assignment_submissions_id_seq', 2, true);


--
-- Name: assignments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.assignments_id_seq', 2, true);


--
-- Name: bookings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.bookings_id_seq', 118, true);


--
-- Name: classes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.classes_id_seq', 27, false);


--
-- Name: holidays_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.holidays_id_seq', 1, false);


--
-- Name: important_schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.important_schedules_id_seq', 2, true);


--
-- Name: inventory_maintenance_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.inventory_maintenance_logs_id_seq', 1, false);


--
-- Name: lab_inventory_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.lab_inventory_history_id_seq', 7, false);


--
-- Name: lab_inventory_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.lab_inventory_id_seq', 39, false);


--
-- Name: lab_sessions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.lab_sessions_id_seq', 43, true);


--
-- Name: maintenance_records_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.maintenance_records_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 23, true);


--
-- Name: notifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.notifications_id_seq', 1, false);


--
-- Name: organizations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.organizations_id_seq', 7, false);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.permissions_id_seq', 95, false);


--
-- Name: procurement_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.procurement_requests_id_seq', 5, false);


--
-- Name: resources_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.resources_id_seq', 9, false);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.roles_id_seq', 5, false);


--
-- Name: schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.schedules_id_seq', 45, false);


--
-- Name: sunday_bookings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.sunday_bookings_id_seq', 2, true);


--
-- Name: teachers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.teachers_id_seq', 30, true);


--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.telescope_entries_sequence_seq', 370905, true);


--
-- Name: time_slots_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.time_slots_id_seq', 11, false);


--
-- Name: user_sessions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_sessions_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 6, false);


--
-- Name: activity_logs activity_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_pkey PRIMARY KEY (id);


--
-- Name: assignment_submissions assignment_submissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assignment_submissions
    ADD CONSTRAINT assignment_submissions_pkey PRIMARY KEY (id);


--
-- Name: assignments assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_pkey PRIMARY KEY (id);


--
-- Name: bookings bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_pkey PRIMARY KEY (id);


--
-- Name: classes classes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classes
    ADD CONSTRAINT classes_pkey PRIMARY KEY (id);


--
-- Name: holidays holidays_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.holidays
    ADD CONSTRAINT holidays_pkey PRIMARY KEY (id);


--
-- Name: important_schedules important_schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_pkey PRIMARY KEY (id);


--
-- Name: inventory_maintenance_logs inventory_maintenance_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_maintenance_logs
    ADD CONSTRAINT inventory_maintenance_logs_pkey PRIMARY KEY (id);


--
-- Name: lab_inventory_history lab_inventory_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory_history
    ADD CONSTRAINT lab_inventory_history_pkey PRIMARY KEY (id);


--
-- Name: lab_inventory lab_inventory_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory
    ADD CONSTRAINT lab_inventory_pkey PRIMARY KEY (id);


--
-- Name: lab_sessions lab_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_sessions
    ADD CONSTRAINT lab_sessions_pkey PRIMARY KEY (id);


--
-- Name: maintenance_records maintenance_records_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_records
    ADD CONSTRAINT maintenance_records_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: organizations organizations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: procurement_requests procurement_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.procurement_requests
    ADD CONSTRAINT procurement_requests_pkey PRIMARY KEY (id);


--
-- Name: resource_user resource_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.resource_user
    ADD CONSTRAINT resource_user_pkey PRIMARY KEY (resource_id, user_id);


--
-- Name: resources resources_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.resources
    ADD CONSTRAINT resources_pkey PRIMARY KEY (id);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: schedules schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_pkey PRIMARY KEY (id);


--
-- Name: sunday_bookings sunday_bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_pkey PRIMARY KEY (id);


--
-- Name: teachers teachers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT teachers_pkey PRIMARY KEY (id);


--
-- Name: telescope_monitoring telescope_monitoring_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.telescope_monitoring
    ADD CONSTRAINT telescope_monitoring_pkey PRIMARY KEY (tag);


--
-- Name: time_slots time_slots_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.time_slots
    ADD CONSTRAINT time_slots_pkey PRIMARY KEY (id);


--
-- Name: user_sessions user_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_sessions
    ADD CONSTRAINT user_sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: assignment_submissions_assignment_id_foreign; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assignment_submissions_assignment_id_foreign ON public.assignment_submissions USING btree (assignment_id);


--
-- Name: assignments_teacher_id_foreign; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assignments_teacher_id_foreign ON public.assignments USING btree (teacher_id);


--
-- Name: bookings_session_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX bookings_session_id_index ON public.bookings USING btree (session_id);


--
-- Name: bookings_teacher_id_foreign; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX bookings_teacher_id_foreign ON public.bookings USING btree (teacher_id);


--
-- Name: classes_pin_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX classes_pin_unique ON public.classes USING btree (pin);


--
-- Name: fk_bookings_approved_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fk_bookings_approved_by ON public.bookings USING btree (approved_by);


--
-- Name: fk_bookings_organization; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fk_bookings_organization ON public.bookings USING btree (organization_id);


--
-- Name: fk_bookings_resource; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fk_bookings_resource ON public.bookings USING btree (resource_id);


--
-- Name: fk_bookings_time_slot; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fk_bookings_time_slot ON public.bookings USING btree (time_slot_id);


--
-- Name: fk_bookings_user; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fk_bookings_user ON public.bookings USING btree (user_id);


--
-- Name: fk_maintenance_created_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fk_maintenance_created_by ON public.maintenance_records USING btree (created_by);


--
-- Name: idx_action_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_action_type ON public.lab_inventory_history USING btree (action_type);


--
-- Name: idx_activity_logs_action; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_activity_logs_action ON public.activity_logs USING btree (action);


--
-- Name: idx_activity_logs_created; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_activity_logs_created ON public.activity_logs USING btree (created_at);


--
-- Name: idx_activity_logs_entity; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_activity_logs_entity ON public.activity_logs USING btree (entity_type, entity_id);


--
-- Name: idx_activity_logs_lookup; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_activity_logs_lookup ON public.activity_logs USING btree (user_id, action, created_at);


--
-- Name: idx_activity_logs_table; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_activity_logs_table ON public.activity_logs USING btree (table_name, record_id);


--
-- Name: idx_activity_logs_user; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_activity_logs_user ON public.activity_logs USING btree (user_id);


--
-- Name: idx_booking_conflict; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_booking_conflict ON public.bookings USING btree (resource_id, booking_date, time_slot_id, status);


--
-- Name: idx_category; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_category ON public.lab_inventory USING btree (category);


--
-- Name: idx_classes_active; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_classes_active ON public.classes USING btree (is_active);


--
-- Name: idx_classes_deleted; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_classes_deleted ON public.classes USING btree (deleted_at);


--
-- Name: idx_classes_grade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_classes_grade ON public.classes USING btree (grade_level);


--
-- Name: idx_classes_organization; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_classes_organization ON public.classes USING btree (organization_id);


--
-- Name: idx_condition; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_condition ON public.lab_inventory USING btree (condition);


--
-- Name: idx_created_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_created_by ON public.lab_inventory USING btree (created_by);


--
-- Name: idx_deleted_at; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_deleted_at ON public.lab_inventory USING btree (deleted_at);


--
-- Name: idx_holidays_active; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_holidays_active ON public.holidays USING btree (is_active);


--
-- Name: idx_holidays_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_holidays_type ON public.holidays USING btree (type);


--
-- Name: idx_inventory_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventory_id ON public.lab_inventory_history USING btree (inventory_id);


--
-- Name: idx_is_resource_date; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_is_resource_date ON public.important_schedules USING btree (resource_id, date);


--
-- Name: idx_lab_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_lab_id ON public.procurement_requests USING btree (lab_id);


--
-- Name: idx_maintenance_dates; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_maintenance_dates ON public.maintenance_records USING btree (scheduled_date, completed_date);


--
-- Name: idx_maintenance_resource; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_maintenance_resource ON public.maintenance_records USING btree (resource_id);


--
-- Name: idx_maintenance_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_maintenance_status ON public.maintenance_records USING btree (status);


--
-- Name: idx_maintenance_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_maintenance_type ON public.maintenance_records USING btree (type);


--
-- Name: idx_notifications_created; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_notifications_created ON public.notifications USING btree (created_at);


--
-- Name: idx_notifications_priority; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_notifications_priority ON public.notifications USING btree (priority);


--
-- Name: idx_notifications_read; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_notifications_read ON public.notifications USING btree (is_read);


--
-- Name: idx_notifications_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_notifications_type ON public.notifications USING btree (type);


--
-- Name: idx_notifications_user; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_notifications_user ON public.notifications USING btree (user_id);


--
-- Name: idx_organizations_active; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_organizations_active ON public.organizations USING btree (is_active);


--
-- Name: idx_organizations_deleted; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_organizations_deleted ON public.organizations USING btree (deleted_at);


--
-- Name: idx_organizations_parent; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_organizations_parent ON public.organizations USING btree (parent_id);


--
-- Name: idx_organizations_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_organizations_type ON public.organizations USING btree (type);


--
-- Name: idx_performed_at; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_performed_at ON public.lab_inventory_history USING btree (performed_at);


--
-- Name: idx_performed_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_performed_by ON public.lab_inventory_history USING btree (performed_by);


--
-- Name: idx_priority; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_priority ON public.procurement_requests USING btree (priority);


--
-- Name: idx_quantity; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_quantity ON public.lab_inventory USING btree (quantity);


--
-- Name: idx_requested_at; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_requested_at ON public.procurement_requests USING btree (requested_at);


--
-- Name: idx_requested_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_requested_by ON public.procurement_requests USING btree (requested_by);


--
-- Name: idx_resource_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_resource_id ON public.lab_inventory USING btree (resource_id);


--
-- Name: idx_resources_deleted; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_resources_deleted ON public.resources USING btree (deleted_at);


--
-- Name: idx_resources_organization; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_resources_organization ON public.resources USING btree (organization_id);


--
-- Name: idx_resources_parent; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_resources_parent ON public.resources USING btree (parent_id);


--
-- Name: idx_resources_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_resources_status ON public.resources USING btree (status);


--
-- Name: idx_resources_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_resources_type ON public.resources USING btree (type);


--
-- Name: idx_reviewed_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_reviewed_by ON public.procurement_requests USING btree (reviewed_by);


--
-- Name: idx_schedules_class; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_schedules_class ON public.schedules USING btree (class_id);


--
-- Name: idx_schedules_day; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_schedules_day ON public.schedules USING btree (day_of_week);


--
-- Name: idx_schedules_deleted; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_schedules_deleted ON public.schedules USING btree (deleted_at);


--
-- Name: idx_schedules_lookup; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_schedules_lookup ON public.schedules USING btree (resource_id, day_of_week, status, deleted_at);


--
-- Name: idx_schedules_resource; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_schedules_resource ON public.schedules USING btree (resource_id);


--
-- Name: idx_schedules_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_schedules_status ON public.schedules USING btree (status);


--
-- Name: idx_schedules_time_slot; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_schedules_time_slot ON public.schedules USING btree (time_slot_id);


--
-- Name: idx_schedules_user; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_schedules_user ON public.schedules USING btree (user_id);


--
-- Name: idx_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_status ON public.lab_inventory USING btree (status);


--
-- Name: idx_status_deleted_cat; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_status_deleted_cat ON public.lab_inventory USING btree (status, deleted_at, category, item_name);


--
-- Name: idx_status_name; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_status_name ON public.resources USING btree (status, name);


--
-- Name: idx_status_procurement; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_status_procurement ON public.procurement_requests USING btree (status);


--
-- Name: idx_time_slots_active; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_time_slots_active ON public.time_slots USING btree (is_active);


--
-- Name: idx_time_slots_day; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_time_slots_day ON public.time_slots USING btree (day_of_week);


--
-- Name: idx_time_slots_order; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_time_slots_order ON public.time_slots USING btree (slot_order);


--
-- Name: idx_updated_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_updated_by ON public.lab_inventory USING btree (updated_by);


--
-- Name: idx_user_sessions_expires; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_user_sessions_expires ON public.user_sessions USING btree (expires_at);


--
-- Name: idx_user_sessions_user; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_user_sessions_user ON public.user_sessions USING btree (user_id);


--
-- Name: idx_users_active; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_users_active ON public.users USING btree (is_active);


--
-- Name: idx_users_deleted; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_users_deleted ON public.users USING btree (deleted_at);


--
-- Name: idx_users_organization; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_users_organization ON public.users USING btree (organization_id);


--
-- Name: idx_users_role; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_users_role ON public.users USING btree (role);


--
-- Name: lab_sessions_resource_id_session_start_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lab_sessions_resource_id_session_start_index ON public.lab_sessions USING btree (resource_id, session_start);


--
-- Name: lab_sessions_token_is_active_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lab_sessions_token_is_active_index ON public.lab_sessions USING btree (token, is_active);


--
-- Name: lab_sessions_token_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX lab_sessions_token_unique ON public.lab_sessions USING btree (token);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: permissions_name_guard_name_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX permissions_name_guard_name_unique ON public.permissions USING btree (name, guard_name);


--
-- Name: role_has_permissions_role_id_foreign; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX role_has_permissions_role_id_foreign ON public.role_has_permissions USING btree (role_id);


--
-- Name: roles_name_guard_name_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX roles_name_guard_name_unique ON public.roles USING btree (name, guard_name);


--
-- Name: schedules_teacher_id_foreign; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX schedules_teacher_id_foreign ON public.schedules USING btree (teacher_id);


--
-- Name: sunday_bookings_approved_by_foreign; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sunday_bookings_approved_by_foreign ON public.sunday_bookings USING btree (approved_by);


--
-- Name: sunday_bookings_organization_id_foreign; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sunday_bookings_organization_id_foreign ON public.sunday_bookings USING btree (organization_id);


--
-- Name: sunday_bookings_teacher_id_foreign; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sunday_bookings_teacher_id_foreign ON public.sunday_bookings USING btree (teacher_id);


--
-- Name: teachers_token_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX teachers_token_unique ON public.teachers USING btree (token);


--
-- Name: uk_classes_name_org; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_classes_name_org ON public.classes USING btree (name, organization_id, deleted_at);


--
-- Name: uk_holidays_date; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_holidays_date ON public.holidays USING btree (date);


--
-- Name: uk_organizations_name; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_organizations_name ON public.organizations USING btree (name);


--
-- Name: uk_resources_name; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_resources_name ON public.resources USING btree (name, deleted_at);


--
-- Name: uk_schedules_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_schedules_unique ON public.schedules USING btree (day_of_week, time_slot_id, resource_id, deleted_at);


--
-- Name: uk_sunday_resource_date; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_sunday_resource_date ON public.sunday_bookings USING btree (resource_id, booking_date);


--
-- Name: uk_teachers_phone; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_teachers_phone ON public.teachers USING btree (phone);


--
-- Name: uk_time_slots_name; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_time_slots_name ON public.time_slots USING btree (name);


--
-- Name: uk_user_sessions_token; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_user_sessions_token ON public.user_sessions USING btree (token);


--
-- Name: uk_users_email; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_users_email ON public.users USING btree (email);


--
-- Name: uk_users_username; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uk_users_username ON public.users USING btree (username);


--
-- Name: assignment_submissions assignment_submissions_assignment_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assignment_submissions
    ADD CONSTRAINT assignment_submissions_assignment_id_foreign FOREIGN KEY (assignment_id) REFERENCES public.assignments(id) ON DELETE CASCADE;


--
-- Name: assignments assignments_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE CASCADE;


--
-- Name: bookings bookings_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE SET NULL;


--
-- Name: activity_logs fk_activity_logs_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: bookings fk_bookings_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_approved_by FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: bookings fk_bookings_organization; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: bookings fk_bookings_resource; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_resource FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: bookings fk_bookings_time_slot; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_time_slot FOREIGN KEY (time_slot_id) REFERENCES public.time_slots(id);


--
-- Name: bookings fk_bookings_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: classes fk_classes_organization; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classes
    ADD CONSTRAINT fk_classes_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: lab_inventory_history fk_inventory_history_inventory; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory_history
    ADD CONSTRAINT fk_inventory_history_inventory FOREIGN KEY (inventory_id) REFERENCES public.lab_inventory(id) ON DELETE CASCADE;


--
-- Name: lab_inventory_history fk_inventory_history_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory_history
    ADD CONSTRAINT fk_inventory_history_user FOREIGN KEY (performed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: lab_inventory fk_lab_inventory_created_by; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory
    ADD CONSTRAINT fk_lab_inventory_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: lab_inventory fk_lab_inventory_resource; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory
    ADD CONSTRAINT fk_lab_inventory_resource FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: lab_inventory fk_lab_inventory_updated_by; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_inventory
    ADD CONSTRAINT fk_lab_inventory_updated_by FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: maintenance_records fk_maintenance_created_by; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_records
    ADD CONSTRAINT fk_maintenance_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: maintenance_records fk_maintenance_resource; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_records
    ADD CONSTRAINT fk_maintenance_resource FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: notifications fk_notifications_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: organizations fk_organizations_parent; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT fk_organizations_parent FOREIGN KEY (parent_id) REFERENCES public.organizations(id) ON DELETE SET NULL;


--
-- Name: resources fk_resources_organization; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.resources
    ADD CONSTRAINT fk_resources_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE SET NULL;


--
-- Name: resources fk_resources_parent; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.resources
    ADD CONSTRAINT fk_resources_parent FOREIGN KEY (parent_id) REFERENCES public.resources(id) ON DELETE SET NULL;


--
-- Name: schedules fk_schedules_class; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT fk_schedules_class FOREIGN KEY (class_id) REFERENCES public.classes(id) ON DELETE CASCADE;


--
-- Name: schedules fk_schedules_resource; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT fk_schedules_resource FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: schedules fk_schedules_time_slot; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT fk_schedules_time_slot FOREIGN KEY (time_slot_id) REFERENCES public.time_slots(id);


--
-- Name: schedules fk_schedules_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT fk_schedules_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: user_sessions fk_user_sessions_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_sessions
    ADD CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: users fk_users_organization; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT fk_users_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE SET NULL;


--
-- Name: important_schedules important_schedules_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: important_schedules important_schedules_end_slot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_end_slot_id_foreign FOREIGN KEY (end_slot_id) REFERENCES public.time_slots(id) ON DELETE SET NULL;


--
-- Name: important_schedules important_schedules_resource_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: important_schedules important_schedules_start_slot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.important_schedules
    ADD CONSTRAINT important_schedules_start_slot_id_foreign FOREIGN KEY (start_slot_id) REFERENCES public.time_slots(id) ON DELETE SET NULL;


--
-- Name: inventory_maintenance_logs inventory_maintenance_logs_lab_inventory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_maintenance_logs
    ADD CONSTRAINT inventory_maintenance_logs_lab_inventory_id_foreign FOREIGN KEY (lab_inventory_id) REFERENCES public.lab_inventory(id) ON DELETE CASCADE;


--
-- Name: inventory_maintenance_logs inventory_maintenance_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_maintenance_logs
    ADD CONSTRAINT inventory_maintenance_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: lab_sessions lab_sessions_resource_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lab_sessions
    ADD CONSTRAINT lab_sessions_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: resource_user resource_user_resource_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.resource_user
    ADD CONSTRAINT resource_user_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: resource_user resource_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.resource_user
    ADD CONSTRAINT resource_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: schedules schedules_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.schedules
    ADD CONSTRAINT schedules_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE SET NULL;


--
-- Name: sunday_bookings sunday_bookings_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: sunday_bookings sunday_bookings_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: sunday_bookings sunday_bookings_resource_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES public.resources(id) ON DELETE CASCADE;


--
-- Name: sunday_bookings sunday_bookings_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sunday_bookings
    ADD CONSTRAINT sunday_bookings_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict jfOHLJOObcgytb0F8DkNJq8Cc8leBz2MCr0GPf1I0L9ro3uLHrG4bz02ZvHDcz4

