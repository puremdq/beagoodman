@extends('layouts.app')

@section('link')
    <link href="http://static.jasminecjc.com/css/user.min.css" rel="stylesheet">
    <link href="http://static.jasminecjc.com/css/comment.min.css" rel="stylesheet">

@endsection

@section('title', $user->username)
@section('content')


    <div class="container person">
        <div class="row">
            <div class="col-xs-16 main">

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

                    @endif

                    <div class="title">
                        <a class="name" href="/u/{{$user->user_id}}">{{$user->username}}</a>
                        @if($user->gender==1)
                            <i class="iconfont ic-man"></i>
                        @elseif($user->gender==2)
                            <i class="iconfont ic-woman"></i>
                        @endif
                    </div>

                    <div class="info">
                        <ul>
                            <li>
                                <div class="meta-block">
                                    <a href="/u/{{$user->user_id}}?show=following">
                                        <p>{{$user->following_num}}</p>
                                        关注 <i class="iconfont ic-arrow"></i>
                                    </a></div>
                            </li>
                            <li>
                                <div class="meta-block">
                                    <a href="/u/{{$user->user_id}}?show=followers">
                                        <p>{{$user->followers_num}}</p>
                                        粉丝 <i class="iconfont ic-arrow"></i>
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="meta-block">
                                    <a href="/u/{{$user->user_id}}?show=dynamic&type=0" class="mood-num">
                                        <p>{{$user->mood_num}}</p>
                                        心情 <i class="iconfont ic-arrow"></i>
                                    </a></div>
                            </li>

                            <li>
                                <div class="meta-block">
                                    <a href="/u/{{$user->user_id}}?show=dynamic&type=1" class="article-num">
                                        <p>{{$user->article_num}}</p>
                                        文章 <i class="iconfont ic-arrow"></i>
                                    </a></div>
                            </li>


                        </ul>
                    </div>
                </div>


                @if($show=='dynamic')
                    <ul class="trigger-menu" id="trigger-menu">
                        <li class="@if(empty($_GET['type'])||$_GET['type']==0) active @endif show-mood"><a href="javascript:void(0);"><i
                                        class="iconfont ic-list-like"></i>
                                心情</a>
                        </li>
                        <li class="@if(!empty($_GET['type'])&&$_GET['type']==1) active @endif show-article"><a href="javascript:void(0);"><i
                                        class="iconfont ic-articles"></i>
                                文章</a>
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

                            <div class="find-nothing">
                                <img src="http://img.beagoodman.cn/nothing.png">
                                <div>这里还木有内容哦~</div>
                            </div>

                            <button class="load-more btn">加载更多</button>

                        </div>


                        <div class="list-container article-list" type="1">

                            <input name="page" class="page" hidden="hidden" value="1">
                            <input name="offset" class="offset" hidden="hidden" value="0">

                            <ul class="note-list">
                            </ul>

                            <div class="find-nothing">
                                <img src="http://img.beagoodman.cn/nothing.png">
                                <div>这里还木有内容哦~</div>
                            </div>

                            <button class="load-more btn">加载更多</button>

                        </div>


                        <div class="list-container like-list" type="zan">

                            <input name="page" class="page" hidden="hidden" value="1">
                            <input name="offset" class="offset" hidden="hidden" value="0">

                            <ul class="note-list">

                            </ul>

                            <div class="find-nothing">
                                <img src="http://img.beagoodman.cn/nothing.png">
                                <div>这里还木有内容哦~</div>
                            </div>

                            <button class="load-more btn">加载更多</button>

                        </div>

                    </div>

                @elseif($show=='following'||$show=='followers')
                    {{--显示关注和未关注--}}

                    <ul class="trigger-menu">
                        <li @if($show=='following') class="active" @endif>
                            <a data-placeholder="user"
                               href="/u/{{$user->user_id}}?show=following">关注用户 {{$user->following_num}}</a>
                        </li>
                        <li @if($show=='followers') class="active" @endif>
                            <a data-placeholder="user"
                               href="/u/{{$user->user_id}}?show=followers">粉丝 {{$user->followers_num}}</a>
                        </li>
                    </ul>


                    <div id="list-container">
                        @if(count($userLists)>0)

                            <ul class="user-list">

                                @foreach($userLists as $userItem )

                                    <li>
                                        <a class="avatar" href="/u/{{$userItem->user_id}}">
                                            <img src="{{env('imgUrl')}}/{{$userItem->avatar_key}}">
                                        </a>
                                        <div class="info">
                                            <a class="name" href="/u/{{$userItem->user_id}}">{{$userItem->username}}</a>
                                            <div class="meta">
                                                <span>关注 {{$userItem->following_num}}</span>
                                                <span>粉丝 {{$userItem->followers_num}}</span>
                                                <span>心情 {{$userItem->mood_num}}</span>
                                                <span>文章 {{$userItem->article_num}}</span>
                                            </div>
                                            {{--   <div class="meta">
                                                   写了 0 字，获得了 0 个喜欢
                                               </div>--}}
                                        </div>


                                        <div class="following-group" user-id="{{$userItem->user_id}}"
                                             style="display: inline">

                                            <a class="btn btn-success follow @if(!empty(session('user'))&&session('user')->isFollowing($userItem->user_id)) hide @endif ">
                                                <i class="iconfont ic-follow"></i><span>关注</span>
                                            </a>

                                            <a class="btn btn-default following @if(empty(session('user'))||!session('user')->isFollowing($userItem->user_id)) hide @endif ">
                                                <i class="iconfont ic-followed"></i>
                                                <span>已关注</span>
                                            </a>
                                        </div>
                                    </li>

                                @endforeach

                            </ul>

                        @endif

                    </div>

                @endif

            </div>

            <div class="col-xs-7 col-xs-offset-1 aside">
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

    @if($show=='dynamic')
        @include('layouts.commentJs')
        @include('layouts.dynamicList')

    @endif

@endsection

@section('script')
    @if($show=='dynamic')
        <script src="http://cdn.bootcss.com/imageviewer/0.5.1/viewer.min.js"></script>
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


                    var currentTme = getTimestamp();
                    if (currentListDom.attr('update-at') === undefined) {

                        startShowDynamic(currentListDom, $(".current-userid").val());

                        currentListDom.attr('update-at', currentTme);
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


            /*    $(".article-num").on('click', function () {
             $(".show-article").click();

             });

             $(".mood-num").on('click', function () {
             $(".show-mood").click();
             });*/


        </script>
    @endif

    <script>

        (function initFolloingGroup() {
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
