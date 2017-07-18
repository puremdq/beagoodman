<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>联系我</title>

    <style>
        a {

            text-decoration: none;
        }

        #title {

            width: 100%;
            height: 50px;
            margin: 10px auto;
            outline: none;
            border: 0;
            border-bottom: 1px solid #ccc;
            font-size: 20px;
            font-family: "Songti SC";
            color: #111;
            font-weight: 900;
            text-align: center;

        }

        .nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #f8f8f8;

            height: 50px;
        }

        .nav div {

            margin: 15px 50px;
        }

        .logo {
            color: green;
            font-size: large;
        }

        .btn {
            background-color: orange;
            padding: 5px 20px;
            border-radius: 5px;
            color: white;
            margin: auto 15px;
        }
        .content{

            width: 60%;
            padding: 10px 20px;
            margin: 50px auto;
        }

    </style>

</head>
<body>

<div class="nav">

    <div style="float: left">
        <a class="logo" href="{{url('')}}">beagoodman</a>
        <span style="margin-left: 10px;color: #bd3a55;">联系我</span>
    </div>

    <div style="float: right">
        <a class="btn" href="/">返回首页</a>
    </div>
</div>

<div class="content" style="clear: both">
    <p>email:puremdq@163.com 或puremdq@gmail.com</p>
    <p>weibo: <a target="_blank" href="http://weibo.com/puremdq">思中译</a></p>
    <p>github: <a target="_blank" href="https://github.com/puremdq">puremdq</a></p>
</div>


</body>


</html>