# Stock Control & Management System (SCMS)

SCMS is a premium, web-based inventory, POS, and warehouse stock control platform built using **PHP**, **MySQL**, **jQuery**, **Bootstrap**, and **Chart.js**. The application features a highly responsive, modern visual layout modeled after professional SaaS dashboards (like Vercel and Linear) with native dark mode support.

![Logo](https://www.freeiconspng.com/uploads/sales-icon-7.png)

---

## 🚀 Key Features

* 💼 **Interactive Point of Sale (POS)**: Dynamic checkout screen with real-time stock deduction, transaction invoice generation, and custom checkout quantities.
* 📦 **Purchase Orders (PO)**: Manage supply chains by drafting pending POs, viewing supplier pricing, and receiving items directly into inventory with automatic price markups.
* ⚠️ **Damaged Goods Write-Off**: Report broken or expired items with built-in stock level validations to prevent over-deducting.
* 📜 **Audit History Logs**: Comprehensive transaction logging (`stock_history`) that tracks every inventory change (Sale, Manual Add, PO Receive, Write-off) with employee signatures.
* 🤖 **Stock AI Assistant**: An embedded, zero-configuration local AI chatbot widget on every page that translates natural language queries (like *"How much money did we make today?"* or *"What items are low on stock?"*) into live database queries.
* 📊 **Interactive Analytics**: Four distinct dashboard charts (Trend chart, Categories doughnut, Top Selling items bar chart, and PO status chart) driven by live MySQL counts.
* 💾 **Data Portability**: Global **Export CSV** downloaders on data grids and customized **Print Invoice** stylesheets that hide menus/sidebars during execution.
* 🌓 **Universal Dark Mode**: High-contrast, custom slate-zinc dark theme toggler with persistent browser memory caching.

---

## 🛠️ Quick Installation Guide (Local XAMPP Setup)

### 1. Clone the Codebase
Move the cloned project files into your local XAMPP web root folder:
`C:\xampp\htdocs\stockms`

### 2. Configure Database in XAMPP
1. Open the **XAMPP Control Panel** and start both **Apache** and **MySQL**.
2. Open **phpMyAdmin** in your browser: `http://localhost/phpmyadmin`.
3. Create a new database named **`scms`**.
4. Select `scms`, go to the **Import** tab, choose the database backup from the project's `/Database` folder, and import it.
5. Import the file **`Database/migration_add_product_fields.sql`** to add the new tables (`purchase_orders`, `damaged_goods`, `stock_history`) and new columns to the `product` table.

### 3. Open the Web Application
Open your web browser and navigate to:
`http://localhost/stockms`

---

## 🔑 Login Credentials

The system supports two user access levels:

| User Type | Username | Password | Access Details |
| :--- | :--- | :--- | :--- |
| **System Admin** | `admin` | `admin` | Full permission to view dashboards, accounts, suppliers, POs, and reports. |
| **Cashier / Staff** | `user` | `user` | Restricted access. Automatically redirects to the POS checkout screen. |

---

## 📁 Key File Structure

* `/pages` - Core page files (e.g. `index.php` dashboard, `pos.php`, `purchase_orders.php`, `damaged_goods.php`).
* `/includes` - Shared theme templates (`sidebar.php`, `topbar.php` / `topp.php`, `footer.php`, `connection.php`).
* `/css` - Styling folders containing `custom-theme.css` variables, grids, and dark-mode parameters.
* `/js` - DataTable settings, city select scripts, and core SB-Admin dependencies.
* `/Database` - SQL installation schemas and table migration scripts.

---

## 📸 Screenshots

### Dashboard Overview
![Dashboard](https://user-images.githubusercontent.com/36708000/189607065-28afd173-791a-43b6-8cb5-6584fadedafe.png)

### Inventory Grid
![Inventory](https://user-images.githubusercontent.com/36708000/189607084-08499a5c-3c23-4c81-bf2f-bda7f6f0bdd8.png)

### Product Management
![Products](https://user-images.githubusercontent.com/36708000/189607103-2ace09bb-3b20-4ec2-a4e1-31b506d8b740.png)
