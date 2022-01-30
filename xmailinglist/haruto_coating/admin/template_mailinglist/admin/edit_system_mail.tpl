        <div class="mailingList__messeage">
            メーリングリストのシステムが送信するメールを編集できます。
        </div>

        <div class="section">
            <h3 class="section__ttl">システムメールの編集</h3>
            <div class="section__body">
                <div id="sub_navi">
                    <ul class="clearfix">
                        <li id="sub_navi_file_01"><div id="sub_tag_file_01" class="{$current_file_01}"><a href="javascript:void(0);" onclick="ChangeVewFile(1,4);">入会確認メール</a></div></li>
                        <li id="sub_navi_file_02"><div id="sub_tag_file_02" class="{$current_file_02}"><a href="javascript:void(0);" onclick="ChangeVewFile(2,4);">退会確認メール</a></div></li>
                        <li id="sub_navi_file_03"><div id="sub_tag_file_03" class="{$current_file_03}"><a href="javascript:void(0);" onclick="ChangeVewFile(3,4);">入会完了メール</a></div></li>
                        <li id="sub_navi_file_04"><div id="sub_tag_file_04" class="{$current_file_04}"><a href="javascript:void(0);" onclick="ChangeVewFile(4,4);">退会完了メール</a></div></li>
                    </ul>
                </div>
                <!-- /sub_navi -->
                
                <form name="main_menu" method="post" action="./?page=EditSystemMail">
                <div id="contents_file_01" style="{$display_file_01}">
                    <p>
                    入会確認時に送信される「入会確認メール」の本文を編集します。<br>
                    入会確認メールは、入会用フォームから申し込みを行った際に、入会用フォームに入力されたメールアドレス宛に送信されるメールです。<br>
                    メーリングリストに関する説明や紹介、注意事項を記載してください。
                    </p>
                    <table class="table">
                        <tr>
                            <th>件名</th>
                            <td>
                                <input type="text" name="subject_01" style="width:320px" value="{$systemmail_admission_subject}" />
                            </td>
                        </tr>
                        <tr>
                            <th>メール本文</th>
                            <td>
                                <textarea cols="100" rows="30" name="input_file_01">{$systemmail_admission}</textarea><br />
                                <div class="red_txt">
                                {$error_txt}
                                </div>
                            </td>
                        </tr>
                    </table>
                    <p>
                        システムメール送信時に、「###認証ＵＲＬ###」の部分に入退会処理を行うための認証用URLが挿入されます。<br>
                        「###認証ＵＲＬ###」を記載しない場合は、メールの文末に認証用URLが挿入されます。
                    </p>
                </div>

                <div id="contents_file_02" style="{$display_file_02}">
                    <p>
    退会確認時に送信される「退会確認メール」の本文を編集します。<br>
     退会確認メールは、退会用フォームから申し込みを行った際に、退会用フォームに入力されたメールアドレス宛に送信されるメールです。<br>
    メーリングリストに関する説明や紹介、注意事項を記載してください。
                    </p>
                    <table class="table">
                        <tr>
                            <th>件名</th>
                            <td>
                                <input type="text" name="subject_02" style="width:320px" value="{$systemmail_withdraw_subject}" />
                            </td>
                        </tr>
                        <tr>
                            <th>メール本文</th>
                            <td>
                                <textarea cols="100" rows="30" name="input_file_02">{$systemmail_withdraw}</textarea><br />
                                <div class="red_txt">
                                {$error_txt}
                                </div>
                            </td>
                        </tr>
                    </table>
                    <p>
                        システムメール送信時に、「###認証ＵＲＬ###」の部分に入退会処理を行うための認証用URLが挿入されます。<br>
                        「###認証ＵＲＬ###」を記載しない場合は、メールの文末に認証用URLが挿入されます。
                    </p>
                </div>

                <div id="contents_file_03" style="{$display_file_03}">
                    <p>
                    入会完了時に送信される「入会完了メール」の本文を編集します。<br>
                    入会完了メールは、入会用フォームからの申し込みが完了した際に、入会用フォームに入力されたメールアドレス宛に送信されるメールです。<br>
                    メーリングリストの入会処理が無事完了した事を通知します。
                    </p>
                    <table class="table">
                        <tr>
                            <th>件名</th>
                            <td>
                                <input type="text" name="subject_03" style="width:320px" value="{$systemmail_welcome_subject}" />
                            </td>
                        </tr>
                        <tr>
                            <th>メール本文</th>
                            <td>
                                <textarea cols="100" rows="30" name="input_file_03">{$systemmail_welcome}</textarea><br />
                                <div class="red_txt">
                                {$error_txt}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="contents_file_04" style="{$display_file_04}">
                    <p>
                        退会完了時に送信される「退会完了メール」の本文を編集します。<br>
                    退会完了メールは、退会用フォームからの申し込みが完了した際に、退会用フォームに入力されたメールアドレス宛に送信されるメールです。<br>
                    メーリングリストの退会処理が無事完了した事を通知します。
                    </p>
                    <table class="table">
                        <tr>
                            <th>件名</th>
                            <td>
                                <input type="text" name="subject_04" style="width:320px" value="{$systemmail_goodbye_subject}" />
                            </td>
                        </tr>
                        <tr>
                            <th>メール本文</th>
                            <td>
                                <textarea cols="100" rows="30" name="input_file_04">{$systemmail_goodbye}</textarea><br />
                                <div class="red_txt">
                                {$error_txt}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="button_box tac">
                    <input type="submit" name="sb_file_save" value="設定を保存する" />
                    <input type="hidden" name="now_systemmail" value="1" />
                </div>
                <!-- /button_box -->
            </form>

        </div>
        </div>
</div>