        <div class="mailingList__messeage">
            ユーザー宛に新規メールマガジンの配信を行うことができます。
        </div>

        <form method="post" action="./?page=MailConfirm">
        <div class="section">
            <h3 class="section__ttl">メールマガジンの新規作成</h3>
            <div class="section__body">
                <div class="error_txt">
                    {$error_txt}
                </div>
                <table class="table">
                    <tr>
                        <th class="w15per">件名</th>
                        <td>
                            <span>{$subject_txt}</span>
                            <input type="text" name="mail_subject" style="width:320px" value="{$mail_subject}" />
                        </td>
                    </tr>
                    <tr>
                        <th>メール本文</th>
                        <td>
                            <div>
                                <textarea name="header_txt" cols="70" rows="2" style="margin-bottom: 5px;">{$header_txt}</textarea>
                                <textarea name="mail_contents" cols="70" rows="25" style="margin-bottom: 5px;">{$mail_contents}</textarea>
                                <textarea name="footer_txt" cols="70" rows="2">{$footer_txt}</textarea>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="button_box tac">
                    <input type="hidden" name="send_contents" value="{$send_contents}" />
                    <input type="submit" name="sb_confirm" value="確認" />
                </div>
                <!-- /button_box -->
            </div>
        </div>
        </form>
    </div>
    <!-- /#main -->