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
 * Data: 2018-03-23T00:00:00+12:00
 */

ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 'Off');

//コントローラの呼び出し
require_once dirname(__FILE__) . '/admin/lib/CCtrlML.php';

//メーリングリスト操作クラスのインスタンス生成
$mCtrlML = new CCtrlML();

//指定アクションの取得
$action  = $mCtrlML->getAction();

//記事閲覧ページ以外はログインは不要
if( $action === '' ||
    $action === 'Index' ||
    $action === 'Article' ||
    $action === 'ArticleContents' ||
    $action === 'ArticleSearch'  ){
    //ユーザツールログイン認証
    require_once PROJECT_BASE . '/lib/CUserLogin.php';
}


if( $_SESSION['ML_MODE'] == "mailinglist" ){

//============================================================
//　ページの表示(メーリングリスト)
//============================================================
switch( $action ) {
    case 'Index':           $mCtrlML->GetHtml_Index();            break;
    case 'Article':         $mCtrlML->GetHtml_Index();            break;
    case 'ArticleContents': $mCtrlML->GetHtml_ArticleContents();  break;
    case 'ArticleSearch':   $mCtrlML->GetHtml_ArticleSearch();    break;
    case 'Apply':           $mCtrlML->GetHtml_Apply();            break;
    case 'ApplyDo':         $mCtrlML->GetHtml_ApplyDo();          break;
    case 'ApplyExit':       $mCtrlML->GetHtml_ApplyExit();        break;

    case 'Admission':       $mCtrlML->GetHtml_AdmissionDo();      break;
    case 'AdmissionExit':   $mCtrlML->GetHtml_AdmissionExit();    break;
    case 'AdmissionError':  $mCtrlML->GetHtml_AdmissionError();   break;
    case 'Withdraw':        $mCtrlML->GetHtml_WithdrawDo();       break;
    case 'WithdrawExit':    $mCtrlML->GetHtml_WithdrawExit();     break;
    case 'WithdrawError':   $mCtrlML->GetHtml_WithdrawError();    break;
    case 'logout':          $mCtrlML->GetHtml_Logout();           break;

    case '':                $mCtrlML->GetHtml_Index();            break;
    default :               echo '無効なページ遷移です。';      break;
}

}else{

//============================================================
//　ページの表示(メールマガジン)
//============================================================
switch( $action ) {
    case 'Apply':           $mCtrlML->GetHtml_Apply();            break;
    case 'ApplyDo':         $mCtrlML->GetHtml_ApplyDo();          break;
    case 'ApplyExit':       $mCtrlML->GetHtml_ApplyExit();        break;

    case 'Admission':       $mCtrlML->GetHtml_AdmissionDo();      break;
    case 'AdmissionExit':   $mCtrlML->GetHtml_AdmissionExit();    break;
    case 'AdmissionError':  $mCtrlML->GetHtml_AdmissionError();   break;
    case 'Withdraw':        $mCtrlML->GetHtml_WithdrawDo();       break;
    case 'WithdrawExit':    $mCtrlML->GetHtml_WithdrawExit();     break;
    case 'WithdrawError':   $mCtrlML->GetHtml_WithdrawError();    break;
    case 'logout':          $mCtrlML->GetHtml_Logout();           break;

    case '':                $mCtrlML->GetHtml_Apply();            break;
    default :               echo '無効なページ遷移です。';      break;
}

}

?>
