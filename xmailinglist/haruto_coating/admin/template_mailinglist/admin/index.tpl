        <div class="mailingList__messeage">
            メーリングリストメンバーの登録・変更・削除などを行うことができます。
        </div>
        
        <div class="section">
            <h3 class="section__ttl">メンバー一覧(参加人数：{$user_num}名)<div class="max_txt">{MaxCount}</div></h3>
            <div class="section__body">
                
                <table class="table mb30" id="members_table">
                    <thead>
                        <tr>
                            <th class="tac">メールアドレス</th>
                            <th class="tac">メモ</th>
                            <th class="tac">権限</th>
                            <th class="tac">設定変更</th>
                            <th class="tac">削除</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$user_list}
                        <tr style="{disp_ml_member_del_all};">
                            <td class="tar" colspan="5">
                                <form action="./?page=MembersDeleteAll" method="post">
                                    <input type="submit" name="sb_ml_member_del_all" value="メンバーの一括削除" onclick="return confirm('メールアドレスを全て削除します。\r\nよろしいですか？');"/>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="pagelink">{$page_link}</div>
                
                <div class="block">
                    <h4 class="block__ttl">メンバーの一覧を表示する</h4>
                    <div class="block__body">
                        <a {members_list_link} >メンバーの一覧表示はこちら</a>
                    </div>
                </div>
            </div>
        </div>

        <form method="post" action="./?page=MembersAdd">
        <div class="section">
            <h3 class="section__ttl">メンバーの登録・入会確認</h3>
            <div class="section__body">
                <div class="block">
                    <h4 class="block__ttl">一人ずつメンバーに登録する</h4>
                    <div class="block__body">
                        
                        <div class="error_txt">{$error_txt}</div>
                        
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th class="w20per">メールアドレス</th>
                                    <td><input name="user_mail" type="text" style="width:320px" value="{$user_mail}"></td>
                                </tr>
                                <tr>
                                    <th>メモ</th>
                                    <td>
                                        <input name="user_memo" type="text" style="width:320px" value="{$user_memo}" class="mb5">
                                        <p class="mb0">入力内容の指定はありません。メールアドレスの利用者名を設定するなど、ご自由にお使いください。</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th>権限</th>
                                    <td>
                                        <select name="user_mode" class="mb5">
                                            <option value="1">メール受信 + メール配信</option>
                                            <option value="2">メール受信のみ</option>
                                            <option value="3">メール配信のみ</option>
                                        </select>
                                        <p class="mb0">[メール受信＋メール配信]権限のメンバーは、メンバー全員にメールを配信することができます。</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th>登録方法</th>
                                    <td>
                                        <select name="register_mode" class="mb5">
                                            <option value="1">今すぐ登録して登録完了通知を送信する</option>
                                            <option value="2">今すぐ登録して登録完了通知を送信しない</option>
                                            <option value="3">入会用URLを送信し、ユーザの了解を得て登録する</option>
                                        </select>
                                        <p class="mb0">[入会用URLを送信し、ユーザの了解を得て登録する]場合の権限は、環境設定の[自動入会メンバーの権限設定]により設定可能です。</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="tac mb30"><input type="submit" name="sb_ml_member_add" value="メーリングリストに登録する" {disabled_1}/></p>
                    </div>
                </div>
                
                <div class="block">
                    <h4 class="block__ttl">複数人を一括でメンバーに登録する</h4>
                    <div class="block__body">
                        <a {lump_link} >メンバーの一括登録はこちら</a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <!-- /#main -->
