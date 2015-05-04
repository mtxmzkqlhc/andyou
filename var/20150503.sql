/*
SQLyog 企业版 - MySQL GUI v5.02
主机 - 5.5.27 : 数据库 - andyou
*********************************************************************
服务器版本 : 5.5.27
*/


/*数据表 `adminuser` 的表结构*/

CREATE TABLE `adminuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(20) NOT NULL COMMENT '用户名',
  `passwd` varchar(50) NOT NULL COMMENT '密码',
  `isAdmin` tinyint(2) NOT NULL COMMENT '类别 1 超级管理员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*数据表 `bills` 的表结构*/

CREATE TABLE `bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bno` varchar(20) NOT NULL COMMENT '单号',
  `useScore` int(11) NOT NULL COMMENT '使用积分',
  `useCard` float(11,2) NOT NULL COMMENT '使用卡内余额',
  `useScoreAsMoney` float(11,2) NOT NULL COMMENT '积分当的金额',
  `price` int(11) NOT NULL COMMENT '收款',
  `discount` float(4,2) NOT NULL COMMENT '折扣',
  `orgPrice` int(11) NOT NULL COMMENT '原始总价',
  `staffid` int(11) NOT NULL COMMENT '员工ID',
  `memberId` int(11) NOT NULL COMMENT '会员ID',
  `tm` int(11) NOT NULL COMMENT '消费时间',
  `dateDay` int(4) NOT NULL COMMENT '消费日期',
  `memberScore` float(11,2) NOT NULL COMMENT '用户消费时的积分',
  `memberCard` float(11,2) NOT NULL COMMENT '用户消费时候的卡内余额',
  `remark` varchar(2000) NOT NULL COMMENT '备注',
  `priceTrue` int(11) NOT NULL COMMENT '销售员修改价格，记录真实的价格',
  PRIMARY KEY (`id`),
  KEY `bno` (`bno`),
  KEY `staffid` (`staffid`,`tm`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=latin1;

/*数据表 `billsitem` 的表结构*/

CREATE TABLE `billsitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL COMMENT '所属订单的id',
  `bno` varchar(20) NOT NULL COMMENT '所属订单NO',
  `proId` int(11) NOT NULL COMMENT '产品ID',
  `num` int(11) NOT NULL COMMENT '购买数量',
  `discount` float(4,2) NOT NULL COMMENT '折扣',
  `price` int(11) NOT NULL COMMENT '价格',
  `staffid` int(11) NOT NULL COMMENT '员工ID',
  `memberId` int(11) NOT NULL COMMENT '会员ID',
  `tm` int(11) NOT NULL COMMENT '消费时间',
  PRIMARY KEY (`id`),
  KEY `bno` (`bno`),
  KEY `bid` (`bid`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=latin1;

/*数据表 `log_backup` 的表结构*/

CREATE TABLE `log_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tableName` varchar(20) NOT NULL COMMENT '表名',
  `tid` int(11) NOT NULL COMMENT '表内ID',
  `dateTm` int(11) NOT NULL COMMENT '同步时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*数据表 `log_cardchange` 的表结构*/

CREATE TABLE `log_cardchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memberId` int(11) NOT NULL COMMENT '会员ID',
  `direction` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 减 0 加',
  `card` int(11) NOT NULL DEFAULT '0' COMMENT '增加的余额',
  `orgCard` int(11) NOT NULL COMMENT '原来的余额',
  `dateTm` int(11) NOT NULL DEFAULT '0' COMMENT '分舒',
  `adminer` varchar(50) NOT NULL COMMENT '操作人',
  `remark` varchar(2000) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*数据表 `log_productinstorage` 的表结构*/

CREATE TABLE `log_productinstorage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proId` int(11) NOT NULL COMMENT '对应的商品ID',
  `adminer` varchar(50) DEFAULT NULL COMMENT '管理员',
  `staffid` int(11) NOT NULL COMMENT '职员ID',
  `dateTm` int(11) NOT NULL COMMENT '更新时间',
  `orgNum` int(11) NOT NULL COMMENT '库存原始数量',
  `addNum` int(11) NOT NULL COMMENT '增加数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*数据表 `log_scorechange` 的表结构*/

CREATE TABLE `log_scorechange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memberId` int(11) NOT NULL COMMENT '会员ID',
  `direction` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 减 0 加',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '增加的分数',
  `orgScore` int(11) NOT NULL COMMENT '原来的分数',
  `dateTm` int(11) NOT NULL DEFAULT '0' COMMENT '分舒',
  `adminer` varchar(50) NOT NULL COMMENT '操作人',
  `remark` varchar(2000) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

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
  `remark` varchar(2000) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`(6))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*数据表 `membercate` 的表结构*/

CREATE TABLE `membercate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `discount` float(4,2) NOT NULL DEFAULT '1.00' COMMENT '会员级别可以享受的折扣',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*数据表 `options` 的表结构*/

CREATE TABLE `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '配置项名',
  `cnName` varchar(20) NOT NULL COMMENT '配置中文名',
  `value` varchar(1000) NOT NULL COMMENT '配置项值',
  `isInt` tinyint(1) NOT NULL COMMENT '是否是整形',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

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
  `discut` float(4,2) NOT NULL COMMENT '最低折扣',
  `addtm` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `code` (`code`(6)),
  KEY `cateId` (`cateId`,`addtm`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*数据表 `productcate` 的表结构*/

CREATE TABLE `productcate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

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
