/* 
 * author zhangxc
 * desc �� ����ҳ��Ԫ�� ����ֵ �Զ����¼�
 */


!function($){
	var Autobind = function(elemment,options){
		this.$elemment = $(elemment);
		this.options   = $.extend({},$.fn.autobind.defaults,options);
	}
	//������������prototypeԭ����
	Autobind.prototype = {
		init : function (){
			var bindEvent = $(this.$elemment).attr(this.options.bindEvent) ; 
			var bindFunc = $(this.$elemment).attr(this.options.bindFunc);
			var elemParam =  $(this.$elemment).attr(this.options.elemParam);
			//Ĭ��Ϊclick �¼�
			if(!bindEvent){
				bindEvent = this.options.defaultEvent;
			}
			var paramSplit = bindFunc.split("."); //  ʹ�� - �ָ� ������ ʹ�� . ����ĳЩ����º�jquery��ͻ
			//��ȡִ�к���
			var func = window;
			var eventNameSpace = '';
			for(var index = 0 ; index < paramSplit.length ;index++){
				var t = typeof (func);
                                
				if('function' === t || 'object' === t){
					func = func[paramSplit[index]];
					eventNameSpace  = eventNameSpace+'.'+paramSplit[index];
				}
			}
			if('function' === typeof (func)){
				//�Ƿ񽫰󶨵�Ԫ�ش������󶨵ĺ���
				var giveElemParam = true;
				if(this.options.domDefaultParam){
					if(0 == parseInt(elemParam)){
						giveElemParam = false;
					}
				}else{
					giveElemParam = false;
					if(1 == parseInt(elemParam)){
						giveElemParam = true;
					}
				}
				
				var that = this.$elemment;
				//��-�¼�-����
				$(this.$elemment).on(bindEvent+'.'+eventNameSpace ,function(e){
					if(giveElemParam){
						func(that);
					}else{
						func();
					}
                                });
			}else {
					if(this.options.debug){
						this.debug(bindFunc);
					}
					 
				}

		},
		//����
		debug : function(funcName){
			console.log('sorry,function:'+funcName+' not found');
		},
		
	}

	var old = $.fn.autobind;

	$.fn.autobind = function(option){
		return this.each(function () {
	      var $this = $(this)
	        , data = $this.data('autobind')
	        , options = typeof option == 'object' && option
	      if (!data) $this.data('autobind', (data = new Autobind(this, options)))
	      	if (typeof option == 'string') data[option]() 
	      	else data.init()
	    })
	}
	//��ͨ���������ı�
	$.fn.autobind.defaults = {
		domDefaultParam : true,     //�Ƿ�Ĭ�� ����ӦԪ��dom���󴫵ݸ����󶨵ĺ���
		    			            //�������ݿ��Դ����dom �ϣ��������õ�ʱ����Ի�ȡ,Ĭ�϶�����
		defaultEvent    :'click',   //Ĭ�ϰ󶨵��¼� click
		debug           : true,     // �Ƿ������ԣ���δ�ҵ�����󶨵ĺ����� console.log ��Ϣ 
                                    //������ֵ ,����������������������� ��ͻʱ�޸�
		bindFunc : 'bind-func',     //Ĭ�ϵĺ���ֵ����
		bindEvent  : 'bind-event',  //Ĭ�ϵ��¼�ֵ����
		elemParam  : 'data-param'  //�Ƿ�Ԫ�ش�������   ������������
                                    //���ڵ�������  1 ��  0 ����   �� elemDaultParam �ֿ���
                                    //��������elemDefaultParam��ô���ã����ֵ����Ч							
	}

    $.fn.autobind.noConflict = function () {
      $.fn.autobind = old
      return this
    }

    $.fn.autobind.constructor = Autobind;
}(window.jQuery)

$(document).ready(function(){
	$('[bind-func]').autobind();
 });


