<?php
/**
 * 消费记录
 */
class Helper_Bill extends Helper_Abstract {
   
    /**
     * 获得今日的收入
     */
    public static function getDayIncome($params=array()){
        $options = array(
            'startTm'             => strtotime(date("Y-m-d 00:00:00",SYSTEM_TIME)), #开始时间
            'endTm'               => strtotime(date("Y-m-d 23:59:59",SYSTEM_TIME)), #结束时间
            'groupDay'            => false, #是否按照日汇总
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
     * 获得一个单号
     */
    public static function getMaxBno(){
        $db = Db_Andyou::instance();
        //获得今天订单个数
        $date = date("Y-m-d ",SYSTEM_TIME);
        $startTm = strtotime($date."00:00:00");
        $sql = "select count(*) from bills where tm >".$startTm;
        $num = $db->getOne($sql);
        
        return date("Ymd",SYSTEM_TIME) . sprintf("%06d",($num+1));
    }


    /**
     * 获得订单管理列表
     */
    public static function getBillsList($params){
        $options = array(
            'num'             => 10,    #数量
            'bno'             => false, #单号
            'staffid'         => false, #员工ID
            'memberId'        => false, #会员ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($bno)$whereSql .= "and bno = '{$bno}' " ;
        if($staffid)$whereSql .= "and staffid = '{$staffid}' " ;
        if($memberId)$whereSql .= "and memberId = '{$memberId}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'bills',    #表名
                    'cols'          => 'id id,bno bno,useScore useScore,useCard useCard,price price,discount discount,staffid staffid,staffName staffName,tm tm,memberId memberId',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得一条订单管理信息
     */
    public static function getBillsInfo($params){
        $options = array(
            'id'              => false, #ID
            'bno'             => false, #单号
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;
        if($bno)$whereSql .= "and bno = '{$bno}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'bills',    #表名
                    'cols'          => 'id id,bno bno,useScore useScore,useCard useCard,price price,discount discount,staffid staffid,staffName staffName,tm tm,memberId memberId',   #列名
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得订单明细管理列表
     */
    public static function getBillsItemList($params){
        $options = array(
            'num'             => 10,    #数量
            'bid'             => false, #订单ID
            'bno'             => false, #单号
            'proId'           => false, #产品ID
            'staffid'         => false, #员工
            'memberId'        => false, #会员
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
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'billsItem',    #表名
                    'cols'          => 'id id,bid bid,bno bno,proId proId,num num,discount discount,price price,staffid staffid,memberId memberId,tm tm',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得一条订单明细管理信息
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
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'billsItem',    #表名
                    'cols'          => 'id id,bid bid,bno bno,proId proId,num num,discount discount,price price,staffid staffid,memberId memberId,tm tm',   #列名
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }





    
    
}
