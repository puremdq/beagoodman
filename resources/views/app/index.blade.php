@extends('layouts.app')
@section('link')
    <link href="/css/app.css" rel="stylesheet">

    <link href="http://cdn.bootcss.com/viewerjs/0.6.2/viewer.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/comment.css">


@endsection

@section('title', 'beagoodman')

@section('content')
    <div class="container" id="all-container">

        <!---->
        <div class="row" style="margin-bottom: 20px">

        <?php $dynamicLength = count($dynamics);  ?>

        @if($dynamicLength>0)

            <!--轮播图  显示浏览量最多的文章-->
                <div class="col-xs-10 col-xs-offset-1">

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

                            ?>
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">

                            @foreach($dynamics as $dynamic)

                                <?php
                                $preview = unserialize($dynamic->preview);
                                $imgUrl = empty($preview['first_img_url']) ? '/img/default.png' : $preview['first_img_url']; ?>

                                <a class="item" href="/dynamic/{{$dynamic->dynamic_id}}">
                                    <img src="{{$imgUrl}}">
                                    <div class="carousel-caption">
                                        <h2 href="">{{$preview['title']}}</h2>
                                    </div>
                                </a>
                            @endforeach

                        </div>

                        <!-- Controls -->
                        <a class="left carousel-control" href="#hot-carousel-generic" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        </a>
                        <a class="right carousel-control" href="#hot-carousel-generic" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        </a>
                    </div>

                </div>

            @else
                <span>文章太少了,无法为你推荐我们正在起步阶段</span>
            @endif

        </div>


        <div class="row">


            <div class="col-sm-7 col-sm-offset-1 .col-xs-10 main">

                <div id="list-container">

                    <input name="page" class="page" hidden="hidden" value="1">
                    <input name="offset" class="offset" hidden="hidden" value="0">

                    <ul class="note-list">
                    </ul>

                    <button class="load-more btn">加载更多</button>

                </div>

            </div>


            <div class="col-sm-2 col-sm-offset-1 hidden-xs aside">
                <div class="board">

                    推荐位
                </div>


            </div>


        </div>


    </div>

    @include('layouts.commentJs')
    @include('layouts.dynamicList')

@endsection

@section('script')

    <script src="http://cdn.bootcss.com/viewerjs/0.6.2/viewer.min.js"></script>
    <script src="/js/jquery-serialize.js"></script>

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


            $("#hot-carousel-generic").find('.item').eq(0).addClass('active');
        })();


    </script>


@endsection
