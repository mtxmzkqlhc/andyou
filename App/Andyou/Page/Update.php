<?php
/**
 * 会员管理管理
 *
 */
class  Andyou_Page_Update extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
        
		return true;
	}

    
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
        $sqlArr = array(
            //2015-6-18
            "create table `bno` (`id` int (11)   NOT NULL AUTO_INCREMENT  COMMENT 'ID',  `tm` int (11)   NOT NULL  COMMENT '日期' , PRIMARY KEY ( `id` )  )",
            "alter table `bno` add index `tm` ( `tm` )",
            //2015-6-28
            "alter table `log_scorechange` add column `rsync` tinyint (1)  DEFAULT '0' NOT NULL  COMMENT '表示该记录是否同步到云端'",
            "update log_scorechange set rsync = 1",
            "alter table `log_cardchange` add column `rsync` tinyint (1)  DEFAULT '0' NOT NULL  COMMENT '表示该记录是否同步到云端'",
            "update log_cardchange set rsync = 1",
            "alter table `member` add column `upTm` int (11)   NOT NULL  COMMENT '信息修改时间'",
            "alter table `member` add column `rsync` tinyint (11)   NOT NULL  COMMENT '表示该记录是否同步到云端'",
            "create table `log_yunrsync` (    `id` int (11)   NOT NULL AUTO_INCREMENT ,  `name` varchar (20)   NOT NULL  COMMENT '同步实例名称',  `tm` int (20)   NOT NULL  COMMENT '同步时间' , PRIMARY KEY ( `id` )  )",
            "alter table `member` add column `site` varchar (11)   NOT NULL  COMMENT '来自哪个站点'",
            "alter table `member` add column `siteObjId` int (11)   NOT NULL  COMMENT '在那个站点的ID'",
            "alter table `memeberotherpro` add column `phone` varchar (20)   NOT NULL  COMMENT '电话' after `memberId`, add column `upTm` int (11)   NOT NULL  COMMENT '更新时间' after `ctype`, add column `site` varchar (20)   NOT NULL  COMMENT '站点' after `upTm`, add column `siteObjId` int (11)   NOT NULL  COMMENT '在那个站点的ID' after `site`",
            "alter table `log_useotherpro` add column `phone` varchar (20)   NOT NULL  COMMENT '电话' after `memberId`",
            "alter table `bills` add column `phone` varchar (20)   NOT NULL  COMMENT '会员电话' after `memberId`, add column `rsync` tinyint (1)   NOT NULL  COMMENT '是否同步' after `isBuyScore`",
            "alter table `memeberotherpro` add column `rsync` tinyint (1)   NOT NULL  COMMENT '是否同步'",
            "alter table `product` add column `rowTm` timestamp   NOT NULL  COMMENT '更新时间戳'",
            "alter table `staff` add column `rowTm` timestamp   NOT NULL  COMMENT '更新时间戳'",
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

