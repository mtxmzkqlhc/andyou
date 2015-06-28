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
            "create table `log_yunrsync` (    `id` int (11)   NOT NULL AUTO_INCREMENT ,  `name` varchar (20)   NOT NULL  COMMENT '同步实例名称',  `tm` int (20)   NOT NULL  COMMENT '同步时间' , PRIMARY KEY ( `id` )  )",
        );
        $db = Db_Andyou::instance();
        if($sqlArr){
            foreach($sqlArr as $sql){
                echo $sql . "<br/>";
                $db->query($sql);
            }
        }
        echo "OK";
	}
	
    
	
}

