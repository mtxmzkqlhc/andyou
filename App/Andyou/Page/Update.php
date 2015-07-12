<?php
/**
 * ��Ա�������
 *
 */
class  Andyou_Page_Update extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
        
		return true;
	}

    
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
        $sqlArr = array(
            //2015-6-18
            "create table `bno` (`id` int (11)   NOT NULL AUTO_INCREMENT  COMMENT 'ID',  `tm` int (11)   NOT NULL  COMMENT '����' , PRIMARY KEY ( `id` )  )",
            "alter table `bno` add index `tm` ( `tm` )",
            //2015-6-28
            "alter table `log_scorechange` add column `rsync` tinyint (1)  DEFAULT '0' NOT NULL  COMMENT '��ʾ�ü�¼�Ƿ�ͬ�����ƶ�'",
            "update log_scorechange set rsync = 1",
            "alter table `log_cardchange` add column `rsync` tinyint (1)  DEFAULT '0' NOT NULL  COMMENT '��ʾ�ü�¼�Ƿ�ͬ�����ƶ�'",
            "update log_cardchange set rsync = 1",
            "alter table `member` add column `upTm` int (11)   NOT NULL  COMMENT '��Ϣ�޸�ʱ��'",
            "alter table `member` add column `rsync` tinyint (11)   NOT NULL  COMMENT '��ʾ�ü�¼�Ƿ�ͬ�����ƶ�'",
            "create table `log_yunrsync` (    `id` int (11)   NOT NULL AUTO_INCREMENT ,  `name` varchar (20)   NOT NULL  COMMENT 'ͬ��ʵ������',  `tm` int (20)   NOT NULL  COMMENT 'ͬ��ʱ��' , PRIMARY KEY ( `id` )  )",
            "alter table `member` add column `site` varchar (11)   NOT NULL  COMMENT '�����ĸ�վ��'",
            "alter table `member` add column `siteObjId` int (11)   NOT NULL  COMMENT '���Ǹ�վ���ID'",
            "alter table `memeberotherpro` add column `phone` varchar (20)   NOT NULL  COMMENT '�绰' after `memberId`, add column `upTm` int (11)   NOT NULL  COMMENT '����ʱ��' after `ctype`, add column `site` varchar (20)   NOT NULL  COMMENT 'վ��' after `upTm`, add column `siteObjId` int (11)   NOT NULL  COMMENT '���Ǹ�վ���ID' after `site`",
            "alter table `log_useotherpro` add column `phone` varchar (20)   NOT NULL  COMMENT '�绰' after `memberId`",
            "alter table `bills` add column `phone` varchar (20)   NOT NULL  COMMENT '��Ա�绰' after `memberId`, add column `rsync` tinyint (1)   NOT NULL  COMMENT '�Ƿ�ͬ��' after `isBuyScore`",
            "alter table `memeberotherpro` add column `rsync` tinyint (1)   NOT NULL  COMMENT '�Ƿ�ͬ��'",
            "alter table `product` add column `rowTm` timestamp   NOT NULL  COMMENT '����ʱ���'",
            "alter table `staff` add column `rowTm` timestamp   NOT NULL  COMMENT '����ʱ���'",
        );
        $db = Db_Andyou::instance();
        if($sqlArr){
            foreach($sqlArr as $sql){
                echo $sql . "<br/>";
                $db->query($sql);
            }
        }
        
        $sql = "select memberId from memeberotherpro where phone = '' union select memberId from log_useotherpro where phone = ''";
        $res = $db->getAll($sql);
        if($res){
            foreach ($res as $re){
                $memberId = $re["memberId"];
                $minfo    = Helper_Member::getMemberInfo(array('id'=>$memberId));
                $phone    = $minfo["phone"];
                $db->query("update memeberotherpro set phone = '{$phone}' where memberId = {$memberId}");
                $db->query("update log_useotherpro set phone = '{$phone}' where memberId = {$memberId}");
            }
        }
        echo "OK";
	}
	
    
	
}

