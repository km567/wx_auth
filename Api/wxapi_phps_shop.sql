-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2019-12-19 08:10:59
-- 服务器版本： 10.1.37-MariaDB
-- PHP 版本： 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `wxapi_phps_shop`
--

-- --------------------------------------------------------

--
-- 表的结构 `news_user`
--

CREATE TABLE `news_user` (
  `id` int(11) NOT NULL,
  `openid` varchar(50) CHARACTER SET utf8 NOT NULL,
  `openid_gzh` varchar(255) NOT NULL,
  `unionid` varchar(70) NOT NULL,
  `nickname` varchar(60) NOT NULL,
  `headpic` varchar(500) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `points` int(11) NOT NULL,
  `start` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL COMMENT '注册时间',
  `update_time` int(11) DEFAULT NULL,
  `order_number` varchar(255) NOT NULL,
  `order_money` float NOT NULL,
  `prepay_id` varchar(255) NOT NULL,
  `is_visible` int(1) NOT NULL DEFAULT '1' COMMENT '1显示0隐藏',
  `code` int(11) DEFAULT '0' COMMENT '验证码',
  `code_time` int(11) NOT NULL COMMENT '验证码保存时间',
  `vip_status` int(1) NOT NULL DEFAULT '0' COMMENT '是否是会员(0非会员，1会员，2会员已过期)',
  `vip_time` int(11) NOT NULL COMMENT '会员到期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `news_user`
--

INSERT INTO `news_user` (`id`, `openid`, `openid_gzh`, `unionid`, `nickname`, `headpic`, `mobile`, `points`, `start`, `create_time`, `update_time`, `order_number`, `order_money`, `prepay_id`, `is_visible`, `code`, `code_time`, `vip_status`, `vip_time`) VALUES
(16, 'oq_jb4nRp3s3fBjZHzVd_YTo1GD4', '', '', 'Katrina，', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83epG66msAX8uh6tWftnY0BrbKe0TdzZAiblibHzYr4KQiaqlmTOAmGns33hzxIMlEswQrKiaBZcp8L06hg/132', '18748535815', 0, NULL, 1576732212, 1576732212, 'qwe', 0.01, 'wx1914040932267128c20c46ad1509847100', 1, 49425, 1576732973, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `sys_config`
--

CREATE TABLE `sys_config` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '主键',
  `key` varchar(255) NOT NULL DEFAULT '' COMMENT '配置项',
  `value` varchar(1000) NOT NULL DEFAULT '' COMMENT '配置值json',
  `desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '描述',
  `type` int(11) NOT NULL COMMENT '1基础2交易',
  `is_use` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用 1启用 0不启用',
  `update_time` int(11) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=963 DEFAULT CHARSET=utf8 COMMENT='第三方配置表';

--
-- 转储表的索引
--

--
-- 表的索引 `news_user`
--
ALTER TABLE `news_user`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `sys_config`
--
ALTER TABLE `sys_config`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `news_user`
--
ALTER TABLE `news_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- 使用表AUTO_INCREMENT `sys_config`
--
ALTER TABLE `sys_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
