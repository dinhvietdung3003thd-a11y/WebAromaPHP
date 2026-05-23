# Aroma Coffee Laravel Backend

## Introduction

This project is a Laravel PHP backend for the Aroma Coffee management system.

It replaces the old ASP.NET Core backend while keeping compatibility with:
- Existing database
- Existing frontend projects
- Existing Elasticsearch integration

---

# Technologies

- Laravel
- PHP
- MySQL
- Elasticsearch
- Docker

---

# Features

- Authentication
- Product management
- Category management
- Order management
- Inventory management
- Recipe management
- Supplier management
- Table management
- Elasticsearch product search

---

# Requirements

- Docker Desktop
- PHP
- Composer

---

# Run With Docker

## Start containers

```bash
docker compose up --build
```

---

# Laravel Setup

## Install dependencies

```bash
composer install
```

---

## Copy env

```bash
cp .env.example .env
```

---

## Generate app key

```bash
php artisan key:generate
```

## Generate JWT secret

```bash
php artisan jwt:secret
```

---

# Database

The project uses the existing SQL dump.

Database:
- aromacafedb

---

# Elasticsearch

## Sync products to Elasticsearch

Set the Elasticsearch host in `.env` (or keep default):

```env
ELASTICSEARCH_HOST=http://localhost:9200
ELASTICSEARCH_PRODUCT_INDEX=products
```

Run:

```bash
php artisan elastic:sync-products
```

---

# API Base URL

```text
http://localhost:8000/api
```

---

# Frontend Compatibility

Compatible with:
- CMS page
- USER page

Only minimal frontend changes should be needed.

Usually:
- API base URL update only

---

# Existing Frontend Ports

## CMS page
- localhost:5500
- 127.0.0.1:5500

## USER page
- localhost:5173

---

# CORS

The backend should allow:

- http://localhost:5500
- http://127.0.0.1:5500
- http://localhost:5173

---

# Elasticsearch Search Example

GET:

```text
/api/Product/search-elastic?keyword=coffee
```

---

# Important Notes

- Do not modify the old ASP.NET backend
- Keep database compatibility
- Keep API compatibility
- Reuse existing Docker services where possible

# Docker Quick Start

## 1) Build and start services

```bash
docker compose up --build -d
```

This starts:
- `laravel-api` on `http://localhost:8000`
- `mysql` on `127.0.0.1:3307` (container `3306`)
- `elasticsearch` on `http://localhost:9200`

## 2) Database initialization

MySQL is initialized from the root SQL dump file:
- `Dump20260327.sql` -> `/docker-entrypoint-initdb.d/Dump20260327.sql`

Database settings:
- Database: `aromacafedb`
- Username: `root`
- Password: `123456`

> Note: the SQL dump runs automatically only on the first MySQL startup with an empty data volume.

## 3) Check logs

```bash
docker compose logs -f laravel-api
```

## 4) Stop services

```bash
docker compose down
```

If you need to re-run SQL initialization from scratch:

```bash
docker compose down -v
```



# API Testing Quick Guide (Thunder Client / Postman)

Use `{{baseUrl}} = http://localhost:8000/api` and placeholders only:
- `{{adminToken}}` = `<ADMIN_JWT_TOKEN>`
- `{{customerToken}}` = `<CUSTOMER_JWT_TOKEN>`

## 1) Admin login

`POST {{baseUrl}}/Auth/login`

```json
{
  "username": "<ADMIN_USERNAME>",
  "password": "<ADMIN_PASSWORD>"
}
```

## 2) Customer login

`POST {{baseUrl}}/Auth/customer/login`

```json
{
  "username": "<CUSTOMER_USERNAME>",
  "password": "<CUSTOMER_PASSWORD>"
}
```

## 3) Auth me

`GET {{baseUrl}}/Auth/me` with header `Authorization: Bearer {{adminToken}}`

## 4) Create order (Admin/Staff)

`POST {{baseUrl}}/Orders` with header `Authorization: Bearer {{adminToken}}`

```json
{
  "orderDate": "2026-05-23T10:00:00",
  "tableId": 1,
  "note": "No ice",
  "details": [
    { "productId": 1, "quantity": 2 }
  ]
}
```

## 5) Create order (Customer)

`POST {{baseUrl}}/client/orders` with header `Authorization: Bearer {{customerToken}}`

```json
{
  "orderDate": "2026-05-23T10:00:00",
  "tableId": null,
  "note": "Less sugar",
  "details": [
    { "productId": 1, "quantity": 1 }
  ]
}
```

## 6) Inventory import/export

`POST {{baseUrl}}/InventoryTransaction` with header `Authorization: Bearer {{adminToken}}`

Import example:
```json
{
  "inventoryId": 1,
  "type": "Import",
  "quantity": 10,
  "note": "Restock"
}
```

Export example:
```json
{
  "inventoryId": 1,
  "type": "Export",
  "quantity": 2,
  "note": "Usage"
}
```

## 7) Recipe duplicate validation

Send duplicate `productId` + `inventoryId` via `POST {{baseUrl}}/Recipe` and verify a clean `422` JSON response.

## 8) Customer forbidden on admin route

Call `POST {{baseUrl}}/Product` using `Authorization: Bearer {{customerToken}}` and verify `403` JSON:

```json
{
  "message": "Forbidden"
}
```
