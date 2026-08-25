--
-- PostgreSQL database dump
--

\restrict gBFu40gCsN5oyY6mOzXQoditIDesBermpBmb9qrXh6zsExcq5QE9e7vJKV3Lf5m

-- Dumped from database version 16.14
-- Dumped by pg_dump version 16.14

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

ALTER TABLE IF EXISTS ONLY public.perwalian DROP CONSTRAINT IF EXISTS perwalian_mahasiswa_id_foreign;
ALTER TABLE IF EXISTS ONLY public.mahasiswa DROP CONSTRAINT IF EXISTS mahasiswa_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.dosen_wali DROP CONSTRAINT IF EXISTS dosen_wali_mahasiswa_id_foreign;
ALTER TABLE IF EXISTS ONLY public.dosen_wali DROP CONSTRAINT IF EXISTS dosen_wali_dosen_id_foreign;
ALTER TABLE IF EXISTS ONLY public.dosen DROP CONSTRAINT IF EXISTS dosen_user_id_foreign;
DROP INDEX IF EXISTS public.sessions_user_id_index;
DROP INDEX IF EXISTS public.sessions_last_activity_index;
DROP INDEX IF EXISTS public.jobs_queue_index;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_username_unique;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_email_unique;
ALTER TABLE IF EXISTS ONLY public.sessions DROP CONSTRAINT IF EXISTS sessions_pkey;
ALTER TABLE IF EXISTS ONLY public.perwalian DROP CONSTRAINT IF EXISTS perwalian_pkey;
ALTER TABLE IF EXISTS ONLY public.password_reset_tokens DROP CONSTRAINT IF EXISTS password_reset_tokens_pkey;
ALTER TABLE IF EXISTS ONLY public.migrations DROP CONSTRAINT IF EXISTS migrations_pkey;
ALTER TABLE IF EXISTS ONLY public.mahasiswa DROP CONSTRAINT IF EXISTS mahasiswa_pkey;
ALTER TABLE IF EXISTS ONLY public.mahasiswa DROP CONSTRAINT IF EXISTS mahasiswa_npm_unique;
ALTER TABLE IF EXISTS ONLY public.jobs DROP CONSTRAINT IF EXISTS jobs_pkey;
ALTER TABLE IF EXISTS ONLY public.job_batches DROP CONSTRAINT IF EXISTS job_batches_pkey;
ALTER TABLE IF EXISTS ONLY public.failed_jobs DROP CONSTRAINT IF EXISTS failed_jobs_uuid_unique;
ALTER TABLE IF EXISTS ONLY public.failed_jobs DROP CONSTRAINT IF EXISTS failed_jobs_pkey;
ALTER TABLE IF EXISTS ONLY public.dosen_wali DROP CONSTRAINT IF EXISTS dosen_wali_pkey;
ALTER TABLE IF EXISTS ONLY public.dosen_wali DROP CONSTRAINT IF EXISTS dosen_wali_mahasiswa_id_unique;
ALTER TABLE IF EXISTS ONLY public.dosen DROP CONSTRAINT IF EXISTS dosen_pkey;
ALTER TABLE IF EXISTS ONLY public.dosen DROP CONSTRAINT IF EXISTS dosen_nidn_unique;
ALTER TABLE IF EXISTS ONLY public.cache DROP CONSTRAINT IF EXISTS cache_pkey;
ALTER TABLE IF EXISTS ONLY public.cache_locks DROP CONSTRAINT IF EXISTS cache_locks_pkey;
ALTER TABLE IF EXISTS public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.perwalian ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.migrations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.mahasiswa ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.jobs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.failed_jobs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.dosen_wali ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.dosen ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE IF EXISTS public.users_id_seq;
DROP TABLE IF EXISTS public.users;
DROP TABLE IF EXISTS public.sessions;
DROP SEQUENCE IF EXISTS public.perwalian_id_seq;
DROP TABLE IF EXISTS public.perwalian;
DROP TABLE IF EXISTS public.password_reset_tokens;
DROP SEQUENCE IF EXISTS public.migrations_id_seq;
DROP TABLE IF EXISTS public.migrations;
DROP SEQUENCE IF EXISTS public.mahasiswa_id_seq;
DROP TABLE IF EXISTS public.mahasiswa;
DROP SEQUENCE IF EXISTS public.jobs_id_seq;
DROP TABLE IF EXISTS public.jobs;
DROP TABLE IF EXISTS public.job_batches;
DROP SEQUENCE IF EXISTS public.failed_jobs_id_seq;
DROP TABLE IF EXISTS public.failed_jobs;
DROP SEQUENCE IF EXISTS public.dosen_wali_id_seq;
DROP TABLE IF EXISTS public.dosen_wali;
DROP SEQUENCE IF EXISTS public.dosen_id_seq;
DROP TABLE IF EXISTS public.dosen;
DROP TABLE IF EXISTS public.cache_locks;
DROP TABLE IF EXISTS public.cache;
SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: dosen; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.dosen (
    id bigint NOT NULL,
    user_id bigint,
    nidn character varying(255) NOT NULL,
    nama character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: dosen_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.dosen_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: dosen_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.dosen_id_seq OWNED BY public.dosen.id;


--
-- Name: dosen_wali; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.dosen_wali (
    id bigint NOT NULL,
    mahasiswa_id bigint NOT NULL,
    dosen_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: dosen_wali_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.dosen_wali_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: dosen_wali_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.dosen_wali_id_seq OWNED BY public.dosen_wali.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: mahasiswa; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.mahasiswa (
    id bigint NOT NULL,
    user_id bigint,
    npm character varying(255) NOT NULL,
    nama character varying(255) NOT NULL,
    prodi character varying(255) NOT NULL,
    angkatan character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: mahasiswa_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.mahasiswa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: mahasiswa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.mahasiswa_id_seq OWNED BY public.mahasiswa.id;


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
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: perwalian; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.perwalian (
    id bigint NOT NULL,
    mahasiswa_id bigint NOT NULL,
    tanggal date NOT NULL,
    semester character varying(255) NOT NULL,
    hasil_perwalian text NOT NULL,
    kendala text,
    rencana_perbaikan text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: perwalian_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.perwalian_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: perwalian_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.perwalian_id_seq OWNED BY public.perwalian.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255),
    username character varying(255) NOT NULL,
    role character varying(255) DEFAULT 'mahasiswa'::character varying NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
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
-- Name: dosen id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen ALTER COLUMN id SET DEFAULT nextval('public.dosen_id_seq'::regclass);


--
-- Name: dosen_wali id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen_wali ALTER COLUMN id SET DEFAULT nextval('public.dosen_wali_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: mahasiswa id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mahasiswa ALTER COLUMN id SET DEFAULT nextval('public.mahasiswa_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: perwalian id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.perwalian ALTER COLUMN id SET DEFAULT nextval('public.perwalian_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: dosen; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.dosen (id, user_id, nidn, nama, created_at, updated_at) FROM stdin;
1	2	0416078001	Dr. Ahmad Fauzi, M.Kom	2026-08-05 17:46:54	2026-08-05 17:46:54
2	3	0425028102	Dr. Siti Rahayu, M.TI	2026-08-05 17:46:54	2026-08-05 17:46:54
3	4	0407088403	Budi Santoso, M.Kom	2026-08-05 17:46:54	2026-08-05 17:46:54
4	5	0427128804	Rina Wulandari, S.Kom., M.Cs	2026-08-05 17:46:55	2026-08-05 17:46:55
\.


--
-- Data for Name: dosen_wali; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.dosen_wali (id, mahasiswa_id, dosen_id, created_at, updated_at) FROM stdin;
1	1	1	2026-08-05 17:46:58	2026-08-05 17:46:58
2	2	2	2026-08-05 17:46:58	2026-08-05 17:46:58
3	3	3	2026-08-05 17:46:58	2026-08-05 17:46:58
4	4	4	2026-08-05 17:46:58	2026-08-05 17:46:58
5	5	1	2026-08-05 17:46:58	2026-08-05 17:46:58
6	6	2	2026-08-05 17:46:58	2026-08-05 17:46:58
7	7	3	2026-08-05 17:46:58	2026-08-05 17:46:58
8	8	4	2026-08-05 17:46:58	2026-08-05 17:46:58
9	9	1	2026-08-05 17:46:58	2026-08-05 17:46:58
10	10	2	2026-08-05 17:46:58	2026-08-05 17:46:58
11	11	3	2026-08-05 17:46:58	2026-08-05 17:46:58
12	12	4	2026-08-05 17:46:58	2026-08-05 17:46:58
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: mahasiswa; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.mahasiswa (id, user_id, npm, nama, prodi, angkatan, created_at, updated_at) FROM stdin;
1	6	20221001	Andi Pratama	Teknik Informatika	2022	2026-08-05 17:46:55	2026-08-05 17:46:55
2	7	20221002	Budi Hartono	Sistem Informasi	2022	2026-08-05 17:46:55	2026-08-05 17:46:55
3	8	20221003	Citra Lestari	Manajemen Informatika	2022	2026-08-05 17:46:56	2026-08-05 17:46:56
4	9	20221004	Dewi Anggraini	Teknik Komputer	2022	2026-08-05 17:46:56	2026-08-05 17:46:56
5	10	20221005	Eko Saputra	Teknik Informatika	2022	2026-08-05 17:46:56	2026-08-05 17:46:56
6	11	20221006	Fitri Handayani	Sistem Informasi	2022	2026-08-05 17:46:56	2026-08-05 17:46:56
7	12	20231001	Gilang Ramadhan	Teknik Informatika	2023	2026-08-05 17:46:57	2026-08-05 17:46:57
8	13	20231002	Hesti Puspita	Manajemen Informatika	2023	2026-08-05 17:46:57	2026-08-05 17:46:57
9	14	20231003	Indra Wijaya	Teknik Komputer	2023	2026-08-05 17:46:57	2026-08-05 17:46:57
10	15	20231004	Joko Susilo	Sistem Informasi	2023	2026-08-05 17:46:58	2026-08-05 17:46:58
11	16	20241001	Laila Nurjanah	Teknik Informatika	2024	2026-08-05 17:46:58	2026-08-05 17:46:58
12	17	20241002	Muhammad Rizki	Sistem Informasi	2024	2026-08-05 17:46:58	2026-08-05 17:46:58
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_08_05_000001_create_mahasiswa_table	1
5	2026_08_05_000002_create_dosen_table	1
6	2026_08_05_000003_create_dosen_wali_table	1
7	2026_08_05_000004_create_perwalian_table	1
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: perwalian; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.perwalian (id, mahasiswa_id, tanggal, semester, hasil_perwalian, kendala, rencana_perbaikan, created_at, updated_at) FROM stdin;
1	1	2025-02-05	8	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
2	2	2025-02-05	7	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
3	2	2025-07-13	8	Membahas rencana pengambilan mata kuliah semester depan. Disarankan mengambil mata kuliah pilihan sesuai minat bidang.	Jadwal kuliah bentrok dengan kegiatan organisasi.	Membuat jadwal belajar yang lebih teratur dan menyesuaikan kegiatan organisasi.	2026-08-05 17:46:58	2026-08-05 17:46:58
4	3	2025-02-05	8	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
5	4	2025-02-05	6	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
6	4	2025-07-13	7	Membahas rencana pengambilan mata kuliah semester depan. Disarankan mengambil mata kuliah pilihan sesuai minat bidang.	Jadwal kuliah bentrok dengan kegiatan organisasi.	Membuat jadwal belajar yang lebih teratur dan menyesuaikan kegiatan organisasi.	2026-08-05 17:46:58	2026-08-05 17:46:58
7	4	2025-12-21	8	Mendiskusikan kesulitan pada mata kuliah pemrograman. Dosen wali memberikan saran untuk mengikuti kelas tambahan dan berlatih mandiri.	Kurang fokus saat pembelajaran daring.	Mencari tempat belajar yang kondusif dan membatasi distraksi.	2026-08-05 17:46:58	2026-08-05 17:46:58
8	5	2025-02-05	7	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
9	5	2025-07-13	8	Membahas rencana pengambilan mata kuliah semester depan. Disarankan mengambil mata kuliah pilihan sesuai minat bidang.	Jadwal kuliah bentrok dengan kegiatan organisasi.	Membuat jadwal belajar yang lebih teratur dan menyesuaikan kegiatan organisasi.	2026-08-05 17:46:58	2026-08-05 17:46:58
10	6	2025-02-05	8	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
11	7	2025-11-05	5	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
12	7	2026-04-13	6	Membahas rencana pengambilan mata kuliah semester depan. Disarankan mengambil mata kuliah pilihan sesuai minat bidang.	Jadwal kuliah bentrok dengan kegiatan organisasi.	Membuat jadwal belajar yang lebih teratur dan menyesuaikan kegiatan organisasi.	2026-08-05 17:46:58	2026-08-05 17:46:58
13	8	2025-11-05	6	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
14	9	2025-11-05	6	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
15	10	2025-11-05	5	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
16	10	2026-04-13	6	Membahas rencana pengambilan mata kuliah semester depan. Disarankan mengambil mata kuliah pilihan sesuai minat bidang.	Jadwal kuliah bentrok dengan kegiatan organisasi.	Membuat jadwal belajar yang lebih teratur dan menyesuaikan kegiatan organisasi.	2026-08-05 17:46:58	2026-08-05 17:46:58
17	11	2026-05-05	4	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
18	12	2026-05-05	3	Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.	Kesulitan memahami materi algoritma dan struktur data.	Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.	2026-08-05 17:46:58	2026-08-05 17:46:58
19	12	2026-07-27	4	Membahas rencana pengambilan mata kuliah semester depan. Disarankan mengambil mata kuliah pilihan sesuai minat bidang.	Jadwal kuliah bentrok dengan kegiatan organisasi.	Membuat jadwal belajar yang lebih teratur dan menyesuaikan kegiatan organisasi.	2026-08-05 17:46:58	2026-08-05 17:46:58
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
c0jc1irZtPyDNeR5rXNcd94VrBm3WEVONF7iXZFp	\N	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTozOntzOjY6Il90b2tlbiI7czo0MDoiM1NuUE9yaTROTGpXY1Z4SVhrbUxKVFNsVE5nWGtRQUxkTklSdmlubyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1785926842
UHH1HFF6jx5Wf5dIX4gPuS1N5u1dtqTnD48TqwRC	\N	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTozOntzOjY6Il90b2tlbiI7czo0MDoicjcybGw4cEt4T3RLVzFTM3VlSVdSaUw3eXFEbVlXVjBHRDhWZU9qZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1785926848
Lxz9vip1lLNSZA8EbhsoBkpi3kG3P3SQoj3ypMjx	1	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWWtobkhLTUQwWUN6OUVpVVd0UnJkSnk3YWVmRm5sdEYxT2pCMzlPWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==	1785926857
sTx2mt9PYgQPc2IG1oymi5hOMXAmswpeh74d4sgj	1	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiblN4dkRNQjlSWmZVMThFSUUxMHFZM2JzVmwyOUVwQVY3eVRBTXJjcCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZWthcCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==	1785926873
flS5Z0tcT8HTwQkKixj9P1nalAUxAtghrtHLc7V2	2	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoidkZJc3E4a2dnaUdwQ0VPanVwcGl0WlpDRlQ5cEtqVkxFWUh5b0w4ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kb3Nlbi9iaW1iaW5nYW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=	1785926887
Wb8WfwWtpKYgb8goLvpIuctOHehwVsFOoEXBz9qe	6	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZll2Q1NhTkZqUnJpNFdzNmlKYWpYdzFiMXZENTNneTZZM0FwVmFDaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYWhhc2lzd2EvcGVyd2FsaWFuL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjY7fQ==	1785926885
gyZERyTOzjXKBzImL1genK6AGgAZ144Wn5MaCMd5	2	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoibWl1RUt2NXI3MVRFV3dDakdoTTZkMVV1dlk3U3EyNGVHcWVWZ2prcyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kb3Nlbi9iaW1iaW5nYW4vMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==	1785926888
CJPtj5CS4KfE7eqyLhNz491SsOoxQXIDnMhLty8M	6	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYlJRdm8wVERtYThmWjZUMGVWVmVrdmFtZ3M3aDZNaEpUQW5oRXpkcyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tYWhhc2lzd2EiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo2O30=	1785926890
mYQKmW0A2x3QOVdCdWKjFYEQqWM3mkoM7LRwd04H	1	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiOEpUSGROdjg3Ylh0TTJQMzN2Mk5jYUlNd0ZSVGtnNkE2bXl1MUxIOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kb3Nlbi13YWxpIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MTp7aTowO3M6Nzoic3VjY2VzcyI7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjc6InN1Y2Nlc3MiO3M6NTY6IkRvc2VuIHdhbGkgdW50dWsgVGVzdGluZyBFbmQgVG8gRW5kIGJlcmhhc2lsIGRpdGV0YXBrYW4uIjt9	1785926905
5Db31s3pyKaVdJYWMAKNF0S1GfOnjF1WcMi0wHhj	18	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo1OntzOjY6Il90b2tlbiI7czo0MDoid2hJOWRWbzBhSjFtQTBKUnVoYms1ekFxd1VnbHppdDZGQ2EyNFRnZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYWhhc2lzd2EvcGVyd2FsaWFuL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjc6InN1Y2Nlc3MiO31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE4O3M6Nzoic3VjY2VzcyI7czozNjoiQ2F0YXRhbiBwZXJ3YWxpYW4gYmVyaGFzaWwgZGlzaW1wYW4uIjt9	1785926915
czYCGArWzFTGLK5WLqQjLltOle60UEDvlcFs6ohe	2	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVjFTVDlhczA2R3ZaMWJMTGFkSFlDd0k5M29tMUdtMEV2blNrd3FyRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kb3Nlbi9iaW1iaW5nYW4vMTMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=	1785926916
kzcyi9bgR4YywzAg54CLqCqiMWb36Tv9puYxxPOG	1	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaDdFa0dnTlpjVUpmNkJFRFJ1czR3S01XUGNCY0xnTlFMRlNScVc5SyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZWthcCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==	1785926918
dyIr00hN7Dj6AlH8KhVkhND8EhyS6X29ktMSd2nS	3	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoibjEyZDFBRk9IczFnSkJ6SkhHNUFLUGYwdUVtamNGQWhQcG12aWh6NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kb3Nlbi9iaW1iaW5nYW4vMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==	1785926919
IiApGfp1sSPPj7MyW9Llt4HYiPzMDo5TnOC5l1kU	18	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSk9TUnlXRXB0UVVqalpYVlVqenZuVTM3anZzcUw1OFRIRHpZcVJHeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYWhhc2lzd2EvcHJvZmlsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MTp7aTowO3M6Nzoic3VjY2VzcyI7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTg7czo3OiJzdWNjZXNzIjtzOjI2OiJQYXNzd29yZCBiZXJoYXNpbCBkaWdhbnRpLiI7fQ==	1785926931
M22US3he8H6tCaEqse9NXQUEx8ZsdNVqvyQ5TbiX	18	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVHpVVkhFdzZuQkFCMmQ1S2xpSE1UN0ZHNDBiWGVrQzhLTGhLZFNJYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE4O30=	1785926932
nDMS9NShnOKHNCM97vEAsFkVt6a8fsYJicFG4wtE	19	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUnBDM1FLUGVUSzdYQlNIdGxPMFp3SUtpUTFLeDFrTFBpRUl0aGh1diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE5O30=	1785926956
8iEmEsdAXBwvWYtLsOPI2xwZCOsb07h8zvdgHIIP	1	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZnZ3d1Jaak5ySnV5OW9EclNMQ2Rnc25VaHFJeGwzenN0Q01ZWWpqbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tYWhhc2lzd2EiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6Nzoic3VjY2VzcyI7czozMjoiRGF0YSBtYWhhc2lzd2EgYmVyaGFzaWwgZGloYXB1cy4iO30=	1785926943
KoFopOmAYYiFNqEKoBdhS4CAygEGnNucV3Tm4SdS	1	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRWVET2prQXkzWlloYlVMSWE0UWl4RWZkM2NGcEVLWGFac1BTN0lxWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kb3NlbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjc6InN1Y2Nlc3MiO31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo3OiJzdWNjZXNzIjtzOjI4OiJEYXRhIGRvc2VuIGJlcmhhc2lsIGRpaGFwdXMuIjt9	1785926957
eC5lrOTHyaXltzBfSQ0qugYbQG6hjmWBeHiy3Wwt	1	127.0.0.1	Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZDhidUliMUxkN0k3dnVlc3lSaEoyYkF2dXlJWEZYblQ5cGw5RE1OdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==	1785927144
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, username, role, email_verified_at, password, remember_token, created_at, updated_at) FROM stdin;
1	Administrator	admin@stmikbandung.ac.id	admin	admin	2026-08-05 17:46:53	$2y$12$1zC/eDUEyFFAU8lpzzdgHe/9/Nh6Fqr/zGpZRcCnGjc9W.dRs3n26	R4HObLf1Me	2026-08-05 17:46:54	2026-08-05 17:46:54
2	Dr. Ahmad Fauzi, M.Kom	0416078001@dosen.stmikbandung.ac.id	0416078001	dosen	2026-08-05 17:46:54	$2y$12$0phkoX92eKeftVAhFu5HAeuPJVQclzrQMpGolxpGBP8KsUPkmANCG	39YaDU7E7l	2026-08-05 17:46:54	2026-08-05 17:46:54
3	Dr. Siti Rahayu, M.TI	0425028102@dosen.stmikbandung.ac.id	0425028102	dosen	2026-08-05 17:46:54	$2y$12$mbBSFzxWImy0mBCiOFdUVe2IboIEQKJDVEnX6FWK0Ru2sAZTsbCOa	SECs1Iiol3	2026-08-05 17:46:54	2026-08-05 17:46:54
4	Budi Santoso, M.Kom	0407088403@dosen.stmikbandung.ac.id	0407088403	dosen	2026-08-05 17:46:54	$2y$12$OQMzEpUjpPzRAJlzQE1ZLOj9K05EM/BvbmOXp7BUDxXX41jaW/A0y	GSu0KVADSW	2026-08-05 17:46:54	2026-08-05 17:46:54
5	Rina Wulandari, S.Kom., M.Cs	0427128804@dosen.stmikbandung.ac.id	0427128804	dosen	2026-08-05 17:46:54	$2y$12$3fz6A2U1CY4X/IgJlA4Zouww5euGN0doRsV9OEbeKBF6bHmeLlxJO	wuwfke07HF	2026-08-05 17:46:55	2026-08-05 17:46:55
6	Andi Pratama	20221001@student.stmikbandung.ac.id	20221001	mahasiswa	2026-08-05 17:46:55	$2y$12$ykmAraGhMFsOP7zn7Gcd9O7v84i2SeRiXtEk57hn0ZkoAI1A370Hy	0q33KiebF2	2026-08-05 17:46:55	2026-08-05 17:46:55
7	Budi Hartono	20221002@student.stmikbandung.ac.id	20221002	mahasiswa	2026-08-05 17:46:55	$2y$12$zflZhO3gnfgUNf98E3gnZ.RsfMXuqOzr3OUylLMHnq/NRJMhwAwZW	7NkF70zSrX	2026-08-05 17:46:55	2026-08-05 17:46:55
8	Citra Lestari	20221003@student.stmikbandung.ac.id	20221003	mahasiswa	2026-08-05 17:46:55	$2y$12$uaMJyarer6vrAW13IICdj.FCag1xKi7Emt1QMteIvC/l./eV7z/xO	3rpj1hgvM0	2026-08-05 17:46:56	2026-08-05 17:46:56
9	Dewi Anggraini	20221004@student.stmikbandung.ac.id	20221004	mahasiswa	2026-08-05 17:46:56	$2y$12$8mgz2jrL.vy52OxlH1UeA.dB9qhqd/YmI6N67CzkO2OkfOMNXR8DC	nXwXGO7CUo	2026-08-05 17:46:56	2026-08-05 17:46:56
10	Eko Saputra	20221005@student.stmikbandung.ac.id	20221005	mahasiswa	2026-08-05 17:46:56	$2y$12$ubfQPYxiYSA.d9TnQJIEle4FUbo4x.3c7lGP6OEzNYFHLF6HUND.i	lGK7ElvuHS	2026-08-05 17:46:56	2026-08-05 17:46:56
11	Fitri Handayani	20221006@student.stmikbandung.ac.id	20221006	mahasiswa	2026-08-05 17:46:56	$2y$12$nKvg1tdr3Xjffuv3KoAIg.7YajRJDcJEYv/jCeQrU3a83hOJrC1KS	Lv9MWORjdV	2026-08-05 17:46:56	2026-08-05 17:46:56
12	Gilang Ramadhan	20231001@student.stmikbandung.ac.id	20231001	mahasiswa	2026-08-05 17:46:56	$2y$12$AudbMu5PHoLGJfcWKX2taeCna6hG.tTTHv8O4CFJ1U06/OBbYsy.q	gqXE3arJlW	2026-08-05 17:46:57	2026-08-05 17:46:57
13	Hesti Puspita	20231002@student.stmikbandung.ac.id	20231002	mahasiswa	2026-08-05 17:46:57	$2y$12$8.9N2TdfWZReNmvF09sHM.ncOgJsuYE30X.icJZ9dUceEbkd02uZi	cHb8ztixo8	2026-08-05 17:46:57	2026-08-05 17:46:57
14	Indra Wijaya	20231003@student.stmikbandung.ac.id	20231003	mahasiswa	2026-08-05 17:46:57	$2y$12$dUEyUEEwEUnCmRceKqPvXe3NXA/bFeg7vMWaGXWHOG93MDAnWDuay	a5Xu1jdn2t	2026-08-05 17:46:57	2026-08-05 17:46:57
15	Joko Susilo	20231004@student.stmikbandung.ac.id	20231004	mahasiswa	2026-08-05 17:46:57	$2y$12$IPYQroW5FifNPtPPW9IDM.bnu9WKdfBBiwakyCH5lWXkSPY9X99FC	vOIiC3370L	2026-08-05 17:46:58	2026-08-05 17:46:58
16	Laila Nurjanah	20241001@student.stmikbandung.ac.id	20241001	mahasiswa	2026-08-05 17:46:58	$2y$12$ZKv0FbvwyrmVd4ybxMpXouy9beSKV2x1LIbNn08Lb5Rp.qSD05KWO	SyUpc7zJ6d	2026-08-05 17:46:58	2026-08-05 17:46:58
17	Muhammad Rizki	20241002@student.stmikbandung.ac.id	20241002	mahasiswa	2026-08-05 17:46:58	$2y$12$3kl7RPhYiBOvBbfanCTnUuprrd3EWVmXahQnFmpwYY.EyO40BZmuO	WkA4I8Ifmf	2026-08-05 17:46:58	2026-08-05 17:46:58
\.


--
-- Name: dosen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.dosen_id_seq', 5, true);


--
-- Name: dosen_wali_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.dosen_wali_id_seq', 13, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: mahasiswa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.mahasiswa_id_seq', 13, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 7, true);


--
-- Name: perwalian_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.perwalian_id_seq', 20, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 19, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: dosen dosen_nidn_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen
    ADD CONSTRAINT dosen_nidn_unique UNIQUE (nidn);


--
-- Name: dosen dosen_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen
    ADD CONSTRAINT dosen_pkey PRIMARY KEY (id);


--
-- Name: dosen_wali dosen_wali_mahasiswa_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen_wali
    ADD CONSTRAINT dosen_wali_mahasiswa_id_unique UNIQUE (mahasiswa_id);


--
-- Name: dosen_wali dosen_wali_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen_wali
    ADD CONSTRAINT dosen_wali_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: mahasiswa mahasiswa_npm_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mahasiswa
    ADD CONSTRAINT mahasiswa_npm_unique UNIQUE (npm);


--
-- Name: mahasiswa mahasiswa_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mahasiswa
    ADD CONSTRAINT mahasiswa_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: perwalian perwalian_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.perwalian
    ADD CONSTRAINT perwalian_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_username_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_unique UNIQUE (username);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: dosen dosen_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen
    ADD CONSTRAINT dosen_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: dosen_wali dosen_wali_dosen_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen_wali
    ADD CONSTRAINT dosen_wali_dosen_id_foreign FOREIGN KEY (dosen_id) REFERENCES public.dosen(id) ON DELETE CASCADE;


--
-- Name: dosen_wali dosen_wali_mahasiswa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dosen_wali
    ADD CONSTRAINT dosen_wali_mahasiswa_id_foreign FOREIGN KEY (mahasiswa_id) REFERENCES public.mahasiswa(id) ON DELETE CASCADE;


--
-- Name: mahasiswa mahasiswa_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mahasiswa
    ADD CONSTRAINT mahasiswa_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: perwalian perwalian_mahasiswa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.perwalian
    ADD CONSTRAINT perwalian_mahasiswa_id_foreign FOREIGN KEY (mahasiswa_id) REFERENCES public.mahasiswa(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict gBFu40gCsN5oyY6mOzXQoditIDesBermpBmb9qrXh6zsExcq5QE9e7vJKV3Lf5m

