/**
 * selectѡ��ļ���
 */
;(function($){    
    	$.fn.rselect = function(options) {
            
            //Ĭ������
            var defaults = {				
                url:        ''  //���������URL
                ,topTxt:    '��ѡ��' //Ĭ�ϵ��ı�
                ,topVal:    '0' //Ĭ�ϵ�ֵ
                ,initVal:   '0' //��ʼֵ
                ,relSel:    false //������ѡ���
                ,relVal:    false //������ѡ����ѡ��ֵ
                ,randStr:   false //�����Ƿ��������ַ�������ajax����
                ,callBack:  function(){}
            };
            var options = $.extend(defaults, options);
        
            var nowObj = $(this);
            
            if(options.relSel){//�Ƿ�����һ���˵�
                $(options.relSel).change(function(){
                    options.relVal = $(this).val();
                    getData(nowObj,options);
                });
                
            }
            getData(nowObj,options);
            
            
            
    		return this;
    	}
        /**
         * ��ϸ�Ļ������
         */
        var getData = function(obj,options){            
            
            var url = options.url;
            if(options.relVal){
                url += '&val=' + options.relVal;
            }
            if(options.randStr){
                url += '&r=' + Math.random();
            }
            
            $.get(url,function(html){    
                //�滻��ʼ��ֵ
                if(options.initVal){
                    html.replace("value='"+options.initVal+"'", "selected='selected' value='"+options.initVal+"'");
                }
                
                obj.html("<option value='" + options.topVal + "'>" + options.topTxt + "</option>" + html);
                obj.val(options.initVal);                
            });            
        }
})( jQuery );