/* 
 * ��������Ա���JS
 */



//������Ա
iptEnter("#memberPhone",function(){
    doSearchMember();
});
$("#searchMemBtn").click(function(){
    doSearchMember();
});
//������
var doSearchMember = function(){
    var phone = $("#memberPhone").val();
    if(phone){
        var url = "?c=Ajax_Member&a=GetMemberByPhone&phone=" + phone;
        $.getJSON(url,{},function(data){
            if(data){
                var bls = data.balance ? data.balance : 0;
                $("#memtbl_name").html(data.name);
                $("#bill_end_membernm").val(data.name);
                $("#memtbl_score").html(data.score);
                $("#memtbl_card").html(bls);
                $("#memtbl_cate").html(data.cateName);
                $("#memtbl_disc").html(data.discount);
                $("#memtbl_remark").html(data.remark);
                $("#bill_member_card").val(bls);

                $("#memberId").val(data.id);
                $("#bill_member_card").removeAttr("readonly");
                $("#bill_member_score").removeAttr("readonly");

                $("#bill_card_left").val(bls);
                $("#bill_score_left").val(data.score);
                $(".memextinfo").show();

                memberDisc = data.discount;//��Ա�ۿ�
                if(memberDisc > 1)memberDisc = 1;

                //�ȹذ�ť����ʾ
                $("#removeMemInfo").show();
            }
        });
    }else{
        removeMemInfo();
    }
}
//�����Ա�����Ϣ
var removeMemInfo = function(){

    $("#memtbl_name").html("");
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

    $("#memberPhone").val("");

    //�ȹذ�ť������
    $("#removeMemInfo").hide();

}
//�����Ա��Ϣ
$("#removeMemInfo").click(function(){
    if(confirm("ȷ����ջ�Ա��Ϣ��")){
        removeMemInfo();
    }
});