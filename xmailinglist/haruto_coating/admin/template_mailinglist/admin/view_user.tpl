        <div class="mailingList__messeage">
            ユーザーへ公開するページである<a href="{$user_url}" target="_blank">公開ページ</a><i class="ico__newWindw"></i>に関する設定を行うことができます。<br>
            公開ページは、メーリングリストの入退会、および過去に配信されたメールである過去ログの閲覧ができます。
        </div>
        <form name="option_menu" method="post" action="./?page=ViewUserDo">
        <div class="section">
            <h3 class="section__ttl">公開設定</h3>
            <div class="section__body">
                
                <div class="error_txt">
                    {$error_txt}
                </div>
                
                <table class="table">
                    <tr>
                        <th>ページの公開</th>
                        <td>
                            <select name="panel_open" onchange="ViewPanelInfo();" style="padding-bottom: 4px;">
                                <option value="1" {$panel_open_1}>公開</option>
                                <option value="0" {$panel_open_0}>非公開</option>
                            </select>
                            <p class="mt5 mb0">[公開]に設定した場合は、メーリングリストのメンバー以外の第三者でもページの閲覧が可能となります。</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div id="userpanel_table" class="section" style="{$display_table}">
            <h3 class="section__ttl">詳細設定</h3>
            <div class="section__body">
               <table class="table">
                <tr>
                    <th>公開ページのURL</th>
                    <td>
                        公開ページのURLを表示します。<br />
                        ユーザーの入退会やメール情報の共有場所としてご使用ください。<br />
                        <strong><a href="{$user_url}" target="_blank">{$disp_url}</a></strong>
                        <p class="mt5 mb0">SSLを利用する場合は、マニュアル「<a href="https://support.xserver.ne.jp/manual/man_mail_mailinglist.php#ml10" target="blank">メーリングリスト > 10.SSLの利用</a><i class="ico__newWindw"></i>」をご参照ください。</p>
                    </td>
                </tr>
                <tr>
                    <th>過去ログの閲覧機能</th>
                    <td>
                        <select name="panel_maillog" onchange="ViewLoginInfo();" style="padding-bottom: 4px;">
                            <option value="1" {$panel_maillog_1}>あり</option>
                            <option value="0" {$panel_maillog_0}>なし</option>
                        </select>
                        <p class="mt5 mb0">[あり]に設定した場合、公開ページで訪問者が過去ログを閲覧出来るようになります。</p>
                    </td>
                </tr>
                <tr id="userpanel_login" style="{$display_login}">
                    <th style="width:170px;">
                        過去ログの保護パスワード
                    </th>
                    <td>
                        <label><input type="checkbox" name="pnael_log_protect" {pnael_log_protect_checked}> &nbsp;保護パスワードを設定する&nbsp;&nbsp;</label>
                        保護パスワード：<input type="password" name="panel_user_password" value="{$panel_user_password}" />
                        <br />
                        <p id="password_warning" style="color:#ff3333;margin-bottom:0">
                            保護パスワードを設定しない場合は、メーリングリストのメンバー以外の第三者でも過去ログを閲覧できます。
                        </p>
                        <p style="margin-bottom:0">
                            保護パスワードを設定する場合は、公開ページの利用者に保護パスワードを通知してください。
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>入会用フォーム</th>
                    <td>
                        <select name="panel_apply" style="padding-bottom: 4px;">
                            <option value="1" {$panel_apply_1}>あり</option>
                            <option value="0" {$panel_apply_0}>なし</option>
                        </select>
                        <p class="mt5 mb0">[あり]に設定した場合、公開ページで訪問者がメーリングリストに入会できるようになります。</p>
                    </td>
                </tr>
                <tr>
                    <th>退会用フォーム</th>
                    <td>
                        <select name="panel_withdraw" style="padding-bottom: 4px;">
                            <option value="1" {$panel_withdraw_1}>あり</option>
                            <option value="0" {$panel_withdraw_0}>なし</option>
                        </select>
                         <p class="mt5 mb0">[あり]に設定した場合、公開ページで参加メンバーがメーリングリストから退会できるようになります。</p>
                    </td>
                </tr>
            </table>
            </div>
        </div>
        <div class="button_box tac">
            <input type="submit" name="sb_setting_save" value="設定を保存する" />
        </div>
        <!-- /button_box -->
        </form>
    </div>
    <!-- /#main -->