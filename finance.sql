--
-- PostgreSQL database dump
--

\restrict N7BVppur9FXYuuFjbrnWgvVizIE484MIrc3detbVoeoToyCbc5hAXG4sdlhKtAw

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
-- Name: accounts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.accounts (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(50) DEFAULT 'cash'::character varying NOT NULL,
    balance numeric(15,2) DEFAULT 0.00 NOT NULL,
    description text,
    is_active smallint DEFAULT 1 NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: accounts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.accounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: accounts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.accounts_id_seq OWNED BY public.accounts.id;


--
-- Name: budget_periods; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.budget_periods (
    id bigint NOT NULL,
    name character varying(100) NOT NULL,
    month smallint NOT NULL,
    year smallint NOT NULL,
    total_budget numeric(15,2) DEFAULT 0.00 NOT NULL,
    notes text,
    status character varying(10) DEFAULT 'active'::character varying NOT NULL,
    created_by bigint,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT budget_periods_status_check CHECK (((status)::text = ANY ((ARRAY['active'::character varying, 'closed'::character varying])::text[])))
);


--
-- Name: budget_periods_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.budget_periods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: budget_periods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.budget_periods_id_seq OWNED BY public.budget_periods.id;


--
-- Name: budgets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.budgets (
    id bigint NOT NULL,
    budget_period_id bigint NOT NULL,
    category_id bigint NOT NULL,
    amount numeric(15,2) DEFAULT 0.00 NOT NULL,
    used_amount numeric(15,2) DEFAULT 0.00 NOT NULL,
    notes text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: budgets_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.budgets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: budgets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.budgets_id_seq OWNED BY public.budgets.id;


--
-- Name: categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(10) NOT NULL,
    color character varying(7) DEFAULT '#6366f1'::character varying NOT NULL,
    icon character varying(100) DEFAULT NULL::character varying,
    description text,
    is_active smallint DEFAULT 1 NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT categories_type_check CHECK (((type)::text = ANY ((ARRAY['income'::character varying, 'expense'::character varying])::text[])))
);


--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: finance_sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.finance_sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45) DEFAULT NULL::character varying,
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: finance_users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.finance_users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    role character varying(10) DEFAULT 'bendahara'::character varying NOT NULL,
    phone character varying(20) DEFAULT NULL::character varying,
    is_active smallint DEFAULT 1 NOT NULL,
    last_login_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    remember_token character varying(100) DEFAULT NULL::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT finance_users_role_check CHECK (((role)::text = ANY ((ARRAY['admin'::character varying, 'bendahara'::character varying])::text[])))
);


--
-- Name: finance_users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.finance_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: finance_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.finance_users_id_seq OWNED BY public.finance_users.id;


--
-- Name: transactions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.transactions (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    account_id bigint NOT NULL,
    category_id bigint NOT NULL,
    budget_period_id bigint,
    type character varying(10) NOT NULL,
    amount numeric(15,2) NOT NULL,
    transaction_date date NOT NULL,
    description character varying(500) NOT NULL,
    notes text,
    reference character varying(100) DEFAULT NULL::character varying,
    attachment character varying(255) DEFAULT NULL::character varying,
    created_by bigint,
    created_by_name character varying(255) DEFAULT NULL::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    deleted_at timestamp without time zone,
    CONSTRAINT transactions_type_check CHECK (((type)::text = ANY ((ARRAY['income'::character varying, 'expense'::character varying])::text[])))
);


--
-- Name: transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.transactions_id_seq OWNED BY public.transactions.id;


--
-- Name: wa_notification_logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wa_notification_logs (
    id bigint NOT NULL,
    transaction_id bigint,
    phone character varying(20) NOT NULL,
    message text NOT NULL,
    status character varying(10) DEFAULT 'pending'::character varying NOT NULL,
    response text,
    sent_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT wa_notification_logs_status_check CHECK (((status)::text = ANY ((ARRAY['sent'::character varying, 'failed'::character varying, 'pending'::character varying])::text[])))
);


--
-- Name: wa_notification_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wa_notification_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wa_notification_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wa_notification_logs_id_seq OWNED BY public.wa_notification_logs.id;


--
-- Name: wa_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.wa_settings (
    id bigint NOT NULL,
    fonnte_token character varying(255) NOT NULL,
    admin_phone character varying(20) NOT NULL,
    device_number character varying(20) DEFAULT NULL::character varying,
    target_phones text,
    notify_income smallint DEFAULT 1 NOT NULL,
    notify_expense smallint DEFAULT 1 NOT NULL,
    notify_budget_warning smallint DEFAULT 1 NOT NULL,
    notify_on_transaction smallint DEFAULT 1 NOT NULL,
    notify_on_budget_exceeded smallint DEFAULT 1 NOT NULL,
    budget_alert_threshold smallint DEFAULT 80 NOT NULL,
    budget_warning_pct smallint DEFAULT 80 NOT NULL,
    is_active smallint DEFAULT 1 NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: wa_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.wa_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wa_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.wa_settings_id_seq OWNED BY public.wa_settings.id;


--
-- Name: accounts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.accounts ALTER COLUMN id SET DEFAULT nextval('public.accounts_id_seq'::regclass);


--
-- Name: budget_periods id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budget_periods ALTER COLUMN id SET DEFAULT nextval('public.budget_periods_id_seq'::regclass);


--
-- Name: budgets id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets ALTER COLUMN id SET DEFAULT nextval('public.budgets_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: finance_users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.finance_users ALTER COLUMN id SET DEFAULT nextval('public.finance_users_id_seq'::regclass);


--
-- Name: transactions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions ALTER COLUMN id SET DEFAULT nextval('public.transactions_id_seq'::regclass);


--
-- Name: wa_notification_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wa_notification_logs ALTER COLUMN id SET DEFAULT nextval('public.wa_notification_logs_id_seq'::regclass);


--
-- Name: wa_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wa_settings ALTER COLUMN id SET DEFAULT nextval('public.wa_settings_id_seq'::regclass);


--
-- Data for Name: accounts; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.accounts (id, name, type, balance, description, is_active, created_at, updated_at) FROM stdin;
1	Kas Tim	cash	715000.00	Kas tunai utama tim	1	2026-02-22 22:48:59	2026-05-28 07:35:58
\.


--
-- Data for Name: budget_periods; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.budget_periods (id, name, month, year, total_budget, notes, status, created_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: budgets; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.budgets (id, budget_period_id, category_id, amount, used_amount, notes, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categories (id, name, type, color, icon, description, is_active, created_at, updated_at) FROM stdin;
1	Dana Institusi	income	#22c55e	\N	\N	1	2026-02-22 22:48:59	2026-02-22 22:48:59
2	Iuran Anggota	income	#3b82f6	\N	\N	1	2026-02-22 22:48:59	2026-02-22 22:48:59
3	Pendapatan Lain	income	#a855f7	\N	\N	1	2026-02-22 22:48:59	2026-02-22 22:48:59
4	Operasional	expense	#f97316	\N	\N	1	2026-02-22 22:48:59	2026-02-22 22:48:59
5	Perlengkapan	expense	#eab308	\N	\N	1	2026-02-22 22:48:59	2026-02-22 22:48:59
6	Konsumsi	expense	#ef4444	\N	\N	1	2026-02-22 22:48:59	2026-02-22 22:48:59
7	Transportasi	expense	#06b6d4	\N	\N	1	2026-02-22 22:48:59	2026-02-22 22:48:59
8	Lain-lain	expense	#64748b	\N	\N	1	2026-02-22 22:48:59	2026-02-22 22:48:59
\.


--
-- Data for Name: finance_sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.finance_sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: finance_users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.finance_users (id, name, email, password, role, phone, is_active, last_login_at, remember_token, created_at, updated_at) FROM stdin;
1	Admin Finance	admin@finance.local	$2y$12$4yOJfyyvgA7P.N6pRBzkle7ZGeo7MQvEvI4bXTXQMPQaJAnc3n0Mu	admin	\N	1	2026-05-28 07:34:57	\N	2026-02-22 22:48:59	2026-05-28 07:34:57
2	Bendahara	bendahara@finance.local	$2y$12$4yOJfyyvgA7P.N6pRBzkle7ZGeo7MQvEvI4bXTXQMPQaJAnc3n0Mu	bendahara	\N	1	2026-05-11 15:47:47	\N	2026-02-22 22:48:59	2026-05-11 15:47:47
\.


--
-- Data for Name: transactions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.transactions (id, code, account_id, category_id, budget_period_id, type, amount, transaction_date, description, notes, reference, attachment, created_by, created_by_name, created_at, updated_at, deleted_at) FROM stdin;
1	TRX-20260223-0001	1	1	\N	income	4000.00	2026-02-23	wifi	\N	\N	\N	1	Admin Finance	2026-02-23 16:04:59	2026-02-23 23:28:28	2026-02-23 23:28:28
2	TRX-20260223-0002	1	1	\N	income	700000.00	2026-02-23	kas awal	\N	\N	\N	1	Admin Finance	2026-02-23 23:36:46	2026-05-12 09:23:29	2026-05-12 09:23:29
3	TRX-20260223-0003	1	6	\N	expense	150000.00	2026-02-23	ngopi	\N	\N	\N	1	Admin Finance	2026-02-23 23:38:08	2026-05-12 09:23:25	2026-05-12 09:23:25
4	TRX-20260225-0001	1	1	\N	income	400000.00	2026-02-25	masuk	\N	\N	\N	1	Admin Finance	2026-02-25 03:01:05	2026-02-25 03:01:15	2026-02-25 03:01:15
5	TRX-20260226-0001	1	1	\N	income	50000.00	2026-02-26	Beli ATK	\N	\N	\N	1	Admin Finance	2026-02-26 01:27:10	2026-02-26 01:28:08	2026-02-26 01:28:08
6	TRX-20260226-0002	1	1	\N	income	100000.00	2026-02-26	Dana operasional	\N	\N	\N	1	Admin Finance	2026-02-26 01:28:54	2026-02-26 01:30:04	2026-02-26 01:30:04
7	TRX-20260226-0003	1	1	\N	income	50000.00	2026-02-26	Konsumsi rapat	\N	\N	\N	1	Admin Finance	2026-02-26 01:32:03	2026-02-26 03:17:38	2026-02-26 03:17:38
8	TRX-20260512-0001	1	1	\N	income	50000.00	2026-05-12	Dana operasional	\N	\N	\N	1	Admin Finance	2026-05-12 09:22:51	2026-05-12 09:23:09	2026-05-12 09:23:09
9	TRX-20260518-0001	1	1	\N	income	550000.00	2026-05-18	masuk	\N	\N	\N	1	Admin Finance	2026-05-18 09:24:52	2026-05-18 09:24:52	\N
10	TRX-20260518-0002	1	4	\N	expense	300000.00	2026-05-18	keluar	\N	\N	\N	1	Admin Finance	2026-05-18 09:25:18	2026-05-18 09:25:18	\N
11	TRX-20260518-0003	1	1	\N	income	400000.00	2026-05-18	masuk	\N	\N	\N	1	Admin Finance	2026-05-18 09:25:59	2026-05-18 09:25:59	\N
12	TRX-20260523-0001	1	1	\N	income	200000.00	2026-05-23	masuk bulan april	\N	\N	\N	1	Admin Finance	2026-05-23 12:42:28	2026-05-23 12:42:28	\N
13	TRX-20260523-0002	1	8	\N	expense	200000.00	2026-05-23	dipinjam ucup	\N	\N	\N	1	Admin Finance	2026-05-23 12:43:14	2026-05-23 12:43:14	\N
14	TRX-20260523-0003	1	5	\N	expense	135000.00	2026-05-23	modem	\N	\N	\N	1	Admin Finance	2026-05-23 12:45:27	2026-05-23 12:45:27	\N
15	TRX-20260528-0001	1	1	\N	income	200000.00	2026-05-28	pengembalian dana kenakalan	\N	\N	\N	1	Admin Finance	2026-05-28 07:35:58	2026-05-28 07:35:58	\N
\.


--
-- Data for Name: wa_notification_logs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.wa_notification_logs (id, transaction_id, phone, message, status, response, sent_at, created_at) FROM stdin;
1	\N	6281217574441	???? TEST NOTIFIKASI	failed	cURL error 7	2026-05-11 23:42:11	2026-05-11 23:42:08
2	8	6281217574441	??? PEMASUKAN BARU TRX-20260512-0001	failed	cURL error 7	2026-05-12 02:22:54	2026-05-12 02:22:51
3	9	6281217574441	??? PEMASUKAN BARU TRX-20260518-0001	sent	{"success":true}	2026-05-18 09:24:52	2026-05-18 02:24:52
4	10	6281217574441	???? PENGELUARAN BARU TRX-20260518-0002	sent	{"success":true}	2026-05-18 09:25:18	2026-05-18 02:25:18
5	11	6281217574441	??? PEMASUKAN BARU TRX-20260518-0003	sent	{"success":true}	2026-05-18 09:25:59	2026-05-18 02:25:59
6	12	6281217574441	??? PEMASUKAN BARU TRX-20260523-0001	sent	{"success":true}	2026-05-23 12:42:29	2026-05-23 05:42:28
7	13	6281217574441	???? PENGELUARAN BARU TRX-20260523-0002	sent	{"success":true}	2026-05-23 12:43:15	2026-05-23 05:43:14
8	14	6281217574441	???? PENGELUARAN BARU TRX-20260523-0003	sent	{"success":true}	2026-05-23 12:45:27	2026-05-23 05:45:27
9	15	6281217574441	??? PEMASUKAN BARU TRX-20260528-0001	sent	{"success":true}	2026-05-28 07:35:59	2026-05-28 00:35:58
\.


--
-- Data for Name: wa_settings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.wa_settings (id, fonnte_token, admin_phone, device_number, target_phones, notify_income, notify_expense, notify_budget_warning, notify_on_transaction, notify_on_budget_exceeded, budget_alert_threshold, budget_warning_pct, is_active, created_at, updated_at) FROM stdin;
1	YHzpGrcZXLQegWjQDHXH	6281217574441	6285143402691	["6281217574441"]	1	1	1	1	1	80	80	1	2026-02-22 22:48:59	2026-05-12 06:41:32
\.


--
-- Name: accounts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.accounts_id_seq', 1, true);


--
-- Name: budget_periods_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.budget_periods_id_seq', 1, false);


--
-- Name: budgets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.budgets_id_seq', 1, false);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.categories_id_seq', 8, true);


--
-- Name: finance_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.finance_users_id_seq', 2, true);


--
-- Name: transactions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.transactions_id_seq', 15, true);


--
-- Name: wa_notification_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.wa_notification_logs_id_seq', 9, true);


--
-- Name: wa_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.wa_settings_id_seq', 1, true);


--
-- Name: accounts accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.accounts
    ADD CONSTRAINT accounts_pkey PRIMARY KEY (id);


--
-- Name: budget_periods budget_periods_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budget_periods
    ADD CONSTRAINT budget_periods_pkey PRIMARY KEY (id);


--
-- Name: budgets budgets_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT budgets_pkey PRIMARY KEY (id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: finance_sessions finance_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.finance_sessions
    ADD CONSTRAINT finance_sessions_pkey PRIMARY KEY (id);


--
-- Name: finance_users finance_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.finance_users
    ADD CONSTRAINT finance_users_pkey PRIMARY KEY (id);


--
-- Name: transactions transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_pkey PRIMARY KEY (id);


--
-- Name: transactions uq_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT uq_code UNIQUE (code);


--
-- Name: finance_users uq_email; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.finance_users
    ADD CONSTRAINT uq_email UNIQUE (email);


--
-- Name: budget_periods uq_month_year; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budget_periods
    ADD CONSTRAINT uq_month_year UNIQUE (month, year);


--
-- Name: budgets uq_period_category; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT uq_period_category UNIQUE (budget_period_id, category_id);


--
-- Name: wa_notification_logs wa_notification_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wa_notification_logs
    ADD CONSTRAINT wa_notification_logs_pkey PRIMARY KEY (id);


--
-- Name: wa_settings wa_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wa_settings
    ADD CONSTRAINT wa_settings_pkey PRIMARY KEY (id);


--
-- Name: idx_fs_last_activity; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fs_last_activity ON public.finance_sessions USING btree (last_activity);


--
-- Name: idx_fs_user_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fs_user_id ON public.finance_sessions USING btree (user_id);


--
-- Name: budgets fk_budgets_category; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT fk_budgets_category FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: budgets fk_budgets_period; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT fk_budgets_period FOREIGN KEY (budget_period_id) REFERENCES public.budget_periods(id) ON DELETE CASCADE;


--
-- Name: budget_periods fk_period_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budget_periods
    ADD CONSTRAINT fk_period_user FOREIGN KEY (created_by) REFERENCES public.finance_users(id) ON DELETE SET NULL;


--
-- Name: transactions fk_trx_account; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT fk_trx_account FOREIGN KEY (account_id) REFERENCES public.accounts(id);


--
-- Name: transactions fk_trx_category; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT fk_trx_category FOREIGN KEY (category_id) REFERENCES public.categories(id);


--
-- Name: transactions fk_trx_period; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT fk_trx_period FOREIGN KEY (budget_period_id) REFERENCES public.budget_periods(id) ON DELETE SET NULL;


--
-- Name: transactions fk_trx_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT fk_trx_user FOREIGN KEY (created_by) REFERENCES public.finance_users(id) ON DELETE SET NULL;


--
-- Name: wa_notification_logs fk_walog_trx; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.wa_notification_logs
    ADD CONSTRAINT fk_walog_trx FOREIGN KEY (transaction_id) REFERENCES public.transactions(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict N7BVppur9FXYuuFjbrnWgvVizIE484MIrc3detbVoeoToyCbc5hAXG4sdlhKtAw

