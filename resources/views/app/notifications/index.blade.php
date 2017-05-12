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
                        'follow' => '关注我的'
                    ]

                    ?>

                    <div class="menu">{{$menuArr[$currentType]}}</div>


                    @if(count($notifications)>0)
                        <ul class="data-list {{$currentType}}-list">

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
                                    <li @if($notification->state==0) class="unread" @endif>


                                        <a href="/u/{{$notification->from_user_id}}" class="avatar">
                                            <img src="{{env('imgUrl')}}/{{$notification->fromUser->avatar_key}}">
                                        </a>
                                        <div class="info"><a href="{{$notification->from_user_id}}"
                                                             class="user">{{$notification->from_username}}</a>
                                            <span>关注了你</span>
                                            <div class="time" data-shared-at="{{$notification->time}}"></div>
                                        </div>
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



            getNotificationNum(null, $(".comment-trigger"), $(".like-trigger"), $(".follow-trigger"));


        })
    </script>


@endsection
