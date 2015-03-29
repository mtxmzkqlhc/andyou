<!DOCTYPE html>
<!--[if lte IE 9]> 
	<html lang="en" class="js no-inlinesvg"> 
<![endif]-->

<!--[if gt IE 9]> 
	<html lang="en" class="js inlinesvg"> 
<![endif]-->
<!--[if !IE]><!--> <html lang="en" class="js inlinesvg"> <!--<![endif]-->
<!-- BEGIN HEAD http://www.keenthemes.com/preview/metronic/index.html -->
<head>
	<meta charset="GBK" />
	<title>��Ʒ���������</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="css/bootstrap2.3.min.css" rel="stylesheet" type="text/css"/>
	<link href="css/bootstrap-responsive2.3.min.css" rel="stylesheet" type="text/css"/>
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="css/style-metro.css" rel="stylesheet" type="text/css"/>
	<link href="css/style.css" rel="stylesheet" type="text/css"/>
	<link href="css/style-responsive.css" rel="stylesheet" type="text/css"/>
	<link href="css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
	<link href="css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="css/halflings.css" rel="stylesheet" type="text/css"/>
    <link href="css/jquery-ui110.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/newstyle-modify.css" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
    <!--[if lt IE 9]> 
	<link id="ie-style" href="css/ie.css" rel="stylesheet">
    <![endif]--> 
	<link rel="shortcut icon" href="image/star.ico" />
    <style>
        .box-content{padding:15px 10px;}
        .page-title{display:none;}
        #content{padding-top:15px;}
        .select{
            font-size: 13px;
        }
        .table-center td{
            text-align: center;
        }
         .table-center th{
            text-align: center;
        }
    </style>
    
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed">   <!-- BEGIN HEADER -->

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">��Ա����</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>��Ա����</h2>
				     <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> �������</button>
                </div>
                <div class="box-content">
                	 <!-- ���� -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="Member" name="c">
<input style="width:120px;" class="spanmalt10" type="text" value="<?=$sername?>" name="name" placeholder="����">
<input style="width:120px;" class="spanmalt10" type="text" value="<?=$serphone?>" name="phone" placeholder="�ֻ���">
<input style="width:120px;" class="spanmalt10" type="text" value="<?=$sercateId?>" name="cateId" placeholder="����">
<button type="submit" class="btn-ser">�鿴</button></form></div>
				    
				    <!-- �б� -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>ID</th><th>����</th><th>�ֻ���</th><th>����</th><th>������</th><th>������</th><th>������</th><th>���ʱ��</th><th>����</th><th>���</th><th>����</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   foreach($data as $v) {
       $outStr = '<tr>';
       $outStr.='<td>'.$v['id'].'</td>';
       $outStr.='<td>'.$v['name'].'</td>';
       $outStr.='<td>'.$v['phone'].'</td>';
       $outStr.='<td>'.$v['cateId'].'</td>';
       $outStr.='<td>'.$v['byear'].'</td>';
       $outStr.='<td>'.$v['bmonth'].'</td>';
       $outStr.='<td>'.$v['bday'].'</td>';
       $outStr.='<td>'.$v['addTm'].'</td>';
       $outStr.='<td>'.$v['score'].'</td>';
       $outStr.='<td>'.$v['balance'].'</td>';
       $outStr.='<td rel="'.$v['id'].'">
       <a title="�޸�" class="btn btn-info editbtnMember"><i class="halflings-icon white edit"></i></a>
       <a title="ɾ��" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a></td>';
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

					
					<div class="alert alert-success">
						<strong>˵����</strong><br/>
						1.������ʹ�ñȽϸ߰汾�������<br/>
					</div>
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
                      <table class="table table-striped table-bordered"> <tbody>
						<tr><td align="right">selectʾ��</td><td><select name="dataStatus"><option value='0'>��ѡ��</option>
                         <?
                         if ($dataStatusArr) {
                                foreach ($dataStatusArr as $k=>$v) {
                                    echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                } 
                            }
                          ?>
                         </select></td></tr>
						                          <tr><td align="right">����:</td><td><input type="text"   name="name" /></td></tr>
                          <tr><td align="right">�ֻ���:</td><td><input type="text"   name="phone" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text"   name="cateId" /></td></tr>
                          <tr><td align="right">������:</td><td><input type="text"   name="byear" /></td></tr>
                          <tr><td align="right">������:</td><td><input type="text"   name="bmonth" /></td></tr>
                          <tr><td align="right">������:</td><td><input type="text"   name="bday" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text"   name="score" /></td></tr>
                          <tr><td align="right">���:</td><td><input type="text"   name="balance" /></td></tr>

                         </tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="Member">
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
                   <table class="table table-striped table-bordered"><tbody>
							                          <tr><td align="right">����:</td><td><input type="text" id="name"  name="name" /></td></tr>
                          <tr><td align="right">�ֻ���:</td><td><input type="text" id="phone"  name="phone" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="cateId"  name="cateId" /></td></tr>
                          <tr><td align="right">������:</td><td><input type="text" id="byear"  name="byear" /></td></tr>
                          <tr><td align="right">������:</td><td><input type="text" id="bmonth"  name="bmonth" /></td></tr>
                          <tr><td align="right">������:</td><td><input type="text" id="bday"  name="bday" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="score"  name="score" /></td></tr>
                          <tr><td align="right">���:</td><td><input type="text" id="balance"  name="balance" /></td></tr>

                          <tr><td align="right">selectʾ��:</td><td><select id="dataStatus" name="dataStatus"><option value='0'>��ѡ��</option>
                         <?
                         if ($dataStatusArr) {
                                foreach ($dataStatusArr as $k=>$v) {
                                    echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                } 
                            }
                          ?>
                         </select></td></tr>
                        </tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="Member">
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
        <input type="hidden" value="Member" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<!-- BEGIN FOOTER -->

<script src="js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="js/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<!--[if lt IE 9]>
<script src="js/respond.min.js"></script>  
<![endif]-->
<script type="text/javascript" src="http://icon.zol-img.com.cn/public/js/swfupload.js"></script>
<script type="text/javascript" src="http://icon.zol-img.com.cn/d/Image/js/uploadInput.js"></script> 
<script src='js/jquery.dataTables.min.js'></script>
<script src="js/index.js"></script>
<!-- END CORE PLUGINS -->
<script src="js/app.js"></script>      
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.source.js?skin=default"></script>
<script>
jQuery(document).ready(function() {
    App.init();
});
</script>
<!-- END JAVASCRIPTS -->

<script type="text/javascript">

var bkUrl = '<?=$pageUrl?>';

//-------------------------------
//��һ����ť�޸�һ��״̬�ĺ�̨
//-------------------------------
var setDataValue = function(id,col,val){
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=Member&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=Member';
        }
   });
}
$('.editbtnMember').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'Member',function(dat){
        $('#name').val(dat['name']);
        $('#phone').val(dat['phone']);
        $('#cateId').val(dat['cateId']);
        $('#byear').val(dat['byear']);
        $('#bmonth').val(dat['bmonth']);
        $('#bday').val(dat['bday']);
        $('#score').val(dat['score']);
        $('#balance').val(dat['balance']);
    }); 
 });


</script>
</body>
</html>

