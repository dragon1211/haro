        <div class="mailingList__messeage">
            メンバー宛にメール送信を行うことができます。
        </div>

        <form method="post" action="./?page=MailConfirm">
        <div class="section">
            <h3 class="section__ttl">メール作成</h3>
            <div class="section__body">
                <div class="error_txt">
                    {$error_txt}
                </div>
                <table class="table">
                    <tr>
                        <th>件名</th>
                        <td>
                            <span>{$subject_txt}</span>
                            <input type="text" name="mail_subject" style="width:320px" value="{$mail_subject}" />
                        </td>
                    </tr>
                    <tr>
                        <th>メール本文</th>
                        <td>
                            <div>{$header_txt}</div>
                            <textarea name="mail_contents">{$mail_contents}</textarea>
                            <div>{$footer_txt}</div>
                        </td>
                    </tr>
                </table>
                
                <div class="button_box tac">
                    <input type="submit" name="sb_confirm" value="確認" />
                </div>
                <!-- /button_box -->
            </div>
        </div>
        </form>
    </div>
    <!-- /#main -->