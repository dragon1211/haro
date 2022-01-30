        <div class="mailingList__messeage">
            これまでのメールの一覧を閲覧できます。
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
            <h3 class="section__ttl">メールの検索結果「検索文字：{$search_word}」</h3>
            
            <div class="section__body">
                {$ml_maildata}
            </div>
        </div>
    </div>
    <!-- /#main -->
