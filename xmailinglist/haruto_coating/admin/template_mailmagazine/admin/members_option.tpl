        <div class="mailingList__messeage">
            ユーザーの設定を変更できます。
        </div>

        <div class="section">
            <h3 class="section__ttl">ユーザーの設定変更</h3>
            <div class="section__body">
                <div class="error_txt">
                    {$error_txt}
                </div>
                <form method="post" name="members_option" action="./?page=MembersOptionDo">
                    <table class="table">
                        <tr>
                            <th>メールアドレス</th>
                            <td>{$user_mail}</td>
                        </tr>
					    <tr>
						    <th>メモ</th>
                            <td>
                                <input name="user_memo" type="text" style="width:320px" value="{$user_memo}" />
                                <br />入力内容の指定はありません。メールアドレスの利用者名を設定するなど、ご自由にお使いください。
                            </td>
						</tr>
                    </table>
                    <div class="tac">
                        <input type="hidden" name="user_id" value="{$user_id}" />
                        <input type="hidden" name="user_mail" value="{$user_mail}" />
                        <input type="submit" name="sb_setting_save" value="設定を保存する" />
                    </div>
                </form>
                
                <a href="./">[ユーザー管理に戻る]</a>
            </div>
        </div>
    </div>
    <!-- /#main -->