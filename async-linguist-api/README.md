# 🌍 AsyncLinguist API - Deadline 2

Database design and implementation using Nestjs and Postgres.

---

## 🏗️ Database Design Summary

System follows a structured relational model designed to support pedagogical flows.

### Entities Overview
* **Language**: Stores supported languages (e.g., Finnish, English).
* **Course**: Connects a source and target language. Uses `RESTRICT` on delete to maintain data integrity.
* **Unit**: Groups learning materials into thematic sections (e.g., "Basics").
* **Sentence**: The core learning object containing source and target strings.
* **Word**: Vocabulary items extracted from sentences (includes lemmas/translations).
* **SentenceWord**: A many-to-many junction table linking sentences to their vocabulary.
* **Attempt**: Records student performance (audio URLs and pronunciation scores). Linked via `CASCADE` to sentences.

---

## 🛠️ Dependency Information

- **Runtime**: Node.js (v18+)
- **Framework**: NestJS
- **ORM**: TypeORM
- **Database**: PostgreSQL 15 (Dockerized)
- **Key Dependencies**:
  - `pg`: PostgreSQL driver
  - `@nestjs/typeorm`: TypeORM integration for NestJS
  - `@nestjs/config`: Environment variable management
  - `dotenv`: Environment loader for standalone scripts (migrations/seeding)

---

## 🚀 Getting Started

Follow these steps to set up the environment and verify the implementation.

### 1. Environment Configuration
Create a `.env` file in the root directory (you can copy `.env.sample`):
```env
DB_HOST=localhost
DB_PORT=5432
DB_USERNAME=user
DB_PASSWORD=password
DB_NAME=async_linguist
```

### 2. Launch Infrastructure (Docker)
Start the PostgreSQL database and pgAdmin using Docker Compose:

```
docker-compose up -d
```

PostgreSQL: Available on localhost:5432

- **PostgreSQL:** Available on `localhost:5432`
- **pgAdmin:** Accessible at http://localhost:8080 (Login: `admin@admin.com` / `admin`)

### 3. Install Packages

```
npm install
```

### 4. Database Setup & Population
To generate the schema and see instances of all models, run the following commands:

**Apply Schema (Migrations):**
```
npm run migration:run
```

**Populate Data (Seeding):**
```
npm run seed
```

---

## 🔍 Verification (pgAdmin)


1- Open pgAdmin at `http://localhost:8080`

2- Add a new server with the following settings:

  - Host: `db` (or `localhost` if connecting from host)
  - Port: `5432`
  - Username: `user`
  - Password: `password`

3- Expand **Databases > async_linguist > Schemas > public > Tables**

4- You will see all 7 tables populated with initial Finnish/English learning data.

---

## 🤖 Use of AI

AI (Gemini) was utilized during this stage to:

- Debugging Docker networking issues between the API and PostgreSQL

- Creating the setup document (i.e `README.md`) file for the project

- Dubugging some typescript related issues.