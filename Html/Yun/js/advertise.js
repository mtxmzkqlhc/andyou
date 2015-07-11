var  Ad = {};

Ad = {
    data:{ },//ajax 发送至  php端的数据
    c : {
        tips:"",     //提示
        ajaxUrl:"/?c=Ad_AdProUnit&a=",  //ajax 基本路径
        isPosting:false,    //是否正在提交数据
        elem:null,          // 当前触发事件的  元素 dom
        ajaxData:{},            // 执行ajax 后返回的数据
    },
    //注册ajax事件响应配置  name 为php端响应事件,callback 为 ajax返回数据后执行的 js响应函数名
    act:{
         setAdOnline:{name:"SetAdOnline",callback:"" },
         adAction:{name:"Modify",callback:0 }
     },
    //广告上线
    setAdOnline:function(elem){
        var adid = $(elem).attr('adid');
        Ad.data = {'adid':adid};
        Ad.postData(Ad.act.setAdOnline);
        
    },
    adAction:function(elem){
        var adid = $(elem).attr('adid');
        var unitid = $(elem).attr('unitid');
        var col = $(elem).attr('col');
        var val = $(elem).attr('val');
        Ad.data = {'adid':adid,
            'unitid':unitid,
            'col':col,
            'val':val
        };
        Ad.postData(Ad.act.adAction);
        
    },
    //ajax 发送数据
    postData : function(act){
        if(Ad.c.isPosting){
            return false;
        }
        if({} === Ad.c.data){
            return false;
        }
        $.ajax({
            type: 'POST',
            url: Ad.c.ajaxUrl+act.name+"&"+Math.random(),
            data: Ad.data,
            success: function(data) {
                Ad.c.isPosting = false;
                var data = eval('(' + data + ')');
                Ad.c.ajaxData = data;
                
                 
                //没定义回调函数的，alert提示或者 刷新页面
                if(0 === act.callback){
                    location.reload();
                }else if(1 == act.callback){
                    $.dialog({
                            lock : true, //锁屏【遮罩层】
                            content : data.tips,
                    });
                    
                }else{
                    //执行回调函数
                    var evalStr = "Ad."+act.callback+"();";
                    eval(evalStr);
                }
                 
            }
        });
    },
    //显示提示，先alert 
    showTips :function(tips){
        if(tips){
            alert(tips);
        }else{
            alert(Message.c.tips);
        }
        
    }
}
