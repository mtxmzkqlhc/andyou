<?php
/**
 * 与图表相关的函数
 * 仲伟涛
 * 2013-11-27
 */
class Helper_Func_Chart extends Helper_Abstract {
    
    
    /**
     * 获得饼形图的代码
     */
    public static function getPieHtml($paramArr) {
		$options = array(
			'title'          =>  false,    #主标题
            'data'           =>  array(),#数据 key => 对应数据数组
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
        if($title){#标题
            $htmlStr .= "title: { text: '{$title}', },";      
        }else{
            $htmlStr .= "title: null,";
        }
        
        #真正数据部分
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
     * 获得曲线的代码
     */
    public static function getLineHtml($paramArr) {
		$options = array(
			'width'          =>  '300px',    #宽度
			'height'         =>  '200px',    #高度
			'title'          =>  false,  #主标题
			'subTitle'       =>  false,  #副标题
            'data'           =>  array(),#数据 key => 对应数据数组
            'xArr'           =>  false,  #横坐标数据
            'yTitle'         =>  '',#
            'yMin'           =>  '',     #纵坐标最小值          
            'yReversed'      =>  false,  #是否反转纵坐标
            'radius'         =>  0,      #原点大小
            'yallowDecimals' =>  true,   #纵坐标中是否显示带有小数点的坐标 
            'valueSuffix'    => '',      #坐标数据显示的后缀
            'legend'         => 'false',  
            'xRot'           => false
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        #真正数据部分
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
        
        if($title){#标题
            $htmlStr .= "title: { text: '{$title}', },";      
        }else{
            $htmlStr .= "title: null,";
        }
        if($subTitle){#副标题
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
        
        #返回数据
        return $htmlStr;
    }
    
}
