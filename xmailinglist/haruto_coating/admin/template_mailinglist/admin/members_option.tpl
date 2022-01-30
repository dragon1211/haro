        <div class="mailingList__messeage">
            メンバーの設定を変更できます。
        </div>

        <div class="section">
            <h3 class="section__ttl">メンバーの設定変更</h3>
            <div class="section__body">
                <div class="error_txt">
                    {$error_txt}
                </div>
                <form method="post" name="members_option" action="./?page=MembersOptionDo">
                    <table class="table">
                        <tr>
                            <th class="w20per">メールアドレス</th>
                            <td>{$user_mail}</td>
                        </tr>
                        <tr>
                            <th>メモ</th>
                            <td>
                                <input name="user_memo" type="text" style="width:320px" value="{$user_memo}" />
                                <p class="mt5 mb0">入力内容の指定はありません。メールアドレスの利用者名を設定するなど、ご自由にお使いください。</p>
                            </td>
                        </tr>
                        <tr>
                            <th>権限</th>
                            <td>
                                <select name="user_mode" style="padding-bottom: 4px;">
                                    <option value="1" {$user_mode_1}>メール受信 + メール配信</option>
                                    <option value="2" {$user_mode_2}>メール受信のみ</option>
                                    <option value="3" {$user_mode_3}>メール配信のみ</option>
                                </select>
                                <p class="mt5 mb0">[メール受信＋メール配信]権限を持つメンバーは、【メーリングリストアドレス】宛てにメールを送信することで、メンバー全員にそのメールを配信できます。</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="tac">
                                    <input type="hidden" name="user_id" value="{$user_id}" />
                                    <input type="hidden" name="user_mail" value="{$user_mail}" />
                                    <input type="submit" name="sb_setting_save" value="設定を保存する" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
                
                <a href="./">[メンバーの管理に戻る]</a>
            </div>
        </div>
    </div>
    <!-- /#main -->