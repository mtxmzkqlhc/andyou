///import core
///commands ����������
///commandsName  Undo,Redo
///commandsTitle  ����,����
/**
 * @description ����
 * @author zhanyi
 */

UE.plugins['undo'] = function () {
    var me = this,
        maxUndoCount = me.options.maxUndoCount || 20,
        maxInputCount = me.options.maxInputCount || 20,
        fillchar = new RegExp(domUtils.fillChar + '|<\/hr>', 'gi');// ie����������</hr>


    function compareAddr(indexA, indexB) {
        if (indexA.length != indexB.length)
            return 0;
        for (var i = 0, l = indexA.length; i < l; i++) {
            if (indexA[i] != indexB[i])
                return 0
        }
        return 1;
    }

    function compareRangeAddress(rngAddrA, rngAddrB) {
        if (rngAddrA.collapsed != rngAddrB.collapsed) {
            return 0;
        }
        if (!compareAddr(rngAddrA.startAddress, rngAddrB.startAddress) || !compareAddr(rngAddrA.endAddress, rngAddrB.endAddress)) {
            return 0;
        }
        return 1;
    }

    function adjustContent(cont) {
        var specialAttr = /\b(?:href|src|name)="[^"]*?"/gi;
        return cont.replace(specialAttr, '')
            .replace(/([\w\-]*?)\s*=\s*(("([^"]*)")|('([^']*)')|([^\s>]+))/gi, function (a, b, c) {
                return b.toLowerCase() + '=' + c.replace(/['"]/g, '').toLowerCase()
            })
            .replace(/(<[\w\-]+)|([\w\-]+>)/gi, function (a, b, c) {
                return (b || c).toLowerCase()
            });
    }

    function UndoManager() {
        this.list = [];
        this.index = 0;
        this.hasUndo = false;
        this.hasRedo = false;
        this.undo = function () {
            if (this.hasUndo) {
                var currentScene = this.getScene(),
                    lastScene = this.list[this.index],
                    lastContent = adjustContent(lastScene.content),
                    currentContent = adjustContent(currentScene.content);

                if (lastContent != currentContent) {
                    this.save();
                }
                if (!this.list[this.index - 1] && this.list.length == 1) {
                    this.reset();
                    return;
                }
                while (this.list[this.index].content == this.list[this.index - 1].content) {
                    this.index--;
                    if (this.index == 0) {
                        return this.restore(0);
                    }
                }
                this.restore(--this.index);
            }
        };
        this.redo = function () {
            if (this.hasRedo) {
                while (this.list[this.index].content == this.list[this.index + 1].content) {
                    this.index++;
                    if (this.index == this.list.length - 1) {
                        return this.restore(this.index);
                    }
                }
                this.restore(++this.index);
            }
        };

        this.restore = function () {
            var scene = this.list[this.index];
            //trace:873
            //ȥ��չλ��
            me.document.body.innerHTML = scene.content.replace(fillchar, '');
            //����undo��ո�չλ������
            if (browser.ie) {
                utils.each(domUtils.getElementsByTagName(me.document,'td th caption p'),function(node){
                    if(domUtils.isEmptyNode(node)){
                        domUtils.fillNode(me.document, node);
                    }
                })
            }
            new dom.Range(me.document).moveToAddress(scene.address).select();
            this.update();
            this.clearKey();
            //���ܰ��Լ�reset��
            me.fireEvent('reset', true);
        };

        this.getScene = function () {
            var rng = me.selection.getRange(),
                restoreAddress = rng.createAddress(),
                rngAddress = rng.createAddress(false,true);

            me.fireEvent('beforegetscene');
            var cont = me.body.innerHTML.replace(fillchar, '');
            browser.ie && (cont = cont.replace(/>&nbsp;</g, '><').replace(/\s*</g, '<').replace(/>\s*/g, '>'));
            me.fireEvent('aftergetscene');
            try{
                rng.moveToAddress(restoreAddress).select(true);
            }catch(e){}
            return {
                address:rngAddress,
                content:cont
            }
        };
        this.save = function (notCompareRange) {
            var currentScene = this.getScene(),
                lastScene = this.list[this.index];
            //������ͬλ����ͬ����
            if (lastScene && lastScene.content == currentScene.content &&
                ( notCompareRange ? 1 : compareRangeAddress(lastScene.address, currentScene.address) )
                ) {
                return;
            }
            this.list = this.list.slice(0, this.index + 1);
            this.list.push(currentScene);
            //���������������ˣ��Ͱ���ǰ���޳�
            if (this.list.length > maxUndoCount) {
                this.list.shift();
            }
            this.index = this.list.length - 1;
            this.clearKey();
            //����undo/redo״̬
            this.update();
        };
        this.update = function () {
            this.hasRedo = !!this.list[this.index + 1];
            this.hasUndo = !!this.list[this.index - 1];
        };
        this.reset = function () {
            this.list = [];
            this.index = 0;
            this.hasUndo = false;
            this.hasRedo = false;
            this.clearKey();
        };
        this.clearKey = function () {
            keycont = 0;
            lastKeyCode = null;
        };
    }

    me.undoManger = new UndoManager();
    function saveScene() {
        this.undoManger.save();
    }

    me.addListener('saveScene', function () {
        me.undoManger.save();
    });

    me.addListener('beforeexeccommand', saveScene);
    me.addListener('afterexeccommand', saveScene);

    me.addListener('reset', function (type, exclude) {
        if (!exclude) {
            me.undoManger.reset();
        }
    });
    me.commands['redo'] = me.commands['undo'] = {
        execCommand:function (cmdName) {
            me.undoManger[cmdName]();
        },
        queryCommandState:function (cmdName) {
            return me.undoManger['has' + (cmdName.toLowerCase() == 'undo' ? 'Undo' : 'Redo')] ? 0 : -1;
        },
        notNeedUndo:1
    };

    var keys = {
            //  /*Backspace*/ 8:1, /*Delete*/ 46:1,
            /*Shift*/ 16:1, /*Ctrl*/ 17:1, /*Alt*/ 18:1,
            37:1, 38:1, 39:1, 40:1,
            13:1 /*enter*/
        },
        keycont = 0,
        lastKeyCode;
    //���뷨״̬�²������ַ���
    var inputType = false;
    me.addListener('ready', function () {
        domUtils.on(me.body, 'compositionstart', function () {
            inputType = true;
        });
        domUtils.on(me.body, 'compositionend', function () {
            inputType = false;
        })
    });
    //��ݼ�
    me.addshortcutkey({
        "Undo":"ctrl+90", //undo
        "Redo":"ctrl+89" //redo

    });
    me.addListener('keydown', function (type, evt) {
        var keyCode = evt.keyCode || evt.which;
        if (!keys[keyCode] && !evt.ctrlKey && !evt.metaKey && !evt.shiftKey && !evt.altKey) {
            if (inputType)
                return;
            if (me.undoManger.list.length == 0 || ((keyCode == 8 || keyCode == 46) && lastKeyCode != keyCode)) {

                me.fireEvent('contentchange');

                me.undoManger.save(true);
                lastKeyCode = keyCode;
                return;
            }
            //trace:856
            //������һ������󣬻��ˣ�������Ҫ��keycont>maxInputCount�����ڻ��˵�����
            if (me.undoManger.list.length == 2 && me.undoManger.index == 0 && keycont == 0) {
                me.undoManger.list.splice(1, 1);
                me.undoManger.update();
            }
            lastKeyCode = keyCode;
            keycont++;
            if (keycont >= maxInputCount || me.undoManger.mousedown) {
                if (me.selection.getRange().collapsed)
                    me.fireEvent('contentchange');
                me.undoManger.save();
                me.undoManger.mousedown = false;
            }
        }
    });
    me.addListener('mousedown',function(){
        me.undoManger.mousedown = true;
    })
};
