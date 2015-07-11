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
         
         //post数据通用判断
          //提示通用配置
     var dialogParam = {  //除了content之外其他值不要改变，要用的话请 复制到局部变量中改变
        lock : true, /* 锁屏【遮罩层】  */
        title:'提示消息',
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
                 dialogParam.content = colname+'不能为空';
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
             dialogParam.content =  '手机号码不正确';
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
               
             dialogParam.content =  '邮箱地址不正确';
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
        //开始收集数据
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
    $("#provinceId").rselect({ //省份
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '请选择' //默认的文本
        
    });

    $("#cityId").rselect({ //城市

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&val=&sel='

        ,topTxt:    '请选择' //默认的文本

        ,relSel:    "#provinceId" //关联的选择框

    });
    
     $("#agentProvinceId").rselect({ //省份
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '请选择' //默认的文本
        
    });

    $("#agentCityId").rselect({ //城市

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&val=&sel='

        ,topTxt:    '请选择' //默认的文本

        ,relSel:    "#provinceId" //关联的选择框

    });
}
 
if('Add' == 'actionType'){
    $("#provinceId").rselect({ //省份
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '请选择' //默认的文本
        
    });



    $("#cityId").rselect({ //城市

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&val=&sel='

        ,topTxt:    '请选择' //默认的文本

        ,relSel:    "#provinceId" //关联的选择框

    });
    
         $("#agentProvinceId").rselect({ //省份
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '请选择' //默认的文本
        
    });

    $("#agentCityId").rselect({ //城市

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&val=&sel='

        ,topTxt:    '请选择' //默认的文本

        ,relSel:    "#provinceId" //关联的选择框

    });
}else{
    var provinceId = $("#provinceId").val();
     $("#provinceId").rselect({ //省份
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '请选择', //默认的文本
        initVal: provinceId
    });


  var cityId = $("#cityId").val();
    $("#cityId").rselect({ //城市

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&sel=&val='+provinceId

        ,topTxt:    '请选择' //默认的文本

        ,relSel:    "#provinceId", //关联的选择框
        initVal: cityId

    });
    
   var agentProvinceId = $("#agentProvinceId").val();
     $("#agentProvinceId").rselect({ //省份
        'url' : '/?c=Ajax_Select&a=AreaOptions&sel='
        ,topTxt:    '请选择', //默认的文本
        initVal: agentProvinceId
    });


  var agentCityId = $("#agentCityId").val();
    $("#agentCityId").rselect({ //城市

        'url' : '/?c=Ajax_Select&a=AreaOptions&datatype=1&sel=&val='+provinceId

        ,topTxt:    '请选择' //默认的文本

        ,relSel:    "#agentProvinceId", //关联的选择框
        initVal: agentCityId

    });
}
//添加城市
$('#addCityBtn').click(function(){
    
     //提示通用配置
     var dialogParam = {  //除了content之外其他值不要改变，要用的话请 复制到局部变量中改变
        lock : true, /* 锁屏【遮罩层】  */
        title:'提示消息',
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
    agentProvinceVal = $('#agentProvinceId').val(),//代理省份
    agentCityVal = $('#agentCityId').val(),//代理城市
    agentProvinceText = $('#agentProvinceId option:selected').text(),//省份中文
    agentCityText = $('#agentCityId option:selected').text();//城市中文
    
    if(agentProvinceVal==0){
         dialogParam.content =  '请先选择代理省份';
         dialogParam.ok = function () {
                this.close();
                return false;
            };
        $.dialog( dialogParam); 
        return false;
    }
    
    if(!agentVerifyBox(agentProvinceVal,agentCityVal)){
         dialogParam.content =  '您已经添加过该地区';
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
                    dialogParam.content =  '此地区已经有代理商关联';
                    var that  = this;
                    dialogParam.ok = function () {
                        this.close();
                        return false;
                    };
                $.dialog( dialogParam); 
               }else{
                       var isExsit = 0;
                        isExsit = $('#agentTd').find('#agentTable').size();//拿到是否有代理地区标头
                        if(isExsit==0){ //如果不存在标头就加入
                            $("#agentTd").append('<p id="agentTitle">已关代理地区：</p><table class="table table-striped table-bordered" id="agentTable"><tbody><tr><td>代理省份</td><td>代理城市</td><td>操作</td></tr>');                                      
                        }
                        var agentDiv = '<tr class="agentTr" data-p="'+agentProvinceVal+'" data-c="'+agentCityVal+'">';
                            agentDiv += '<td data-id="'+agentProvinceVal+'">'+agentProvinceText+'</td>';
                            if(agentCityVal>0){
                                agentDiv += '<td data-id="'+agentCityVal+'">'+agentCityText+'</td>';
                            }else{
                                agentDiv += '<td data-id="0">全省</td>';
                            }
                            agentDiv += '<td><button style="height:33px;" class="btn btn-large btn-danger cancel-btn">取消</button></td>';
                            agentDiv += '</tr>';
                        $("#agentTable").append(agentDiv);
               }
           },
           dataType: "json"
     }); 
    

    
    return false;
});
 
//取消地区
$('.cancel-btn').live('click',function(){
   $(this).parents('.agentTr').remove();
    var agentTrExsit = 0; //判断是否还有关联数据
    agentTrExsit = $('#agentTable').find('.agentTr').size();
    if(agentTrExsit==0){ //如果没有了就连标头一起移除
        $('#agentTitle').remove();
        $('#agentTable').remove();
    }
   return false;
});

//代理地区验证盒子
function agentVerifyBox(agentProvinceId,agentCityId){
    //先收集页面上已关联的代理地区
    var agentTrExsit = 0; //判断是否还有关联数据
    agentTrExsit = $('#agentTable').find('.agentTr').size();
    if(agentTrExsit==0){ //如果是第一次直接跳过
        return true;
    }
    var agentProvinceArr = [],//省份盒子
    agentCityArr = [],//城市盒子
    agentAllPrivinceArr = [];//全省盒子
    //开始收集数据
    $('#agentTable').find('.agentTr').each(function(){
        agentProvinceArr.push($(this).attr('data-p'));
        agentCityArr.push($(this).attr('data-c'));
        if($(this).attr('data-c')==0){
            agentAllPrivinceArr.push($(this).attr('data-p'));
        }
    });
    //如果城市不是全省就单独验证
    if(agentCityId!=0){
        if(agentCityArr.in_array(agentCityId)){ //如果城市ID已经存在就添加过
            return false;
        }
        if(agentAllPrivinceArr.in_array(agentProvinceId)){ //城市单独验证后还要验证这个省份之前是不是已经全省过了
            return false;
        }
    }else{//如果是全省了 就优先验证全省盒子
        if(agentProvinceId!=0){
            if(agentAllPrivinceArr.in_array(agentProvinceId)){ //验证全省盒子
                return false;
            }
            //如果全省盒子通过了,再看看此前有木有添加过此省的城市
            if(agentProvinceArr.in_array(agentProvinceId)){
                //之前有过添加此省的地区了，我们要干掉他们然后换成全省
               $('#agentTable').find("[data-p="+agentProvinceId+"]").remove();
            }
        }else{
            return false;
        }
    }
    return true;
}