# API Contract

This file documents the old backend API structure.
The new Laravel backend should keep compatibility as much as possible.

---

# Authentication APIs

## Admin Login

POST /api/Auth/login

Request:
```json
{
  "username": "admin",
  "password": "123456"
}
```

Response:
```json
{
  "userId": 1,
  "fullName": "Admin",
  "role": "Admin",
  "token": "jwt_token"
}
```

---

## Customer Login

POST /api/Auth/customer/login

Request:
```json
{
  "username": "customer",
  "password": "123456"
}
```

Response:
```json
{
  "customerId": 1,
  "fullName": "Customer Name",
  "loyaltyPoints": 100,
  "role": "Customer",
  "token": "jwt_token"
}
```

---

## Customer Register

POST /api/Auth/customer/register

Request:
```json
{
  "username": "customer",
  "password": "123456",
  "fullName": "Customer Name",
  "phoneNumber": "0123456789",
  "email": "customer@gmail.com"
}
```

Response:
```json
{
  "message": "Register successful"
}
```

---

# Category APIs

## Get Categories

GET /api/Categories

Response:
```json
[
  {
    "categoryId": 1,
    "name": "Coffee",
    "description": "Coffee products"
  }
]
```

---

# Product APIs

## Get Products

GET /api/Product

Response:
```json
[
  {
    "productId": 1,
    "name": "Milk Coffee",
    "price": 30000,
    "imageUrl": "",
    "isAvailable": true,
    "categoryId": 1,
    "description": "Vietnamese milk coffee",
    "categoryName": "Coffee"
  }
]
```

---

## Get Product By Id

GET /api/Product/{id}

---

## Create Product

POST /api/Product

---

## Update Product

PUT /api/Product/{id}

---

## Delete Product

DELETE /api/Product/{id}

---

# Elasticsearch Search

## Product Search

GET /api/Product/search-elastic?keyword=coffee

Response:
```json
[
  {
    "productId": 1,
    "name": "Milk Coffee",
    "price": 30000,
    "imageUrl": "",
    "isAvailable": true,
    "categoryId": 1,
    "description": "Vietnamese milk coffee",
    "categoryName": "Coffee"
  }
]
```

---

# Order APIs

## Create Customer Order

POST /api/client/orders

Headers:
Authorization: Bearer <token>

Request:
```json
{
  "orderDate": "2026-05-22T10:00:00",
  "tableId": null,
  "note": "Less sugar",
  "details": [
    {
      "productId": 1,
      "quantity": 2
    }
  ]
}
```

Response:
```json
{
  "orderId": 1
}
```

---

## Get Orders

GET /api/Orders

---

## Get Order Detail

GET /api/Orders/{id}

---

# Inventory APIs

## Get Inventory

GET /api/Inventory

---

## Create Inventory

POST /api/Inventory

---

## Update Inventory

PUT /api/Inventory/{id}

---

## Delete Inventory

DELETE /api/Inventory/{id}

---

# Inventory Transaction APIs

## Get Inventory Transactions

GET /api/InventoryTransaction

---

## Create Inventory Transaction

POST /api/InventoryTransaction

---

# Recipe APIs

## Get Recipes

GET /api/Recipe

---

## Create Recipe

POST /api/Recipe

---

## Update Recipe

PUT /api/Recipe/{id}

---

## Delete Recipe

DELETE /api/Recipe/{id}

---

# Supplier APIs

## Get Suppliers

GET /api/Supplier

---

## Create Supplier

POST /api/Supplier

---

## Update Supplier

PUT /api/Supplier/{id}

---

## Delete Supplier

DELETE /api/Supplier/{id}

---

# Table APIs

## Get Tables

GET /api/Tables

---

## Create Table

POST /api/Tables

---

## Update Table

PUT /api/Tables/{id}

---

## Delete Table

DELETE /api/Tables/{id}

---

# Error Handling

## Unauthorized

HTTP 401

```json
{
  "message": "Unauthorized"
}
```

---

## Validation Error

HTTP 400

```json
{
  "message": "Validation failed"
}
```

---

## Forbidden

HTTP 403

```json
{
  "message": "Forbidden"
}
```