        <div class="mailingList__messeage">
            これまで配信されたメールである過去ログを閲覧できます。
        </div>

        <div class="section">
            <div id="search_section" class="section__body">
                <form method="post" action="./?page=ArticleSearch">
                    <strong>検索</strong>
                    <input type="text" name="search_word" size="60" />
                    <input type="submit" name="submit_button" value="検索" />
                </form>
            </div>
        </div>
                
        <div class="section">
            <h3 class="section__ttl">配信済みメールの閲覧</h3>
            
            <div class="section__body">
                <div id="view_mail_list">{$ml_maildata}</div>
            </div>
        </div>
    </div>
    <!-- /#main -->
