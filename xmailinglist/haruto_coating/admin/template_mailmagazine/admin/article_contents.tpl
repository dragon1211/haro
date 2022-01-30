        <div class="mailingList__messeage">
            これまで配送されたメールマガジンである過去ログを閲覧できます。
        </div>

        <div class="section">
            <h3 class="section__ttl">過去記事(メール)の閲覧</h3>
            <div class="section__body">
                <div id="search_section" class="section">
                    <form method="post" action="./?page=ArticleSearch">
                        <strong>キーワード</strong>
                        <input type="text" name="search_word" size="60" />
                        <input type="submit" name="submit_button" value="検索" />
                    </form>
                </div>

                <div id="view_mail_list" class="view_mail">
                {$ml_maildata}
                </div>
                <!-- /view_mail -->
            </div>
        </div>
    </div>
    <!-- /#main -->