            <div class="inner">

                <form method="post" action="./?page=ApplyDo">
                    <h4>退会手続き</h4>
                    <p>
                        ご記入のメールアドレス宛へ退会申し込みの確認メールを送信します。<br />
                        <div class="red_txt li">{error_txt}</div>
                    </p>
                    <table class="menu_table">
                        <tr>
                            <th>メールアドレス</th>
                            <td><input type="text" name="delete_mail" value="" /></td>
                        </tr>
                    </table>

                    <div class="button_box">
                        <input type="hidden" name="identity" value="{identity}" />
                        <input type="submit" name="submit_button" value="メールマガジンから退会する" onclick="return DeleteMailMsg();" />
                    </div>
                </form>

            </div>
            <!-- /inner -->
