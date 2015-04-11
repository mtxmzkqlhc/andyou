<?= $header ?>
<?= $navi ?>
<style>
    #dayInfoTbl{margin:15px 0;}
   #dayInfoTbl .d_l{text-align: right;width: 95px;background: #eee;}
</style>
<script src="js/echarts-all.js"></script>
<div id="content">
    <div class="row-fluid">

        <div class="box">
            <div class="box-header">
                <h2><i class="halflings-icon list-alt"></i><span class="break"></span>�����������</h2>
                <div class="box-icon">
                    <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                    <a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
                </div>
            </div>
            <div class="box-content clearfix">
                <table class="table table-bordered" id="dayInfoTbl">
                    <tr>
                    <?php
                    if($todayBillInfo && $todayBillInfo["cnt"]>1){
                    ?>
                    <td class="d_l">��������</td><td><?=$todayBillInfo['cnt']?></td>
                    <td class="d_l">��������룺</td><td><?=$todayBillInfo['price']/100?></td>
                    <td class="d_l">��Ա�����ģ�</td><td><?=$todayBillInfo['useCard']?></td>
                    <td class="d_l">��Ա�������ģ�</td><td><?=$todayBillInfo['useScore']?></td>
                    <?php
                    }else{
                        echo "<td>������δ���ţ�</td>";
                    }
                    ?>
                    </tr>
                </table>
            </div>
        </div>
        
        
        <div class="box">
            <div class="box-header">
                <h2><i class="halflings-icon list-alt"></i><span class="break"></span>30��Ӫ������</h2>
                <div class="box-icon">
                    <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                    <a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
                </div>
            </div>
            <div class="box-content clearfix" id="monthLines" style="height:400px;">
                
            </div>
        </div>
    </div>
</div>

            <script>
                var myChart = echarts.init(document.getElementById('monthLines'));
                var option = {
                    tooltip : {
                        trigger: 'axis'
                    },
                    legend: {
                        data:['���������','������']
                    },
                    toolbox: { show : false},
                    calculable : true,
                    xAxis : [{
                            type : 'category',
                            boundaryGap : false,
                            data : ['<?=implode("','",array_keys($monthBillInfo))?>']
                    }],
                    yAxis : [{
                            type : 'value'
                        }],
                    series : [
                        <?php
                        $comma = "";
                        $rmbArr = array();
                        $cntArr = array();

                        foreach($monthBillInfo as $k => $v){
                            $rmbArr[] = $v["price"]/100;
                            $cntArr[] = $v["cnt"];

                        }
                        ?>
                        {
                            name:'���������',
                            type:'line',
                            stack: '����',
                            data:[<?=implode(",",$rmbArr)?>]
                        },
                        {
                            name:'������',
                            type:'line',
                            stack: '����',
                            data:[<?=implode(",",$cntArr)?>]
                        }
                    ]
                };
                myChart.setOption(option);
            </script>

<?= $footer ?>

</body>
</html>