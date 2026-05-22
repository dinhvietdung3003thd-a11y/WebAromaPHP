# Aroma Coffee - Project Context

## Project Goal

This project is a coffee shop management system.

The original backend was built using ASP.NET Core Web API.
A new backend will now be rebuilt completely using Laravel PHP.

The goal is:
- Keep API compatibility with the old system as much as possible
- Reuse the existing MySQL database
- Reuse Elasticsearch
- Reuse the existing frontend projects
- Do NOT redesign frontend logic
- Do NOT change database structure unless necessary

---

# Existing System

## Old Backend
- ASP.NET Core Web API
- Dapper ORM
- MySQL
- Elasticsearch using Nest client
- JWT Authentication

## Frontends

### CMS page
Admin management frontend.

Technologies:
- HTML
- CSS
- JavaScript

Features:
- Admin login
- Dashboard
- Product management
- Category management
- Order management
- Inventory management
- Recipe management
- Supplier management
- Table management

### USER page
Customer frontend.

Technologies:
- React
- Vite
- TypeScript

Features:
- Customer register
- Customer login
- Product browsing
- Product search
- Cart
- Order creation

---

# New Backend Requirements

The new backend must:
- Be built completely in Laravel PHP
- Be created in a separate repository
- NOT modify the old ASP.NET backend
- Keep endpoint naming compatible
- Keep JSON response fields compatible
- Keep MySQL schema compatible
- Use Elasticsearch for search
- Use Docker for MySQL and Elasticsearch

---

# Existing Technologies

## Database
MySQL

## Search Engine
Elasticsearch

## Containers
Docker Desktop

---

# Existing Docker Environment

## MySQL
- Container port: 3306
- Host port: 3307

## Elasticsearch
- Port: 9200

---

# Authentication

The old system uses:
- JWT token authentication
- Authorization: Bearer <token>

The Laravel backend should implement compatible authentication.

---

# Existing API Style

Endpoints commonly use:
- /api/Product
- /api/Categories
- /api/Auth/login
- /api/Auth/customer/login
- /api/client/orders

Response fields commonly use camelCase:
- productId
- categoryId
- customerId
- imageUrl
- loyaltyPoints

---

# Important Rules

- Do not redesign the frontend
- Do not rename existing database tables
- Do not rename existing columns
- Do not create unnecessary new architecture
- Keep compatibility with the old frontend
- Keep API JSON shape similar to the old backend

---

# Main Features

## Authentication
- Admin login
- Customer login
- Customer register
- Change password

## Product
- CRUD products
- Product search using Elasticsearch

## Category
- CRUD categories

## Orders
- Customer create order
- Admin manage orders

## Inventory
- CRUD inventory
- Inventory transactions

## Recipe
- CRUD recipe

## Supplier
- CRUD supplier

## Table
- CRUD table

---

# Elasticsearch

Products are indexed into Elasticsearch.

The Laravel backend should provide:
- Product indexing
- Product search
- Elasticsearch sync command

Example:
php artisan elastic:sync-products

---

# Frontend Compatibility

The Laravel backend should support:
- Existing CMS page frontend
- Existing USER page frontend

Minimal frontend changes are preferred.

Usually only API base URL changes should be needed.

Example:
http://localhost:8000/api