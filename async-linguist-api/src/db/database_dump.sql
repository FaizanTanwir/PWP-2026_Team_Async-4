--
-- PostgreSQL database dump
--

\restrict neNCbDybHmUidJaOYPq01potU5U2mLZ0pZKTWeSGOWjuFpqhG5sXaA0NeCsZeHj

-- Dumped from database version 15.15 (Debian 15.15-1.pgdg13+1)
-- Dumped by pg_dump version 15.15 (Debian 15.15-1.pgdg13+1)

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
-- Name: attempts; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.attempts (
    id integer NOT NULL,
    audio_url text NOT NULL,
    score double precision DEFAULT '0'::double precision NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    "sentenceId" integer
);


ALTER TABLE public.attempts OWNER TO "user";

--
-- Name: attempts_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.attempts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.attempts_id_seq OWNER TO "user";

--
-- Name: attempts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.attempts_id_seq OWNED BY public.attempts.id;


--
-- Name: courses; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.courses (
    id integer NOT NULL,
    title character varying NOT NULL,
    source_language_id integer,
    target_language_id integer
);


ALTER TABLE public.courses OWNER TO "user";

--
-- Name: courses_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.courses_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.courses_id_seq OWNER TO "user";

--
-- Name: courses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.courses_id_seq OWNED BY public.courses.id;


--
-- Name: languages; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.languages (
    id integer NOT NULL,
    name character varying NOT NULL,
    code character varying(10) NOT NULL
);


ALTER TABLE public.languages OWNER TO "user";

--
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.languages_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.languages_id_seq OWNER TO "user";

--
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.languages_id_seq OWNED BY public.languages.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    "timestamp" bigint NOT NULL,
    name character varying NOT NULL
);


ALTER TABLE public.migrations OWNER TO "user";

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO "user";

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: sentence_words; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.sentence_words (
    sentence_id integer NOT NULL,
    word_id integer NOT NULL
);


ALTER TABLE public.sentence_words OWNER TO "user";

--
-- Name: sentences; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.sentences (
    id integer NOT NULL,
    text_target text NOT NULL,
    text_source text NOT NULL,
    "unitId" integer
);


ALTER TABLE public.sentences OWNER TO "user";

--
-- Name: sentences_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.sentences_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sentences_id_seq OWNER TO "user";

--
-- Name: sentences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.sentences_id_seq OWNED BY public.sentences.id;


--
-- Name: units; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.units (
    id integer NOT NULL,
    title character varying NOT NULL,
    "courseId" integer
);


ALTER TABLE public.units OWNER TO "user";

--
-- Name: units_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.units_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.units_id_seq OWNER TO "user";

--
-- Name: units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.units_id_seq OWNED BY public.units.id;


--
-- Name: words; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.words (
    id integer NOT NULL,
    term character varying NOT NULL,
    lemma character varying,
    translation character varying
);


ALTER TABLE public.words OWNER TO "user";

--
-- Name: words_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.words_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.words_id_seq OWNER TO "user";

--
-- Name: words_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.words_id_seq OWNED BY public.words.id;


--
-- Name: attempts id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.attempts ALTER COLUMN id SET DEFAULT nextval('public.attempts_id_seq'::regclass);


--
-- Name: courses id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.courses ALTER COLUMN id SET DEFAULT nextval('public.courses_id_seq'::regclass);


--
-- Name: languages id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.languages ALTER COLUMN id SET DEFAULT nextval('public.languages_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: sentences id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.sentences ALTER COLUMN id SET DEFAULT nextval('public.sentences_id_seq'::regclass);


--
-- Name: units id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.units ALTER COLUMN id SET DEFAULT nextval('public.units_id_seq'::regclass);


--
-- Name: words id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.words ALTER COLUMN id SET DEFAULT nextval('public.words_id_seq'::regclass);


--
-- Data for Name: attempts; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.attempts (id, audio_url, score, created_at, "sentenceId") FROM stdin;
\.


--
-- Data for Name: courses; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.courses (id, title, source_language_id, target_language_id) FROM stdin;
1	Finnish for Beginners	1	2
\.


--
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.languages (id, name, code) FROM stdin;
1	English	en
2	Finnish	fi
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.migrations (id, "timestamp", name) FROM stdin;
1	1770732805819	InitialSchema1770732805819
\.


--
-- Data for Name: sentence_words; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.sentence_words (sentence_id, word_id) FROM stdin;
1	1
1	2
2	3
2	4
3	5
3	6
4	7
4	8
\.


--
-- Data for Name: sentences; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.sentences (id, text_target, text_source, "unitId") FROM stdin;
1	Hyvää huomenta	Good morning	1
2	Mitä kuuluu?	How are you?	1
3	Kiitos paljon	Thank you very much	1
4	Hauska tavata	Nice to meet you	1
\.


--
-- Data for Name: units; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.units (id, title, "courseId") FROM stdin;
1	Greetings & Basics	1
\.


--
-- Data for Name: words; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.words (id, term, lemma, translation) FROM stdin;
1	Hyvää	\N	\N
2	huomenta	\N	\N
3	Mitä	\N	\N
4	kuuluu	\N	\N
5	Kiitos	\N	\N
6	paljon	\N	\N
7	Hauska	\N	\N
8	tavata	\N	\N
\.


--
-- Name: attempts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.attempts_id_seq', 1, false);


--
-- Name: courses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.courses_id_seq', 1, true);


--
-- Name: languages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.languages_id_seq', 2, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 1, true);


--
-- Name: sentences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.sentences_id_seq', 4, true);


--
-- Name: units_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.units_id_seq', 1, true);


--
-- Name: words_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.words_id_seq', 8, true);


--
-- Name: sentence_words PK_1276c9bb245dd6ca5ac0b788839; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.sentence_words
    ADD CONSTRAINT "PK_1276c9bb245dd6ca5ac0b788839" PRIMARY KEY (sentence_id, word_id);


--
-- Name: attempts PK_295ca261e361fd2fd217754dcac; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.attempts
    ADD CONSTRAINT "PK_295ca261e361fd2fd217754dcac" PRIMARY KEY (id);


--
-- Name: courses PK_3f70a487cc718ad8eda4e6d58c9; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.courses
    ADD CONSTRAINT "PK_3f70a487cc718ad8eda4e6d58c9" PRIMARY KEY (id);


--
-- Name: units PK_5a8f2f064919b587d93936cb223; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.units
    ADD CONSTRAINT "PK_5a8f2f064919b587d93936cb223" PRIMARY KEY (id);


--
-- Name: migrations PK_8c82d7f526340ab734260ea46be; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT "PK_8c82d7f526340ab734260ea46be" PRIMARY KEY (id);


--
-- Name: sentences PK_9b3aec16318cf425aaddad6dd5f; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.sentences
    ADD CONSTRAINT "PK_9b3aec16318cf425aaddad6dd5f" PRIMARY KEY (id);


--
-- Name: languages PK_b517f827ca496b29f4d549c631d; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT "PK_b517f827ca496b29f4d549c631d" PRIMARY KEY (id);


--
-- Name: words PK_feaf97accb69a7f355fa6f58a3d; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.words
    ADD CONSTRAINT "PK_feaf97accb69a7f355fa6f58a3d" PRIMARY KEY (id);


--
-- Name: languages UQ_7397752718d1c9eb873722ec9b2; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT "UQ_7397752718d1c9eb873722ec9b2" UNIQUE (code);


--
-- Name: languages UQ_9c0e155475f0aa782e4a6178969; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT "UQ_9c0e155475f0aa782e4a6178969" UNIQUE (name);


--
-- Name: attempts FK_23d05c1d12a3cff6c5f37aecfc8; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.attempts
    ADD CONSTRAINT "FK_23d05c1d12a3cff6c5f37aecfc8" FOREIGN KEY ("sentenceId") REFERENCES public.sentences(id) ON DELETE CASCADE;


--
-- Name: units FK_488039d526ad82da05a60e93556; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.units
    ADD CONSTRAINT "FK_488039d526ad82da05a60e93556" FOREIGN KEY ("courseId") REFERENCES public.courses(id) ON DELETE CASCADE;


--
-- Name: courses FK_7e86af9e306ba2e3e6a9311852e; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.courses
    ADD CONSTRAINT "FK_7e86af9e306ba2e3e6a9311852e" FOREIGN KEY (target_language_id) REFERENCES public.languages(id) ON DELETE RESTRICT;


--
-- Name: sentence_words FK_beb95a0e4b0c0849a135df3e6a6; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.sentence_words
    ADD CONSTRAINT "FK_beb95a0e4b0c0849a135df3e6a6" FOREIGN KEY (sentence_id) REFERENCES public.sentences(id) ON DELETE CASCADE;


--
-- Name: sentences FK_cc85373a64bcde109581205c9db; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.sentences
    ADD CONSTRAINT "FK_cc85373a64bcde109581205c9db" FOREIGN KEY ("unitId") REFERENCES public.units(id) ON DELETE CASCADE;


--
-- Name: sentence_words FK_d680af839a5695c4c1c41e22a16; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.sentence_words
    ADD CONSTRAINT "FK_d680af839a5695c4c1c41e22a16" FOREIGN KEY (word_id) REFERENCES public.words(id) ON DELETE CASCADE;


--
-- Name: courses FK_ff0f83e9edee6868c06228c5121; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.courses
    ADD CONSTRAINT "FK_ff0f83e9edee6868c06228c5121" FOREIGN KEY (source_language_id) REFERENCES public.languages(id) ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

\unrestrict neNCbDybHmUidJaOYPq01potU5U2mLZ0pZKTWeSGOWjuFpqhG5sXaA0NeCsZeHj

