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
        <h1>{$ml_app_name}</h1>
    </div>
    <!-- /header -->

    <div id="contents">

        <div id="main">
            メーリングリストに入会希望の方は、下記のボタンを押してください。<br />
            登録メールアドレス：{$user_mail}<br />
            <form method="post" action="./?page=AdmissionDo">
                <input type="hidden" name="user_mail" value="{$user_mail}" />
                <input type="hidden" name="param" value="{$param}" />
                <input type="submit" name="sb_reg_ml" value="メーリングリストに入会する" />
            </form>
        </div>

    </div>
</div>

</body>
</html>

