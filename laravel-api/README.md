# 🚀 Deployment Guide: Linguist API

This document provides technical instructions for replicating the production environment of the **Linguist REST API**. Our deployment utilizes a containerized architecture to ensure environment parity and high reliability.

## 📦 1. Required Components

To deploy this system, the following must be installed on the host machine (VPS):

* **Operating System:** Ubuntu 22.04 LTS or higher.
* **Engine:** Docker Engine (v24.0+) & Docker Compose (v2.20+).
* **Infrastructure:** Minimum 4GB RAM / 2 vCPU (required for LibreTranslate models).
* **Network:** Ports `80` (HTTP) and `443` (HTTPS) must be open on the firewall.

---

## 🛠 2. Environment Setup

### 1. Repository Preparation

Clone the backend repository and navigate to the root:

```bash
git clone https://github.com/FaizanTanwir/PWP-2026_Team_Async-4
cd backend

```

### 2. Configuration (Secrets)

Create the production environment file. You must update the database credentials and the `APP_URL`.

```bash
cp .env.example .env
nano .env

```

*Key variables to set:* `APP_ENV=production`, `APP_DEBUG=false`, `DB_PASSWORD`, and `TRANSLATOR_URL=http://libretranslate:5000`.

---

## 🚢 3. Deploying the Web API

We use **Docker Compose** to orchestrate the ecosystem. The deployment is fully scripted to minimize manual errors.

### 1. Launch Containers

Run the following command to build and start all services in detached mode:

```bash
docker compose up -d --build

```

*This command starts: Laravel (PHP-FPM), MySQL, Nginx Proxy Manager, and LibreTranslate.*

### 2. Initialize Application

The project already has `docker-compose/entrypoint.sh` script to initialize the application. It runs automatically when the containers are run

---

## 🔒 4. SSL Certificates & HTTPS

We use **Nginx Proxy Manager (NPM)** to handle security and certificates.

* **Software:** Nginx Proxy Manager (Dockerized).
* **Maintenance:** Automated via **Let's Encrypt** (integrated into NPM).
* **Setup:**
1. Access the NPM dashboard at `http://<your-vps-ip>:81`.
2. Add a **Proxy Host** for your domain (e.g., `api.linguist.com`).
3. Set the Forward IP to `app` and Port to `8000`.
4. Under the **SSL** tab, select "Request a new SSL Certificate."



---

## ✅ 5. Verification & Testing

To check if the environment is properly configured, run these tests:

### 1. Container Health Check

Verify all services are `up` and running:

```bash
docker compose ps

```

### 2. Automated Feature Tests

Run the internal test suite to ensure the API logic is intact in the production environment:

```bash
docker compose exec app ./vendor/bin/phpunit

```

### 3. Linting & Code Style Check

Verify that the code adheres to the PSR-12 standard using **Laravel Pint**:

```bash
docker compose exec app ./vendor/bin/pint --test

```

### 4. API Documentation Connectivity

Navigate to `https://<your-domain>/docs/api` to verify that **Scramble** is correctly serving the OpenAPI documentation.
Navigate to `https://<your-domain>/docs/api.json` to get the json representation of the OpenAPI documentation.

---

## ✍️ 6. Attribution & Authorship

| Category | Description | Source |
| --- | --- | --- |
| **Core API Logic** | Controllers, Models, Migrations, and Business Logic. | **Implemented by Team** |
| **Infrastructure** | Docker Compose orchestration and Nginx configuration. | **Implemented by Team** |
| **Linting Setup** | Laravel Pint configuration. | **Course Materials / Official Docs** |
| **Documentation** | Scramble integration and README structure. | **Implemented with AI Assistance** |
| **Translation Engine** | LibreTranslate Container. | **Open Source (LibreTranslate)** |
