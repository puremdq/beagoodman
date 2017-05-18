/*生成随机给定长度字符串*/
function randomString(len) {

    len = len || 32;

    var timestamp = Date.parse(new Date());
    timestamp = timestamp / 1000;
    len = len - (timestamp.toString().length);

    len = len < 10 ? 10 : len;

    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz_';
    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
    var maxPos = $chars.length;
    var str = '';
    for (var i = 0; i < len; i++) {
        str += $chars.charAt(Math.floor(Math.random() * maxPos));
    }

    return str + "_" + timestamp;
}


/*得到对象内容长度*/
function getObjLength(selector) {

    var str;
    if (typeof (selector) == 'string') {

        str = selector;
    } else {

        str = $(selector).val();

    }
    str = $.trim(str);
    return str.length;
}


/*更好地调用 console.log()*/

function printLog(data) {
    console.log(data);
}


//state >0表示积极的消息  <0表示消极的消息  0正常
function alertMessage(msg, state) {
    var icon;

    if (state == undefined) {

        state = 0;
    }

    if (state == 0) {
        layer.msg(msg, {time: 2000});
    } else {
        if (state >= 1) {
            icon = 1
        } else {
            icon = 5
        }
        layer.msg(msg, {
            "icon": icon,
            'time': 2000
        });

    }

}


/*自己写的弹出提示信息*/
function doAlert(msg, stay, level) {


    /*<div class="alert alert-success">成功！很好地完成了提交。</div>
     <div class="alert alert-info">信息！请注意这个信息。</div>
     <div class="alert alert-warning">警告！请不要提交。</div>
     <div class="alert alert-danger">错误！请进行一些更改。</div*/

    if (!arguments[1]) {
        stay = 1000;
    }

    if (!arguments[2]) {
        level = 1;
    } else {

        level = parseInt(level);
    }


    var alertLevelClass;
    switch (level) {

        case 0:
            alertLevelClass = 'alert-success';
            break;
        case 1:
            alertLevelClass = 'alert-info';
            break;
        case 2:
            alertLevelClass = 'alert-warning';
            break;
        case 3:
            alertLevelClass = 'alert-danger';
            break;
    }

    var width = document.body.clientWidth / 2 - 50;
    var height = 80;
    var html = '<div class="alert ' + alertLevelClass + '" id="alertDiv" hidden ' +
        'style="text-align: center;position: fixed;bottom: ' + height + 'px;left:' + width + 'px">' +
        msg + '</div>';

    //console.log(html);

    $("body").append(html);
    var obj = $("#alertDiv");
    obj.show(100);
    setTimeout(
        function () {
            obj.hide(10, function () {
                obj.remove();
            });

        }, stay);

}

/*得到日期的时间戳*/
function getTimestamp(date) {

    if (date == undefined) {

        date = new Date();
    }

    return Date.parse(date) / 1000;
}


/*将时间戳转为日期*/
function formatDate(timestamp, separator) {

    if (timestamp == undefined) {
        timestamp = getTimestamp();

    }

    if (separator == undefined) {
        separator = '.'

    }

    var d = new Date(parseInt(timestamp) * 1000);

    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var date = d.getDate();
    var hour = d.getHours();
    var minute = d.getMinutes();
    return year + separator + month + separator + date + "  " + hour + ":" + minute;
}


/*将时间戳转化为常用的日期格式*/
function getShowTime(timestamp) {

    var showTime;
    var difference;

    difference = getTimestamp() - timestamp;

    if (difference <= 60) {

        showTime = '刚刚';

    } else if (difference <= 60 * 60) {

        showTime = parseInt(difference / 60) + '分钟前';


    } else if (difference <= 12 * 60 * 60) {

        showTime = parseInt(difference / 3600) + '小时前';


    } else {
        showTime = formatDate(timestamp);

    }

    return showTime;
}


/*解析某个元素上的时间
 * selector jquery 对象 或者 选择器
 * */
function analyticShowTime(selector) {

    var timestamp = parseInt($(selector).attr('data-shared-at'));

    if (timestamp) {

        $(selector).html(getShowTime(timestamp));

    }

}


/*解析某个元素的表情*/


function analyticTargetEmotion(selector) {

    var interval = setInterval(function () {

        if (isHashTableComplete === true) {

            $(selector).html(AnalyticEmotion($(selector).html()));
            clearInterval(interval);

        }

    }, 500);

}
/*截取字符串 从 某个 字符第一次出现 后一个字符 到字符串结束*/
function getAfterStr(str, separator) {
    return str.substring(str.indexOf(separator) + separator.length);//得到id
}


/*判断是否为整数*/

function isInteger(obj) {

    return parseInt(obj).toString() === obj.toString()


}


/*检查用户是否登录  登录返回true 否则返回false 并弹出登录框*/

function isLogin(isShowLoginModal) {

    isShowLoginModal = (isShowLoginModal === undefined) ? true : isShowLoginModal;

    var loginState = true;

    if ($("#reg_login_modal").length >= 1) {

        loginState = false;

        if (isShowLoginModal) {

            $("#for-login").click();

        }

    }

    return loginState;


}


/*重新设置num的值
 *@param targetSelector  selector|domObj
 *@param valOrHtml  String 'val'|'html'
 *@param modified  String 'add'|'sub'
 * */
function resetNum(targetSelector, valOrHtml, modified) {

    var numVal;
    numVal = (valOrHtml === 'val') ? targetSelector.val() : targetSelector.html();
    numVal = isInteger(numVal) ? parseInt(numVal) : 0;

    if (modified === 'add') {

        numVal++;
    } else {

        numVal--;

    }
    (valOrHtml === 'val') ? targetSelector.val(numVal) : targetSelector.html(numVal);
}


/**
 *@param selector   DOM|string  要进行提示的元素
 *@param msg        string            提示信息
 *@param location   int      1从上面 提示  2 右边 3下 4右
 */
function startTips(selector, msg, location) {


    location = location === undefined ? 3 : parseInt(location);

    location = (location > 0 && location < 5) ? location : 3;

    var layerTip;

    $(selector).on('mouseenter', function () {

        layerTip = layer.tips(msg, selector, {
            tips: location
        });

    });

    $(selector).on('mouseleave', function () {

        layer.close(layerTip);

    });

}


/*查看当前原始是否在屏幕可见范围*/
function isElementInViewport(el) {
    var rect = el.getBoundingClientRect();//返回某个元素在视窗中的位置，包括left、right、top、bottom、width、height
    return (
        rect.top >= 0 && rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
}


function unserialize(data) {
    //  discuss at: http://locutus.io/php/unserialize/
    // original by: Arpad Ray (mailto:arpad@php.net)
    // improved by: Pedro Tainha (http://www.pedrotainha.com)
    // improved by: Kevin van Zonneveld (http://kvz.io)
    // improved by: Kevin van Zonneveld (http://kvz.io)
    // improved by: Chris
    // improved by: James
    // improved by: Le Torbi
    // improved by: Eli Skeggs
    // bugfixed by: dptr1988
    // bugfixed by: Kevin van Zonneveld (http://kvz.io)
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    // bugfixed by: philippsimon (https://github.com/philippsimon/)
    //  revised by: d3x
    //    input by: Brett Zamir (http://brett-zamir.me)
    //    input by: Martin (http://www.erlenwiese.de/)
    //    input by: kilops
    //    input by: Jaroslaw Czarniak
    //    input by: lovasoa (https://github.com/lovasoa/)
    //      note 1: We feel the main purpose of this function should be
    //      note 1: to ease the transport of data between php & js
    //      note 1: Aiming for PHP-compatibility, we have to translate objects to arrays
    //   example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}')
    //   returns 1: ['Kevin', 'van', 'Zonneveld']
    //   example 2: unserialize('a:2:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";}')
    //   returns 2: {firstName: 'Kevin', midName: 'van'}
    //   example 3: unserialize('a:3:{s:2:"ü";s:2:"ü";s:3:"四";s:3:"四";s:4:"𠜎";s:4:"𠜎";}')
    //   returns 3: {'ü': 'ü', '四': '四', '𠜎': '𠜎'}

    var $global = (typeof window !== 'undefined' ? window : global);

    var utf8Overhead = function (str) {
        var s = str.length;
        for (var i = str.length - 1; i >= 0; i--) {
            var code = str.charCodeAt(i);
            if (code > 0x7f && code <= 0x7ff) {
                s++
            } else if (code > 0x7ff && code <= 0xffff) {
                s += 2
            }
            // trail surrogate
            if (code >= 0xDC00 && code <= 0xDFFF) {
                i--;
            }
        }
        return s - 1
    }
    var error = function (type,
                          msg, filename, line) {
        throw new $global[type](msg, filename, line);
    }
    var readUntil = function (data, offset, stopchr) {
        var i = 2;
        var buf = [];
        var chr = data.slice(offset, offset + 1);

        while (chr !== stopchr) {
            if ((i + offset) > data.length) {
                error('Error', 'Invalid');
            }
            buf.push(chr);
            chr = data.slice(offset + (i - 1), offset + i);
            i += 1
        }
        return [buf.length, buf.join('')];
    }
    var readChrs = function (data, offset, length) {
        var i, chr, buf;

        buf = [];
        for (i = 0; i < length; i++) {
            chr = data.slice(offset + (i - 1), offset + i);
            buf.push(chr);
            length -= utf8Overhead(chr);
        }
        return [buf.length, buf.join('')];
    }

    function _unserialize(data, offset) {
        var dtype;
        var dataoffset;
        var keyandchrs;
        var keys;
        var contig;
        var length;
        var array;
        var readdata;
        var readData;
        var ccount;
        var stringlength;
        var i;
        var key;
        var kprops;
        var kchrs;
        var vprops;
        var vchrs;
        var value;
        var chrs = 0;
        var typeconvert = function (x) {
            return x
        };

        if (!offset) {
            offset = 0
        }
        dtype = (data.slice(offset, offset + 1)).toLowerCase();

        dataoffset = offset + 2;

        switch (dtype) {
            case 'i':
                typeconvert = function (x) {
                    return parseInt(x, 10)
                };
                readData = readUntil(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
                break;
            case 'b':
                typeconvert = function (x) {
                    return parseInt(x, 10) !== 0
                }
                readData = readUntil(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
                break;
            case 'd':
                typeconvert = function (x) {
                    return parseFloat(x)
                };
                readData = readUntil(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
                break;
            case 'n':
                readdata = null;
                break;
            case 's':
                ccount = readUntil(data, dataoffset, ':');
                chrs = ccount[0];
                stringlength = ccount[1];
                dataoffset += chrs + 2;

                readData = readChrs(data, dataoffset + 1, parseInt(stringlength, 10));
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 2;
                if (chrs !== parseInt(stringlength, 10) && chrs !== readdata.length) {
                    error('SyntaxError', 'String length mismatch');
                }
                break;
            case 'a':
                readdata = {}

                keyandchrs = readUntil(data, dataoffset, ':')
                chrs = keyandchrs[0];
                keys = keyandchrs[1];
                dataoffset += chrs + 2;
                length = parseInt(keys, 10);
                contig = true;

                for (i = 0; i < length; i++) {
                    kprops = _unserialize(data, dataoffset);
                    kchrs = kprops[1];
                    key = kprops[2];
                    dataoffset += kchrs;

                    vprops = _unserialize(data, dataoffset);
                    vchrs = vprops[1];
                    value = vprops[2];
                    dataoffset += vchrs;

                    if (key !== i) {
                        contig = false;
                    }

                    readdata[key] = value;
                }

                if (contig) {
                    array = new Array(length);
                    for (i = 0; i < length; i++) {
                        array[i] = readdata[i];
                    }
                    readdata = array;
                }

                dataoffset += 1;
                break;
            default:
                error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
                break
        }
        return [dtype, dataoffset - offset, typeconvert(readdata)];
    }

    return _unserialize((data + ''), 0)[2];
}


function toInt(val) {

    if(val===undefined){

        return 0;
    }else {

        return parseInt(val);
    }

}

function HTMLEncode(html) {
    var temp = document.createElement("div");
    (temp.textContent != null) ? (temp.textContent = html) : (temp.innerText = html);
    var output = temp.innerHTML;
    temp = null;
    return output;
}