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
                <h2><i class="halflings-icon list-alt"></i><span class="break"></span>ͬ������</h2>
            </div>
            <div class="box-content clearfix" style="padding-left:40px;padding-right:40px;">
                <table class="table table-bordered" id="dayInfoTbl">
                    <tr><td>+ <a href="?c=Rsync_Member&a=UpAll" target="_blank">����Աͬ�����ƶ�</a></td></tr>
                    <tr><td>+ <a href="?c=Rsync_Member&a=UpLog" target="_blank">����Ա��������ͬ�����ƶ�</a></td></tr>
                    <tr><td>+ <a href="?c=Rsync_Member&a=UpNew" target="_blank">���ƶ˽������»�Ա����</a></td></tr>
                    <tr><td>+ <a href="?c=Rsync_Member&a=UpNew&onlyGetFromYun=1&allData=1" target="_blank">����ƶ����л�Ա��Ϣ</a></td></tr>
                    <tr><td>+ <a href="?c=Rsync_Item&a=UpData" target="_blank">ͬ����Ʒ��������������</a></td></tr>
                    <tr><td>+ <a href="?c=Rsync_Item&a=UpData&isAll=1" target="_blank">ͬ����Ʒ��������������</a></td></tr>
                </table>
            </div>
        </div>
        
        
    </div>
</div>

            
<?= $footer ?>

</body>
</html>