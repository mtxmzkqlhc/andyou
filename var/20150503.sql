/*
SQLyog ��ҵ�� - MySQL GUI v5.02
���� - 5.5.27 : ���ݿ� - andyou
*********************************************************************
�������汾 : 5.5.27
*/


/*���ݱ� `adminuser` �ı�ṹ*/

CREATE TABLE `adminuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(20) NOT NULL COMMENT '�û���',
  `passwd` varchar(50) NOT NULL COMMENT '����',
  `isAdmin` tinyint(2) NOT NULL COMMENT '��� 1 ��������Ա',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*���ݱ� `bills` �ı�ṹ*/

CREATE TABLE `bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bno` varchar(20) NOT NULL COMMENT '����',
  `useScore` int(11) NOT NULL COMMENT 'ʹ�û���',
  `useCard` float(11,2) NOT NULL COMMENT 'ʹ�ÿ������',
  `useScoreAsMoney` float(11,2) NOT NULL COMMENT '���ֵ��Ľ��',
  `price` int(11) NOT NULL COMMENT '�տ�',
  `discount` float(4,2) NOT NULL COMMENT '�ۿ�',
  `orgPrice` int(11) NOT NULL COMMENT 'ԭʼ�ܼ�',
  `staffid` int(11) NOT NULL COMMENT 'Ա��ID',
  `memberId` int(11) NOT NULL COMMENT '��ԱID',
  `tm` int(11) NOT NULL COMMENT '����ʱ��',
  `dateDay` int(4) NOT NULL COMMENT '��������',
  `memberScore` float(11,2) NOT NULL COMMENT '�û�����ʱ�Ļ���',
  `memberCard` float(11,2) NOT NULL COMMENT '�û�����ʱ��Ŀ������',
  `remark` varchar(2000) NOT NULL COMMENT '��ע',
  `priceTrue` int(11) NOT NULL COMMENT '����Ա�޸ļ۸񣬼�¼��ʵ�ļ۸�',
  PRIMARY KEY (`id`),
  KEY `bno` (`bno`),
  KEY `staffid` (`staffid`,`tm`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=latin1;

/*���ݱ� `billsitem` �ı�ṹ*/

CREATE TABLE `billsitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL COMMENT '����������id',
  `bno` varchar(20) NOT NULL COMMENT '��������NO',
  `proId` int(11) NOT NULL COMMENT '��ƷID',
  `num` int(11) NOT NULL COMMENT '��������',
  `discount` float(4,2) NOT NULL COMMENT '�ۿ�',
  `price` int(11) NOT NULL COMMENT '�۸�',
  `staffid` int(11) NOT NULL COMMENT 'Ա��ID',
  `memberId` int(11) NOT NULL COMMENT '��ԱID',
  `tm` int(11) NOT NULL COMMENT '����ʱ��',
  PRIMARY KEY (`id`),
  KEY `bno` (`bno`),
  KEY `bid` (`bid`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=latin1;

/*���ݱ� `log_backup` �ı�ṹ*/

CREATE TABLE `log_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tableName` varchar(20) NOT NULL COMMENT '����',
  `tid` int(11) NOT NULL COMMENT '����ID',
  `dateTm` int(11) NOT NULL COMMENT 'ͬ��ʱ��',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*���ݱ� `log_cardchange` �ı�ṹ*/

CREATE TABLE `log_cardchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memberId` int(11) NOT NULL COMMENT '��ԱID',
  `direction` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 �� 0 ��',
  `card` int(11) NOT NULL DEFAULT '0' COMMENT '���ӵ����',
  `orgCard` int(11) NOT NULL COMMENT 'ԭ�������',
  `dateTm` int(11) NOT NULL DEFAULT '0' COMMENT '����',
  `adminer` varchar(50) NOT NULL COMMENT '������',
  `remark` varchar(2000) NOT NULL COMMENT '��ע',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*���ݱ� `log_productinstorage` �ı�ṹ*/

CREATE TABLE `log_productinstorage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proId` int(11) NOT NULL COMMENT '��Ӧ����ƷID',
  `adminer` varchar(50) DEFAULT NULL COMMENT '����Ա',
  `staffid` int(11) NOT NULL COMMENT 'ְԱID',
  `dateTm` int(11) NOT NULL COMMENT '����ʱ��',
  `orgNum` int(11) NOT NULL COMMENT '���ԭʼ����',
  `addNum` int(11) NOT NULL COMMENT '��������',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*���ݱ� `log_scorechange` �ı�ṹ*/

CREATE TABLE `log_scorechange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memberId` int(11) NOT NULL COMMENT '��ԱID',
  `direction` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 �� 0 ��',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '���ӵķ���',
  `orgScore` int(11) NOT NULL COMMENT 'ԭ���ķ���',
  `dateTm` int(11) NOT NULL DEFAULT '0' COMMENT '����',
  `adminer` varchar(50) NOT NULL COMMENT '������',
  `remark` varchar(2000) NOT NULL COMMENT '��ע',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*���ݱ� `member` �ı�ṹ*/

CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `cateId` tinyint(4) NOT NULL,
  `byear` int(4) NOT NULL COMMENT '������',
  `bmonth` tinyint(4) NOT NULL COMMENT '������',
  `bday` tinyint(4) NOT NULL COMMENT '������',
  `addTm` int(11) NOT NULL COMMENT '���ʱ��',
  `score` int(11) NOT NULL COMMENT '����',
  `balance` int(11) NOT NULL COMMENT '���',
  `remark` varchar(2000) NOT NULL COMMENT '��ע',
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`(6))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*���ݱ� `membercate` �ı�ṹ*/

CREATE TABLE `membercate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `discount` float(4,2) NOT NULL DEFAULT '1.00' COMMENT '��Ա����������ܵ��ۿ�',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*���ݱ� `options` �ı�ṹ*/

CREATE TABLE `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '��������',
  `cnName` varchar(20) NOT NULL COMMENT '����������',
  `value` varchar(1000) NOT NULL COMMENT '������ֵ',
  `isInt` tinyint(1) NOT NULL COMMENT '�Ƿ�������',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*���ݱ� `product` �ı�ṹ*/

CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL COMMENT '��Ʒ��',
  `code` varchar(50) NOT NULL COMMENT '�����',
  `cateId` tinyint(4) NOT NULL COMMENT '����ID',
  `price` int(11) NOT NULL COMMENT '�洢*100������',
  `inPrice` int(11) NOT NULL COMMENT '������',
  `stock` int(11) NOT NULL COMMENT '�������',
  `score` int(4) NOT NULL COMMENT '���ֱ���',
  `discut` float(4,2) NOT NULL COMMENT '����ۿ�',
  `addtm` int(11) NOT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `code` (`code`(6)),
  KEY `cateId` (`cateId`,`addtm`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*���ݱ� `productcate` �ı�ṹ*/

CREATE TABLE `productcate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*���ݱ� `staff` �ı�ṹ*/

CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL COMMENT '����',
  `inDate` varchar(20) NOT NULL COMMENT '��ְʱ��',
  `byear` int(4) DEFAULT NULL COMMENT '������',
  `bmonth` tinyint(2) DEFAULT NULL COMMENT '������',
  `bday` tinyint(2) DEFAULT NULL COMMENT '������',
  `cateId` tinyint(4) NOT NULL COMMENT '����',
  `salary` int(11) unsigned NOT NULL COMMENT '��н',
  `percentage` int(11) NOT NULL COMMENT '��ɱ���',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*���ݱ� `staffcate` �ı�ṹ*/

CREATE TABLE `staffcate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `salary` int(11) NOT NULL,
  `percentage` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
