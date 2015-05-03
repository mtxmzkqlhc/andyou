<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">管理员管理</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>管理员管理</h2>
				     <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> 添加数据</button>
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="AdminUser" name="c">
<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$seruserId?>" name="userId" placeholder="用户名">
<button type="submit" class="btn-ser">查看</button></form></div>
				    
				    <!-- 列表 -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>ID</th><th>用户名</th><th>身份</th><th>操作</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   foreach($data as $v) {
       $outStr = '<tr>';
       $outStr.='<td>'.$v['id'].'</td>';
       $outStr.='<td>'.$v['userId'].'</td>';
       $outStr.='<td>'.(isset($typeArr[$v['isAdmin']]) ? $typeArr[$v['isAdmin']] : '').'</td>';
       $outStr.='<td rel="'.$v['id'].'">
       <a title="修改" class="btn btn-info editbtnAdminUser"><i class="halflings-icon white edit"></i></a>
       <a title="删除" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a></td>';
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
                          <tr><td align="right">用户名:</td><td><input type="text"   name="userId" /></td></tr>
                          <tr><td align="right">密码:</td><td><input type="text"   name="passwd" /></td></tr>
                          <tr><td align="right">身份:</td><td>                              
                          <select name="isAdmin"><option value='0'>请选择</option>
                                <?php
                                if ($typeArr) {
                                       foreach ($typeArr as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                           </select>    
                              
                         </td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="AdminUser">
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
                          <tr><td align="right">用户名:</td><td><input type="text" id="userId"  name="userId" /></td></tr>
                          <tr><td align="right">密码:</td><td><input type="text" id="passwd"  name="passwd" /></td></tr>
                          <tr><td align="right">身份:</td><td>                              
                          <select name="isAdmin" id="isAdmin"><option value='0'>请选择</option>
                                <?php
                                if ($typeArr) {
                                       foreach ($typeArr as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                           </select>    
                              </td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="AdminUser">
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
        <input type="hidden" value="AdminUser" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnAdminUser').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'AdminUser',function(dat){
        $('#userId').val(dat['userId']);
        $('#passwd').val(dat['passwd']);
        $('#isAdmin').val(dat['isAdmin']);
    }); 
 });


//-------------------------------
//用一个按钮修改一种状态的后台
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=AdminUser&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=AdminUser';
        }
   });
}

</script>
</body>
</html>

