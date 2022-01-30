        <div class="mailingList__messeage">
            メンバー宛にメール送信を行うことができます。
        </div>

        <form name="mail_menu" method="post" action="./?page=MailDo">
            
        <div class="section">
            <h3 class="section__ttl">メール作成</h3>
            <div class="section__body">
                <table class="table">
                    <tr>
                        <th>件名</th>
                        <td>
                            <span>{$subject_txt}</span>
                            {$mail_subject}
                            <input type="hidden" name="mail_subject" value="{$mail_subject}" />
                        </td>
                    </tr>
                    <tr>
                        <th>メール本文</th>
                        <td>
                            <div>{$header_txt}</div>
                            {$mail_view_contents}
                            <div>{$footer_txt}</div>
                            <input type="hidden" name="mail_contents" value="{$mail_contents}" />
                        </td>
                    </tr>
                </table>
                
                <div class="button_box tac">
                    <input type="submit" name="submit_button" value="戻る" />
                    <input type="submit" name="sb_mail_send" value="メールを送信する" />
                </div>
                <!-- /button_box -->
            </div>
        </div>
        </form>
    </div>
    <!-- /#main -->
