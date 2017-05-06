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

            <!--轮播图-->
            <div class="col-xs-10 col-xs-offset-1">

                <div id="carousel-example-generic" class="carousel slide " data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                    </ol>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        <div class="item active">
                            <img src="img/0.jpg" alt="...">
                            <div class="carousel-caption">
                                00000
                            </div>
                        </div>
                        <div class="item">
                            <img src="img/1.jpg" alt="...">
                            <div class="carousel-caption">
                                1111
                            </div>
                        </div>
                        <div class="item">
                            <img src="img/2.jpg" alt="...">
                            <div class="carousel-caption">
                                2222
                            </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

            </div>

        </div>

        <div class="row">


            <div class="col-xs-7 col-xs-offset-1 main">

                <div id="list-container">

                    <input name="page" class="page" hidden="hidden" value="1">
                    <input name="offset" class="offset" hidden="hidden" value="0">

                    <ul class="note-list">
                    </ul>

                    <button class="load-more btn">加载更多</button>

                </div>

            </div>


            <div class="col-xs-2 col-xs-offset-1  aside">
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
        })();


    </script>


@endsection
