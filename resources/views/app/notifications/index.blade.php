@extends('layouts.app')

@section('title', '我的消息')
@section('link')
    <link href="http://static.jasminecjc.com/css/notifications.min.css" rel="stylesheet">

    {{--<link rel="stylesheet" media="all" href="/css/notifications.css">--}}
@endsection

@section('content')
    <div class="container notification">


        <div class="row">

            {{--菜单--}}
            <div class="aside">
                <ul class="trigger-index">
                    <li @if($currentType=='comment') class="active" @endif>
                        <a href="/notification?type=comment" class="comment-trigger">
                            <i class="iconfont ic-comments"></i>
                            <span>评论</span>
                        </a>
                    </li>
                    {{-- <li class=""><a href="/notifications/#/chats"><i class="iconfont ic-chats"></i> <span>简信</span>
                             <!---->
                         </a></li>--}}

                    <li @if($currentType=='like') class="active" @endif>
                        <a href="/notification?type=like" class="like-trigger">
                            <i class="iconfont ic-zan"></i> <span>赞</span>
                        </a>
                    </li>

                    <li @if($currentType=='message') class="active" @endif>
                        <a href="/notification?type=message" class="like-trigger">
                            <i class="iconfont ic-chats"></i> <span>站内信</span>
                        </a>
                    </li>

                    <li @if($currentType=='follow') class="active" @endif>
                        <a href="/notification?type=follow" class="follow-trigger">
                            <i class="iconfont ic-follows"></i>
                            <span>关注</span>
                        </a>
                    </li>
                    {{--<li class=""><a href="/notifications/#/money"><i class="iconfont ic-money"></i> <span>赞赏</span>--}}
                    {{--<!---->--}}
                    {{--</a></li>--}}
                    {{--   <li class=""><a href="javascript:void(0);"><i class="iconfont ic-others"></i> <span>系统消息</span>
                               <!----></a></li>--}}
                </ul>
            </div>

            {{--显示--}}
            <div class="col-xs-16 col-xs-offset-8 main trigger-contents">


                <div class="trigger-content">
                    <?php
                    $menuArr = [
                        'comment' => '我收到的评论',
                        'like' => '我收到的赞',
                        'follow' => '关注我的',
                        'message' => '全部站内信'
                    ];

                    $currentShowType = $currentType == 'message' ? 'jianxin' : $currentType;
                    ?>

                    <div class="menu">{{$menuArr[$currentType]}}</div>


                    @if(count($notifications)>0)
                        <ul class="data-list {{$currentShowType}}-list">

                            @foreach($notifications as $notification)
                                @if($currentType=='comment')
                                    {{--评论--}}
                                    <li @if($notification->state==0) class="unread" @endif>
                                        <a href="/u/{{$notification->from_user_id}}" class="avatar"><img
                                                    src="{{env('imgUrl')}}/{{$notification->fromUser->avatar_key}}"/></a>
                                        <div class="info">
                                            <div>
                                                <a class="user"
                                                   href="/u/{{$notification->from_user_id}}">{{$notification->from_username}}</a>

                                                <?php

                                                $comment = $notification->target;

                                                $dynamic = $comment->target;

                                                $dynamicTip = $dynamic->dynamic_type == 0 ? '心情' : '文章';
                                                $preview = unserialize($dynamic->preview);
                                                $title = !empty($preview['title']) ? $preview['title'] : $preview['content'];


                                                if ($notification->notification_type == 1) {

                                                    $tip0 = '评论了你的' . $dynamicTip;
                                                    $tip1 = null;
                                                } else if ($notification->notification_type == 3) {

                                                    $tip0 = '在' . $dynamicTip;
                                                    $tip1 = '的评论中回复了你';

                                                } else {

                                                    $tip0 = '在' . $dynamicTip;
                                                    $tip1 = '的评论中提到了你';

                                                }

                                                ?>
                                                <span class="comment-slogan">{{$tip0}}</span>

                                                <a class="need-emoji" href="/dynamic/{{$dynamic->dynamic_id}}">&nbsp;&nbsp;{{$title}}
                                                    &nbsp;&nbsp;</a>

                                                @if(!empty($tip1))
                                                    <span class="comment-slogan">{{$tip1}}</span>
                                                @endif
                                            </div>
                                            <div class="time" data-shared-at="{{$notification->time}}"></div>
                                        </div>

                                        <p class="need-emoji">
                                            {{--<a href="" class="maleskine-author" target="_blank">@思中译</a>--}}
                                            {{$comment->comment_content}}
                                        </p>

                                        <div class="meta">
                                            {{--<a class="function-btn"><i class="iconfont ic-comment"></i><span>回复</span></a>--}}
                                            <a href="/dynamic/{{$dynamic->dynamic_id}}#comment-{{$notification->target_id}}"
                                               class="function-btn"><i class="iconfont ic-go"></i><span>查看</span></a>
                                            {{-- <a class="report" reportable-type="[object Object]"><span>举报</span></a>--}}
                                        </div>

                                        {{--<div class="add-comment">--}}
                                        {{--<form class="new-comment"><!----> <textarea placeholder="写下你的评论..."></textarea>--}}
                                        {{--<div class="write-function-block">--}}
                                        {{--<div class="emoji-modal-wrap"><a class="emoji"><i--}}
                                        {{--class="iconfont ic-comment-emotions"></i></a></div>--}}
                                        {{--<a class="btn btn-send">发送</a>--}}
                                        {{--<a class="cancel">取消</a>--}}
                                        {{--</div>--}}
                                        {{--</form>--}}
                                        {{--</div>--}}
                                    </li>

                                @elseif($currentType=='like')

                                    <li @if($notification->state==0) class="unread" @endif>
                                        <a href="/u/{{$notification->from_user_id}}" class="avatar">
                                            <img src="{{env('imgUrl')}}/{{$notification->fromUser->avatar_key}}">
                                        </a>
                                        <div class="info">
                                            <a href="/u/{{$notification->from_user_id}}"
                                               class="user">{{$notification->from_username}}</a>

                                            <?php
                                            $LikeRecord = $notification->target;
                                            $dynamicOrComment = $LikeRecord->target;

                                            if ($LikeRecord->target_type == 0) {
                                                //动态
                                                $preview = unserialize($dynamicOrComment->preview);

                                                if ($dynamicOrComment->dynamic_type == 0) {
                                                    $dynamicTitle = $preview['content'];
                                                    $dynamicType = '心情';

                                                } else {
                                                    $dynamicTitle = $preview['title'];
                                                    $dynamicType = '文章';

                                                }
                                            }
                                            ?>

                                            @if($LikeRecord->target_type==0)

                                                <span>赞了你的{{$dynamicType}}&nbsp;&nbsp;</span>
                                                <a class="need-emoji"
                                                   href="/dynamic/{{$dynamicOrComment->dynamic_id}}">{{$dynamicTitle}}</a>

                                            @elseif($LikeRecord->target_type==1)
                                                <span>赞你的评论&nbsp;&nbsp;</span>
                                                <a class="need-emoji"
                                                   href="/dynamic/{{$dynamicOrComment->comment_target}}#comment_{{$dynamicOrComment->comment_id}}">{{$dynamicOrComment->comment_content}}</a>
                                            @endif

                                            <div class="time" data-shared-at="{{$notification->time}}"></div>
                                        </div>
                                    </li>


                                @elseif(($currentType=='follow'))
                                    <li @if($notification->state==0) class="unread" @endif >

                                        <a href="/u/{{$notification->from_user_id}}" class="avatar">
                                            <img src="{{env('imgUrl')}}/{{$notification->fromUser->avatar_key}}">
                                        </a>
                                        <div class="info"><a href="{{$notification->from_user_id}}"
                                                             class="user">{{$notification->from_username}}</a>
                                            <span>关注了你</span>
                                            <div class="time" data-shared-at="{{$notification->time}}"></div>
                                        </div>
                                    </li>


                                @elseif(($currentType=='message'))

                                    <?php
                                    $currentUser = session('user');
                                    $currentUserId = $currentUser->user_id;
                                    $fromUserId = $notification->user_id1 == $currentUserId ? $notification->user_id2 : $notification->user_id1;
                                    $fromUserName = \App\Http\Model\User::getUsername($fromUserId);
                                    $fromUserAvatar = \App\Http\Model\User::getAvatar($fromUserId);

                                    ?>

                                    <li class="message-li @if($notification->state==0) unread @endif"
                                        notification-id="{{$notification->id}}">

                                        <div class="pull-right">
                                            <span class="time">{{date('Y.m.d H:i:s',$notification->update_time)}}</span>
                                            <div><a data-toggle="dropdown" href="javascript:void(0);">
                                                    <i class="iconfont ic-show"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li><a class="del-conversation"><i
                                                                    class="iconfont ic-delete"></i>删除会话</a></li>
                                                    <li><a class="add-blacklist"><i class="iconfont ic-block"></i>加入黑名单</a>
                                                    </li>
                                                    <li><a class="report"><span><i
                                                                        class="iconfont ic-report"></i>举报用户</span></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <a href="/u/{{$fromUserId}}" target="_blank" class="avatar">
                                            <img src="{{$fromUserAvatar}}">
                                        </a>
                                        <a class="wrap show-message-detail" from-id="{{$fromUserId}}">
                                            <div class="name">{{$fromUserName}}</div>
                                            <p>{{$notification->message_preview}}</p>
                                        </a>
                                    </li>


                                @endif

                            @endforeach


                        </ul>


                        @if($totalPage>1)
                            <ul class="like-list data-list">

                                @foreach($notifications as $notification)
                                    <?php

                                    ?>

                                @endforeach


                            </ul>
                        @endif

                    @else

                        <div class="find-nothing">
                            <img src="{{env('imgUrl')}}/nothing.png">
                            <div>这里还木有内容哦~</div>
                        </div>

                    @endif

                </div>


            </div>


            @if($currentType=='message')

                <div class="col-xs-16 col-xs-offset-8 main message-detail hide">
                    <div>
                        <div class="chat-top"><a class="back-to-list active" href="/notification?type=message"><i
                                        class="iconfont ic-back"></i> 返回站内信列表
                            </a>
                            <b>与<a target="_blank" class="other-user"></a>
                                的对话
                            </b>
                            <div><a data-toggle="dropdown" href="javascript:void(0);"><i
                                            class="iconfont ic-show"></i></a>
                                <ul class="dropdown-menu"><!---->
                                    <li><a><i class="iconfont ic-block"></i>加入黑名单</a></li>
                                    <li><a class="report"><span><i class="iconfont ic-report"></i>举报用户</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="message-show">
                            <a class="load-more" current-page="1">加载更多</a>
                            <li class="hide for-clone">
                                <a href="/u/777e265e1d39" class="avatar">
                                    <img src=""></a>
                                <div><span class="content">123456</span></div>
                                <span class="time">06.10 21:30</span>
                            </li>
                            <ul class="message-list">


                            </ul>
                        </div>
                        <div class="write-message">
                            <form><textarea type="text" placeholder="输入内容" class="form-control"></textarea>
                                <input type="button" id="btn-send" class="btn btn-send" value="发送"></form>
                            <div class="hint">SHIFT + ENTER 换行，ENTER 直接发送</div>
                        </div> <!----> <!----></div>
                </div>


            @endif
        </div>

    </div>


@endsection
@section('script')

    <script>

        $(document).ready(function () {

            $(".time").each(function () {
                analyticShowTime($(this));

            });
            $(".need-emoji").each(function () {
                analyticTargetEmotion($(this));
            });

            getNotificationNum(null, $(".comment-trigger"), $(".like-trigger"), $(".follow-trigger"), $(".message-trigger"));
        })
    </script>

    @if($currentType=='message')

        <script>

            $(document).ready(function () {

                //删除会话
                $(".message-li").on('click', '.del-conversation', function () {

                    var dom = $(this).parents('.message-li');
                    var notificationId = parseInt(dom.attr('notification-id'));
                    delConversation(dom, notificationId);
                });

                //查看会话详情
                $(".message-li").on('click', '.show-message-detail', function () {

                    var thiss = $(this);
                    var otherUserId = thiss.attr('from-id');//这里为与当前用户会话的另一个人的id
                    var otherUserName = $.trim(thiss.find('.name').html());
                    var messageDetailDom = $(".message-detail");
                    messageDetailDom.attr('other-user-id', otherUserId);
                    messageFunction(messageDetailDom, otherUserId, otherUserName);

                });
                @if(!empty($_GET['sendMessage'])&&$_GET['sendMessage']=='true')
                messageFunction($(".message-detail"), "{{$_GET['userId']}}", "{{$_GET['username']}}");
                @endif
            });

        </script>


        <script>

            function delConversation(dom, conversationId) {

                $.ajax({
                    url: '/delnotification',
                    type: 'post',
                    dataType: 'json',
                    data: {

                        'notification_id': conversationId,//此时会话的id就是通知的id
                        '_token': '{{csrf_token()}}'
                    },
                    success: function (resp) {

                        if (resp.state == 0) {
                            dom.slideUp(1000, function () {

                                dom.remove();
                            });

                        }
                    }
                });
            }

            //站内信模块 函数体
            function messageFunction(messageDetailDom, otherUserId, otherUserName) {


                (function initThis() {
                    //初始化的自调用函数
                    showMessageDetail();//显示聊天详情窗口


                    //内部变量
                    var textareaDom = $(".write-message").find("textarea");


                    //绑定发消息
                    $("#btn-send").click(function () {



                        var messageText = $.trim(textareaDom.val());

                        if (messageText.length > 1000) {

                            alertMessage('内容过长请分条发送', -1);
                            return false;

                        }

                        sendMessage(otherUserId, messageText, textareaDom);
                    });

                    messageDetailDom.find("a.load-more").click(function () {

                        var dom = messageDetailDom.find('.message-list').children('li:first').find('.time');
                        var endTime = dom.attr('data-shared-at');
                        endTime = parseInt(endTime);
                        getMessageAndInsert(0, endTime, 'prepend', true);
                    });

                    //定时去服务器拿消息
                    var Interval = setInterval(function () {

                        var dom = messageDetailDom.find('.message-list').children('li:last').find('.time');
                        var startTime = dom.attr('data-shared-at');
                        startTime = parseInt(startTime);

                        getMessageAndInsert(startTime, 0, 'append');

                    }, 2000);


                    document.onkeydown = function (e) {
                        if (e && e.shiftKey && e.keyCode == 13) {

                            var val=textareaDom.val();
                            textareaDom.val(val+'\n');
                        }
                        else if (e && e.keyCode == 13) { // enter 键
                            $("#btn-send").click();
                        }
                    };


                })();


                function showMessageDetail() {

                    messageDetailDom.removeClass('hide');
                    $(".trigger-contents").addClass('hide');
                    messageDetailDom.find('.other-user').attr('href', '/u/' + otherUserId).html(otherUserName);

                    getMessageAndInsert();


                }


                //进行发送消息操作
                function sendMessage(toId, messageContent, textareaDom) {
                    $.ajax({
                        url: '/sendmessage',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'to_id': toId,//发送给
                            'message_content': messageContent,//此时会话的id就是通知的id
                            '_token': '{{csrf_token()}}'
                        },
                        success: function (resp) {

                            if (resp.state == 0) {


                                var messageList = messageDetailDom.find(".message-list");
                                var dom = messageDetailDom.find(".for-clone").clone();

                                dom = setMessageDom(dom, resp.messageData, resp.currentUser, resp.otherUser);
                                messageList.append(dom);
                                textareaDom.val('');
                                scrollToBottom();
                            } else {
                                alertMessage('发送失败了，请稍候再试', -1);
                            }

                        }
                    });

                }


                //得到站内信 并插入到相应的位置
                // startTime 开始时间 0表示不限制开始时间    endTime 0表示现在 prependOrAppend 在开头还是在结尾插入

                function getMessageAndInsert(startTime, endTime, prependOrAppend, forLoadMore) {

                    if (startTime === undefined) {
                        startTime = 0

                    }

                    if (endTime === undefined) {
                        endTime = 0

                    }

                    if (prependOrAppend === undefined) {
                        prependOrAppend = 'prepend'//在之前插入

                    }

                    if (forLoadMore === undefined) {
                        forLoadMore = false   //在之前插入

                    }

                    $.ajax({

                        url: '/message',
                        type: 'get',
                        data: {
                            'otherUserId': otherUserId,
                            'startTime': startTime,
                            'endTime': endTime,
                            'prependOrAppend': prependOrAppend

                        },
                        dataType: 'json',
                        success: function (resp) {

                            if (resp.state == 0) {

                                var messageData = resp.data;

                                if (messageData.length < 1 && forLoadMore === true) {

                                    messageDetailDom.find('.load-more').addClass('hide');
//                                    if(forLoadMore===false){}
//                                    scrollToBottom();
                                }

                                for (var i = 0; i < messageData.length; i++) {

                                    var currentData = messageData[i];
                                    var liDom = messageDetailDom.find('.for-clone').clone();
                                    liDom = setMessageDom(liDom, currentData, resp.currentUser, resp.otherUser);

                                    if (prependOrAppend == 'append') {
                                        messageDetailDom.find('.message-list').append(liDom);
                                    } else if (prependOrAppend == 'prepend') {
                                        messageDetailDom.find('.message-list').prepend(liDom);
                                    }
                                }

                                if (messageData.length > 0) {

                                    if (forLoadMore === true) {
                                        scrollToTop();

                                    } else {
                                        scrollToBottom();
                                    }
                                }


                            }

                        }
                    })

                }


                function setMessageDom(dom, data, currentUser, otherUser) {

                    var liDom = dom.removeClass('for-clone').removeClass('hide');

                    analyticShowTime(liDom.find('.time').attr('data-shared-at', data.send_time));

                    if (data.from_id == currentUser.id) {
                        //由当前用户发给另一个人
                        liDom.addClass('message-r');
                        liDom.find('.avatar').attr('href', '/u/' + currentUser.id).children('img').attr('src', currentUser.avatar);

                    } else if (data.from_id == otherUser.id) {
                        //由另一个人发给当前用户
                        liDom.addClass('message-l');
                        liDom.find('.avatar').attr('href', '/u/' + otherUser.id).children('img').attr('src', otherUser.avatar);

                    }

                    var content = (data.message_content == null || data.message_content == '' ) ? data.message_preview : data.message_content;
                    liDom.find('.content').html(content);

                    return liDom;

                }
            }
        </script>
    @endif

@endsection
