var  Ad = {};

Ad = {
    data:{ },//ajax ������  php�˵�����
    c : {
        tips:"",     //��ʾ
        ajaxUrl:"/?c=Ad_AdProUnit&a=",  //ajax ����·��
        isPosting:false,    //�Ƿ������ύ����
        elem:null,          // ��ǰ�����¼���  Ԫ�� dom
        ajaxData:{},            // ִ��ajax �󷵻ص�����
    },
    //ע��ajax�¼���Ӧ����  name Ϊphp����Ӧ�¼�,callback Ϊ ajax�������ݺ�ִ�е� js��Ӧ������
    act:{
         setAdOnline:{name:"SetAdOnline",callback:"" },
         adAction:{name:"Modify",callback:0 }
     },
    //�������
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
    //ajax ��������
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
                
                 
                //û����ص������ģ�alert��ʾ���� ˢ��ҳ��
                if(0 === act.callback){
                    location.reload();
                }else if(1 == act.callback){
                    $.dialog({
                            lock : true, //���������ֲ㡿
                            content : data.tips,
                    });
                    
                }else{
                    //ִ�лص�����
                    var evalStr = "Ad."+act.callback+"();";
                    eval(evalStr);
                }
                 
            }
        });
    },
    //��ʾ��ʾ����alert 
    showTips :function(tips){
        if(tips){
            alert(tips);
        }else{
            alert(Message.c.tips);
        }
        
    }
}
