/**
 * @file
 * @name UE.Utils
 * @short Utils
 * @desc UEditor��װʹ�õľ�̬���ߺ���
 * @import editor.js
 */
var utils = UE.utils = {
    /**
     * �������飬����nodeList
     * @name each
     * @grammar UE.utils.each(obj,iterator,[context])
     * @since 1.2.4+
     * @desc
     * * obj Ҫ�����Ķ���
     * * iterator �����ķ���,�����ĵ�һ���Ǳ�����ֵ���ڶ�������������������obj
     * * context  iterator��������
     * @example
     * UE.utils.each([1,2],function(v,i){
     *     console.log(v)//ֵ
     *     console.log(i)//����
     * })
     * UE.utils.each(document.getElementsByTagName('*'),function(n){
     *     console.log(n.tagName)
     * })
     */
    each : function(obj, iterator, context) {
        if (obj == null) return;
        if (obj.length === +obj.length) {
            for (var i = 0, l = obj.length; i < l; i++) {
                if(iterator.call(context, obj[i], i, obj) === false)
                    return false;
            }
        } else {
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    if(iterator.call(context, obj[key], key, obj) === false)
                        return false;
                }
            }
        }
    },

    makeInstance:function (obj) {
        var noop = new Function();
        noop.prototype = obj;
        obj = new noop;
        noop.prototype = null;
        return obj;
    },
    /**
     * ��source�����е�������չ��target������
     * @name extend
     * @grammar UE.utils.extend(target,source)  => Object  //������չ
     * @grammar UE.utils.extend(target,source,true)  ==> Object  //������չ
     */
    extend:function (t, s, b) {
        if (s) {
            for (var k in s) {
                if (!b || !t.hasOwnProperty(k)) {
                    t[k] = s[k];
                }
            }
        }
        return t;
    },

    /**
     * ģ��̳л��ƣ�subClass�̳�superClass
     * @name inherits
     * @grammar UE.utils.inherits(subClass,superClass) => subClass
     * @example
     * function SuperClass(){
     *     this.name = "С��";
     * }
     * SuperClass.prototype = {
     *     hello:function(str){
     *         console.log(this.name + str);
     *     }
     * }
     * function SubClass(){
     *     this.name = "С��";
     * }
     * UE.utils.inherits(SubClass,SuperClass);
     * var sub = new SubClass();
     * sub.hello("���Ϻ�!"); ==> "С�����Ϻã�"
     */
    inherits:function (subClass, superClass) {
        var oldP = subClass.prototype,
            newP = utils.makeInstance(superClass.prototype);
        utils.extend(newP, oldP, true);
        subClass.prototype = newP;
        return (newP.constructor = subClass);
    },

    /**
     * ��ָ����context��Ϊfn�����ģ�Ҳ����this
     * @name bind
     * @grammar UE.utils.bind(fn,context)  =>  fn
     */
    bind:function (fn, context) {
        return function () {
            return fn.apply(context, arguments);
        };
    },

    /**
     * �����ӳ�delayִ�еĺ���fn
     * @name defer
     * @grammar UE.utils.defer(fn,delay)  =>fn   //�ӳ�delay����ִ��fn������fn
     * @grammar UE.utils.defer(fn,delay,exclusion)  =>fn   //�ӳ�delay����ִ��fn����exclusionΪ�棬�򻥳�ִ��fn
     * @example
     * function test(){
     *     console.log("�ӳ������");
     * }
     * //�ǻ����ӳ�ִ��
     * var testDefer = UE.utils.defer(test,1000);
     * testDefer();   =>  "�ӳ������";
     * testDefer();   =>  "�ӳ������";
     * //�����ӳ�ִ��
     * var testDefer1 = UE.utils.defer(test,1000,true);
     * testDefer1();   =>  //���β�ִ��
     * testDefer1();   =>  "�ӳ������";
     */
    defer:function (fn, delay, exclusion) {
        var timerID;
        return function () {
            if (exclusion) {
                clearTimeout(timerID);
            }
            timerID = setTimeout(fn, delay);
        };
    },

    /**
     * ����Ԫ��item������array�е�����, ���Ҳ�������-1
     * @name indexOf
     * @grammar UE.utils.indexOf(array,item)  => index|-1  //Ĭ�ϴ����鿪ͷ����ʼ����
     * @grammar UE.utils.indexOf(array,item,start)  => index|-1  //startָ����ʼ���ҵ�λ��
     */
    indexOf:function (array, item, start) {
        var index = -1;
        start = this.isNumber(start) ? start : 0;
        this.each(array,function(v,i){
            if(i >= start && v === item){
                index = i;
                return false;
            }
        });
        return index;
    },

    /**
     * �Ƴ�����array�е�Ԫ��item
     * @name removeItem
     * @grammar UE.utils.removeItem(array,item)
     */
    removeItem:function (array, item) {
        for (var i = 0, l = array.length; i < l; i++) {
            if (array[i] === item) {
                array.splice(i, 1);
                i--;
            }
        }
    },

    /**
     * ɾ���ַ���str����β�ո�
     * @name trim
     * @grammar UE.utils.trim(str) => String
     */
    trim:function (str) {
        return str.replace(/(^[ \t\n\r]+)|([ \t\n\r]+$)/g, '');
    },

    /**
     * ���ַ���list(��','�ָ�)��������listת�ɹ�ϣ����
     * @name listToMap
     * @grammar UE.utils.listToMap(list)  => Object  //Object����{test:1,br:1,textarea:1}
     */
    listToMap:function (list) {
        if (!list)return {};
        list = utils.isArray(list) ? list : list.split(',');
        for (var i = 0, ci, obj = {}; ci = list[i++];) {
            obj[ci.toUpperCase()] = obj[ci] = 1;
        }
        return obj;
    },

    /**
     * ��str�е�html����ת��,Ĭ�Ͻ�ת��''&<">''�ĸ��ַ������Զ���reg��ȷ����Ҫת����ַ�
     * @name unhtml
     * @grammar UE.utils.unhtml(str);  => String
     * @grammar UE.utils.unhtml(str,reg)  => String
     * @example
     * var html = '<body>You say:"��ã�Baidu & UEditor!"</body>';
     * UE.utils.unhtml(html);   ==>  &lt;body&gt;You say:&quot;��ã�Baidu &amp; UEditor!&quot;&lt;/body&gt;
     * UE.utils.unhtml(html,/[<>]/g)  ==>  &lt;body&gt;You say:"��ã�Baidu & UEditor!"&lt;/body&gt;
     */
    unhtml:function (str, reg) {
        return str ? str.replace(reg || /[&<">]/g, function (m) {
            return {
                '<':'&lt;',
                '&':'&amp;',
                '"':'&quot;',
                '>':'&gt;'
            }[m]
        }) : '';
    },
    /**
     * ��str�е�ת���ַ���ԭ��html�ַ�
     * @name html
     * @grammar UE.utils.html(str)  => String   //��ϸ�μ�<code><a href = '#unhtml'>unhtml</a></code>
     */
    html:function (str) {
        return str ? str.replace(/&((g|l|quo)t|amp);/g, function (m) {
            return {
                '&lt;':'<',
                '&amp;':'&',
                '&quot;':'"',
                '&gt;':'>'
            }[m]
        }) : '';
    },
    /**
     * ��css��ʽת��Ϊ�շ����ʽ����font-size => fontSize
     * @name cssStyleToDomStyle
     * @grammar UE.utils.cssStyleToDomStyle(cssName)  => String
     */
    cssStyleToDomStyle:function () {
        var test = document.createElement('div').style,
            cache = {
                'float':test.cssFloat != undefined ? 'cssFloat' : test.styleFloat != undefined ? 'styleFloat' : 'float'
            };

        return function (cssName) {
            return cache[cssName] || (cache[cssName] = cssName.toLowerCase().replace(/-./g, function (match) {
                return match.charAt(1).toUpperCase();
            }));
        };
    }(),
    /**
     * ��̬�����ļ���doc�У�������obj���������ԣ����سɹ���ִ�лص�����fn
     * @name loadFile
     * @grammar UE.utils.loadFile(doc,obj)
     * @grammar UE.utils.loadFile(doc,obj,fn)
     * @example
     * //ָ�����ص���ǰdocument��һ��script�ļ������سɹ���ִ��function
     * utils.loadFile( document, {
     *     src:"test.js",
     *     tag:"script",
     *     type:"text/javascript",
     *     defer:"defer"
     * }, function () {
     *     console.log('���سɹ���')
     * });
     */
    loadFile:function () {
        var tmpList = [];
        function getItem(doc,obj){
            try{
                for(var i= 0,ci;ci=tmpList[i++];){
                    if(ci.doc === doc && ci.url == (obj.src || obj.href)){
                        return ci;
                    }
                }
            }catch(e){
                return null;
            }

        }
        return function (doc, obj, fn) {
            var item = getItem(doc,obj);
            if (item) {
                if(item.ready){
                    fn && fn();
                }else{
                    item.funs.push(fn)
                }
                return;
            }
            tmpList.push({
                doc:doc,
                url:obj.src||obj.href,
                funs:[fn]
            });
            if (!doc.body) {
                var html = [];
                for(var p in obj){
                    if(p == 'tag')continue;
                    html.push(p + '="' + obj[p] + '"')
                }
                doc.write('<' + obj.tag + ' ' + html.join(' ') + ' ></'+obj.tag+'>');
                return;
            }
            if (obj.id && doc.getElementById(obj.id)) {
                return;
            }
            var element = doc.createElement(obj.tag);
            delete obj.tag;
            for (var p in obj) {
                element.setAttribute(p, obj[p]);
            }
            element.onload = element.onreadystatechange = function () {
                if (!this.readyState || /loaded|complete/.test(this.readyState)) {
                    item = getItem(doc,obj);
                    if (item.funs.length > 0) {
                        item.ready = 1;
                        for (var fi; fi = item.funs.pop();) {
                            fi();
                        }
                    }
                    element.onload = element.onreadystatechange = null;
                }
            };
            element.onerror = function(){
                throw Error('The load '+(obj.href||obj.src)+' fails,check the url settings of file editor_config.js ')
            };
            doc.getElementsByTagName("head")[0].appendChild(element);
        }
    }(),
    /**
     * �ж�obj�����Ƿ�Ϊ��
     * @name isEmptyObject
     * @grammar UE.utils.isEmptyObject(obj)  => true|false
     * @example
     * UE.utils.isEmptyObject({}) ==>true
     * UE.utils.isEmptyObject([]) ==>true
     * UE.utils.isEmptyObject("") ==>true
     */
    isEmptyObject:function (obj) {
        if (obj == null) return true;
        if (this.isArray(obj) || this.isString(obj)) return obj.length === 0;
        for (var key in obj) if (obj.hasOwnProperty(key)) return false;
        return true;
    },

    /**
     * ͳһ����ɫֵʹ��16������ʽ��ʾ
     * @name fixColor
     * @grammar UE.utils.fixColor(name,value) => value
     * @example
     * rgb(255,255,255)  => "#ffffff"
     */
    fixColor:function (name, value) {
        if (/color/i.test(name) && /rgba?/.test(value)) {
            var array = value.split(",");
            if (array.length > 3)
                return "";
            value = "#";
            for (var i = 0, color; color = array[i++];) {
                color = parseInt(color.replace(/[^\d]/gi, ''), 10).toString(16);
                value += color.length == 1 ? "0" + color : color;
            }
            value = value.toUpperCase();
        }
        return  value;
    },
    /**
     * ֻ���border,padding,margin���˴�����Ϊ��������
     * @public
     * @function
     * @param {String}    val style�ַ���
     */
    optCss:function (val) {
        var padding, margin, border;
        val = val.replace(/(padding|margin|border)\-([^:]+):([^;]+);?/gi, function (str, key, name, val) {
            if (val.split(' ').length == 1) {
                switch (key) {
                    case 'padding':
                        !padding && (padding = {});
                        padding[name] = val;
                        return '';
                    case 'margin':
                        !margin && (margin = {});
                        margin[name] = val;
                        return '';
                    case 'border':
                        return val == 'initial' ? '' : str;
                }
            }
            return str;
        });

        function opt(obj, name) {
            if (!obj) {
                return '';
            }
            var t = obj.top , b = obj.bottom, l = obj.left, r = obj.right, val = '';
            if (!t || !l || !b || !r) {
                for (var p in obj) {
                    val += ';' + name + '-' + p + ':' + obj[p] + ';';
                }
            } else {
                val += ';' + name + ':' +
                    (t == b && b == l && l == r ? t :
                        t == b && l == r ? (t + ' ' + l) :
                            l == r ? (t + ' ' + l + ' ' + b) : (t + ' ' + r + ' ' + b + ' ' + l)) + ';'
            }
            return val;
        }

        val += opt(padding, 'padding') + opt(margin, 'margin');
        return val.replace(/^[ \n\r\t;]*|[ \n\r\t]*$/, '').replace(/;([ \n\r\t]+)|\1;/g, ';')
            .replace(/(&((l|g)t|quot|#39))?;{2,}/g, function (a, b) {
                return b ? b + ";;" : ';'
            });
    },
    /**
     * ��ȿ�¡���󣬴�source��target
     * @name clone
     * @grammar UE.utils.clone(source) => anthorObj �µĶ�����������source�ĸ���
     * @grammar UE.utils.clone(source,target) => target������source���������ݣ������Ḳ��
     */
    clone:function (source, target) {
        var tmp;
        target = target || {};
        for (var i in source) {
            if (source.hasOwnProperty(i)) {
                tmp = source[i];
                if (typeof tmp == 'object') {
                    target[i] = utils.isArray(tmp) ? [] : {};
                    utils.clone(source[i], target[i])
                } else {
                    target[i] = tmp;
                }
            }
        }
        return target;
    },
    /**
     * ת��cm/pt��px
     * @name transUnitToPx
     * @grammar UE.utils.transUnitToPx('20pt') => '27px'
     * @grammar UE.utils.transUnitToPx('0pt') => '0'
     */
    transUnitToPx : function(val){
        if(!/(pt|cm)/.test(val)){
            return val
        }
        var unit;
        val.replace(/([\d.]+)(\w+)/,function(str,v,u){
            val = v;
            unit = u;
        });
        switch(unit){
            case 'cm':
                val = parseFloat(val) * 25;
                break;
            case 'pt':
                val = Math.round(parseFloat(val) * 96 / 72);
        }
        return val + (val?'px':'');
    },
    /**
     * DomReady�������ص���������dom��ready��ɺ�ִ��
     * @name domReady
     * @grammar UE.utils.domReady(fn)  => fn  //����һ���ӳ�ִ�еķ���
     */
    domReady:function () {

        var fnArr = [];

        function doReady(doc) {
            //ȷ��onreadyִֻ��һ��
            doc.isReady = true;
            for (var ci; ci = fnArr.pop();ci()){}
        }

        return function (onready,win) {
            win = win || window;
            var doc = win.document;
            onready && fnArr.push(onready);
            if (doc.readyState === "complete") {
                doReady(doc);
            }else{
                doc.isReady && doReady(doc);
                if (browser.ie) {
                    (function () {
                        if (doc.isReady) return;
                        try {
                            doc.documentElement.doScroll("left");
                        } catch (error) {
                            setTimeout(arguments.callee, 0);
                            return;
                        }
                        doReady(doc);
                    })();
                    win.attachEvent('onload', function(){
                        doReady(doc)
                    });
                } else {
                    doc.addEventListener("DOMContentLoaded", function () {
                        doc.removeEventListener("DOMContentLoaded", arguments.callee, false);
                        doReady(doc);
                    }, false);
                    win.addEventListener('load', function(){doReady(doc)}, false);
                }
            }

        }
    }(),
    /**
     * ��̬���css��ʽ
     * @name cssRule
     * @grammar UE.utils.cssRule('��ӵ���ʽ�Ľڵ�����',['��ʽ'��'�ŵ��ĸ�document��'])
     * @grammar UE.utils.cssRule('body','body{background:#ccc}') => null  //��body��ӱ�����ɫ
     * @grammar UE.utils.cssRule('body') =>��ʽ���ַ���  //ȡ��keyֵΪbody����ʽ������,���û���ҵ�keyֵ�ȹص���ʽ�����ؿգ�����ղ��Ǹ�������ɫ�������� body{background:#ccc}
     * @grammar UE.utils.cssRule('body','') =>null //��ո�����keyֵ�ı�����ɫ
     */
    cssRule : browser.ie ? function(key,style,doc){
            var indexList,index;
            doc = doc || document;
            if(doc.indexList){
                indexList = doc.indexList;
            }else{
                indexList = doc.indexList =  {};
            }
            var sheetStyle;
            if(!indexList[key]){
                if(style === undefined){
                    return ''
                }
                sheetStyle = doc.createStyleSheet('',index = doc.styleSheets.length);
                indexList[key] = index;
            }else{
                sheetStyle = doc.styleSheets[indexList[key]];
            }
            if(style === undefined){
                return sheetStyle.cssText
            }
            sheetStyle.cssText = style || ''
        }:function(key,style,doc){
            doc = doc || document;
            var head = doc.getElementsByTagName('head')[0],node;
            if(!(node = doc.getElementById(key))){
                if(style === undefined){
                    return ''
                }
                node = doc.createElement('style');
                node.id = key;
                head.appendChild(node)
            }
            if(style === undefined){
                return node.innerHTML
            }
            if(style !== ''){
                node.innerHTML = style;
            }else{
                head.removeChild(node)
            }
        }

};
/**
 * �ж�str�Ƿ�Ϊ�ַ���
 * @name isString
 * @grammar UE.utils.isString(str) => true|false
 */
/**
 * �ж�array�Ƿ�Ϊ����
 * @name isArray
 * @grammar UE.utils.isArray(obj) => true|false
 */
/**
 * �ж�obj�����Ƿ�Ϊ����
 * @name isFunction
 * @grammar UE.utils.isFunction(obj)  => true|false
 */
/**
 * �ж�obj�����Ƿ�Ϊ����
 * @name isNumber
 * @grammar UE.utils.isNumber(obj)  => true|false
 */
utils.each(['String','Function','Array','Number','RegExp'],function(v){
    UE.utils['is' + v] = function(obj){
        return Object.prototype.toString.apply(obj) == '[object ' + v + ']';
    }
});