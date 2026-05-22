# Docker Environment Context

## Existing Docker Services

The existing system already uses Docker Desktop.

The Laravel backend should reuse the current MySQL and Elasticsearch containers.

---

# MySQL

## Image
mysql:8.0

## Ports
Host: 3307
Container: 3306

## Database
aromacafedb

## Credentials

Username:
root

Password:
123456

---

# Elasticsearch

## Port
9200

## Example URL
http://localhost:9200

---

# Existing Usage

The old ASP.NET backend already uses:
- MySQL
- Elasticsearch

The new Laravel backend should connect to the same services.

---

# Laravel .env Example

If Laravel runs OUTSIDE Docker:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=aromacafedb
DB_USERNAME=root
DB_PASSWORD=123456

ELASTICSEARCH_HOST=http://localhost:9200

---

If Laravel runs INSIDE Docker:

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=aromacafedb
DB_USERNAME=root
DB_PASSWORD=123456

ELASTICSEARCH_HOST=http://elasticsearch:9200

---

# Docker Goals

The Laravel backend should:
- Work with Docker
- Reuse existing containers
- Not destroy existing data
- Not conflict with the old ASP.NET backend

---

# Recommended Laravel Port

Laravel API:
http://localhost:8000/api

---

# Existing Frontend Ports

## CMS page
Usually:
- localhost:5500
- 127.0.0.1:5500

## USER page
Usually:
- localhost:5173

---

# Required CORS

The Laravel backend should allow:

http://localhost:5500
http://127.0.0.1:5500
http://localhost:5173