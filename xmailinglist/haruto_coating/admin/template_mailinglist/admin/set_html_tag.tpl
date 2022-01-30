        <div class="mailingList__messeage">
            空メールを送信することで自動入会が可能となるメールアドレスと、入会・退会に用いるフォームのHTMLコードを下記に掲載しております。<br>
            ホームページやブログ等のWebページに、それぞれ貼り付けてご利用ください。
        </div>

        <div class="section">
            <h3 class="section__ttl">空メール自動入会用メールアドレス</h3>
            <div id="search_section" class="section__body">
                <p>本メールアドレス宛に空メールを送信していただくことで、送信元のメールアドレスを自動的にメンバー登録することができます</p>
                <p><strong>{$auto_apply_mail}</strong></p>
            </div>
        </div>

        <div class="section">
            <h3 class="section__ttl">入会用フォーム</h3>
            <div class="section__body">
                <p>
                    入会用フォームを表示する設置タグです。<br>
                    下記の設置タグをWebページに貼り付けることで、入会用フォームを設置できます。
                </p>
                <p class="mb5"><strong>サンプル</strong></p>
                <div class="border border--gray mb20">
                    <p class="ml20"><input type="text" /><input type="button" value="入会する" /></p>
                </div>
                
                <p class="mb5"><strong>設置タグ</strong></p>
                <textarea cols="105" rows="18" readonly onclick="this.select();">
&lt;script&gt;
function MLFormSubmitOnlyIn( strButton ){
var obj;obj = window.open('{$action_url}','tml_form','width=400,height=300,menubar=no,toolbar=no');document.ml_form_only_in.target = 'tml_form';ml_form_only_in.sb_reg.value = strButton;
org = document.charset;document.charset = 'UTF-8';document.ml_form_only_in.submit();document.charset = org;
}
&lt;/script&gt;
&lt;form name="ml_form_only_in" id="ml_form_only_in" method="post" action="{$action_url}?page=MailReg" accept-charset="UTF-8"&gt;
    &lt;input type="text" name="add_mail" /&gt;
    &lt;input type="button" value="入会する" onClick="MLFormSubmitOnlyIn('入会する');" /&gt;&lt;br /&gt;
    &lt;input type="hidden" name="sb_reg" value="" /&gt;&lt;br /&gt;
    &lt;input type="hidden" name="identity" value="{identity}" /&gt;
&lt;/form&gt;</textarea>

            </div>
        </div>

        <div class="section">
            <h3 class="section__ttl">退会用フォーム</h3>
            <div class="section__body">
                <p>
                    退会用フォームを表示する設置タグです。<br>
                    下記の設置タグをWebページに貼り付けることで、退会用フォームを設置できます。
                </p>
                <p class="mb5"><strong>サンプル</strong></p>
                <div class="border border--gray mb20">
                    <p class="ml20"><input type="text" /><input type="button" value="退会する" /></p>
                </div>
                
                <p class="mb5"><strong>設置タグ</strong></p>
                <textarea cols="105" rows="18" readonly onclick="this.select();">
&lt;script&gt;
function MLFormSubmitOnlyOut( strButton ){
var obj;obj = window.open('{$action_url}','tml_form','width=400,height=300,menubar=no,toolbar=no');document.ml_form_only_out.target = 'tml_form';document.ml_form_only_out.sb_rel.value = strButton;
org = document.charset;document.charset = 'UTF-8';document.ml_form_only_out.submit();document.charset = org;
}
&lt;/script&gt;
&lt;form name="ml_form_only_out" id="ml_form_only_out" method="post" action="{$action_url}?page=MailRel" accept-charset="UTF-8"&gt;
    &lt;input type="text" name="delete_mail" /&gt;
    &lt;input type="button" value="退会する" onClick="MLFormSubmitOnlyOut('退会する');" /&gt;&lt;br /&gt;
    &lt;input type="hidden" name="sb_rel" value="" /&gt;&lt;br /&gt;
    &lt;input type="hidden" name="identity" value="{identity}" /&gt;
&lt;/form&gt;</textarea>
            </div>
        </div>

        <div class="section">
            <h3 class="section__ttl">入会・退会用フォーム</h3>
            <div class="section__body">
                <p>
                    入会・退会用フォームを表示する設置タグです。<br>
                    下記の設置タグをWebページに貼り付けることで、入会・退会用フォームを設置できます。
                </p>
                <p class="mb5"><strong>サンプル</strong></p>
                <div class="border border--gray mb20">
                    <p class="ml20">
                        <input type="text" /><input type="button" value="入会する" /><br />
                        <input type="text" /><input type="button" value="退会する" /><br />
                    </p>
                </div>
                
                <p class="mb5"><strong>設置タグ</strong></p>
                <textarea cols="105" rows="18" readonly onclick="this.select();">
&lt;script&gt;
function MLFormSubmitReg( strButton ){
var obj;obj = window.open('{$action_url}','tml_form','width=400,height=300,menubar=no,toolbar=no');document.ml_form.target = 'tml_form';document.ml_form.action='{$action_url}?page=MailReg';document.ml_form.sb_reg.value = strButton;
org = document.charset;document.charset = 'UTF-8';document.ml_form.submit();document.charset = org;
}
function MLFormSubmitRel( strButton ){
var obj;obj = window.open('{$action_url}','tml_form','width=400,height=300,menubar=no,toolbar=no');document.ml_form.target = 'tml_form';document.ml_form.action='{$action_url}?page=MailRel';document.ml_form.sb_rel.value = strButton;
org = document.charset;document.charset = 'UTF-8';document.ml_form.submit();document.charset = org;
}
&lt;/script&gt;
&lt;form name="ml_form" id="ml_form" method="post" action="{$action_url}" accept-charset="UTF-8"&gt;
    &lt;input type="text" name="add_mail" /&gt;
    &lt;input type="button" value="入会する" onClick="MLFormSubmitReg('入会する');" /&gt;&lt;br /&gt;
    &lt;input type="text" name="delete_mail" /&gt;
    &lt;input type="button" value="退会する" onClick="MLFormSubmitRel('退会する');" /&gt;&lt;br /&gt;
    &lt;input type="hidden" name="sb_reg" value="" /&gt;&lt;br /&gt;
    &lt;input type="hidden" name="sb_rel" value="" /&gt;&lt;br /&gt;
    &lt;input type="hidden" name="identity" value="{identity}" /&gt;
&lt;/form&gt;</textarea>
            </div>
        </div>
    </div>
    <!-- /#main -->

