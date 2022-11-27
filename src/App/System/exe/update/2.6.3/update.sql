ALTER TABLE `system_admin_op_log`
CHANGE `content` `content` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容';
