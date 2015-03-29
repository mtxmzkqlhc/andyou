<?php
/**
 * 相关数学计算公式或算法
 *
 * @author 钱志伟
 * @copyright (c) 2013-1-31
 */
class ZOL_Algorithm
{
    /**
     * 皮尔逊计算相关系数
     * @param array $matrix
     * @param type $p1
     * @param type $p2
     * @return real 
     */
    public static function sim_pearson($matrix, $p1, $p2)
    {
        if (!self::checkData($matrix, $p1, $p2)) return 'error';

        $colNum = count($matrix[$p1]);
        $p1Row = $matrix[$p1];
        $p2Row = $matrix[$p2];
        #对所有偏好求和
        $sum1 = array_sum($p1Row);
        $sum2 = array_sum($p2Row);
        #求平方和
        $sum1Sq = $sum2Sq = 0;
        foreach ($p1Row as $p1Col) { $sum1Sq += pow($p1Col, 2); }
        foreach ($p2Row as $p2Col) { $sum2Sq += pow($p2Col, 2); }
        #求乘积之和
        $pSum = 0;
        foreach ($p1Row as $key=>$p1Col) {
            $pSum += $p1Col * $p2Row[$key];
        }
        #计算皮尔逊评价值
        $num = $pSum - ($sum1*$sum2/$colNum);
        $denominator = sqrt(($sum1Sq-pow($sum1, 2)/$colNum)*($sum2Sq-pow($sum2, 2)/$colNum)); #分母
        if($denominator==0) return 0;
        $r = $num/$denominator;
        return $r;
    }
    
    /**
     * 余弦定时计算相似度
     * @param array $matrix
     * @param type $p1
     * @param type $p2
     * @return real 
     */
    public static function sim_cosine(array $matrix, $p1, $p2)
    {
        if (!self::checkData($matrix, $p1, $p2)) return 'error';
        
        $colNum = count($matrix);
        $p1Row = $matrix[$p1];
        $p2Row = $matrix[$p2];    
        #计算分子
        $member = 0;
        foreach ($p1Row as $key=>$p1Col) {
            $member += $p1Col*$p2Row[$key];
        }
        #计算分母
        $sum1Sq = 0;#p1的平方和
        $sum2Sq = 0;#p2的平方和
        foreach ($p1Row as $p1Col) { $sum1Sq += pow($p1Col, 2); }
        foreach ($p2Row as $p2Col) { $sum2Sq += pow($p2Col, 2); }

        $denominator = sqrt($sum1Sq)*sqrt($sum2Sq);
        if($denominator==0) return 0;
        
        $r = $member/$denominator;
        return $r;
    }
    /**
     *
     * @param array $matrix
     * @param type $p1
     * @param type $p2
     * @return real 
     */
    public static function sim_euclidean(array $matrix, $p1, $p2)
    {
        if (!self::checkData($matrix, $p1, $p2)) return 'error';
        
        $colNum = count($matrix);
        $p1Row = $matrix[$p1];
        $p2Row = $matrix[$p2];
        
        
        return 0.00;
    }

    //检查数据
    public static function checkData($matrix, $p1, $p2)
    {
        $error = array(
            1=>'矩阵不是数组或矩阵行数<2',
            2=>'参与计算的两物品只少有一个不在矩阵'
        );
        if (!is_array($matrix) || count($matrix)<2) { echo $error[1]; return false; }
        if (!isset($matrix[$p1]) || !isset($matrix[$p2])) { echo $err[2]; return false; }
        return true;
    }
    
    public static function distance($d1, $d2) 
    {
        return abs($d1-$d2);
    }
    
    /**
     * 筛选指定比率的元素
     * @param type $paramArr 
     */
    public static function getOptimal($paramArr = array()) {
        $options = array(
            'data'      => array(),     #数据
            'rate'      => 0.1,         #阀值率
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        if(!$data) return array();

        $sum = array_sum($data);
        if(!$sum) return array();

        $ret = array();
        foreach ($data as $k=>$val) {
            if($val/$sum>=$rate) {
                $ret[] = $k;
            }
        }
        return $ret;
    }
}