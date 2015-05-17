/* 
 * ��������Ա���JS
 */



//������Ա
iptEnter("#memberPhone",function(){
    doSearchMember();
});
$("#searchMemBtn2").click(function(){
    doSearchMember();
});
//������
var doSearchMember = function(){
    $("#addbtn").attr("disabled",true);
    var phone = $("#memberPhone").val();
    if(phone){
        var t = Date.parse(new Date()); 
        var url = "?c=Ajax_Member&a=GetMemberByPhone&phonecard=" + phone+"&t="+t;
        $.getJSON(url,{},function(data){
            if(data){
                var bls = data.balance ? data.balance : 0;
                $("#memtbl_name").show().html(data.name);
                $("#memtbl_score").html(data.score);
                memberAllScore = data.score;
                $("#memtbl_card").html(bls);
                $("#memtbl_cate").html(data.cateName);
                $("#memtbl_disc").html(data.discount);
                $("#memtbl_remark").html(data.remark);
                $("#memtbl_allsum").html(data.allsum);
                
                if(data.score < minCheckoutScore){
                    alert("�û�Ա�Ļ���δ�ﵽ��Ͷһ�ֵ�����ܽ��жһ���");
                    return false;
                }
                $("#listBox").show();
                $("#addbtn").removeAttr("disabled");
    
                $("#bill_member_card").val(bls);
                $("#bill_end_membernm").val(data.name);

                $("#memberId").val(data.id);
                $("#bill_member_card").removeAttr("readonly");
                $("#bill_member_score").removeAttr("readonly");

                $("#bill_card_left").val(bls);
                $("#bill_score_left").val(data.score);
                $(".memextinfo").show();

                memberDisc = data.discount;//��Ա�ۿ�
                if(memberDisc > 1)memberDisc = 1;
                    
                 $("#bill_disc").val(memberDisc);
                //�ȹذ�ť����ʾ
                $("#removeMemInfo").show();
                
                //����һ�½��
                refreshRightTbl();
                calcBillSumInfo();
            }
        });
    }else{
        removeMemInfo();
    }
}
//�����Ա�����Ϣ
var removeMemInfo = function(){

    $("#memtbl_name").hide().html("");
    $("#bill_end_membernm").val("");
    $("#memtbl_score").html("");
    $("#memtbl_card").html("");
    $("#memtbl_cate").html("");
    $("#memberId").val(0);
    $("#bill_member_card").attr("readonly","true");
    $("#bill_member_score").attr("readonly","true");
    $("#bill_card_left").val(0);
    $("#bill_score_left").val(0);
    $(".memextinfo").hide();
    $("#memtbl_remark").html("");
    $("#memtbl_disc").html("");
    $("#memtbl_allsum").html("");

    $("#memberPhone").val("");

    //�ȹذ�ť������
    $("#removeMemInfo").hide();
    //����һ�½��
    refreshRightTbl();
    calcBillSumInfo();

}
//�����Ա��Ϣ
$("#removeMemInfo").click(function(){
    if(confirm("ȷ����ջ�Ա��Ϣ��")){
        removeMemInfo();
        $("#listBox").hide();
    }
});