<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('style')
</head>
<body style="background:#F3F3F4;">
@yield('content')
</body>
<script src="http://cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<script src="{{url('/libs/layer/layer.js')}}"></script>


<script>
    /*公共方法区*/

    {{--自动弹出提示信息--}}
@if(!empty(session('errorsToAlert')))
    layer.msg('{{session('errorsToAlert')}}', function () {
        //do something
    });
            @endif


            {{--自动为表单元素添加错误提示信息--}}
            @if(count($errors)>0)
            @foreach($errors->toArray() as $name=>$error)
    var errorHtml = '<label class="error">{{$error[0]}}<label>';
    $("[name='{{$name}}']").after(errorHtml).on('click', function () {
        $(this).next('.error').remove();
    });

    @endforeach
    @endif


    {{--为input自动赋值 在页面中调用addValueToInput()--}}
    @if(!empty($formData))
    function addValueToInput() {

        var tempInputObj;
        @foreach($formData->toArray() as $inputName=>$item)
                tempInputObj = $("input[name='{{$inputName}}']");
        if ($(tempInputObj).length > 0) {
            //console.log($(this));
            $(tempInputObj).val('{{$item}}');
        }
        @endforeach

    }
    @endif


    /**
     * @parameter resp json，isReload int
     * @description 自动为ajax弹出消息
     * */
    function alertMessage(resp, isReload) {

        /*console.log(resp);*/
        if (resp.state != 0) {
            /*操作失败*/
            layer.open({
                title: '提示',
                content: resp.msg,
                icon: 5,
                btn: [],
                time: 1500,
                end: function () {
                    location.reload();
                }
            });
        } else {
            /*操作成功*/
            layer.open({
                title: '提示',
                content: resp.msg,
                icon: 6,
                btn: [],
                time: 1500,
                end: function () {
                    if (isReload == 1) {
                        location.reload();
                    }
                }
            });
        }

    }


</script>

<!-- 配置文件 -->
<script type="text/javascript" charset="utf-8" src="/libs/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/libs/ueditor/ueditor.all.min.js"> </script>

<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="/libs/ueditor/lang/zh-cn/zh-cn.js"></script>


@yield('script')
</html>

