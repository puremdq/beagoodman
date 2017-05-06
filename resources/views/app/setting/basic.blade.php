@extends('layouts.app')

@section('link')

    <link href="/css/app.css" rel="stylesheet">
    <link href="http://cdn.bootcss.com/cropper/3.0.0-beta/cropper.min.css" rel="stylesheet">


    <style>


        .setting .aside {
            position: fixed;
            top: 0;
            bottom: 0;
            margin: 86px 0 0;
            padding: 0 0 30px;
            width: 280px;
            overflow: auto;
        }

        .setting .aside a {
            padding: 10px 15px;
            font-size: 15px;
            display: block;
        }

        .setting .aside .setting-icon {
            margin-right: 15px;
            width: 32px;
            height: 32px;
            text-align: center;
            color: #fff;
            background-color: #a0a0a0;
            border-radius: 4px;
            display: inline-block;
            vertical-align: middle;
        }

        .setting .aside .setting-icon i {
            margin: 6px 0 0 1px;
            font-size: 16px;
            display: block;
        }

        .setting .aside ul {
            list-style: none;

        }

        .setting .aside .active {

            background-color: #eeeeee;
        }

        .setting .aside ul li:hover {

            background-color: #eeeeee;
        }

        .setting .aside ul li a:hover {

            text-decoration: none;
        }

        .setting .main .avatar {
            width: 100px;
            height: 100px;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            border: 1px solid #ddd;
            border-radius: 50%;
            margin: 10px 0;
        }

        .setting .main .top-line {
            padding-top: 0;
        }

        .setting .main tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .setting .main td {
            padding: 20px 0;
        }

        .setting .main .setting-title {
            font-size: 15px;
            color: #969696;
        }

        .setting .main textarea {
            width: 80%;
            height: 100px;
            margin-bottom: 10px;
            padding: 8px 10px;
            font-size: 15px;
            border: 1px solid #c8c8c8;
            border-radius: 4px;
            display: block;
            resize: none;
            outline-style: none;
        }

        .setting .main input[type=email], .setting .main input[type=text] {
            width: 80%;
        }

        .setting table {

            width: 100%;

        }

        .setting .main .setting-verticle {
            vertical-align: bottom;
        }

        img {

            max-width: 100%;
        }

        #avatar_setting {

            width: 800px;
            display: none;
        }

        .avatar-setting-container {
            margin: 20px 30px;
        }

        #avatar_div {
            margin-top: 15px;
            float: left;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, .25);
            background-color: #fcfcfc;
            overflow: hidden;
            width: 70%;
            height: 300px
        }

        #avatar_preview {
            margin-top: 15px;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, .25);
            background-color: #fcfcfc;
            float: right;
            width: 180px;
            height: 180px;
            overflow: hidden;
        }

        #avatar_select {
            display: none;

        }

        #avatar_img_rotate {

            display: none;
        }

    </style>
@endsection

<!-- Loading state -->
@section('content')

    <div id="avatar_setting">

        <div class="avatar-setting-container">

            <div>
                <a class="btn btn-primary" onclick="$('#avatar_select').click();">选择图片</a>
                <input type="file" id='avatar_select' accept="image/png,image/gif,image/jpg,image/jpeg"/>

                <a id="avatar_img_rotate" href="javascript:void (0);" class="btn btn-danger">
                    <span class="glyphicon glyphicon-repeat"></span> 旋转
                </a>
            </div>


            <div class="img-container">

                <div id="avatar_div">
                    <img id="avatar_img" src="">
                </div>


                <div id="avatar_preview" class="img-circle">

                </div>

            </div>

        </div>


    </div>

    <div class="container setting">
        <div class="row">
            <div class="aside">
                <ul>
                    <li class="active"><a href="/settings/basic">
                            <div class="setting-icon"><i class="iconfont ic-settings-basic"></i></div>
                            <span>基础设置</span> <!----></a></li>
                    <li class=""><a href="/settings/blacklist">
                            <div class="setting-icon"><i class="iconfont ic-settings-block"></i></div>
                            <span>黑名单</span> <!----></a></li>
                    {{-- <li class=""><a href="/settings/profile">
                             <div class="setting-icon"><i class="iconfont ic-settings-profile"></i></div>
                             <span>个人资料</span> <!----></a></li>
                     <li class=""><a href="/settings/weibo-auth">
                             <div class="setting-icon"><i class="iconfont ic-settings-verify"></i></div>
                             <span>微博认证</span> <!----></a></li>

                     <li class=""><a href="/settings/reward-setting">
                             <div class="setting-icon"><i class="iconfont ic-settings-money"></i></div>
                             <span>赞赏设置</span> <!----></a></li>
                     <li class=""><a href="/settings/misc">
                             <div class="setting-icon"><i class="iconfont ic-settings-account"></i></div>
                             <span>帐号管理</span> <!----></a></li>--}}
                </ul>
            </div>
            <div class="col-xs-offset-4 col-xs-80 main">
                <form id="form" data-toggle="validator">

                    {{csrf_field()}}
                    <input hidden name="action" value="basicSetting">

                    <table>
                        <thead>
                        <tr>
                            <th class="setting-head responsive-head"></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="base">
                        <tr>
                            <td class="top-line">
                                <div class="avatar">
                                    <img id="show-avatar"
                                         src="{{env('imgUrl')}}/{{$data->avatar_key}}">
                                </div>
                            </td>
                            <td class="top-line">
                                <a class="btn btn-success" href="javascript:void(0);" id="change_img">
                                    更改头像
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="setting-title">
                                昵称
                            </td>
                            <td>
                                <div class="form-group has-feedback">
                                    <input type="text" class="form-control" placeholder="请输入昵称" name="username"
                                           value="{{$data->username}}"
                                           data-remote="{{url('validator')}}" data-minlength="3" required
                                           data-maxlength="15" data-remote-error="用户名已存在">


                                    {{--<span class="glyphicon form-control-feedback" aria-hidden="true"></span>--}}
                                    <div class="help-block with-errors"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="setting-title pull-left">个人简介</td>
                            <td>
                                <div class="form-group has-feedback">

                                    <textarea class="form-control" name="introduce" placeholder="填写你的个人简介"
                                              data-maxlength="50">{{$data->introduce}}</textarea>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="setting-title setting-verticle">
                                性别
                            </td>
                            <td>
                                <div class="col-xs-2">
                                    <input type="radio" value="1" name="gender" @if($data->gender==1) checked @endif >
                                    <span>男</span>

                                </div>
                                <div class="col-xs-2">
                                    <input type="radio" value="2" name="gender"
                                           @if($data->gender==2) checked @endif ><span>女</span>

                                </div>
                                <div class="col-xs-2">
                                    <input type="radio" value="0" name="gender" @if($data->gender==0) checked @endif>
                                    <span>保密</span>

                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="setting-title">电子邮件</td>
                            <td>

                                <div class="form-group has-feedback">
                                    <input type="text" name="email" class="form-control" value="{{$data->email}}"
                                           required
                                           pattern="^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$"
                                           data-remote="{{url('validator')}}"
                                           data-remote-error="邮箱已经被注册"
                                           data-pattern-error="请输入正确的邮箱地址">

                                    <div class="help-block with-errors"></div>
                                </div>

                            </td>
                        </tr>

                        <tr>
                            <td class="setting-title setting-verticle">消息接受设置</td>
                            <td>
                                <div class="col-xs-4"><input type="radio" value="0" name="allowMessage"
                                                             @if($data->allowMessage==0) checked @endif>
                                    <span class="responsive-span">接收所有</span></div>
                                <div class="col-xs-4"><input type="radio" value="1" name="allowMessage"
                                                             @if($data->allowMessage==1) checked @endif> <span
                                            class="responsive-span">只接收好友</span>
                                </div>
                            </td>
                        </tr>

                        {{--      <tr>
                                  <td class="setting-title setting-verticle">提醒邮件通知</td>
                                  <td>
                                      <div class="col-xs-8"><input type="radio" value="instantly"> <span class="responsive-span">所有动态</span>
                                      </div>
                                      <div class="col-xs-11"><input type="radio" value="later"> <span class="responsive-span">每天未读汇总</span>
                                      </div>
                                      <div class="col-xs-5"><input type="radio" value="none"> <span
                                                  class="responsive-span">不接收</span></div>
                                  </td>
                              </tr>--}}
                        </tbody>
                    </table>
                    <input type="submit" class="btn btn-success btn-lg" id="submit" value="保存"> <!---->

                </form>

            </div>
        </div>
    </div>

    {{--<div data-vcomp="side-tool"></div>--}}

@endsection

@section('script')
    <script src="http://cdn.bootcss.com/cropper/3.0.0-beta/cropper.min.js"></script>

    <script>


        var avatarSettingDialog = null;
        $("#change_img").click(function () {

            avatarSettingDialog = layer.open({
                type: 1,
                title: '头像设置',
                offset: '200px',
                resize: false,
                maxWidth: 1000,
                content: $('#avatar_setting'), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                btn: ['确定', '取消'], //按钮
                btn1: function () {
                    doUpload();
                }
            });

        });

        var avatarImg = $('#avatar_img');
        var showAvatar = $('#show-avatar');

        var isBuildCopper = false;


        $("#avatar_select").change(function () {

            var files = this.files;
            var url = null;
            if (files.length) {
                // 对文件进行处理，下面会讲解checkFile()会做什么
                var file = files[0];

                if (file.size > 2 * 1024 * 1024) {
                    alert("图片太大了");
                    return false;
                }

                console.log(file.size / 1024);
                var reader = new FileReader();
                // show表示<div id='show'></div>，用来展示图片预览的
                if (!/image\/\w+/.test(file.type)) {
                    alert("请确保文件为图像类型");
                    return false;
                }

                if (!isBuildCopper) {

                    url = URL.createObjectURL(file);

                    $(avatarImg).attr("src", url).cropper({
                        /*  crop: function(e) {
                         // Output the result data for cropping image.
                         console.log(e.x);
                         console.log(e.y);
                         console.log(e.width);
                         console.log(e.height);
                         console.log(e.rotate);
                         console.log(e.scaleX);
                         console.log(e.scaleY);
                         },*/
                        aspectRatio: 1,
                        viewMode: 1,
                        preview: '#avatar_preview'
                    });

                    $("#avatar_img_rotate").show();

                    isBuildCopper = true;
                } else {

                    URL.revokeObjectURL(url); // Revoke the old one

                    url = URL.createObjectURL(file);
                    $(avatarImg).cropper('replace', url);

                }

            }


        });

        $("#avatar_img_rotate").click(function () {

            $(avatarImg).cropper('rotate', 90);

        });


        function doUpload() {

            $(avatarImg).cropper('getCroppedCanvas').toBlob(function (blob) {

                var formData = new FormData();

                formData.append('croppedImage', blob);
                formData.append('_token', '{{csrf_token()}}');
                formData.append('inputName', 'croppedImage');
                formData.append('key', randomString());
                formData.append('action', 'avatarSetting');

                var loading = layer.load(1, {time: 3 * 1000}); //设定最长等待10秒


                $.ajax('{{url('setting')}}', {
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (resp) {
                        layer.close(loading);

                        if (resp.state == 0) {

                            showAvatar.attr('src', resp.url);
                            alertMessage(resp.msg, 1);
                            layer.close(avatarSettingDialog);//关闭设置头像弹出框

                        } else {
                            alertMessage(resp.msg, -1);
                        }
                        //console.log(resp);
                    },
                    error: function () {
                        layer.close(loading);
                        alertMessage('未知的错误发生了,请稍候再试', -1);
                    }
                });
            });


        }

    </script>

    <script>

        $('#form').on('submit', function (e) {


            if ($("#submit").hasClass('disabled')) {

                alertMessage('请检查你的输入', 0);
                // 验证通过

            } else {

                e.preventDefault();//取消默认操作
                doBasicSetting();
            }
        });

        function doBasicSetting() {

            var formData = new FormData(document.getElementById("form"));
            formData.append('action', 'basicSetting');

            $.ajax({
                url: '{{url('setting')}}',
                data: formData,
                dataType: 'json',
                type: 'post',
                processData: false,
                contentType: false,
                success: function (resp) {

                    if (resp.state == 0) {

                        alertMessage(resp.msg, 1);

                    } else {

                        alertMessage(resp.msg, 0);

                    }
                }

            });

        }

    </script>


@endsection