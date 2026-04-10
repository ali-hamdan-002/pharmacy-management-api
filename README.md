# 💊 Pharmacy Management System API

This is a robust Backend API built with **Laravel 10** for managing pharmacy operations. The system handles complex inventory tracking, automated order processing lifecycles, and advanced financial reporting.

---

## 🌟 Project Overview

The project was developed with a focus on **Data Integrity** and **Business Logic**. It ensures that no medical stock is lost or incorrectly accounted for by implementing strict order status transitions and automated stock management.

## 🚀 Key Features

### 📦 Order Lifecycle Management
* **Sequential Processing:** Orders follow a strict path: `Pending` -> `Shipped` -> `Delivered`.
* **Validation Rules:** * Orders cannot be marked as **Delivered** unless they have been **Shipped** first.
    * **Cancellation** is only allowed for `Pending` orders; once an order is shipped, the action is irreversible.
* **Payment Guard:** Payment status can only be updated to `Paid` for orders that have reached the `Shipped` or `Delivered` stage.

### 🧪 Smart Inventory Control
* **Auto-Deduction:** Stock levels are automatically decremented from the drugs table the moment an order is marked as `Shipped`.
* **Availability Check:** The system verifies stock availability for every item in an order before allowing the shipping process to proceed.

### 📊 Financial & Loss Reporting
* **Weekly Analytics:** Generates reports showing Total Revenue, Max, and Min order values for the last 7 days.
* **Expired Meds Tracking:** Calculates financial losses based on the quantity and price of expired medicines.

---

## 🛠️ Tech Stack

* **Framework:** Laravel 10 (PHP 8.x)
* **Database:** MySQL
* **Tools:** Carbon (Time Management), Eloquent ORM.

---

## 🔌 Core API Endpoints

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/api/admin_sent_order` | Validate stock, calculate final price, and ship the order. |
| `POST` | `/api/admin_received_order` | Transition status from `Shipped` to `Delivered`. |
| `POST` | `/api/admin_cancel_order` | Cancel pending orders only. |
| `POST` | `/api/admin_paid_order` | Update `payment_status` to `Paid` for valid orders. |
| `GET` | `/api/losses` | Calculate total financial loss from expired stock. |
| `GET` | `/api/admin_show_orders_value_report` | Fetch weekly financial stats (Total, Max, Min). |

---

## 📮 Postman Collection

To make testing easier, a Postman collection is included in this repository.

1.  Find the file: `Pharmacy_System.postman_collection.json` in the root folder.
2.  Open Postman and click **Import**.
3.  Drag and drop the JSON file.
4.  Set your environment variable `base_url` to `http://127.0.0.1:8000/api`.

---

## 📦 Installation & Setup

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/ali-hamdan-002/pharmacy-management-api.git
    ```
2.  **Install Dependencies:**
    ```bash
    composer install
    ```
3.  **Environment Setup:**
    * Copy `.env.example` to `.env`.
    * Configure your database credentials in `.env`.
    ```bash
    php artisan key:generate
    ```
4.  **Database Migration:**
    ```bash
    php artisan migrate
    ```
5.  **Run the Application:**
    ```bash
    php artisan serve
    ```

---

## 👤 Author
**Ali Hamdan**
*Computer Science & Engineering Student*

---
*This project was built to demonstrate advanced Laravel logic and database management in a healthcare context.*
