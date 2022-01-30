<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css;" />
<title>メーリングリスト/{$ml_app_name}</title>
<link type="text/css" rel="stylesheet" href="css/layout.css?ver=1.2.1">
<link type="text/css" rel="stylesheet" href="css/themecolor.css?ver=1.2.1" />
</head>

<body>

<div id="contents_wrapper">

    <div id="header">
        <h1>{$ml_app_name}</h1>
    </div>
    <!-- /header -->

    <div class="pagelink_navi">
        <ul>
            <li>[<a href="./">過去ログ履歴</a>]</li>
            {$ml_url_apply}
        </ul>
    </div>
    <!-- /pagelink_navi -->


    <div id="contents">
        <div id="main">

            <div class="inner">

                <form name="main_menu" method="post" action="./">
                <div class="login_section">

                    {$error_txt}

                    <table class="login_table">
                        <tr>
                            <th scope="row">過去ログ保護パスワード</th>
                            <td><input type="password" name="password" size="40" /></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="login_button">
                                    <input type="submit" name="login_button" value="ログイン" />
                                </div>
                            </td>
                        </tr>
                    <input type="hidden" name="username" value="{user_name}" />
                    </table>
                </div>
                <!-- /login_section -->

                </form>

                <div class="center_txt">
                <input type="hidden" name="" value="1" />
                <!-- a href="./?page=PwdIssueIndex">アカウント登録</a><br /><br / --!>
                <!--a href="./?page=PwdChangeIndex">パスワードを忘れてしまった方はこちら</a --!>
                </div>

                <br />

                <div class="center_txt">
                {$admission_txt}
                </div>

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

