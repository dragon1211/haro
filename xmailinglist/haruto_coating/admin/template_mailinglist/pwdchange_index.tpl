            <div class="inner">
                <h4>パスワードの変更</h4>

                ログイン時に使用するパスワードを変更できます。<br />
                <form name="main_menu" method="post" action="./?page=PwdChangeDo">
                <div class="pwdchange_section">

                    <div class="red_txt">{$error_txt}</div>

                    <table class="pwdchange_table">
                        <tr>
                            <th scope="row">ユーザーID</th>
                            <td>{$user_id}</td>
                        </tr>
                        <tr>
                            <th scope="row">現在のパスワード</th>
                            <td><input type="password" name="now_password" size="40" value="{$now_password}" /></td>
                        </tr>
                        <tr>
                            <th scope="row">新しいパスワード</th>
                            <td><input type="password" name="change_password1" size="40" value="{$change_password1}" /></td>
                        </tr>
                        <tr>
                            <th scope="row">新しいパスワード(確認)</th>
                            <td><input type="password" name="change_password2" size="40" value="{$change_password2}" /></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="pwdchange_button">
                                    <input type="submit" name="sb_pwd_change" value="パスワードの変更" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /login_section -->

                </form>
            </div>
            <!-- /inner -->
