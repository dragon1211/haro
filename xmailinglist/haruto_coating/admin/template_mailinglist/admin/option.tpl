        <div class="mailingList__messeage">
            メーリングリストに関する各種設定を行うことができます。
        </div> 
    
        <div class="section">
            <h3 class="section__ttl">環境設定</h3>
            <div class="section__body">
                <form name="option_menu" method="post" action="./?page=OptionDo">
                <div class="error_txt">
                    {$error_txt}
                </div>
                
                <table class="table">
                    <tr>
                        <th class="w25per">メーリングリスト名</th>
                        <td><input type="text" name="ml_app_name" value="{$ml_app_name}" style="width:30em" />&nbsp;<span class="font11">30文字以内</span>
                            <p class="mt5 mb0">本メーリングリストの名称を設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>メーリングリストアドレス</th>
                        <td>
                            <div style="font-weight:bold;">{$ml_mailaddress}</div>
                            <p class="mt5 mb0">
                                メーリングリストの宛先です。<br />
                                [メール受信＋メール配信]権限を持つメンバーが、上記メールアドレスにメールを送信することで、メンバー全員にそのメールが配信されます。
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>管理者メールアドレス</th>
                        <td>
                            <div style="font-weight:bold;"><input type="text" name="ml_moderators" value="{$ml_moderators}" style="width:300px;"/></div>
                            <p class="mt5 mb0">メンバー登録の完了通知メールやシステム・エラーメールを受信するためのメールアドレスを設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>配信アドレスの設定</th>
                        <td>
                            <label><input type="checkbox" name="ml_sender_check" {$ml_sender_checked} onclick="senderDisable()"/>&nbsp;アドレスを統一する</label>&nbsp;&nbsp;&nbsp;&nbsp;
                            配信者名：<input type="text" name="ml_sender_name" value="{$ml_sender_name}" style="width:300px;"/>&nbsp;<span class="font11">20文字以内</span>
                            
                            <p class="mt5 mb0">[アドレスを統一する]場合、メンバーそれぞれの配信メールの差出人(From部分)が"配信者名&lt;{$ml_mailaddress}&gt;"へと置き換わります。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>件名の編集</th>
                        <td>
                            <span class="mr5">書式</span>    
                            <select name="ml_subject_mode" style="padding-bottom: 4px;">
                                <option value="0" {$ml_subject_mode_0}>件名</option>
                                <option value="7" {$ml_subject_mode_7}>[タイトル] 件名</option>
                                <option value="8" {$ml_subject_mode_8}>(タイトル) 件名</option>                            
                                <option value="5" {$ml_subject_mode_5}>[通し番号] 件名</option>
                                <option value="6" {$ml_subject_mode_6}>(通し番号) 件名</option>
                                <option value="1" {$ml_subject_mode_1}>[タイトル:通し番号] 件名</option>
                                <option value="2" {$ml_subject_mode_2}>(タイトル:通し番号) 件名</option>
                                <option value="3" {$ml_subject_mode_3}>[タイトル,通し番号] 件名</option>
                                <option value="4" {$ml_subject_mode_4}>(タイトル,通し番号) 件名</option>
                            </select>
                            <p class="mt5"><span class="mr5">タイトル</span> <input type="text" name="ml_subject_name" value="{$ml_name}" maxlength="16" style="width:16em;" />&nbsp;<span class="font11">16文字以内</span></p>
                            <p class="mt5 mb0">
                                メンバーに配信されるメールの件名に、【タイトル】および【通し番号】を挿入できます。<br>
                                件名は、メンバーが、メーリングリストアドレス宛に送信したメールの件名です。<br>
                                通し番号には、メーリングリストで配信されたメールの順番を示す数値が挿入されます。
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>ヘッダーの設定</th>
                        <td>
                            <textarea name="ml_add_header" rows="6" cols="72">{$ml_add_header}</textarea>
                            <p class="mt5 mb0">入力された内容を、メール本文の文頭に追加されるよう設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>フッターの設定</th>
                        <td>
                            <textarea name="ml_add_footer" rows="6" cols="72">{$ml_add_footer}</textarea>
                            <p class="mt5 mb0">入力された内容を、メール本文の文末に追加されるよう設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>メールの配信前確認</th>
                        <td>
                            <select id="select_limit_send" name="ml_limit_send" style="padding-bottom: 4px;">
                                <option value="2" {$ml_limit_send_2}>確認する</option>
                                <option value="1" {$ml_limit_send_1}>確認しない</option>
                            </select>
                            <p class="mt5 mb0">
                                [確認する]に設定した場合、管理者が承認したメールのみがメンバーに配信されうよう設定できます。<br />
                                管理者は、【管理者メールアドレス】宛に送られたメールを確認し、配信を承認するかどうかを判断します。
                            </p>
                            <p class="caution mt5" style="color:#ff3333">
                            管理者は配信承認後、必ず配信が完了したかどうかを確認して下さい。
                            <br />配信が完了した場合は、管理者メールアドレスに件名が「moderated article[xxxx.......]」のメールが配信完了通知として届きます。
                            <br />※ご利用前に一度テスト配信してみて下さい。
                            </p>
                            <script>
                                function selectLimitSend(){
                                    if($("#select_limit_send").val() == 1){
                                        $(".caution").hide();
                                    }else{
                                        $(".caution").show();
                                    }
                                }
                                $("#select_limit_send").change(function (){selectLimitSend();});
                                selectLimitSend();
                            </script>
                        </td>
                    </tr>
                    <tr>
                    <tr>
                        <th>メールの返信先設定</th>
                        <td>
                            <select name="ml_reply_mode" style="padding-bottom: 4px;">
                                <option value="1" {$ml_reply_mode_1}>メンバー全員へ返信</option>
                                <option value="2" {$ml_reply_mode_2}>投稿した送信者へ返信</option>
                            </select>
                            <p class="mt5 mb0">配信されたメールに対して返信する場合に、[メンバー全員へ返信]を行うか[投稿した送信者へ返信]を行うかを設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>メールの容量制限</th>
                        <td>
                            <select name="ml_limit_size" style="padding-bottom: 4px;">
                                <option value="1" {$ml_limit_size_1}>～500KB</option>
                                <option value="2" {$ml_limit_size_2}>～1MB</option>
                                <option value="3" {$ml_limit_size_3}>～3MB</option>
                                <option value="4" {$ml_limit_size_4}>～10MB</option>
                                <option value="5" {$ml_limit_size_5}>～15MB</option>
                                <option value="6" {$ml_limit_size_6}>～20MB</option>
                                <option value="0" {$ml_limit_size_0}>～30MB</option>
                            </select>
                            <p class="mt5 mb0">メーリングリストに配信可能な１通あたりのメールサイズの上限設定を設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>HTMLメール設定</th>
                        <td>
                            <select name="ml_content_type" style="padding-bottom: 4px;">
                                <option value="1" {$ml_content_type_1}>許可しない</option>
                                <option value="2" {$ml_content_type_2}>許可する</option>
                            </select>
                            <br />
                            <p class="mt5 mb0">[許可しない]に設定した場合、HTML形式部分は除去されテキスト形式でメールが配信されます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>ファイル添付設定</th>
                        <td>
                            <select name="ml_temp_file" style="padding-bottom: 4px;">
                                <option value="1" {$ml_temp_file_1}>許可しない</option>
                                <option value="2" {$ml_temp_file_2}>許可する</option>
                            </select>
                            <br />
                            <p class="mt5 mb0">[[許可しない]に設定した場合、ファイル添付があるメールはエラーとなり配信されません。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>自動入会メンバーの権限設定</th>
                        <td>
                            <select name="ml_member_auth" style="padding-bottom: 4px;">
                                <option value="1" {$ml_member_auth_1}>メール受信 + メール配信</option>
                                <option value="2" {$ml_member_auth_2}>受信のみ</option>
                                <option value="3" {$ml_member_auth_3}>配信のみ</option>
                            </select>
                            <p class="mt5 mb0">[公開ページや自動入会機能から入会したメンバーに付与する権限を設定できます。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>システム・エラーメール受信設定</th>
                        <td>
                            <select name="ml_transmit_val" style="padding-bottom: 4px;">
                                <option value="1" {$ml_transmit_val_1}>受信しない</option>
                                <option value="2" {$ml_transmit_val_2}>受信する</option>
                            </select>
                            <p class="mt5 mb0">[[受信する]に設定した場合、メーリングリスト配信時に発生したシステム・エラーの内容を[管理者メールアドレス]宛に送信します。</p>
                        </td>
                </table>

                <div class="button_box tac">
                    <input type="submit" name="sb_setting_save" value="設定を保存する" />
                </div>
                <!-- /button_box -->

            </div>
        </div>
        </form>

        </div>