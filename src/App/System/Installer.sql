SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `system_app` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '应用名',
  `label` varchar(60) NOT NULL DEFAULT '' COMMENT '应用中文标识',
  `icon` varchar(60) NOT NULL DEFAULT '' COMMENT '应用图标',
  `ordering` INT NOT NULL DEFAULT '0' COMMENT '排序',
  `install_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '安装时间',
  `update_time` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用';

CREATE TABLE `system_mail_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `to_email` varchar(60) NOT NULL COMMENT '收件人邮箱',
  `to_name` varchar(60) NOT NULL COMMENT '收件人姓名',
  `cc_email` varchar(60) NOT NULL COMMENT '抄送人邮箱',
  `cc_name` varchar(60) NOT NULL COMMENT '抄送人姓名',
  `bcc_email` varchar(60) NOT NULL COMMENT '暗送人邮箱',
  `bcc_name` varchar(60) NOT NULL COMMENT '暗送人姓名',
  `subject` varchar(200) NOT NULL COMMENT '标题',
  `body` text NOT NULL COMMENT '内容',
  `sent` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否发送成功',
  `sent_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '发送时间',
  `error_message` varchar(200) NOT NULL COMMENT '失败信息',
  `times` tinyint(4) NOT NULL COMMENT '重试次数',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='发送邮件队列';

CREATE TABLE `system_theme` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(60) NOT NULL COMMENT '应用名',
  `label` varchar(60) NOT NULL COMMENT '应用中文标识',
  `install_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '安装时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主题';

INSERT INTO `system_theme` (`id`, `name`, `label`, `install_time`, `update_time`) VALUES
(1, 'Admin', '默认主题', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

CREATE TABLE `system_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `app` varchar(60) NOT NULL COMMENT '应用	',
  `name` varchar(60) NOT NULL COMMENT '名称',
  `label` varchar(60) NOT NULL COMMENT '中文名称',
  `schedule` varchar(30) NOT NULL DEFAULT '* * * * *' COMMENT '执行计划',
  `schedule_lock` TINYINT NOT NULL DEFAULT '0' COMMENT '是否锁定执行计划',
  `timeout` int(11) NOT NULL DEFAULT '0' COMMENT '超时时间',
  `data` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '任务数据（JSON格式）',
  `is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `last_execute_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后执行时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `app` (`app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计划任务';

CREATE TABLE `system_task_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `task_id` int(11) NOT NULL COMMENT '任务ID',
  `data` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '任务数据（JSON格式）',
  `status` varchar(30) NOT NULL COMMENT '状态（RUNNING：运行中/COMPLETE：执行完成/ERROR：出错）	',
  `message` varchar(200) NOT NULL COMMENT '异常信息',
  `trigger` varchar(30) NOT NULL COMMENT '触发方式：SYSTEM：系统调度/MANUAL：人工启动',
  `complete_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '完成时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽取数据';

CREATE TABLE `system_admin_op_log` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `admin_user_id` int NOT NULL DEFAULT '0' COMMENT '用户ID',
  `app` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '应用名',
  `controller` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '控制器名',
  `action` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '动作名',
  `content` VARCHAR(240) NOT NULL DEFAULT '' COMMENT '内容',
  `details` text NOT NULL DEFAULT '' COMMENT '明细',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `admin_user_id` (`admin_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统后台操作日志';

CREATE TABLE `system_admin_role` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '角色名',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `permission` tinyint NOT NULL COMMENT '权限（0: 无权限/1: 所有权限/-1: 自定义权限）',
  `permissions` text NOT NULL COMMENT '自定义权限',
  `is_enable` tinyint NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `ordering` int NOT NULL COMMENT '排序（越小越靠前）',
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色';

INSERT INTO `system_admin_role` (`id`, `name`, `remark`, `permission`, `permissions`, `is_enable`, `is_delete`, `ordering`, `create_time`, `update_time`) VALUES
(1, '超级管理员', '能执行所有操作', 1, '', 1, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

CREATE TABLE `system_admin_user` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `username` varchar(120) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(40) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(32) NOT NULL DEFAULT '' COMMENT '密码盐值',
  `admin_role_id` INT NOT NULL DEFAULT '0' COMMENT '角色ID',
  `avatar` varchar(60) NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(120) NOT NULL DEFAULT '' COMMENT '邮箱',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `gender` tinyint NOT NULL DEFAULT '-1' COMMENT '性别（0：女/1：男/-1：保密）',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `last_login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上次登陆时间',
  `this_login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '本次登陆时间',
  `last_login_ip` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '上次登录的IP',
  `this_login_ip` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '本次登录的IP',
  `is_enable` tinyint NOT NULL DEFAULT '1' COMMENT '是否可用',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '是否已删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户';

INSERT INTO `system_admin_user` (`id`, `username`, `password`, `salt`, `admin_role_id`, `avatar`, `email`, `name`, `gender`, `phone`, `mobile`, `last_login_time`, `this_login_time`, `last_login_ip`, `this_login_ip`, `is_enable`, `is_delete`, `create_time`, `update_time`) VALUES
(1, 'admin', 'a2ad3e6e3acf5b182324ed782f8a0556d43e59dd', 'ybFD7uzKMH8yvPHvuPNNT0vDv7uF2811', 1, '', 'be@phpbe.com', '管理员', 0, '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '127.0.0.1', '127.0.0.1', 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

CREATE TABLE `system_admin_user_login_log` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `username` varchar(120) NOT NULL DEFAULT '' COMMENT '用户名',
  `success` tinyint NOT NULL COMMENT '是否登录成功（0-不成功/1-成功）',
  `description` varchar(240) NOT NULL DEFAULT '' COMMENT '描述',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户登录日志';

