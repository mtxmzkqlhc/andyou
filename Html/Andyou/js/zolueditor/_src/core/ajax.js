/**
 * @file
 * @name UE.ajax
 * @short Ajax
 * @desc UEditor���õ�ajax����ģ��
 * @import core/utils.js
 * @user: taoqili
 * @date: 11-8-18
 * @time: ����3:18
 */
UE.ajax = function() {
    /**
     * ����һ��ajaxRequest����
     */
    var fnStr = 'XMLHttpRequest()';
    try {
        new ActiveXObject("Msxml2.XMLHTTP");
        fnStr = 'ActiveXObject(\'Msxml2.XMLHTTP\')';
    } catch (e) {
        try {
            new ActiveXObject("Microsoft.XMLHTTP");
            fnStr = 'ActiveXObject(\'Microsoft.XMLHTTP\')'
        } catch (e) {
        }
    }
    var creatAjaxRequest = new Function('return new ' + fnStr);


    /**
     * ��json����ת�����ʺ�ajax�ύ�Ĳ����б�
     * @param json
     */
    function json2str(json) {
        var strArr = [];
        for (var i in json) {
            //����Ĭ�ϵļ�������
            if(i=="method" || i=="timeout" || i=="async") continue;
            //���ݹ����Ķ���ͺ��������ύ֮��
            if (!((typeof json[i]).toLowerCase() == "function" || (typeof json[i]).toLowerCase() == "object")) {
                strArr.push( encodeURIComponent(i) + "="+encodeURIComponent(json[i]) );
            }
        }
        return strArr.join("&");

    }


    return {
		/**
         * @name request
         * @desc ����ajax����ajaxOpt��Ĭ�ϰ���method��timeout��async��data��onsuccess�Լ�onerror��������֧���Զ�����Ӳ���
         * @grammar UE.ajax.request(url,ajaxOpt);
         * @example
         * UE.ajax.request('http://www.xxxx.com/test.php',{
         *     //��ʡ�ԣ�Ĭ��POST
         *     method:'POST',
         *     //�����Զ������
         *     content:'�������ύ������',
         *     //Ҳ����ֱ�Ӵ�json������ֻ������Ϊdata��������һ���ַ�������
         *     data:{
         *         name:'UEditor',
         *         age:'1'
         *     }
         *     onsuccess:function(xhr){
         *         console.log(xhr.responseText);
         *     },
         *     onerror:function(xhr){
         *         console.log(xhr.responseText);
         *     }
         * })
		 * @param ajaxOptions
		 */
		request:function(url, ajaxOptions) {
            var ajaxRequest = creatAjaxRequest(),
                //�Ƿ�ʱ
                timeIsOut = false,
                //Ĭ�ϲ���
                defaultAjaxOptions = {
                    method:"POST",
                    timeout:5000,
                    async:true,
                    data:{},//��Ҫ���ݶ���Ļ�ֻ�ܸ���
                    onsuccess:function() {
                    },
                    onerror:function() {
                    }
                };

			if (typeof url === "object") {
				ajaxOptions = url;
				url = ajaxOptions.url;
			}
			if (!ajaxRequest || !url) return;
			var ajaxOpts = ajaxOptions ? utils.extend(defaultAjaxOptions,ajaxOptions) : defaultAjaxOptions;

			var submitStr = json2str(ajaxOpts);  // { name:"Jim",city:"Beijing" } --> "name=Jim&city=Beijing"
			//����û�ֱ��ͨ��data��������json�����������ҲҪ����json����ת��Ϊ�ַ���
			if (!utils.isEmptyObject(ajaxOpts.data)){
                submitStr += (submitStr? "&":"") + json2str(ajaxOpts.data);
			}
            //��ʱ���
            var timerID = setTimeout(function() {
                if (ajaxRequest.readyState != 4) {
                    timeIsOut = true;
                    ajaxRequest.abort();
                    clearTimeout(timerID);
                }
            }, ajaxOpts.timeout);

			var method = ajaxOpts.method.toUpperCase();
            var str = url + (url.indexOf("?")==-1?"?":"&") + (method=="POST"?"":submitStr+ "&noCache=" + +new Date);
			ajaxRequest.open(method, str, ajaxOpts.async);
			ajaxRequest.onreadystatechange = function() {
				if (ajaxRequest.readyState == 4) {
					if (!timeIsOut && ajaxRequest.status == 200) {
						ajaxOpts.onsuccess(ajaxRequest);
					} else {
						ajaxOpts.onerror(ajaxRequest);
					}
				}
			};
			if (method == "POST") {
				ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				ajaxRequest.send(submitStr);
			} else {
				ajaxRequest.send(null);
			}
		}
	};


}();
