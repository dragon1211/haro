        <div class="mailingList__messeage">
            メールマガジンのシステムが送信するメールを編集できます。
        </div>

        <div class="section">
            <h3 class="section__ttl">システムメールの編集</h3>
            <div class="section__body">
                <div id="sub_navi">
            <ul class="clearfix">
                <li id="sub_navi_file_01"><div id="sub_tag_file_01" class="{$current_file_01}"><a href="javascript:void(0);" onclick="ChangeVewFile(1,4);">登録確認メール</a></div></li>
                <li id="sub_navi_file_02"><div id="sub_tag_file_02" class="{$current_file_02}"><a href="javascript:void(0);" onclick="ChangeVewFile(2,4);">退会確認メール</a></div></li>
                <li id="sub_navi_file_03"><div id="sub_tag_file_03" class="{$current_file_03}"><a href="javascript:void(0);" onclick="ChangeVewFile(3,4);">登録受付完了メール</a></div></li>
                <li id="sub_navi_file_04"><div id="sub_tag_file_04" class="{$current_file_04}"><a href="javascript:void(0);" onclick="ChangeVewFile(4,4);">退会申し込み完了メール</a></div></li>
            </ul>
        </div>
        <!-- /sub_navi -->

        <form name="main_menu" method="post" action="./?page=EditSystemMail">

            <div id="contents_file_01" style="{$display_file_01}">
                <p>
                    登録確認時に送信される登録確認メールの本文を編集します。<br>
                    登録確認メールは、登録用フォームから申し込みを行った際に、登録用フォームに入力されたメールアドレス宛に送信されるメールです。<br>
                    メールマガジンに関する説明や紹介、注意事項を記載してください。
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
                            <div>
                                <textarea name="input_file_01" cols="80" rows="25">{$systemmail_admission}</textarea>
                            </div>
                        </td>
                    </tr>
                </table>
                <p>
                    システムメール送信時に、「###認証ＵＲＬ###」の部分に登録の処理を行うための認証用URLが挿入されます。<br>
                    「###認証ＵＲＬ###」を記載しない場合は、メールの文末に認証用URLが挿入されます。
                </p>
            </div>

            <div id="contents_file_02" style="{$display_file_02}">
                <p>
                退会確認時に送信される退会確認メールの本文を編集します。<br>
                退会確認メールは、退会用フォームから申し込みを行った際に、退会用フォームに入力されたメールアドレス宛に送信されるメールです。<br>
                メールマガジンに関する説明や紹介、注意事項を記載してください。
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
                            <div>
                                <textarea name="input_file_02" cols="80" rows="25">{$systemmail_withdraw}</textarea>
                            </div>
                        </td>
                    </tr>
                </table>
                <p>
                    システムメール送信時に、「###認証ＵＲＬ###」の部分に退会の処理を行うための認証用URLが挿入されます。<br>
                    「###認証ＵＲＬ###」を記載しない場合は、メールの文末に認証用URLが挿入されます。
                </p>
            </div>

            <div id="contents_file_03" style="{$display_file_03}">
                <p>
                    登録受付完了時に送信される登録受付完了メールの本文を編集します。<br>
                    登録受付完了メールは、登録用フォームからの申し込みが完了した際に、登録用フォームに入力されたメールアドレス宛に送信されるメールです。<br>
                    メールマガジンの登録受付処理が無事完了した事を通知します。
                </p>
                <table class="table">
                    <tr>
                        <th>件名</th>
                        <td>
                            <div>
                                <input type="text" name="subject_03" style="width:320px" value="{$systemmail_welcome_subject}" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>メール本文</th>
                        <td>
                            <div>
                                <textarea name="input_file_03" cols="80" rows="25">{$systemmail_welcome}</textarea>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="contents_file_04" style="{$display_file_04}">
                <p>
                退会申し込み完了時に送信される退会申し込み完了メールの本文を編集します。<br>
                退会申し込み完了メールは、退会用フォームからの申し込みが完了した際に、退会用フォームに入力されたメールアドレス宛に送信されるメールです。<br>
                メールマガジンの退会処理が無事完了した事を通知します。
                </p>
                <table class="table">
                    <tr>
                        <th>件名</th>
                        <td>
                            <div>
                            <input type="text" name="subject_04" style="width:320px" value="{$systemmail_goodbye_subject}" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>メール本文</th>
                        <td>
                            <div>
                            <textarea name="input_file_04" cols="80" rows="25">{$systemmail_goodbye}</textarea>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="button_box tac">
                <input type="submit" name="sb_file_save" value="設定を保存する" />
                <input type="hidden" name="now_systemmail" value="{$now_systemmail}" />
            </div>
            <!-- /button_box -->

        </form>

        </div>
        </div>
</div>


        

