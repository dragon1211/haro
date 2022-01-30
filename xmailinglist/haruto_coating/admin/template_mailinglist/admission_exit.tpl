<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Style-Type" content="text/css;" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <title>メーリングリスト/{$ml_app_name}</title>
    <link type="text/css" rel="stylesheet" href="css/layout.css?ver=1.2.1" />
    <link type="text/css" rel="stylesheet" href="css/themecolor.css?ver=1.2.1" />
</head>

<body>

<div id="contents_wrapper">

    <div id="header">
        <h1><a href="./">{$ml_app_name}</a></h1>
    </div>
    <!-- /header -->

    <div id="contents">

        <div id="main">
            入会を完了しました。<br />
        </div>
        <input type="button" value="閉じる" onclick="window.close()" style="width:200px; height:20px;">
        <br />
        <br />
        {$exit_url}
        <!--<a href="{$withdraw_url}?page=Apply">退会用URL</a>-->
    </div>
</div>

</body>
</html>

