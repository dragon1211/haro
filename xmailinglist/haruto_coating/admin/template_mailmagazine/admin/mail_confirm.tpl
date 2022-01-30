        <div class="mailingList__messeage">
            メンバー宛に新規メールマガジンの配信を行うことができます。
        </div>

        <form name="mail_menu" method="post" action="./?page=MailDo">
            
        <div class="section">
            <h3 class="section__ttl">メールマガジンの確認</h3>
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
                            <textarea name="mail_contents" cols="70" rows="25" readonly>{$header_txt}{$mail_contents}{$footer_txt}</textarea>
                        </td>
                    </tr>
                </table>
                
                <div class="button_box tac">
                    <input type="hidden" name="header_txt" value="{$header_txt}" />
                    <input type="hidden" name="mail_contents" value="{$mail_contents}" />
                    <input type="hidden" name="footer_txt" value="{$footer_txt}" />
                    <input type="hidden" name="send_contents" value="{$send_contents}" />

                    <INPUT type="submit" id="exe_submit_button1" name="submit_button" value="戻る" onclick="ReturnNow(this);">
                    <INPUT type="submit" id="exe_submit_button2" name="sb_mail_send" value="メールマガジンを配信する" onclick="ExeNow(this);">
                </div>
                <!-- /button_box -->
            </div>
        </div>
        </form>
    </div>
    <!-- /#main -->