<!DOCTYPE html>
<html lang="zh-CN">


<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title','beagoodman')</title>

    <!-- Bootstrap -->
    <link href="http://cdn.bootcss.com/normalize/7.0.0/normalize.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
    {{--<link href="/css/app.css" rel="stylesheet"/>--}}
    <link href="http://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/lib/sinaEmotion/jquery.sinaEmotion.css"/>
    <link href="/lib/ssi-uploader/styles/ssi-uploader.min.css" rel="stylesheet"/>
@yield('link')

<!-- <script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
     <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>-->

    <style>

        /*导航栏*/
        .header {
            background-color: #fff;
            padding: 10px;
            border-bottom: solid #f0f0f0 1px;
            z-index: 100;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 68px;

        }

        /*轮播图图片样式*/
        .carousel-inner .item img {

            width: 100%;
            height: 300px;
        }

        .login-btn {

            background-color: #ff630e;
            color: white;
        }

        .login-btn:focus, .login-btn:hover {
            text-decoration: none !important;
            background-color: #ff4d08 !important;
        }

        .nav li a {

            color: #333 !important;
        }

        .navbar-right li {

            margin: auto 15px;
        }

        #mood_img_input {
            width: 395px;
            font-size: 12px;
            background: #fff;
            position: absolute;
            border: 1px solid #E8E8E8;
            z-index: 29891016;

        }

        .dropdown-menu a span {
            vertical-align: middle;
            display: inline-block;
            margin-left: 10px;
            min-width: 30px;
        }


    </style>
</head>


<body>


{{--新心情弹出框--}}
<div id="new_mood_modal" class="container" style="display:none;width: 500px">

    <div id="num_tips" style="text-align: right;margin: 5px 0">
        <span id="already_tip">已输入</span>
        <span id="font_num" style="font-size: larger">0</span>字
    </div>
    <form id="new_mood_form" method="post" action="{{url('/dynamic')}}">

        <div class="form-group">
            <textarea class="form-control" rows="5" id="new_mood_textarea" name="mood_content"></textarea>
        </div>

        {{ csrf_field() }}
        <input hidden name="img_keys" id="img_keys">
        <input hidden name="dynamic_type" value="mood">
    </form>

    <div style="margin-top: 5px">
        <div style="float: left">

            <a id="active_emoji" href="javascript:void(0);" style="margin-right: 15px;text-decoration: none">
                <i class="iconfont ic-comment-emotions"></i>
            </a>
            <input type="button" class="btn btn-success btn-xs" id="active_img" value="添加图片">
        </div>

        <div style="float: right">
            <a class="btn btn-small" id="mood_submit">发表</a>
        </div>
    </div>

</div>

@if(empty(session('user')))
    <!--登录弹出框-->
    <div class="modal fade" id="reg_login_modal" tabindex="-1">

        <div class="modal-dialog" role="login">
            <div class="modal-content">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <ul class="nav nav-tabs">

                        <li role="presentation"><a href="#login" role="tab" data-toggle="tab">登录</a></li>
                        <li role="presentation"><a href="#register" role="tab" data-toggle="tab">注册</a></li>

                    </ul>

                </div>

                <div class="modal-body">

                    <div class="tab-content">

                        <!--登录panel-->
                        <div role="tabpanel" class="tab-pane" id="login">

                            <form id="login_form" method="post" action="{{url('login')}}" data-toggle="validator">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="username1" class="control-label">用户名:</label>
                                    <input type="text" class="form-control" id="username1" name="username" required
                                           data-minlength="3" data-maxlength="35" placeholder="请输入用户名或邮箱">
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group">
                                    <label for="password1" class="control-label">密码:</label>
                                    <input type="password" class="form-control" id="password1" name="password" required>
                                    <div class="help-block with-errors"></div>

                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">登录</button>
                                </div>

                            </form>

                        </div>

                        <!--注册panel-->
                        <div role="tabpanel" class="tab-pane" id="register">

                            <form id="register_form" method="post" action="{{url('register')}}" data-toggle="validator">
                                {{ csrf_field() }}
                                <div class="form-group has-feedback">
                                    <label for="username" class="control-label">用户名:</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                           data-remote="{{url('validator')}}" data-minlength="3"
                                           data-maxlength="15" data-remote-error="不能以数字开头或用户名已存在">
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label for="email" class="control-label">邮箱:</label>

                                    <input type="text" class="form-control" id="email" name="email" required
                                           pattern="^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$"
                                           data-remote="{{url('validator')}}"
                                           data-remote-error="邮箱已经被注册"
                                           data-pattern-error="请输入正确的邮箱地址">
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>

                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group">
                                    <label for="password" class="control-label">密码:</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           data-minlength="6" data-maxlength="16">
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation" class="control-label">重复密码:</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                           name="password_confirmation" data-match="#password">
                                    <div class="help-block with-errors"></div>
                                </div>


                                <div class="form-group">

                                    <label for="captcha" class="control-label">请输入下图验证码:</label><br>
                                    <input type="text" class="form-control " id="captcha"
                                           name="captcha" style="width: 40%;display: inline-block" required
                                           data-remote="{{url('validator')}}"
                                           data-remote-error="验证码输入错误"
                                    >
                                    <img id="captchaImg" src="{{url('/captcha')}}"
                                         style="width: 100px;height:36px;margin-left: 10px">
                                    <a href="javascript:void (0);"
                                       onclick=" document.getElementById('captchaImg').src='{{url('/captcha')}}?'+Math.random()">
                                        <i class="fa fa-refresh" style="margin-left: 10px;"></i>
                                    </a>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">注册</button>
                                </div>

                            </form>

                        </div>

                    </div>


                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>

            </div>
        </div>
    </div>

@else

    <input hidden id="current-user-id" value="{{session('user')->user_id}}">

@endif
{{--body 部分--}}
<div class="body">


    <!--header-->
    <header class="header">

        <div class="container">


            {{--已登录状态ul--}}
            @if(!empty(session('user')))

                <ul class="nav nav-pills" style="display: inline-block">
                    <li class="active"><a href="{{url('/')}}">首页</a></li>


                    <li class="notification dropdown">
                        <a data-hover="dropdown" class="notification-btn dropdown-toggle" data-toggle="dropdown">
                            <i class="iconfont ic-navigation-notification menu-icon"></i>
                            <span class="menu-text">消息</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/notification?type=comment" class="comment-notification"><i
                                            class="iconfont ic-comments"></i>
                                    <span>评论</span>
                                    <!----></a></li>
                        {{-- <li><a href="/notifications#/chats"><i class="iconfont ic-chats"></i> <span>简信</span>--}}
                        <!----></a></li>
                            {{-- <li><a href="/notifications#/requests"><i class="iconfont ic-requests"></i>
                                     <span>投稿请求</span> <!----></a></li>--}}
                            <li><a href="/notification?type=like" class="like-notification"><i
                                            class="iconfont ic-likes"></i> <span>赞</span>
                                    <!----></a></li>

                            <li>
                                <a href="/notification?type=message" class="message-notification">
                                    <i class="iconfont ic-chats"></i>
                                    <span>站内信</span>
                                </a>
                            </li>

                            <li><a href="/notification?type=follow" class="follow-notification"><i
                                            class="iconfont ic-follows"></i> <span>关注</span>
                                    <!----></a></li>
                            {{--<li><a href="/notifications#/money"><i class="iconfont ic-money"></i> <span>赞赏</span>--}}
                        <!----></a></li>
                            {{-- <li><a href="/notifications#/others"><i class="iconfont ic-others"></i> <span>其他消息</span>
                                     <!----></a></li>--}}
                        </ul>
                    </li>
                </ul>


                <ul class="nav nav-pills navbar-right">

                    <li class="dropdown">


                        <a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                           href="u/{{session('user_id')}}">

                            <i class="iconfont ic-navigation-profile"></i>
                            <span>{{session('user')->username}}</span>
                            <span class="caret"></span>
                        </a>


                        {{--  <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                              <span class="glyphicon glyphicon-user">{{session('user')->username}}</span>
                          </a>--}}

                        <ul class="dropdown-menu">

                            <?php $userId = session('user')->user_id ?>

                            <li>
                                <a id="my-home" href="/u/{{$userId}}">
                                    <i class="iconfont ic-navigation-profile"></i><span> 我的主页</span>
                                </a>
                            </li>

                            <li>
                                <a id="setting" href="/setting">
                                    <i class="iconfont ic-navigation-settings"></i>
                                    <span> 设置</span>
                                </a>
                            </li>


                            <li>
                                <a id="logout" href="{{url('logout')}}">
                                    <i class="iconfont ic-navigation-signout"></i>
                                    <span> 安全退出</span>
                                </a>
                            </li>


                        </ul>

                    </li>
                    <li><a href="#" class="new-mood btn btn-success" id="new_mood"><span
                                    class="glyphicon glyphicon-pencil">新心情</span></a></li>
                    <li><a href="{{url('/write')}}" class="new-mood btn btn-info"><span
                                    class="glyphicon glyphicon-edit">新文章</span></a>
                    </li>
                </ul>

            @else
                {{--未登录状态ul--}}

                <ul class="nav nav-pills" style="display: inline-block">
                    <li class="active"><a href="{{url('/')}}">首页</a></li>
                </ul>

                <ul class="nav nav-pills navbar-right" style="display: inline-block">


                    <li>
                        <a class="login-btn" data-toggle="modal" data-target="#reg_login_modal" id="for-login">
                            <span class="glyphicon glyphicon-log-in"> 登录</span>
                        </a>
                    </li>

                    <li>
                        <a data-toggle="modal" class="reg-btn" data-target="#reg_login_modal" id="for-reg">
                            <span class="glyphicon glyphicon-registration-mark"> 注册</span>
                        </a>
                    </li>

                </ul>
            @endif
        </div>


    </header>

    <div style="margin-top: 68px" class="margin-top-div"></div>


    <!--content-->

    @yield('content')


    <footer class="footer">

    </footer>

</div>
<script src="http://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap-hover-dropdown/2.0.10/bootstrap-hover-dropdown.min.js"></script>
<script src="{{url('/lib/sinaEmotion/jquery.sinaEmotion.js')}}"></script>
<script src="{{url('lib/layer/layer.js')}}"></script>
<script src="{{url('js/validator.js')}}"></script>
<script src="/lib/ssi-uploader/js/ssi-uploader.js"></script>
<script src="/js/app.js"></script>



<script>
    /*公共方法区*/


    /*点赞操作*/
    function doLike(likeDom, url, postData, likeNumDom, likedClass) {

        if (!isLogin()) {
            return false;
        }

        likedClass = likedClass === undefined ? 'active' : likedClass;

        $.ajax({

            url: url,
            type: "post",
            dataType: 'json',
            data: postData,
            success: function (resp) {
                if (resp.state == 0) {

                    var likeNum = ($(likeNumDom).html() == '赞') ? 0 : parseInt($(likeNumDom).html());

                    if (resp.isCancel == 1) {
                        /*进行的是取消 而不是点赞操作*/
                        likeDom.removeClass(likedClass);
                        $(likeNumDom).html(likeNum - 1);

                    } else {
                        /*进行的不是取消 而是点赞操作*/
                        likeDom.addClass(likedClass);
                        $(likeNumDom).html(likeNum + 1);
                    }

                } else {
                    alertMessage(resp.msg, -1);
                }
            }
        });

    }

    /*关注 取消关注*/
    function activeFollowListener(followingGroupDom, postData, followClass, followingClass) {


        followClass = (followClass === undefined) ? '.follow' : followClass;
        followingClass = (followingClass === undefined) ? '.following' : followingClass;

        function doAjax(postData) {

            $.ajax({

                url: '/u/toggle_follow',
                type: 'post',
                dataType: 'json',
                data: postData,
                success: function (resp) {
                    if (resp.state == 0) {

                        if (resp.isCancel == 0) {

                            //进行的是关注操作
                            $(followingGroupDom).find(followClass).addClass('hide');
                            $(followingGroupDom).find(followingClass).removeClass('hide');//显示已经关注

                        } else {
                            $(followingGroupDom).find(followClass).removeClass('hide');
                            $(followingGroupDom).find(followingClass).addClass('hide');//显示已经关注
                        }

                    } else {

                        alertMessage(resp.msg, -1);
                    }
                }
            });
        }


        $(followingGroupDom).on('click', followClass, function () {

            if (!isLogin()) {

                return false;
            }


            doAjax(postData);

        });


        $(followingGroupDom).on('click', followingClass, function () {

            if (!isLogin()) {

                return false;
            }

            doAjax(postData);

        });

        startTips(followingGroupDom.find(followingClass), '取消关注');

    }


    $(document).ready(function () {

        $("a.dropdown-toggle").click(function () {

            printLog('sdf00');
            var thiss = $(this);

            var dom = thiss.parent(".dropdown").children(".dropdown-menu").children("li").children("a");

            window.location.href = dom.attr('href');

        })

    });

</script>


@if(empty(session('user')))

    <script>
        /*登录注册*/
        $(document).ready(function () {

            $("#for-login").on('click', function () {

                $('.modal-header a[href="#login"]').tab('show');

            });

            $("#for-reg").on('click', function () {

                $('.modal-header a[href="#register"]').tab('show');

            });


            $('#register_form').on('submit', function (e) {
                if (!e.isDefaultPrevented()) {
                    // 验证通过
                    e.preventDefault();//取消默认操作
                    doRegister();

                }
            });

            /*做注册*/
            function doRegister() {

                var form;
                var url;

                form = new FormData(document.getElementById("register_form"));
                url = $("#register_form").attr('action');

                // console.log(url);
                $.ajax({

                    url: url,
                    type: "post",
                    data: form,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function (data) {

                        console.log(data);

                        if (data.state == 0) {
                            /*成功*/
                            $('#reg_login_modal').modal('hide');
                            doAlert('注册成功', 1500);
                            setTimeout(
                                function () {
                                    location.reload();
                                }, 1500);

                        } else {
                            $('#reg_login_modal').modal('hide');
                            alert("注册失败");
                        }

                    },
                    error: function (e) {
                        alert("一个未知错误发生了，请稍候再试");
                    }
                });

            }
        })


    </script>
@else
    <script>

        /*新心情 js*/
        var new_mood_textarea = $("#new_mood_textarea");
        $('#active_emoji').SinaEmotion(new_mood_textarea);

        $('#new_mood').on('click', function () {


            openNewMood();

            function openNewMood() {


                var imgKeys = $("#img_keys").val('');

                layer.open({
                    type: 1,
                    title: '随便说点什么吧',
                    offset: '200px',
                    resize: false,
                    maxWidth: 500,
                    content: $('#new_mood_modal'), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                    cancel: function (index) {
                        if (getObjLength(imgKeys) >= 1) {

                            if (confirm('确定要放弃已上传有图片么')) { //只有当点击confirm框的确定时，该层才会关闭

                                imgKeys.val('');
                                layer.close(index);

                                $("#close_mood_img_input").click();
                            } else {
                                return false;
                            }
                        }
                    }
                });

                // moodLayer
            }

            /*设置 弹出框 布局为absolute 使得能一起滑动  */
            $("#new_mood_modal").parents(".layui-layer").css("position", "absolute");

            new_mood_textarea.on("keyup", function () {
                // console.log($("#new_mood_textarea").html());
                checkFontNum();
            });


            /*点击选择图片按钮*/
            $("#active_img").on('click', function (event) {

                event.stopPropagation();
                var eTop = $("#active_img").offset().top;
                var eLeft = $("#new_mood_modal").offset().left - 1;

                $('body').append('<div id="mood_img_input"></div>');
                $('#mood_img_input').css({top: eTop, left: eLeft});
                var html = '<div>' +
                    '<a id="close_mood_img_input" style="text-align:right; display: block;margin-right: 10px;font-size: large;">x </a>' +

                    '<input type="file" multiple id="ssi-upload"/>' + '</div>';
                $('#mood_img_input').html(html);

                $('#ssi-upload').ssi_uploader({
                    url: '{{url('/upload')}}',
                    data: {

                        '_token': '{{csrf_token()}}',
                        'action': 'dynamicImgUpload',
                        'inputName': 'files',
                        'key': randomString()

                    },
                    locale: 'zh_CN',
                    maxNumberOfFiles: 5,
                    responseValidation: {
                        validationKey: 'state',
                        resultKey: 'msg',
                        success: 0,
                        error: 1
                    },

                    beforeEachUpload: function () {

                        this.data.key = randomString();

                    },
                    onEachUpload: function (data, resp) {

                        //console.log(resp);
                        //do something
                        if (resp.state == 0) {
                            /*success*/

                            var img_keys = $("#img_keys");
                            var key = resp.key;
                            //console.log(key);

                            var value = img_keys.val();

                            if (value.length >= 1) {
                                value = value + '|' + key
                            } else {
                                value = key;
                            }
                            img_keys.val(value);

                            //console.log(img_keys.val());

                            if (getObjLength(new_mood_textarea) < 1) {

                                new_mood_textarea.val('分享了图片');

                            }

                        }
                    }

                });

                /*关闭图片上传框*/
                $("#close_mood_img_input").click(function () {

                    var imgKeys = $("#img_keys");

                    if (getObjLength(imgKeys) >= 1) {

                        layer.confirm('请问你确定要放弃已经上传的图片吗', {
                            btn: ['是', '否'] //按钮
                        }, function () {
                            $("#img_keys").val('');
                            new_mood_textarea.val('');
                            $("#mood_img_input").remove();
                            imgKeys.val('');
                            layer.closeAll('dialog');
                            //layer.msg('已移出图片', {icon: 1});

                        }, function () {


                        });


                    } else {

                        $("#mood_img_input").remove();
                    }


                });

                //console.log(eTop);
                //console.log(eLeft);

            });

            $("#mood_submit").on("click", function () {
                if (checkFontNum()) {
                    $("#new_mood_form").submit();

                }

            })

        });


        function checkFontNum() {

            // console.log($("#new_mood_textarea").html());

            var length = getObjLength(new_mood_textarea);
            if (length <= 500) {
                $("#num_tips").css('color', 'black');
                $("#already_tip").html("已输入");
                $("#font_num").html(length);

                return length >= 1;

            } else {

                $("#num_tips").css('color', 'red');
                $("#already_tip").html("已超出");
                $("#font_num").html(length - 500);
                return false;
            }
        }

    </script>

    <script>

        function getNotificationNum(allNotificationDom, commentNotificationDom, likeNotificationDom, followNotificationDom,messageNotificationDom) {

            $.ajax({
                url: '/getnotification',
                type: 'get',
                dataType: 'json',
                success: function (resp) {

                            {{-- /*0 赞了 你 动态     1 评论了你的动态  2赞了你的评论  3回复了你的评论  4@le你   5关注了你  6站内信*/--}}

                    var data = resp.data;
                    var dataVal = [0, 0, 0, 0, 0, 0,0];
                    var i;

                    for (i in data) {//不使用过滤

                        dataVal[i] = data[i];

                        //console.log(i + "  ->  " + data[i]);
                    }


                    var commentNum = toInt(dataVal[1]) + toInt(dataVal[3]) + toInt(dataVal[4]);
                    var likeNum = toInt(dataVal[0]) + toInt(dataVal[2]);
                    var followNum = toInt(dataVal[5]);
                    var messageNum = toInt(dataVal[6]);

//                    console.log('commentNum:'+commentNum);
//                    console.log('likeNum:'+likeNum);
//                    console.log('followNum:'+followNum);

                    var totalNum = commentNum + likeNum + followNum+messageNum;

                    if (totalNum > 0) {
                        var html = ' <span class="badge">' + totalNum + '</span>';
                        $(allNotificationDom).append(html);
                    }

                    if (commentNum > 0) {
                        var html = ' <span class="badge">' + commentNum + '</span>';

                        $(commentNotificationDom).append(html);
                    }

                    if (likeNum > 0) {
                        var html = ' <span class="badge">' + likeNum + '</span>';

                        $(likeNotificationDom).append(html);
                    }

                    if (followNum > 0) {
                        var html = ' <span class="badge">' + followNum + '</span>';

                        $(followNotificationDom).append(html);
                    }

                    if (messageNum > 0) {
                        var html = ' <span class="badge">' + messageNum + '</span>';
                        $(messageNotificationDom).append(html);
                    }
                }
            });
        }

        $(document).ready(function () {

            getNotificationNum($(".notification-btn"), $(".comment-notification"), $(".like-notification"), $(".follow-notification"),$(".message-notification"));

        })

    </script>

@endif

@if(!empty(session('tips')))
    <script>
        alertMessage("{{session('tips')}}", "{{session('state')}}");
        // doAlert("{{session('tips')}}", 2000, "{{session('state')}}");
    </script>
@endif

@yield('script')
{{--在这里添加其他的script--}}
</body>
</html>