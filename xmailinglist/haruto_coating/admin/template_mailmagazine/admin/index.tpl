        <div class="mailingList__messeage">
            メールマガジンの配信先ユーザーの登録・変更・削除などを行うことができます。
        </div>
        
        <div class="section">
            <h3 class="section__ttl">ユーザー一覧(参加人数：{$user_num}名)<div class="max_txt">{MaxCount}</div></h3>
            <div class="section__body">

                <table class="table" id="members_table">
                    <tr>
                        <th>メールアドレス</th>
                        <th>メモ</th>
                        <th>設定変更</th>
                        <th>削除</th>
                    </tr>
                    {$user_list}
                    <tr style="{disp_ml_member_del_all};">
                        <td colspan="4">
                            <div class="button_box tar">
                                <form action="./?page=MembersDeleteAll" method="post">
                                    <input type="submit" name="sb_ml_member_del_all" value="ユーザーの一括削除" onclick="return confirm('メールアドレスを全て削除します。\r\nよろしいですか？');"/>
                                </form>
                            </div>
                        </td>
                    </tr>
                </table>
                
                <div class="pagelink">
                    {$page_link}
                </div>
                
                
                
                <div class="block">
                    <h4 class="block__ttl">ユーザーの一覧を表示する</h4>
                    <div class="block__body">
                        <a {members_list_link} >ユーザーの一覧表示はこちら</a>
                    </div>
                </div>
            </div>
        </div>

        <form method="post" action="./?page=MembersAdd">
        <div class="section">
            <h3 class="section__ttl">ユーザーの登録</h3>
            <div class="section__body">
                <div class="block">
                    <h4 class="block__ttl">一人ずつユーザーに登録する</h4>
                    <div class="block__body">
                        
                        <div class="error_txt">
                            {$error_txt}
                        </div>
                        
                        <table class="table">
                            <tr>
                                <th>メールアドレス</th>
                                <td><input name="user_mail" type="text" style="width:320px" value="{$user_mail}" /></td>
                            </tr>
                            <tr>
                                <th>メモ</th>
                                <td>
                                    <input name="user_memo" type="text" style="width:320px" value="{$user_memo}" />
                                    <br />入力内容の指定はありません。メールアドレスの利用者名を設定するなど、ご自由にお使いください。
                                </td>
                            </tr>
                            <tr>
                                <th>登録方法</th>
                                <td>
                                    <select name="register_mode">
                                        <option value="1">今すぐ登録して登録完了通知を送信する</option>
                                        <option value="2">今すぐ登録して登録完了通知を送信しない</option>
                                        <option value="3">購読用URLを送信し、ユーザの了解を得て登録する</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <div class="button_box tac">
                            <input type="submit" name="sb_ml_member_add" value="メールマガジンに登録する" {disabled_1}/>
                        </div>
                    </div>
                </div>
                
                <div class="block">
                    <h4 class="block__ttl">複数人を一括でユーザーに登録する</h4>
                    <div class="block__body">
                        <a {lump_link} >ユーザーの一括登録はこちら</a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <!-- /#main -->