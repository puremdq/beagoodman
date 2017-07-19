<!doctype html>
<html lang="en">

<link rel="stylesheet" type="text/css" href="lib/wangEditor/css/wangEditor.min.css">


<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>新文章</title>

    <style>
        a {

            text-decoration: none;
        }

        #title {
            width: 100%;
            height: 50px;
            margin: 10px auto;
            outline: none;
            border: 0;
            border-bottom: 1px solid #ccc;
            font-size: 20px;
            font-family: "Songti SC";
            color: #111;
            font-weight: 900;
            text-align: center;
        }

        .nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #f8f8f8;

            min-height: 60px;
        }

        .nav div {

            margin: 15px 50px;
        }

        .logo {

            color: green;
            font-size: large;
        }

        .btn {
            background-color: orange;
            padding: 5px 20px;
            border-radius: 5px;
            color: white;
            margin: auto 15px;
        }

        #sub_btn {

            background-color: #04b03c;

        }
        .write-content{
            margin-top:60px ;
        }



    </style>

</head>
<body>

<div class="nav">

    <div style="float: left">
        <a class="logo" href="{{url('')}}">beagoodman</a>
        <span style="margin-left: 10px;color: #bd3a55;">新文章</span>
    </div>

    <div style="float: right" class="btns">
        <a class="btn" id="clear_btn" href="javascript:void (0);">清空</a>
        <a class="btn" id="sub_btn" href="javascript:void (0);">发表</a>
    </div>

</div>

<div class="write-content" style="width: 80%;margin: 50px auto">

    <form method="post" action="{{url('dynamic')}}" id="article_form">

        {{csrf_field()}}
        <input type="text" hidden name="dynamic_type" value="article">
        <input type="text" hidden name="img_keys" id="img_keys">
        <input type="text" hidden id="first_img_url" name="first_img_url" value="">


        <input id="title" type="text" name="article_title" placeholder="请输入标题">
        <textarea hidden name="article_content" id="article_content"></textarea>


        <div id="div1" style="height: 650px">
            <p>请输入内容...</p>
        </div>
    </form>

</div>
</body>

<!--引入jquery和wangEditor.js-->   <!--注意：javascript必须放在body最后，否则可能会出现问题-->
<script src="http://cdn.bootcss.com/jquery/3.2.0/jquery.min.js"></script>
<script src="/js/app.js"></script>
<script type="text/javascript" src="lib/wangEditor/js/wangEditor.min.js"></script>
<script src="{{url('lib/layer/layer.js')}}"></script>


<!--这里引用jquery和wangEditor.js-->
<script type="text/javascript">

    (function initCss() {

        if(document.body.clientWidth<600){

            $(".write-content").css("margin-top","120px");
            $(".btns").css('float','left');
        }

    })();

    (function () {


        var editor = new wangEditor('div1');

        editor.config.menus = $.map(wangEditor.config.menus, function (item, key) {
            if (item === 'video' || item === 'location') {
                return null;
            }

            return item;
        });

        editor.config.emotions = {
            // 支持多组表情
            // 第一组，id叫做 'default'
            'default': {
                title: '默认',  // 组名称
                data: 'http://static.jasminecjc.com/emotion/default-emoji.json'
            },
            'mood': {
                title: '心情',  // 组名称
                data: 'http://static.jasminecjc.com/emotion/mood-emoji.json'
            },
            'gaoguai': {
                title: '搞怪',  // 组名称
                data: 'http://static.jasminecjc.com/emotion/gaoguai-emoji.json'
            },
            'baozou': {
                title: '暴走漫画',  // 组名称
                data: 'http://static.jasminecjc.com/emotion/baozou-emoji.json'
            }
////http://www.wangeditor.com/wangEditor/test/emotions.data

        };

        // 上传图片（举例）
        editor.config.uploadImgUrl = '/upload';
        editor.config.printLog = false;

        // 配置自定义参数（举例）
        editor.config.uploadParams = {

            'inputName': 'wangEditorImg',
            'action': 'dynamicImgUpload',
            'key': randomString(32),
            '_token': '{{csrf_token()}}'
        };
        editor.config.uploadImgFileName = 'wangEditorImg';
        editor.config.uploadImgFns.onload = function (responseData, xhr) {
            // resultText 服务器端返回的text
            // xhr 是 xmlHttpRequest 对象，IE8、9中不支持
            var data;
            try {
                data = $.parseJSON(responseData);
                //console.log(data);
            } catch (err) {
                data = responseData;
            }
            if (data.state == 0) {
                // 上传图片时，已经将图片的名字存在 editor.uploadImgOriginalName
                var originalName = editor.uploadImgOriginalName || '';

                //console.log(data.key);
                // 如果 resultText 是图片的url地址，可以这样插入图片：
                editor.command(null, 'insertHtml', '<img src="' + data.url + '" alt="' + originalName + '" style="max-width:100%;"/>');

            } else {

                alert('上传失败' + data.msg);
            }

            this.config.uploadParams.key = randomString(32);

        };

        editor.onchange = function () {
            // 编辑区域内容变化时，实时打印出当前内容

            var imgDoms = this.$txt.find('img');
            var imgKeys = '';

            for (var i = 0; i < imgDoms.length; i++) {

                var currentImgDom = $(imgDoms[i]);
                var currentUrl = currentImgDom.attr('src');

                if (i === 0) {
                    //printLog(currentUrl);
                    $("#first_img_url").val(currentUrl);
                }

                if (currentUrl.indexOf("getfile?key=") >= 0) {
                    //包含
                    imgKeys = imgKeys + getAfterStr(currentUrl, 'getfile?key=') + '|';

                }
            }

            if (imgKeys.length > 0) {
                imgKeys = imgKeys.substr(0, imgKeys.length - 1);

            }
            $("#img_keys").val(imgKeys);

            //printLog(imgKeys);
        };

        // 设置 headers（举例）
        editor.config.uploadHeaders = {
            'Accept': 'text/x-json'
        };

        editor.create();


        $("#sub_btn").click(function () {

            var html = editor.$txt.html();          // 获取容
            var text = editor.$txt.text();          //获取纯文本
            var titleVal = $("#title").val();

            var titleLength = getObjLength(titleVal);
            var textLength = getObjLength(text);

            if (titleLength > 35) {

                layer.msg('标题太长啦,人家不要嘛', {icon: 5});
                return false;

            } else if (titleLength < 3) {

                layer.msg('老师说标题至少要3个字符哦', {icon: 5});
                return false;
            }


            if (textLength < 20) {

                layer.msg('再多写写哦,20个字符都不到诶', {icon: 5});
                return false;

            }

            if (textLength > 5000) {

                layer.msg('你写得太多了哦', {icon: 5});
                return false;
            }

            $("#article_content").val(html);
            $("#article_form").submit();

        });

        $("#clear_btn").click(function () {

            layer.confirm('你确定要清空么?', {
                btn: ['是的', '不要'] //按钮
            }, function (index) {
                editor.$txt.html('<p><br></p>');
                layer.close(index);
            }, function () {

            });
        });

        $("#div1").one('click', function () {

            if (getObjLength($("#img_keys").val()) < 1) {
                editor.$txt.html('<p><br></p>');
            }

        });

    })();

</script>
</html>