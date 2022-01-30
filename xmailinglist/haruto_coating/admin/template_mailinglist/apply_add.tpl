            <div class="inner">

                <form method="post" action="./?page=ApplyDo">
                    <h4>入会手続き</h4>
                    <p>
                        ご記入のメールアドレス宛へ入会の確認メールを送信します。
                        <div class="red_txt li">{error_txt}</div>
                    </p>
                    <table class="menu_table">
                        <tr>
                            <th>メールアドレス</th>
                            <td><input type="text" name="add_mail" value="" /></td>
                        </tr>
                    </table>
                    
                    <div class="button_box">
                        <input type="hidden" name="identity" value="{identity}" />
                        <input type="submit" name="sb_reg_ml" value="メーリングリストに入会する" onclick="return AddMailMsg();" />
                    </div>
                </form>

            </div>
            <!-- /inner -->
