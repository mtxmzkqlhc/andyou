<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">������ϸ����</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>������ϸ����</h2>
				     <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> �������</button>
                </div>
                <div class="box-content">
                	 <!-- ���� -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="BillsItem" name="c">
����ID��<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$serbid?>" name="bid" placeholder="����ID">
���ţ�<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$serbno?>" name="bno" placeholder="����">
��ƷID��<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$serproId?>" name="proId" placeholder="��ƷID">
Ա����<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$serstaffid?>" name="staffid" placeholder="Ա��">
��Ա��<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$sermemberId?>" name="memberId" placeholder="��Ա">
<button type="submit" class="btn-ser">�鿴</button></form></div>
				    
				    <!-- �б� -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>����</th><th>��Ʒ</th><th>ԭ��</th><th>����</th><th>�ۿ�</th><th>�ۼ�</th><th>����Ա</th><th>��Ա</th><th>����ʱ��</th><th>����</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   $proArr = array();
   foreach($data as $v) {
       $memName = "-";
       if($v['memberId']){
           $memInfo = Helper_Member::getMemberInfo(array("id"=>$v['memberId']));
           $memName = $memInfo["name"];
       }
       //��ò�Ʒ��Ϣ
       $proId = (int)$v['proId'];
       $proName = "";
       $proPrice = 0;
       if(!isset($proArr[$proId])){
           $proArr[$proId] = Helper_Product::getProductInfo(array('id'=>$proId));
       }
       if( $proArr[$proId]){
           $proName = $proArr[$proId]["name"];
           $proPrice = $proArr[$proId]["price"];
       }
       
       $outStr = '<tr>';
       $outStr.='<td algin="left">'.$v['bno'].'</td>';
       $outStr.='<td style="text-align:left">'.$proName.'</td>';
       $outStr.='<td>'.($proPrice).'</td>';
       $outStr.='<td>'.$v['num'].'</td>';
       $outStr.='<td>'.$v['discount'].'</td>';
       $outStr.='<td>'.($v['price']/100).'</td>';
       $outStr.='<td>'.(isset($staffInfo[$v['staffid']]) ? $staffInfo[$v['staffid']] : '-').'</td>';
       $outStr.='<td>'.$memName.'</td>';
       $outStr.='<td>'.date("Y-m-d H:i",$v['tm']).'</td>';
       $outStr.='<td rel="'.$v['id'].'">
       <a title="�޸�" class="btn btn-info editbtnBillsItem"><i class="halflings-icon white edit"></i></a>
       </td>';
       $outStr.='</tr>';
       echo $outStr;
   }
} ?>
</tbody>
                     
                    </table>
                    
                    <!-- ��ҳ -->
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
                <h4 class="modal-title">�������</h4>
            </div>
            <div class="modal-body">
                <form id="addform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"> <tbody>						
                          <tr><td align="right">����ID:</td><td><input type="text"   name="bid" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text"   name="bno" /></td></tr>
                          <tr><td align="right">��ƷID:</td><td><input type="text"   name="proId" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text"   name="num" /></td></tr>
                          <tr><td align="right">�ۿ�:</td><td><input type="text"   name="discount" /></td></tr>
                          <tr><td align="right">�۸�:</td><td><input type="text"   name="price" /></td></tr>
                          <tr><td align="right">Ա��:</td><td><input type="text"   name="staffid" /></td></tr>
                          <tr><td align="right">��Ա:</td><td><input type="text"   name="memberId" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="BillsItem">
						 <input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">�ر�</button>
                <button type="button" class="btn btn-primary" id="addbtn">���</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal hide fade" id="edit-box" style="width:760px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">�༭����</h4>
            </div>
            <div class="modal-body">
                <form id="editform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"><tbody>
                          <tr><td align="right">����ID:</td><td><input type="text" id="bid"  name="bid" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="bno"  name="bno" /></td></tr>
                          <tr><td align="right">��ƷID:</td><td><input type="text" id="proId"  name="proId" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="num"  name="num" /></td></tr>
                          <tr><td align="right">�ۿ�:</td><td><input type="text" id="discount"  name="discount" /></td></tr>
                          <tr><td align="right">�۸�:</td><td><input type="text" id="price"  name="price" /></td></tr>
                          <tr><td align="right">Ա��:</td><td><input type="text" id="staffid"  name="staffid" /></td></tr>
                          <tr><td align="right">��Ա:</td><td><input type="text" id="memberId"  name="memberId" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="BillsItem">
					<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">�ر�</button>
                <button type="button" class="btn btn-primary" id="savebtn">����</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="del-box" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4>ɾ��</h4>
    </div>
    <div class="modal-body" >
        �˲���������,ȷ��Ҫɾ��������?
    </div>
    <form id="delform" method="post" action="?">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">�ر�</button>
            <a class="btn btn-danger" target="_self" id="delbtn">ɾ��</a>
        </div>
        <input type="hidden" id="deldataid" name="dataid" value="">
        <input type="hidden" value="DelItem" name="a"/>
        <input type="hidden" value="BillsItem" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnBillsItem').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'BillsItem',function(dat){
        $('#bid').val(dat['bid']);
        $('#bno').val(dat['bno']);
        $('#proId').val(dat['proId']);
        $('#num').val(dat['num']);
        $('#discount').val(dat['discount']);
        $('#price').val(dat['price']);
        $('#staffid').val(dat['staffid']);
        $('#memberId').val(dat['memberId']);
    }); 
 });


//-------------------------------
//��һ����ť�޸�һ��״̬�ĺ�̨
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=BillsItem&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=BillsItem';
        }
   });
}

</script>
</body>
</html>

