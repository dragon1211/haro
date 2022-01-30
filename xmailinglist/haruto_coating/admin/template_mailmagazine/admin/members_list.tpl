        <div class="mailingList__messeage">
            メールマガジンのユーザーを一覧で表示します。
        </div>

        <div class="section">
            <h3 class="section__ttl">ユーザーの一覧表示</h3>
            <div class="section__body">
                <table class="table">
                    <tr>
                        <th>メールアドレス,メモ</th>
                        <td class="lump_field">
                            <p>※1行あたり1件ずつ（メールアドレスとメモをカンマ区切りで）表示しています。<br />
                                例．taro@test.xsrv.jp,メモ</p>
                            <textarea name="user_mail_lump" readonly="readonly" cols="75" rows="15">{$user_mail_lump_actives}</textarea>
                        </td>
                    </tr>
                </table>
                
                <a href="./">[ユーザーの管理に戻る]</a>
            </div>
        </div>
    </div>
    <!-- /#main -->