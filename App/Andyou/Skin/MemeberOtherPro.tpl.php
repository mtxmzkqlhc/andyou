<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">������Ʒ����</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>������Ʒ����</h2>
                </div>
                <div class="box-content">
                	 <!-- ���� -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="MemeberOtherPro" name="c">
��Ա��<input style="width:120px;" class="spanmalt10" type="text" value="<?=$member?>" name="member" placeholder="">
<button type="submit" class="btn-ser">�鿴</button></form></div>
				    
				    <!-- �б� -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
                        <tr>
                        <th>��Ա</th><th>��ԱID</th><th>��Ʒ��</th><th>������</th><th>����</th><th>����ʱ��</th><th>����</th>
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
                               <a title="�޸�" class="btn btn-info editbtnMemeberOtherPro"><i class="halflings-icon white edit"></i></a>
                               <a title="ɾ��" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a></td>';
                               */
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
                          <tr><td align="right">��ԱID:</td><td><input type="text"   name="memberId" /></td></tr>
                          <tr><td align="right">��ƷID:</td><td><input type="text"   name="proId" /></td></tr>
                          <tr><td align="right">������:</td><td><input type="text"   name="name" /></td></tr>
                          <tr><td align="right">��Ʒ��:</td><td><input type="text"   name="proName" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="MemeberOtherPro">
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
                          <tr><td align="right">��ԱID:</td><td><input type="text" id="memberId"  name="memberId" /></td></tr>
                          <tr><td align="right">��ƷID:</td><td><input type="text" id="proId"  name="proId" /></td></tr>
                          <tr><td align="right">������:</td><td><input type="text" id="name"  name="name" /></td></tr>
                          <tr><td align="right">��Ʒ��:</td><td><input type="text" id="proName"  name="proName" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="num"  name="num" /></td></tr>
                          <tr><td align="right">����ʱ��:</td><td><input type="text" id="buytm"  name="buytm" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="ctype"  name="ctype" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="MemeberOtherPro">
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
//��һ����ť�޸�һ��״̬�ĺ�̨
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

