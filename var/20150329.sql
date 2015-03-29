/*
SQLyog ��ҵ�� - MySQL GUI v5.02
���� - 5.5.27 : ���ݿ� - andyou
*********************************************************************
�������汾 : 5.5.27
*/


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
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`(6))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*���ݱ� `membercate` �ı�ṹ*/

CREATE TABLE `membercate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
  `discut` int(4) NOT NULL COMMENT '����ۿ�',
  `addtm` int(11) NOT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `code` (`code`(6)),
  KEY `cateId` (`cateId`,`addtm`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*���ݱ� `productcate` �ı�ṹ*/

CREATE TABLE `productcate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
