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
            "alter table `bno` add index `tm` ( `tm` )"
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

