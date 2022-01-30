<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="copyright" content="Copyright Xserver Inc.">
<title>Xserverビジネス/メールマガジン/{$ml_app_name}</title>
<link type="text/css" rel="stylesheet" href="css/layout_mm.css?ver=1.2.1">
<link type="text/css" rel="stylesheet" href="css/themecolor.css?ver=1.2.1" />
<script type="text/javascript" src="./js/common_func.js"></script>
</head>

<body>
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
    
    <div id="target_address">
    </div>
    <!-- /target_address -->
    
    <div id="main">
        <form name="main_menu" method="post" action="./">
        <div class="section">
            <h3 class="section__ttl">管理ツールログイン</h3>
            <div class="section__body">
                <table class="table">
                    <tr>
                        <th scope="row">メールマガジンアドレス</th>
                        <td><input type="text" name="username" size="40" /></td>
                    </tr>
                    <tr>
                        <th scope="row">パスワード</th>
                        <td><input type="password" name="password" size="40" /></td>
                    </tr>
                    {DEBUG_MODE}
                </table>
                <p class="tac"><input type="submit" name="login_button" value="ログイン" /></p>
                {$error_txt}
            </div>
        </div>
        </form>
    </div>
    <!-- /#main -->
    
            
    <footer id="footer">
        &copy; 2013-2021 XSERVER Inc. All rights reserved.
    </footer>
    <!-- /#footer -->
</div>
<!-- /#wrapper -->
</body>
</html>