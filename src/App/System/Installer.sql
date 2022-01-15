CREATE TABLE `system_mail_queue` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `to_email` varchar(60) NOT NULL DEFAULT '' COMMENT '收件人邮箱',
  `to_name` varchar(60) NOT NULL DEFAULT '' COMMENT '收件人姓名',
  `cc_email` varchar(60) NOT NULL DEFAULT '' COMMENT '抄送人邮箱',
  `cc_name` varchar(60) NOT NULL DEFAULT '' COMMENT '抄送人姓名',
  `bcc_email` varchar(60) NOT NULL DEFAULT '' COMMENT '暗送人邮箱',
  `bcc_name` varchar(60) NOT NULL DEFAULT '' COMMENT '暗送人姓名',
  `subject` varchar(200) NOT NULL DEFAULT '' COMMENT '标题',
  `body` text NOT NULL COMMENT '内容',
  `sent` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否发送成功',
  `sent_time` timestamp NULL COMMENT '发送时间',
  `error_message` varchar(200) NOT NULL DEFAULT '' COMMENT '失败信息',
  `times` tinyint(4) NOT NULL DEFAULT '0' COMMENT '重试次数',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='发送邮件队列';

CREATE TABLE `system_menu` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `name` varchar(60) NOT NULL COMMENT '菜单名',
  `label` varchar(60) NOT NULL COMMENT '中文名称',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统菜单',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='菜单';

INSERT INTO `system_menu` (`id`, `name`, `label`, `is_system`, `create_time`, `update_time`) VALUES
((SELECT UUID()), 'North', '顶部菜单', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
((SELECT UUID()), 'South', '底部菜单', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

CREATE TABLE `system_menu_item` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `menu_name` varchar(36) NOT NULL COMMENT '菜单名称',
  `parent_id` varchar(36) NOT NULL COMMENT '父ID',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '名称',
  `route` varchar(240) NOT NULL DEFAULT '' COMMENT '路由',
  `params` varchar(240) NOT NULL DEFAULT '' COMMENT '路由参数',
  `url` varchar(240) NOT NULL DEFAULT '' COMMENT '指定网址',
  `description` varchar(120) NOT NULL DEFAULT '' COMMENT '菜单描述',
  `target` varchar(7) NOT NULL DEFAULT '' COMMENT '打开方式',
  `ordering` int NOT NULL DEFAULT '0' COMMENT '排序（越小越靠前）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `menu_name` (`menu_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='菜单项';

INSERT INTO `system_menu_item` (`id`, `menu_name`, `parent_id`, `name`, `route`, `params`, `url`, `description`, `target`, `ordering`, `create_time`, `update_time`) VALUES
((SELECT UUID()), 'North', '', '首页', 'System.Index.index', '', '', '首页', '_self', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

CREATE TABLE `system_task` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `app` varchar(60) NOT NULL DEFAULT '' COMMENT '应用',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
  `label` varchar(60) NOT NULL DEFAULT '' COMMENT '中文名称',
  `schedule` varchar(30) NOT NULL DEFAULT '* * * * *' COMMENT '执行计划',
  `schedule_lock` TINYINT NOT NULL DEFAULT '0' COMMENT '是否锁定执行计划',
  `timeout` int(11) NOT NULL DEFAULT '0' COMMENT '超时时间',
  `data` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '任务数据（JSON格式）',
  `is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `last_execute_time` timestamp NULL COMMENT '最后执行时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `app` (`app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='计划任务';

CREATE TABLE `system_task_log` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `task_id` varchar(36) NOT NULL DEFAULT '' COMMENT '任务ID',
  `data` varchar(200) NOT NULL DEFAULT '' COMMENT '任务数据（JSON格式）',
  `status` varchar(30) NOT NULL DEFAULT 'RUNNING' COMMENT '状态（RUNNING：运行中/COMPLETE：执行完成/ERROR：出错）	',
  `message` varchar(200) NOT NULL DEFAULT '' COMMENT '异常信息',
  `trigger` varchar(30) NOT NULL DEFAULT 'SYSTEM' COMMENT '触发方式：SYSTEM：系统调度/MANUAL：人工启动',
  `complete_time` timestamp NULL COMMENT '完成时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抽取数据';

CREATE TABLE `system_admin_op_log` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `admin_user_id` varchar(36) NOT NULL DEFAULT '' COMMENT '用户ID',
  `app` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '应用名',
  `controller` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '控制器名',
  `action` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '动作名',
  `content` VARCHAR(240) NOT NULL DEFAULT '' COMMENT '内容',
  `details` text NOT NULL COMMENT '明细',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `admin_user_id` (`admin_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统后台操作日志';

CREATE TABLE `system_admin_role` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '角色名',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `permission` tinyint NOT NULL DEFAULT '0' COMMENT '权限（0: 无权限/1: 所有权限/-1: 自定义权限）',
  `permission_keys` text NOT NULL COMMENT '自定义权限',
  `is_enable` tinyint NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `ordering` int NOT NULL DEFAULT '0' COMMENT '排序（越小越靠前）',
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户角色';

INSERT INTO `system_admin_role` (`id`, `name`, `remark`, `permission`, `permission_keys`, `is_enable`, `is_delete`, `ordering`, `create_time`, `update_time`) VALUES
((SELECT UUID()), '超级管理员', '能执行所有操作', 1, '', 1, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

CREATE TABLE `system_admin_user` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `username` varchar(120) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(40) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(32) NOT NULL DEFAULT '' COMMENT '密码盐值',
  `admin_role_id` varchar(36) NOT NULL DEFAULT '' COMMENT '角色ID',
  `avatar` varchar(60) NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(120) NOT NULL DEFAULT '' COMMENT '邮箱',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `gender` tinyint NOT NULL DEFAULT '-1' COMMENT '性别（0：女/1：男/-1：保密）',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `last_login_time` timestamp NULL COMMENT '上次登陆时间',
  `this_login_time` timestamp NULL COMMENT '本次登陆时间',
  `last_login_ip` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '上次登录的IP',
  `this_login_ip` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '本次登录的IP',
  `is_enable` tinyint NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户';

INSERT INTO `system_admin_user` (`id`, `username`, `password`, `salt`, `admin_role_id`, `avatar`, `email`, `name`, `gender`, `phone`, `mobile`, `last_login_time`, `this_login_time`, `last_login_ip`, `this_login_ip`, `is_enable`, `is_delete`, `create_time`, `update_time`) VALUES
((SELECT UUID()), 'admin', 'a2ad3e6e3acf5b182324ed782f8a0556d43e59dd', 'ybFD7uzKMH8yvPHvuPNNT0vDv7uF2811', (SELECT id FROM system_admin_role WHERE `name` = '超级管理员' LIMIT 1), '', 'be@phpbe.com', '管理员', 0, '', '', NULL, NULL, '', '', 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

CREATE TABLE `system_admin_user_login_log` (
  `id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
  `username` varchar(120) NOT NULL DEFAULT '' COMMENT '用户名',
  `success` tinyint NOT NULL DEFAULT '0' COMMENT '是否登录成功（0-不成功/1-成功）',
  `description` varchar(240) NOT NULL DEFAULT '' COMMENT '描述',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户登录日志';

