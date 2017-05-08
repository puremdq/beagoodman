@extends('layouts.app')

@section('link')

    <link href="/css/app.css" rel="stylesheet">
    <link href="http://cdn.bootcss.com/viewerjs/0.6.2/viewer.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/comment.css">

    <style>

        .avatar.main-avatar img {
            width: 90px;
            height: 90px;
        }


    </style>


    <style>

        .trigger-menu {
            margin-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            list-style: none;
            padding-left: 0;

        }

        .trigger-menu li {
            display: inline-block;

        }

        .trigger-menu a {
            display: block;
            padding: 13px 20px;
            font-size: 15px;
            font-weight: 700;
            line-height: 25px;
            color: #969696;
            text-decoration: none;
        }

        .trigger-menu li.active {
            border-bottom: 2px solid #646464;
        }

        .trigger-menu li:hover {

            border-bottom: 2px solid #646464;

        }

        .trigger-menu li.active a {
            color: #333;

        }

        .trigger-menu li a:hover {
            text-decoration: none;

        }

    </style>
@endsection


@section('title', $user->username)

@section('content')


    <div class="container person">
        <div class="row">
            <div class="col-xs-7 main">

                <div class="main-top">
                    <input class="current-userid" hidden value="{{$user->user_id}}">

                    <a class="avatar main-avatar" href="/u/{{$user->user_id}}">
                        <img src="{{env('imgUrl')}}/{{$user->avatar_key}}" alt="头像"/>
                    </a>


                    @if(empty(session('user'))||$user->user_id!=session('user')->user_id)


                        <div class="following-group" user-id="{{$user->user_id}}">


                            <a class="btn btn-success follow @if(!empty(session('user'))&&session('user')->isFollowing($user->user_id)) hide @endif">
                                <i class="iconfont ic-follow"></i><span>关注</span>
                            </a>

                            <a class="btn btn-default following @if(empty(session('user'))||!session('user')->isFollowing($user->user_id)) hide @endif">
                                <i class="iconfont ic-followed"></i>
                                <span>已关注</span>
                            </a>
                        </div>


                        <a class="btn btn-hollow" href="javascript:void(0);">发私信</a>

                        <div class="title">
                            <a class="name" href="/u/{{$user->user_id}}">{{$user->username}}</a>
                            <i class="iconfont ic-man"></i>
                        </div>

                    @endif

                    <div class="info">
                        <ul>
                            <li>
                                <div class="meta-block">
                                    <a href="javascript:void(0);">
                                        <p>{{$user->following_num}}</p>
                                        关注 <i class="iconfont ic-arrow"></i>
                                    </a></div>
                            </li>
                            <li>
                                <div class="meta-block">
                                    <a href="javascript:void(0);">
                                        <p>{{$user->followers_num}}</p>
                                        粉丝 <i class="iconfont ic-arrow"></i>
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="meta-block">
                                    <a href="javascript:void(0);" class="mood-num">
                                        <p>{{$user->mood_num}}</p>
                                        心情 <i class="iconfont ic-arrow"></i>
                                    </a></div>
                            </li>

                            <li>
                                <div class="meta-block">
                                    <a href="javascript:void(0);" class="article-num">
                                        <p>{{$user->article_num}}</p>
                                        文章 <i class="iconfont ic-arrow"></i>
                                    </a></div>
                            </li>


                        </ul>
                    </div>
                </div>


                <ul class="trigger-menu" id="trigger-menu">
                    <li class="active show-mood"><a href="javascript:void(0);"><i
                                    class="iconfont ic-list-like"></i>
                            心情</a>
                    </li>
                    <li class="show-article"><a href="javascript:void(0);"><i class="iconfont ic-articles"></i> 文章</a>
                    </li>
                    {{-- <li class=""><a href="/u/93666dd4205b?order_by=commented_at"><i
                                     class="iconfont ic-latestcomments"></i>
                             最新评论</a></li>--}}
                    <li class=""><a href="javascript:void(0);"><i class="iconfont ic-zan"></i> 赞</a></li>
                </ul>

                <div id="trigger-show">

                    <div class="list-container mood-list" type="0">

                        <input name="page" class="page" hidden="hidden" value="1">
                        <input name="offset" class="offset" hidden="hidden" value="0">

                        <ul class="note-list">
                        </ul>

                        <button class="load-more btn">加载更多</button>

                    </div>


                    <div class="list-container article-list" type="1">

                        <input name="page" class="page" hidden="hidden" value="1">
                        <input name="offset" class="offset" hidden="hidden" value="0">

                        <ul class="note-list">
                        </ul>

                        <button class="load-more btn">加载更多</button>

                    </div>


                    <div class="list-container like-list" type="zan">

                        <input name="page" class="page" hidden="hidden" value="1">
                        <input name="offset" class="offset" hidden="hidden" value="0">

                        <ul class="note-list">
                        </ul>

                        <button class="load-more btn">加载更多</button>

                    </div>

                </div>

            </div>

            <div class="col-xs-4 col-xs-offset-1 aside">
                <div class="title">个人介绍</div>
                <div class="description">
                    <div class="js-intro">
                        {{$user->introduce}}
                    </div>


                </div>

                <!-- 专题和文集 -->
                <div class="js-collection-and-notebook-container"></div>
                <div class="user-action">
                    <a class="js-block-button" href="javascript:void(0);">加入黑名单</a>
                    ·
                    <a class="js-report-button">举报用户</a>
                </div>
            </div>

        </div>
    </div>
    {{--<div data-vcomp="side-tool"></div>--}}
    @include('layouts.commentJs')
    @include('layouts.dynamicList')

@endsection

@section('script')
    <script src="http://cdn.bootcss.com/viewerjs/0.6.2/viewer.min.js"></script>
    <script>

        function initTriggerMenu(triggerMenu, triggerShow) {

            $(triggerMenu).on('click', 'li', function () {

                var thiss = $(this);
                var index = thiss.index();

                thiss.parent().children('.active').removeClass('active');
                thiss.addClass('active');

                var listContainerDom = $(triggerShow).children(".list-container");
                $(listContainerDom).addClass("hide");

                var currentListDom = $(listContainerDom).eq(index);
                currentListDom.removeClass("hide");

                if (currentListDom.attr('is-init') === undefined) {

                    startShowDynamic(currentListDom, $(".current-userid").val());

                    currentListDom.attr('is-init', 'true');
                }
            });

            triggerMenu.children('.active').click();
        }

        initTriggerMenu($("#trigger-menu"), $("#trigger-show"));

        /*初始化*/
        $("#list-container").on('DOMNodeInserted', '.comment-num', function () {

            var thiss = $(this);
            thiss.parents(".mood").find(".dynamic-comment-num").html(thiss.html());

        });


        $(".article-num").on('click', function () {
            $(".show-article").click();

        });

        $(".mood-num").on('click', function () {
            $(".show-mood").click();
        });


    </script>

    <script>

        (function () {

            var followingGroup;
            var data;

            $(".following-group").each(function () {

                followingGroup = $(this);

                data = {
                    '_token': '{{csrf_token()}}',
                    'user_id': followingGroup.attr('user-id')
                };
                activeFollowListener(followingGroup, data);
            });
        })();


    </script>


@endsection