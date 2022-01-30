<?php

/**
 * Xserver Mailinglist & Mailmagazine Program 
 * http://www.xserver.ne.jp/
 * 
 * Copyright 2013 Xserver Inc.
 * http://www.xserver.co.jp/
 * 
 * Data: 2013-12-05T00:00:00+09:00
 * Data: 2015-04-14T00:00:00+12:00
 */

//コントローラの呼び出し
require_once dirname(__FILE__) . '/admin/lib/CCtrlML.php';

//メーリングリスト操作クラスのインスタンス生成
$mCtrlML = new CCtrlML();

$mCtrlML->AnalyzeAdminMail();
?>
