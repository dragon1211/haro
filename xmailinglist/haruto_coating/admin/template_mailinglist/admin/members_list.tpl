        <div class="mailingList__messeage">
            メーリングリストのメンバーを一覧で表示します。
        </div>

        <div class="section">
            <h3 class="section__ttl">メンバーの一覧表示</h3>
            <div class="section__body">
                <div id="sub_navi">
                    <ul class="clearfix">
                        <li id="sub_navi_file_01"><div id="sub_tag_file_01" class="{$current_file_01}"><a href="javascript:void(0);" onclick="ChangeVewFile(1,3);">メール受信 + メール配信</a></div></li>
                        <li id="sub_navi_file_02"><div id="sub_tag_file_02" class="{$current_file_02}"><a href="javascript:void(0);" onclick="ChangeVewFile(2,3);">メール受信のみ</a></div></li>
                        <li id="sub_navi_file_03"><div id="sub_tag_file_03" class="{$current_file_03}"><a href="javascript:void(0);" onclick="ChangeVewFile(3,3);">メール配信のみ</a></div></li>
                    </ul>
                </div>
                
                <table class="table">
                    <tr>
                        <th>メールアドレス,メモ</th>
                        <td class="lump_field">
                            ※1行あたり1件ずつ（メールアドレスとメモをカンマ区切りで）表示しています。<br />
                              例．taro@test.xsrv.jp,メモ
                            <textarea cols="50" rows="15" id="contents_file_01" name="user_mail_lump" style="{$display_file_01}" readonly="readonly">{$user_mail_lump_membaers}</textarea>
                            <textarea cols="50" rows="15" id="contents_file_02" name="user_mail_lump" style="{$display_file_02}" readonly="readonly">{$user_mail_lump_actives}</textarea>
                            <textarea cols="50" rows="15" id="contents_file_03" name="user_mail_lump" style="{$display_file_03}" readonly="readonly">{$user_mail_lump_membaers_admin}</textarea>
                        </td>
                    </tr>
                </table>
                
                <a href="./">[メンバーの管理に戻る]</a>
            </div>
        </div>
    </div>
    <!-- /#main -->