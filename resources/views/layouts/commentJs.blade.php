{{--用于评论相关的clone--}}
<div class="for-clone hide">

    {{--为添加评论clone--}}
    <div id="comment-placeholder" class="comment">

        <div>
            <div class="author">
                <a href="#" target="_blank" class="avatar">
                    <img @if(!empty(session('user'))) src="{{url('/getfile')}}?key={{session('user')->avatar_key}}" @endif>
                </a>

                <div class="info">
                    <a href="" target="_blank"
                       class="name">@if(!empty(session('user'))) {{session('user')->username}} @endif</a>
                    <div class="meta">
                        <span class="time"></span>
                    </div>
                </div>
            </div>

            <div class="comment-wrap">

                <p class="content"></p>

                <div class="tool-group tool-group-wrapper">
                    <a class="do-like" href="javascript:void(0);">
                        <i class="iconfont ic-zan"></i> <span
                                class="like-num">赞</span>
                    </a>
                    <a class="reply-comment"><i
                                class="iconfont ic-comment"></i>
                        <span
                                class="reply-num">回复</span></a>
                    <a class="del hide"><span>删除</span></a>

                    <a class="pack-up hide" is-pack-up="true"><span>收起评论</span></a>
                </div>
            </div>
        </div>

        {{--评论的评论展示区--}}
        <div class="sub-comment-list hide comment-list-wrapper">

            <input hidden class="page" value="1">
            <input hidden class="offset" value="0">
            {{--<div style="margin: 10px 0;"><a style="color:#ff8140" class="btn">添加新评论</a></div>--}}

            <div class="just-comment-list">
                {{--论的评论区域--}}

            </div>


            <div style="margin-top:10px;">

                <a style="color:#666" class="pack-up-1">收起评论 </a>

                <a class="line-warp load-more load-more-1 more-comment hide" style="color: #666"> | 加载更多</a>

            </div>


        </div>

    </div>

    {{--为评论的评论clone--}}
    <div id="sub-comment-placeholder" class="sub-comment">

        <p>
            <a href="#" target="_blank"
               class="comment-user"></a>：{{--给评论发出者的链接--}}
            <span class="content"><a href="#"
                                     class="maleskine-author"
                                     target="_blank"></a>
                                </span>
        </p>
        <div class="sub-tool-group tool-group-wrapper">
            <span class="time"></span>
            <a class="reply-comment reply-comment1">
                <i class="iconfont ic-comment"></i>
                <span>回复</span>
            </a>

            <a class="del hide"><span>删除</span></a>
            {{--<a class="report"><span>举报</span></a>--}}
        </div>
    </div>

    {{--用于回复用户的评论--}}
    <div class="add-comment reply-comment-wrapper hide">
        <form class="new-comment-form new-comment"
              data-toggle="validator">

            {{csrf_field()}}
            <input hidden name="comment_target">
            <input hidden class="comment-pid" name="comment_pid">

            <div class="form-group has-feedback">

                                <textarea placeholder="写下你的评论..." class="form-control new-comment-textarea"
                                          name="comment_content" data-maxlength="200"></textarea>
                <div class="help-block with-errors"></div>

            </div>

            <div class="write-function-block">
                <div class="emoji-modal-wrap">
                    <a class="emoji">
                        <i class="iconfont ic-comment-emotions"></i>
                    </a>
                </div>

                <input type="submit" class="btn btn-send"
                       value="发送">
                <a class="cancel">取消</a>
            </div>
        </form>
    </div>


    <div class="add-comment new-comment-wrapper">
        @if(!empty(session('user')))

            <form class="new-comment-form new-comment"
                  data-toggle="validator">
                {{csrf_field()}}
                <input hidden name="comment_target">
                <input hidden name="comment_pid" value="0">

                <a class="avatar" href="/u/{{session('user')->user_id}}">
                    <img src="{{url('getfile')}}?key={{session('user')->avatar_key}}">
                </a>

                <div class="form-group has-feedback">

                                <textarea placeholder="写下你的评论..." class="form-control new-comment-textarea"
                                          name="comment_content" data-maxlength="200"></textarea>
                    <div class="help-block with-errors"></div>

                </div>

                <div class="write-function-block">
                    <div class="emoji-modal-wrap">
                        <a class="emoji">
                            <i class="iconfont ic-comment-emotions"></i>
                        </a>
                    </div>

                    <input type="submit" class="btn btn-send" value="发送">
                    <a class="cancel">取消</a>
                </div>
            </form>

        @else

            <div>请<a onclick="$('#for-login').click();">登录</a>后评论</div>

        @endif

    </div>

</div>


<script>

    /*评论相关js*/


    function initComment(selector) {


        var thiss = $(selector);


        var normalCommentList = thiss.find('.normal-comment-list');

        startComment(normalCommentList, $(".for-clone").find(".new-comment-wrapper").clone());

        /*初始化评论  加载第一页评论*/
        $(normalCommentList).find(".load-more-0").click();

    }


    function startComment(normalCommentList, newCommentWrapper) {


        $(".reply-comment-wrapper").each(function () {

            if ($(this).attr('hasadd') === undefined || $(this).attr('hasadd') == 0) {
                setAddCommentEvent($(this));
                $(this).attr('hasadd', 1);

            }
        });

        normalCommentList.parent(".comment-list").prepend(newCommentWrapper);

        setAddCommentEvent(newCommentWrapper);


        /*为给定的添加评论输入框 DOM对象添加事件*/
        function setAddCommentEvent(thiss) {

            var type;

            if ($(thiss).hasClass('reply-comment-wrapper')) {

                type = 1;//回复评论(包括评论的评论)

            } else {

                type = 0;//添加评论

            }


            var textarea = $(thiss).find('.new-comment-textarea');
            var writeFunctionBlock = $(thiss).find('.write-function-block');
            var newCommentForm = $(thiss).find('.new-comment-form');


            $(thiss).find('.emoji').SinaEmotion(textarea);

            $(thiss).find('.cancel').click(function () {

                if (type === 1) {

                    /*回复评论的评论*/
                    $(thiss).addClass('hide');

                    /* if ($(newCommentForm).parents(".add-comment").prev('.just-comment-list').children(".sub-comment").length < 1) {
                     $(newCommentForm).parents('.sub-comment-list').addClass('hide');
                     }*/


                } else if (type === 0) {

                    $(textarea).val('');
                    $(writeFunctionBlock).hide();

                }

            });

            $(textarea).click(function () {

                $(writeFunctionBlock).show();
            });


            $(newCommentForm).on('submit', function (e) {


                if (!isLogin()) {
                    return false;
                }

                e.preventDefault();//取消默认操作


                var targetId = $(newCommentForm).parents(".comment-list").attr("target_id");


                $(newCommentForm).find("input[name='comment_target']").val(targetId);

                if ($(newCommentForm).find('.btn-send').hasClass('disabled') || getObjLength(textarea) < 1) {

                    alertMessage('请检查你的输入', 0);

                } else {

                    /*添加 评论到后台 并插入dom*/
                    var formData = new FormData($(newCommentForm)[0]);
                    $.ajax({
                        url: "{{url('comment')}}",
                        data: formData,
                        dataType: 'json',
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function (resp) {

                            if (resp.state == 0) {

                                insertComment(resp, resp.comment_pid, 1);//将评论插入

                                $(newCommentForm).find('.cancel').click();

                                $(newCommentForm.find('.new-comment-textarea')).val('');

                                //设置offset
                                var commentListDom, offset, offsetVal;

                                var commentNumDom = $(newCommentForm).parents(".comment-list").find(".comment-num");

                                if (type === 0) {

                                    resetNum(commentNumDom, 'html', 'add');
                                    //$(commentNumDom).html(parseInt($(commentNumDom).html()) + 1);

                                } else {
                                    commentListDom = $(newCommentForm).parents(".sub-comment-list");

                                    var replyNumDom = $(commentListDom).parents(".comment-wrapper").find(".reply-num");

                                    resetNum(replyNumDom, 'html', 'add');
                                    resetNum(commentNumDom, 'html', 'add');
                                    /* var replyNumVal = $(replyNumDom).html();

                                     replyNumVal = replyNumVal == '回复' ? 0 : parseInt(replyNumVal);
                                     replyNumDom.html(replyNumVal + 1);*/


                                }


                                offset = $(commentListDom).children(".offset");
                                offsetVal = parseInt(offset.val());
                                $(offset).val(offsetVal + 1);
                                //end 设置 offset

                                alertMessage('评论成功', 1);

                            } else {

                                alertMessage(resp.msg, -1);

                            }
                        }

                    });


                }
            });

        }


        /**
         *给定评论的数据,将它插入到DOM中合适的位置
         *插入位置 0从后面插入  1从前面插入
         */
        function insertComment(data, pid, insertLocation) {

            var type = pid;

            type = (type > 0) ? 1 : 0;

            var commentsPlaceholder;
            var commentObj;

            var justCommentList;

            if (type === 0) {

                commentsPlaceholder = $(".for-clone").find("#comment-placeholder").clone();

                justCommentList = $(normalCommentList).find('.just-comment-list')[0];


            } else if (type === 1) {

                var commentDom = $("#comment-" + pid);
                commentsPlaceholder = $(".for-clone").find("#sub-comment-placeholder").clone();
                justCommentList = $(commentDom).find('.just-comment-list');

            }

            commentObj = setComment(commentsPlaceholder, data, type);


            if (insertLocation === 0) {


                $(justCommentList).append(commentObj);

            } else if (insertLocation === 1) {


                $(justCommentList).prepend(commentObj);


            }


            if ($(justCommentList).parent(".hide").length > 0) {

                $(justCommentList).parent(".hide").removeClass("hide");

            }

            analyticShowTime(commentObj.find('.time')); // 解析时间
            analyticTargetEmotion(commentObj.find('.content'));//解析表情*!/

        }


        /**
         * 将给定的数据装入评论dom对象 并返回
         * */
        function setComment(commentsPlaceholder, data, type) {


            commentsPlaceholder.attr('user_id', data.user_id);
            commentsPlaceholder.addClass("comment-wrapper");

            if (type === 0) {

                var avatar = commentsPlaceholder.find('.avatar');//头像
                var content = commentsPlaceholder.find('.content');//评论内容
                var likeNum = commentsPlaceholder.find('.like-num');//点赞数
                var replyNum = commentsPlaceholder.find('.reply-num');//回复数

                avatar.children("img").attr('src', "{{url('getfile')}}?key=" + data.avatar_key);
                avatar.attr('href', "{{url('u')}}/" + data.user_id);
                commentsPlaceholder.find('.name').html(data.username).attr('href', "{{url('u')}}/" + data.user_id);
                commentsPlaceholder.find('.time').attr('data-shared-at', data.comment_time);
                content.html(data.comment_content);

                likeNum.html((parseInt(data.like_num) == 0) ? '赞' : data.like_num);
                replyNum.html((parseInt(data.reply_num) == 0) ? '回复' : data.reply_num);

                commentsPlaceholder.attr('id', 'comment-' + data.comment_id);


            } else if (type === 1) {

                var commentUser = commentsPlaceholder.find('.comment-user');
                var maleskineAuthor = commentsPlaceholder.find(".maleskine-author");


                $(commentUser).attr('href', "{{url('u')}}/" + data.user_id);
                $(commentUser).html(data.username);
                commentsPlaceholder.find('.time').attr('data-shared-at', data.comment_time);
                commentsPlaceholder.attr('id', 'comment-' + data.comment_id);

                if (data.comment_content[0] == "@") {

                    var str = data.comment_content;
                    var username = str.substring(1, str.indexOf(' ') + 1);

                    $(maleskineAuthor).attr('href', "{{url('u')}}/" + username);


                    $(maleskineAuthor).html('@' + username);

                    $(maleskineAuthor).after(str.substring(str.indexOf(' ') + 1));


                } else {
                    maleskineAuthor.parent().html(data.comment_content);

                }
            }

            return commentsPlaceholder;

        }


        /**向服务器请求评论并将评论插入到html页面中
         * @param int pid 请求的评论的pid
         * *setComment()*/
        function setCommentDom(pid, page, targetId) {


            var offsetDom, offsetVal;

            pid = (pid === undefined) ? 0 : parseInt(pid);

            //printLog(pid);

            offsetDom = (pid === 0) ? $(normalCommentList).children('.offset') : $("#comment-" + pid).find('.offset');
            offsetVal = parseInt(offsetDom.val());

            $.ajax({
                url: "{{url('comment')}}",
                data: {
                    dynamic_id: targetId,
                    comment_pid: pid,
                    page: page,
                    offset: offsetVal
                },
                dataType: 'json',
                type: 'get',
                success: function (resp) {

                    if (resp.state != 0) {

                        alertMessage('无法加载评论', -1);
                        return false;

                    }

                    var commentList = resp.commentList;

                    for (var i = 0; i < commentList.length; i++) {

                        insertComment(commentList[i], pid, 0);

                        if (parseInt(commentList[i].reply_num) > 0) {

                            // setCommentDom(commentList[i].comment_id);
                            $("#comment-" + commentList[i].comment_id).find(".load-more").click();
                            $("#comment-" + commentList[i].comment_id).find('.pack-up').attr('is-pack-up', 'false')

                        }

                    }


                    if (pid === 0) {

                        if (resp.isAll == 1) {
                            /*已经加载完*/

                            var newCommentTips = $(".new-comment-tips");
                            newCommentTips.find(".load-more").addClass('hide');
                            //newCommentTips.find(".all-complete-tip").removeClass('hide');
                        }

                    } else {

                        var moreCommentTipsDom = $("#comment-" + pid).find(".more-comment");

                        if (resp.isAll != 1) {

                            $(moreCommentTipsDom).removeClass('hide');

                        } else {
                            $(moreCommentTipsDom).addClass("hide");
                        }

                    }

                }
            });
        }


        /*点击回复 弹出恢复框*/
        $(normalCommentList).on('click', '.reply-comment', function () {


            if (!isLogin()) {
                return false;
            }

            var current = $(this);


            /*type 0 表示点击的是 评论 的 回复  1表示点击的是 评论的评论 的 回复*/
            var type = $(current).hasClass('reply-comment1') ? 1 : 0;


            var commentDom = $(current).parents('.comment');

            var commentPid = getAfterStr(commentDom.attr('id'), '-');//得到id
            var subCommentList = $(commentDom).find('.sub-comment-list');


            var newReplyComment = $(".reply-comment-wrapper");//添加评论输入框部分

            // setAddCommentEvent(newReplyComment);


            newReplyComment.removeClass("hide");

            var commentTextarea = $(newReplyComment).find('.new-comment-textarea');


            if (type === 0) {

                $(subCommentList).prepend(newReplyComment);
                $(commentTextarea).val('');

            } else if (type === 1) {

                commentTextarea.val("@" + commentDom.find(".comment-user").html() + " ");
                $(current).parents(".sub-comment").append(newReplyComment);

            }

            $(commentDom).find('.comment-pid').val(commentPid);//设置form pid
            subCommentList.removeClass('hide');
            newReplyComment.removeClass('hide');
            commentDom.find('.pack-up').attr('is-pack-up', 'false');
            $(commentTextarea).click();

        });


        /*加载更多评论*/
        $(normalCommentList).on('click', '.load-more', function () {


            var thiss = $(this);

            var pageDom, pageVal, commentListDom, pid;

            if (thiss.hasClass("load-more-0")) {

                commentListDom = $(thiss).parents(".comment-list").find(".normal-comment-list");
                pid = 0;

            } else if (thiss.hasClass("load-more-1")) {

                commentListDom = thiss.parents(".sub-comment-list");

                pid = getAfterStr(commentListDom.parent(".comment").attr('id'), '-');

            }

            pageDom = $(commentListDom).children(".page");
            pageVal = parseInt(pageDom.val());


            var targetId = $(thiss).parents(".comment-list").attr("target_id");
            setCommentDom(pid, pageVal, targetId);

            pageDom.val(pageVal + 1);


        });


        /*删除 和收起*/
        $(normalCommentList).on('mouseenter', '.tool-group-wrapper', function () {

            var thiss = $(this);


            @if(!empty(session('user')))
            /*显示删除按钮*/
            var userId = $(thiss.parents(".comment-wrapper")[0]).attr('user_id');

            if (userId == '{{session('user')->user_id}}') {

                $(this).find('.del').removeClass('hide');
            }

                    @endif

            var packUpDom = $(thiss).find('.pack-up');

            if ($(packUpDom).attr("is-pack-up") == 'false') {

                $(packUpDom).removeClass('hide');

            }

        });


        /*删除 和收起*/
        $(normalCommentList).on('mouseleave', '.tool-group-wrapper', function () {


            $(this).find('.del').addClass('hide');
            $(this).find('.pack-up').addClass('hide');

        });


        /*删除*/
        $(normalCommentList).on('click', '.tool-group-wrapper .del', function () {



            var thiss = $(this);
            var commentDom = $(thiss.parents('.comment-wrapper')[0]);

            var id = getAfterStr($(commentDom).attr('id'), '-');

            $.ajax({

                url: '{{url('comment')}}/' + id,
                type: 'post',
                dataType: 'json',
                data: {
                    '_token': '{{csrf_token()}}',
                    'action': 'delete'
                },

                success: function (resp) {

                    var justCommentList = $(commentDom).parent(".just-comment-list");

                    var commentListWrapper = justCommentList.parent(".comment-list-wrapper");


                    var replyNumVal = commentDom.find(".reply-num").html();
                    replyNumVal = isInteger(replyNumVal) ? parseInt(replyNumVal) : 0;
                    var commentNumDom = commentDom.parents(".comment-list").find(".comment-num");
                    var replyCommentWrapper = commentDom.find(".reply-comment-wrapper");


                    if (replyCommentWrapper.length > 0) {
                        $(".for-clone").prepend(replyCommentWrapper);
                    }

                    if (resp.state == 0) {

                        commentDom.slideUp(1000, function () {

                            commentDom.remove();

                            if (justCommentList.children().length < 1) {

                                commentListWrapper.addClass('hide');

                            }

                            if (commentListWrapper.hasClass("normal-comment-list")) {

                                var commentNumVal = isInteger(commentNumDom.html()) ? parseInt(commentNumDom.html()) : 0;

                                var res = commentNumVal - replyNumVal - 1 >= 0 ? commentNumVal - replyNumVal - 1 : 0;

                                commentNumDom.html(res);

                            } else {

                                /*删除的是评论的评论*/
                                resetNum(commentListWrapper.parents(".comment").find(".reply-num"), 'html', 'sub');


                            }


                        });


                    } else {

                        alertMessage(resp.msg, -1);
                    }
                }

            })


        });


        /*收起评论*/
        $(normalCommentList).on('click', '.tool-group-wrapper .pack-up', function () {

            var thiss = $(this);
            var commentDom = $(thiss.parents('.comment-wrapper')[0]);
            var replyComment = thiss.parent(".tool-group-wrapper").find(".reply-comment");


            if ($(thiss).attr("is-pack-up") == 'false') {

                /*未收起 执行收起操作*/
                $(commentDom).find(".sub-comment-list").addClass("hide");
                $(thiss).attr("is-pack-up", 'true');//已收起
                $(thiss).addClass("hide");

            }
            /*else{
             /!*yi收起 执行收zankai操作*!/
             $(commentDom).find(".sub-comment-list").removeClass("hide");
             $(thiss).attr("is-pack-up", 'false');//wei收起
             $(thiss).html('收起评论');
             $(thiss).addClass("hide");

             }*/


        });

        $(normalCommentList).on("click", ".pack-up-1", function () {

            var thiss = $(this);

            $(thiss).parents(".comment-wrapper").find(".pack-up").click();

        });
        $(normalCommentList).on('click', '.do-like', function () {

            var thiss = $(this);

            var url = '{{url('dolike')}}';

            var postData = {

                '_token': '{{csrf_token()}}',
                'target_id': getAfterStr(thiss.parents(".comment").attr('id'), '-'),
                'target_type': 1

            };
            doLike(thiss, url, postData, thiss.find(".like-num"));
        });
    }
</script>