<link rel="stylesheet" type="text/css" href="/css/dynamic.css">
<link href="http://cdn.bootcss.com/imageviewer/0.5.1/viewer.min.css" rel="stylesheet">

<div class="for-dynamic-clone hide">

    <li class="article dynamic">
        <div class="do-op btn-group">
            <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-chevron-down"></i>
            </button>

            <ul class="dropdown-menu do-op-ul">
                <li><a class="dynamic-del hide" href="javascript:void(0);">删除</a></li>
                <li><a class="dynamic-detail" href="javascript:void(0);">在详情页面展开</a></li>
                <li><a class="follow-toggle" href="javascript:void(0);">关注/取消关注</a></li>
                {{-- <li><a href="javascript:void(0);">加入黑名单</a></li>--}}
            </ul>
        </div>

        <div class="content">

            <div class="author">
                <a class="avatar" target="_blank" href="#">
                    <img alt="作者头像"/>
                </a>
                <div class="name">
                    <a class="blue-link" target="_blank"></a>
                    <span class="time"></span>
                </div>


            </div>

            <a class="title" target="_blank"></a>


            <div class="dynamic-show">
                <p class="abstract" style="display: inline-block">
                </p>

            </div>

            <div class="meta">{{--相关信息--}}
                <span>
                    <i class="iconfont ic-list-read"></i>
                    <span class="ic-read-num"></span>
                </span>


                {{--点赞--}}
                <a href="javascript:void(0);" class="do-like">
                    <i class="iconfont ic-zan"></i>
                    <span class="ic-zan-num"></span>
                </a>


                <a target="_blank" class="get-comment">
                    <i class="iconfont ic-list-comments"></i>
                    <span class="dynamic-comment-num"></span>
                </a>
            </div>
        </div>
    </li>

    <li class="mood dynamic">

        <div class="do-op">
            <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-chevron-down"></i>
            </button>

            <ul class="dropdown-menu do-op-ul">
                <li><a class="dynamic-del hide" href="javascript:void(0);">删除</a></li>
                <li><a class="dynamic-detail" href="javascript:void(0);">在详情页面展开</a></li>
                <li><a class="follow-toggle" href="javascript:void(0);">关注/取消关注</a></li>
                {{-- <li><a href="javascript:void(0);">加入黑名单</a></li>--}}
            </ul>
        </div>

        <div class="content">

            <div class="author">
                <a class="avatar" target="_blank" href="#">
                    <img alt="作者头像"/>
                </a>
                <div class="name">
                    <a class="blue-link" target="_blank"></a>
                    <span class="time"></span>
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
                <a href="javascript:void(0);" class="do-like">
                    <i class="iconfont ic-zan"></i>
                    <span class="ic-zan-num"></span>
                </a>


                <a class="load-comment" target="_blank" href="javascript:void(0);" is-load="false">
                    <i class="iconfont ic-list-comments"></i>
                    <span class="dynamic-comment-num"></span>
                    <span class="down1 hide"></span>

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

        var LoadMoreDom = listContainer.children(".load-more");
        var pageDom = $(listContainer).children(".page");


        /*自动更新浏览次数*/
        function autoViewCount() {

            $(listContainer).find(".dynamic").each(function () {

                var thiss = $(this);

                if (isElementInViewport(thiss[0])) {
                    //printLog(thiss.attr('dynamic-id') + '  in screen');

                    var currentTimestamp = getTimestamp();

                    var lastShowTime;

                    if (thiss.attr('last-show-time') !== undefined) {
                        lastShowTime = thiss.attr('last-show-time');

                        if (currentTimestamp - lastShowTime >= 2) {

                            //进行了一次浏览

                            var addViewNum;
                            var eachAddNum = 3;

                            addViewNum = (thiss.attr('add-view-num') !== undefined) ? parseInt(thiss.attr('add-view-num')) : 0;
                            addViewNum++;

                            if (addViewNum >= eachAddNum) {

                                $.ajax({
                                    'url': '/dynamic/' + thiss.attr('dynamic-id') + '/addviews',
                                    'type': 'post',
                                    'dataType': 'json',
                                    'data': {

                                        '_token': '{{csrf_token()}}',
                                        'each_add_num': eachAddNum
                                    },
                                    success: function (resp) {
                                        if (resp.state == 0) {
                                            thiss.attr('add-view-num', addViewNum - eachAddNum);
                                        } else {


                                            thiss.attr('add-view-num', addViewNum);
                                            printLog(resp.msg);

                                        }
                                    }
                                })

                            } else {
                                thiss.attr('add-view-num', addViewNum);
                            }

                        }   //end 进行一次浏览


                    }

                    thiss.attr('last-show-time', currentTimestamp);

                }
            });
        }


        /*下拉到底自动添加*/
        (function addAutoLoad() {

            window.onscroll = function () {


                if (!LoadMoreDom.hasClass("hide") && isElementInViewport($(LoadMoreDom)[0])) {

                    if (pageDom.val() <= 10) {

                        LoadMoreDom.html('正在加载...');
                        LoadMoreDom.click();

                    } else {

                        LoadMoreDom.html('加载更多');
                    }

                }


                autoViewCount();
            }

        })();


        $(listContainer).on("click", ".mood .load-comment", function () {

            var thiss = $(this);
            var commentListDom = thiss.parents(".mood").find(".comment-list");

            var down1Dom = thiss.find(".down1");


            if (thiss.attr("showing") === undefined || thiss.attr("showing") == 'false') {


                if (thiss.attr("is-load") === undefined || thiss.attr("is-load") == 'false') {

                    initComment(commentListDom);
                    thiss.attr("is-load", 'true');

                }

                thiss.attr("showing", 'true');
                commentListDom.removeClass('hide');
                down1Dom.removeClass('hide');

                thiss.unbind();

                startTips(thiss, '收起评论');


            } else {

                commentListDom.addClass('hide');
                down1Dom.addClass('hide');
                thiss.attr("showing", 'false');
                thiss.unbind();
                startTips(thiss, '查看评论', 1);

            }
        });


        $(listContainer).on("click", ".do-like", function () {
                var thiss = $(this);
                var data = {
                    '_token': '{{csrf_token()}}',
                    'target_id': thiss.parents(".dynamic").attr('dynamic-id'),
                    'target_type': 0
                };

                var likeNumDom = thiss.find(".ic-zan-num");
                doLike(thiss, '{{url('dolike')}}', data, likeNumDom)

            }
        );


        $(LoadMoreDom).on('click', function () {

            //console.log("load-more");
            var offsetVal = $(listContainer).children(".offset").val();
            var type = $(listContainer).attr('type');

            setDynamicDom(pageDom.val(), offsetVal, type, userid);
            resetNum(pageDom, 'val', 'add');//页码+1


        }).click();


        /*删除动态*/
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

                    url: '/dynamic/' + dynamicId + '/delete',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        '_token': '{{csrf_token()}}'
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

                });

                layer.close(index);

            }, function (index) {
                layer.close(index);
            });

        });


        /*关注取消关注*/
        $(listContainer).on('click', ".follow-toggle", function () {

            if (!isLogin()) {

                return false;
            }

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

            $(selector).viewer(option);


        }

        function setImgBox(selector) {


            var lis = $(selector).children('.mood-imgs').children('li');
            var length = $(lis).length;

            if (length === 4) {
                selector.find("li").css('width', '40%');

            }

        }


        function setDynamic(domSelector, data) {


            var type = data.dynamic_type;

            $(domSelector).attr('id', 'dynamic-' + data.dynamic_id);
            $(domSelector).attr('dynamic-id', data.dynamic_id);
            $(domSelector).find(".avatar").attr('href', '/u/' + data.user_id).find("img").attr('src', '{{env('imgUrl')}}/' + data.avatar_key);

            $(domSelector).find('.blue-link').html(data.username).attr('href', '/u/' + data.user_id);
            analyticShowTime($(domSelector).find('.time').attr('data-shared-at', data.published_time));

            // var preview = $.phpSerializer.unserialize(data.preview);
            var preview = unserialize(data.preview);
            //printLog(preview);
            analyticTargetEmotion($(domSelector).find(".abstract").html(preview.content));

            var userOpUlDom = $(domSelector).find('.do-op-ul');
            //printLog(userOpUlDom);
            userOpUlDom.find('.dynamic-detail').attr('href', '/dynamic/' + data.dynamic_id);
            var followToggleDom = userOpUlDom.find(".follow-toggle");

            //printLog(followToggleDom);

            var followToggleHtml = '关注 ' + data.username;

            followToggleDom.attr('target-user-id', data.user_id);
            followToggleDom.attr('target-username', data.username);

            if ($(userOpUlDom).length > 0) {
                var currentUserId = $("#current-user-id").val();
                if (currentUserId == data.user_id) {
                    $(userOpUlDom).find('.dynamic-del').removeClass('hide');
                    followToggleDom.addClass('hide');

                } else {

                    if (data.follow_state === 0) {
                        /*以关注*/
                        followToggleHtml = '取消关注 ' + data.username;

                    }

                }
            }

            $(followToggleDom).html(followToggleHtml);


            //设置是否已赞

            if (data.like_state === 0) {

                $(domSelector).find('.do-like').addClass('active');

            }

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


                $(domSelector).find(".comment-list").attr("target_id", data.dynamic_id).addClass('hide');

                $(domSelector).find('.comment-num').html(data.comment_num);

                startTips($(domSelector).find('.load-comment'), '查看评论', 1);


            } else if (type == 1) {
                /*文章*/


                if (preview.first_img_url !== undefined) {

                    var src = preview.first_img_url;
                    var innerHtml = '<a class="wrap-img" href="' + src + '" target="_blank">' +
                        '<img src="' + src + '">' +
                        ' </a>';

                    $(domSelector).addClass('have-img').prepend(innerHtml);

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
                            var noteListDom = $(listContainer).find(".note-list");
                            if (i === 0) {
                                $(listContainer).find('.find-nothing').addClass('hide');

                            }
                            noteListDom.append(dynamicDom);
                        }
                        if (resp.isAll == 1) {

                            if (dynamics.length == 0) {

                                $(listContainer).children(".load-more").addClass('hide');

                            } else {

                                $(listContainer).children(".load-more").html('已加载全部');

                            }

                        }


                    } else {
                        alertMessage('加载动态失败', -1);
                    }

                }

            });
        }
    }
</script>