<?php

/**
 * Xserver Mailinglist & Mailmagazine Program 
 * http://www.xserver.ne.jp/
 * 
 * Copyright 2013 Xserver Inc.
 * http://www.xserver.co.jp/
 * 
 * Data: 2013-12-05T00:00:00+09:00
 * Data: 2018-03-23T00:00:00+12:00
 */

ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 'Off');

//コントローラの呼び出し
require_once dirname(__FILE__) . '/lib/CCtrlML.php';

//メーリングリスト操作クラスのインスタンス生成
$mCtrlML = new CCtrlML();

//指定アクションの呼び出し
$action  = $mCtrlML->getAction();

//管理ツールログイン認証
require_once PROJECT_BASE . '/lib/CLogin.php';

//============================================================
//　アクション実行(admin領域で想定するアクションだけ実行)
//============================================================
switch( $action ) {
    case 'Index':             $mCtrlML->GetHtml_AdminIndex();              break;
    case 'MembersLump':       $mCtrlML->GetHtml_AdminMembersLump();        break;
    case 'MembersAdd':        $mCtrlML->GetHtml_AdminMembersAdd();         break;
    case 'MembersAddLump':    $mCtrlML->GetHtml_AdminMembersAddLump();     break;
    case 'MembersList':       $mCtrlML->GetHtml_AdminMembersList();        break;
    case 'MembersExit':       $mCtrlML->GetHtml_AdminMembersExit();        break;
    case 'MembersLumpExit':   $mCtrlML->GetHtml_AdminMembersLumpExit();    break;
    case 'MembersOption':     $mCtrlML->GetHtml_AdminMembersOption();      break;
    case 'MembersOptionDo':   $mCtrlML->GetHtml_AdminMembersOptionDo();    break;
    case 'MembersOptionExit': $mCtrlML->GetHtml_AdminMembersOptionExit();  break;
    case 'MembersDelete':     $mCtrlML->GetHtml_AdminMembersDelete();      break;
    case 'MembersDeleteAll':  $mCtrlML->GetHtml_AdminMembersDeleteAll();   break;
    case 'MailCreate':        $mCtrlML->GetHtml_AdminMailCreate();         break;
    case 'MailConfirm':       $mCtrlML->GetHtml_AdminMailConfirm();        break;
    case 'MailDo':            $mCtrlML->GetHtml_AdminMailDo();             break;
    case 'MailExit':          $mCtrlML->GetHtml_AdminMailExit();           break;
    case 'Article':           $mCtrlML->GetHtml_AdminArticle();            break;
    case 'ArticleContents':   $mCtrlML->GetHtml_AdminArticleContents();    break;
    case 'ArticleSearch':     $mCtrlML->GetHtml_AdminArticleSearch();      break;
    case 'Option':            $mCtrlML->GetHtml_AdminOption();             break;
    case 'OptionDo':          $mCtrlML->GetHtml_AdminOptionDo();           break;
    case 'OptionExit':        $mCtrlML->GetHtml_AdminOptionExit();         break;
    case 'EditSystemMail':    $mCtrlML->GetHtml_AdminEditSystemMail();     break;
    case 'EditSystemMailDo':  $mCtrlML->GetHtml_AdminEditSystemMailDo();   break;
    case 'SetHtmlTag':        $mCtrlML->GetHtml_AdminSetHtmlTag();         break;
    case 'ViewUser':          $mCtrlML->GetHtml_AdminViewUser();           break;
    case 'ViewUserDo':        $mCtrlML->GetHtml_AdminViewUserDo();         break;
    case 'ViewUserExit':      $mCtrlML->GetHtml_AdminViewUserExit();       break;
    case 'IssueIndex':        $mCtrlML->GetHtml_AdminIssueIndex();         break;
    case 'IssueDo':           $mCtrlML->GetHtml_AdminIssueDo();            break;
    case 'IssueExit':         $mCtrlML->GetHtml_AdminIssueExit();          break;
    case 'ErrMailList':       $mCtrlML->GetHtml_AdminErrMailList();        break;
    case 'ErrMailDetail':     $mCtrlML->GetHtml_AdminErrMailDetail();      break;
    case 'ErrMailDelete':     $mCtrlML->GetHtml_AdminErrMailDelete();      break;
    case 'logout':            $mCtrlML->GetHtml_AdminLogout();             break;
    case '':                  $mCtrlML->GetHtml_AdminIndex();              break;
    default :                 echo '無効なページ遷移です。';             break;
}

?>
