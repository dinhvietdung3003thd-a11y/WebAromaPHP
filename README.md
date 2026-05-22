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

---

# Database

The project uses the existing SQL dump.

Database:
- aromacafedb

---

# Elasticsearch

## Sync products to Elasticsearch

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