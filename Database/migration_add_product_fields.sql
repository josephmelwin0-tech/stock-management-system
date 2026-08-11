-- Migration to add new custom fields to the product table
-- Non-destructive additions only

ALTER TABLE `product`
  ADD COLUMN `CATEGORY` varchar(100) DEFAULT NULL,
  ADD COLUMN `REORDER_THRESHOLD` int(11) DEFAULT 0,
  ADD COLUMN `UNIT_COST` decimal(10,2) DEFAULT 0.00,
  ADD COLUMN `SALE_PRICE` decimal(10,2) DEFAULT 0.00,
  ADD COLUMN `LOCATION` varchar(100) DEFAULT NULL;
