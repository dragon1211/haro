<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="copyright" content="Copyright Xserver Inc.">
<title>Xserverビジネス/メールマガジン/{$ml_app_name}</title>
<link type="text/css" rel="stylesheet" href="css/layout_mm.css?ver=1.2.1">
<link type="text/css" rel="stylesheet" href="css/themecolor.css?ver=1.2.1" />
<script type="text/javascript" src="./js/jquery-1.7.1.js"></script>
<script type="text/javascript" src="./js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="./js/common_func.js"></script>
<script type="text/javascript" src="./js/design_func.js"></script>

<script>
function mailDisable(){
    if($("input[name=ml_transmit_val]").prop("checked")){
            $("input[name=ml_moderators]").removeAttr("disabled");
    }
    else{
            $("input[name=ml_moderators]").attr("disabled", "disabled");
    }
}
function mMailDisable(){
    if($("input[name=ml_limit_send]").prop("checked")){
            $("input[name=mm_moderators]").removeAttr("disabled");
    }
    else{
            $("input[name=mm_moderators]").attr("disabled", "disabled");
    }
}
$(document).ready(function(){
    $("input[name=ml_transmit_val]").click(mailDisable);
    $("input[name=ml_limit_send]").click(mMailDisable);
    mailDisable();
    mMailDisable();
});
</script>
</head>

<body id="{$select_index}">
<div id="wrapper">
    <header id="header" class="clearfix">
        <h1 class="logo"><a href="./"><img src="images/logo.png" alt="Xserverビジネス"></a></h1>
        <ul class="subNav clearfix">
            <li><a href="./">トップ</a></li>
            <li><a href="https://support.xserver.ne.jp/manual/man_mail_mailmagazine.php" target="blank">マニュアル</a></li>
            <li><a href="./?page=logout">ログアウト</a></li>
        </ul>
    </header>
    <!-- /#header -->
    <h2 class="mailingList__name">{$ml_app_name}</h2>
    
    <div id="main" class="clearfix">
        <ul class="settingNav clearfix">
            <li id="main_navi_members"><a href="./">ユーザー管理</a></li>
            <li id="main_navi_mail"><a href="./?page=MailCreate">メールマガジンの配信</a></li>
            <li id="main_navi_news"><a href="./?page=Article">配信済みの情報</a></li>
            <li id="main_navi_errmail"><a href="./?page=ErrMailList">配信エラー管理</a></li>                    
            <li id="main_navi_option"><a href="./?page=Option">環境設定</a></li>
            <li id="main_navi_file"><a href="./?page=EditSystemMail">システムメール</a></li>
            <li id="main_navi_html" class="list_end"><a href="./?page=SetHtmlTag">自動登録機能</a></li>
        </ul>

        <div style="color:#EE0000">
            <noscript>
                    本ツールは、Javascriptを利用しております。
                    お手数ですが、JavascriptをONにして再読み込みしてください。
            </noscript>
        </div>
        <!-- /main_navi -->