<style>
    li.dynamic {

        background-color: #f1f6ff;
        padding: 10px 15px;
    }

    .img-box .mood-imgs img {

        width: 100%;
        height: 110px;

    }

    .img-box .mood-imgs {

        padding: 0;
    }

    .meta a {

        text-decoration: none;
    }

    .img-box .mood-imgs li {
        cursor: zoom-in;
        display: inline-block;
        width: 30%;
        padding: 0;
        margin: 0;
        border: 1px solid transparent;

    }

    .down1 {
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 15px solid #f2f2f5;
        margin-left: -25px;

    }

    .comment-list .normal-comment-list {

        margin-top: 10px;
    }

    .comment-list {
        background-color: #f2f2f5;
        margin-top: 10px;
        padding: 5px 15px;

    }

    .comment-list * {
        font-size: 13px;

    }

    .normal-comment-list {

        margin: 5px;
    }

    .comment-list .sub-comment-list {

        margin: 5px 15px;
        background-color: #eaeaec;
    }

    .comment-list .top {

        padding-bottom: 10px;
    }

    .comment-list .add-comment {

        padding: 10px 20px;
    }

    .comment-list .comment {

        padding-bottom: 5px;
    }

    ul.dropdown-menu li {
        border: none;
        margin: 0;
        padding: 0;

    }

</style>

<div class="for-dynamic-clone hide">

    <li class="article dynamic">

        <div class="content">

            <div class="author">
                <a class="avatar" target="_blank" href="#">
                    <img alt="作者头像"/>
                </a>
                <div class="name">
                    <a class="blue-link" target="_blank"></a>
                    <span class="time"></span>
                </div>

                <div class="user-op btn-group pull-right">
                    <button type="button" class="btn btn-ls btn-info dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon glyphicon-menu-down"></span>
                    </button>

                    <ul class="dropdown-menu user-op-ul">
                        <li><a class="dynamic-del hide" href="javascript:void(0);">删除</a></li>
                        <li><a class="follow-toggle" href="javascript:void(0);">关注/取消关注</a></li>
                        {{--<li><a href="javascript:void(0);">加入黑名单</a></li>--}}
                    </ul>
                </div>

            </div>

            <a class="title" target="_blank"></a>
            <p class="abstract">
            </p>

            <div class="meta">{{--相关信息--}}
                <span>
                    <i class="iconfont ic-list-read"></i>
                    <span class="ic-read-num"></span>
                </span>

                <span>
                    <i class="iconfont ic-zan"></i>
                    <span class="ic-zan-num"></span>
                </span>

                <a target="_blank" class="get-comment">
                    <i class="iconfont ic-list-comments"></i>
                    <span class="dynamic-comment-num"></span>
                </a>
            </div>
        </div>
    </li>

    <li class="mood dynamic">

        <div class="content">

            <div class="author">
                <a class="avatar" target="_blank" href="#">
                    <img alt="作者头像"/>
                </a>
                <div class="name">
                    <a class="blue-link" target="_blank"></a>
                    <span class="time"></span>
                </div>


                <div class="user-op btn-group pull-right">
                    <button type="button" class="btn btn-ls btn-info dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon glyphicon-menu-down"></span>
                    </button>

                    <ul class="dropdown-menu user-op-ul">
                        <li><a class="dynamic-del hide" href="javascript:void(0);">删除</a></li>
                        <li><a class="follow-toggle" href="javascript:void(0);">关注/取消关注</a></li>
                        {{-- <li><a href="javascript:void(0);">加入黑名单</a></li>--}}
                    </ul>
                </div>


            </div>


            <div class="mood-show">

                <p class="abstract"></p>

            </div>

            <div class="meta">

                                <span>
                                    <i class="iconfont ic-list-read"></i>
                                    <span class="ic-read-num"></span>
                                </span>

                {{--点赞--}}
                <a href="javascript:void(0);" class="mood-do-like">
                    <i class="iconfont ic-zan"></i>
                    <span class="ic-zan-num"></span>
                </a>
                <a target="_blank" href="javascript:void(0);">
                    <i class="iconfont ic-list-comments"></i>
                    <span class="dynamic-comment-num"></span>
                    <span class="down1"></span>

                </a>

            </div>

            <div class="comment-list">

                {{--评论列表--}}
                <div class="normal-comment-list comment-list-wrapper">

                    <div class="top">
                                        <span>
                                            <code class="comment-num" style="font-size: 18px;"></code>条评论
                                        </span>

                    </div>

                    <input hidden class="page" value="1">
                    <input hidden class="offset" value="0">
                    <div class="just-comment-list">

                        {{--仅仅是一条一条的评论区--}}

                    </div>

                    <div class="new-comment-tips">

                        <div>
                            <a class="btn btn-lg btn-success load-more load-more-0">查看更多</a>
                        </div>

                        {{--<div class="all-complete-tip hide">
                            <span>已加载全部</span>
                        </div>--}}

                    </div>

                </div>
            </div>
        </div>
    </li>
</div>

<script>

    function startShowDynamic(listContainer, userid) {


        $(listContainer).on("click", ".mood-do-like", function () {


                var thiss = $(this);

                var data = {
                    '_token': '{{csrf_token()}}',
                    'target_id': thiss.parents(".mood").attr('dynamic-id'),
                    'target_type': 0
                };

                var likeNumDom = thiss.find(".ic-zan-num");

                doLike(thiss, '{{url('dolike')}}', data, likeNumDom)

            }
        );


        $(listContainer).children(".load-more").on('click', function () {

            var pageDom = $(listContainer).children(".page");

            var offsetVal = $(listContainer).children(".offset").val();
            var type = $(listContainer).attr('type');
            setDynamicDom(pageDom.val(), offsetVal, type, userid);
            resetNum(pageDom, 'val', 'add');//页码+1

        }).click();


        $(listContainer).on("click", '.dynamic-del', function () {

            if (isLogin() === false) {
                return false;
            }

            var thiss = $(this);

            var currentDynamicDom = thiss.parents('.dynamic');
            var dynamicId = currentDynamicDom.attr('dynamic-id');

            layer.confirm('你确定要删除吗', {
                btn: ['是的', '手滑了'] //按钮
            }, function (index) {

                $.ajax({

                    url: '/dynamic/' + dynamicId,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'action': 'delete'
                    },
                    success: function (resp) {

                        if (resp.state == 0) {

                            currentDynamicDom.slideUp(1000, function () {
                                currentDynamicDom.remove();
                            });

                        } else {
                            alertMessage(state.msg);
                        }
                    }

                })

                layer.close(index);

            }, function (index) {
                layer.close(index);
            });

        });


        $(listContainer).on('click', ".follow-toggle", function () {

            function doFolloToggleAjax(thiss) {

                $.ajax({

                    url: '/u/toggle_follow',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        '_token': '{{csrf_token()}}',

                        'user_id': $(thiss).attr('target-user-id')

                    },
                    success: function (resp) {

                        if (resp.state == 0) {

                            var username = $(thiss).attr('target-username');

                            if (resp.isCancel == 0) {

                                //进行的是关注操作
                                $(thiss).html('取消关注 ' + username);

                            } else {
                                $(thiss).html('关注 ' + username);
                            }

                            alertMessage('操作成功', 1);

                        } else {

                            alertMessage(resp.msg, -1);
                        }
                    }
                });


            }

            doFolloToggleAjax($(this));

        });


        function setViewer(selector) {

            var option = {
                'movable': false,
                'navbar': 2
            };
            var viewer = new Viewer($(selector)[0], option);


        }

        function setImgBox(selector) {


            var lis = $(selector).children('.mood-imgs').children('li');
            var length = $(lis).length;

            if (length === 4) {
                thiss.find("li").css('width', '40%');

            }

        }


        function setDynamic(domSelector, data) {


            var type = data.dynamic_type;

            $(domSelector).attr('id', 'dynamic-' + data.dynamic_id);
            $(domSelector).attr('dynamic-id', data.dynamic_id);
            $(domSelector).find(".avatar").attr('href', '/u/' + data.user_id).find("img").attr('src', '{{env('imgUrl')}}/' + data.avatar_key);

            $(domSelector).find('.blue-link').html(data.username).attr('href', '/u/' + data.user_id);
            analyticShowTime($(domSelector).find('.time').attr('data-shared-at', data.published_time));

            var preview = $.phpSerializer.unserialize(data.preview);

            analyticTargetEmotion($(domSelector).find(".abstract").html(preview.content));

            var userOpUlDom = $(domSelector).find('.user-op-ul');

            var followToggleDom = userOpUlDom.find(".follow-toggle");

            var followToggleHtml = '关注 ' + data.username;

            followToggleDom.attr('target-user-id', data.user_id);
            followToggleDom.attr('target-username', data.username);

            if ($(userOpUlDom).length > 0) {
                var currentUserId = $("#current-user-id").val();
                if (currentUserId == data.user_id) {
                    $(userOpUlDom).find('.dynamic-del').removeClass('hide');
                    followToggleDom.addClass('hide');

                } else {

                    if (data.source_user_id == currentUserId && data.follow_state == 0) {
                        /*以关注*/

                        followToggleHtml = '取消关注 ' + data.username;

                    }

                }
            }

            $(followToggleDom).html(followToggleHtml);

            var metaDom = $(domSelector).find(".meta");
            $(metaDom).find(".ic-read-num").html(data.read_num);
            $(metaDom).find(".ic-zan-num").html(data.like_num);
            $(metaDom).find(".dynamic-comment-num").html(data.comment_num);

            if (type == 0) {
                /*xin qin*/
                if (preview.img_keys !== undefined) {

                    var html = ' <div class="img-box"><ul class="mood-imgs"></ul></div>'

                    $(domSelector).find(".mood-show").append(html);

                    var imgKeysArr = preview.img_keys.split("|");


                    for (var i = 0; i < imgKeysArr.length; i++) {

                        html = '<li><img src="{{env('imgUrl')}}/' + imgKeysArr[i] + '"></li>';
                        $(domSelector).find(".mood-imgs").append(html);
                    }

                    var imgBoxDom = $(domSelector).find('.img-box')
                    setImgBox(imgBoxDom);
                    setViewer($(imgBoxDom).find(".mood-imgs"));

                }


                $(domSelector).find(".comment-list").attr("target_id", data.dynamic_id);

                $(domSelector).find('.comment-num').html(data.comment_num);
                if (data.comment_num < 1) {
                    $(domSelector).find(".normal-comment-list").addClass("hide");
                }

                initComment($(domSelector).find(".comment-list"));

            } else if (type == 1) {
                /*文章*/


                if (preview.first_img_url !== undefined) {

                    var src = preview.first_img_url;
                    var innerHtml = '<a class="wrap-img" href="' + src + '" target="_blank">' +
                        '<img src="' + src + '">' +
                        ' </a>';

                    $(domSelector).addClass('have-img')
                        .prepend(innerHtml);

                    /*  */
                }
                $(domSelector).find('.title').attr('href', '/dynamic/' + data.dynamic_id).html(preview.title);

                $(domSelector).find(".get-comment").attr("href", '/dynamic/' + data.dynamic_id + '#comment-list');

            }

            return $(domSelector);

        }


        function setDynamicDom(page, offset, type, userid) {

            type = (type === undefined) ? '%' : type;

            $.ajax({
                type: 'get',
                url: '{{url('dynamic')}}',
                data: {
                    'page': page,
                    'offset': offset,
                    'type': type,
                    'userid': userid

                },
                dataType: 'json',
                success: function (resp) {

                    if (resp.state == 0) {

                        var dynamics = resp.dynamicList;

                        for (var i = 0; i < dynamics.length; i++) {

                            var dynamicDom;

                            if (dynamics[i].dynamic_type == 0) {
                                dynamicDom = $(".for-dynamic-clone").find('.mood').clone();

                            } else if (dynamics[i].dynamic_type == 1) {

                                dynamicDom = $(".for-dynamic-clone").find('.article').clone();

                            } else {

                                alertMessage('未知错误');

                                return false;
                            }


                            dynamicDom = setDynamic(dynamicDom, dynamics[i]);


                            $(listContainer).find(".note-list").append(dynamicDom);


                        }
                        if (resp.isAll == 1) {

                            $(listContainer).children(".load-more").addClass('hide');
                        }


                    } else {
                        alertMessage('加载动态失败', -1);
                    }


                }

            });

        }
    }


</script>