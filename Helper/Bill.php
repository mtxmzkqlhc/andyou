<?php
/**
 * ���Ѽ�¼
 */
class Helper_Bill extends Helper_Abstract {
   
    /**
     * ��ý��յ�����
     */
    public static function getDayIncome($params=array()){
        $options = array(
            'startTm'             => strtotime(date("Y-m-d 00:00:00",SYSTEM_TIME)), #��ʼʱ��
            'endTm'               => strtotime(date("Y-m-d 23:59:59",SYSTEM_TIME)), #����ʱ��
            'groupDay'            => false, #�Ƿ����ջ���
        );
        if($params && is_array($params)) $options = array_merge($options, $params);
        extract($options);
        $db = Db_Andyou::instance();
        
        $groupBy = $groupDay ? "group by dateDay" : "";
        $addCols = $groupDay ? ",dateDay" : "";
        
        $sql = "select sum(price) price,sum(useScore) useScore,sum(useScoreAsMoney) useScoreAsMoney,sum(useCard) useCard,count('x') cnt {$addCols} from bills where tm > {$startTm} and tm < {$endTm} {$groupBy}";
        if($groupDay){
            $row = array();
            $res = $db->getAll($sql);
            if($res){
                foreach($res as $re){
                   $row[$re["dateDay"]] =  $re;
                }
            }
        }else{
            $row = $db->getRow($sql);
        }
        return $row;
    }
    
    /**
     * ���һ������
     */
    public static function getMaxBno(){
        $db = Db_Andyou::instance();
        //��ý��충������
        $date = date("Y-m-d ",SYSTEM_TIME);
        $startTm = strtotime($date."00:00:00");
        $sql = "select count(*) from bills where tm >".$startTm;
        $num = $db->getOne($sql);
        
        return date("Ymd",SYSTEM_TIME) . sprintf("%06d",($num+1));
    }


    /**
     * ��ö��������б�
     */
    public static function getBillsList($params){
        $options = array(
            'num'             => 10,    #����
            'bno'             => false, #����
            'staffid'         => false, #Ա��ID
            'memberId'        => false, #��ԱID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($bno)$whereSql .= "and bno = '{$bno}' " ;
        if($staffid)$whereSql .= "and staffid = '{$staffid}' " ;
        if($memberId)$whereSql .= "and memberId = '{$memberId}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'bills',    #����
                    'cols'          => 'id id,bno bno,useScore useScore,useCard useCard,price price,discount discount,staffid staffid,staffName staffName,tm tm,memberId memberId',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ���һ������������Ϣ
     */
    public static function getBillsInfo($params){
        $options = array(
            'id'              => false, #ID
            'bno'             => false, #����
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;
        if($bno)$whereSql .= "and bno = '{$bno}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'bills',    #����
                    'cols'          => 'id id,bno bno,useScore useScore,useCard useCard,price price,discount discount,staffid staffid,staffName staffName,tm tm,memberId memberId',   #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ��ö�����ϸ�����б�
     */
    public static function getBillsItemList($params){
        $options = array(
            'num'             => 10,    #����
            'bid'             => false, #����ID
            'bno'             => false, #����
            'proId'           => false, #��ƷID
            'staffid'         => false, #Ա��
            'memberId'        => false, #��Ա
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($bid)$whereSql .= "and bid = '{$bid}' " ;
        if($bno)$whereSql .= "and bno = '{$bno}' " ;
        if($proId)$whereSql .= "and proId = '{$proId}' " ;
        if($staffid)$whereSql .= "and staffid = '{$staffid}' " ;
        if($memberId)$whereSql .= "and memberId = '{$memberId}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'billsItem',    #����
                    'cols'          => 'id id,bid bid,bno bno,proId proId,num num,discount discount,price price,staffid staffid,memberId memberId,tm tm',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ���һ��������ϸ������Ϣ
     */
    public static function getBillsItemInfo($params){
        $options = array(
            'id'              => false, #ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'billsItem',    #����
                    'cols'          => 'id id,bid bid,bno bno,proId proId,num num,discount discount,price price,staffid staffid,memberId memberId,tm tm',   #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }





    
    
}
