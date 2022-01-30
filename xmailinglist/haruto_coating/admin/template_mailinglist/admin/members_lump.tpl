        <div class="mailingList__messeage">
            メーリングリストメンバーを一括で登録または入会確認メールを一括で送信できます。
        </div>

        <div class="section">
            <h3 class="section__ttl">メンバーの一括登録・入会確認</h3>
            <div class="section__body">
                <div class="error_txt">
                    {$error_txt}
                </div>
                <form method="post" action="./?page=MembersAddLump">
                    <table class="table">
                        <tr>
                            <th>
                                メールアドレス,メモ
                            </th>
                            <td class="lump_field">
                                <p class="note">
                                    ※1行あたり1件ずつ（メールアドレスとメモをカンマ区切りで）入力して、改行後次のデータを入力して下さい。<br />
                                    例：taro@test.xsrv.jp,メモ
                                </p>
                                <textarea cols="50" rows="15" name="user_mail_lump">{$user_mail_lump}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>メンバーの権限 </th>
                            <td>
                                <select name="user_mode">
                                    <option value="1">メール受信 + メール配信</option>
                                    <option value="2">メール受信のみ</option>
                                    <option value="3">メール配信のみ</option>
                                </select>
                                <br />[メール受信＋メール配信]権限のメンバーは、メンバー全員にメールを配信することができます。
                            </td>
                        </tr>
                    <tr>
                        <th>登録方法</th>
                        <td>
                            <select name="register_mode">
                                <option value="1">今すぐ登録して登録完了通知を送信する</option>
                                <option value="2">今すぐ登録して登録完了通知を送信しない</option>
                                <option value="3">入会用URLを送信し、ユーザの了解を得て登録する</option>
                            </select>
                            <br />[入会用URLを送信し、ユーザの了解を得て登録する]場合の権限は、環境設定の[自動入会メンバーの権限設定]により設定可能です。
                        </td>
                    </tr>
                    </table>
                    
                    <div class="tac">
                    <input type="submit" name="sb_ml_member_add" value="メーリングリストに登録する" {disabled_1}/>
                    </div>
                    
                    <a href="./">[メンバーの管理に戻る]</a>

                </form>
            </div>
        </div>
    </div>
    <!-- /#main -->