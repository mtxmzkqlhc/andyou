/**
 * @file
 * @name �༭���¼��ӿ�
 * @short Custom events
 * @des ���ļ��Ǳ༭�������ļ��������������ɶ�Ӧ���¼��ӿ��ĵ�
 * UEditor�༭���е������¼������ʹ�����ͳһ����
 * ''editor''�Ǳ༭��ʵ��
 * editor.addListener("eventName",handler) �� editor.fireEvent("eventName")��ʽ���ã�֧�������ԭ���¼�����keydown,keyup,mousedown,mouseup��
 */
/**
 * �༭����������¼������ģ����ڱ༭��׼����������������ʱ�������󲿷ֳ�������ʹ��editor.ready(fn)ȡ����
 * @name ready
 * @grammar editor.addListener("ready",fn)
 * @example
 * editor.addListener("ready",function(){
 *     //thisΪeditorʵ��
 *     this.setContent("��ӭʹ��UEditor��");
 * })
 * //ͬ���½ӿڷ�ʽ����
 * editor.ready(function(){
 *     this.setContent("��ӭʹ��UEditor��");
 * })
 */
/**
 * ѡ���仯�¼������ģ�����ѡ�����ֱ仯ʱ������
 * ��UEditor�У��κ��漰�����ı�Ĳ������ᴥ��ѡ���仯�¼������¼���Ҫ����ʵ�ֹ�����״̬���䡣
 * @name selectionChange
 * @grammar editor.addListener("selectionChange",fn)
 * @grammar editor.fireEvent("selectionChange")
 * @example
 * editor.addListener("selectionChange",function(){
 *     //thisΪeditorʵ��
 * })
 */

/**
 * ���ݱ仯�¼������ģ������༭�����е��ı����ݳ��ֱ仯ʱ����
 * @name contentChange
 * @grammar editor.addListener("contentChange",fn)
 * @grammar editor.fireEvent("contentChange")
 */

/**
 * ճ���¼������ģ�����ʹ��ctr+v��ݼ�ճ��(����Chrome��FF��������Ҽ�ճ��)ʱ�ᴥ�����¼�
 * @name (before|after)Paste
 * @grammar editor.addListener("beforePaste",fn)
 * @desc
 * * beforePaste �ڽ�ճ��������д���༭��֮ǰ����������¼�����ʱ��ճ�������ݻ�δ�ڱ༭������ʾ
 * * afterPaste ճ���������Ѿ�д���༭����ߺ󴥷�
 * @example
 * editor.addListener("beforePaste",function(type,data){
 *     //beforePaste�¼�����������afterPaste�¼���������Ҫ��һ�������Ǵ���һ��data������
 *     //��data������һ�����󣬰�������html��
 *     //���û��ڴ˴����ĸ�html��ֵʱ������Ӱ��ճ�����༭���е�����,��Ҫ����ճ��ʱ��Ҫ���⴦���һЩ������
 *     console.log(this.getContent) //this���ǵ�ǰ�༭����ʵ��
 *     //before�¼��������������������д���༭��֮ǰ��ճ�����������ݽ��������޸�
 *     data.html = "�Ұ�ճ�����ݸĳ�����仰";
 * })
 */

/**
 * ���������¼������ģ���������setContent����ʱ����
 * @name (before|after)SetContent
 * @grammar editor.addListener("beforeSetContent",fn)
 * @desc
 * * beforeSetContent ������д���༭��֮ǰ����
 * * afterSetContent �����Ѿ�д���༭����ߺ󴥷�
 * @example
 * editor.addListener("beforeSetContent",function(type,data){
 *     //beforeSetContent�¼�����������afterSetContent�¼���������Ҫ��һ�������Ǵ���һ��data������
 *     //��data������һ�����󣬰�������html��
 *     //���û��ڴ˴����ĸ�html��ֵʱ������Ӱ�����õ��༭���е�����,��Ҫ������������ʱ��Ҫ���⴦���һЩ������
 *     data.html = "�Ұ��������ݸĳ�����仰";
 * })
 */

/**
 * getAllHtml�¼���������getAllHtml����ʱ����
 * @name getAllHtml
 * @grammar editor.addListener("getAllHtml",fn)
 * @desc
 * * ��Ҫ�����������ɵ�����html�����е�head���ݽ��ж��ƣ���������������Լ�����ʽ��script��ǩ�ȣ�������չʾʱʹ��
 * @example
 * editor.addListener("getAllHtml",function(type,data){
 *     //data��document��head����html�ķ�װ����ͨ��data.html����ȡ��Ӧ�ַ�����
 *     //��Ҫ�޸ĵĻ������¸�ֵdata.html = '<style type="text/css"> body{margin:0;}</style>';
 * })
 */

/**
 * �����ύ�¼�(���)���������ύ������ز�������autosubmit����ʱ�������������ύ֮ǰ����֤
 * @name beforeSubmit
 * @grammar editor.addListener("beforeSubmit",fn)   //��fn����false������ֹ�����ύ
 * @example
 * editor.addListener("beforeSubmit",function(){
 *     if(!editor.hasContents()){
 *         return false;
 *     }
 * })
 */

/**
 * ���ץȡԶ�̵�ͼƬʧ���ˣ��ʹ���
 * @name catchRemoteError
 * @grammar editor.addListener("catchRemoteError",fn)
 * @example
 * editor.addListener("catchRemoteError",function(){
 *     console.log("ץȡʧ���ˣ�")
 * })
 */

/**
 * ��ץȡԶ�̵�ͼƬ�ɹ����᷵������ͼƬ������ʱ����
 * @name catchRemoterSuccess
 * @grammar editor.addListener("catchRemoterSuccess",fn)
 * @example
 * editor.addListener("catchRemoterSuccess",function(){
 *     console.log("ץȡ�ɹ�")
 * })
 */

/**
 * �༭ģʽ�л��¼������������Դ��ģʽ�͸��ı�ģʽ�����л�ʱ�����¼�
 * @name sourceModeChanged
 * @grammar  editor.addListener("sourceModeChanged",fn)
 * @example
 * editor.addListener("sourceModeChanged",function(type,mode){
 *     //mode�����˵�ǰ�ı༭ģʽ��true�����л�����Դ��ģʽ��false�����л����˸��ı�ģʽ
 * })
 */

/**
 * ȫ���л��¼������������ִ��ȫ���л���ʱ�򴥷��¼�
 * @name fullScreenChanged
 * @grammar  editor.addListener("fullScreenChanged",fn)
 * @example
 * editor.addListener("fullScreenChanged",function(type,mode){
 *     //mode����ǰ�Ƿ�ȫ����true�����л�����ȫ��ģʽ��false�����л�������ͨģʽ
 * })
 */

/**
 * �������������¼������������������ַ�����������������ʱ����
 * @name wordCountOverflow
 * @grammar editor.addListener("wordCountOverflow",fn)
 * @example
 * editor.addListener("wordCountOverflow",function(type,length){
 *     console.log(length)
 * })
 */

