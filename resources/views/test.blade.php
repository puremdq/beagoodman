<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>hello</title>
</head>
<body>

<form method="post" action="/forteacherwulike">
    {{csrf_field()}}
    <sapn>请输入要点赞次数</sapn>
    <input type="text" value="10" name="counts">
    <input type="submit">
</form>

</body>
</html>