<?php
$target = $dynamic->target;
$user = $dynamic->author;
$title = '出错了...';
$dynamicType = $dynamic->dynamic_type;
$avatarUrl = env('imgUrl') . '/' . $user->avatar_key;

if ($dynamicType == 0) {

    $title = $target->mood_content;
} else if ($dynamicType == 1) {

    $title = $target->article_title;
    //文章
}
?>
@extends('layouts.app')

@section('link')

    {{--<link rel="stylesheet" type="text/css" href="/css/comment.css">--}}
    <link href="http://static.jasminecjc.com/css/article.min.css" rel="stylesheet">

    @if($dynamicType==0)

        <link href="http://cdn.bootcss.com/imageviewer/0.5.1/viewer.min.css" rel="stylesheet">

        <style>
            .img-box .mood-imgs img {

                width: 100%;
                height: 180px;

            }

            .img-box .mood-imgs {

                padding: 0;
            }

            .img-box .mood-imgs li {
                cursor: zoom-in;
                display: inline-block;
                width: 40%;
                padding: 0;
                margin: 0;
                border: 1px solid transparent;

            }
        </style>
    @endif
@endsection




@section('title', $title)

@section('content')


    <div class="note">
        <div class="post">
            <div class="article">
                @if($dynamicType==1)
                    <h1 class="title">{{$title}}</h1>

            @endif

            <!-- 作者区域 -->
                <div class="author">
                    <a class="avatar" href="/u/{{$user->user_id}}">
                        <img src="{{$avatarUrl}}" alt="用户头像"/>
                    </a>
                    <div class="info">
                        <span class="tag">作者</span>
                        <span class="name"><a href="/u/{{$user->user_id}}">{{$user->username}}</a></span>
                        <!-- 关注用户按钮 -->

                        @if(empty(session('user'))||$user->user_id!=session('user_id'))


                            <div class="following-group" user-id="{{$user->user_id}}" style="display: inline-block">


                                <a class="btn btn-success btn-xs follow @if(!empty(session('user'))&&session('user')->isFollowing($user->user_id)) hide @endif">
                                    <i class="iconfont ic-follow"></i><span>关注</span>
                                </a>

                                <a class="btn btn-default btn-xs following @if(empty(session('user'))||!session('user')->isFollowing($user->user_id)) hide @endif">
                                    <i class="iconfont ic-followed"></i>
                                    <span>已关注</span>
                                </a>
                            </div>

                    @endif

                    <!-- 文章数据信息 -->
                        <div class="meta">
                            <span class="publish-time">{{date('Y-m-d H:i', $dynamic->published_time)}}</span>
                            <span class="views-count">阅读 {{$dynamic->read_num}}</span>
                            <span class="comments-count">评论 {{$dynamic->comment_num}}</span>
                            <span class="likes-count">赞 {{$dynamic->like_num}}</span>
                        </div>
                    </div>


                    <!-- 如果是当前作者，加入编辑按钮 -->
                </div>

                <!-- 文章内容 -->
                <div class="show-content">

                    @if($dynamicType==0)

                        <div class="mood-content" style="margin: 5px 0 15px 15px">
                            {!! $target->mood_content !!}

                        </div>


                        @if(!empty($target->img_keys))
                            <?php $imgKeys = explode('|', $target->img_keys) ?>

                            <div class="img-box">

                                <ul class="mood-imgs">
                                    @foreach($imgKeys as $imgKey)
                                        <li><img src="{{env('imgUrl')}}/{{$imgKey}}"></li>
                                    @endforeach
                                </ul>
                            </div>

                        @endif



                    @elseif($dynamicType==1)

                        {!! $target->article_content !!}
                    @else
                        数据出错了...

                    @endif

                </div>


                <div class="show-foot">

                    {{--  <div class="pull-left">

                          <ul class="tags">

                              <li><span class="tag-head">标签</span></li>
                              <li><a><span class="tag">标签</span></a></li>
                              <li><a><span class="tag">标签</span></a></li>
                          </ul>

                      </div>--}}

                    <div class="pull-right">


                        {{--  <div class="modal-wrap" data-report-note="">
                              <a id="report-modal">举报文章</a>
                          </div>--}}

                        <div class="copyright" data-toggle="tooltip" data-html="true"
                             data-original-title="转载请联系作者获得授权，并标注“简书作者”。">
                            © 著作权归作者所有
                        </div>


                    </div>

                </div>

            </div>
            {{--end article--}}
            <div class="clearfix"></div>

            <!-- 文章底部作者信息 -->
            <div class="follow-detail">
                <div class="info">
                    <a class="avatar" href="/u/{{$user->user_id}}">
                        <img src="{{$avatarUrl}}" alt="头像">
                    </a>

                    @if(empty(session('user'))||$user->user_id!=session('user')->user_id)

                        <div class="following-group pull-right" user-id="{{$user->user_id}}"
                             style="display: inline-block">


                            <a class="btn btn-success follow @if(!empty(session('user'))&&session('user')->isFollowing($user->user_id)) hide @endif">
                                <i class="iconfont ic-follow"></i><span>关注</span>
                            </a>

                            <a class="btn btn-default following @if(empty(session('user'))||!session('user')->isFollowing($user->user_id)) hide @endif">
                                <i class="iconfont ic-followed"></i>
                                <span>已关注</span>
                            </a>
                        </div>

                    @endif

                    <a class="title"
                       href="/u/{{$user->user_id}}">{{$user->username}}</a>{{--用户名--}}
                    <p>关注{{$user->following_num}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;粉丝{{$user->followers_num}}</p></div>
                <div class="signature">
                    <span>{{$user->introduce}}</span>
                </div>
            </div>

            <div class="meta-bottom">

                <div class="like">
                    <div class="btn like-group @if($dynamic->isUserLiked(session('user_id',''))) active @endif">
                        <div class="btn-like"><a><i class="iconfont ic-zan"></i>赞</a></div>
                        <div class="modal-wrap"><a class="like-num">{{$dynamic->like_num}}</a></div>
                    </div>
                </div>

                <div class="share-group">
                    <a class="share-circle" data-action="weixin-share" data-toggle="tooltip"
                       data-original-title="分享到微信">
                        <i class="iconfont ic-wechat"></i>
                    </a>
                    <a class="share-circle" data-toggle="tooltip"
                       data-original-title="分享到微博">
                        <i class="iconfont ic-weibo"></i>
                    </a>

                    <a class="share-circle more-share" tabindex="0" data-toggle="popover" data-placement="top"
                       data-html="true" data-trigger="focus" href="javascript:void(0);" data-original-title="" title="">更多分享</a>
                </div>
            </div>


            <div class="comment-list" target_id="{{$dynamic->dynamic_id}}">


                {{--评论列表--}}
                <div class="normal-comment-list comment-list-wrapper  @if(intval($dynamic->comment_num)<1)hide @endif">

                    <div class="top">
                        <span><code class="comment-num"
                                    style="font-size: 18px;">{{$dynamic->comment_num}}</code>条评论</span>
                        {{--<a class="author-only">只看作者</a>
                        <div class="pull-right"><a class="active">按喜欢排序</a><a class="">按时间正序</a><a
                                    class="">按时间倒序</a>
                        </div>--}}
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


                        <div class="all-complete-tip hide">
                            <span>已加载全部</span>
                        </div>

                    </div>


                </div>


            </div>


        </div>

    </div>

    @include('layouts.commentJs')

@endsection

@section('script')


    @if($dynamicType==0)
        @if(!empty($target->img_keys))
            <script src="http://cdn.bootcss.com/imageviewer/0.5.1/viewer.min.js"></script>
        @endif
        <script>
            (function () {
                var moodImgsDom = $(".mood-imgs");
                var moodContentDom = $(".mood-content");

                if (moodImgsDom.length > 0) {

                    $(moodImgsDom).viewer({
                        'movable': false,
                        'navbar': 2
                    });

                }

                if (moodContentDom.length > 0) {
                    analyticTargetEmotion(moodContentDom);

                }

            })();

        </script>

    @endif

    <script>
        initComment($(".comment-list"));

        $(".meta-bottom").on("click", '.like-group', function () {


            var thiss = $(this);
            var data = {
                '_token': '{{csrf_token()}}',
                'target_id': '{{$dynamic->dynamic_id}}',
                'target_type': 0
            };

            var likeNumDom = thiss.find(".like-num");
            var url = '{{url('dolike')}}';
            doLike(thiss, url, data, likeNumDom, 'active');

        });


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