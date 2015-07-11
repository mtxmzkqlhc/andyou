/**
 * Created with JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-10-12
 * Time: ����10:05
 * To change this template use File | Settings | File Templates.
 */
UE.plugins['table'] = function () {
    var me = this,
        debug = true;

    function showError(e) {
        if (debug) throw e;
    }

    //�����϶�����ѡ��ط���
    var startTd = null, //��갴��ʱ��ê��td
        currentTd = null, //��ǰ��꾭��ʱ��td
        onDrag = "", //ָʾ��ǰ�϶�״̬����ֵ��Ϊ"","h","v" ,�ֱ��ʾδ�϶�״̬�������϶�״̬�������϶�״̬����������ƶ������е��ж�
        onBorder = false, //�����갴��ʱ�Ƿ��ڵ�Ԫ���Եλ��
        dragButton = null,
        dragOver = false,
        dragLine = null, //ģ����϶���
        dragTd = null;    //�����϶���Ŀ��td

    var mousedown = false,
    //todo �жϻ���ģʽ
        needIEHack = true;

    me.setOpt({
        'maxColNum':20,
        'maxRowNum':100,
        'defaultCols':5,
        'defaultRows':5,
        'tdvalign':'top',
        'cursorpath':me.options.UEDITOR_HOME_URL + "themes/default/images/cursor_",
        'tableDragable':false
    });
    me.getUETable = getUETable;
    var commands = {
        'deletetable':1,
        'inserttable':1,
        'cellvalign':1,
        'insertcaption':1,
        'deletecaption':1,
        'inserttitle':1,
        'deletetitle':1,
        "mergeright":1,
        "mergedown":1,
        "mergecells":1,
        "insertrow":1,
        "insertrownext":1,
        "deleterow":1,
        "insertcol":1,
        "insertcolnext":1,
        "deletecol":1,
        "splittocells":1,
        "splittorows":1,
        "splittocols":1,
        "adaptbytext":1,
        "adaptbywindow":1,
        "adaptbycustomer":1,
        "insertparagraph":1,
        "averagedistributecol":1,
        "averagedistributerow":1
    };
    me.ready(function () {
        utils.cssRule('table',
            //ѡ�е�td�ϵ���ʽ
            '.selectTdClass{background-color:#edf5fa !important}' +
                'table.noBorderTable td,table.noBorderTable th,table.noBorderTable caption{border:1px dashed #ddd !important}', me.document);

        var tableCopyList, isFullCol, isFullRow;
        //ע��del/backspace�¼�
        me.addListener('keydown', function (cmd, evt) {
            var me = this;
            var keyCode = evt.keyCode || evt.which;

            if (keyCode == 8) {

                var ut = getUETableBySelected(me);
                if (ut && ut.selectedTds.length) {
                    me.fireEvent('saveScene');
                    if (ut.isFullCol()) {
                        me.execCommand('deletecol')
                    } else if (ut.isFullRow()) {
                        me.execCommand('deleterow')
                    } else {
                        me.fireEvent('delcells');
                    }
                    domUtils.preventDefault(evt);
                }

                var caption = domUtils.findParentByTagName(me.selection.getStart(), 'caption', true),
                    range = me.selection.getRange();
                if (range.collapsed && caption && isEmptyBlock(caption)) {
                    me.fireEvent('saveScene');
                    var table = caption.parentNode;
                    domUtils.remove(caption);
                    if (table) {
                        range.setStart(table.rows[0].cells[0], 0).setCursor(false, true);
                    }
                }
                me.fireEvent('saveScene');
            }

            if (keyCode == 46) {

                ut = getUETableBySelected(me);
                if (ut) {
                    me.fireEvent('saveScene');
                    for (var i = 0, ci; ci = ut.selectedTds[i++];) {
                        domUtils.fillNode(me.document, ci)
                    }
                    me.fireEvent('saveScene');
                    domUtils.preventDefault(evt);

                }

            }
            if (keyCode == 13) {

                var rng = me.selection.getRange(),
                    caption = domUtils.findParentByTagName(rng.startContainer, 'caption', true);
                if (caption) {
                    var table = domUtils.findParentByTagName(caption, 'table');
                    if (!rng.collapsed) {

                        rng.deleteContents();
                        me.fireEvent('saveScene');
                    } else {
                        if (caption) {
                            rng.setStart(table.rows[0].cells[0], 0).setCursor(false, true);
                        }
                    }
                    domUtils.preventDefault(evt);
                    return;
                }
                /*�ڱ���ڻس�BUG
                if (rng.collapsed) {
                    var table = domUtils.findParentByTagName(rng.startContainer, 'table');
                    if (table) {
                        var cell = table.rows[0].cells[0],
                            start = domUtils.findParentByTagName(me.selection.getStart(), ['td', 'th'], true),
                            preNode = table.previousSibling;
                        if (cell === start && (!preNode || preNode.nodeType == 1 && preNode.tagName == 'TABLE' ) && domUtils.isStartInblock(rng)) {
                            me.execCommand('insertparagraphbeforetable');
                            domUtils.preventDefault(evt);
                        }
                    }
                }*/
            }

            if ((evt.ctrlKey || evt.metaKey) && evt.keyCode == '67') {
                tableCopyList = null;
                table = getUETableBySelected(me);
                if (table) {
                    var tds = table.selectedTds;
                    isFullCol = table.isFullCol();
                    isFullRow = table.isFullRow();
                    tableCopyList = [
                        [cloneCell(tds[0])]
                    ];
                    for (var i = 1, ci; ci = tds[i]; i++) {
                        if (ci.parentNode !== tds[i - 1].parentNode) {
                            tableCopyList.push([cloneCell(ci)])
                        } else {
                            tableCopyList[tableCopyList.length - 1].push(cloneCell(ci))
                        }

                    }
                }
            }
        });

        me.addListener('beforepaste', function (cmd, html) {
            var me = this;
            var rng = me.selection.getRange();
            if (domUtils.findParentByTagName(rng.startContainer, 'caption', true)) {
                var div = me.document.createElement("div");
                div.innerHTML = html.html;
                html.html = div[browser.ie ? 'innerText' : 'textContent'];
                return;
            }
            var table = getUETableBySelected(me);
            if (tableCopyList) {
                me.fireEvent('saveScene');
                var rng = me.selection.getRange();
                var td = domUtils.findParentByTagName(rng.startContainer, ['td', 'th'], true), tmpNode, preNode;
                if (td) {
                    var ut = getUETable(td);
                    if (isFullRow) {
                        var rowIndex = ut.getCellInfo(td).rowIndex;
                        if (td.tagName == 'TH') {
                            rowIndex++;
                        }
                        for (var i = 0, ci; ci = tableCopyList[i++];) {
                            var tr = ut.insertRow(rowIndex++, "td");
                            for (var j = 0, cj; cj = ci[j]; j++) {
                                var cell = tr.cells[j];
                                if (!cell) {
                                    cell = tr.insertCell(j)
                                }
                                cell.innerHTML = cj.innerHTML;
                                cj.getAttribute('width') && cell.setAttribute('width', cj.getAttribute('width'));
                                cj.getAttribute('vAlign') && cell.setAttribute('vAlign', cj.getAttribute('vAlign'));
                                cj.getAttribute('align') && cell.setAttribute('align', cj.getAttribute('align'));
                                cj.style.cssText && (cell.style.cssText = cj.style.cssText)
                            }
                            for (var j = 0, cj; cj = tr.cells[j]; j++) {
                                if (!ci[j])
                                    break;
                                cj.innerHTML = ci[j].innerHTML;
                                ci[j].getAttribute('width') && cj.setAttribute('width', ci[j].getAttribute('width'));
                                ci[j].getAttribute('vAlign') && cj.setAttribute('vAlign', ci[j].getAttribute('vAlign'));
                                ci[j].getAttribute('align') && cj.setAttribute('align', ci[j].getAttribute('align'));
                                ci[j].style.cssText && (cj.style.cssText = ci[j].style.cssText)
                            }
                        }
                    } else {
                        if (isFullCol) {
                            cellInfo = ut.getCellInfo(td);
                            var maxColNum = 0;
                            for (var j = 0, ci = tableCopyList[0], cj; cj = ci[j++];) {
                                maxColNum += cj.colSpan || 1;
                            }
                            me.__hasEnterExecCommand = true;
                            for (i = 0; i < maxColNum; i++) {
                                me.execCommand('insertcol');
                            }
                            me.__hasEnterExecCommand = false;
                            td = ut.table.rows[0].cells[cellInfo.cellIndex];
                            if (td.tagName == 'TH') {
                                td = ut.table.rows[1].cells[cellInfo.cellIndex];
                            }
                        }
                        for (var i = 0, ci; ci = tableCopyList[i++];) {
                            tmpNode = td;
                            for (var j = 0, cj; cj = ci[j++];) {
                                if (td) {
                                    td.innerHTML = cj.innerHTML;
                                    //todo ���ƴ���
                                    cj.getAttribute('width') && td.setAttribute('width', cj.getAttribute('width'));
                                    cj.getAttribute('vAlign') && td.setAttribute('vAlign', cj.getAttribute('vAlign'));
                                    cj.getAttribute('align') && td.setAttribute('align', cj.getAttribute('align'));
                                    cj.style.cssText && (td.style.cssText = cj.style.cssText);
                                    preNode = td;
                                    td = td.nextSibling;
                                } else {
                                    var cloneTd = cj.cloneNode(true);
                                    domUtils.removeAttributes(cloneTd, ['class', 'rowSpan', 'colSpan']);

                                    preNode.parentNode.appendChild(cloneTd)
                                }
                            }
                            td = ut.getNextCell(tmpNode, true, true);
                            if (!tableCopyList[i])
                                break;
                            if (!td) {
                                var cellInfo = ut.getCellInfo(tmpNode);
                                ut.table.insertRow(ut.table.rows.length);
                                ut.update();
                                td = ut.getVSideCell(tmpNode, true);
                            }
                        }
                    }
                    ut.update();
                } else {
                    table = me.document.createElement('table');
                    for (var i = 0, ci; ci = tableCopyList[i++];) {
                        var tr = table.insertRow(table.rows.length);
                        for (var j = 0, cj; cj = ci[j++];) {
                            cloneTd = cloneCell(cj);
                            domUtils.removeAttributes(cloneTd, ['class']);
                            tr.appendChild(cloneTd)
                        }
                        if (j == 2 && cloneTd.rowSpan > 1) {
                            cloneTd.rowSpan = 1;
                        }
                    }

                    var defaultValue = getDefaultValue(me),
                        width = me.body.offsetWidth -
                            (needIEHack ? parseInt(domUtils.getComputedStyle(me.body, 'margin-left'), 10) * 2 : 0) - defaultValue.tableBorder * 2 - (me.options.offsetWidth || 0);
                    me.execCommand('insertHTML', '<table  ' +
                        ( isFullCol && isFullRow ? 'width="' + width + '"' : '') +
                        '>' + table.innerHTML.replace(/>\s*</g, '><').replace(/\bth\b/gi, "td") + '</table>')
                }
                me.fireEvent('contentchange');
                me.fireEvent('saveScene');
                html.html = '';
            } else {
                var div = me.document.createElement("div"), tables;
                div.innerHTML = html.html;
                tables = div.getElementsByTagName("table");
                if (domUtils.findParentByTagName(me.selection.getStart(), 'table')) {
                    utils.each(tables, function (t) {
                        domUtils.remove(t)
                    });
                    if (domUtils.findParentByTagName(me.selection.getStart(), 'caption', true)) {
                        div.innerHTML = div[browser.ie ? 'innerText' : 'textContent'];
                    }
                } else {
                    utils.each(tables, function (table) {
                        removeStyleSize(table, true);
                        domUtils.removeAttributes(table, ['style', 'border']);
                        utils.each(domUtils.getElementsByTagName(table, "td"), function (td) {
                            if (isEmptyBlock(td)) {
                                domUtils.fillNode(me.document, td);
                            }
                            removeStyleSize(td, true);
//                            domUtils.removeAttributes(td, ['style'])
                        });
                    });
                }
                html.html = div.innerHTML;
            }
        });

        me.addListener('afterpaste', function () {
            utils.each(domUtils.getElementsByTagName(me.body, "table"), function (table) {
                if (table.offsetWidth > me.body.offsetWidth) {
                    var defaultValue = getDefaultValue(me, table);
                    table.style.width = me.body.offsetWidth - (needIEHack ? parseInt(domUtils.getComputedStyle(me.body, 'margin-left'), 10) * 2 : 0) - defaultValue.tableBorder * 2 - (me.options.offsetWidth || 0) + 'px'
                }
            })
        });
        me.addListener('blur', function () {
            tableCopyList = null;
        });
        var timer;
        me.addListener('keydown', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                var rng = me.selection.getRange(),
                    cell = domUtils.findParentByTagName(rng.startContainer, ['th', 'td'], true);
                if (cell) {
                    var table = cell.parentNode.parentNode.parentNode;
                    if (table.offsetWidth > table.getAttribute("width")) {
                        cell.style.wordBreak = "break-all";
                    }
                }

            }, 100);
        });
        me.addListener("selectionchange", function () {
            toggleDragableState(me, false, "", null);
        });


        //���ݱ仯ʱ������������
        //todo �ɷ��Ǳ�Ǽ�⣬������漰���ı仯�Ͳ����������ؽ��͸���
        me.addListener("contentchange", function () {
            var me = this;
            //�������ų�һЩ����Ҫ���µ�״��
            hideDragLine(me);
            if (getUETableBySelected(me))return;
            var rng = me.selection.getRange();
            var start = rng.startContainer;
            start = domUtils.findParentByTagName(start, ['td', 'th'], true);
            utils.each(domUtils.getElementsByTagName(me.document, 'table'), function (table) {
                if (me.fireEvent("excludetable", table) === true) return;
                table.ueTable = new UETable(table);
                utils.each(domUtils.getElementsByTagName(me.document, 'td'), function (td) {

                    if (domUtils.isEmptyBlock(td) && td !== start) {
                        domUtils.fillNode(me.document, td);
                        if (browser.ie && browser.version == 6) {
                            td.innerHTML = '&nbsp;'
                        }
                    }
                });
                utils.each(domUtils.getElementsByTagName(me.document, 'th'), function (th) {
                    if (domUtils.isEmptyBlock(th) && th !== start) {
                        domUtils.fillNode(me.document, th);
                        if (browser.ie && browser.version == 6) {
                            th.innerHTML = '&nbsp;'
                        }
                    }
                });
                table.onmousemove = function () {
                    me.options.tableDragable && toggleDragButton(true, this, me);
                };
                table.onmouseout = function () {
                    toggleDragableState(me, false, "", null);
                    hideDragLine(me);
                };
                table.onclick = function (evt) {
                    evt = me.window.event || evt;
                    var target = getParentTdOrTh(evt.target || evt.srcElement);
                    if (!target)return;
                    var ut = getUETable(target),
                        table = ut.table,
                        cellInfo = ut.getCellInfo(target),
                        cellsRange,
                        rng = me.selection.getRange();
//                    if ("topLeft" == inPosition(table, mouseCoords(evt))) {
//                        cellsRange = ut.getCellsRange(ut.table.rows[0].cells[0], ut.getLastCell());
//                        ut.setSelected(cellsRange);
//                        return;
//                    }
//                    if ("bottomRight" == inPosition(table, mouseCoords(evt))) {
//
//                        return;
//                    }
                    if (inTableSide(table, target, evt, true)) {
                        var endTdCol = ut.getCell(ut.indexTable[ut.rowsNum - 1][cellInfo.colIndex].rowIndex, ut.indexTable[ut.rowsNum - 1][cellInfo.colIndex].cellIndex);
                        if (evt.shiftKey && ut.selectedTds.length) {
                            if (ut.selectedTds[0] !== endTdCol) {
                                cellsRange = ut.getCellsRange(ut.selectedTds[0], endTdCol);
                                ut.setSelected(cellsRange);
                            } else {
                                rng && rng.selectNodeContents(endTdCol).select();
                            }
                        } else {
                            if (target !== endTdCol) {
                                cellsRange = ut.getCellsRange(target, endTdCol);
                                ut.setSelected(cellsRange);
                            } else {
                                rng && rng.selectNodeContents(endTdCol).select();
                            }
                        }
                        return;
                    }
                    if (inTableSide(table, target, evt)) {
                        var endTdRow = ut.getCell(ut.indexTable[cellInfo.rowIndex][ut.colsNum - 1].rowIndex, ut.indexTable[cellInfo.rowIndex][ut.colsNum - 1].cellIndex);
                        if (evt.shiftKey && ut.selectedTds.length) {
                            if (ut.selectedTds[0] !== endTdRow) {
                                cellsRange = ut.getCellsRange(ut.selectedTds[0], endTdRow);
                                ut.setSelected(cellsRange);
                            } else {
                                rng && rng.selectNodeContents(endTdRow).select();
                            }
                        } else {
                            if (target !== endTdRow) {
                                cellsRange = ut.getCellsRange(target, endTdRow);
                                ut.setSelected(cellsRange);
                            } else {
                                rng && rng.selectNodeContents(endTdRow).select();
                            }
                        }
                    }
                };
                table.ondblclick = function (evt) {
                    evt = me.window.event || evt;
                    var target = getParentTdOrTh(evt.target || evt.srcElement);
                    if (target) {
                        var h;
                        if (h = getRelation(target, mouseCoords(evt))) {
                            if (h == 'h1') {
                                h = 'h';
                                if (inTableSide(domUtils.findParentByTagName(target, "table"), target, evt)) {
                                    me.execCommand('adaptbywindow');
                                } else {
                                    target = getUETable(target).getPreviewCell(target);
                                    if (target) {
                                        var rng = me.selection.getRange();
                                        rng.selectNodeContents(target).setCursor(true, true)
                                    }
                                }
                            }
                            h == 'h' && me.execCommand('adaptbytext');
                        }
                    }
                }
            });

            switchBoderColor(true);
        });

        //��IE8����֧��
        if (!browser.ie || (browser.ie && browser.version > 7)) {
            domUtils.on(me.document, "mousemove", mouseMoveEvent);
        }
        domUtils.on(me.document, "mouseout", function (evt) {
            var target = evt.target || evt.srcElement;
            if (target.tagName == "TABLE") {
                toggleDragableState(me, false, "", null);
            }
        });


        me.addListener("mousedown", mouseDownEvent);
        me.addListener("mouseup", mouseUpEvent);

        var currentRowIndex = 0;
        me.addListener("mousedown", function () {
            currentRowIndex = 0;
        });
        me.addListener('tabkeydown', function () {
            var range = this.selection.getRange(),
                common = range.getCommonAncestor(true, true),
                table = domUtils.findParentByTagName(common, 'table');
            if (table) {
                if (domUtils.findParentByTagName(common, 'caption', true)) {
                    var cell = domUtils.getElementsByTagName(table, 'th td');
                    if (cell && cell.length) {
                        range.setStart(cell[0], 0).setCursor(false, true)
                    }
                } else {
                    var cell = domUtils.findParentByTagName(common, ['td', 'th'], true),
                        ua = getUETable(cell);
                    currentRowIndex = cell.rowSpan > 1 ? currentRowIndex : ua.getCellInfo(cell).rowIndex;
                    var nextCell = ua.getTabNextCell(cell, currentRowIndex);
                    if (nextCell) {
                        if (isEmptyBlock(nextCell)) {
                            range.setStart(nextCell, 0).setCursor(false, true)
                        } else {
                            range.selectNodeContents(nextCell).select()
                        }
                    } /*else {ȥ��tab��������һ�б��
                        me.fireEvent('saveScene');
                        me.__hasEnterExecCommand = true;
                        this.execCommand('insertrownext');
                        me.__hasEnterExecCommand = false;
                        range = this.selection.getRange();
                        range.setStart(table.rows[table.rows.length - 1].cells[0], 0).setCursor();
                        me.fireEvent('saveScene');
                    }*/
                }
                return true;
            }

        });
        browser.ie && me.addListener('selectionchange', function () {
            toggleDragableState(this, false, "", null);
        });
        me.addListener("keydown", function (type, evt) {
            var me = this;
            //�����ڱ������һ������tab�����µı��
            var keyCode = evt.keyCode || evt.which;
            if (keyCode == 8 || keyCode == 46) {
                return;
            }
            var notCtrlKey = !evt.ctrlKey && !evt.metaKey && !evt.shiftKey && !evt.altKey;
            notCtrlKey && removeSelectedClass(domUtils.getElementsByTagName(me.body, "td"));
            var ut = getUETableBySelected(me);
            if (!ut) return;
            notCtrlKey && ut.clearSelected();
        });

        me.addListener("beforegetcontent", function () {
            switchBoderColor(false);
            browser.ie && utils.each(me.document.getElementsByTagName('caption'), function (ci) {
                if (domUtils.isEmptyNode(ci)) {
                    ci.innerHTML = '&nbsp;'
                }
            });
        });
        me.addListener("aftergetcontent", function () {
            switchBoderColor(true);
        });
        me.addListener("getAllHtml", function () {
            removeSelectedClass(me.document.getElementsByTagName("td"));
        });
        //����ȫ��״̬�²���ı�����ڷ�ȫ��״̬�³ſ��༭�������
        me.addListener("fullscreenchanged", function (type, fullscreen) {
            if (!fullscreen) {
                var ratio = this.body.offsetWidth / document.body.offsetWidth,
                    tables = domUtils.getElementsByTagName(this.body, "table");
                utils.each(tables, function (table) {
                    if (table.offsetWidth < me.body.offsetWidth) return false;
                    var tds = domUtils.getElementsByTagName(table, "td"),
                        backWidths = [];
                    utils.each(tds, function (td) {
                        backWidths.push(td.offsetWidth);
                    });
                    for (var i = 0, td; td = tds[i]; i++) {
                        td.setAttribute("width", Math.floor(backWidths[i] * ratio));
                    }
                    table.setAttribute("width", Math.floor(getTableWidth(me, needIEHack, getDefaultValue(me))))
                });
            }
        });

        //��дexecCommand������ڴ����ѡʱ�Ĵ���
        var oldExecCommand = me.execCommand;
        me.execCommand = function (cmd) {
            var me = this;
            cmd = cmd.toLowerCase();
            var ut = getUETableBySelected(me), tds,
                range = new dom.Range(me.document),
                cmdFun = me.commands[cmd] || UE.commands[cmd],
                result;
            if (!cmdFun) return;
            if (ut && !commands[cmd] && !cmdFun.notNeedUndo && !me.__hasEnterExecCommand) {
                me.__hasEnterExecCommand = true;
                me.fireEvent("beforeexeccommand", cmd);
                tds = ut.selectedTds;
                var lastState = -2, lastValue = -2, value, state;
                for (var i = 0, td; td = tds[i]; i++) {
                    if (isEmptyBlock(td)) {
                        range.setStart(td, 0).setCursor(false, true)
                    } else {
                        range.selectNodeContents(td).select(true);
                    }
                    state = me.queryCommandState(cmd);
                    value = me.queryCommandValue(cmd);
                    if (state != -1) {
                        if (lastState !== state || lastValue !== value) {
                            me._ignoreContentChange = true;
                            result = oldExecCommand.apply(me, arguments);
                            me._ignoreContentChange = false;

                        }
                        lastState = me.queryCommandState(cmd);
                        lastValue = me.queryCommandValue(cmd);
                        if (domUtils.isEmptyBlock(td)) {
                            domUtils.fillNode(me.document, td)
                        }
                    }
                }
                range.setStart(tds[0], 0).shrinkBoundary(true).setCursor(false, true);
                me.fireEvent('contentchange');
                me.fireEvent("afterexeccommand", cmd);
                me.__hasEnterExecCommand = false;
                me._selectionChange();
            } else {
                result = oldExecCommand.apply(me, arguments);
            }
            return result;
        };


    });
    /**
     * ɾ��obj�Ŀ��style���ĳ����Կ��
     * @param obj
     * @param replaceToProperty
     */
    function removeStyleSize(obj, replaceToProperty) {
        removeStyle(obj, "width", true);
        removeStyle(obj, "height", true);
    }

    function removeStyle(obj, styleName, replaceToProperty) {
        if (obj.style[styleName]) {
            replaceToProperty && obj.setAttribute(styleName, parseInt(obj.style[styleName], 10));
            obj.style[styleName] = "";
        }
    }

    function getParentTdOrTh(ele) {
        if (ele.tagName == "TD" || ele.tagName == "TH") return ele;
        var td;
        if (td = domUtils.findParentByTagName(ele, "td", true) || domUtils.findParentByTagName(ele, "th", true)) return td;
        return null;
    }

    function isEmptyBlock(node) {
        var reg = new RegExp(domUtils.fillChar, 'g');
        if (node[browser.ie ? 'innerText' : 'textContent'].replace(/^\s*$/, '').replace(reg, '').length > 0) {
            return 0;
        }
        for (var n in dtd.$isNotEmpty) {
            if (node.getElementsByTagName(n).length) {
                return 0;
            }
        }
        return 1;
    }


    function mouseCoords(evt) {
        if (evt.pageX || evt.pageY) {
            return { x:evt.pageX, y:evt.pageY };
        }
        return {
            x:evt.clientX + me.document.body.scrollLeft - me.document.body.clientLeft,
            y:evt.clientY + me.document.body.scrollTop - me.document.body.clientTop
        };
    }

    function mouseMoveEvent(evt) {
        try {
            //��ͨ״̬������ƶ�
            var target = getParentTdOrTh(evt.target || evt.srcElement),
                pos;
            //�޸ĵ�Ԫ���Сʱ������ƶ�
            if (onDrag && dragTd) {
                me.document.body.style.webkitUserSelect = 'none';
                me.selection.getNative()[browser.ie ? 'empty' : 'removeAllRanges']();
                pos = mouseCoords(evt);
                toggleDragableState(me, true, "", pos, target);
                if (onDrag == "h") {
                    dragLine.style.left = getPermissionX(dragTd, evt) + "px";
                } else if (onDrag == "v") {
                    dragLine.style.top = getPermissionY(dragTd, evt) + "px";
                }
                return;
            }
            //����괦��table��ʱ���޸��ƶ������еĹ��״̬
            if (target) {
                //���ʹ��table��Ϊ�����������������קЧ��
                if (me.fireEvent('excludetable', target) === true)
                    return;
                pos = mouseCoords(evt);
                var state = getRelation(target, pos),
                    table = domUtils.findParentByTagName(target, "table", true);

                if (inTableSide(table, target, evt, true)) {
                    //toggleCursor(pos,true,"_h");
                    if (me.fireEvent("excludetable", table) === true) return;
                    me.body.style.cursor = "url(" + me.options.cursorpath + "h.png),pointer";
                } else if (inTableSide(table, target, evt)) {
                    //toggleCursor(pos,true,"_v");
                    if (me.fireEvent("excludetable", table) === true) return;
                    me.body.style.cursor = "url(" + me.options.cursorpath + "v.png),pointer";
                } else {
                    //toggleCursor(pos,false,"");
                    me.body.style.cursor = "text";
                    if (/\d/.test(state)) {
                        state = state.replace(/\d/, '');
                        target = getUETable(target).getPreviewCell(target, state == "v");
                    }
                    //λ�ڵ�һ�еĶ������ߵ�һ�е����ʱ�����϶�
                    toggleDragableState(me, target ? !!state : false, target ? state : '', pos, target);
                }
//                toggleDragButton(inTable(pos,table),table);
            } else {
                toggleDragButton(false, table, me);
            }

        } catch (e) {
            showError(e);
        }
    }

    var dragButtomTimer;

    function toggleDragButton(show, table, editor) {
        if (!show) {
            if (dragOver)return;
            dragButtomTimer = setTimeout(function () {
                !dragOver && dragButton && dragButton.parentNode && dragButton.parentNode.removeChild(dragButton);
            }, 2000);
        } else {
            createDragButton(table, editor);
        }
    }

    function createDragButton(table, editor) {
        var pos = domUtils.getXY(table),
            doc = table.ownerDocument;
        if (dragButton && dragButton.parentNode)return dragButton;
        dragButton = doc.createElement("div");
        dragButton.contentEditable = false;
        dragButton.innerHTML = "";
        dragButton.style.cssText = "width:15px;height:15px;background-image:url(" + editor.options.UEDITOR_HOME_URL + "dialogs/table/dragicon.png);position: absolute;cursor:move;top:" + (pos.y - 15) + "px;left:" + (pos.x) + "px;";
        domUtils.unSelectable(dragButton);
        dragButton.onmouseover = function (evt) {
            dragOver = true;
        };
        dragButton.onmouseout = function (evt) {
            dragOver = false;
        };
        domUtils.on(dragButton, 'click', function (type, evt) {
            doClick(evt, this);
        });
        domUtils.on(dragButton, 'dblclick', function (type, evt) {
            doDblClick(evt);
        });
        domUtils.on(dragButton, 'dragstart', function (type, evt) {
            domUtils.preventDefault(evt);
        });
        var timer;

        function doClick(evt, button) {
            // �������������Ҫ����
            clearTimeout(timer);
            timer = setTimeout(function () {
                editor.fireEvent("tableClicked", table, button);
            }, 300);
        }

        function doDblClick(evt) {
            clearTimeout(timer);
            var ut = getUETable(table),
                start = table.rows[0].cells[0],
                end = ut.getLastCell(),
                range = ut.getCellsRange(start, end);
            editor.selection.getRange().setStart(start, 0).setCursor(false, true);
            ut.setSelected(range);
        }

        doc.body.appendChild(dragButton);
    }


//    function inPosition(table, pos) {
//        var tablePos = domUtils.getXY(table),
//            width = table.offsetWidth,
//            height = table.offsetHeight;
//        if (pos.x - tablePos.x < 5 && pos.y - tablePos.y < 5) {
//            return "topLeft";
//        } else if (tablePos.x + width - pos.x < 5 && tablePos.y + height - pos.y < 5) {
//            return "bottomRight";
//        }
//    }

    function inTableSide(table, cell, evt, top) {
        var pos = mouseCoords(evt),
            state = getRelation(cell, pos);
        if (top) {
            var caption = table.getElementsByTagName("caption")[0],
                capHeight = caption ? caption.offsetHeight : 0;
            return (state == "v1") && ((pos.y - domUtils.getXY(table).y - capHeight) < 8);
        } else {
            return (state == "h1") && ((pos.x - domUtils.getXY(table).x) < 8);
        }
    }


    /**
     * ��ȡ�϶�ʱ�����X������
     * @param dragTd
     * @param evt
     */
    function getPermissionX(dragTd, evt) {
        var ut = getUETable(dragTd);
        if (ut) {
            var preTd = ut.getSameEndPosCells(dragTd, "x")[0],
                nextTd = ut.getSameStartPosXCells(dragTd)[0],
                mouseX = mouseCoords(evt).x,
                left = (preTd ? domUtils.getXY(preTd).x : domUtils.getXY(ut.table).x) + 20 ,
                right = nextTd ? domUtils.getXY(nextTd).x + nextTd.offsetWidth - 20 : (me.body.offsetWidth + 5 || parseInt(domUtils.getComputedStyle(me.body, "width"), 10));
            return mouseX < left ? left : mouseX > right ? right : mouseX;
        }
    }

    /**
     * ��ȡ�϶�ʱ�����Y������
     * @param dragTd
     * @param evt
     */
    function getPermissionY(dragTd, evt) {
        try {
            var top = domUtils.getXY(dragTd).y,
                mousePosY = mouseCoords(evt).y;
            return mousePosY < top ? top : mousePosY;
        } catch (e) {
            showError(e);
        }

    }

    /**
     * �ƶ�״̬�л�
     * @param dragable
     * @param dir
     */
    function toggleDragableState(editor, dragable, dir, mousePos, cell) {
        try {
            editor.body.style.cursor = dir == "h" ? "col-resize" : dir == "v" ? "row-resize" : "text";
            if (browser.ie) {
                if (dir && !mousedown && !getUETableBySelected(editor)) {
                    getDragLine(editor, editor.document);
                    showDragLineAt(dir, cell);
                } else {
                    hideDragLine(editor)
                }
            }
            onBorder = dragable;
        } catch (e) {
            showError(e);
        }
    }


    /**
     * ��ȡ����뵱ǰ��Ԫ������λ��
     * @param ele
     * @param mousePos
     */
    function getRelation(ele, mousePos) {
        var elePos = domUtils.getXY(ele);
        if (elePos.x + ele.offsetWidth - mousePos.x < 4) {
            return "h";
        }
        if (mousePos.x - elePos.x < 4) {
            return 'h1'
        }
        if (elePos.y + ele.offsetHeight - mousePos.y < 4) {
            return "v";
        }
        if (mousePos.y - elePos.y < 4) {
            return 'v1'
        }
        return '';
    }

    function mouseDownEvent(type, evt) {

        //�Ҽ��˵���������
        if (evt.button == 2) {
            var ut = getUETableBySelected(me),
                flag = false;
            if (ut) {
                var td = getTargetTd(me, evt);
                utils.each(ut.selectedTds, function (ti) {
                    if (ti === td) {
                        flag = true;
                    }
                });
                if (!flag) {
                    removeSelectedClass(domUtils.getElementsByTagName(me.body, "td"));
                    removeSelectedClass(domUtils.getElementsByTagName(me.body, "th"));
                    ut.clearSelected()
                } else {
                    td = ut.selectedTds[0];
                    setTimeout(function () {
                        me.selection.getRange().setStart(td, 0).setCursor(false, true);
                    }, 0);

                }

            }
            return;
        }
        if (evt.shiftKey) {
            return;
        }

        removeSelectedClass(domUtils.getElementsByTagName(me.body, "td"));
        removeSelectedClass(domUtils.getElementsByTagName(me.body, "th"));
        //trace:3113
        //ѡ�е�Ԫ�񣬵��table�ⲿ���������table�Ϲҵ�ueTable,������getUETableBySelected��������ֵ
        utils.each(me.document.getElementsByTagName('table'), function (t) {
            t.ueTable = null;
        });
        startTd = getTargetTd(me, evt);
        if (!startTd) return;
        var table = domUtils.findParentByTagName(startTd, "table", true);
        ut = getUETable(table);
        ut && ut.clearSelected();
        //�жϵ�ǰ���״̬
        if (!onBorder) {
            me.document.body.style.webkitUserSelect = '';
            mousedown = true;
            me.addListener('mouseover', mouseOverEvent);
        } else {
            if (browser.ie && browser.version < 8) return;
            var state = getRelation(startTd, mouseCoords(evt));
            if (/\d/.test(state)) {
                state = state.replace(/\d/, '');
                startTd = getUETable(startTd).getPreviewCell(startTd, state == 'v');
            }
            hideDragLine(me);
            getDragLine(me, me.document);
            showDragLineAt(state, startTd);
            mousedown = true;
            //�϶���ʼ
            onDrag = state;
            dragTd = startTd;
        }
    }

    function removeSelectedClass(cells) {
        utils.each(cells, function (cell) {
            domUtils.removeClasses(cell, "selectTdClass");
        })
    }

    function addSelectedClass(cells) {
        utils.each(cells, function (cell) {
            domUtils.addClass(cell, "selectTdClass");
        })
    }

    function getWidth(cell) {
        if (!cell)return 0;
        return parseInt(domUtils.getComputedStyle(cell, "width"), 10);
    }

    function changeColWidth(cell, changeValue) {
        if (Math.abs(changeValue) < 10) return;
        var ut = getUETable(cell);
        if (ut) {
            var table = ut.table,
                backTableWidth = getWidth(table),
                defaultValue = getDefaultValue(me, table),
            //���ﲻ����һ����û����������һ����û�У�������Ϊ�ñ��Ľṹ���Ծ���
                leftCells = ut.getSameEndPosCells(cell, "x"),
                backLeftWidth = getWidth(leftCells[0]) - defaultValue.tdPadding * 2 - defaultValue.tdBorder,
                rightCells = ut.getSameStartPosXCells(cell),
                backRightWidth = getWidth(rightCells[0]) - defaultValue.tdPadding * 2 - defaultValue.tdBorder;
            //���б�rowspanʱ����
            utils.each(leftCells, function (cell) {
                if (cell.style.width) cell.style.width = "";
                if (changeValue < 0)cell.style.wordBreak = "break-all";
                cell.setAttribute("width", backLeftWidth + changeValue);
            });
            utils.each(rightCells, function (cell) {
                if (cell.style.width) cell.style.width = "";
                if (changeValue > 0)cell.style.wordBreak = "break-all";
                cell.setAttribute("width", backRightWidth - changeValue);
            });
            //������ڱ�����ұ��϶�������Ҫ��������ȣ������ںϲ����ĵ�Ԫ�����������֣����ᱻ�ſ�
            if (!cell.nextSibling) {
                if (table.style.width) table.style.width = "";
                table.setAttribute("width", backTableWidth + changeValue);
            }
        }

    }

    function changeRowHeight(td, changeValue) {
        if (Math.abs(changeValue) < 10) return;
        var ut = getUETable(td);
        if (ut) {
            var cells = ut.getSameEndPosCells(td, "y"),
            //������Ҫ�����仯��td��ԭʼ�߶ȣ���������޷���ȡ��ȷ��ֵ
                backHeight = cells[0] ? cells[0].offsetHeight : 0;
            for (var i = 0, cell; cell = cells[i++];) {
                setCellHeight(cell, changeValue, backHeight);
            }
        }

    }

    function setCellHeight(cell, height, backHeight) {
        var lineHight = parseInt(domUtils.getComputedStyle(cell, "line-height"), 10),
            tmpHeight = backHeight + height;
        height = tmpHeight < lineHight ? lineHight : tmpHeight;
        if (cell.style.height) cell.style.height = "";
        cell.rowSpan == 1 ? cell.setAttribute("height", height) : (cell.removeAttribute && cell.removeAttribute("height"));
    }

    function mouseUpEvent(type, evt) {
        if (evt.button == 2)return;
        var me = this;
        //��������ԭ����ѡ����
        var range = me.selection.getRange(),
            start = domUtils.findParentByTagName(range.startContainer, 'table', true),
            end = domUtils.findParentByTagName(range.endContainer, 'table', true);

        if (start || end) {
            if (start === end) {
                start = domUtils.findParentByTagName(range.startContainer, ['td', 'th', 'caption'], true);
                end = domUtils.findParentByTagName(range.endContainer, ['td', 'th', 'caption'], true);
                if (start !== end) {
                    me.selection.clearRange()
                }
            } else {
                me.selection.clearRange()
            }
        }
        mousedown = false;
        me.document.body.style.webkitUserSelect = '';
        //��ק״̬�µ�mouseUP
        if ((!browser.ie || (browser.ie && browser.version > 7)) && onDrag && dragTd) {
            dragLine = me.document.getElementById('ue_tableDragLine');
            var dragTdPos = domUtils.getXY(dragTd),
                dragLinePos = domUtils.getXY(dragLine);
            switch (onDrag) {
                case "h":
                    changeColWidth(dragTd, dragLinePos.x - dragTdPos.x - dragTd.offsetWidth);
                    break;
                case "v":
                    changeRowHeight(dragTd, dragLinePos.y - dragTdPos.y - dragTd.offsetHeight);
                    break;
                default:
            }
            onDrag = "";
            dragTd = null;

            hideDragLine(me);
            return;
        }
        //����״̬�µ�mouseup
        if (!startTd) {
            var target = domUtils.findParentByTagName(evt.target || evt.srcElement, "td", true);
            if (!target) target = domUtils.findParentByTagName(evt.target || evt.srcElement, "th", true);
            if (target && (target.tagName == "TD" || target.tagName == "TH")) {
                if (me.fireEvent("excludetable", target) === true) return;
                range = new dom.Range(me.document);
                range.setStart(target, 0).setCursor(false, true);
            }
        } else {
            var ut = getUETable(startTd),
                cell = ut ? ut.selectedTds[0] : null;
            if (cell) {
                range = new dom.Range(me.document);
                if (domUtils.isEmptyBlock(cell)) {
                    range.setStart(cell, 0).setCursor(false, true);
                } else {
                    range.selectNodeContents(cell).shrinkBoundary().setCursor(false, true);
                }
            } else {
                range = me.selection.getRange().shrinkBoundary();
                if (!range.collapsed) {
                    var start = domUtils.findParentByTagName(range.startContainer, ['td', 'th'], true),
                        end = domUtils.findParentByTagName(range.endContainer, ['td', 'th'], true);
                    //��table��ߵĲ������
                    if (start && !end || !start && end || start && end && start !== end) {
                        range.setCursor(false, true);
                    }
                }
            }
            startTd = null;
            me.removeListener('mouseover', mouseOverEvent);
        }
        me._selectionChange(250, evt);
    }

    function mouseOverEvent(type, evt) {
        var me = this,
            tar = evt.target || evt.srcElement;
        currentTd = domUtils.findParentByTagName(tar, "td", true) || domUtils.findParentByTagName(tar, "th", true);
        //��Ҫ�ж�����TD�Ƿ�λ��ͬһ�������
        if (startTd && currentTd &&
            ((startTd.tagName == "TD" && currentTd.tagName == "TD") || (startTd.tagName == "TH" && currentTd.tagName == "TH")) &&
            domUtils.findParentByTagName(startTd, 'table') == domUtils.findParentByTagName(currentTd, 'table')) {
            var ut = getUETable(currentTd);
            if (startTd != currentTd) {
                me.document.body.style.webkitUserSelect = 'none';
                me.selection.getNative()[browser.ie ? 'empty' : 'removeAllRanges']();
                var range = ut.getCellsRange(startTd, currentTd);
                ut.setSelected(range);
            } else {
                me.document.body.style.webkitUserSelect = '';
                ut.clearSelected();
            }

        }
        evt.preventDefault ? evt.preventDefault() : (evt.returnValue = false);
    }

    /**
     * ���ݵ�ǰ�����td����table��ȡ��������
     * @param tdOrTable
     */
    function getUETable(tdOrTable) {
        var tag = tdOrTable.tagName.toLowerCase();
        tdOrTable = (tag == "td" || tag == "th" || tag == 'caption') ? domUtils.findParentByTagName(tdOrTable, "table", true) : tdOrTable;
        if (!tdOrTable.ueTable) {
            tdOrTable.ueTable = new UETable(tdOrTable);
        }
        return tdOrTable.ueTable;
    }

    /**
     * ���ݵ�ǰ��ѡ��td����ȡueTable����
     */
    function getUETableBySelected(editor) {
        var table = getTableItemsByRange(editor).table;
        if (table && table.ueTable && table.ueTable.selectedTds.length) {
            return table.ueTable;
        }
        return null;
    }

    function getDragLine(editor, doc) {
        if (mousedown)return;
        dragLine = editor.document.createElement("div");
        domUtils.setAttributes(dragLine, {
            id:"ue_tableDragLine",
            unselectable:'on',
            contenteditable:false,
            'onresizestart':'return false',
            'ondragstart':'return false',
            'onselectstart':'return false',
            style:"background-color:blue;position:absolute;padding:0;margin:0;background-image:none;border:0px none;opacity:0;filter:alpha(opacity=0)"
        });
//            domUtils.on(dragLine,['resizestart','dragstart','selectstart'],function(evt){
//                domUtils.preventDefault(evt);
//            });
        editor.body.appendChild(dragLine);


    }

    function hideDragLine(editor) {
        if (mousedown)return;
        var line;
        while (line = editor.document.getElementById('ue_tableDragLine')) {
            domUtils.remove(line)
        }
    }

    /**
     * ����state��v|h����cellλ����ʾ����
     * @param state
     * @param cell
     */
    function showDragLineAt(state, cell) {
        if (!cell) return;
        var table = domUtils.findParentByTagName(cell, "table"),
            caption = table.getElementsByTagName('caption'),
            width = table.offsetWidth,
            height = table.offsetHeight - (caption.length > 0 ? caption[0].offsetHeight : 0),
            tablePos = domUtils.getXY(table),
            cellPos = domUtils.getXY(cell), css;
        switch (state) {
            case "h":
                css = 'height:' + height + 'px;top:' + (tablePos.y + (caption.length > 0 ? caption[0].offsetHeight : 0)) + 'px;left:' + (cellPos.x + cell.offsetWidth - 2);
                dragLine.style.cssText = css + 'px;position: absolute;display:block;background-color:blue;width:1px;border:0; color:blue;opacity:.3;filter:alpha(opacity=30)';
                break;
            case "v":
                css = 'width:' + width + 'px;left:' + tablePos.x + 'px;top:' + (cellPos.y + cell.offsetHeight - 2 );
                //�������border:0��color:blue������Ͱ�ie��֧�ֱ���ɫ��ʾ
                dragLine.style.cssText = css + 'px;overflow:hidden;position: absolute;display:block;background-color:blue;height:1px;border:0;color:blue;opacity:.2;filter:alpha(opacity=20)';
                break;
            default:
        }
    }

    /**
     * �����߿���ɫΪ��ɫʱ����Ϊ����,trueΪ�������
     * @param flag
     */
    function switchBoderColor(flag) {
        var tableArr = domUtils.getElementsByTagName(me.body, "table"), color;
        for (var i = 0, node; node = tableArr[i++];) {
            var td = domUtils.getElementsByTagName(node, "td");
            if (td[0]) {
                if (flag) {
                    color = (td[0].style.borderColor).replace(/\s/g, "");
                    if (/(#ffffff)|(rgb\(255,f55,255\))/ig.test(color))
                        domUtils.addClass(node, "noBorderTable")
                } else {
                    domUtils.removeClasses(node, "noBorderTable")
                }
            }

        }
    }

    function getTableWidth(editor, needIEHack, defaultValue) {
        var body = editor.body;
        return body.offsetWidth - (needIEHack ? parseInt(domUtils.getComputedStyle(body, 'margin-left'), 10) * 2 : 0) - defaultValue.tableBorder * 2 - (editor.options.offsetWidth || 0);
    }

    UE.commands['inserttable'] = {
        queryCommandState:function () {
            return getTableItemsByRange(this).table ? -1 : 0;
        },
        execCommand:function (cmd, opt) {
            function createTable(opt, tableWidth, tdWidth) {
                var html = [],
                    rowsNum = opt.numRows,
                    colsNum = opt.numCols;
                for (var r = 0; r < rowsNum; r++) {
                    html.push('<tr>');
                    for (var c = 0; c < colsNum; c++) {
                        html.push('<td style="font-size:12px;line-height:200%;" align="center" bgcolor="#FFFFFF" width="' + tdWidth + '"  vAlign="' + opt.tdvalign + '" >' + (browser.ie ? domUtils.fillChar : '&nbsp;') + '</td>')
                    }
                    html.push('</tr>')
                }
                return '<table align="center" width="98%" bgcolor="#a2c7e1" border="0" cellpadding="1" cellspacing="1"><tbody>' + html.join('') + '</tbody></table>'
            }

            if (!opt) {
                opt = utils.extend({}, {
                    numCols:this.options.defaultCols,
                    numRows:this.options.defaultRows,
                    tdvalign:this.options.tdvalign
                })
            }

            var range = this.selection.getRange(),
                start = range.startContainer,
                firstParentBlock = domUtils.findParent(start, function (node) {
                    return domUtils.isBlockElm(node);
                }, true);
            var me = this,
                defaultValue = getDefaultValue(me),
                tableWidth = getTableWidth(me, needIEHack, defaultValue) - (firstParentBlock ? parseInt(domUtils.getXY(firstParentBlock).x, 10) : 0),
                tdWidth = Math.floor(tableWidth / opt.numCols - defaultValue.tdPadding * 2 - defaultValue.tdBorder);
            //todo��������
            !opt.tdvalign && (opt.tdvalign = me.options.tdvalign);
            me.execCommand("inserthtml", createTable(opt, tableWidth, tdWidth));
        }
    };

    UE.commands['insertparagraphbeforetable'] = {
        queryCommandState:function () {
            return getTableItemsByRange(this).cell ? 0 : -1;
        },
        execCommand:function () {
            var table = getTableItemsByRange(this).table;
            if (table) {
                var p = this.document.createElement("p");
                p.innerHTML = browser.ie ? '&nbsp;' : '&nbsp;';
                table.parentNode.insertBefore(p, table);
                this.selection.getRange().setStart(p, 0).setCursor();
            }
        }
    };

    UE.commands['deletetable'] = {
        queryCommandState:function () {
            var rng = this.selection.getRange();
            return domUtils.findParentByTagName(rng.startContainer, 'table', true) ? 0 : -1;
        },
        execCommand:function (cmd, table) {
            var rng = this.selection.getRange();
            table = table || domUtils.findParentByTagName(rng.startContainer, 'table', true);
            if (table) {
                var next = table.nextSibling;
                if (!next) {
                    next = domUtils.createElement(this.document, 'p', {
                        'innerHTML':browser.ie ? domUtils.fillChar : '&nbsp;'
                    });
                    table.parentNode.insertBefore(next, table);
                }
                domUtils.remove(table);
                rng = this.selection.getRange();
                if (next.nodeType == 3) {
                    rng.setStartBefore(next)
                } else {
                    rng.setStart(next, 0)
                }
                rng.setCursor(false, true)
                toggleDragableState(this, false, "", null);
                if (dragButton)domUtils.remove(dragButton);
            }

        }
    };
    UE.commands['cellalign'] = {
        queryCommandState:function () {
            return getSelectedArr(this).length ? 0 : -1
        },
        execCommand:function (cmd, align) {
            var selectedTds = getSelectedArr(this);
            if (selectedTds.length) {
                for (var i = 0, ci; ci = selectedTds[i++];) {
                    ci.setAttribute('align', align);
                }
            }
        }
    };
    UE.commands['cellvalign'] = {
        queryCommandState:function () {
            return getSelectedArr(this).length ? 0 : -1;
        },
        execCommand:function (cmd, valign) {
            var selectedTds = getSelectedArr(this);
            if (selectedTds.length) {
                for (var i = 0, ci; ci = selectedTds[i++];) {
                    ci.setAttribute('vAlign', valign);
                }
            }
        }
    };
    UE.commands['insertcaption'] = {
        queryCommandState:function () {
            var table = getTableItemsByRange(this).table;
            if (table) {
                return table.getElementsByTagName('caption').length == 0 ? 1 : -1;
            }
            return -1;
        },
        execCommand:function () {
            var table = getTableItemsByRange(this).table;
            if (table) {
                var caption = this.document.createElement('caption');
                caption.innerHTML = browser.ie ? domUtils.fillChar : '&nbsp;';
                table.insertBefore(caption, table.firstChild);
                var range = this.selection.getRange();
                range.setStart(caption, 0).setCursor();
            }

        }
    };
    UE.commands['deletecaption'] = {
        queryCommandState:function () {
            var rng = this.selection.getRange(),
                table = domUtils.findParentByTagName(rng.startContainer, 'table');
            if (table) {
                return table.getElementsByTagName('caption').length == 0 ? -1 : 1;
            }
            return -1;
        },
        execCommand:function () {
            var rng = this.selection.getRange(),
                table = domUtils.findParentByTagName(rng.startContainer, 'table');
            if (table) {
                domUtils.remove(table.getElementsByTagName('caption')[0]);
                var range = this.selection.getRange();
                range.setStart(table.rows[0].cells[0], 0).setCursor();
            }

        }
    };
    UE.commands['inserttitle'] = {
        queryCommandState:function () {
            var table = getTableItemsByRange(this).table;
            if (table) {
                var firstRow = table.rows[0];
                return firstRow.getElementsByTagName('th').length == 0 ? 0 : -1
            }
            return -1;
        },
        execCommand:function () {
            var table = getTableItemsByRange(this).table;
            if (table) {
                getUETable(table).insertRow(0, 'th');
            }
            var th = table.getElementsByTagName('th')[0];
            this.selection.getRange().setStart(th, 0).setCursor(false, true);
        }
    };
    UE.commands['deletetitle'] = {
        queryCommandState:function () {
            var table = getTableItemsByRange(this).table;
            if (table) {
                var firstRow = table.rows[0];
                return firstRow.getElementsByTagName('th').length ? 0 : -1
            }
            return -1;
        },
        execCommand:function () {
            var table = getTableItemsByRange(this).table;
            if (table) {
                domUtils.remove(table.rows[0])
            }
            var td = table.getElementsByTagName('td')[0];
            this.selection.getRange().setStart(td, 0).setCursor(false, true);
        }
    };

    UE.commands["mergeright"] = {
        queryCommandState:function (cmd) {
            var tableItems = getTableItemsByRange(this);
            if (!tableItems.cell) return -1;
            var ut = getUETable(tableItems.table);
            if (ut.selectedTds.length) return -1;
            var cellInfo = ut.getCellInfo(tableItems.cell),
                rightColIndex = cellInfo.colIndex + cellInfo.colSpan;
            if (rightColIndex >= ut.colsNum) return -1;
            var rightCellInfo = ut.indexTable[cellInfo.rowIndex][rightColIndex];
            return (rightCellInfo.rowIndex == cellInfo.rowIndex
                && rightCellInfo.rowSpan == cellInfo.rowSpan) ? 0 : -1;
        },
        execCommand:function (cmd) {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell);
            ut.mergeRight(cell);
            rng.moveToBookmark(bk).select();
        }
    };
    UE.commands["mergedown"] = {
        queryCommandState:function (cmd) {
            var tableItems = getTableItemsByRange(this),
                cell = tableItems.cell;
            if (!cell || cell.tagName == "TH") return -1;
            var ut = getUETable(tableItems.table);
            if (ut.selectedTds.length)return -1;
            var cellInfo = ut.getCellInfo(tableItems.cell),
                downRowIndex = cellInfo.rowIndex + cellInfo.rowSpan;
            // ����������±�����f���Һϲ�
            if (downRowIndex >= ut.rowsNum) return -1;
            var downCellInfo = ut.indexTable[downRowIndex][cellInfo.colIndex];
            // ���ҽ�������Cell�Ŀ�ʼ�кźͽ����к�һ��ʱ�ܽ��кϲ�
            return (downCellInfo.colIndex == cellInfo.colIndex
                && downCellInfo.colSpan == cellInfo.colSpan) && tableItems.cell.tagName !== 'TH' ? 0 : -1;
        },
        execCommand:function () {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell);
            ut.mergeDown(cell);
            rng.moveToBookmark(bk).select();
        }
    };
    UE.commands["mergecells"] = {
        queryCommandState:function () {
            return getUETableBySelected(this) ? 0 : -1;
        },
        execCommand:function () {
            var ut = getUETableBySelected(this);
            if (ut && ut.selectedTds.length) {
                var cell = ut.selectedTds[0];
                getUETableBySelected(this).mergeRange();
                var rng = this.selection.getRange();
                if (domUtils.isEmptyBlock(cell)) {
                    rng.setStart(cell, 0).collapse(true)
                } else {
                    rng.selectNodeContents(cell)
                }
                rng.select();
            }


        }
    };
    UE.commands["insertrow"] = {
        queryCommandState:function () {
            var tableItems = getTableItemsByRange(this),
                cell = tableItems.cell;
            return cell && cell.tagName == "TD" && getUETable(tableItems.table).rowsNum < this.options.maxRowNum ? 0 : -1;
        },
        execCommand:function () {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell),
                cellInfo = ut.getCellInfo(cell);
            //ut.insertRow(!ut.selectedTds.length ? cellInfo.rowIndex:ut.cellsRange.beginRowIndex,'');
            if (!ut.selectedTds.length) {
                ut.insertRow(cellInfo.rowIndex, cell);
            } else {
                var range = ut.cellsRange;
                for (var i = 0, len = range.endRowIndex - range.beginRowIndex + 1; i < len; i++) {
                    ut.insertRow(range.beginRowIndex, cell);
                }
            }
            rng.moveToBookmark(bk).select();
        }
    };
    //�������
    UE.commands["insertrownext"] = {
        queryCommandState:function () {
            var tableItems = getTableItemsByRange(this),
                cell = tableItems.cell;
            return cell && (cell.tagName == "TD") && getUETable(tableItems.table).rowsNum < this.options.maxRowNum ? 0 : -1;
        },
        execCommand:function () {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell),
                cellInfo = ut.getCellInfo(cell);
            //ut.insertRow(!ut.selectedTds.length? cellInfo.rowIndex + cellInfo.rowSpan : ut.cellsRange.endRowIndex + 1,'');
            if (!ut.selectedTds.length) {
                ut.insertRow(cellInfo.rowIndex + cellInfo.rowSpan, cell);
            } else {
                var range = ut.cellsRange;
                for (var i = 0, len = range.endRowIndex - range.beginRowIndex + 1; i < len; i++) {
                    ut.insertRow(range.endRowIndex + 1, cell);
                }
            }
            rng.moveToBookmark(bk).select();
        }
    };
    UE.commands["deleterow"] = {
        queryCommandState:function () {
            var tableItems = getTableItemsByRange(this);
            if (!tableItems.cell) {
                return -1;
            }
        },
        execCommand:function () {
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell),
                cellsRange = ut.cellsRange,
                cellInfo = ut.getCellInfo(cell),
                preCell = ut.getVSideCell(cell),
                nextCell = ut.getVSideCell(cell, true),
                rng = this.selection.getRange();
            if (utils.isEmptyObject(cellsRange)) {
                ut.deleteRow(cellInfo.rowIndex);
            } else {
                for (var i = cellsRange.beginRowIndex; i < cellsRange.endRowIndex + 1; i++) {
                    ut.deleteRow(cellsRange.beginRowIndex);
                }
            }
            var table = ut.table;
            if (!table.getElementsByTagName('td').length) {
                var nextSibling = table.nextSibling;
                domUtils.remove(table);
                if (nextSibling) {
                    rng.setStart(nextSibling, 0).setCursor(false, true);
                }
            } else {
                if (cellInfo.rowSpan == 1 || cellInfo.rowSpan == cellsRange.endRowIndex - cellsRange.beginRowIndex + 1) {
                    if (nextCell || preCell) rng.selectNodeContents(nextCell || preCell).setCursor(false, true);
                } else {
                    var newCell = ut.getCell(cellInfo.rowIndex, ut.indexTable[cellInfo.rowIndex][cellInfo.colIndex].cellIndex);
                    if (newCell) rng.selectNodeContents(newCell).setCursor(false, true);
                }
            }
        }
    };
    UE.commands["insertcol"] = {
        queryCommandState:function (cmd) {
            var tableItems = getTableItemsByRange(this),
                cell = tableItems.cell;
            return cell && (cell.tagName == "TD" || cell.tagName == 'TH') && getUETable(tableItems.table).colsNum < this.options.maxColNum ? 0 : -1;
        },
        execCommand:function (cmd) {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            if (this.queryCommandState(cmd) == -1)return;
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell),
                cellInfo = ut.getCellInfo(cell);
            //ut.insertCol(!ut.selectedTds.length ? cellInfo.colIndex:ut.cellsRange.beginColIndex);
            if (!ut.selectedTds.length) {
                ut.insertCol(cellInfo.colIndex, cell);
            } else {
                var range = ut.cellsRange;
                for (var i = 0, len = range.endColIndex - range.beginColIndex + 1; i < len; i++) {
                    ut.insertCol(range.beginColIndex, cell);
                }
            }
            rng.moveToBookmark(bk).select(true);
        }
    };
    UE.commands["insertcolnext"] = {
        queryCommandState:function () {
            var tableItems = getTableItemsByRange(this),
                cell = tableItems.cell;
            return cell && getUETable(tableItems.table).colsNum < this.options.maxColNum ? 0 : -1;
        },
        execCommand:function () {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell),
                cellInfo = ut.getCellInfo(cell);
            //ut.insertCol(!ut.selectedTds.length ? cellInfo.colIndex + cellInfo.colSpan:ut.cellsRange.endColIndex +1);
            if (!ut.selectedTds.length) {
                ut.insertCol(cellInfo.colIndex + cellInfo.colSpan, cell);
            } else {
                var range = ut.cellsRange;
                for (var i = 0, len = range.endColIndex - range.beginColIndex + 1; i < len; i++) {
                    ut.insertCol(range.endColIndex + 1, cell);
                }
            }
            rng.moveToBookmark(bk).select();
        }
    };

    UE.commands["deletecol"] = {
        queryCommandState:function () {
            var tableItems = getTableItemsByRange(this);
            if (!tableItems.cell) return -1;
        },
        execCommand:function () {
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell),
                range = ut.cellsRange,
                cellInfo = ut.getCellInfo(cell),
                preCell = ut.getHSideCell(cell),
                nextCell = ut.getHSideCell(cell, true);
            if (utils.isEmptyObject(range)) {
                ut.deleteCol(cellInfo.colIndex);
            } else {
                for (var i = range.beginColIndex; i < range.endColIndex + 1; i++) {
                    ut.deleteCol(range.beginColIndex);
                }
            }
            var table = ut.table,
                rng = this.selection.getRange();

            if (!table.getElementsByTagName('td').length) {
                var nextSibling = table.nextSibling;
                domUtils.remove(table);
                if (nextSibling) {
                    rng.setStart(nextSibling, 0).setCursor(false, true);
                }
            } else {
                if (domUtils.inDoc(cell, this.document)) {
                    rng.setStart(cell, 0).setCursor(false, true);
                } else {
                    if (nextCell && domUtils.inDoc(nextCell, this.document)) {
                        rng.selectNodeContents(nextCell).setCursor(false, true);
                    } else {
                        if (preCell && domUtils.inDoc(preCell, this.document)) {
                            rng.selectNodeContents(preCell).setCursor(true, true);
                        }
                    }
                }
            }
        }
    };
    UE.commands["splittocells"] = {
        queryCommandState:function () {
            var tableItems = getTableItemsByRange(this),
                cell = tableItems.cell;
            if (!cell) return -1;
            var ut = getUETable(tableItems.table);
            if (ut.selectedTds.length > 0) return -1;
            return cell && (cell.colSpan > 1 || cell.rowSpan > 1) ? 0 : -1;
        },
        execCommand:function () {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell);
            ut.splitToCells(cell);
            rng.moveToBookmark(bk).select();
        }
    };
    UE.commands["splittorows"] = {
        queryCommandState:function () {
            var tableItems = getTableItemsByRange(this),
                cell = tableItems.cell;
            if (!cell) return -1;
            var ut = getUETable(tableItems.table);
            if (ut.selectedTds.length > 0) return -1;
            return cell && cell.rowSpan > 1 ? 0 : -1;
        },
        execCommand:function () {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell);
            ut.splitToRows(cell);
            rng.moveToBookmark(bk).select();
        }
    };
    UE.commands["splittocols"] = {
        queryCommandState:function () {
            var tableItems = getTableItemsByRange(this),
                cell = tableItems.cell;
            if (!cell) return -1;
            var ut = getUETable(tableItems.table);
            if (ut.selectedTds.length > 0) return -1;
            return cell && cell.colSpan > 1 ? 0 : -1;
        },
        execCommand:function () {
            var rng = this.selection.getRange(),
                bk = rng.createBookmark(true);
            var cell = getTableItemsByRange(this).cell,
                ut = getUETable(cell);
            ut.splitToCols(cell);
            rng.moveToBookmark(bk).select();

        }
    };

    function getDefaultValue(editor, table) {
        var borderMap = {
                thin:'0px',
                medium:'1px',
                thick:'2px'
            },
            tableBorder, tdPadding, tdBorder, tmpValue;
        if (!table) {
            table = editor.document.createElement('table');
            table.insertRow(0).insertCell(0).innerHTML = 'xxx';
            editor.body.appendChild(table);
            var td = table.getElementsByTagName('td')[0];
            tmpValue = domUtils.getComputedStyle(table, 'border-left-width');
            tableBorder = parseInt(borderMap[tmpValue] || tmpValue, 10);
            tmpValue = domUtils.getComputedStyle(td, 'padding-left');
            tdPadding = parseInt(borderMap[tmpValue] || tmpValue, 10);
            tmpValue = domUtils.getComputedStyle(td, 'border-left-width');
            tdBorder = parseInt(borderMap[tmpValue] || tmpValue, 10);
            domUtils.remove(table);
            return {
                tableBorder:tableBorder,
                tdPadding:tdPadding,
                tdBorder:tdBorder
            };
        } else {
            td = table.getElementsByTagName('td')[0];
            tmpValue = domUtils.getComputedStyle(table, 'border-left-width');
            tableBorder = parseInt(borderMap[tmpValue] || tmpValue, 10);
            tmpValue = domUtils.getComputedStyle(td, 'padding-left');
            tdPadding = parseInt(borderMap[tmpValue] || tmpValue, 10);
            tmpValue = domUtils.getComputedStyle(td, 'border-left-width');
            tdBorder = parseInt(borderMap[tmpValue] || tmpValue, 10);
            return {
                tableBorder:tableBorder,
                tdPadding:tdPadding,
                tdBorder:tdBorder
            };
        }
    }

    UE.commands["adaptbytext"] =
        UE.commands["adaptbywindow"] = {
            queryCommandState:function () {
                return getTableItemsByRange(this).table ? 0 : -1
            },
            execCommand:function (cmd) {
                var tableItems = getTableItemsByRange(this),
                    table = tableItems.table;
                if (table) {
                    var tds = table.getElementsByTagName("td");
                    utils.each(tds, function (td) {
                        td.removeAttribute("width");
                    });
                    if (cmd == 'adaptbywindow') {
                        table.setAttribute('width', getTableWidth(this, needIEHack, getDefaultValue(this, table)));
                        utils.each(tds, function (td) {
                            td.setAttribute("width", td.offsetWidth + "");
                        });
                    } else {
                        table.style.width = "";
                        table.removeAttribute("width");

                        var defaultValue = getDefaultValue(me, table);
                        var width = table.offsetWidth,
                            bodyWidth = me.body.offsetWidth;
                        if (width > bodyWidth) {
                            table.setAttribute('width', getTableWidth(me, needIEHack, defaultValue));
                        }
                    }
                }
            }
        };

    //ƽ���������
    UE.commands['averagedistributecol'] = {
        queryCommandState:function () {
            var ut = getUETableBySelected(this);
            if (!ut) return -1;
            return ut.isFullRow() || ut.isFullCol() ? 0 : -1;
        },
        execCommand:function (cmd) {
            var me = this,
                ut = getUETableBySelected(me);

            function getAverageWidth() {
                var tb = ut.table,
                    averageWidth, sumWidth = 0, colsNum = 0,
                    tbAttr = getDefaultValue(me, tb);

                if (ut.isFullRow()) {
                    sumWidth = tb.offsetWidth;
                    colsNum = ut.colsNum;
                } else {
                    var begin = ut.cellsRange.beginColIndex,
                        end = ut.cellsRange.endColIndex,
                        node;
                    for (var i = begin; i <= end;) {
                        node = ut.selectedTds[i];
                        sumWidth += node.offsetWidth;
                        i += node.colSpan;
                        colsNum += 1;
                    }
                }
                averageWidth = Math.ceil(sumWidth / colsNum) - tbAttr.tdBorder * 2 - tbAttr.tdPadding * 2;
                return averageWidth;
            }

            function setAverageWidth(averageWidth) {
                utils.each(domUtils.getElementsByTagName(ut.table, "th"), function (node) {
                    node.setAttribute("width", "");
                });
                var cells = ut.isFullRow() ? domUtils.getElementsByTagName(ut.table, "td") : ut.selectedTds;

                utils.each(cells, function (node) {
                    if (node.colSpan == 1) {
                        node.setAttribute("width", averageWidth);
                    }
                });
            }

            if (ut && ut.selectedTds.length) {
                setAverageWidth(getAverageWidth());
            }
        }
    };
    //ƽ���������
    UE.commands['averagedistributerow'] = {
        queryCommandState:function () {
            var ut = getUETableBySelected(this);
            if (!ut) return -1;
            if (ut.selectedTds && /th/ig.test(ut.selectedTds[0].tagName)) return -1;
            return ut.isFullRow() || ut.isFullCol() ? 0 : -1;
        },
        execCommand:function (cmd) {
            var me = this,
                ut = getUETableBySelected(me);

            function getAverageHeight() {
                var averageHeight, rowNum, sumHeight = 0,
                    tb = ut.table,
                    tbAttr = getDefaultValue(me, tb),
                    tdpadding = parseInt(domUtils.getComputedStyle(tb.getElementsByTagName('td')[0], "padding-top"));

                if (ut.isFullCol()) {
                    var captionArr = domUtils.getElementsByTagName(tb, "caption"),
                        thArr = domUtils.getElementsByTagName(tb, "th"),
                        captionHeight, thHeight;

                    if (captionArr.length > 0) {
                        captionHeight = captionArr[0].offsetHeight;
                    }
                    if (thArr.length > 0) {
                        thHeight = thArr[0].offsetHeight;
                    }

                    sumHeight = tb.offsetHeight - (captionHeight || 0) - (thHeight || 0);
                    rowNum = thArr.length == 0 ? ut.rowsNum : (ut.rowsNum - 1);
                } else {
                    var begin = ut.cellsRange.beginRowIndex,
                        end = ut.cellsRange.endRowIndex,
                        count = 0,
                        trs = domUtils.getElementsByTagName(tb, "tr");
                    for (var i = begin; i <= end; i++) {
                        sumHeight += trs[i].offsetHeight;
                        count += 1;
                    }
                    rowNum = count;
                }
                //ie8���ǻ���ģʽ
                if (browser.ie && browser.version < 9) {
                    averageHeight = Math.ceil(sumHeight / rowNum);
                } else {
                    averageHeight = Math.ceil(sumHeight / rowNum) - tbAttr.tdBorder * 2 - tdpadding * 2;
                }
                return averageHeight;
            }

            function setAverageHeight(averageHeight) {
                var cells = ut.isFullCol() ? domUtils.getElementsByTagName(ut.table, "td") : ut.selectedTds;
                utils.each(cells, function (node) {
                    if (node.rowSpan == 1) {
                        node.setAttribute("height", averageHeight);
                    }
                });
            }

            if (ut && ut.selectedTds.length) {
                setAverageHeight(getAverageHeight());
            }
        }
    };

    //��Ԫ����뷽ʽ
    UE.commands['cellalignment'] = {
        queryCommandState:function () {
            return getTableItemsByRange(this).table ? 0 : -1
        },
        execCommand:function (cmd, data) {
            var me = this,
                ut = getUETableBySelected(me);

            if (!ut) {
                var start = me.selection.getStart(),
                    cell = start && domUtils.findParentByTagName(start, ["td", "th", "caption"], true);
                if (!/caption/ig.test(cell.tagName)) {
                    domUtils.setAttributes(cell, data);
                } else {
                    cell.style.textAlign = data.align;
                    cell.style.verticalAlign = data.vAlign;
                }
                me.selection.getRange().setCursor(true);
            } else {
                utils.each(ut.selectedTds, function (cell) {
                    domUtils.setAttributes(cell, data);
                });
            }
        }
    };
    //�����뷽ʽ
    UE.commands['tablealignment'] = {
        queryCommandState:function () {
            return getTableItemsByRange(this).table ? 0 : -1
        },
        execCommand:function (cmd, data) {
            var me = this,
                start = me.selection.getStart(),
                table = start && domUtils.findParentByTagName(start, ["table"], true);

            if (table) {
                var obj = {};
                obj[data[0]] = data[1];

                table.style[utils.cssStyleToDomStyle("float")] = "";
                table.style.margin = "";
                domUtils.setStyles(table, obj);
            }
        }
    };

    //�������
    UE.commands['edittable'] = {
        queryCommandState:function () {
            return getTableItemsByRange(this).table ? 0 : -1
        },
        execCommand:function (cmd, color) {
            var rng = this.selection.getRange(),
                table = domUtils.findParentByTagName(rng.startContainer, 'table');
            if (table) {
                var arr = domUtils.getElementsByTagName(table, "td").concat(
                    domUtils.getElementsByTagName(table, "th"),
                    domUtils.getElementsByTagName(table, "caption")
                );
                utils.each(arr, function (node) {
                    node.style.borderColor = color;
                });
            }
        }
    };
    //��Ԫ������
    UE.commands['edittd'] = {
        queryCommandState:function () {
            return getTableItemsByRange(this).table ? 0 : -1
        },
        execCommand:function (cmd, bkColor) {
            var me = this,
                ut = getUETableBySelected(me);

            if (!ut) {
                var start = me.selection.getStart(),
                    cell = start && domUtils.findParentByTagName(start, ["td", "th", "caption"], true);
                if (cell) {
                    cell.style.backgroundColor = bkColor;
                }
            } else {
                utils.each(ut.selectedTds, function (cell) {
                    cell.style.backgroundColor = bkColor;
                });
            }
        }
    };

    /**
     * UE��������
     * @param table
     * @constructor
     */
    function UETable(table) {
        this.table = table;
        this.indexTable = [];
        this.selectedTds = [];
        this.cellsRange = {};
        this.update(table);
    }

    UETable.prototype = {
        getMaxRows:function () {
            var rows = this.table.rows, maxLen = 1;
            for (var i = 0, row; row = rows[i]; i++) {
                var currentMax = 1;
                for (var j = 0, cj; cj = row.cells[j++];) {
                    currentMax = Math.max(cj.rowSpan || 1, currentMax);
                }
                maxLen = Math.max(currentMax + i, maxLen);
            }
            return maxLen;
        },
        /**
         * ��ȡ��ǰ�����������
         */
        getMaxCols:function () {
            var rows = this.table.rows, maxLen = 0, cellRows = {};
            for (var i = 0, row; row = rows[i]; i++) {
                var cellsNum = 0;
                for (var j = 0, cj; cj = row.cells[j++];) {
                    cellsNum += (cj.colSpan || 1);
                    if (cj.rowSpan && cj.rowSpan > 1) {
                        for (var k = 1; k < cj.rowSpan; k++) {
                            if (!cellRows['row_' + (i + k)]) {
                                cellRows['row_' + (i + k)] = (cj.colSpan || 1);
                            } else {
                                cellRows['row_' + (i + k)]++
                            }
                        }

                    }
                }
                cellsNum += cellRows['row_' + i] || 0;
                maxLen = Math.max(cellsNum, maxLen);
            }
            return maxLen;
        },
        getCellColIndex:function (cell) {

        },
        /**
         * ��ȡ��ǰcell�Աߵĵ�Ԫ��
         * @param cell
         * @param right
         */
        getHSideCell:function (cell, right) {
            try {
                var cellInfo = this.getCellInfo(cell),
                    previewRowIndex, previewColIndex;
                var len = this.selectedTds.length,
                    range = this.cellsRange;
                //���л�������û��ǰ�õ�Ԫ��
                if ((!right && (!len ? !cellInfo.colIndex : !range.beginColIndex)) || (right && (!len ? (cellInfo.colIndex == (this.colsNum - 1)) : (range.endColIndex == this.colsNum - 1)))) return null;

                previewRowIndex = !len ? cellInfo.rowIndex : range.beginRowIndex;
                previewColIndex = !right ? ( !len ? (cellInfo.colIndex < 1 ? 0 : (cellInfo.colIndex - 1)) : range.beginColIndex - 1)
                    : ( !len ? cellInfo.colIndex + 1 : range.endColIndex + 1);
                return this.getCell(this.indexTable[previewRowIndex][previewColIndex].rowIndex, this.indexTable[previewRowIndex][previewColIndex].cellIndex);
            } catch (e) {
                showError(e);
            }
        },
        getTabNextCell:function (cell, preRowIndex) {
            var cellInfo = this.getCellInfo(cell),
                rowIndex = preRowIndex || cellInfo.rowIndex,
                colIndex = cellInfo.colIndex + 1 + (cellInfo.colSpan - 1),
                nextCell;
            try {
                nextCell = this.getCell(this.indexTable[rowIndex][colIndex].rowIndex, this.indexTable[rowIndex][colIndex].cellIndex);
            } catch (e) {
                try {
                    rowIndex = rowIndex * 1 + 1;
                    colIndex = 0;
                    nextCell = this.getCell(this.indexTable[rowIndex][colIndex].rowIndex, this.indexTable[rowIndex][colIndex].cellIndex);
                } catch (e) {
                }
            }
            return nextCell;

        },
        /**
         * ��ȡ�Ӿ��ϵĺ��õ�Ԫ��
         * @param cell
         * @param bottom
         */
        getVSideCell:function (cell, bottom, ignoreRange) {
            try {
                var cellInfo = this.getCellInfo(cell),
                    nextRowIndex, nextColIndex;
                var len = this.selectedTds.length && !ignoreRange,
                    range = this.cellsRange;
                //ĩ�л���ĩ��û�к��õ�Ԫ��
                if ((!bottom && (cellInfo.rowIndex == 0)) || (bottom && (!len ? (cellInfo.rowIndex + cellInfo.rowSpan > this.rowsNum - 1) : (range.endRowIndex == this.rowsNum - 1)))) return null;

                nextRowIndex = !bottom ? ( !len ? cellInfo.rowIndex - 1 : range.beginRowIndex - 1)
                    : ( !len ? (cellInfo.rowIndex + cellInfo.rowSpan) : range.endRowIndex + 1);
                nextColIndex = !len ? cellInfo.colIndex : range.beginColIndex;
                return this.getCell(this.indexTable[nextRowIndex][nextColIndex].rowIndex, this.indexTable[nextRowIndex][nextColIndex].cellIndex);
            } catch (e) {
                showError(e);
            }
        },
        /**
         * ��ȡ��ͬ����λ�õĵ�Ԫ��xOrYָ�����ǻ�ȡx����ͬ����y����ͬ
         */
        getSameEndPosCells:function (cell, xOrY) {
            try {
                var flag = (xOrY.toLowerCase() === "x"),
                    end = domUtils.getXY(cell)[flag ? 'x' : 'y'] + cell["offset" + (flag ? 'Width' : 'Height')],
                    rows = this.table.rows,
                    cells = null, returns = [];
                for (var i = 0; i < this.rowsNum; i++) {
                    cells = rows[i].cells;
                    for (var j = 0, tmpCell; tmpCell = cells[j++];) {
                        var tmpEnd = domUtils.getXY(tmpCell)[flag ? 'x' : 'y'] + tmpCell["offset" + (flag ? 'Width' : 'Height')];
                        //��Ӧ�е�td�Ѿ���������rowSpan��
                        if (tmpEnd > end && flag) break;
                        if (cell == tmpCell || end == tmpEnd) {
                            //ֻ��ȡ��һ�ĵ�Ԫ��
                            //todo ����ȡ��һ��Ԫ�����ض�����»����returnsΪ�գ��Ӷ�Ӱ���������קʵ�֣�����������迼������
                            if (tmpCell[flag ? "colSpan" : "rowSpan"] == 1) {
                                returns.push(tmpCell);
                            }
                            if (flag) break;
                        }
                    }
                }
                return returns;
            } catch (e) {
                showError(e);
            }
        },
        setCellContent:function (cell, content) {
            cell.innerHTML = content || (browser.ie ? domUtils.fillChar : "&nbsp;");
        },
        cloneCell:cloneCell,
        /**
         * ��ȡ����ǰ��Ԫ����ұ�����Ϊ��ߵ�����δ�ϲ���Ԫ��
         */
        getSameStartPosXCells:function (cell) {
            try {
                var start = domUtils.getXY(cell).x + cell.offsetWidth,
                    rows = this.table.rows, cells , returns = [];
                for (var i = 0; i < this.rowsNum; i++) {
                    cells = rows[i].cells;
                    for (var j = 0, tmpCell; tmpCell = cells[j++];) {
                        var tmpStart = domUtils.getXY(tmpCell).x;
                        if (tmpStart > start) break;
                        if (tmpStart == start && tmpCell.colSpan == 1) {
                            returns.push(tmpCell);
                            break;
                        }
                    }
                }
                return returns;
            } catch (e) {
                showError(e);
            }
        },
        /**
         * ����table��Ӧ��������
         */
        update:function (table) {
            this.table = table || this.table;
            this.selectedTds = [];
            this.cellsRange = {};
            this.indexTable = [];
            var rows = this.table.rows,
            //��ʱ����rows Length,�Բ�ȱ�����ܴ������⣬
            //todo ���Կ���ȡ���ֵ
                rowsNum = rows.length,
                colsNum = this.getMaxCols();
            this.rowsNum = rowsNum;
            this.colsNum = colsNum;
            for (var i = 0, len = rows.length; i < len; i++) {
                this.indexTable[i] = new Array(colsNum);
            }
            //���������
            for (var rowIndex = 0, row; row = rows[rowIndex]; rowIndex++) {
                for (var cellIndex = 0, cell, cells = row.cells; cell = cells[cellIndex]; cellIndex++) {
                    //�������б�rowSpanʱ���µ������������
                    if (cell.rowSpan > rowsNum) {
                        cell.rowSpan = rowsNum;
                    }
                    var colIndex = cellIndex,
                        rowSpan = cell.rowSpan || 1,
                        colSpan = cell.colSpan || 1;
                    //���Ѿ�����һ��rowSpan���߱�ǰһ��colSpan�ˣ���������һ����Ԫ�����
                    while (this.indexTable[rowIndex][colIndex]) colIndex++;
                    for (var j = 0; j < rowSpan; j++) {
                        for (var k = 0; k < colSpan; k++) {
                            this.indexTable[rowIndex + j][colIndex + k] = {
                                rowIndex:rowIndex,
                                cellIndex:cellIndex,
                                colIndex:colIndex,
                                rowSpan:rowSpan,
                                colSpan:colSpan
                            }
                        }
                    }
                }
            }
            //�޸���ȱtd
            for (j = 0; j < rowsNum; j++) {
                for (k = 0; k < colsNum; k++) {
                    if (this.indexTable[j][k] === undefined) {
                        row = rows[j];
                        cell = row.cells[row.cells.length - 1];
                        cell = cell ? cell.cloneNode(true) : this.table.ownerDocument.createElement("td");
                        this.setCellContent(cell);
                        if (cell.colSpan !== 1)cell.colSpan = 1;
                        if (cell.rowSpan !== 1)cell.rowSpan = 1;
                        row.appendChild(cell);
                        this.indexTable[j][k] = {
                            rowIndex:j,
                            cellIndex:cell.cellIndex,
                            colIndex:k,
                            rowSpan:1,
                            colSpan:1
                        }
                    }
                }
            }
            //����ѡ��ɾ���л����к�������Ҫ�ؽ�ѡ����
            var tds = domUtils.getElementsByTagName(this.table, "td"),
                selectTds = [];
            utils.each(tds, function (td) {
                if (domUtils.hasClass(td, "selectTdClass")) {
                    selectTds.push(td);
                }
            });
            if (selectTds.length) {
                var start = selectTds[0],
                    end = selectTds[selectTds.length - 1],
                    startInfo = this.getCellInfo(start),
                    endInfo = this.getCellInfo(end);
                this.selectedTds = selectTds;
                this.cellsRange = {
                    beginRowIndex:startInfo.rowIndex,
                    beginColIndex:startInfo.colIndex,
                    endRowIndex:endInfo.rowIndex + endInfo.rowSpan - 1,
                    endColIndex:endInfo.colIndex + endInfo.colSpan - 1
                };
            }

        },
        /**
         * ��ȡ��Ԫ���������Ϣ
         */
        getCellInfo:function (cell) {
            if (!cell) return;
            var cellIndex = cell.cellIndex,
                rowIndex = cell.parentNode.rowIndex,
                rowInfo = this.indexTable[rowIndex],
                numCols = this.colsNum;
            for (var colIndex = cellIndex; colIndex < numCols; colIndex++) {
                var cellInfo = rowInfo[colIndex];
                if (cellInfo.rowIndex === rowIndex && cellInfo.cellIndex === cellIndex) {
                    return cellInfo;
                }
            }
        },
        /**
         * �������кŻ�ȡ��Ԫ��
         */
        getCell:function (rowIndex, cellIndex) {
            return rowIndex < this.rowsNum && this.table.rows[rowIndex].cells[cellIndex] || null;
        },
        /**
         * ɾ����Ԫ��
         */
        deleteCell:function (cell, rowIndex) {
            rowIndex = typeof rowIndex == 'number' ? rowIndex : cell.parentNode.rowIndex;
            var row = this.table.rows[rowIndex];
            row.deleteCell(cell.cellIndex);
        },
        /**
         * ����ʼĩ������Ԫ���ȡ����ѡ�����е�Ԫ��Χ
         */
        getCellsRange:function (cellA, cellB) {
            function checkRange(beginRowIndex, beginColIndex, endRowIndex, endColIndex) {
                var tmpBeginRowIndex = beginRowIndex,
                    tmpBeginColIndex = beginColIndex,
                    tmpEndRowIndex = endRowIndex,
                    tmpEndColIndex = endColIndex,
                    cellInfo, colIndex, rowIndex;
                // ͨ��indexTable����Ƿ���ڳ���TableRange�ϱ߽�����
                if (beginRowIndex > 0) {
                    for (colIndex = beginColIndex; colIndex < endColIndex; colIndex++) {
                        cellInfo = me.indexTable[beginRowIndex][colIndex];
                        rowIndex = cellInfo.rowIndex;
                        if (rowIndex < beginRowIndex) {
                            tmpBeginRowIndex = Math.min(rowIndex, tmpBeginRowIndex);
                        }
                    }
                }
                // ͨ��indexTable����Ƿ���ڳ���TableRange�ұ߽�����
                if (endColIndex < me.colsNum) {
                    for (rowIndex = beginRowIndex; rowIndex < endRowIndex; rowIndex++) {
                        cellInfo = me.indexTable[rowIndex][endColIndex];
                        colIndex = cellInfo.colIndex + cellInfo.colSpan - 1;
                        if (colIndex > endColIndex) {
                            tmpEndColIndex = Math.max(colIndex, tmpEndColIndex);
                        }
                    }
                }
                // ����Ƿ��г���TableRange�±߽�����
                if (endRowIndex < me.rowsNum) {
                    for (colIndex = beginColIndex; colIndex < endColIndex; colIndex++) {
                        cellInfo = me.indexTable[endRowIndex][colIndex];
                        rowIndex = cellInfo.rowIndex + cellInfo.rowSpan - 1;
                        if (rowIndex > endRowIndex) {
                            tmpEndRowIndex = Math.max(rowIndex, tmpEndRowIndex);
                        }
                    }
                }
                // ����Ƿ��г���TableRange��߽�����
                if (beginColIndex > 0) {
                    for (rowIndex = beginRowIndex; rowIndex < endRowIndex; rowIndex++) {
                        cellInfo = me.indexTable[rowIndex][beginColIndex];
                        colIndex = cellInfo.colIndex;
                        if (colIndex < beginColIndex) {
                            tmpBeginColIndex = Math.min(cellInfo.colIndex, tmpBeginColIndex);
                        }
                    }
                }
                //�ݹ����ֱ������������п�ѡ��Ԫ�����չ
                if (tmpBeginRowIndex != beginRowIndex || tmpBeginColIndex != beginColIndex || tmpEndRowIndex != endRowIndex || tmpEndColIndex != endColIndex) {
                    return checkRange(tmpBeginRowIndex, tmpBeginColIndex, tmpEndRowIndex, tmpEndColIndex);
                } else {
                    // ����Ҫ��չTableRange�����
                    return {
                        beginRowIndex:beginRowIndex,
                        beginColIndex:beginColIndex,
                        endRowIndex:endRowIndex,
                        endColIndex:endColIndex
                    };
                }
            }

            try {
                var me = this,
                    cellAInfo = me.getCellInfo(cellA);
                if (cellA === cellB) {
                    return {
                        beginRowIndex:cellAInfo.rowIndex,
                        beginColIndex:cellAInfo.colIndex,
                        endRowIndex:cellAInfo.rowIndex + cellAInfo.rowSpan - 1,
                        endColIndex:cellAInfo.colIndex + cellAInfo.colSpan - 1
                    };
                }
                var cellBInfo = me.getCellInfo(cellB);
                // ����TableRange���ĸ���
                var beginRowIndex = Math.min(cellAInfo.rowIndex, cellBInfo.rowIndex),
                    beginColIndex = Math.min(cellAInfo.colIndex, cellBInfo.colIndex),
                    endRowIndex = Math.max(cellAInfo.rowIndex + cellAInfo.rowSpan - 1, cellBInfo.rowIndex + cellBInfo.rowSpan - 1),
                    endColIndex = Math.max(cellAInfo.colIndex + cellAInfo.colSpan - 1, cellBInfo.colIndex + cellBInfo.colSpan - 1);

                return checkRange(beginRowIndex, beginColIndex, endRowIndex, endColIndex);


            } catch (e) {
                if (debug) throw e;
            }
        },
        /**
         * ����cellsRange��ȡ��Ӧ�ĵ�Ԫ�񼯺�
         */
        getCells:function (range) {
            //ÿ�λ�ȡcells֮ǰ����������ϴε�ѡ�񣬷����Ժ�����ȡ�������Ӱ��
            this.clearSelected();
            var beginRowIndex = range.beginRowIndex,
                beginColIndex = range.beginColIndex,
                endRowIndex = range.endRowIndex,
                endColIndex = range.endColIndex,
                cellInfo, rowIndex, colIndex, tdHash = {}, returnTds = [];
            for (var i = beginRowIndex; i <= endRowIndex; i++) {
                for (var j = beginColIndex; j <= endColIndex; j++) {
                    cellInfo = this.indexTable[i][j];
                    rowIndex = cellInfo.rowIndex;
                    colIndex = cellInfo.colIndex;
                    // ���Cells���Ѿ������˴�Cell������
                    var key = rowIndex + '|' + colIndex;
                    if (tdHash[key]) continue;
                    tdHash[key] = 1;
                    if (rowIndex < i || colIndex < j || rowIndex + cellInfo.rowSpan - 1 > endRowIndex || colIndex + cellInfo.colSpan - 1 > endColIndex) {
                        return null;
                    }
                    returnTds.push(this.getCell(rowIndex, cellInfo.cellIndex));
                }
            }
            return returnTds;
        },
        /**
         * �����Ѿ�ѡ�еĵ�Ԫ��
         */
        clearSelected:function () {
            removeSelectedClass(this.selectedTds);
            this.selectedTds = [];
            this.cellsRange = {};
        },
        /**
         * ����range�����Ѿ�ѡ�еĵ�Ԫ��
         */
        setSelected:function (range) {
            var cells = this.getCells(range);
            addSelectedClass(cells);
            this.selectedTds = cells;
            this.cellsRange = range;
        },
        isFullRow:function () {
            var range = this.cellsRange;
            return (range.endColIndex - range.beginColIndex + 1) == this.colsNum;
        },
        isFullCol:function () {
            var range = this.cellsRange,
                table = this.table,
                ths = table.getElementsByTagName("th"),
                rows = range.endRowIndex - range.beginRowIndex + 1;
            return  !ths.length ? rows == this.rowsNum : rows == this.rowsNum || (rows == this.rowsNum - 1);

        },
        /**
         * ��ȡ�Ӿ��ϵ�ǰ�õ�Ԫ��Ĭ������ߣ�top����ʱ
         * @param cell
         * @param top
         */
        getNextCell:function (cell, bottom, ignoreRange) {
            try {
                var cellInfo = this.getCellInfo(cell),
                    nextRowIndex, nextColIndex;
                var len = this.selectedTds.length && !ignoreRange,
                    range = this.cellsRange;
                //ĩ�л���ĩ��û�к��õ�Ԫ��
                if ((!bottom && (cellInfo.rowIndex == 0)) || (bottom && (!len ? (cellInfo.rowIndex + cellInfo.rowSpan > this.rowsNum - 1) : (range.endRowIndex == this.rowsNum - 1)))) return null;

                nextRowIndex = !bottom ? ( !len ? cellInfo.rowIndex - 1 : range.beginRowIndex - 1)
                    : ( !len ? (cellInfo.rowIndex + cellInfo.rowSpan) : range.endRowIndex + 1);
                nextColIndex = !len ? cellInfo.colIndex : range.beginColIndex;
                return this.getCell(this.indexTable[nextRowIndex][nextColIndex].rowIndex, this.indexTable[nextRowIndex][nextColIndex].cellIndex);
            } catch (e) {
                showError(e);
            }
        },
        getPreviewCell:function (cell, top) {
            try {
                var cellInfo = this.getCellInfo(cell),
                    previewRowIndex, previewColIndex;
                var len = this.selectedTds.length,
                    range = this.cellsRange;
                //���л�������û��ǰ�õ�Ԫ��
                if ((!top && (!len ? !cellInfo.colIndex : !range.beginColIndex)) || (top && (!len ? (cellInfo.rowIndex > (this.colsNum - 1)) : (range.endColIndex == this.colsNum - 1)))) return null;

                previewRowIndex = !top ? ( !len ? cellInfo.rowIndex : range.beginRowIndex )
                    : ( !len ? (cellInfo.rowIndex < 1 ? 0 : (cellInfo.rowIndex - 1)) : range.beginRowIndex);
                previewColIndex = !top ? ( !len ? (cellInfo.colIndex < 1 ? 0 : (cellInfo.colIndex - 1)) : range.beginColIndex - 1)
                    : ( !len ? cellInfo.colIndex : range.endColIndex + 1);
                return this.getCell(this.indexTable[previewRowIndex][previewColIndex].rowIndex, this.indexTable[previewRowIndex][previewColIndex].cellIndex);
            } catch (e) {
                showError(e);
            }
        },
        /**
         * �ƶ���Ԫ���е�����
         */
        moveContent:function (cellTo, cellFrom) {
            if (isEmptyBlock(cellFrom)) return;
            if (isEmptyBlock(cellTo)) {
                cellTo.innerHTML = cellFrom.innerHTML;
                return;
            }
            var child = cellTo.lastChild;
            if (child.nodeType == 3 || !dtd.$block[child.tagName]) {
                cellTo.appendChild(cellTo.ownerDocument.createElement('br'))
            }
            while (child = cellFrom.firstChild) {
                cellTo.appendChild(child);
            }
        },
        /**
         * ���Һϲ���Ԫ��
         */
        mergeRight:function (cell) {
            var cellInfo = this.getCellInfo(cell),
                rightColIndex = cellInfo.colIndex + cellInfo.colSpan,
                rightCellInfo = this.indexTable[cellInfo.rowIndex][rightColIndex],
                rightCell = this.getCell(rightCellInfo.rowIndex, rightCellInfo.cellIndex);
            //�ϲ�
            cell.colSpan = cellInfo.colSpan + rightCellInfo.colSpan;
            //���ϲ��ĵ�Ԫ��Ӧ���ڿ������
            cell.removeAttribute("width");
            //�ƶ�����
            this.moveContent(cell, rightCell);
            //ɾ�����ϲ���Cell
            this.deleteCell(rightCell, rightCellInfo.rowIndex);
            this.update();
        },
        /**
         * ���ºϲ���Ԫ��
         */
        mergeDown:function (cell) {
            var cellInfo = this.getCellInfo(cell),
                downRowIndex = cellInfo.rowIndex + cellInfo.rowSpan,
                downCellInfo = this.indexTable[downRowIndex][cellInfo.colIndex],
                downCell = this.getCell(downCellInfo.rowIndex, downCellInfo.cellIndex);
            cell.rowSpan = cellInfo.rowSpan + downCellInfo.rowSpan;
            cell.removeAttribute("height");
            this.moveContent(cell, downCell);
            this.deleteCell(downCell, downCellInfo.rowIndex);
            this.update();
        },
        /**
         * �ϲ�����range�е�����
         */
        mergeRange:function () {
            //���ںϲ���������������ʱ�̽��У������޷�ͨ�����λ�õ���Ϣʵʱ����range��ֻ��ͨ������ʵ���е�cellsRange����������
            var range = this.cellsRange,
                leftTopCell = this.getCell(range.beginRowIndex, this.indexTable[range.beginRowIndex][range.beginColIndex].cellIndex);
            if (leftTopCell.tagName == "TH" && range.endRowIndex !== range.beginRowIndex) {
                var index = this.indexTable,
                    info = this.getCellInfo(leftTopCell);
                leftTopCell = this.getCell(1, index[1][info.colIndex].cellIndex);
                range = this.getCellsRange(leftTopCell, this.getCell(index[this.rowsNum - 1][info.colIndex].rowIndex, index[this.rowsNum - 1][info.colIndex].cellIndex));
            }

            // ɾ��ʣ���Cells
            var cells = this.getCells(range),
                len = cells.length, cell;
            while (len--) {
                cell = cells[len];
                if (cell !== leftTopCell) {
                    this.moveContent(leftTopCell, cell);
                    this.deleteCell(cell);
                }
            }
            // �޸����Ͻ�Cell��rowSpan��colSpan�������������������
            leftTopCell.rowSpan = range.endRowIndex - range.beginRowIndex + 1;
            leftTopCell.rowSpan > 1 && leftTopCell.removeAttribute("height");
            leftTopCell.colSpan = range.endColIndex - range.beginColIndex + 1;
            leftTopCell.colSpan > 1 && leftTopCell.removeAttribute("width");
            if (leftTopCell.rowSpan == this.rowsNum && leftTopCell.colSpan != 1) {
                leftTopCell.colSpan = 1;
            }
            if (leftTopCell.colSpan == this.colsNum && leftTopCell.rowSpan != 1) {
                var rowIndex = leftTopCell.parentNode.rowIndex;
                for (var i = 0; i < leftTopCell.rowSpan - 1; i++) {
                    var row = this.table.rows[rowIndex + 1];
                    row.parentNode.removeChild(row);
                }
                leftTopCell.rowSpan = 1;
            }
            this.update();
        },
        /**
         * ����һ�е�Ԫ��
         */
        insertRow:function (rowIndex, sourceCell) {
            var numCols = this.colsNum,
                table = this.table,
                row = table.insertRow(rowIndex), cell,
                width = parseInt((table.offsetWidth - numCols * 20 - numCols - 1) / numCols, 10);
            //����ֱ�Ӳ���,���迼�ǲ��ֵ�Ԫ��rowspan�����
            if (rowIndex == 0 || rowIndex == this.rowsNum) {
                for (var colIndex = 0; colIndex < numCols; colIndex++) {
                    cell = this.cloneCell(sourceCell, true);
                    this.setCellContent(cell);
                    cell.getAttribute('vAlign') && cell.setAttribute('vAlign', cell.getAttribute('vAlign'));
                    row.appendChild(cell);
                }
            } else {
                var infoRow = this.indexTable[rowIndex],
                    cellIndex = 0;
                for (colIndex = 0; colIndex < numCols; colIndex++) {
                    var cellInfo = infoRow[colIndex];
                    //�������ĳ����Ԫ���rowspan�����������е�λ�ã����޸ĸõ�Ԫ���rowspan���ɣ�������뵥Ԫ��
                    if (cellInfo.rowIndex < rowIndex) {
                        cell = this.getCell(cellInfo.rowIndex, cellInfo.cellIndex);
                        cell.rowSpan = cellInfo.rowSpan + 1;
                    } else {
                        cell = this.cloneCell(sourceCell, true);
                        this.setCellContent(cell);
                        row.appendChild(cell);
                    }
                }
            }
            //��ѡʱ���벻����contentchange����Ҫ�ֶ�����������
            this.update();
            return row;
        },
        /**
         * ɾ��һ�е�Ԫ��
         * @param rowIndex
         */
        deleteRow:function (rowIndex) {
            var row = this.table.rows[rowIndex],
                infoRow = this.indexTable[rowIndex],
                colsNum = this.colsNum,
                count = 0;     //�������
            for (var colIndex = 0; colIndex < colsNum;) {
                var cellInfo = infoRow[colIndex],
                    cell = this.getCell(cellInfo.rowIndex, cellInfo.cellIndex);
                if (cell.rowSpan > 1) {
                    if (cellInfo.rowIndex == rowIndex) {
                        var clone = cell.cloneNode(true);
                        clone.rowSpan = cell.rowSpan - 1;
                        clone.innerHTML = "";
                        cell.rowSpan = 1;
                        var nextRowIndex = rowIndex + 1,
                            nextRow = this.table.rows[nextRowIndex],
                            insertCellIndex,
                            preMerged = this.getPreviewMergedCellsNum(nextRowIndex, colIndex) - count;
                        if (preMerged < colIndex) {
                            insertCellIndex = colIndex - preMerged - 1;
                            //nextRow.insertCell(insertCellIndex);
                            domUtils.insertAfter(nextRow.cells[insertCellIndex], clone);
                        } else {
                            if (nextRow.cells.length) nextRow.insertBefore(clone, nextRow.cells[0])
                        }
                        count += 1;
                        //cell.parentNode.removeChild(cell);
                    }
                }
                colIndex += cell.colSpan || 1;
            }
            var deleteTds = [], cacheMap = {};
            for (colIndex = 0; colIndex < colsNum; colIndex++) {
                var tmpRowIndex = infoRow[colIndex].rowIndex,
                    tmpCellIndex = infoRow[colIndex].cellIndex,
                    key = tmpRowIndex + "_" + tmpCellIndex;
                if (cacheMap[key])continue;
                cacheMap[key] = 1;
                cell = this.getCell(tmpRowIndex, tmpCellIndex);
                deleteTds.push(cell);
            }
            var mergeTds = [];
            utils.each(deleteTds, function (td) {
                if (td.rowSpan == 1) {
                    td.parentNode.removeChild(td);
                } else {
                    mergeTds.push(td);
                }
            });
            utils.each(mergeTds, function (td) {
                td.rowSpan--;
            });
            row.parentNode.removeChild(row);
            //����������������bug,�����Զ��巽��ɾ��
            //this.table.deleteRow(rowIndex);
            this.update();
        },
        insertCol:function (colIndex, sourceCell, defaultValue) {
            var rowsNum = this.rowsNum,
                rowIndex = 0,
                tableRow, cell,
                backWidth = parseInt((this.table.offsetWidth - (this.colsNum + 1) * 20 - (this.colsNum + 1)) / (this.colsNum + 1), 10);

            function replaceTdToTh(rowIndex, cell, tableRow) {
                if (rowIndex == 0) {
                    var th = cell.nextSibling || cell.previousSibling;
                    if (th.tagName == 'TH') {
                        th = cell.ownerDocument.createElement("th");
                        th.appendChild(cell.firstChild);
                        tableRow.insertBefore(th, cell);
                        domUtils.remove(cell)
                    }
                }
            }

            var preCell;
            if (colIndex == 0 || colIndex == this.colsNum) {
                for (; rowIndex < rowsNum; rowIndex++) {
                    tableRow = this.table.rows[rowIndex];
                    preCell = tableRow.cells[colIndex == 0 ? colIndex : tableRow.cells.length];
                    cell = this.cloneCell(sourceCell, true); //tableRow.insertCell(colIndex == 0 ? colIndex : tableRow.cells.length);
                    this.setCellContent(cell);
                    cell.setAttribute('vAlign', cell.getAttribute('vAlign'));
                    preCell && cell.setAttribute('width', preCell.getAttribute('width'));
                    if (!colIndex) {
                        tableRow.insertBefore(cell, tableRow.cells[0]);
                    } else {
                        domUtils.insertAfter(tableRow.cells[tableRow.cells.length - 1], cell);
                    }
                    replaceTdToTh(rowIndex, cell, tableRow)
                }
            } else {
                for (; rowIndex < rowsNum; rowIndex++) {
                    var cellInfo = this.indexTable[rowIndex][colIndex];
                    if (cellInfo.colIndex < colIndex) {
                        cell = this.getCell(cellInfo.rowIndex, cellInfo.cellIndex);
                        cell.colSpan = cellInfo.colSpan + 1;
                    } else {
                        tableRow = this.table.rows[rowIndex];
                        preCell = tableRow.cells[cellInfo.cellIndex];

                        cell = this.cloneCell(sourceCell, true);//tableRow.insertCell(cellInfo.cellIndex);
                        this.setCellContent(cell);
                        cell.setAttribute('vAlign', cell.getAttribute('vAlign'));
                        preCell && cell.setAttribute('width', preCell.getAttribute('width'))
                        tableRow.insertBefore(cell, preCell);
                    }
                    replaceTdToTh(rowIndex, cell, tableRow);
                }
            }
            //��ѡʱ���벻����contentchange����Ҫ�ֶ���������
            this.update();
            this.updateWidth(backWidth, defaultValue || {tdPadding:10, tdBorder:1});
        },
        updateWidth:function (width, defaultValue) {
            var table = this.table,
                tmpWidth = getWidth(table) - defaultValue.tdPadding * 2 - defaultValue.tdBorder + width;
            if (tmpWidth < table.ownerDocument.body.offsetWidth) {
                table.setAttribute("width", tmpWidth);
                return;
            }
            var tds = domUtils.getElementsByTagName(this.table, "td");
            utils.each(tds, function (td) {
                td.setAttribute("width", width);
            })
        },
        deleteCol:function (colIndex) {
            var indexTable = this.indexTable,
                tableRows = this.table.rows,
                backTableWidth = this.table.getAttribute("width"),
                backTdWidth = 0,
                rowsNum = this.rowsNum,
                cacheMap = {};
            for (var rowIndex = 0; rowIndex < rowsNum;) {
                var infoRow = indexTable[rowIndex],
                    cellInfo = infoRow[colIndex],
                    key = cellInfo.rowIndex + '_' + cellInfo.colIndex;
                // �����Ѿ��������Cell
                if (cacheMap[key])continue;
                cacheMap[key] = 1;
                var cell = this.getCell(cellInfo.rowIndex, cellInfo.cellIndex);
                if (!backTdWidth) backTdWidth = cell && parseInt(cell.offsetWidth / cell.colSpan, 10).toFixed(0);
                // ���Cell��colSpan����1, ���޸�colSpan, �����ɾ�����Cell
                if (cell.colSpan > 1) {
                    cell.colSpan--;
                } else {
                    tableRows[rowIndex].deleteCell(cellInfo.cellIndex);
                }
                rowIndex += cellInfo.rowSpan || 1;
            }
            this.table.setAttribute("width", backTableWidth - backTdWidth);
            this.update();
        },
        splitToCells:function (cell) {
            var me = this,
                cells = this.splitToRows(cell);
            utils.each(cells, function (cell) {
                me.splitToCols(cell);
            })
        },
        splitToRows:function (cell) {
            var cellInfo = this.getCellInfo(cell),
                rowIndex = cellInfo.rowIndex,
                colIndex = cellInfo.colIndex,
                results = [];
            // �޸�Cell��rowSpan
            cell.rowSpan = 1;
            results.push(cell);
            // ���뵥Ԫ��
            for (var i = rowIndex, endRow = rowIndex + cellInfo.rowSpan; i < endRow; i++) {
                if (i == rowIndex)continue;
                var tableRow = this.table.rows[i],
                    tmpCell = tableRow.insertCell(colIndex - this.getPreviewMergedCellsNum(i, colIndex));
                tmpCell.colSpan = cellInfo.colSpan;
                this.setCellContent(tmpCell);
                tmpCell.setAttribute('vAlign', cell.getAttribute('vAlign'));
                tmpCell.setAttribute('align', cell.getAttribute('align'));
                if (cell.style.cssText) {
                    tmpCell.style.cssText = cell.style.cssText;
                }
                results.push(tmpCell);
            }
            this.update();
            return results;
        },
        getPreviewMergedCellsNum:function (rowIndex, colIndex) {
            var indexRow = this.indexTable[rowIndex],
                num = 0;
            for (var i = 0; i < colIndex;) {
                var colSpan = indexRow[i].colSpan,
                    tmpRowIndex = indexRow[i].rowIndex;
                num += (colSpan - (tmpRowIndex == rowIndex ? 1 : 0));
                i += colSpan;
            }
            return num;
        },
        splitToCols:function (cell) {
            var backWidth = (cell.offsetWidth / cell.colSpan - 22).toFixed(0),

                cellInfo = this.getCellInfo(cell),
                rowIndex = cellInfo.rowIndex,
                colIndex = cellInfo.colIndex,
                results = [];
            // �޸�Cell��rowSpan
            cell.colSpan = 1;
            cell.setAttribute("width", backWidth);
            results.push(cell);
            // ���뵥Ԫ��
            for (var j = colIndex, endCol = colIndex + cellInfo.colSpan; j < endCol; j++) {
                if (j == colIndex)continue;
                var tableRow = this.table.rows[rowIndex],
                    tmpCell = tableRow.insertCell(this.indexTable[rowIndex][j].cellIndex + 1);
                tmpCell.rowSpan = cellInfo.rowSpan;
                this.setCellContent(tmpCell);
                tmpCell.setAttribute('vAlign', cell.getAttribute('vAlign'));
                tmpCell.setAttribute('align', cell.getAttribute('align'));
                tmpCell.setAttribute('width', backWidth);
                if (cell.style.cssText) {
                    tmpCell.style.cssText = cell.style.cssText;
                }
                //����th�����
                if (cell.tagName == 'TH') {
                    var th = cell.ownerDocument.createElement('th');
                    th.appendChild(tmpCell.firstChild);
                    th.setAttribute('vAlign', cell.getAttribute('vAlign'));
                    th.rowSpan = tmpCell.rowSpan;
                    tableRow.insertBefore(th, tmpCell);
                    domUtils.remove(tmpCell);
                }
                results.push(tmpCell);
            }
            this.update();
            return results;
        },
        isLastCell:function (cell, rowsNum, colsNum) {
            rowsNum = rowsNum || this.rowsNum;
            colsNum = colsNum || this.colsNum;
            var cellInfo = this.getCellInfo(cell);
            return ((cellInfo.rowIndex + cellInfo.rowSpan) == rowsNum) &&
                ((cellInfo.colIndex + cellInfo.colSpan) == colsNum);
        },
        getLastCell:function (cells) {
            cells = cells || this.table.getElementsByTagName("td");
            var firstInfo = this.getCellInfo(cells[0]);
            var me = this, last = cells[0],
                tr = last.parentNode,
                cellsNum = 0, cols = 0, rows;
            utils.each(cells, function (cell) {
                if (cell.parentNode == tr)cols += cell.colSpan || 1;
                cellsNum += cell.rowSpan * cell.colSpan || 1;
            });
            rows = cellsNum / cols;
            utils.each(cells, function (cell) {
                if (me.isLastCell(cell, rows, cols)) {
                    last = cell;
                    return false;
                }
            });
            return last;

        },
        selectRow:function (rowIndex) {
            var indexRow = this.indexTable[rowIndex],
                start = this.getCell(indexRow[0].rowIndex, indexRow[0].cellIndex),
                end = this.getCell(indexRow[this.colsNum - 1].rowIndex, indexRow[this.colsNum - 1].cellIndex),
                range = this.getCellsRange(start, end);
            this.setSelected(range);
        },
        selectTable:function () {
            var tds = this.table.getElementsByTagName("td"),
                range = this.getCellsRange(tds[0], tds[tds.length - 1]);
            this.setSelected(range);
        }
    };


    function getSelectedArr(editor) {
        var ut = getTableItemsByRange(editor).cell || getUETableBySelected(editor);
        return ut ? (ut.nodeType ? [ut] : ut.selectedTds) : [];
    }

    /**
     * ���ݵ�ǰѡ����ȡ��ص�table��Ϣ
     * @return {Object}
     */
    function getTableItemsByRange(editor) {
        var start = editor.selection.getStart(),
        //��table����td��Ե�п��ܴ���ѡ��tr�����
            cell = start && domUtils.findParentByTagName(start, ["td", "th"], true),
            tr = cell && cell.parentNode,
            caption = start && domUtils.findParentByTagName(start, 'caption', true),
            table = caption ? caption.parentNode : tr && tr.parentNode.parentNode;

        return {
            cell:cell,
            tr:tr,
            table:table,
            caption:caption
        }
    }

    /**
     * ��ȡ��Ҫ������Ӧ�������move�¼���td����
     * @param evt
     */
    function getTargetTd(editor, evt) {
        var target = domUtils.findParentByTagName(evt.target || evt.srcElement, ["td", "th"], true);
        //�ų��˷�td�ڲ��Լ����ڴ���������ֵ�td
        return target && !(editor.fireEvent("excludetable", target) === true) ? target : null;
    }

    function cloneCell(cell, ingoreMerge) {
        if (!cell || utils.isString(cell)) {
            return this.table.ownerDocument.createElement(cell || 'td');
        }
        var flag = domUtils.hasClass(cell, "selectTdClass");
        flag && domUtils.removeClasses(cell, "selectTdClass");
        var tmpCell = cell.cloneNode(true);
        if (ingoreMerge) {
            tmpCell.rowSpan = tmpCell.colSpan = 1;
        }
        tmpCell.style.borderLeftStyle = "";
        tmpCell.style.borderTopStyle = "";
        tmpCell.style.borderLeftColor = cell.style.borderRightColor;
        tmpCell.style.borderLeftWidth = cell.style.borderRightWidth;
        tmpCell.style.borderTopColor = cell.style.borderBottomColor;
        tmpCell.style.borderTopWidth = cell.style.borderBottomWidth;
        flag && domUtils.addClass(cell, "selectTdClass");
        return tmpCell;
    }
};
