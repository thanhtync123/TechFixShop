-- ============================================================
--  Migration: Shopee Sync Tables
--  Chạy file này 1 lần trong phpMyAdmin hoặc MySQL CLI
-- ============================================================

USE hometech_db;

-- ── Bảng cấu hình OAuth token ────────────────────────────────
CREATE TABLE IF NOT EXISTS `shopee_settings` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `partner_id`    int(11)      NOT NULL,
  `shop_id`       bigint(20)   NOT NULL,
  `access_token`  varchar(512) NOT NULL DEFAULT '',
  `refresh_token` varchar(512) NOT NULL DEFAULT '',
  `token_expires` datetime     DEFAULT NULL  COMMENT 'Thời điểm access_token hết hạn',
  `updated_at`    timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lưu OAuth token Shopee';

-- Chèn 1 dòng mặc định để UPDATE sau
INSERT IGNORE INTO `shopee_settings` (`id`, `partner_id`, `shop_id`)
VALUES (1, 0, 0);

-- ── Bảng mapping sản phẩm web ↔ Shopee ──────────────────────
CREATE TABLE IF NOT EXISTS `shopee_product_sync` (
  `id`              int(11)      NOT NULL AUTO_INCREMENT,
  `source_type`     enum('service','equipment') NOT NULL COMMENT 'Loại nguồn',
  `source_id`       int(11)      NOT NULL COMMENT 'ID trong bảng services hoặc equipments',
  `shopee_item_id`  bigint(20)   DEFAULT NULL COMMENT 'Item ID trên Shopee',
  `shopee_model_id` bigint(20)   DEFAULT NULL,
  `shopee_image_id` varchar(255) DEFAULT NULL COMMENT 'Image ID đã upload lên Shopee',
  `sync_status`     enum('pending','synced','error','deleted') NOT NULL DEFAULT 'pending',
  `last_synced_at`  datetime     DEFAULT NULL,
  `error_message`   text         DEFAULT NULL,
  `created_at`      timestamp    NOT NULL DEFAULT current_timestamp(),
  `updated_at`      timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_source` (`source_type`, `source_id`),
  KEY `idx_shopee_item` (`shopee_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Trạng thái đồng bộ sản phẩm lên Shopee';

-- ── Bảng log đồng bộ ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `shopee_sync_log` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `source_type` enum('service','equipment') NOT NULL,
  `source_id`   int(11)      NOT NULL,
  `action`      varchar(50)  NOT NULL COMMENT 'add_item | update_item | update_price | update_stock | delete_item | upload_image',
  `request`     text         DEFAULT NULL,
  `response`    text         DEFAULT NULL,
  `success`     tinyint(1)   NOT NULL DEFAULT 0,
  `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_source` (`source_type`, `source_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lịch sử đồng bộ Shopee';
