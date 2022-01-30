        <div class="mailingList__messeage">
            メールマガジンに関する各種設定を行うことが出来ます。
        </div>
        <form name="option_menu" method="post" action="./?page=OptionDo">
        <div class="section">
            <h3 class="section__ttl">環境設定</h3>
            <div class="section__body">
                <div class="error_txt">
                    {$error_txt}
                </div>
                <table class="table">
                    <tr>
                        <th>メールマガジン名</th>
                        <td><input type="text" name="ml_app_name" value="{$ml_app_name}"  style="width:30em" />&nbsp;<span class="font11">30文字以内</span>
                            <p class="mt5 mb0">本メールマガジンの名称を設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>送信者名</th>
                            <td><input type="text" name="mm_from_name" value="{$mm_from_name}"  style="width:20em" />&nbsp;<span class="font11">20文字以内</span>
                                <p class="mt5 mb0">メールマガジンの送信者名を設定できます。</p>
                            </td>
                        </tr>
                    <tr>
                    <th>配信ナンバーの設定</th>
                        <td>
                         <input type="text" name="auto_number" value="{$auto_number}"/>
                            <p class="mt5 mb0">
                                メールマガジンの通し番号である配信ナンバーを設定できます。<br>
                                配信ナンバーは、メールマガジンが配信される度に1ずつ加算されます。
                            </p>
                        </td>
                    </tr>
                    <th>メールマガジン件名</th>
                        <td>
                            <input type="text" name="mm_title_name" value="{$mm_title_name}" style="width:320px" />&nbsp;<span class="font11">60文字以内</span>
                            <p class="mt5">
                                メールマガジンの件名の初期値を設定できます。<br>
                                配信日時を設定したい場合、次の雛形を記入することで配信日時が挿入されます。<br>
                                配信ナンバーを設定したい場合、次の雛形を記入することで配信ナンバーが挿入されます。
                            </p>
                            <p class="mb0">
                               例.『{year}年{month}月{day}日 』&nbsp;⇒&nbsp;『{$year}年{$month}月{$day}日 』<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;『{auto_number}回目 』&nbsp;⇒&nbsp;『{$auto_number}回目 』
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>ヘッダーの設定</th>
                        <td>
                            <textarea name="ml_add_header" rows="6" cols="72">{$ml_add_header}</textarea>

                            <p class="mt5">
                                入力された内容をメール本文の文頭に追加するよう設定できます。<br>
                                配信日時を設定したい場合、次の雛形を記入することで配信日時が挿入されます。
                            </p>
                            <p class="mb0">
                               例．『{year}年{month}月{day}日 』&nbsp;⇒&nbsp;『{$year}年{$month}月{$day}日 』
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>フッターの設定</th>
                        <td>
                            <textarea name="ml_add_footer" rows="6" cols="72">{$ml_add_footer}</textarea>
                            <p class="mt5">
                                入力された内容をメール本文の文末に追加されるよう設定できます。<br>
                                退会用のURLを設定したい場合、次の雛形を利用することで退会用フォームのURLが挿入されます。
                            </p>
                            <p class="mb0">
                                例．『###退会用URL###』&nbsp;⇒&nbsp;『{$withdraw_url}』
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>メールマガジンの返信先<br />(Reply-to )</th>
                        <td>
                            <input type="text" name="mm_reply_to" value="{$mm_reply_to}" style="width:300px" />
                            <p class="mt5 mb0">メールマガジンの返信先アドレスを設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>システム・エラーメール<br />受信設定</th>
                        <td>
                        <label><input type="checkbox" name="ml_transmit_val" {$ml_transmit_checked} onclick="mailDisable()"/>&nbsp;受信する<br></label>
                            受信用メールアドレス: &nbsp;<input type="text" name="ml_moderators" value="{$ml_moderators}" style="width:300px" />
                            
                            <p class="mt5 mb0">
                                [受信する]に設定した場合は、メールマガジン配信時に発生したシステム・エラーの内容を受信用メールアドレス宛に送信します。
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>管理ツール以外からの<br>配信設定</th>
                        <td>
                            <label>
                                <input id="ml_limit_send" type="checkbox" name="ml_limit_send" {$ml_limit_send} onclick="mMailDisable()"/>&nbsp;
                                有効にする
                            </label>
                            <br />送信用メールアドレス: &nbsp;<input type="text" name="mm_moderators" value="{$mm_moderators}" style="width:300px" /><br />
                            
                            <p class="mt5">配信用メールアドレス&nbsp;:&nbsp;<span style="font-weight:bold;">{$ml_mailaddress}</span></p>
                            
                            <p class="mt10">
                                [有効にする]に設定した場合は、普段ご利用のメールアドレスからメールマガジンを配信することができます。<br>
                                作成した内容をメールマガジン配信用に設定しているメールアドレスに送信し、受信した内容を承認する事でメールマガジンが配信されます。
                            </p>

                            <p class="caution" style="color:#ff3333">
                            <br >配信時には必ず、配信が完了したかどうかを確認して下さい。
                            <br >配信が完了した場合は、送信用メールアドレスに件名が「moderated article[xxxx.......]」のメールが配信完了通知として届きます。
                            <br >※ご利用前に一度テスト配信してみて下さい。
                            </p>
                            <script>
                                function clickLimitSend(){
                                    if($("#ml_limit_send").prop("checked")){
                                        $(".caution").show();
                                    }else{
                                        $(".caution").hide();
                                    }
                                }
                                $("#ml_limit_send").unbind().click(function (){clickLimitSend();});
                                clickLimitSend();
                             </script>
                        </td>
                    </tr>
                </table>

                <div class="button_box tac">
                    <input type="submit" name="sb_setting_save" value="設定を保存する" />
                </div>
                <!-- /button_box -->
            </div>
        </div>
        </form>
    </div>
    <!-- /#main -->