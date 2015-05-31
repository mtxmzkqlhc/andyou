<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">其他产品管理</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>其他产品管理</h2>
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="MemeberOtherPro" name="c">
会员：<input style="width:120px;" class="spanmalt10" type="text" value="<?=$member?>" name="member" placeholder="">
<button type="submit" class="btn-ser">查看</button></form></div>
				    
				    <!-- 列表 -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
                        <tr>
                        <th>会员</th><th>会员ID</th><th>商品名</th><th>服务名</th><th>数量</th><th>购买时间</th><th>类型</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if($data) {
                           foreach($data as $v) {
                               $memInfo = Helper_Member::getMemberInfo(array('id' => $v['memberId'] ));
                               $outStr = '<tr>';
                               $outStr.='<td>'.($memInfo ? $memInfo["name"] : "").'</td>';
                               $outStr.='<td>'.($memInfo ? $memInfo["phone"] : "").'</td>';
                               $outStr.='<td>'.$v['proName'].'</td>';
                               $outStr.='<td>'.$v['name'].'</td>';
                               $outStr.='<td>'.$v['num'].'</td>';
                               $outStr.='<td>'.date("Y-m-d H:i",$v['buytm']).'</td>';
                               $outStr.='<td>'.$proCtypeArr[$v['ctype']]['name'].'</td>';
                               /*
                               $outStr.='<td rel="'.$v['id'].'">
                               <a title="修改" class="btn btn-info editbtnMemeberOtherPro"><i class="halflings-icon white edit"></i></a>
                               <a title="删除" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a></td>';
                               */
                               $outStr.='</tr>';
                               echo $outStr;
                           }
                        } ?>
                        </tbody>
                     
                    </table>
                    
                    <!-- 分页 -->
					<?php if ($pageBar) { ?>
                    <div class="row-fluid"><div class="span12 center"><div class="dataTables_paginate paging_bootstrap pagination"><ul><?=$pageBar?></ul></div></div></div>
					<?php } ?>

					
                </div>
            </div>

        </div>

    </div>
</div>

<div class="modal hide fade" id="add-box" style="width:760px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">添加数据</h4>
            </div>
            <div class="modal-body">
                <form id="addform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"> <tbody>						
                          <tr><td align="right">会员ID:</td><td><input type="text"   name="memberId" /></td></tr>
                          <tr><td align="right">商品ID:</td><td><input type="text"   name="proId" /></td></tr>
                          <tr><td align="right">服务名:</td><td><input type="text"   name="name" /></td></tr>
                          <tr><td align="right">商品名:</td><td><input type="text"   name="proName" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="MemeberOtherPro">
						 <input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="addbtn">添加</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal hide fade" id="edit-box" style="width:760px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">编辑数据</h4>
            </div>
            <div class="modal-body">
                <form id="editform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"><tbody>
                          <tr><td align="right">会员ID:</td><td><input type="text" id="memberId"  name="memberId" /></td></tr>
                          <tr><td align="right">商品ID:</td><td><input type="text" id="proId"  name="proId" /></td></tr>
                          <tr><td align="right">服务名:</td><td><input type="text" id="name"  name="name" /></td></tr>
                          <tr><td align="right">商品名:</td><td><input type="text" id="proName"  name="proName" /></td></tr>
                          <tr><td align="right">数量:</td><td><input type="text" id="num"  name="num" /></td></tr>
                          <tr><td align="right">购买时间:</td><td><input type="text" id="buytm"  name="buytm" /></td></tr>
                          <tr><td align="right">类型:</td><td><input type="text" id="ctype"  name="ctype" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="MemeberOtherPro">
					<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="savebtn">保存</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="del-box" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4>删除</h4>
    </div>
    <div class="modal-body" >
        此操作不可逆,确定要删除数据吗?
    </div>
    <form id="delform" method="post" action="?">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <a class="btn btn-danger" target="_self" id="delbtn">删除</a>
        </div>
        <input type="hidden" id="deldataid" name="dataid" value="">
        <input type="hidden" value="DelItem" name="a"/>
        <input type="hidden" value="MemeberOtherPro" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnMemeberOtherPro').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'MemeberOtherPro',function(dat){
        $('#memberId').val(dat['memberId']);
        $('#proId').val(dat['proId']);
        $('#name').val(dat['name']);
        $('#proName').val(dat['proName']);
        $('#num').val(dat['num']);
        $('#buytm').val(dat['buytm']);
        $('#ctype').val(dat['ctype']);
    }); 
 });


//-------------------------------
//用一个按钮修改一种状态的后台
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=MemeberOtherPro&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=MemeberOtherPro';
        }
   });
}

</script>
</body>
</html>

