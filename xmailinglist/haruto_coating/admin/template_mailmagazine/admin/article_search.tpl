        <div class="mailingList__messeage">
            これまでの配信したメールマガジンを確認できます。
        </div>

        <div class="section">
            <div id="search_section" class="section__body">
                <form method="post" action="./?page=ArticleSearch">
                    <strong>キーワード</strong>
                    <input type="text" name="search_word" size="60" value="{$search_word}" />
                    <input type="submit" name="submit_button" value="検索" />
                </form>
            </div>
        </div>

        <div class="section">
            <h3 class="section__ttl">過去記事(メール)の検索結果「検索文字：{$search_word}」</h3>
            
            <div class="section__body">
                <div class="view_mail">
                {$ml_maildata}
                </div>
                <!-- /view_mail -->
            </div>
        </div>
    </div>
    <!-- /#main -->