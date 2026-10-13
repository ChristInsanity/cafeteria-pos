-- ============================================================
-- Cafeteria POS — Database Schema + Seed Data
-- IT 415 Midterm Practical Exam (Scenario 1)
-- ============================================================

CREATE DATABASE IF NOT EXISTS cafeteria_pos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE cafeteria_pos;

-- ============================================================
-- Products table
-- ============================================================
DROP TABLE IF EXISTS products;

CREATE TABLE products (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100)   NOT NULL,
  price       DECIMAL(10,2)  NOT NULL,
  is_active   TINYINT(1)     NOT NULL DEFAULT 1,
  created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Transactions table (one row per completed sale)
-- ============================================================
DROP TABLE IF EXISTS transactions;

CREATE TABLE transactions (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  reference_no    VARCHAR(30)   NOT NULL UNIQUE,
  total_amount    DECIMAL(10,2) NOT NULL,
  amount_paid     DECIMAL(10,2) NOT NULL,
  change_amount   DECIMAL(10,2) NOT NULL,
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Transaction items (line items per transaction)
-- ============================================================
DROP TABLE IF EXISTS transaction_items;

CREATE TABLE transaction_items (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  transaction_id  INT           NOT NULL,
  product_id      INT           NOT NULL,
  product_name    VARCHAR(100)  NOT NULL,
  unit_price      DECIMAL(10,2) NOT NULL,
  quantity        INT           NOT NULL,
  subtotal        DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id)     REFERENCES products(id)
);

-- ============================================================
-- Seed: Scenario 1 — Campus Cafeteria products
-- ============================================================
INSERT INTO products (name, price) VALUES
  ('Rice (Plain)',        15.00),
  ('Fried Chicken (1 pc)',65.00),
  ('Pork Adobo',          60.00),
  ('Vegetable Side Dish', 35.00),
  ('Iced Tea (cup)',      20.00),
  ('Bottled Water',       20.00);