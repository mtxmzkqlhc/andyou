<?= $header ?>
<?= $navi ?>
<style>
    #addUserTbl {font-size:12px;}
    #addUserTbl td{text-align: left;}
</style>
<div class="content" style="padding-top:20px;">
        
			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>��Ʒ���</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix">
                	 <form id="addform" method="post" action="?" onsubmit="return doCheckIpt()">
                         <table class="table table-center table-striped table-bordered" id="addUserTbl">
                        <tbody>
                        
                          <tr><td align="right">����:</td><td><?=$billInfo["bno"]?></td></tr>
                          <tr><td align="right">���ѽ��:</td><td><?=round($billInfo["price"]/100,2)?></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="name"  name="name" /></td></tr>
                          <tr><td align="right">�ֻ���:</td><td><input type="text"  id="phone"   name="phone" /></td></tr>
                          <tr><td align="right">����:</td><td>
                          <select name="cateId" id="cateId"><option value='0'>��ѡ��</option>
                                <?php
                                if ($memberCate) {
                                       foreach ($memberCate as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                           </select>    
                          </td></tr>
                          <tr><td align="right">����:</td><td><input type="text" name="byear"  style="width:60px" /> �� <input type="text" name="bmonth"  style="width:60px" /> �� <input type="text" name="bday"  style="width:60px" /> ��</td></tr>
                          <tr><td align="right">�ɻ����:</td><td><?=$canGetScore?></td></tr>
                         <!-- <tr><td align="right">�����:</td><td><input type="text"   name="balance" value='0'/></td></tr> -->
                          <tr><td align="right">��ע:</td><td><textarea  name="remark"  style="width:350px;height:50px"></textarea></td></tr>
                          <tr><td>&nbsp;</td><td><input type="submit" value="ȷ�����" class="btn btn-primary"/></td></tr>
                          

                         </tbody></table>
                         <input type="hidden" name="a" value="AddUserFromBill">
                         <input type="hidden" name="c" value="Member">
                         <input type="hidden" name="bid" value="<?=$billInfo["id"]?>">
                </form>
					
                </div>
            </div>

        </div>

</div>

<?= $footer ?>
<script>
var doCheckIpt = function(){
    var cid = $("#cateId").val();
    if(cid == 0){
        alert("��ѡ�����");
        return false;
    }
    if($("#name").val() == "" || $("#phone").val() == ""){        
        alert("����д������");
        return false;
    }
    return true;
    
}
var bkUrl = '<?=$pageUrl?>';

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
        $('#remark').html(dat['remark']);
    }); 
 });


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

</script>
</body>
</html>

