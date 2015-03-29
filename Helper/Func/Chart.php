<?php
/**
 * ��ͼ����صĺ���
 * ��ΰ��
 * 2013-11-27
 */
class Helper_Func_Chart extends Helper_Abstract {
    
    
    /**
     * ��ñ���ͼ�Ĵ���
     */
    public static function getPieHtml($paramArr) {
		$options = array(
			'title'          =>  false,    #������
            'data'           =>  array(),#���� key => ��Ӧ��������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $id = time();
        $charId = "pie_" . $id . rand(0, 100);
        
        $htmlStr = "<div id='".$charId."'></div><script type='text/javascript'>
                   $(function () {
                        
                            $('#{$charId}').highcharts({
                                chart: {
                                    plotBackgroundColor: null,
                                    plotBorderWidth: null,
                                    plotShadow: false
                                },";
        if($title){#����
            $htmlStr .= "title: { text: '{$title}', },";      
        }else{
            $htmlStr .= "title: null,";
        }
        
        #�������ݲ���
        $dataStr = "";
        if($data){
            $comma = "";
            foreach($data as $k => $v){                
                $dataStr .= $comma .  "['{$k}',$v]";
                $comma = ",";
            }
        }
        
        $htmlStr .= "
                        tooltip: {
                            pointFormat: ' <b>{point.percentage:.1f}%</b>'
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: false
                                },
                                showInLegend: true
                            }
                        },
                        series: [{
                            type: 'pie',
                            name: '',
                            data: [{$dataStr}]
                        }]
                    });

            });</script>";
            return $htmlStr;
    }
    
    /**
     * ������ߵĴ���
     */
    public static function getLineHtml($paramArr) {
		$options = array(
			'width'          =>  '300px',    #���
			'height'         =>  '200px',    #�߶�
			'title'          =>  false,  #������
			'subTitle'       =>  false,  #������
            'data'           =>  array(),#���� key => ��Ӧ��������
            'xArr'           =>  false,  #����������
            'yTitle'         =>  '',#
            'yMin'           =>  '',     #��������Сֵ          
            'yReversed'      =>  false,  #�Ƿ�ת������
            'radius'         =>  0,      #ԭ���С
            'yallowDecimals' =>  true,   #���������Ƿ���ʾ����С��������� 
            'valueSuffix'    => '',      #����������ʾ�ĺ�׺
            'legend'         => 'false',  
            'xRot'           => false
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        #�������ݲ���
        $dataStr = "";
        if($data){
            $comma = "";
            foreach($data as $name => $d){                
                $dataStr .= $comma .  "{name: '{$name}',data: [".  implode(",", $d)."],lineWidth:2,marker:{radius: ".$radius."}}";
                $comma = ",";
            }
        }
        
        $xAxis = $xArr && is_array($xArr) ? "'".implode("','",$xArr)."'" : '';
        $id = time();
        $charId = "line_" . $id . rand(0, 100);
        $htmlStr = "<div id='".$charId."' style='width:{$width};height:{$height}'></div><script type='text/javascript'>
                    $(function () {
                        $('#{$charId}').highcharts({";
        
        if($title){#����
            $htmlStr .= "title: { text: '{$title}', },";      
        }else{
            $htmlStr .= "title: null,";
        }
        if($subTitle){#������
            $htmlStr .= "subtitle: { text: '{$subTitle}', },";
        }else{
            $htmlStr .= "subtitle: null,";
        }        
        if($yReversed){
            $yReversed = 'reversed : true,';
        }else{
            $yReversed = '';
        }
        if(!$yallowDecimals){
            $yallowDecimals = 'allowDecimals : false,';
        }else{
            $yallowDecimals = 'allowDecimals : true,';
        }
        if(is_numeric($yMin)){
            $yMin = "min:{$yMin},";
        }else{
            $yMin = '';
        }
        $xLabRot  = "";
        if($xRot){
            $xLabRot = " ,labels: {rotation: {$xRot}}    ";
        }
        $htmlStr .= <<<EOT
       
                xAxis: {
                    categories: [{$xAxis}],
                    labels:	{
                        enabled:false
                    }
                    {$xLabRot}                      
                },
                yAxis: {
                    {$yReversed}
                    {$yallowDecimals}
                    title: {
                        text: null,margin:0
                    },
                    {$yMin}
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    valueSuffix: '{$valueSuffix}',
                    shared: true,
                    //crosshairs: true,
                    headerFormat: '<b>{point.x}</b><br/>'
                    //pointFormat: '{point.y}'
                },
                legend: {
                    enabled: {$legend}
                },
                series: [{$dataStr}]
            });
        });
        </script>
EOT;
        
        #��������
        return $htmlStr;
    }
    
}
