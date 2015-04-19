<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">商品管理</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>商品管理</h2>
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="ProductSm" name="c">
条码:<input style="width:120px;" class="spanmalt10" type="text" value="<?=$sercode?>" name="code" placeholder="条码">
商品名:<input style="width:120px;" class="spanmalt10" type="text" value="<?=$sername?>" name="name" placeholder="商品名">
<select name="cateId">
    <option value="0">所有分类</option>
    <?php
    if($cateInfo){
        foreach($cateInfo as $k => $v){
            $seled = $k == $sercateId ? "selected" : "";
            echo "<option value='{$k}' {$seled}>{$v}</option>";
        }
        
    }?>
</select>
<button type="submit" class="btn-ser">查看</button></form></div>
				    
				    <!-- 列表 -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>商品名</th><th>分类</th><th>条码</th><th>售价</th><th>库存</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   foreach($data as $v) {
       $outStr = '<tr>';
       $outStr.='<td style="text-align:left" data="name" rel="'.$v['id'].'" >'.$v['name'].'</td>';
       $outStr.='<td>'.(isset($cateInfo[$v['cateId']]) ? $cateInfo[$v['cateId']] : '').'</td>';
       $outStr.='<td  data="code" rel="'.$v['id'].'" >'.$v['code'].'</td>';
       $outStr.='<td  data="price" rel="'.$v['id'].'" >'.round($v['price']/100,2).'</td>';
       $outStr.='<td class="editColumn11111" data="stock" rel="'.$v['id'].'" >'.$v['stock'].'</td>';
       //$outStr.='<td  data="score" rel="'.$v['id'].'" >'.$v['score'].'</td>';
       //$outStr.='<td  data="discut" rel="'.$v['id'].'" >'.$v['discut'].'</td>';
       
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
						
                          <tr><td align="right">商品名:</td><td><input type="text"   name="name" /></td></tr>
                          <tr><td align="right">条码号:</td><td><input type="text"   name="code" /></td></tr>
                          <tr><td align="right">分类:</td><td>
                                <select name="cateId"><option value='0'>请选择</option>
                                <?php
                                if ($cateInfo) {
                                       foreach ($cateInfo as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                                </select> </td></tr>
                          <tr><td align="right">售价:</td><td><input type="text"   name="price" /></td></tr>
                          <tr><td align="right">进货价:</td><td><input type="text"   name="inPrice" /></td></tr>
                          <tr><td align="right">库存数量:</td><td><input type="text"   name="stock" /></td></tr>
                          <!-- <tr><td align="right">积分比例:</td><td><input type="text" value='1'  name="score" /> <span style="color:#666666">1表示一元积一分</span></td></tr> -->
                          <tr><td align="right">最低折扣:</td><td><input type="text" value='0'  name="discut"  /> 用小数表示,如:0.8 </td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="Product">
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
                          <tr><td align="right">商品名:</td><td><input type="text" id="name"  name="name" /></td></tr>
                          <tr><td align="right">条码:</td><td><input type="text" id="code"  name="code" /></td></tr>
                          <tr><td align="right">分类:</td><td>
                              <select id="cateId" name="cateId"><option value='0'>请选择</option>
                                <?php
                                if ($cateInfo) {
                                       foreach ($cateInfo as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                                </select>   
                              </td></tr>
                          <tr><td align="right">售价:</td><td><input type="text" id="price"  name="price" /></td></tr>
                          <tr><td align="right">进货价:</td><td><input type="text" id="inPrice"  name="inPrice" /></td></tr>
                          <tr><td align="right">库存数量:</td><td><input type="text" id="stock"  name="stock" /></td></tr>
                          <!-- <tr><td align="right">积分比例:</td><td><input type="text" id="score"  name="score" /> <span style="color:#666666">1表示一元积一分</span></td></tr> -->
                          <tr><td align="right">最低折扣:</td><td><input type="text" id="discut"  name="discut" /> 用小数表示,如:0.8</td></tr>

                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="Product">
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
        <input type="hidden" value="Product" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnProduct').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'Product',function(dat){
        $('#name').val(dat['name']);
        $('#code').val(dat['code']);
        $('#cateId').val(dat['cateId']);
        $('#price').val(dat['price']);
        $('#inPrice').val(dat['inPrice']);
        $('#stock').val(dat['stock']);
        $('#score').val(dat['score']);
        $('#discut').val(dat['discut']);
    }); 
 });
$('.tempEdit').live('blur',function(){
    var newVal = $(this).val();
    var docid = $(this).parent().attr('rel');
    var name = $(this).attr('name');
    var pamStr = '&'+name+'='+newVal;
    updata(docid,'Product','UpItem',pamStr);
    $(this).parent().html(newVal).addClass("editColumn");
 });


//-------------------------------
//用一个按钮修改一种状态的后台
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=Product&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=Product';
        }
   });
}

</script>
</body>
</html>

