        <div class="mailingList__messeage">
            エラーメールの発生日時を確認できます。
        </div>
    
        <div class="section">
            <h3 class="section__ttl">エラーメール詳細</h3>
            <div class="section__body">
                <table class="table">
                    <tr><th>メールアドレス</th><td>{$address}</td></tr>
                    <tr>
                        <td colspan="2">
                            <div class="tac">
                                <form name="member_delete" method="post" action="./?page=ErrMailList">
                                  <input type="hidden" name="now_systemmail" value="3">
                                  <input type="hidden" name="delete_mail[]" value="{$address}" />
                                  <input type="submit" name="sb_Address_delete" value="このアドレスをメンバーから削除する"
                                      onclick='return confirm("メールアドレスを削除します。\r\nよろしいですか？")' />
                                </form>
                            </div>
                        </td>
                    </tr>
                <table>

                <table class="table" id="members_table">
                    <tr>
                        <th>発生日時</th>
                    </tr>
                    {$err_list}
                </table>
                <div class="pagelink">
                    {$page_link}
                </div>
                <p class="note">※エラーメールの発生日時は、メーリングリスト配信日時とは異なります。</p>
                <p class="note">
                    ※エラーメールの内容を本ツールから確認することはできません。<br>
                    エラーメールの詳細を確認されたい場合は、環境設定画面の[システム・エラーメール受信設定]を【受信する】にして【管理者メールアドレス】にて確認して下さい。
                </p>
                [<a href="#" onclick="document.back.submit()">エラーメール集計へ戻る</a>]
                <form name="back" method="post" action="./?page=ErrMailList">
                  <input type="hidden" name="now_systemmail" value="3">
                </form>
            </div>
        </div>
    </div>
    <!-- /#main -->