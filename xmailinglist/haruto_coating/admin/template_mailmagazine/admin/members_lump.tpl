        <div class="mailingList__messeage">
            メールマガジンのユーザーを一括で登録または登録確認メールを一括で送信できます。
        </div>

        <div class="section">
            <h3 class="section__ttl">ユーザーの一括登録</h3>
            <div class="section__body">
                <div class="error_txt">
                    {$error_txt}
                </div>
                <form method="post" action="./?page=MembersAddLump">
                    <table class="table">
                        <tr>
                            <th>メールアドレス,メモ</th>
                            <td class="lump_field">
                                <p>※1行あたり1件ずつ（メールアドレスとメモをカンマ区切りで）入力して、改行後次のデータを入力して下さい。<br />
                                    例．taro@test.xsrv.jp,メモ</p>
                                <textarea name="user_mail_lump" cols="75" rows="15">{$user_mail_lump}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>登録方法</th>
                            <td>
                                <select name="register_mode">
                                    <option value="1">今すぐ登録して登録完了通知を送信する</option>
                                    <option value="2">今すぐ登録して登録完了通知を送信しない</option>
                                    <option value="3">登録用URLを送信し、ユーザの了解を得て登録する</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                
                            </td>
                        </tr>
                    </table>
                    <div class="tac">
                    <input type="submit" name="sb_ml_member_add" value="メールマガジンに登録する" {disabled_1}/>
                    </div>
                    
                    <a href="./">[ユーザーの管理に戻る]</a>

                </form>
            </div>
        </div>
    </div>
    <!-- /#main -->