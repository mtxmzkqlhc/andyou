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
                <h2><i class="halflings-icon list-alt"></i><span class="break"></span>同步操作</h2>
            </div>
            <div class="box-content clearfix" style="padding-left:40px;padding-right:40px;">
                <table class="table table-bordered" id="dayInfoTbl">
                    <tr><td>+ <a href="?c=Rsync_Member&a=UpAll" target="_blank">将会员同步到云端</a></td></tr>
                    <tr><td>+ <a href="?c=Rsync_Member&a=UpLog" target="_blank">将会员操作消费同步到云端</a></td></tr>
                </table>
            </div>
        </div>
        
        
    </div>
</div>

            
<?= $footer ?>

</body>
</html>