/**
 * @file
 * @name UE.EventBase
 * @short EventBase
 * @import editor.js,core/utils.js
 * @desc UE���õ��¼����࣬�̳д���Ķ�Ӧ�ཫ��ȡaddListener,removeListener,fireEvent������
 * ��UE�У�Editor�Լ�����uiʵ�����̳��˸��࣬�ʿ����ڶ�Ӧ��ui�����Լ�editor������ʹ������������
 */
var EventBase = UE.EventBase = function () {};

EventBase.prototype = {
    /**
     * ע���¼�������
     * @name addListener
     * @grammar editor.addListener(types,fn)  //typesΪ�¼����ƣ�������ÿո�ָ�
     * @example
     * editor.addListener('selectionchange',function(){
     *      console.log("ѡ���Ѿ��仯��");
     * })
     * editor.addListener('beforegetcontent aftergetcontent',function(type){
     *         if(type == 'beforegetcontent'){
     *             //do something
     *         }else{
     *             //do something
     *         }
     *         console.log(this.getContent) // this��ע����¼��ı༭��ʵ��
     * })
     */
    addListener:function (types, listener) {
        types = utils.trim(types).split(' ');
        for (var i = 0, ti; ti = types[i++];) {
            getListener(this, ti, true).push(listener);
        }
    },
    /**
     * �Ƴ��¼�������
     * @name removeListener
     * @grammar editor.removeListener(types,fn)  //typesΪ�¼����ƣ�������ÿո�ָ�
     * @example
     * //changeCallbackΪ������
     * editor.removeListener("selectionchange",changeCallback);
     */
    removeListener:function (types, listener) {
        types = utils.trim(types).split(' ');
        for (var i = 0, ti; ti = types[i++];) {
            utils.removeItem(getListener(this, ti) || [], listener);
        }
    },
    /**
     * �����¼�
     * @name fireEvent
     * @grammar editor.fireEvent(types)  //typesΪ�¼����ƣ�������ÿո�ָ�
     * @example
     * editor.fireEvent("selectionchange");
     */
    fireEvent:function (types) {
        types = utils.trim(types).split(' ');
        for (var i = 0, ti; ti = types[i++];) {
            var listeners = getListener(this, ti),
                r, t, k;
            if (listeners) {
                k = listeners.length;
                while (k--) {
                    if(!listeners[k])continue;
                    t = listeners[k].apply(this, arguments);
                    if(t === true){
                        return t;
                    }
                    if (t !== undefined) {
                        r = t;
                    }
                }
            }
            if (t = this['on' + ti.toLowerCase()]) {
                r = t.apply(this, arguments);
            }
        }
        return r;
    }
};
/**
 * ��ö�����ӵ�м������͵����м�����
 * @public
 * @function
 * @param {Object} obj  ��ѯ�������Ķ���
 * @param {String} type �¼�����
 * @param {Boolean} force  Ϊtrue�ҵ�ǰ����type���͵�������������ʱ������һ���ռ���������
 * @returns {Array} ����������
 */
function getListener(obj, type, force) {
    var allListeners;
    type = type.toLowerCase();
    return ( ( allListeners = ( obj.__allListeners || force && ( obj.__allListeners = {} ) ) )
        && ( allListeners[type] || force && ( allListeners[type] = [] ) ) );
}

