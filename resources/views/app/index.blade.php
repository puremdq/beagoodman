@extends('layouts.app')
@section('link')
    <link href="http://static.jasminecjc.com/css/index.min.css" rel="stylesheet">
    <link href="http://static.jasminecjc.com/css/comment.min.css" rel="stylesheet">
@endsection

@section('title', 'beagoodman')

@section('content')
    <div class="container  index" id="all-container">

        <div class="row">
            <div class="col-sm-23 col-xs-offset-1 col-xs-22">

            <?php $dynamicLength = count($dynamics);  ?>

            @if($dynamicLength>0)

                <!--轮播图  显示浏览量最多的文章-->

                    <div id="hot-carousel-generic" class="carousel slide " data-ride="carousel">
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            <?php

                            for ($i = 0; $i < $dynamicLength; $i++) {

                                if ($i === 0) {

                                    echo '<li data-target="#hot-carousel-generic" data-slide-to="0" class="active"></li>';
                                } else {
                                    echo '<li data-target="#hot-carousel-generic" data-slide-to="' . $i . '"></li>';
                                }
                            }

                            $i = 0;

                            ?>
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">

                            <?php $i = 0; ?>
                            @foreach($dynamics as $dynamic)

                                <?php
                                $preview = unserialize($dynamic->preview);
                                $imgUrl = empty($preview['first_img_url']) ? '/img/default.png' : $preview['first_img_url']; ?>

                                <a class="item @if($i==0) active @endif " href="/dynamic/{{$dynamic->dynamic_id}}">
                                    <img src="{{$imgUrl}}">
                                    <div class="carousel-caption">
                                        <h2 href="">{{$preview['title']}}</h2>
                                    </div>
                                </a>

                                <?php $i++; ?>
                            @endforeach

                        </div>

                        <!-- Controls -->
                        <a class="left carousel-control" href="#hot-carousel-generic" role="button" data-slide="prev">
                            <i class="iconfont ic-previous-s"></i>
                        </a>
                        <a class="right carousel-control" href="#hot-carousel-generic" data-slide="next">
                            <i class="iconfont ic-next-s"></i>
                        </a>
                    </div>


                @else
                    <span>文章太少了,无法为你推荐我们正在起步阶段</span>
                @endif
            </div>

            <div class="col-sm-16 col-xs-offset-1 col-xs-22  main">

                <div id="list-container" class="list-container">

                    <input name="page" class="page" hidden="hidden" value="1">
                    <input name="offset" class="offset" hidden="hidden" value="0">

                    <ul class="note-list">
                    </ul>

                    <button class="load-more btn">加载更多</button>

                </div>

            </div>


            <div class="col-sm-7 aside hidden-xs">
                <div class="board">

                    推荐位
                </div>


            </div>


        </div>


    </div>

    @include('layouts.commentJs')
    @include('layouts.dynamicList')

    <footer class="container">
        <div class="row">
            <div class="col-sm-16 col-xs-offset-1 col-xs-22 main">

                <a target="_blank" href="/about">关于本站</a>
                <em> · </em>
                <a target="_blank" href="/contact">联系我们</a>
                <em> · </em>
                <a target="_blank" href="http://yuicer.com">yuicer.com</a>
                <div class="icp">
                    ©2016-2017 <a target="_blank" href="http://beagoodman.cn">beagoodman.cn</a> /  <a target="_blank" href="http://www.beian.gov.cn"> 渝ICP备16008532号-1</a>
                </div>
            </div>
        </div>
    </footer>

@endsection


@section('script')
    <script src="http://cdn.bootcss.com/imageviewer/0.5.1/viewer.min.js"></script>
    <script>

        /*
         analyticShowTime($(this));

         analyticTargetEmotion($(this));*/

        /*初始化*/

        (function () {

            var listContainer = $("#list-container");
            listContainer.on('DOMNodeInserted', '.comment-num', function () {

                var thiss = $(this);
                thiss.parents(".mood").find(".dynamic-comment-num").html(thiss.html());

            });
            startShowDynamic(listContainer);

        })();


    </script>


@endsection
