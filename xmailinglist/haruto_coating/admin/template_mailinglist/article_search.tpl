            <div class="inner">

                <div id="search_section" class="section">
                    <form method="get">
                        <input type="hidden" name="page" value="ArticleSearch">
                        <strong>検索</strong><input type="text" name="search_word" size="60" /><input type="submit" name="submit_button" value="検索" />
                    </form>
                </div>
                <!-- /section -->


                <div class="section">
                    <strong>「検索文字：{$search_word}」</strong><br />
                        <div class="view_mail">
                        {$ml_maildata}
                        </div>
                        <!-- /view_mail -->
                    <br />
                </div>
                <!-- /section -->
                
            </div>
            <!-- /inner -->
