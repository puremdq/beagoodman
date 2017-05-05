@extends('layouts.app')

@section('link')
    <link href="/css/app.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/lib/wangEditor/css/wangEditor.min.css">
    <link rel="stylesheet" type="text/css" href="/css/comment.css">

    <style>

        a {
            text-decoration: none !important;
        }

        .note {
            padding-top: 10px;
        }

        .note .post {
            margin: 0 auto;
            padding-top: 20px;
            padding-bottom: 60px;
            width: 620px;
        }

        .note .post .article .title {
            margin: 20px 0 0;
            font-family: -apple-system, SF UI Display, Arial, PingFang SC, Hiragino Sans GB, Microsoft YaHei, WenQuanYi Micro Hei, sans-serif;
            font-size: 34px;
            font-weight: 700;
            line-height: 1.3;
        }

        .note .post .article .author {
            margin: 30px 0 40px;
        }

        .note .post .article .author .avatar img {
            width: 48px;
            height: 48px;

        }

        .note .post .article .author .info {
            vertical-align: middle;
            display: inline-block;
            margin-left: 8px;
        }

        .note .post .article .author .tag {
            padding: 1px 2px;
            font-size: 12px;
            color: #ea6f5a;
            border: 1px solid #ea6f5a;
            border-radius: 3px;
        }

        .note .post .article .author .name {
            margin-left: 3px;
            margin-right: 3px;
            font-size: 16px;
            vertical-align: middle;
        }

        .note .post .article .author .meta {
            margin-top: 5px;
            font-size: 12px;
            color: #969696;
        }

        .note .post .article .author .meta span {
            padding-right: 5px;
        }

        .note .post .article .show-content {
            color: #2f2f2f;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.7;
        }

        .note .post .article .show-content blockquote {
            padding: 20px;
            margin-bottom: 25px;
            background-color: #f7f7f7;
            border-left: 6px solid #b4b4b4;
            font-size: 16px;
            font-weight: 400;
            line-height: 30px;
        }

        .note .post .article .show-foot {
            margin: 40px 0 30px;
        }

        .tags {

            padding: 0;
        }

        .tags li {

            display: inline-block;
        }

        .tags li a {

            text-decoration: none;
        }

        .tags .tag-head {
            color: #ff7e00;

        }

        .tags .tag {

            margin: 10px;
            padding: 5px 8px;
            border-radius: 5px;
            color: whitesmoke;

            background-color: #ea6f5a;

        }

        .pull-right > div {

            display: inline-block;

        }

        .note .post .article .show-foot .copyright {
            margin-top: 5px;
            font-size: 12px;
            line-height: 1.7;
            color: #9b9b9b;
        }

        .note .post .article .show-foot .modal-wrap {
            margin-top: 5px;
            margin-right: 20px;
            font-size: 12px;
            line-height: 1.7;
        }

        .note .post .article .show-foot .modal-wrap > a {
            color: #9b9b9b;
        }

        .note .post .follow-detail {
            padding: 20px;
            background-color: hsla(0, 0%, 71%, .1);
            border: 1px solid #e1e1e1;
            border-radius: 4px;
            font-size: 12px;
        }

        .note .post .follow-detail .info {
            min-height: 47px;
        }

        .note .post .follow-detail .avatar {
            float: left;
            margin-right: 10px;
            width: 48px;
            height: 48px;
        }

        .note .post .follow-detail .btn {
            float: right;
            margin-top: 4px;
            padding: 8px 0;
            width: 100px;
            font-size: 16px;
        }

        .note .post .follow-detail .info .title {
            margin: 0;
            font-size: 17px;
            line-height: 1.8;
        }

        .note .post .follow-detail .signature {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e1e1;
        }

        .note .post .meta-bottom {
            margin-top: 30px;
        }

        .note .post .meta-bottom .like {
            display: inline-block;
        }

        .note .post .meta-bottom .like-group {
            padding: 13px 0 15px;
            font-size: 0;
            border: 1px solid #ea6f5a;
            border-radius: 40px;
        }

        .note .post .meta-bottom .like-group .btn-like {
            font-size: 19px;
            display: inline-block;
        }

        .note .post .meta-bottom .like-group.liked {
            background-color: #ea6f5a;
        }

        .note .post .meta-bottom .like-group.liked .btn-like > a {
            color: #fff;
        }

        .note .post .meta-bottom .like-group.liked .modal-wrap {
            border-left: 1px solid #fff;
        }

        .note .post .meta-bottom .like-group.liked .modal-wrap > a {
            color: #fff;
        }

        .note .post .meta-bottom .like-group .btn-like a {
            color: #ea6f5a;
            padding: 18px 15px 18px 30px;
        }

        .note .post .meta-bottom .like-group .modal-wrap {
            font-size: 18px;
            border-left: 1px solid rgba(236, 97, 73, .4);
            display: inline-block;
        }

        .note .post .meta-bottom .like-group .modal-wrap a {
            color: #ea6f5a;
            padding: 18px 30px 18px 17px;
        }

        .note .post .meta-bottom .share-group {
            float: right;
            margin-top: 6px;
        }

        .note .post .meta-bottom .share-group .share-circle {
            width: 50px;
            height: 50px;
            margin-left: 5px;
            text-align: center;
            border: 1px solid #dcdcdc;
            border-radius: 50%;
            vertical-align: middle;
            display: inline-block;
        }

        .note .post .meta-bottom .share-group .share-circle {
            width: 50px;
            height: 50px;
            margin-left: 5px;
            text-align: center;
            border: 1px solid #dcdcdc;
            border-radius: 50%;
            vertical-align: middle;
            display: inline-block;
        }

        .note .post .meta-bottom .share-group .more-share {
            width: auto;
            padding: 4px 18px;
            font-size: 14px;
            color: #9b9b9b;
            line-height: 40px;
            border-radius: 50px;
        }

        .note .post .meta-bottom .share-group .share-circle i {
            margin-top: 10px;
            font-size: 24px;
            line-height: 2;
        }

        .note .post .meta-bottom .share-group .share-circle .ic-wechat {
            color: #00bb29;
        }

        .note .post .meta-bottom .share-group .share-circle .ic-weibo {
            color: #e05244;
        }

    </style>

@endsection

@section('content')

    <div class="note">
        <div class="post">
            <div class="article">
                <h1 class="title">{{$article->article_title}}</h1>


            <?php $user = $dynamic->author  ?>
            <!-- 作者区域 -->
                <div class="author">
                    <a class="avatar" href="/u/{{$user->user_id}}">
                        <img src="{{url('/getfile')}}?key={{$user->avatar_key}}" alt="用户头像"/>
                    </a>
                    <div class="info">
                        <span class="tag">作者</span>
                        <span class="name"><a href="/u/{{$user->user_id}}">{{$user->username}}</a></span>
                        <!-- 关注用户按钮 -->

                        @if(empty(session('user'))||$user->user_id!=session('user')->user_id)


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

                    {!! $article->article_content !!}

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
                    <a class="avatar" href="/u/{{$dynamic->author->user_id}}">
                        <img src="{{url('/getfile')}}?key={{$dynamic->author->avatar_key}}" alt="头像">
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
                    <span>{{$dynamic->author->introduce}}</span>
                </div>
            </div>

            <div class="meta-bottom">


                <div class="like">
                    <div class="btn like-group">
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
            doLike(thiss, url, data, likeNumDom);

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