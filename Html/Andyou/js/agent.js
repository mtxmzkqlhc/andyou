 Array.prototype.in_array = function(e)
    {
        for(i=0;i<this.length;i++)
        {
            if(this[i] == e)
            return true;
        }
        return false;
    }
 function submitForum(){
         
         //post����ͨ���ж�
          //��ʾͨ������
     var dialogParam = {  //����content֮������ֵ��Ҫ�ı䣬Ҫ�õĻ��� ���Ƶ��ֲ������иı�
        lock : true, /* ���������ֲ㡿  */
        title:'��ʾ��Ϣ',
        width : '242px',
        height: '117px',
        icon:'error',  // error,message,message,succeed,warning
        ok: function () {
                        this.close();
                        return false;
                    },
        content:''
     };
        var cacheDom = {};
         
        var  postArr = {};
         $('.valfield').each(function(){
             var key = $(this).attr('name');
             var colname = $(this).attr('colname');
             var val = $(this).val();
             if('telZone' != key && 'telPhone' && 'subStationUserid' != key && (null === val || '' ===  val  ) ){
                 dialogParam.content = colname+'����Ϊ��';
                 var that  = this;
                 dialogParam.ok = function () {
                        this.close();
                        $(that).focus();
                        return false;
                    };
                $.dialog( dialogParam); 
                
                return false;
             }  
             postArr[key] = val;
             cacheDom[key] = this;
              
         });
          
         var regMobilePartton=/1[3-8]+\d{9}/;
         if(!regMobilePartton.test(postArr['mobileTel'])){
             dialogParam.content =  '�ֻ����벻��ȷ';
                 var that  = this;
                 dialogParam.ok = function () {
                        this.close();
                        $(cacheDom['mobileTel']).focus();
                        return false;
                    };
                $.dialog( dialogParam); 
         };
       var regex =  /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/ ;
          if(!regex.test(postArr['email'])){
               
             dialogParam.content =  '�����ַ����ȷ';
                 var that  = this;
                 dialogParam.ok = function () {
                        this.close();
                        $(cacheDom['email']).focus();
                        return false;
                    };
                $.dialog( dialogParam); 
         };
         
    if($('#agentTable').find('.agentTr').size()>0){
        postArr['agentArea'] = Array();
        //��ʼ�ռ�����
        $('#agentTable').find('.agentTr').each(function(){
            postArr['agentArea'].push($(this).attr('data-p')+'-'+$(this).attr('data-c'));
        });
    }
         
        $.ajax({
               type: "POST",
               url: "/?c=Agent_New",
               cache:false,
               
               data: postArr,
               success: function(data){
                   if(1 == parseInt(data.status) ) {
                        
                        window.location.href='/?c=Agent_List';
                   }else{
                        dialogParam.content =  data.tips;
                        var that  = this;
                        dialogParam.ok = function () {
                            this.close();
                            $(cacheDom['mobileTel']).focus();
                            return false;
                        };
                    $.dialog( dialogParam); 
                   }
               },
               dataType: "json"
         });  
    }
function addAreaBind (){
    $("#provinceId").rselect({ //ʡ��
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�
        
    });

    $("#cityId").rselect({ //����

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&val=&sel='

        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�

        ,relSel:    "#provinceId" //������ѡ���

    });
    
     $("#agentProvinceId").rselect({ //ʡ��
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�
        
    });

    $("#agentCityId").rselect({ //����

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&val=&sel='

        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�

        ,relSel:    "#provinceId" //������ѡ���

    });
}
 
if('Add' == 'actionType'){
    $("#provinceId").rselect({ //ʡ��
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�
        
    });



    $("#cityId").rselect({ //����

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&val=&sel='

        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�

        ,relSel:    "#provinceId" //������ѡ���

    });
    
         $("#agentProvinceId").rselect({ //ʡ��
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�
        
    });

    $("#agentCityId").rselect({ //����

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&val=&sel='

        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�

        ,relSel:    "#provinceId" //������ѡ���

    });
}else{
    var provinceId = $("#provinceId").val();
     $("#provinceId").rselect({ //ʡ��
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '��ѡ��', //Ĭ�ϵ��ı�
        initVal: provinceId
    });


  var cityId = $("#cityId").val();
    $("#cityId").rselect({ //����

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&sel=&val='+provinceId

        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�

        ,relSel:    "#provinceId", //������ѡ���
        initVal: cityId

    });
    
   var agentProvinceId = $("#agentProvinceId").val();
     $("#agentProvinceId").rselect({ //ʡ��
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '��ѡ��', //Ĭ�ϵ��ı�
        initVal: agentProvinceId
    });


  var agentCityId = $("#agentCityId").val();
    $("#agentCityId").rselect({ //����

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&sel=&val='+provinceId

        ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�

        ,relSel:    "#agentProvinceId", //������ѡ���
        initVal: agentCityId

    });
}
//��ӳ���
$('#addCityBtn').click(function(){
    
     //��ʾͨ������
     var dialogParam = {  //����content֮������ֵ��Ҫ�ı䣬Ҫ�õĻ��� ���Ƶ��ֲ������иı�
        lock : true, /* ���������ֲ㡿  */
        title:'��ʾ��Ϣ',
        width : '242px',
        height: '117px',
        icon:'error',  // error,message,message,succeed,warning
        ok: function () {
                        this.close();
                        return false;
                    },
        content:''
     };
     
    var 
    agentProvinceVal = $('#agentProvinceId').val(),//����ʡ��
    agentCityVal = $('#agentCityId').val(),//�������
    agentProvinceText = $('#agentProvinceId option:selected').text(),//ʡ������
    agentCityText = $('#agentCityId option:selected').text();//��������
    
    if(agentProvinceVal==0){
         dialogParam.content =  '����ѡ�����ʡ��';
         dialogParam.ok = function () {
                this.close();
                return false;
            };
        $.dialog( dialogParam); 
        return false;
    }
    
    if(!agentVerifyBox(agentProvinceVal,agentCityVal)){
         dialogParam.content =  '���Ѿ���ӹ��õ���';
         dialogParam.ok = function () {
                this.close();
                return false;
            };
        $.dialog( dialogParam); 
        return false;
    }
      var postArr={};
      
      postArr['a'] = 'AgentAreaExsit';
      postArr['name'] = $('#id-userid').val();
      postArr['provinceId'] = agentProvinceVal;
      postArr['cityId'] = agentCityVal;

      $.ajax({
           type: "POST",
           url: "/?c=Agent_New",
           cache:false,

           data: postArr,
           success: function(data){
               if(2 == parseInt(data) ) {
                    dialogParam.content =  '�˵����Ѿ��д����̹���';
                    var that  = this;
                    dialogParam.ok = function () {
                        this.close();
                        return false;
                    };
                $.dialog( dialogParam); 
               }else{
                       var isExsit = 0;
                        isExsit = $('#agentTd').find('#agentTable').size();//�õ��Ƿ��д��������ͷ
                        if(isExsit==0){ //��������ڱ�ͷ�ͼ���
                            $("#agentTd").append('<p id="agentTitle">�ѹش��������</p><table class="table table-striped table-bordered" id="agentTable"><tbody><tr><td>����ʡ��</td><td>�������</td><td>����</td></tr>');                                      
                        }
                        var agentDiv = '<tr class="agentTr" data-p="'+agentProvinceVal+'" data-c="'+agentCityVal+'">';
                            agentDiv += '<td data-id="'+agentProvinceVal+'">'+agentProvinceText+'</td>';
                            if(agentCityVal>0){
                                agentDiv += '<td data-id="'+agentCityVal+'">'+agentCityText+'</td>';
                            }else{
                                agentDiv += '<td data-id="0">ȫʡ</td>';
                            }
                            agentDiv += '<td><button style="height:33px;" class="btn btn-large btn-danger cancel-btn">ȡ��</button></td>';
                            agentDiv += '</tr>';
                        $("#agentTable").append(agentDiv);
               }
           },
           dataType: "json"
     }); 
    

    
    return false;
});
 
//ȡ������
$('.cancel-btn').live('click',function(){
   $(this).parents('.agentTr').remove();
    var agentTrExsit = 0; //�ж��Ƿ��й�������
    agentTrExsit = $('#agentTable').find('.agentTr').size();
    if(agentTrExsit==0){ //���û���˾�����ͷһ���Ƴ�
        $('#agentTitle').remove();
        $('#agentTable').remove();
    }
   return false;
});

//���������֤����
function agentVerifyBox(agentProvinceId,agentCityId){
    //���ռ�ҳ�����ѹ����Ĵ������
    var agentTrExsit = 0; //�ж��Ƿ��й�������
    agentTrExsit = $('#agentTable').find('.agentTr').size();
    if(agentTrExsit==0){ //����ǵ�һ��ֱ������
        return true;
    }
    var agentProvinceArr = [],//ʡ�ݺ���
    agentCityArr = [],//���к���
    agentAllPrivinceArr = [];//ȫʡ����
    //��ʼ�ռ�����
    $('#agentTable').find('.agentTr').each(function(){
        agentProvinceArr.push($(this).attr('data-p'));
        agentCityArr.push($(this).attr('data-c'));
        if($(this).attr('data-c')==0){
            agentAllPrivinceArr.push($(this).attr('data-p'));
        }
    });
    //������в���ȫʡ�͵�����֤
    if(agentCityId!=0){
        if(agentCityArr.in_array(agentCityId)){ //�������ID�Ѿ����ھ���ӹ�
            return false;
        }
        if(agentAllPrivinceArr.in_array(agentProvinceId)){ //���е�����֤��Ҫ��֤���ʡ��֮ǰ�ǲ����Ѿ�ȫʡ����
            return false;
        }
    }else{//�����ȫʡ�� ��������֤ȫʡ����
        if(agentProvinceId!=0){
            if(agentAllPrivinceArr.in_array(agentProvinceId)){ //��֤ȫʡ����
                return false;
            }
            //���ȫʡ����ͨ����,�ٿ�����ǰ��ľ����ӹ���ʡ�ĳ���
            if(agentProvinceArr.in_array(agentProvinceId)){
                //֮ǰ�й���Ӵ�ʡ�ĵ����ˣ�����Ҫ�ɵ�����Ȼ�󻻳�ȫʡ
               $('#agentTable').find("[data-p="+agentProvinceId+"]").remove();
            }
        }else{
            return false;
        }
    }
    return true;
}