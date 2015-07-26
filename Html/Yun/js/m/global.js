var el;
var showTips = function(txt){
    
    el=$.tips({
        content:txt,
        stayTime:2000,
        type:"success"
    })
    el.on("tips:hide",function(){
    })
}

$("#error_msg").tap(function(){
    $(this).hide();
})