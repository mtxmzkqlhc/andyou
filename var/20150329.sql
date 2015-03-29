/*
SQLyog 企业版 - MySQL GUI v5.02
主机 - 5.5.27 : 数据库 - andyou
*********************************************************************
服务器版本 : 5.5.27
*/


/*数据表 `member` 的表结构*/

CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `cateId` tinyint(4) NOT NULL,
  `byear` int(4) NOT NULL COMMENT '生日年',
  `bmonth` tinyint(4) NOT NULL COMMENT '生日月',
  `bday` tinyint(4) NOT NULL COMMENT '生日日',
  `addTm` int(11) NOT NULL COMMENT '添加时间',
  `score` int(11) NOT NULL COMMENT '积分',
  `balance` int(11) NOT NULL COMMENT '余额',
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`(6))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*数据表 `membercate` 的表结构*/

CREATE TABLE `membercate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*数据表 `product` 的表结构*/

CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL COMMENT '商品名',
  `code` varchar(50) NOT NULL COMMENT '条码号',
  `cateId` tinyint(4) NOT NULL COMMENT '分类ID',
  `price` int(11) NOT NULL COMMENT '存储*100的数字',
  `inPrice` int(11) NOT NULL COMMENT '进货价',
  `stock` int(11) NOT NULL COMMENT '库存数量',
  `score` int(4) NOT NULL COMMENT '积分比例',
  `discut` int(4) NOT NULL COMMENT '最低折扣',
  `addtm` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `code` (`code`(6)),
  KEY `cateId` (`cateId`,`addtm`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*数据表 `productcate` 的表结构*/

CREATE TABLE `productcate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*数据表 `staff` 的表结构*/

CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL COMMENT '姓名',
  `inDate` varchar(20) NOT NULL COMMENT '入职时间',
  `byear` int(4) DEFAULT NULL COMMENT '生日年',
  `bmonth` tinyint(2) DEFAULT NULL COMMENT '生日月',
  `bday` tinyint(2) DEFAULT NULL COMMENT '生日日',
  `cateId` tinyint(4) NOT NULL COMMENT '级别',
  `salary` int(11) unsigned NOT NULL COMMENT '底薪',
  `percentage` int(11) NOT NULL COMMENT '提成比例',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*数据表 `staffcate` 的表结构*/

CREATE TABLE `staffcate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `salary` int(11) NOT NULL,
  `percentage` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
