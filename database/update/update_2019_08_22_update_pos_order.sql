ALTER TABLE `pos_order` ADD `order_discount_of` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 is owner, 1 is station,2 divide 50/50' AFTER `order_discount`, ADD `order_debit_number` VARCHAR(255) NOT NULL AFTER `order_discount_of`, ADD `order_debit_amount` INT(11) NOT NULL AFTER `order_debit_number`;