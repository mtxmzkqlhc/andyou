<?php
/**
 * �����ѧ���㹫ʽ���㷨
 *
 * @author Ǯ־ΰ
 * @copyright (c) 2013-1-31
 */
class ZOL_Algorithm
{
    /**
     * Ƥ��ѷ�������ϵ��
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
        #������ƫ�����
        $sum1 = array_sum($p1Row);
        $sum2 = array_sum($p2Row);
        #��ƽ����
        $sum1Sq = $sum2Sq = 0;
        foreach ($p1Row as $p1Col) { $sum1Sq += pow($p1Col, 2); }
        foreach ($p2Row as $p2Col) { $sum2Sq += pow($p2Col, 2); }
        #��˻�֮��
        $pSum = 0;
        foreach ($p1Row as $key=>$p1Col) {
            $pSum += $p1Col * $p2Row[$key];
        }
        #����Ƥ��ѷ����ֵ
        $num = $pSum - ($sum1*$sum2/$colNum);
        $denominator = sqrt(($sum1Sq-pow($sum1, 2)/$colNum)*($sum2Sq-pow($sum2, 2)/$colNum)); #��ĸ
        if($denominator==0) return 0;
        $r = $num/$denominator;
        return $r;
    }
    
    /**
     * ���Ҷ�ʱ�������ƶ�
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
        #�������
        $member = 0;
        foreach ($p1Row as $key=>$p1Col) {
            $member += $p1Col*$p2Row[$key];
        }
        #�����ĸ
        $sum1Sq = 0;#p1��ƽ����
        $sum2Sq = 0;#p2��ƽ����
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

    //�������
    public static function checkData($matrix, $p1, $p2)
    {
        $error = array(
            1=>'������������������<2',
            2=>'������������Ʒֻ����һ�����ھ���'
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
     * ɸѡָ�����ʵ�Ԫ��
     * @param type $paramArr 
     */
    public static function getOptimal($paramArr = array()) {
        $options = array(
            'data'      => array(),     #����
            'rate'      => 0.1,         #��ֵ��
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