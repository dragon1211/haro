<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css;" />
<title>メーリングリスト/{$ml_app_name}</title>
<link type="text/css" rel="stylesheet" href="css/layout.css?ver=1.2.1">
<link type="text/css" rel="stylesheet" href="css/themecolor.css?ver=1.2.1" />
<script type="text/javascript" src="./js/common_func.js"></script>

</head>

<body>

<div id="contents_wrapper">

    <div id="header">
        <h1>メーリングリスト：ログイン</h1>
    </div>
    <!-- /header -->

    <div class="pagelink_navi">
        <ul>
            <li>[<a href="./">トップページ</a>]</li>
            {$ml_url_apply}
        </ul>
    </div>
    <!-- /pagelink_navi -->


    <div id="contents">
        <div id="main">

            <div class="inner">

                <form method="post" action="./?page=IssuePasswordDo">
                    <h4>メーリングリスト用パスワード再発行フォーム</h4>
                    <p>
                        ご登録のメールアドレス宛にパスワードを送信しました。<br />
                    </p>
                    <div class="red_txt">
                        ※ご記入のメールアドレスが未登録の場合は、メールを送信しておりません
                    </div>
                </form>

            </div>
            <!-- /inner -->

        </div>
        <!-- /main -->

    </div>
    <!-- /contents -->

</div>
<!-- /contents_wrapper -->

</body>
</html>

