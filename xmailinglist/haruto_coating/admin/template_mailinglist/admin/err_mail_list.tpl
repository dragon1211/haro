        <div class="mailingList__messeage">
            配信エラーが発生したメンバーに対して各種対応を行うことができます。
        </div>

        <div class="section">
            <h3 class="section__ttl">配信エラー管理</h3>
            <div class="section__body">
                
                <div class="error_txt">
                    {$error_txt}
                </div>
                
                <div id="sub_navi" class="clearfix">
                    <ul>
                        <li id="sub_navi_file_01"><div id="sub_tag_file_01" class="{$current_file_01}"><a href="javascript:void(0);" onclick="ChangeVewFile(1,4);">自動削除設定</a></div></li>
                        <li id="sub_navi_file_02"><div id="sub_tag_file_02" class="{$current_file_02}"><a href="javascript:void(0);" onclick="ChangeVewFile(2,4);">削除済みメールアドレス</a></div></li>
                        <li id="sub_navi_file_03"><div id="sub_tag_file_03" class="{$current_file_03}"><a href="javascript:void(0);" onclick="ChangeVewFile(3,4);">エラーメール集計</a></div></li>
                    </ul>
                </div>
                <!-- /sub_navi -->
                
                <div id="contents_file_01" style="{$display_file_01}">
                    <p>
                    配信エラーが頻発するメンバーを自動的に削除するように設定できます。
                    </p>

                    <form name="delete_setting" method="post" action="./?page=ErrMailList">
                        <input type="hidden" name="now_systemmail" value="1">
                        <table class="table">
                            <tr>
                                <th>自動削除の有効化</th>
                                <td>
                                    <label>
                                        <input id="chk_auto_delete" type="checkbox" name="set_auto_delete_flg" {$set_auto_delete_flg} />&nbsp;自動削除を有効にする<br><br>
                                    </label>
                                    
                                    <aside class="msg msg--caution caution mb0">
                                        <h4 class="msg__ttl">【!】無効に設定されています。現在、メールアドレスの自動削除は行われません。</h4>
                                        <div class="msg__body">
                                            <p>
                                                エラーが頻発するメールアドレス宛へ配信が繰り返されると配信先のメールサーバーにて迷惑メールと認識される可能性が高まります。
                                            </p>
                                            <p>
                                                本機能を無効にする場合は、必ず「エラーメール集計」にてメール配信に失敗したあて先を定期的に確認、送信に失敗しているメールアドレスをメンバーから除外するなど個別に対策をおこなってください。
                                            </p>
                                        </div>
                                    </aside>
    
                                     <script>
                                        function clickChkAutoDelete(){
                                            if($("#chk_auto_delete").prop("checked")){
                                                $(".caution").hide();
                                            }else{
                                                $(".caution").show();
                                            }
                                        }
                                        
                                        $("#chk_auto_delete").unbind().click(function (){clickChkAutoDelete();});
                                        clickChkAutoDelete();
                                     </script>
                                </td>
                            </tr>
                            <tr>
                                <th>自動削除を実行する<br>エラー回数</th>
                                <td>
                                    <label>
                                        エラー回数&nbsp;<input type="text" name="set_err_mail_num" value="{$set_err_mail_num}" style="width:50px;"/>&nbsp;回
                                    </label>
                                        &nbsp;&nbsp;&nbsp;&nbsp;(設定値：1～10)
                                    <br />{$set_err_span}日の間に、設定回数を超えて配信エラーとなったメールアドレスはメンバーから自動削除されます。
                                </td>
                            </tr>
                            <tr>
                                <th>メール通知</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="set_auto_delete_notify" {$set_auto_delete_notify} />&nbsp;
                                        通知する
                                    </label>
                                    <br />[通知する]に設定した場合は、管理者メールアドレスに自動削除の実行内容を通知します。
                                    <br />※管理者メールアドレスは環境設定にて設定する事ができます。
                                </td>
                            </tr>
                        </table>

                        <div class="button_box tac">
                            <input type="submit" name="sb_set_auto_delete" value="設定を保存する"
                                onclick='return confirm("自動削除の設定を保存します。\r\nよろしいですか？")' />
                        </div>
                    </form>
                </div>
                <div id="contents_file_02" style="{$display_file_02}">
                    <p>自動削除されたメンバーの一覧表示と再登録ができます。</p>

                    <form name="member_reg" method="post" action="./?page=ErrMailList">
                        <input type="hidden" name="now_systemmail" value="2">
                        <table class="table" id="members_table">
                            <tr>
                                <th></th>
                                <th>メールアドレス</th>
                                <th>エラー回数</th>
                                <th>削除日時</th>
                            </tr>
                            {$delete_address}
                            <tr>
                                <td colspan="4" class="tar">
                                    <input type="button" value="全選択" id="all_reg_check">
                                    <input type="button" value="全解除" id="all_reg_uncheck">
                                    <input type="button" value="削除履歴のクリア" id="list_clear">
                                </td>
                            </tr>
                        </table>

                        <div class="pagelink">
                            {$auto_del_page_link}
                        </div>

                        <div id="command">
                            <div class="button_box tac">
                                <input type="submit" name="sb_Address_reg" value="選択したアドレスを再登録する"
                                    onclick='return confirm("選択したメールアドレスを再登録します。\r\nよろしいですか？")' />
                            </div>
                            <script>
                                function check_reg_mail(flag){
                                    if(flag){
                                        $(".reg_mail").attr("checked", "checked");
                                    }else{
                                        $(".reg_mail").attr("checked", false);
                                    }
                                }
                                function post_form_list_clear(){
                                    ret = confirm("自動削除の履歴をクリアします。\r\nよろしいですか？")
                                    if (ret) {
                                        $("#form_list_clear").submit();
                                    }
                                }
                                $("#all_reg_check").click(function(){check_reg_mail(true);})
                                $("#all_reg_uncheck").click(function(){check_reg_mail(false);})
                                $("#list_clear").click(function(){post_form_list_clear();})
                            </script>
                        </div>
                    </form>

                    <form name="list_clear" id="form_list_clear" method="post" action="./?page=ErrMailList">
                        <input type="hidden" name="now_systemmail" value="2">
                        <input type="hidden" name="list_clear" value="1">
                    </form>
                    <br>
                </div>

                <div id="contents_file_03" style="{$display_file_03}">

                    <p>
                    配信時に発生したエラーを、ユーザーごとに集計して表示しています。<br>
                    [詳細]をクリックすることで、エラーの発生時刻を一覧表示します。
                    </p>

                    <form name="member_delete" method="post" action="./?page=ErrMailList">
                        <input type="hidden" name="now_systemmail" value="3">
                        <table class="table" id="members_table">
                            <tr>
                                <th></th>
                                <th>メールアドレス</th>
                                <th>エラー回数</th>
                                <th>最終日時</th>
                                <th>詳細</th>
                            </tr>
                            {$err_address}
                            <tr>
                                <td colspan="5" class="tar">
                                    <input type="button" value="全選択" id="all_check">
                                    <input type="button" value="全解除" id="all_uncheck">
                                </td>
                            </tr>
                        </table>

                        <div class="pagelink">
                            {$err_list_page_link}
                        </div>

                        <div id="command">
                            <div class="button_box tac">
                                <input type="submit" name="sb_Address_delete" value="選択したアドレスをメンバーから削除する"
                                    onclick='return confirm("選択したメールアドレスを削除します。\r\nよろしいですか？")' />
                            </div>
                            <script>
                                function check_delete_mail(flag){
                                    if(flag){
                                        $(".delete_mail").attr("checked", "checked");
                                    }else{
                                        $(".delete_mail").attr("checked", false);
                                    }
                                }
                                $("#all_check").click(function(){check_delete_mail(true);})
                                $("#all_uncheck").click(function(){check_delete_mail(false);})
                            </script>
                        </div>
                    </form>
              </div>

            </div>
        </div>
    </div>
    <!-- /#main -->
