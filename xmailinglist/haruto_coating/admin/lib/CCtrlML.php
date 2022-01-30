<?php

session_start();

define("PROJECT_BASE_CONFIG",   "/home/atec1188/harutocoating.jp/xmailinglist/haruto_coating/user_panel");
define("FML_BASE",              "/home/atec1188/harutocoating.jp/xmailinglist/lib");
//ベースパスの設定
define("PROJECT_BASE",          "/home/atec1188/harutocoating.jp/public_html/xmailinglist/haruto_coating/admin");

//クラスの呼び出し
require_once PROJECT_BASE . '/lib/CTemplate.php';
require_once PROJECT_BASE . '/lib/CMHonArc.php';
require_once PROJECT_BASE . '/lib/CCommon.php';
require_once PROJECT_BASE . '/lib/CConfig.php';
require_once PROJECT_BASE . '/lib/CCtrlCmdMail.php';
require_once PROJECT_BASE . '/lib/CCtrlFml.php';
require_once PROJECT_BASE . '/lib/CMailData.php';
require_once PROJECT_BASE . '/lib/CCheckConfirm.php';
require_once PROJECT_BASE . '/lib/CCtrlUserPwd.php';
require_once PROJECT_BASE . '/lib/CChangeWordBinder.php';
require_once PROJECT_BASE . '/lib/CCtrlErrMail.php';

//設定ファイルの呼び出し
require_once PROJECT_BASE_CONFIG . '/setting.php';


//get_magic_quotes_gpc対策
CCommon::CheckMagicQuotesGpc();

//mbstring.encoding_translation対策
CCommon::CheckTranslationEncoding();
//===========================================
//
//  メーリングリストの制御クラス
//
//===========================================
class CCtrlML {
    //==============================
    // 定数定義
    //==============================
    //１ページに表示されるメールアドレスの表示件数
    const eMAIL_PAGEVIEW_MAX      = 10;

    //選択ページ
    const eSELECTPAGE_MEMBERS     = 'members_index';
    const eSELECTPAGE_MAIL        = 'mail_index';
    const eSELECTPAGE_ARTICLE     = 'article_index';
    const eSELECTPAGE_OPTION      = 'option_index';
    const eSELECTPAGE_SYSTEMMAIL  = 'systemmail_index';
    const eSELECTPAGE_VIEWUSER    = 'user_index';
    const eSELECTPAGE_SETHTML     = 'html_index';
    const eSELECTPAGE_ERRMAIL     = 'errmail_index';
    const ML_MAX_MEMBER           = 500;
    const MG_MAX_MEMBER           = 1000;
    const MAXLENGTH_USERNAME      = 20;

    //メーリングリスト権限
    const ML_AUTH_MEMBERS         = 1;
    const ML_AUTH_ACTIVES         = 2;
    const ML_AUTH_MEMBERS_ADMIN   = 3;
    //権限カウント
    const ML_AUTH_NUM             = 3;


    //==============================
    // 変数定義
    //==============================
    //ＦＭＬの操作クラス
    var $mCtrlFml;

    //設定ファイルの操作クラス
    var $mConfig;

    //メンバー情報の保管配列
    var $mMemberInfo;

    //MHonArc データの操作クラス
    var $MHonArc;

    //テンプレートの操作クラス
    var $mTemplate;

    //コマンドメールの操作クラス
    var $mCtrlCmdMail;

    //メールデータの操作クラス
    var $mMailData;

    //入退会確認の操作クラス
    var $mCheckConfirm;

    //ユーザーログイン情報の管理クラス
    var $mCtrlUserPwd;

    //エラーメール取得クラス
    var $mCtrlErrMail;

    //-----------------------------
    //登録できないメールアドレスリスト
    var $mNgMailAddress;

    //エラーメッセージ
    var $errMsg;

    //メンバー一括登録時のエラーメールアドレス
    var $errMail;

    //完了メッセージ
    var $comitMsg;

    //エラー戻り時の画面表示内容
    var $mDispContents;

    //ML・MM名
    var $mAppTitleName;

    //==============================
    //
    // コンストラクタ
    //
    //==============================
    function __construct() {
        $this->mCtrlFml     = new CCtrlFml();
        $this->mConfig      = new CConfig();
        $this->MHonArc      = new CMHonArc();
        $this->mTemplate    = new CTemplate();
        $this->mCtrlCmdMail = new CCtrlCmdMail();
        $this->mMailData    = new CMailData();
        $this->mCheckConfirm= new CCheckConfirm();
        $this->mCtrlUserPwd = new CCtrlUserPwd();
        $this->errMsg       = null;
        $this->errMail      = array();

        //ML・MM名
        $this->mAppTitleName = mb_convert_encoding( $this->mConfig->mAdminAppName,'UTF-8', 'UTF-8' );

        if (!isset($_SESSION['ML_MODE'])){
            $_SESSION['ML_MODE'] = ML_MODE;
        }

        //テスト環境以外でモードが入れ替わらない対応
        if (is_null($_POST['debug_mode'])){
            $_SESSION['ML_MODE'] = ML_MODE;
        }

        // aliasの設定
        $this->mCtrlFml->SetMailHook();

        //過去記事へのフォルダ設定
        $this->MHonArc->SetFolder( $this->mCtrlFml->GetNewsFolderPath() );

        //メーリングリストの操作用の初期設定
        $this->mMemberInfo  = $this->GetUserList();

        //テンプレートへのパスを設定する
        $this->mTemplate->SetFolder( PROJECT_BASE . '/template_' . $_SESSION['ML_MODE'] . '/' );

        //登録できないメールアドレス一覧の作成
        $this->mNgMailAddress = array(
            'root@' . DOMAIN_NAME,
            'postmaster@' . DOMAIN_NAME,
            'MAILER-DAEMON@' . DOMAIN_NAME,
            'msgs@' . DOMAIN_NAME,
            'nobody@' . DOMAIN_NAME,
            'news@' . DOMAIN_NAME,
            'majordomo@' . DOMAIN_NAME,
            'listserv@' . DOMAIN_NAME,
            'listproc@' . DOMAIN_NAME,
            ML_NAME . '@' . DOMAIN_NAME,
            ML_NAME . '-help@' . DOMAIN_NAME,
            ML_NAME . '-apply@' . DOMAIN_NAME,
            ML_NAME . '-subscribe@' . DOMAIN_NAME,
            ML_NAME . '-unsubscribe@' . DOMAIN_NAME
        );
    }

    //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
    //
    //  FML操作系の関数
    //
    //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
    //==============================
    // メーリングリスト参加メンバー一覧の取得
    //==============================
    function GetUserList( $isSort = true ) {
        $users   = array();
        $actives = array();
        $list    = array();
        $count   = 0;
        $flag    = 0;

        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
        //メーリングリストの場合、membersとActivesからユーザーを取得
            $users          = $this->mCtrlFml->GetMemberUser();
            $actives        = $this->mCtrlFml->GetActiveUser();
            $members_admin  = $this->mCtrlFml->GetMemberAdmin();
            $members_name   = $this->mCtrlFml->getMemberNameList();

            foreach( $users as $value ){
                //空白、またはコメントの場合は除外する
                $value = trim($value);
                if( $value == '' ) continue;
                if( mb_strpos( $value, '#', 0, 'UTF-8' ) === 0 ) continue;

                //管理系のメールアドレスは表示しない
                if( $value === ML_NAME . '@' . DOMAIN_NAME ){        continue;   }
                if( $value === ML_NAME . '-admin@' . DOMAIN_NAME ){  continue;   }
                if( $value === ML_NAME . '-ctl@' . DOMAIN_NAME ){    continue;   }

                //メンバーの追加
                $list[$count]['mail']    = $value;
                $memo = mb_convert_encoding( $members_name[$value], "UTF-8", "SJIS-WIN" );
                if (isset($memo)){
                    $list[$count]['memo']    = $memo;
                }else{
                    $list[$count]['memo']    = '';
                }
                $list[$count]['status']  = 'メール受信＋メール配信';
                $list[$count]['mode']    = CCtrlML::ML_AUTH_MEMBERS;
                $count++;
            }

            //メーリングリストの受信のみのユーザーがいる場合
            if ($actives){
                foreach( $actives as $value ){
                    //空白、またはコメントの場合は除外する
                    $value = trim($value);
                    if( $value == '' ) continue;
                    if( mb_strpos( $value, '#', 0, 'UTF-8' ) === 0 ) continue;

                    //管理系のメールアドレスは表示しない
                    if( $value === ML_NAME . '@' . DOMAIN_NAME ){        continue;   }
                    if( $value === ML_NAME . '-admin@' . DOMAIN_NAME ){  continue;   }
                    if( $value === ML_NAME . '-ctl@' . DOMAIN_NAME ){    continue;   }
                    //メンバーの追加
                    $list[$count]['mail']    = $value;
                    $memo = mb_convert_encoding( $members_name[$value], "UTF-8", "SJIS-WIN" );
                    if (isset($memo)){
                        $list[$count]['memo']    = $memo;
                    }else{
                        $list[$count]['memo']    = '';
                    }
                    $list[$count]['status']  = 'メール受信のみ';
                    $list[$count]['mode']    = CCtrlML::ML_AUTH_ACTIVES;
                    $count++;
                }
            }

            //メーリングリストの配信のみのユーザーがいる場合
            if ($members_admin){
                $moderator  = $this->mCtrlFml->GetModerators();
                foreach( $members_admin as $value ){
                    //空白、またはコメントの場合は除外する
                    $value = trim($value);
                    if( $value == '' ) continue;
                    if( mb_strpos( $value, '#', 0, 'UTF-8' ) === 0 ) continue;

                    //管理系のメールアドレスは表示しない
                    if ( preg_match("/".ML_NAME."(-admin|-ctl)?@/", $value) ) { continue; }
                    if ( array_search($value, $moderator) !== FALSE )         { continue; }

                    //メンバーの追加
                    $list[$count]['mail']    = $value;
                    $memo = mb_convert_encoding( $members_name[$value], "UTF-8", "SJIS-WIN" );
                    if (isset($memo)){
                        $list[$count]['memo']    = $memo;
                    }else{
                        $list[$count]['memo']    = '';
                    }
                    $list[$count]['status']  = 'メール配信のみ';
                    $list[$count]['mode']    = CCtrlML::ML_AUTH_MEMBERS_ADMIN;
                    $count++;
                }
            }

            //ユーザーのソート
            if( $isSort ){
                for( $i=0;$i<($count-1);$i++ ){
                    for( $j=($count-1);$j>$i;$j-- ){
                        if( strcmp( $list[$j]['mail'], $list[($j-1)]['mail'] ) >= 0 ){
                            continue;
                        }
                        $mail                 = $list[$j]['mail'];
                        $memo                 = $list[$j]['memo'];
                        $status               = $list[$j]['status'];
                        $mode                 = $list[$j]['mode'];
                        $list[$j]['mail']     = $list[($j-1)]['mail'];
                        $list[$j]['memo']     = $list[($j-1)]['memo'];
                        $list[$j]['status']   = $list[($j-1)]['status'];
                        $list[$j]['mode']     = $list[($j-1)]['mode'];
                        $list[($j-1)]['mail'] = $mail;
                        $list[($j-1)]['memo'] = $memo;
                        $list[($j-1)]['status'] = $status;
                        $list[($j-1)]['mode'] = $mode;
                    }
                }
            }

        }else{
            //メールマガジンの場合、とActivesからユーザーを取得
            $users = $this->mCtrlFml->GetActiveUser();
            $members_name = $this->mCtrlFml->getMemberNameList();

            foreach( $users as $value ){
                //空白、またはコメントの場合は除外する
                $value = trim($value);
                if( $value == '' ) continue;
                if( mb_strpos( $value, '#', 0, 'UTF-8' ) === 0 ) continue;

                //管理系のメールアドレスは表示しない
                if( $value === ML_NAME . '@' . DOMAIN_NAME ){        continue;   }
                if( $value === ML_NAME . '-admin@' . DOMAIN_NAME ){  continue;   }
                if( $value === ML_NAME . '-ctl@' . DOMAIN_NAME ){    continue;   }

                //メンバーの追加
                $list[$count]['mail']    = $value;
                $memo = mb_convert_encoding( $members_name[$value], "UTF-8", "SJIS-WIN" );
                if (isset($memo)){
                    $list[$count]['memo']    = $memo;
                }else{
                    $list[$count]['memo']    = '';
                }
                $list[$count]['mode']    = CCtrlML::ML_AUTH_ACTIVES;
                $count++;
            }

            //ユーザーのソート
            if( $isSort ){
                for( $i=0;$i<($count-1);$i++ ){
                    for( $j=($count-1);$j>$i;$j-- ){
                        if( strcmp( $list[$j]['mail'], $list[($j-1)]['mail'] ) >= 0 ){
                            continue;
                        }
                        $mail                 = $list[$j]['mail'];
                        $memo                 = $list[$j]['memo'];
                        $mode                 = $list[$j]['mode'];
                        $list[$j]['mail']     = $list[($j-1)]['mail'];
                        $list[$j]['memo']     = $list[($j-1)]['memo'];
                        $list[$j]['mode']     = $list[($j-1)]['mode'];
                        $list[($j-1)]['mail'] = $mail;
                        $list[($j-1)]['memo'] = $memo;
                        $list[($j-1)]['mode'] = $mode;
                    }
                }
            }
        }

        //ＩＤ番号の設定
        for( $i=0;$i<$count;$i++ ){
            $list[$i]['id']    = $i;
        }

        return $list;

    }

    //==============================
    // アクション取得
    //==============================
    function getAction(){
        $action = '';
        if( isset( $_GET['page'] ) ){
            $action = $_GET['page'];
        }
        return $action;
    }

    //==============================
    // 登録不可なメールアドレスかを調べる
    //==============================
    function IsNgMailAddress( $strMailAddress ){
        foreach( $this->mNgMailAddress as $value ){
            if( $strMailAddress === $value ){
                return true;
            }
        }
        return false;
    }

    //==============================
    // 入会処理
    //==============================
    function GetHtml_AdmissionDo() {
        $flag      = true;
        $user_mail = CCommon::AdjustNullValue( $_GET['mail'] );
        $param     = CCommon::AdjustNullValue( $_GET['param'] );
        $id        = CCommon::AdjustNullValue( $_GET['id'] );

        //除外判定
        if ( ! CCommon::IsMailAddress($user_mail) ) {
            if ( empty($id) ) {
                $flag   = false;
            }
            else if ( ($mail = $this->mCheckConfirm->CheckAdmisionMail( '', $id, $identity)) === false ) {
                $flag   = false;
            }
            else {
                $user_mail  = $mail;
            }
        }
        else if( ($mail = $this->mCheckConfirm->CheckAdmisionMail( $user_mail, $param, $identity)) === false ) {
            $flag = false;
        }

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->GetHtml_AdmissionError();
            exit;
        }

        //管理者メールアドレスに登録されていないか確認する
        if ($this->mCtrlFml->CheckModerators($user_mail)){
            $this->GetHtml_AdmissionError();
            exit;
        }

        //入会処理
        $welcome_mail = false;
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            //members・actives・members-adminに記載ない場合に登録
            if( !($this->mCtrlFml->CheckMemberUser( $user_mail )) && !($this->mCtrlFml->CheckActiveUser($user_mail)) && !$this->mCtrlFml->CheckMemberAdmin($user_mail) ){

                    $ml_member_auth  = $this->mConfig->mMlMemberAuth;
                    switch ($ml_member_auth) {
                        //環境設定のメンバーの自動登録設定が【メール受信＋メール配信】の場合
                        case CCtrlML::ML_AUTH_MEMBERS:
                            $this->mCtrlFml->AddMemberUser( $user_mail );
                            break;
                        //【受信のみ】の場合
                        case CCtrlML::ML_AUTH_ACTIVES:
                            $this->mCtrlFml->AddActiveUser( $user_mail );
                            break;
                        //【配信のみ】の場合
                        case CCtrlML::ML_AUTH_MEMBERS_ADMIN:
                            $this->mCtrlFml->AddMembersAdmin( $user_mail );
                            break;
                    }
                $welcome_mail = true;
            }else{
                $this->GetHtml_AdmissionError();
                $this->errMsg = '「' . CCommon::EscHtml( $user_mail ) . '」は既に入会されています。';
                $error_txt  .= '<ul><li>「' . CCommon::EscHtml( $user_mail ) . '」は既に入会されています。 </li></ul>';
            }
        }else{
            if( ! $this->mCtrlFml->CheckActiveUser( $user_mail ) ){
                $this->mCtrlFml->AddActiveUser( $user_mail );
                $welcome_mail = true;
            }
        }

        //既に入会済みの場合
        if (isset($error_txt)){
            $html     = '';
            $header   = $this->mTemplate->GetHTML( 'header.tpl' );
            //ヘッダー情報の置換
            $header   = $this->ChangeHtml_UserHeader( $header );
            $contents  = $this->mTemplate->GetHTML( 'apply_add.tpl' );
            $footer   = $this->mTemplate->GetHTML( 'footer.tpl' );

            //情報の置換
            $contents = str_replace( '{error_txt}', $error_txt, $contents );
            $html     = $header . $contents . $footer;
            echo $html;
            exit;
        }

        //登録完了のお知らせ
        if( $welcome_mail ){

            // システムメールの送信者名を設定
            if( $_SESSION['ML_MODE'] == 'mailinglist' ){
                //ML名を送信者名に設定
                $fromname = $this->mConfig->mAdminAppName;
            }else{
                //送信者名を取得
                $fromname = $this->mConfig->mAdminFromName;
                if (!$fromname || $fromname == ''){
                    $fromname = $this->mConfig->mAdminAppName;
                }
            }

            //入会者に通知メール
            CCommon::SendMail(
                    $fromname,
                    $this->mMailData->mMLMainAddress,
                    $user_mail,
                    $this->mConfig->mSystemMailWelcomeSubject,
                    $this->mCtrlFml->GetTextFileWelcome()
            );

            //-admin宛に通知メール
            //メモがある場合
            $txt_memo =$this->mCheckConfirm->GetApplyMemo($user_mail, $param, $id);
            if ($txt_memo != ''){
                $txt_memo = mb_convert_encoding( $txt_memo, "SJIS-WIN" , "UTF-8" );
                $this->mCtrlFml->putMemberName( $user_mail , $txt_memo);
            }

            //本文を上書き
            $mailaddress = ML_NAME . '@' . DOMAIN_NAME;
            $contents = $this->mCtrlFml->GetTextFileAdNotice();
            $contents = str_replace('###ML_TITLENAME###', $mailaddress , $contents);
            $contents = str_replace('###NEW_MEMBER###', $user_mail , $contents);

            CCommon::SendMail(
                    $fromname,
                    $this->mMailData->mMLMainAddress,
                    $this->mMailData->mMLAdminAddress,
                    'メンバー加入( ' . $user_mail . ' )',
                    $contents
            );

        }

        header( "Location: ./?page=AdmissionExit" );
        exit;
    }

    //==============================
    // 入会完了用ページの表示
    //==============================
    function GetHtml_AdmissionExit() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $html     = $this->mTemplate->GetHTML( 'admission_exit.tpl' );

        //メッセージの置換
        if (isset($this->errMsg)){
            $html = str_replace( '{$confirmMsg}', $this->errMsg, $html );
        }else{
            $html = str_replace( '{$confirmMsg}', '', $html );
        }
        $html = str_replace( '{$ml_app_name}', $this->mAppTitleName , $html );

        //  退会用URL
        $exitUrl = 'http://' . $_SERVER["SERVER_NAME"]. '/xmailinglist/' . ML_NAME . '/';
        //  MLとMMで分岐
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            $repString = "";
            if( ($this->mConfig->mUserPanelOpen && $this->mConfig->mUserPanelWithdraw)){
                $repString = '<a href="' . $exitUrl . '?page=Apply">退会用フォームはこちら</a>';
            }
            $html = str_replace( '{$exit_url}', $repString ,$html);
        }else{
            $html = str_replace( '{$withdraw_url}', $exitUrl, $html );
        }

        echo $html;
    }

    //==============================
    // 入会エラーページの表示
    //==============================
    function GetHtml_AdmissionError() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $html     = $this->mTemplate->GetHTML( 'admission_error.tpl' );
        $html     = str_replace( '{$ml_app_name}', $this->mAppTitleName , $html );

        echo $html;
    }

    //==============================
    // 退会処理
    //==============================
    function GetHtml_WithdrawDo() {
        $flag      = true;
        $user_mail = CCommon::AdjustNullValue( $_GET['mail'] );
        $param     = CCommon::AdjustNullValue( $_GET['param'] );
        $id        = CCommon::AdjustNullValue( $_GET['id'] );

        //除外判定
        /*
        if( ! CCommon::IsMailAddress( $user_mail ) ){                           $flag = false; }
        if( ! $this->mCheckConfirm->CheckWithdrawMail( $user_mail, $param ) ){  $flag = false; }
        */
        if ( ! CCommon::IsMailAddress($user_mail) ) {
            if ( empty($id) ) {
                $flag   = false;
            }
            else if ( ($mail = $this->mCheckConfirm->CheckWithdrawMail( '', $id) ) === false ) {
                $flag   = false;
            }
            else {
                $user_mail  = $mail;
            }
        }
        else if( ($mail = $this->mCheckConfirm->CheckWithdrawMail( $user_mail, $param) ) === false ) {
            $flag = false;
        }

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->GetHtml_WithdrawError();
            exit;
        }

        //退会処理
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            if( $this->mCtrlFml->CheckMemberUser( $user_mail )){
                $this->mCtrlFml->DeleteMemberUser( $user_mail );
            }
            if( $this->mCtrlFml->CheckActiveUser( $user_mail )){
                $this->mCtrlFml->DeleteActiveUser( $user_mail );
                $ctrlErrMail = new CCtrlErrMail($this->mCtrlFml->GetMLPath());
                $ctrlErrMail->UnsetSummaryData(array($user_mail));
            }
            //メンバー確認
            if ( !$this->mCtrlFml->CheckModerators($user_mail) && $this->mCtrlFml->CheckMemberAdmin($user_mail) ) {
                $this->mCtrlFml->DeleteMembersAdmin($user_mail);
            }

            //ML名を送信者名に設定
            $fromname = $this->mConfig->mAdminAppName;
        }else{
            if( $this->mCtrlFml->CheckActiveUser( $user_mail ) ){
                $this->mCtrlFml->DeleteActiveUser( $user_mail );
                $ctrlErrMail = new CCtrlErrMail($this->mCtrlFml->GetMLPath());
                $ctrlErrMail->UnsetSummaryData(array($user_mail));
            }

            //送信者名を取得
            $fromname = $this->mConfig->mAdminFromName;
            if (!$fromname || $fromname == ''){
                $fromname = $this->mConfig->mAdminAppName;
            }
        }

        //退会者に通知メール送信
        $contents = '';
        $contents = $this->mCtrlFml->GetTextFileGoodbye();

        if ($_SESSION['ML_MODE'] == 'mailinglist'){
            $contents = str_replace('###Service###' , 'メーリングリスト',$contents);
        }else{
            $contents = str_replace('###Service###' , 'メールマガジン',$contents);
        }
        $contents = str_replace('###ACOUNT###' , ML_NAME . '@' . DOMAIN_NAME , $contents);

        CCommon::SendMail(
                $fromname,
                $this->mMailData->mMLMainAddress,
                $user_mail,
                $this->mConfig->mSystemMailGoodbyeSubject,
                $contents
        );

        //-admin宛に通知メール
        //本文を上書き
        $mainaddress = ML_NAME . '@' . DOMAIN_NAME;
        $contents = '';
        $contents = $this->mCtrlFml->GetTextFileWithNotice();
        $contents = str_replace('###ML_TITLENAME###', $mainaddress , $contents);
        $contents = str_replace('###NEW_MEMBER###', $user_mail , $contents);

        CCommon::SendMail(
            $fromname,
            $this->mMailData->mMLMainAddress,
            $this->mMailData->mMLAdminAddress,
            'メンバー退会( ' . $user_mail . ' )',
            $contents
        );

        header( "Location: ./?page=WithdrawExit" );
        exit;
    }

    //==============================
    // 退会完了用ページの表示
    //==============================
    function GetHtml_WithdrawExit() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = $this->mTemplate->GetHTML( 'withdraw_exit.tpl' );
        $html     = str_replace( '{$ml_app_name}', $this->mAppTitleName , $html );
        echo $html;
    }

    //==============================
    // 退会エラーページの表示
    //==============================
    function GetHtml_WithdrawError() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = $this->mTemplate->GetHTML( 'withdraw_error.tpl' );
        $html     = str_replace( '{$ml_app_name}', $this->mAppTitleName , $html );
        echo $html;
    }


    //==============================
    // 記事一覧(日付)の表示ページ
    //==============================
    function GetHtml_Index() {
        //非公開の場合は何も処理しない
        if( ! $this->mConfig->mUserPanelOpen ){
            header( 'HTTP/1.0 403 Forbidden' );
            exit;
        }else{
            if( ! $this->mConfig->mUserPanelMailLog ){
                if( ! $this->mConfig->mUserPanelApply && ! $this->mConfig->mUserPanelWithdraw ){
                    header( 'HTTP/1.0 403 Forbidden' );
                    exit;
                }else{
                    $this->GetHtml_Apply();
                    exit;
                }
            }
        }

        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------
        $file_data = '';
        $mail_path = CCommon::AdjustNullValue( $_GET['mail'] );
        if( $mail_path === '' ){         $mail_path = 'index.html';     }

        //メールデータの読込
        if( $mail_path ){
            $file_data = $this->mCtrlFml->GetNewsData( $mail_path );
            if( $file_data ){
                $file_data = $this->MHonArc->ConvertHtml( $file_data, true );
            }
        }

        //ソート順を取得してレイアウトを指定
        $tmp_data = CCommon::ParseMailHtml($file_data);
        if ($tmp_data[1] == 'date_index'){
            //日付表示のスタイル
            $lyaout = 'chrono_mail_list';
        }else{
            //スレッド表示のスタイル
            $lyaout = 'thread_mail_list';
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'index.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'footer.tpl' );

        //ヘッダー情報の置換
        $header   = $this->ChangeHtml_UserHeader( $header );

        //メール情報の置換
        $contents = str_replace( '{$ml_maildata}', $tmp_data[0], $contents );

        $html     = $header . $contents . $footer;
        echo $html;


    }

    //==============================
    // 記事内容の表示ページ
    //==============================
    function GetHtml_ArticleContents() {
        //非公開の場合は何も処理しない
        if( ! $this->mConfig->mUserPanelOpen ){
            header( 'HTTP/1.0 403 Forbidden' );
            exit;
        }else{
            if( ! $this->mConfig->mUserPanelMailLog ){
                if( ! $this->mConfig->mUserPanelApply && ! $this->mConfig->mUserPanelWithdraw ){
                    header( 'HTTP/1.0 403 Forbidden' );
                    exit;
                }else{
                    $this->GetHtml_Apply();
                    exit;
                }
            }
        }

        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------
        $file_data = '';
        $mail_path = CCommon::AdjustNullValue( $_GET['mail'] );
        if( $mail_path === '' ){         $mail_path = 'index.html';     }

        //メールデータの読込
        if( $mail_path ){
            $file_data = $this->mCtrlFml->GetNewsData( $mail_path );
            if( $file_data ){
                $file_data = $this->MHonArc->ConvertHtml( $file_data );
            }
        }

        //ソート順を取得してレイアウトを指定
        $tmp_data = CCommon::ParseMailHtml($file_data);
        if ($tmp_data[1] == 'date_index'){
            //日付表示のスタイル
            $lyaout = 'chrono_mail_list';
        }else{
            //スレッド表示のスタイル
            $lyaout = 'thread_mail_list';
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html       = '';
        $header     = $this->mTemplate->GetHTML( 'header.tpl' );
        $contents   = $this->mTemplate->GetHTML( 'article_contents.tpl' );
        $reply_mail = $this->mTemplate->GetHTML( 'reply_mail.tpl' );
        $footer     = $this->mTemplate->GetHTML( 'footer.tpl' );

        //ヘッダー情報の置換
        $header   = $this->ChangeHtml_UserHeader( $header );

        //メール情報の置換
        $contents = str_replace( '{$ml_maildata}', $tmp_data[0], $contents );
        if( $this->mConfig->mUserPanelApply ){    $contents = str_replace( '{$reply_mail}', $reply_mail, $contents );
        }else{                                    $contents = str_replace( '{$reply_mail}', '', $contents );
        }

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // 記事内容の検索結果ページ
    //==============================
    function GetHtml_ArticleSearch() {
        //非公開の場合は何も処理しない
        if( ! $this->mConfig->mUserPanelOpen ){
            header( 'HTTP/1.0 403 Forbidden' );
            exit;
        }else{
            if( ! $this->mConfig->mUserPanelMailLog ){
                if( ! $this->mConfig->mUserPanelApply && ! $this->mConfig->mUserPanelWithdraw ){
                    header( 'HTTP/1.0 403 Forbidden' );
                    exit;
                }else{
                    $this->GetHtml_Apply();
                    exit;
                }
            }
        }

        //----------------------------------------
        // データの検索処理
        //----------------------------------------
        $search_word = CCommon::AdjustNullValue( $_GET['search_word'] );
        if( ! $search_word ){
            header( "Location: ./?page=Article" );
        }

        //検索データの取得
        $search_data = $this->MHonArc->SearchHtml( $search_word );
        if( ! $search_data ){
            $search_data = '該当メールはありません。';
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'article_search.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'footer.tpl' );

        //ヘッダー情報の置換
        $header   = $this->ChangeHtml_UserHeader( $header );

        //メール情報の置換
        $contents = str_replace( '{$search_word}', CCommon::EscHtml( $search_word ), $contents );
        $contents = str_replace( '{$ml_maildata}', $search_data, $contents );

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // 入退会の手続きページ
    //==============================
    function GetHtml_Apply() {
        //非公開の場合は何も処理しない
        if( ! $this->mConfig->mUserPanelOpen ){
            header( 'HTTP/1.0 403 Forbidden' );
            exit;
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'header.tpl' );
        $cnt_add  = $this->mTemplate->GetHTML( 'apply_add.tpl' );
        $cnt_del  = $this->mTemplate->GetHTML( 'apply_delete.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'footer.tpl' );

        //ヘッダー情報の置換
        $header   = $this->ChangeHtml_UserHeader( $header );

        //contentsの置き換え
        $cnt_add = str_replace( '{identity}', AUTO_KEY, $cnt_add );
        $cnt_del = str_replace( '{identity}', AUTO_KEY, $cnt_del );

        //error_txtの置き換え
        $text_add = '';
        $text_del = '';
        if (isset($this->errMsg)){
            if (strstr($this->errMsg, '退会')){
                $text_del = '<ul><li>'. $this->errMsg .'</li></ul>';
            }else{
                $text_add = '<ul><li>'. $this->errMsg .'</li></ul>';
            }
        }
        $cnt_add = str_replace( '{error_txt}', $text_add, $cnt_add );
        $cnt_del = str_replace( '{error_txt}', $text_del, $cnt_del );

        //HTMLの結合
        $html    .= $header;
        if( $this->mConfig->mUserPanelApply ){    $html .= $cnt_add; }
        if( $this->mConfig->mUserPanelWithdraw ){ $html .= $cnt_del; }
        $html    .= $footer;
        echo $html;
    }

    //==============================
    // 入退会の手続き実行ページ
    //==============================
    function GetHtml_ApplyDo() {
        //非公開の場合は何も処理しない
        if( ! $this->mConfig->mUserPanelOpen ){
            header( 'HTTP/1.0 403 Forbidden' );
            exit;
        }

        $mail     = CCommon::AdjustNullValue( $_POST['add_mail'] );
        $flag = true;

        //管理者メールアドレスに登録されていないか確認する
        if ($this->mCtrlFml->CheckModerators($mail)){
            $this->errMsg = '「'. CCommon::EscHtml( $mail ) .'」は管理者メールアドレスで登録されています。';
            $flag = false;
        }

        //上限値チェック
        $nowmember = $this->mMemberInfo;

        // 上限値の取得
        if ( $_SESSION['ML_MODE'] == 'mailinglist' ){
            $maxMember = CCtrlML::ML_MAX_MEMBER;
        } else {
            $maxMember = CCtrlML::MG_MAX_MEMBER;
        }

        if( $mail && $maxMember <= count($nowmember) ){
            $this->errMsg = '現在入会受付できません。管理者へお問い合わせ下さい。';
            $flag = false;
        }

        if (!$flag){
            $this->GetHtml_Apply();
            exit;
        }else{
            $identity = CCommon::AdjustNullValue( $_POST['identity'] );
            $add_ret = $this->mCtrlCmdMail->SendAddMail($mail, $identity);
            if( $add_ret == 0){
                header( "Location: ./?page=ApplyExit" );
                exit;
            }elseif( $add_ret == 99){
                $this->errMsg = '既に入会されています。';
                $this->GetHtml_Apply();
                exit;
            }

            $mail    = CCommon::AdjustNullValue( $_POST['delete_mail'] );
            $del_ret = $this->mCtrlCmdMail->SendDeleteMail($mail, $identity);
            if( $del_ret == 0 ){
                header( "Location: ./?page=ApplyExit" );
                exit;
            }elseif( $del_ret == 99 ){
                $this->errMsg = '既に退会済みかまたは入会されていません。';
                $this->GetHtml_Apply();
                exit;
            }
        }

        header( "Location: ./?page=Apply" );
        exit;
    }

    //==============================
    // 入退会の手続き完了ページ
    //==============================
    function GetHtml_ApplyExit() {
        //非公開の場合は何も処理しない
        if( ! $this->mConfig->mUserPanelOpen ){
            header( 'HTTP/1.0 403 Forbidden' );
            exit;
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'apply_exit.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'footer.tpl' );

        //ヘッダー情報の置換
        $header   = $this->ChangeHtml_UserHeader( $header );

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // ログアウト処理
    //==============================
    function GetHtml_Logout() {
        //$_SESSION = array();
        //session_destroy();

        $_SESSION['USER_ID']         = '';
        $_SESSION['USER_PASSWORD']   = '';

        header( "Location: ./" );
        exit;
    }


    //==============================
    // ユーザーヘッダーの変換関数
    //==============================
    function ChangeHtml_UserHeader( $strHeader ) {
        if( $this->mConfig->mUserPanelApply && $this->mConfig->mUserPanelWithdraw ){
            $strHeader   = str_replace( '{$ml_url_apply}',   '<li>[<a href="./?page=Apply">入退会手続き</a>]</li>', $strHeader );
        }else if( $this->mConfig->mUserPanelApply && !$this->mConfig->mUserPanelWithdraw ){
            $strHeader   = str_replace( '{$ml_url_apply}',   '<li>[<a href="./?page=Apply">入会手続き</a>]</li>', $strHeader );
        }else if( !$this->mConfig->mUserPanelApply && $this->mConfig->mUserPanelWithdraw ){
            $strHeader   = str_replace( '{$ml_url_apply}',   '<li>[<a href="./?page=Apply">退会手続き</a>]</li>', $strHeader );
        }else{
            $strHeader   = str_replace( '{$ml_url_apply}',   '', $strHeader );
        }

        if( $this->mConfig->mUserPanelMailLog ){
            $strHeader   = str_replace( '{$ml_url_top}',     '<li>[<a href="./">過去ログ履歴</a>]</li>', $strHeader );
        }else{
            $strHeader   = str_replace( '{$ml_url_top}',  '', $strHeader );
        }

        if( $this->mConfig->mUserPanelLogin &&
            isset( $_SESSION['USER_ID'] ) && isset( $_SESSION['USER_PASSWORD'] ) &&
            $this->mCtrlUserPwd->CheckUserPwd( $_SESSION['USER_ID'], $_SESSION['USER_PASSWORD'] ) ){

            $strTxt  = '';
            $strTxt .= '<li>[<a href="./?page=logout">ログアウト</a>]</li>';
        }

        $strHeader   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $strHeader );
        $strHeader   = str_replace( '{$ml_url_logout}',  $strTxt, $strHeader );

        return $strHeader;
    }

    //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
    //
    // HTML出力系の関数(管理者用)
    //
    //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
    //==============================
    // メンバー情報の表示ページ
    //==============================
    function GetHtml_AdminIndex() {
        //----------------------------------------
        // ユーザーの読み込み処理
        //----------------------------------------
        $userlist = $this->mMemberInfo;
        $pageno   = CCommon::AdjustNullValue($_GET['p'], 1);
        $user_memo   = CCommon::AdjustNullValue($_POST['user_memo']);
        $user_mail   = CCommon::AdjustNullValue($_POST['user_mail']);
        $user_mode   = CCommon::AdjustNullValue($_POST['user_mode']);
        $register_mode   = CCommon::AdjustNullValue($_POST['register_mode']);
        $checkmax = 1;
        $error_txt    = '';

        //最大ページの取得
        $checkmax = floor(count($userlist)/CCtrlML::eMAIL_PAGEVIEW_MAX);
        if( (count($userlist) % CCtrlML::eMAIL_PAGEVIEW_MAX) != 0 ){
            $checkmax += 1;
        }

        //ページが数字以外の場合
        if( ! is_numeric( $pageno ) ){
            $pageno   = 1;
        }
        //最大ページ以上の値の場合
        if( $pageno > $checkmax ){
            $pageno   = $checkmax;
        }
        //０以下の値の場合
        if( $pageno < 1 ){
            $pageno   = 1;
        }

        //  エラーメッセージがあるとき
        if (isset($this->errMsg)){
            $error_txt  .= '<ul><li>' . $this->errMsg . '</li></ul>';
        }else{
            //ボタンのチェック
            if( isset( $_POST['submit_button'] ) ){
                if( $user_mail == '' ){
                    $error_txt  .= '<ul><li>メールアドレスを入力して下さい</li></ul>';
                }
            }
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/index.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //---------------------
        //ヘッダー情報の置換
        //---------------------
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MEMBERS, $header );

        //---------------------
        //コンテンツ内容の置換
        //---------------------
        //ユーザー情報の置換
        $contents = str_replace( '{$user_num}', count( $userlist ), $contents );
        $contents = str_replace( '{$user_memo}', CCommon::EscHtml( $user_memo ), $contents );
        $contents = str_replace( '{$user_mail}', CCommon::EscHtml( $user_mail ), $contents );
        $contents = str_replace( '{$error_txt}', $error_txt, $contents );

        //上限値の警告
        if ($_SESSION['ML_MODE'] == 'mailinglist'){
            if(CCtrlML::ML_MAX_MEMBER <= count( $userlist )){
                $contents = str_replace( '{MaxCount}', 'メンバーの参加数が上限値に達しています！!', $contents );
                $contents = str_replace( '{disabled_1}', 'disabled', $contents );
                $contents = str_replace( '{disabled_2}', 'disabled', $contents );
                $contents = str_replace( '{lump_link}', '', $contents);
            }else{
                $contents = str_replace( '{MaxCount}', '', $contents );
                $contents = str_replace( '{disabled_1}', '', $contents );
                $contents = str_replace( '{disabled_2}', '', $contents );
                $contents = str_replace( '{lump_link}', 'href="./?page=MembersLump"', $contents);
            }
        }else{
            if(CCtrlML::MG_MAX_MEMBER <= count( $userlist )){
                $contents = str_replace( '{MaxCount}', 'ユーザーの参加数が上限値に達しています！!', $contents );
                $contents = str_replace( '{disabled_1}', 'disabled', $contents );
                $contents = str_replace( '{lump_link}', '', $contents);
            }else{
                $contents = str_replace( '{MaxCount}', '', $contents );
                $contents = str_replace( '{disabled_1}', '', $contents );
                $contents = str_replace( '{lump_link}', 'href="./?page=MembersLump"', $contents);
            }
        }

        // 一括編集リンク
        $contents = str_replace( '{members_list_link}', 'href="./?page=MembersList"', $contents);
        // 一括削除ボタン表示/非表示
        $contents = str_replace( '{disp_ml_member_del_all}', count($userlist) > 0 ? '' : 'display:none;', $contents );

        //ユーザー情報一覧の作成
        $buf_tags = '';
        if ( count($userlist) > 0 ) {
            for( $i=( ($pageno-1) * CCtrlML::eMAIL_PAGEVIEW_MAX );$i<( $pageno * CCtrlML::eMAIL_PAGEVIEW_MAX );$i++ ){
                if( ! isset( $userlist[$i]['id'] ) ){
                    break;
                }

                $buf_tags .= '<tr>';
                $buf_tags .= '<td class="txt_left">' . CCommon::EscHtml( $userlist[$i]['mail'] ) . '</td>';
                $buf_tags .= '<td class="txt_left">' . CCommon::EscHtml( $userlist[$i]['memo'] ) . '</td>';

                //メーリングリストの場合staus欄を追加
                if ($_SESSION['ML_MODE'] == 'mailinglist'){
                    $buf_tags .= '<td>' . CCommon::EscHtml( $userlist[$i]['status'] ) . '</td>';
                }
                //---
                $buf_tags .= '<td>';
                $buf_tags .= '[<a href="./?page=MembersOption&id=' . CCommon::EscHtml( $userlist[$i]['id'] ) . '">設定変更</a>]';
                $buf_tags .= '</td>';

                $buf_tags .= '<td>';
                $buf_tags .= '[<a href="./?page=MembersDelete&id=' . CCommon::EscHtml( $userlist[$i]['id'] ) . '" onclick="return DeleteMail();">削除</a>]';
                $buf_tags .= '</td>';
                //---
                $buf_tags .= '</tr>';

            }
        }
        else {
            $col        = ($_SESSION['ML_MODE'] == 'mailinglist') ? 5 : 4;
            $tmp        = ($_SESSION['ML_MODE'] == 'mailinglist') ? "メンバー" : "ユーザー";
            $buf_tags   = '<tr><td colspan="'. $col .'">'.$tmp.'が登録されていません</td></tr>';
        }
        $contents = str_replace( '{$user_list}', $buf_tags, $contents );

        //ページ送りの作成
        $buf_tags = '';
        if( count($userlist) > CCtrlML::eMAIL_PAGEVIEW_MAX ){
            //「戻る」リンク
            if( $pageno > 1 ){
                $buf_tags .= '<a href="./?p=' . ( $pageno - 1 ) . '">';
                $buf_tags .= '&lt;&lt;&nbsp;戻る';
                $buf_tags .= '</a>&nbsp;';
            }else{
                $buf_tags .= '&lt;&lt;&nbsp;戻る&nbsp;';
            }
            // ページ番号のリンク作成
            for( $i=0;$i<9;$i++ ){
                $baseno  = ( $pageno - 4 );
                if( $pageno <= 5 ){                  $baseno = 1;                 }
                if( $pageno > ( $checkmax - 5 ) ){   $baseno = ( $checkmax - 8 ); }
                if( $baseno <= 0 ){                  $baseno = 1;                 }

                if( ( $baseno + $i ) > $checkmax ){    continue;    }

                if( $pageno != ( $baseno + $i ) ){
                    $buf_tags .= '&nbsp;<a href="./?p=' . ( $baseno + $i ) . '">';
                    $buf_tags .= '[' . ( $baseno + $i ) . ']';
                    $buf_tags .= '</a>&nbsp;';
                }else{
                    $buf_tags .= '&nbsp;[' . ( $baseno + $i ) . ']&nbsp;';
                }
            }
            //「次へ」リンク
            if( $pageno < $checkmax ){
                $buf_tags .= '&nbsp;<a href="./?p=' . ( $pageno + 1 ) . '">';
                $buf_tags .= '次へ&nbsp;&gt;&gt;';
                $buf_tags .= '</a>';
            }else{
                $buf_tags .= '&nbsp;次へ&nbsp;&gt;&gt;';
            }
        }
        $contents = str_replace( '{$page_link}', $buf_tags, $contents );

        //---------------------
        //HTML連結
        //---------------------
        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // メンバー情報の一括登録ページ
    //==============================
    function GetHtml_AdminMembersLump() {
        $user_mail_lump = CCommon::AdjustNullValue($_POST['user_mail_lump']);
        $register_mode = CCommon::AdjustNullValue($_POST['register_mode']);

        //エラーのチェック
        $error_txt    = '';
        if( (isset( $_POST['sb_ml_member_chk'] ) ||
            isset( $_POST['sb_ml_member_add'] ) ||
            isset( $_POST['sb_mg_member_add'] ) )
            && isset( $_POST['user_mail_lump'] ) ){
            //$user_mail_lump = htmlspecialchars( $user_mail_lump,  ENT_QUOTES, "UTF-8" );
            $user_mail_lump = trim( $user_mail_lump );
            $user_mail_lump = str_replace( "\r\n", "\n", $user_mail_lump );
            $user_mail_lump = str_replace( "\r",   "\n", $user_mail_lump );
            $maillist       = explode( "\n", $user_mail_lump );
            $error_txt  .= '<ul>';
            if( $user_mail_lump == '' ){
                $error_txt  .= '<li>メールアドレスを入力して下さい</li>';
            }else{
                if (count($this->errMail) > 0 ){
                    foreach($this->errMail as $key => $value){
                        $error_txt  .= '<li>「' . $value[$key] . '」は既にメンバーとして登録されているか、管理者メールアドレスとして登録されています。</li>';

                    }
                }
                foreach( $maillist as $value ){
                    list($mailaddress, $memo) = explode(",",$value,2);
                    if( $mailaddress == '' ){
                        continue;
                    }
                    if( ! CCommon::IsMailAddress( $mailaddress ) || $this->IsNgMailAddress( $mailaddress ) ){
                        $error_txt  .= '<li>「' . $mailaddress . '」は登録できません。</li>';
                    }
                }
            }
            $error_txt  .= '</ul>';
        }

        if( isset($this->errMsg)){
            $error_txt .= '<ul><li>';
            $error_txt .= $this->errMsg;
            $error_txt .= '</li></ul>';
        }

        //---------------------
        //ヘッダー情報の置換
        //---------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/members_lump.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MEMBERS, $header );

        $contents = str_replace( '{$error_txt}', $error_txt, $contents );
        $contents = str_replace( '{$user_mail_lump}', CCommon::EscHtml( $user_mail_lump ), $contents );

        $html     = $header . $contents . $footer;
        echo $html;
    }


    //==============================
    // メンバー設定の新規追加
    //==============================
    function GetHtml_AdminMembersAdd() {
        $user_mail = CCommon::AdjustNullValue($_POST['user_mail']);
        $user_memo = CCommon::AdjustNullValue($_POST['user_memo']);
        $user_mode  = CCommon::AdjustNullValue($_POST['user_mode']);
        $register_mode  = CCommon::AdjustNullValue($_POST['register_mode']);
        $this->errMsg = '';
        $flag = true;

        if(CCtrlML::MAXLENGTH_USERNAME < mb_strlen($user_memo,'UTF-8')){
            $this->errMsg = 'メモは20文字以下で入力して下さい<br/>';
            $flag = false;
        }

        if(strlen($user_mail) <= 0){
            $this->errMsg = 'メールアドレスを入力して下さい';
            $flag = false;

        }else{
            //除外判定
            if( ! CCommon::IsMailAddress( $user_mail ) || $this->IsNgMailAddress( $user_mail ) ){
                $flag = false;
                $this->errMsg  .= '「' . $user_mail . '」は登録できません。';
            }
        }

    	$memberlist = $this->mMemberInfo;
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            if (CCtrlML::ML_MAX_MEMBER <= count($memberlist)){
                $this->errMsg = 'メンバー数が上限値を超えてしまうので、登録できません。';
                $flag = false;
            }
        }else{
            if (CCtrlML::MG_MAX_MEMBER <= count($memberlist)){
                $this->errMsg = 'ユーザー数が上限値を超えてしまうので、登録できません。';
                $flag = false;
            }
        }

        $confirm    = false;

        //除外判定：登録処理の場合
        if( isset( $_POST['sb_ml_member_add'] ) ||
            isset( $_POST['sb_mg_member_add'] ) ){
            if( $register_mode != 3){
                //$welcome  = htmlspecialchars( $_POST['rdo_welcome'],  ENT_QUOTES, "UTF-8" );
                $this->comitMsg = '登録が完了しました。';
            }else{
                $this->comitMsg = '入会確認のメールを送信しました';
                $confirm = true;
            }
        }


        //メーリングリストの場合、members・actives・members-adminに登録済みでないか確認する
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            if ($this->mCtrlFml->CheckMemberUser($user_mail) or $this->mCtrlFml->CheckActiveUser($user_mail) or $this->mCtrlFml->CheckMemberAdmin($user_mail)){
                $this->errMsg = '「'. CCommon::EscHtml( $user_mail ) .'」は既に登録されています。';
                $flag = false;
            }
        }else{
            if ($this->mCtrlFml->CheckActiveUser($user_mail)){
                $this->errMsg = '「'. CCommon::EscHtml( $user_mail ) .'」は既に登録されています。';
                $flag = false;
            }
        }

        //管理者メールアドレスに登録されていないか確認する
        if ($this->mCtrlFml->CheckModerators($user_mail)){
            $this->errMsg = '「'. CCommon::EscHtml( $user_mail ) .'」は管理者メールアドレスで登録されています。';
            $flag = false;
        }

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->GetHtml_AdminIndex();
            exit;
        }else{

            //メンバーの新規追加
            if(!$confirm){
                if( $_SESSION['ML_MODE'] == 'mailinglist' ){
                    //ML名を送信者名に設定
                    $fromname = $this->mConfig->mAdminAppName;

                    switch ($user_mode) {
                        //受信＋配信の場合
                        case CCtrlML::ML_AUTH_MEMBERS:
                            $this->mCtrlFml->AddMemberUser( $user_mail );
                            break;
                        //受信のみの場合
                        case CCtrlML::ML_AUTH_ACTIVES:
                            $this->mCtrlFml->AddActiveUser( $user_mail );
                            break;
                        //配信のみの場合
                        case CCtrlML::ML_AUTH_MEMBERS_ADMIN:
                            $this->mCtrlFml->AddMembersAdmin( $user_mail );
                            break;
                    }
                }else{
                    //メールマガジン
                    $this->mCtrlFml->AddActiveUser( $user_mail );

                    //送信者名を取得
                    $fromname = $this->mConfig->mAdminFromName;
                    if (!$fromname || $fromname == ''){
                        $fromname = $this->mConfig->mAdminAppName;
                    }
                }

                //ユーザー名とアドレス一覧に登録する
                if ($user_memo != ''){
                    $memo = mb_convert_encoding( $user_memo,"SJIS-WIN", "UTF-8" );
                    $this->mCtrlFml->putMemberName( $user_mail , $memo);
                }

                //登録完了メールを新規メンバーに送信
                if ($register_mode == 1){
                    CCommon::SendMail(
                            $fromname,
                            $this->mMailData->mMLMainAddress,
                            $user_mail,
                            $this->mConfig->mSystemMailWelcomeSubject,
                            $this->mCtrlFml->GetTextFileWelcome()
                    );
                }

            }else{
                //入会確認の場合
                $address = array();
                $address[0] = $user_mail . ',' . $user_memo;
                $this->mCtrlCmdMail->SendAddMailLearge($address);
            }
        }

        header( "Location: ./?page=MembersExit" );
        exit;
    }

    //==============================
    // メンバー設定の一括の新規追加
    //==============================
    function GetHtml_AdminMembersAddLump() {

        $flag            = true;
        $this->comitMsg = '';
        $user_mail_lump = CCommon::AdjustNullValue($_POST['user_mail_lump']);
        $user_mode  = CCommon::AdjustNullValue($_POST['user_mode']);
        $register_mode  = CCommon::AdjustNullValue($_POST['register_mode']);
        //$welcome = CCommon::AdjustNullValue($_POST['rdo_welcome']);

        if( $user_mail_lump == '' ){ $flag = false; }

        //除外判定：登録処理の場合
        if( isset( $_POST['sb_ml_member_add'] ) ||
            isset( $_POST['sb_mg_member_add'] ) ){
            if( $register_mode != 3){
                //$welcome  = htmlspecialchars( $_POST['rdo_welcome'],  ENT_QUOTES, "UTF-8" );
                $this->comitMsg = '登録が完了しました。';
            }else{
                $this->comitMsg = '入会確認のメールを送信しました';
                $confirm = true;
            }
        }

        $user_mail_lump = trim( $user_mail_lump );
        $user_mail_lump = str_replace( "\r\n", "\n", $user_mail_lump );
        $user_mail_lump = str_replace( "\r",   "\n", $user_mail_lump );
        //$maillist       = explode( "\n", $user_mail_lump );
        $tmpMaillist       = explode( "\n", $user_mail_lump );
        $maillist = array();
        foreach($tmpMaillist as $val){
            $tmp = trim($val);
            if($tmp != ""){
                $maillist[] = $tmp;
            }
        }

        //一括登録数が上限値を超える場合エラー
        $nowmember = $this->mMemberInfo;
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            if(CCtrlML::ML_MAX_MEMBER < count($nowmember) + count($maillist)){
                $this->errMsg = 'メンバー数が上限値を超えてしまうので、全件登録できません。';
                $flag = false;
            }
        }else{
            if(CCtrlML::MG_MAX_MEMBER < count($nowmember) + count($maillist)){
                $this->errMsg = 'ユーザー数が上限値を超えてしまうので、全件登録できません。';
                $flag = false;
            }
        }

        $count = 0;
        if( $flag ){
            foreach( $maillist as $value ){
                $mailaddress = '';
                $memo = '';
                if (strpos($value, ',') !== false){
                    list($mailaddress, $memo) = explode(",",$value,2);
                }else{
                    $mailaddress = $value;
                }
                if( $mailaddress == '' ){    continue; }
                //メールアドレスではない、もしくは登録不可メールの場合はハジく
                if( ! CCommon::IsMailAddress( $mailaddress ) ||
                    $this->IsNgMailAddress( $mailaddress ) ){
                    $flag = false;
                    break;
                }

                if ($_SESSION['ML_MODE'] == 'mailinglist'){
                    //メーリングリストの場合、members/actives/members-adminファイルの重複チェック
                    if ($this->mCtrlFml->CheckMemberUser($mailaddress) || $this->mCtrlFml->CheckActiveUser($mailaddress) || !$this->mCtrlFml->CheckMemberAdmin($user_mail)){
                        $this->errMail[$count] = array($count => $mailaddress);
                        $count++ ;
                        $flag = false;
                    }

                    //管理者メールアドレスに登録されていないか確認する
                    if ($this->mCtrlFml->CheckModerators($mailaddress)){
                        $this->errMail[$count] = array($count => $mailaddress);
                        $count++ ;
                        $flag = false;
                    }

                }else{
                    //メールマガジンの場合、activesファイルの重複チェック
                    if ($this->mCtrlFml->CheckActiveUser($mailaddress)){
                        $this->errMail[$count] = array($count => $mailaddress);
                        $count++ ;
                        $flag = false;
                    }

                    //管理者メールアドレスに登録されていないか確認する
                    if ($this->mCtrlFml->CheckModerators($mailaddress)){
                        $this->errMail[$count] = array($count => $mailaddress);
                        $count++ ;
                        $flag = false;
                    }

                }

                if(CCtrlML::MAXLENGTH_USERNAME <= mb_strlen($memo,'UTF-8')){
                    $this->errMsg = 'メモは20文字以下で入力して下さい';
                    $flag = false;
                }
            }
        }

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->GetHtml_AdminMembersLump();
            exit;
        }

        $address = array();
        $txt_memo = array();

        // システムメールの送信者名を設定
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            //ML名を送信者名に設定
            $fromname = $this->mConfig->mAdminAppName;
        }else{
            //送信者名を取得
            $fromname = $this->mConfig->mAdminFromName;
            if (!$fromname || $fromname == ''){
                $fromname = $this->mConfig->mAdminAppName;
            }
        }
        foreach( $maillist as $row => $value ){
            $mailaddress = '';
            $memo = '';
            $value = trim( $value );
            if (strpos($value, ',') !== false){
                list($mailaddress, $memo) = explode(",",$value,2);
            }else{
                    $mailaddress = $value;
            }
            if( $mailaddress == '' ){                          continue;    }
            if( ! CCommon::IsMailAddress( $mailaddress ) ){    continue;    }

            //登録処理
            if (!$confirm){
                if( $_SESSION['ML_MODE'] == 'mailinglist' ){
                    //メーリングリスト
                    switch ($user_mode) {
                        //受信＋配信の場合
                        case CCtrlML::ML_AUTH_MEMBERS:
                            $this->mCtrlFml->AddMemberUser( $mailaddress );
                            break;
                        //受信のみの場合
                        case CCtrlML::ML_AUTH_ACTIVES:
                            $this->mCtrlFml->AddActiveUser( $mailaddress );
                            break;
                        //配信のみの場合
                        case CCtrlML::ML_AUTH_MEMBERS_ADMIN:
                            $this->mCtrlFml->AddMembersAdmin( $mailaddress );
                            break;
                    }
                }else{
                    //メールマガジン
                    $this->mCtrlFml->AddActiveUser( $mailaddress );
                }
                //メモを登録
                if ($memo != ''){
                    $memo_txt = mb_convert_encoding( $memo,'SJIS-WIN', 'UTF-8' );
                    $this->mCtrlFml->putMemberName($mailaddress, $memo_txt);
                }

                //登録完了メールを新規メンバーに送信
                if ($register_mode == 1){
                    CCommon::SendMail(
                            $fromname,
                            $this->mMailData->mMLMainAddress,
                            //$value,
                            $mailaddress,
                            $this->mConfig->mSystemMailWelcomeSubject,
                            $this->mCtrlFml->GetTextFileWelcome()
                    );
                }
            }
            $address[$row] = $mailaddress . ',' . $memo;
        }

        //入会確認メールを送信する
        if ($confirm){
            $this->mCtrlCmdMail->SendAddMailLearge($address);
        }


        header( "Location: ./?page=MembersLumpExit" );
        exit;
    }

    //==============================
    // メンバー一覧ページ
    //==============================
    function GetHtml_AdminMembersList() {
        $user_list  = $this->mMemberInfo;
        $user_mail_lump_actives         = '';
        $user_mail_lump_membaers        = '';
        $user_mail_lump_membaers_admin  = '';

        foreach ($user_list as $val) {
            $line   = $val['mail'].",".$val['memo']."\n";
            switch ($val['mode']) {
                // 受信＋配信
                case CCtrlML::ML_AUTH_MEMBERS:
                    $user_mail_lump_membaers        .= $line;
                    break;
                // 受信
                case CCtrlML::ML_AUTH_ACTIVES:
                    $user_mail_lump_actives         .= $line;
                    break;
                // 配信
                case CCtrlML::ML_AUTH_MEMBERS_ADMIN:
                    $user_mail_lump_membaers_admin  .= $line;
                    break;
            }
        }

        //---------------------
        //ヘッダー情報の置換
        //---------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/members_list.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', CCommon::EscHtml( $this->mAppTitleName ), $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MEMBERS, $header );

        $contents = str_replace( '{$user_mail_lump_actives}', CCommon::EscHtml( $user_mail_lump_actives ), $contents );
        $contents = str_replace( '{$user_mail_lump_membaers}', CCommon::EscHtml( $user_mail_lump_membaers ), $contents );
        $contents = str_replace( '{$user_mail_lump_membaers_admin}', CCommon::EscHtml( $user_mail_lump_membaers_admin ), $contents );
        for( $i=1;$i<=CCtrlML::ML_AUTH_NUM;$i++ ){
            if( $i == 1 ){
                $contents = str_replace( '{$current_file_0' . $i . '}', 'current_tag_file', $contents );
                $contents = str_replace( '{$display_file_0' . $i . '}', 'display:block',    $contents );
            }else{
                $contents = str_replace( '{$current_file_0' . $i . '}', '',                 $contents );
                $contents = str_replace( '{$display_file_0' . $i . '}', 'display:none',     $contents );
            }
        }

        $html     = $header . $contents . $footer;
        echo $html;
    }



    //==============================
    // メンバー設定の追加完了
    //==============================
    function GetHtml_AdminMembersExit() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/members_exit.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MEMBERS, $header );

        if(isset($this->comitMsg) && $this->comitMsg != ''){
            $contents   = str_replace( '{message}', $this->comitMsg, $contents );
        }else{
            $contents   = str_replace( '{message}', '処理が完了しました。', $contents );
        }

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // メンバー設定の一括追加完了
    //==============================
    function GetHtml_AdminMembersLumpExit() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/members_lump_exit.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MEMBERS, $header );

        //contents
        if(isset($this->comitMsg)){
            $contents = str_replace( '{message}' , $this->comitMsg, $contents);
        }else{
            $contents = str_replace( '{message}' , '処理が完了しました。', $contents);
        }

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // メンバー詳細変更ページ
    //==============================
    function GetHtml_AdminMembersOption() {
        if (isset($this->mDispContents)){
            $dispvalue = explode(',', $this->mDispContents);
            $id = $dispvalue[0];
            $user_mail = $dispvalue[1];
            $user_memo = $dispvalue[2];
            $status = $dispvalue[3];
            $userlist = $dispvalue[4];
        }else{
            $id = CCommon::AdjustNullValue($_GET['id'] );
            $userlist = $this->mMemberInfo;
            $user_mail = $userlist[$id]['mail'];
            $user_memo = $userlist[$id]['memo'];
            $status    = $userlist[$id]['mode'];
        }

        //---------------------
        //ヘッダー情報の置換
        //---------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/members_option.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MEMBERS, $header );

        //エラーメッセージ
        if( isset($this->errMsg)){
            $error_txt .= '<ul><li>';
            $error_txt .= $this->errMsg;
            $error_txt .= '</li></ul>';
        }
        $contents = str_replace( '{$error_txt}', $error_txt, $contents );

        //コンテンツ
        $contents = str_replace( '{$user_mail}', CCommon::EscHtml( $user_mail ), $contents );
        $contents = str_replace( '{$user_memo}', CCommon::EscHtml( $user_memo ), $contents );
        $contents = str_replace( '{$user_id}', CCommon::EscHtml( $id ), $contents );

        for ($i=1; $i <= CCtrlML::ML_AUTH_NUM; $i++) {
            if ( $i == $status ) {
                $contents = str_replace( '{$user_mode_'.$i.'}', 'selected', $contents );
            }
            else {
                $contents = str_replace( '{$user_mode_'.$i.'}', '', $contents );
            }
        }

        $html     = $header . $contents . $footer;
        echo $html;
    }


    //==============================
    // メンバー詳細更新
    //==============================
    function GetHtml_AdminMembersOptionDo() {
        $flag       = true;
        $user_id    = CCommon::AdjustNullValue($_POST['user_id'] );
        $user_mail  = CCommon::AdjustNullValue($_POST['user_mail'] );
        $user_memo  = CCommon::AdjustNullValue($_POST['user_memo'] );
        $user_mode  = CCommon::AdjustNullValue($_POST['user_mode'] );
        $userlist = $this->mMemberInfo;

        //除外判定
        if( ! isset( $_POST['sb_setting_save'] ) ){   $flag = false; }

        if(CCtrlML::MAXLENGTH_USERNAME < mb_strlen($user_memo, 'UTF-8')){
            $this->errMsg = 'メモは20文字以下で入力して下さい';
            $flag = false;
        }

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->mDispContents = $user_id . ',' . $user_mail . ',' . $user_memo . ',' . $user_mode;
            $this->GetHtml_AdminMembersOption();
            exit;
        }else{
            if( $_SESSION['ML_MODE'] == 'mailinglist' ){
                $this->mCtrlFml->DeleteCompleteMemberUser( $user_mail );
                $this->mCtrlFml->DeleteCompleteActiveUser( $user_mail );
                $this->mCtrlFml->DeleteMembersAdmin( $user_mail );
                switch ($user_mode) {
                    //権限を受信＋配信に変更
                    case CCtrlML::ML_AUTH_MEMBERS:
                        $this->mCtrlFml->AddMemberUser( $user_mail );
                        break;
                    //受信のみに変更
                    //メールアドレスが変更されていない場合
                    case CCtrlML::ML_AUTH_ACTIVES:
                        $this->mCtrlFml->AddActiveUser( $user_mail );
                        break;
                    //配信のみに変更
                    case CCtrlML::ML_AUTH_MEMBERS_ADMIN:
                        $this->mCtrlFml->AddMembersAdmin( $user_mail );
                        break;
                }
            }

            //ユーザー名とアドレス一覧更新
            if ($user_memo != ''){
                $memo = mb_convert_encoding( $user_memo,'SJIS-WIN', 'UTF-8' );
                $this->mCtrlFml->putMemberName( $user_mail, $memo );
            }else{
                $this->mCtrlFml->deleteMemberName( $user_mail, $memo );
            }

        }

        header( "Location: ./?page=MembersOptionExit" );
        exit;
    }


    //==============================
    // メンバー詳細の更新完了
    //==============================
    function GetHtml_AdminMembersOptionExit() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------

        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/members_option_exit.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MEMBERS, $header );

        $html     = $header . $contents . $footer;
        echo $html;
    }


    //==============================
    // メンバー設定の削除
    //==============================
    function GetHtml_AdminMembersDelete() {
        //----------------------------------------
        // ユーザーの読み込み処理
        //----------------------------------------
        $userlist = $this->mMemberInfo;
        $mailno   = CCommon::AdjustNullValue($_GET['id'], 0);
        $ctrlErrMail = new CCtrlErrMail($this->mCtrlFml->GetMLPath());
        $delete_mails = array();
        //if( isset( $_GET['id'] ) ){
        //    $mailno   = htmlspecialchars( $_GET['id'],  ENT_QUOTES, "UTF-8" );
        //}

        //ユーザー情報一覧の作成
        for( $i=0;$i<count($userlist);$i++ ){
            if( $mailno == $userlist[$i]['id'] ){
                $this->mCtrlFml->DeleteActiveUser( $userlist[$i]['mail'] );
                $this->mCtrlFml->DeleteMemberUser( $userlist[$i]['mail'] );
                $this->mCtrlFml->deleteMemberName( $userlist[$i]['mail'] );
                $this->mCtrlFml->DeleteMembersAdmin( $userlist[$i]['mail'] );
                array_push($delete_mails, ($userlist[$i]['mail']));
                break;
            }
        }
        $ctrlErrMail->UnsetSummaryData($delete_mails);

        header( 'Location: ./' );
        exit;
    }

    //==============================
    // メンバー設定の削除
    // $delete_mails    : 削除対象アドレス一覧 # array("xxx@yyy.jp", "iii@jjj.com", "nnn@mmm.co.jp", ...)
    //==============================
    function MembersDeleteForAddr($delete_mails) {
        $userlist = $this->mMemberInfo;
        if ( count($delete_mails) > 0 ) {
            for( $i=0;$i<count($userlist);$i++ ){
                if ( array_search($userlist[$i]['mail'], $delete_mails) !== FALSE ) {
                    $this->mCtrlFml->DeleteActiveUser( $userlist[$i]['mail'] );
                    $this->mCtrlFml->DeleteMemberUser( $userlist[$i]['mail'] );
                    $this->mCtrlFml->deleteMemberName( $userlist[$i]['mail'] );
                    $this->mCtrlFml->DeleteMembersAdmin( $userlist[$i]['mail'] );
                }
            }
        }
    }


    //==============================
    // メンバー設定の追加
    // $delete_mails    : 追加アドレス一覧
    //==============================
    function MembersRegForAddr($reg_mails) {
        $result = array();
        $flag = true;

        //一括登録数が上限値を超える場合エラー
        $nowmember = $this->mMemberInfo;
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            if(CCtrlML::ML_MAX_MEMBER < count($nowmember) + count($reg_mails)){
                $this->errMsg = 'メンバー数が上限値を超えてしまうので、全件登録できません。';
                $flag = false;
            }
        }else{
            if(CCtrlML::MG_MAX_MEMBER < count($nowmember) + count($reg_mails)){
                $this->errMsg = 'ユーザー数が上限値を超えてしまうので、全件登録できません。';
                $flag = false;
            }
        }

        if( ! $flag ){
            exit;
        }

        foreach( $reg_mails as $mailaddress => &$value ){
            $flag = true;

            //メールアドレスではない、もしくは登録不可メールの場合はハジく
            if( ! CCommon::IsMailAddress( $mailaddress ) ||
                $this->IsNgMailAddress( $mailaddress ) ){
                    array_push($this->errMail, $mailaddress);
            }

            if ($_SESSION['ML_MODE'] == 'mailinglist'){
                //管理者メールアドレスに登録されていないか確認する
                if ($this->mCtrlFml->CheckModerators($mailaddress)){
                    array_push($this->errMail, $mailaddress);
                }

                //membersとactivesに登録済みでないか確認する
                if ($this->mCtrlFml->CheckMemberUser($mailaddress) || $this->mCtrlFml->CheckActiveUser($mailaddress)){
                    array_push($this->errMail, $mailaddress);
                }
            }else{
                //メールマガジンの場合、activesファイルの重複チェック
                if ($this->mCtrlFml->CheckActiveUser($mailaddress)){
                    array_push($this->errMail, $mailaddress);
                }
            }

            //チェックに引っかかったアドレスは登録しない
            if( ! $flag ){
                continue;
            }

            //登録処理
            if( $_SESSION['ML_MODE'] == 'mailinglist' ){
                //メーリングリスト
                if ($value['status'] == 'メール受信＋メール配信'){
                    //受信＋配信の場合
                    $this->mCtrlFml->AddMemberUser( $mailaddress );
                }else{
                    //受信のみの場合
                    $this->mCtrlFml->AddActiveUser( $mailaddress );
                }
            }else{
                //メールマガジン
                $this->mCtrlFml->AddActiveUser( $mailaddress );
            }

            //メモを登録
            $memo = $value['memo'];
            if ($memo != ''){
                $memo_txt = mb_convert_encoding( $memo,'SJIS-WIN', 'UTF-8' );
                $this->mCtrlFml->putMemberName($mailaddress, $memo_txt);
            }

            $result[$mailaddress] = $value;
        }

        return $result;
    }


    //==============================
    // メンバー設定の一括削除
    //==============================
    function GetHtml_AdminMembersDeleteAll() {
        //----------------------------------------
        // ユーザーの読み込み処理
        //----------------------------------------
        $userlist = $this->mMemberInfo;
        $ctrlErrMail = new CCtrlErrMail($this->mCtrlFml->GetMLPath());
        $delete_mails = array();

        //ユーザー情報一覧の作成
        for( $i=0;$i<count($userlist);$i++ ){
            $this->mCtrlFml->DeleteActiveUser( $userlist[$i]['mail'] );
            $this->mCtrlFml->DeleteMemberUser( $userlist[$i]['mail'] );
            $this->mCtrlFml->deleteMemberName( $userlist[$i]['mail'] );
            $this->mCtrlFml->DeleteMembersAdmin( $userlist[$i]['mail'] );
            array_push($delete_mails, $userlist[$i]['mail']);
        }
        $ctrlErrMail->UnsetSummaryData($delete_mails);

        header( 'Location: ./' );
        exit;
    }

    //==============================
    // メールの新規作成ページ
    //==============================
    function GetHtml_AdminMailCreate() {
        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------
        $error_txt     = '';

        $mail_subject = CCommon::AdjustNullValue($_POST['mail_subject'], $this->mConfig->mAdminTitleName);
        $ml_add_header = CCommon::AdjustNullValue($_POST['header_txt']);
        $mail_contents = CCommon::AdjustNullValue($_POST['mail_contents']);
        $ml_add_footer = CCommon::AdjustNullValue($_POST['footer_txt']);
        $send_contents = CCommon::AdjustNullValue($_POST['send_contents']);
        $auto_number   = CCommon::AdjustNullValue($_POST['auto_number'], $this->mConfig->mAdminAutoNumber);

        $ml_name         = $this->mCtrlFml->GetTitleName();
        $ml_subject_mode = $this->mCtrlFml->GetTitleForm();
        $ml_add_subject  = '';
        switch( $ml_subject_mode ){
            case 1:    $ml_add_subject  = '[' . $ml_name . ':1234]';    break;
            case 2:    $ml_add_subject  = '(' . $ml_name . ':1234)';    break;
            case 3:    $ml_add_subject  = '[' . $ml_name . ',1234]';    break;
            case 4:    $ml_add_subject  = '(' . $ml_name . ',1234)';    break;
            case 5:    $ml_add_subject  = '[1234]';                     break;
            case 6:    $ml_add_subject  = '(1234)';                     break;
            case 7:    $ml_add_subject  = '[' . $ml_name . ']';         break;
            case 8:    $ml_add_subject  = '(' . $ml_name . ')';         break;
        }

        //ヘッダーの置換
        $ml_add_header   = $this->mCtrlFml->GetTextFileMgHeader();
        $ml_add_header = str_replace( '{year}', date('Y'), $ml_add_header );
        $ml_add_header = str_replace( '{month}', date('n'), $ml_add_header );
        $ml_add_header = str_replace( '{day}', date('j'), $ml_add_header );
        $ml_add_header = str_replace( '{auto_number}', $auto_number, $ml_add_header);

        //  件名の置換
        $mail_subject = str_replace( '{year}', date('Y'), $mail_subject );
        $mail_subject = str_replace( '{month}', date('n'), $mail_subject );
        $mail_subject = str_replace( '{day}', date('j'), $mail_subject );
        $mail_subject = str_replace( '{auto_number}', $auto_number, $mail_subject);

        //$ml_add_header = str_replace( '{hour}', date('H:i:s'), $ml_add_header );

        $ml_add_footer = $this->mCtrlFml->GetTextFileMgFooter();
        $url           = 'http://' . $_SERVER["SERVER_NAME"]. '/xmailinglist/' . ML_NAME . '/';

        //文言変更前の置換用文字列対応
        $ml_add_footer = str_replace( '###購読解除用URL###', ' ' . $url . ' ', $ml_add_footer );

        $ml_add_footer = str_replace( '###退会用URL###', ' ' . $url . ' ', $ml_add_footer );

        //  保存時に付与している改行を削除して表示
        $ml_add_header   = mb_substr($ml_add_header, 0, mb_strlen($ml_add_header)-1);
        $ml_add_footer   = mb_substr($ml_add_footer, 0, mb_strlen($ml_add_footer)-1);

        $send_contents = $ml_add_header . "\n" . $mail_contents . "\n" . $ml_add_footer;

        $ml_add_header = str_replace( "<br>", '\n', $ml_add_header );
        $ml_add_footer = str_replace( "<br>", '\n', $ml_add_footer );

        if( isset( $_POST['sb_confirm'] ) && ( $mail_subject == '' || $mail_contents == '' ) ){
            $error_txt  .= '<ul>';
            if( $mail_subject == '' ){
                $error_txt  .= '<li>';
                $error_txt  .= '件名を入力して下さい';
                $error_txt  .= '</li>';
            }
            if( $mail_contents == '' ){
                $error_txt  .= '<li>';
                $error_txt  .= '本文を入力して下さい';
                $error_txt  .= '</li>';
            }
            $error_txt  .= '</ul>';
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/mail_create.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MAIL, $header );

        //本文情報の置換
        $contents = str_replace( '{$subject_txt}', $ml_add_subject, $contents );
        $contents = str_replace( '{$header_txt}', CCommon::EscHtml($ml_add_header), $contents );
        $contents = str_replace( '{$footer_txt}', CCommon::EscHtml($ml_add_footer), $contents );
        $contents = str_replace( '{$mail_subject}', CCommon::EscHtml( $mail_subject ), $contents );
        $contents = str_replace( '{$mail_contents}',CCommon::EscHtml( $mail_contents ), $contents );
        $contents = str_replace( '{$send_contents}',CCommon::EscHtml( $send_contents ), $contents );

        $contents = str_replace( '{$error_txt}', $error_txt, $contents );

        $html     = $header . $contents . $footer;
        echo $html;
    }


    //==============================
    // メールの内容確認ページ
    //==============================
    function GetHtml_AdminMailConfirm() {
        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------
        $flag          = true;
        $mail_subject  = CCommon::AdjustNullValue( $_POST['mail_subject'] );
        $ml_add_header = CCommon::AdjustNullValue($_POST['header_txt']);
        $mail_contents = CCommon::AdjustNullValue($_POST['mail_contents']);
        $ml_add_footer = CCommon::AdjustNullValue($_POST['footer_txt']);
        $send_contents = CCommon::AdjustNullValue( $_POST['send_contents'] );

        //除外判定
        if( ! $_POST['sb_confirm'] ){     $flag = false; }
        if( $mail_subject  == '' ){     $flag = false; }
        if( $mail_contents == '' ){     $flag = false; }

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->GetHtml_AdminMailCreate();
            exit;
        }

        //$mail_view_contents = str_replace( "\n", '<br />', $mail_contents );
        $mail_contents .= "\n";
        $ml_add_header .= "\n";
        $ml_add_footer .= "\n";
        //$ml_add_header = str_replace( "\n", '<br>', $ml_add_header );
        //$ml_add_footer = str_replace( "\n", '<br>', $ml_add_footer );


        $ml_name         = $this->mCtrlFml->GetTitleName();
        $ml_subject_mode = $this->mCtrlFml->GetTitleForm();
        $ml_add_subject  = '';
        switch( $ml_subject_mode ){
            case 1:    $ml_add_subject  = '[' . $ml_name . ':1234]';    break;
            case 2:    $ml_add_subject  = '(' . $ml_name . ':1234)';    break;
            case 3:    $ml_add_subject  = '[' . $ml_name . ',1234]';    break;
            case 4:    $ml_add_subject  = '(' . $ml_name . ',1234)';    break;
            case 5:    $ml_add_subject  = '[1234]';                     break;
            case 6:    $ml_add_subject  = '(1234)';                     break;
            case 7:    $ml_add_subject  = '[' . $ml_name . ']';    break;
            case 8:    $ml_add_subject  = '(' . $ml_name . ')';    break;
        }

        //メール本文にヘッダーとフッターを追加
        $send_contents =        $ml_add_header  . $mail_contents . $ml_add_footer;
        $mail_view_contents =   $ml_add_header  . $mail_contents . $ml_add_footer;

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/mail_confirm.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MAIL, $header );

        //本文情報の置換
        $contents = str_replace( '{$subject_txt}', $ml_add_subject, $contents );
        $contents = str_replace( '{$mail_subject}',  CCommon::EscHtml( $mail_subject ), $contents );
        $contents = str_replace( '{$mail_contents}', CCommon::EscHtml( $mail_contents ), $contents );
        $contents = str_replace( '{$send_contents}', CCommon::EscHtml( $send_contents), $contents );
        $contents = str_replace( '{$mail_view_contents}', $mail_view_contents , $contents );
        $contents = str_replace( '{$header_txt}', CCommon::EscHtml($ml_add_header), $contents );
        $contents = str_replace( '{$footer_txt}', CCommon::EscHtml($ml_add_footer), $contents );

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // メールの送信処理
    //==============================
    function GetHtml_AdminMailDo() {
        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------

        $mail_subject  = CCommon::AdjustNullValue( $_POST['mail_subject'] );
        $send_contents = CCommon::AdjustNullValue( $_POST['send_contents'] );
        //$mail_contents = CCommon::AdjustNullValue( $_POST['mail_contents'] );
        $flag          = true;

        //除外判定
        if( ! isset( $_POST['sb_mail_send'] ) ){    $flag = false;}
        if( $mail_subject  == '' ){                 $flag = false;}
        if( $send_contents == '' ){                 $flag = false;}

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->GetHtml_AdminMailCreate();
            exit;
        }

        // members_adminからの管理者アドレス消失対策
        $ml_mailaddress = ML_NAME . '@' . DOMAIN_NAME;
        if (!$this->mCtrlFml->CheckMemberAdmin($ml_mailaddress)) {
            $this->mCtrlFml->UpdateMembersAdmin($ml_mailaddress);
        }
        $ml_admin_mail = ML_NAME . '-admin@' . DOMAIN_NAME;
        if (!$this->mCtrlFml->CheckMemberAdmin($ml_admin_mail)) {
            $this->mCtrlFml->UpdateMembersAdmin($ml_admin_mail);
        }

        //送信者名を取得
        $from_name = $this->mConfig->mAdminFromName;
        if (!$from_name || $from_name == ''){
            $from_name = $this->mConfig->mAdminAppName;
        }

        //メールの送信
        CCommon::SendMail(
                $from_name,
                MAILADDRESS,
                $this->mMailData->mMLMainAddress,
                $mail_subject,
                $send_contents,
                $this->mCtrlFml->CreateMailKey()
        );

        // メルマガ送信時は配信ナンバーインクリメント
        if ( $_SESSION['ML_MODE'] == 'mailmagazine' ) {
            $this->mConfig->mAdminAutoNumber++;
            $this->mConfig->Update();
        }

        header( 'Location: ./?page=MailExit' );
        exit;
    }

    //==============================
    // メールの送信完了ページ
    //==============================
    function GetHtml_AdminMailExit() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/mail_exit.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_MAIL, $header );

        //メール情報の置換
        $contents = str_replace( '{$ml_maildata}', $file_data, $contents );

        $html     = $header . $contents . $footer;
        echo $html;
    }


    //==============================
    // 記事一覧(日付)の表示ページ
    //==============================
    function GetHtml_AdminArticle() {
        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------
        $file_data = '';
        $mail_path = CCommon::AdjustNullValue( $_GET['mail'] );
        if( $mail_path === '' ){         $mail_path = 'index.html';     }

        $dispId = '';
        $layout = '';

        //メールデータの読込
        if( $mail_path ){
            $file_data = $this->mCtrlFml->GetNewsData( $mail_path );
            if( $file_data ){
                $file_data = $this->MHonArc->ConvertHtml( $file_data, true );
            }
        }

        //ソート順を取得してレイアウトを指定
        $tmp_data = CCommon::ParseMailHtml($file_data);
        if ($tmp_data[1] == 'date_index'){
            //日付表示のスタイル
            $lyaout = 'chrono_mail_list';
        }else{
            //スレッド表示のスタイル
            $lyaout = 'thread_mail_list';
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/article.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_ARTICLE, $header );

        //メール情報の置換
        $contents = str_replace( '{$ml_maildata}', $tmp_data[0], $contents );

        //表示レイアウトの設定
        $contents = str_replace( '{view_style}', $lyaout, $contents );

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // 記事内容の表示ページ
    //==============================
    function GetHtml_AdminArticleContents() {
        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------
        $file_data = '';
        $mail_path = CCommon::AdjustNullValue( $_GET['mail'] );
        if( $mail_path === '' ){         $mail_path = 'index.html';     }

        //メールデータの読込
        if( $mail_path ){
            $file_data = $this->mCtrlFml->GetNewsData( $mail_path );
            if( $file_data ){
                $file_data = $this->MHonArc->ConvertHtml( $file_data );
            }
        }

        //ソート順を取得してレイアウトを指定
        $tmp_data = CCommon::ParseMailHtml($file_data);
        if ($tmp_data[1] == 'date_index'){
            //日付表示のスタイル
            $lyaout = 'chrono_mail_list';
        }else{
            //スレッド表示のスタイル
            $lyaout = 'thread_mail_list';
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/article_contents.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_ARTICLE, $header );

        //メール情報の置換
        $contents = str_replace( '{$ml_maildata}', $tmp_data[0], $contents );

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // 記事内容の検索結果ページ
    //==============================
    function GetHtml_AdminArticleSearch() {
        //----------------------------------------
        // データの検索処理
        //----------------------------------------
        $search_data = '';
        $search_word = CCommon::AdjustNullValue( $_POST['search_word'] );
        if( ! $search_word ){
            header( "Location: ./?page=Article" );
        }

        //検索データの取得
        $search_data = $this->MHonArc->SearchHtml( $search_word );
        if( ! $search_data ){
            $search_data = '該当メールはありません。';
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/article_search.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_ARTICLE, $header );

        //メール情報の置換
        $contents = str_replace( '{$search_word}', CCommon::EscHtml( $search_word ), $contents );
        $contents = str_replace( '{$ml_maildata}', $search_data, $contents );


        $html     = $header . $contents . $footer;
        echo $html;
    }


    //==============================
    // エラーメール解析画面表示
    //==============================
    function GetHtml_AdminErrMailList() {
        //エラーメール解析クラスのインスタンス化
        $ctrlErrMail = new CCtrlErrMail($this->mCtrlFml->GetMLPath());

        // メンバー削除
        if ( isset($_POST['sb_Address_delete']) ) {
            $delete_mails   = CCommon::AdjustNullValue($_POST['delete_mail'], array());
            $this->MembersDeleteForAddr($delete_mails);
            $ctrlErrMail->UnsetSummaryData($delete_mails);
        }

        // メンバー再登録
        if ( isset($_POST['sb_Address_reg']) ) {
            $reg_mails   = CCommon::AdjustNullValue($_POST['reg_mail'], array());
            $list = $ctrlErrMail->GetDeleteList();

            $regList = array();
            foreach($reg_mails as $value) {
                foreach($list as $address => $data) {
                    if ($value === $address) {
                        $regList[$address] = $data;
                    }
                }
            }

            $result = $this->MembersRegForAddr($regList);
            $ctrlErrMail->UnsetDeleteData($result);
        }

        //エラー集計結果の取得
        $list = $ctrlErrMail->GetSummaryList();

        // エラーメール集計・自動削除ユーザーページング
        $tab            = CCommon::AdjustNullValue($_GET['tab'], 1);
        $auto_del_page  = CCommon::AdjustNullValue($_GET['auto_del_page'], 1);
        $err_list_page  = CCommon::AdjustNullValue($_GET['err_list_page'], 1);
        $auto_del_page_link  = '';
        $err_list_page_link  = '';

        // エラーメッセージ
        $error_txt  = '';

        //エラーアドレス用のタグを生成
        $count  = 0;
        $offset = ($err_list_page-1) * CCtrlML::eMAIL_PAGEVIEW_MAX;
        $limit  = $err_list_page * CCtrlML::eMAIL_PAGEVIEW_MAX;
        if ( count($list) > 0 ) {
            foreach($list as $address => $data) {
                $count++;
                if ( $count <= $offset ) {
                    continue;
                }
                else if ( $count > $limit ) {
                    break;
                }
                $sum    = $data['permanent'] + $data['temporary'] + $data['unknown'];
                $err_address .= '<tr>';
                $err_address .= '<td><input class="delete_mail" type="checkbox" name="delete_mail[]" value="' . $address . '" /></td>';
                $err_address .= '<td class="txt_left">' . $address . '</td>';
                $err_address .= '<td>' . $sum . '</td>';
                $err_address .= '<td>' . $data['date'] . '</td>';
                $err_address .= '<td>[<a href="./?page=ErrMailDetail&addr=' . $address . '">詳細</a>]</td>';
                $err_address .= '</tr>';
            }
        }
        else {
            $err_address    = '<tr><td colspan="7">該当データはありません。</td></tr>';
        }
        $err_list_page_link  = $this->GetPaging($list, './', $err_list_page, 'err_list_page', array('page' => 'ErrMailList', 'tab' => 3));

        //----------------------------------------
        // 自動削除履歴の削除
        //----------------------------------------
        if (CCommon::AdjustNullValue($_POST['list_clear'], 0) == 1) {
            $ctrlErrMail->ClearDeleteList();
        }

        //----------------------------------------
        // 自動削除ユーザーの取得
        //----------------------------------------
        $list = $ctrlErrMail->GetDeleteList();

        $count  = 0;
        $offset = ($auto_del_page-1) * CCtrlML::eMAIL_PAGEVIEW_MAX;
        $limit  = $auto_del_page * CCtrlML::eMAIL_PAGEVIEW_MAX;
        if ( count($list) > 0 ) {
            foreach($list as $address => $data) {
                $count++;
                if ( $count <= $offset ) {
                    continue;
                }
                else if ( $count > $limit ) {
                    break;
                }
                $sum    = $data['permanent'] + $data['temporary'] + $data['unknown'];
                $delete_address .= '<tr>';
                $delete_address .= '<td><input class="reg_mail" type="checkbox" name="reg_mail[]" value="' . $address . '" /></td>';
                $delete_address .= '<td class="txt_left">' . $address . '</td>';
                $delete_address .= '<td>' . $sum . '</td>';
                $delete_address .= '<td>' . $data['date'] . '</td>';
                $delete_address .= '</tr>';
            }
        }
        else {
            $delete_address = '<tr><td colspan="6">該当データはありません。</td></tr>';
        }
        $auto_del_page_link  = $this->GetPaging($list, './', $auto_del_page, 'auto_del_page', array('page' => 'ErrMailList', 'tab' => 2));

        //----------------------------------------
        // 自動削除設定値の保存
        //----------------------------------------
        if ( isset($_POST['sb_set_auto_delete']) ) {
            $this->mConfig->mErrMailAutoDelete    = isset($_POST['set_auto_delete_flg']) ? 1 : 0;
            $this->mConfig->mErrMailAutoDelNotify = isset($_POST['set_auto_delete_notify']) ? 1 : 0;
            $this->mConfig->mErrMailNum           = CCommon::AdjustNullValue($_POST['set_err_mail_num'], CCtrlErrMail::ERR_DEFAULT_NUM);

            // メール通知設定確認
            if ($this->mConfig->mErrMailAutoDelNotify) {
                if ( count($this->mCtrlFml->GetAdminMailHook()) <= 0 ) {
                    $error_txt   .= '<li>メール通知を設定するには、環境設定[システム・エラーメール受信設定]を[受信する]に設定してください</li>';
                }
            }
            // エラーメール回数設定値確認
            if ( !preg_match("/^[0-9]{1,2}$/", $this->mConfig->mErrMailNum) ||
                    ($this->mConfig->mErrMailNum < CCtrlErrMail::ERR_MIN_NUM) ||
                        ($this->mConfig->mErrMailNum > CCtrlErrMail::ERR_MAX_NUM ) ) {
                $error_txt  .= '<li>自動削除を実行するエラー回数の設定値が正しくありません</li>';
            }

            if ( strlen($error_txt) <= 0 ) {
                $this->mConfig->Update();
            }
        }

        //----------------------------------------
        // 自動削除設定値の取得
        //----------------------------------------
        $set_auto_delete_flg   = $this->mConfig->mErrMailAutoDelete ? "checked" : "";
        $set_auto_delete_notify= $this->mConfig->mErrMailAutoDelNotify ? "checked" : "";
        $set_err_mail_num      = $this->mConfig->mErrMailNum;

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/err_mail_list.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //---------------------
        //ヘッダー情報の置換
        //---------------------
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_ERRMAIL, $header );

        //---------------------
        //コンテンツ内容の置換
        //---------------------
        //ユーザー情報の置換
        $contents = str_replace( '{$set_auto_delete_flg}', $set_auto_delete_flg, $contents );
        $contents = str_replace( '{$err_address}', $err_address, $contents );
        $contents = str_replace( '{$err_Mail}', $mail_tag, $contents );
        $contents = str_replace( '{$delete_address}', $delete_address, $contents );
        $contents = str_replace( '{$auto_del_page_link}', $auto_del_page_link, $contents );
        $contents = str_replace( '{$err_list_page_link}', $err_list_page_link, $contents );
        $contents = str_replace( '{$set_auto_delete_notify}', $set_auto_delete_notify, $contents );
        $contents = str_replace( '{$set_err_mail_num}', $set_err_mail_num, $contents );
        $contents = str_replace( '{$set_err_span}', CCtrlErrMail::ERR_SPAN, $contents );
        $contents = str_replace( '{$set_err_min}', CCtrlErrMail::ERR_MIN_NUM, $contents );
        $contents = str_replace( '{$set_err_max}', CCtrlErrMail::ERR_MAX_NUM, $contents );

        $contents = str_replace( '{$error_txt}',     "<ul>$error_txt</ul>", $contents );

        //タブ設定
        $nowpage = ($_SERVER['REQUEST_METHOD'] == 'POST') ? CCommon::AdjustNullValue( $_POST['now_systemmail'], '1'): $tab;
        for( $i=1;$i<=3;$i++ ){
            if( $i == $nowpage ){
                $contents = str_replace( '{$current_file_0' . $i . '}', 'current_tag_file', $contents );
                $contents = str_replace( '{$display_file_0' . $i . '}', 'display:block',    $contents );
            }else{
                $contents = str_replace( '{$current_file_0' . $i . '}', '',                 $contents );
                $contents = str_replace( '{$display_file_0' . $i . '}', 'display:none',     $contents );
            }
        }

        //---------------------
        //HTML連結
        //---------------------
        $html     = $header . $contents . $footer;
        echo $html;
    }


    //==============================
    // オプションの設定ページ
    //==============================
    function GetHtml_AdminErrMailDetail() {
        $addr = CCommon::AdjustNullValue($_GET['addr'], '');

        $ctrlErrMail = new CCtrlErrMail($this->mCtrlFml->GetMLPath());
        $list = $ctrlErrMail->GetErrorMailList($addr);

        // ページング用
        $page       = CCommon::AdjustNullValue($_GET['p'], 1);
        $offset     = ($page-1) * CCtrlML::eMAIL_PAGEVIEW_MAX;
        $limit      = $page * CCtrlML::eMAIL_PAGEVIEW_MAX;
        $page_link  = '';

        for($i = $offset; $i <= $limit; $i++) {
            if ( !isset($list[$i]) ) {
                break;
            }
            $tag .= '<tr>';
            $tag .= '<td>' . $list[$i]['date'] . '</td>';
            $tag .= '</tr>';
        }
        $page_link  = $this->GetPaging($list, './', $page, 'p', array('page' => 'ErrMailDetail', 'addr' => $addr));

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/err_mail_detail.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //---------------------
        //ヘッダー情報の置換
        //---------------------
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_ERRMAIL, $header );

        //---------------------
        //コンテンツ内容の置換
        //---------------------
        //ユーザー情報の置換
        $contents = str_replace( '{$address}', $addr, $contents );
        $contents = str_replace( '{$err_list}', $tag, $contents );
        $contents = str_replace( '{$page_link}', $page_link, $contents );

        //---------------------
        //HTML連結
        //---------------------
        $html     = $header . $contents . $footer;
        echo $html;
    }


    //==============================
    // オプションの設定ページ
    //==============================
    function GetHtml_AdminOption() {
        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------
        $ml_name              = $this->mCtrlFml->GetTitleName();
        $ml_subject_mode      = $this->mCtrlFml->GetTitleForm();
        $ml_limit_send        = $this->mCtrlFml->GetSendLimit();
        $ml_limit_size        = $this->mCtrlFml->GetMailSizeLimit();
        $ml_climit_member     = $this->mCtrlFml->GetDenyCommandMail_Member();
        $ml_climit_news       = $this->mCtrlFml->GetDenyCommandMail_News();
        $ml_climit_status     = $this->mCtrlFml->GetDenyCommandMail_Status();
        $ml_member_auth       = $this->mConfig->mMlMemberAuth;
        $mm_reply_to          = $this->mCtrlFml->GetMmReplyAddress();
        $ml_moderators        = ($_SESSION['ML_MODE'] == 'mailinglist') ? $this->mCtrlFml->GetAdminModerators() : array_shift($this->mCtrlFml->GetAdminMailHook());
        $ml_transmit_val      = $this->mCtrlFml->CheckAdminMailHook($ml_moderators);
        $mm_from_name         = $this->mConfig->mAdminFromName;
        $mm_title_name        = $this->mConfig->mAdminTitleName;
        $ml_app_name          = $this->mConfig->mAdminAppName;
        $ml_sender_check      = $this->mCtrlFml->GetMLSender();
        $ml_sender_name       = $this->mCtrlFml->GetSenderName();
        $auto_number          = $this->mConfig->mAdminAutoNumber;
        list($ml_content_type, $ml_temp_file)      = $this->mCtrlFml->GetContentType();
        //list($mm_moderators)  = $this->mCtrlFml->GetAliasAdminMail();
        $mm_moderators = $this->mCtrlFml->GetAdminModerators();

        //ヘッダーフッターの読込
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            $ml_add_header   = $this->mCtrlFml->GetTextFileMailHeader();
            $ml_add_footer   = $this->mCtrlFml->GetTextFileMailFooter();
        }else{
            $ml_add_header   = $this->mCtrlFml->GetTextFileMgHeader();
            $ml_add_footer   = $this->mCtrlFml->GetTextFileMgFooter();
        }
        //  保存時に付与している改行を削除して表示
        $ml_add_header   = mb_substr($ml_add_header, 0, mb_strlen($ml_add_header)-1);
        $ml_add_footer   = mb_substr($ml_add_footer, 0, mb_strlen($ml_add_footer)-1);

        if( isset( $_POST['sb_setting_save'] ) ){
            $ml_name              = CCommon::AdjustNullValue( $_POST['ml_subject_name'] );
            $ml_subject_mode      = CCommon::AdjustNullValue( $_POST['ml_subject_mode'] );
            $ml_limit_send        = CCommon::AdjustNullValue( $_POST['ml_limit_send'] );
            $ml_moderators        = CCommon::AdjustNullValue( $_POST['ml_moderators'] );
            $ml_reply_mode        = CCommon::AdjustNullValue( $_POST['ml_reply_mode'] );
            $ml_limit_size        = CCommon::AdjustNullValue( $_POST['ml_limit_size'] );
            $ml_climit_member     = CCommon::AdjustNullValue( $_POST['ml_limit_menber'] );
            $ml_climit_news       = CCommon::AdjustNullValue( $_POST['ml_limit_article'] );
            $ml_climit_status     = CCommon::AdjustNullValue( $_POST['ml_limit_option'] );
            $ml_add_header        = CCommon::AdjustNullValue( $_POST['ml_add_header'] );
            $ml_add_footer        = CCommon::AdjustNullValue( $_POST['ml_add_footer'] );
            $ml_member_auth       = CCommon::AdjustNullValue( $_POST['ml_member_auth'] );
            $mm_reply_to          = CCommon::AdjustNullValue( $_POST['mm_reply_to'] );
            $ml_temp_file         = CCommon::AdjustNullValue( $_POST['ml_temp_file'] );
            $ml_transmit_val      = CCommon::AdjustNullValue( $_POST['ml_transmit_val'] );
            $mm_from_name         = CCommon::AdjustNullValue( $_POST['mm_from_name'] );
            $mm_title_name        = CCommon::AdjustNullValue( $_POST['mm_title_name']);
            $ml_app_name          = CCommon::AdjustNullValue( $_POST['ml_app_name']);
            $ml_sender_check      = CCommon::AdjustNullValue( $_POST['ml_sender_check']);
            $ml_sender_name       = CCommon::AdjustNullValue( $_POST['ml_sender_name']);
            $auto_number          = CCommon::AdjustNullValue( $_POST['auto_number']);
            $ml_content_type      = CCommon::AdjustNullValue( $_POST['ml_content_type']);
            $mm_moderators        = CCommon::AdjustNullValue( $_POST['mm_moderators']);
        }

        //メールの返信先設定
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            if ($mm_reply_to == '$MAIL_LIST'){
                $ml_reply_mode = 1;
            }else{
                $ml_reply_mode = 2;
            }
        }else{
            if ($mm_reply_to =='$MAIL_LIST' || $mm_reply_to == '$From_address'){
                $mm_reply_to = '';
            }else{
                $mm_reply_to = str_replace("'", "", $mm_reply_to);
            }
        }

        //エラーテキスト
        $error_txt = '';
        if( mb_strlen( $ml_name, 'UTF-8' ) > 16 ) {
            $error_txt .= '<li>[件名に付加する名前]は、１６文字以内にしてください</li>';
        }
        if( $error_txt != '' ){
            $error_txt    = '<ul>' . $error_txt . '</ul>';
        }
        if( $this->errMsg != '' ){
            $error_txt    .= '<ul>' . $this->errMsg . '</ul>';
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/option.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );
        $url           = 'http://' . $_SERVER["SERVER_NAME"]. '/xmailinglist/' . ML_NAME . '/';

        if ( 55 < mb_strlen($url,'UTF-8')){
            $url = substr($url,0,55) . '...';
        }


        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $ml_app_name , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_OPTION, $header );

        //コンテンツ情報の置換
        $ml_admin_mail = CCommon::EscHtml( $_SESSION['MAILINGLIST_ID'] );
        $contents = str_replace( '{$ml_mailaddress}',ML_NAME . '@' . DOMAIN_NAME ,$contents);
        $contents = str_replace( '{$ml_admin_mail}',$ml_admin_mail,$contents);
        $contents = str_replace( '{$withdraw_url}', $url, $contents );
        $contents = str_replace( '{$error_txt}',     $error_txt, $contents );
        $contents = str_replace( '{$ml_name}',       CCommon::EscHtml( $ml_name ), $contents );
        $contents = str_replace( '{$ml_moderators}', CCommon::EscHtml( $ml_moderators ), $contents );
        $contents = str_replace( '{$mm_reply_to}', CCommon::EscHtml($mm_reply_to), $contents );
        $contents = str_replace( '{$mm_from_name}', CCommon::EscHtml($mm_from_name), $contents );
        $contents = str_replace( '{$mm_title_name}', CCommon::EscHtml($mm_title_name), $contents );
        $contents = str_replace( '{$ml_app_name}', CCommon::EscHtml($ml_app_name), $contents );
        $contents = str_replace( '{$ml_sender_name}',CCommon::EscHtml( $ml_sender_name ), $contents );
        $contents = str_replace( '{$auto_number}', CCommon::EscHtml($auto_number), $contents );
        $contents = str_replace( '{$mm_moderators}', CCommon::EscHtml($mm_moderators), $contents );

        $contents = str_replace( '{$year}', CCommon::EscHtml(date('Y')), $contents );
        $contents = str_replace( '{$month}', CCommon::EscHtml(date('n')), $contents );
        $contents = str_replace( '{$day}', CCommon::EscHtml(date('j')), $contents );

        for( $i=0;$i<=8;$i++ ){
            if( $i==$ml_subject_mode ){    $contents = str_replace( '{$ml_subject_mode_' . $i . '}', 'selected', $contents );
            }else{                         $contents = str_replace( '{$ml_subject_mode_' . $i . '}', '',         $contents );
            }
        }

        for( $i=1;$i<=2;$i++ ){
            if( $i==$ml_limit_send ){    $contents = str_replace( '{$ml_limit_send_' . $i . '}', 'selected', $contents );
            }else{                       $contents = str_replace( '{$ml_limit_send_' . $i . '}', '',         $contents );
            }
        }

        for( $i=1;$i<=2;$i++ ){
            if( $i==$ml_reply_mode ){    $contents = str_replace( '{$ml_reply_mode_' . $i . '}', 'selected', $contents );
            }else{                       $contents = str_replace( '{$ml_reply_mode_' . $i . '}', '',         $contents );
            }
        }

        for( $i=1;$i<=CCtrlML::ML_AUTH_NUM;$i++ ){
            if( $i==$ml_member_auth ){    $contents = str_replace( '{$ml_member_auth_' . $i . '}', 'selected', $contents );
            }else{                        $contents = str_replace( '{$ml_member_auth_' . $i . '}', '',         $contents );
            }
        }

        for( $i=0;$i<=6;$i++ ){
            if( $i==$ml_limit_size ){    $contents = str_replace( '{$ml_limit_size_' . $i . '}', 'selected', $contents );
            }else{                         $contents = str_replace( '{$ml_limit_size_' . $i . '}', '',         $contents );
            }
        }

        for( $i=1;$i<=2;$i++ ){
            if( $i==$ml_temp_file ){    $contents = str_replace( '{$ml_temp_file_' . $i . '}', 'selected', $contents );
            }else{                      $contents = str_replace( '{$ml_temp_file_' . $i . '}', '',         $contents );
            }
        }
        for ($i=1; $i <=2; $i++) {
            if ( $i == $ml_content_type ) { $contents = str_replace( '{$ml_content_type_' . $i . '}', 'selected', $contents );
            } else {                     $contents = str_replace( '{$ml_content_type_' . $i . '}', '', $contents );
            }
        }

        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            //システム・エラーメール受信設定
            for( $i=1;$i<=2;$i++ ){
                if( $i==$ml_transmit_val ){    $contents = str_replace( '{$ml_transmit_val_' . $i . '}', 'selected' , $contents );
                }else{                      $contents = str_replace( '{$ml_transmit_val_' . $i . '}', '',         $contents );
                }
            }
        }else{
            if( $ml_transmit_val == '2' || $ml_transmit_val === "on" ){
                $contents = str_replace( '{$ml_transmit_checked}', 'checked', $contents );
            }else{
                $contents = str_replace( '{$ml_transmit_checked}', '', $contents );
            }
            if ( $ml_limit_send == '2' || $ml_limit_send === "on" ) {
                $contents = str_replace( '{$ml_limit_send}', 'checked', $contents );
            }else{
                $contents = str_replace( '{$ml_limit_send}', '', $contents );
            }
        }

        if( $ml_sender_check == '1'){
            $contents = str_replace( '{$ml_sender_checked}', 'checked', $contents );
        }else{
            $contents = str_replace( '{$ml_sender_checked}', '', $contents );
        }

        //$contents = str_replace( '{$ml_limit_size}', CCommon::EscHtml( $ml_limit_size ), $contents );
        $contents = str_replace( '{$ml_add_header}', CCommon::EscHtml( $ml_add_header ), $contents );
        $contents = str_replace( '{$ml_add_footer}', CCommon::EscHtml( $ml_add_footer ), $contents );

        //コマンドメール制限
        if( $ml_climit_member ){            $contents = str_replace( '{$ml_chk_limit_member}',  'checked', $contents );
        }else{                              $contents = str_replace( '{$ml_chk_limit_member}',  '',        $contents );
        }
        if( $ml_climit_news   ){            $contents = str_replace( '{$ml_chk_limit_article}', 'checked', $contents );
        }else{                              $contents = str_replace( '{$ml_chk_limit_article}', '',        $contents );
        }
        if( $ml_climit_status ){            $contents = str_replace( '{$ml_chk_limit_option}',  'checked', $contents );
        }else{                              $contents = str_replace( '{$ml_chk_limit_option}',  '',        $contents );
        }

        //HTML連結
        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // オプションの保存ページ
    //==============================
    function GetHtml_AdminOptionDo() {
        $flag      = true;

        //システムデータ
        $ml_name         = CCommon::AdjustNullValue( $_POST['ml_subject_name'], $this->mCtrlFml->GetTitleName() );
        $ml_subject_mode = CCommon::AdjustNullValue( $_POST['ml_subject_mode'], $this->mCtrlFml->GetTitleForm() );
        $ml_limit_send   = CCommon::AdjustNullValue( $_POST['ml_limit_send'], $this->mCtrlFml->GetSendLimit() );
        $ml_moderators   = CCommon::AdjustNullValue( $_POST['ml_moderators'] );
        $ml_reply_mode   = CCommon::AdjustNullValue( $_POST['ml_reply_mode'], $this->mCtrlFml->GetMailReply() );
        $ml_limit_size   = CCommon::AdjustNullValue( $_POST['ml_limit_size'], $this->mCtrlFml->GetMailSizeLimit() );
        $ml_climit_member= CCommon::AdjustNullValue( $_POST['ml_limit_menber'], $this->mCtrlFml->GetDenyCommandMail_Member() );
        $ml_climit_news  = CCommon::AdjustNullValue( $_POST['ml_limit_article'], $this->mCtrlFml->GetDenyCommandMail_News() );
        $ml_climit_status= CCommon::AdjustNullValue( $_POST['ml_limit_option'], $this->mCtrlFml->GetDenyCommandMail_Status() );
        $ml_member_auth  = CCommon::AdjustNullValue( $_POST['ml_member_auth'], $this->mConfig->mMlMemberAuth );
        $mm_reply_to     = CCommon::AdjustNullValue( $_POST['mm_reply_to'], $this->mCtrlFml->GetMmReplyAddress());
        $ml_temp_file    = CCommon::AdjustNullValue( $_POST['ml_temp_file'], 2);
        $ml_transmit_val = CCommon::AdjustNullValue( $_POST['ml_transmit_val'],1);
        $mm_from_name    = CCommon::AdjustNullValue( $_POST['mm_from_name'], $this->mConfig->mAdminFromName);
        $mm_title_name   = trim(CCommon::AdjustNullValue( $_POST['mm_title_name'], $this->mConfig->mAdminTitleName));
        $ml_app_name     = CCommon::AdjustNullValue( $_POST['ml_app_name'], $this->mConfig->mAdminAppName);
        $auto_number     = CCommon::AdjustNullValue( $_POST['auto_number'], $this->mConfig->mAdminAutoNumber);
        $ml_content_type = CCommon::AdjustNullValue( $_POST['ml_content_type'], 1);
        $ml_sender_check = CCommon::AdjustNullValue( $_POST['ml_sender_check'], $this->mCtrlFml->GetMLSender());
        $ml_sender_name  = CCommon::AdjustNullValue( $_POST['ml_sender_name'], $this->mCtrlFml->GetSenderName());
        $mm_moderators   = CCommon::AdjustNullValue( $_POST['mm_moderators'] );

        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            $ml_add_header   = CCommon::AdjustNullValue( $_POST['ml_add_header'], $this->mCtrlFml->GetTextFileMailHeader() );
            $ml_add_footer   = CCommon::AdjustNullValue( $_POST['ml_add_footer'], $this->mCtrlFml->GetTextFileMailFooter() );
        }else{
            $ml_add_header   = CCommon::AdjustNullValue( $_POST['ml_add_header'], $this->mCtrlFml->GetTextFileMgHeader() );
            $ml_add_footer   = CCommon::AdjustNullValue( $_POST['ml_add_footer'], $this->mCtrlFml->GetTextFileMgFooter() );
        }

        //除外判定
        if( ! isset( $_POST['sb_setting_save'] ) ){$flag = false; }


        //メーリングリストの場合のみ
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){

            $catch_name ="管理者";

            // メーリングリスト名
            if (mb_strlen( $ml_app_name, 'UTF-8' ) == 0){
                $flag = false;
                $this->errMsg .= '<li>[メーリングリスト名]を入力して下さい</li>';
            }

            // メーリングリスト名
            if (mb_strlen( $ml_app_name, 'UTF-8' ) > 30){
                $flag = false;
                $this->errMsg .= '<li>[メーリングリスト名]は30文字以下を入力して下さい</li>';
            }

            //差出人名
            if (mb_strlen( $ml_sender_name, 'UTF-8' ) > 20){
                $flag = false;
                $this->errMsg .= '<li>[差出人名]は20文字以下を入力して下さい</li>';
            }

            // メールタイトル
            // 書式に「タイトル」が含まれたときのみチェック
            if ( (1 <= $ml_subject_mode && $ml_subject_mode <= 4) || $ml_subject_mode == 7 || $ml_subject_mode == 8) {
                if ( strlen($ml_name) <= 0 || preg_match("/^[\s　]+$/", $ml_name) ) {
                    $flag = false;
                    $this->errMsg .= '<li>書式に[タイトル]を含む場合は、[タイトル]を入力してください</li>';
                }
                else if ( mb_strlen($ml_name, 'UTF-8') > 16 ) {
                    $flag = false;
                    $this->errMsg .= '<li>[タイトル]は16文字以下を入力して下さい</li>';
                }
            }

            //配信前の確認チェックするの場合
            if ($ml_limit_send == '2'){

                //管理者アドレス必須
                if (mb_strlen($ml_moderators,'UTF-8') < 1){
                    $flag = false;
                    $this->errMsg  .= '<li>メールの配信前確認を[確認する]場合、管理者メールアドレスを入力して下さい</li>';
                }

                //ファイル添付を[許可しない]の同時有効エラー
                if ($ml_temp_file == '1'){
                    $flag = false;
                    $this->errMsg  .= '<li>メールの配信前確認を[確認する]場合、ファイル添付設定を[許可する]にして下さい</li>';
                }
            }

            //システム・エラーメール受信設定
            if ($ml_transmit_val == '2'){
                if (mb_strlen($ml_moderators,'UTF-8') == 0){
                    $flag = false;
                    $this->errMsg .= '<li>システム・エラーメール受信設定を[受信する]に設定する場合、管理者メールアドレスを入力して下さい</li>';
                }
            }

        }else{
            $catch_name ="受信用";

             // メールマガジン名
            if (mb_strlen( $ml_app_name, 'UTF-8' ) == 0){
                $flag = false;
                $this->errMsg .= '<li>[メールマガジン名]を入力して下さい</li>';
            }

            // メールマガジン名
            if (mb_strlen( $ml_app_name, 'UTF-8' ) > 30){
                $flag = false;
                $this->errMsg .= '<li>[メールマガジン名]は30文字以下を入力して下さい</li>';
            }

            //送信者名
            if ($mm_from_name != ''){
                if (mb_strlen( $mm_from_name, 'UTF-8' ) > 20){
                    $flag = false;
                    $this->errMsg .= '<li>[送信者名]は20文字以下を入力して下さい</li>';
                }
            }

            //件名
            if ($mm_title_name != ''){
                if (mb_strlen( $mm_title_name, 'UTF-8' ) > 60){
                    $flag = false;
                    $this->errMsg .= '<li>[件名のテンプレート]は、60文字以下を入力して下さい</li>';
                }
            }

            //返信先アドレス
            if ($mm_reply_to != ''){
                if ($mm_reply_to != '$MAIL_LIST' && $mm_reply_to != '$From_address'){
                    //妥当性チェック
                    if( ! CCommon::IsMailAddress( $mm_reply_to ) || $this->IsNgMailAddress( $mm_reply_to ) ){
                        $flag = false;
                        $this->errMsg  .= '<li>返信先アドレスがメールアドレスとして正しくありません</li>';
                    }
                }

                //返信先アドレスがメンバー登録されていないか確認
                if( $this->mCtrlFml->CheckMemberUser( $mm_reply_to ) ||  $this->mCtrlFml->CheckActiveUser($mm_reply_to) ){
                    $flag = false;
                    $this->errMsg .= '<li>返信先アドレスはメンバー未登録のアドレスを設定して下さい</li>';
                }
            }

            //HTMLメール配信
            if ($ml_limit_send === "on"){
                if (mb_strlen($mm_moderators,'UTF-8') < 1){
                    $flag = false;
                    $this->errMsg  .= '<li>HTMLメール配信設定を[配信する]に設定する場合、外部メールアドレスを入力して下さい</li>';
                }
                $ml_limit_send  = CCtrlFml::ePERMIT_MODERATOR;
            }
            else{
                $ml_limit_send  = CCtrlFml::ePERMIT_MENBERS_ONLY;
            }

            //システム・エラーメール受信設定
            if ($ml_transmit_val == 'on'){
                if (mb_strlen($ml_moderators,'UTF-8') == 0){
                    $flag = false;
                    $this->errMsg .= '<li>システム・エラーメール受信設定を[受信する]に設定する場合、受信用メールアドレスを入力して下さい</li>';
                }
            }

            // 配信ナンバー設定
            if ( ! preg_match("/^\d+$/", $auto_number) ) {
                $flag   = false;
                $this->errMsg   .= '<li>配信ナンバーには0以上の数字(整数)を入力してください</li>';
            }
            else if ( $auto_number > PHP_INT_MAX ) {
                $flag   = false;
                $this->errMsg   .= '<li>配信ナンバーに設定できる値を超えています</li>';
            }

            if ($mm_moderators != ''){
                // 外部メールアドレスの妥当性チェック
                if( ! CCommon::IsMailAddress( $mm_moderators ) || $this->IsNgMailAddress( $mm_moderators ) ){
                    $flag = false;
                    $this->errMsg  .= '<li>' . '外部メールアドレスがメールアドレスとして正しくありません</li>';
                }

                // 外部メールアドレスがメンバーとして登録されていないか確認
                if( $this->mCtrlFml->CheckMemberUser( $mm_moderators ) ||  $this->mCtrlFml->CheckActiveUser($mm_moderators) ){
                    $flag = false;
                    $this->errMsg .= '<li>' . '外部メールアドレスはメンバー未登録のアドレスを設定して下さい</li>';
                }

                // 外部メールアドレスにメーリングリストアドレスの登録不可
                if( $mm_moderators == ML_NAME . '@' . DOMAIN_NAME ){
                    $flag = false;
                    $this->errMsg .= '<li>' . '外部メールアドレスにメーリングリストアドレスを登録できません</li>';
                }
            }
        }

        if ($ml_moderators != ''){
            //管理者アドレスの妥当性チェック
            if( ! CCommon::IsMailAddress( $ml_moderators ) || $this->IsNgMailAddress( $ml_moderators ) ){
                $flag = false;
                $this->errMsg  .= '<li>' . $catch_name .'メールアドレスがメールアドレスとして正しくありません</li>';
            }

            //管理者アドレスがメンバーとして登録されていないか確認
            if ( !$this->mCtrlFml->CheckModerators( $ml_moderators ) ) {
                if( $this->mCtrlFml->CheckMemberUser( $ml_moderators ) ||  $this->mCtrlFml->CheckActiveUser($ml_moderators) || $this->mCtrlFml->CheckMemberAdmin($ml_moderators) ){
                    $flag = false;
                    $this->errMsg .= '<li>' . $catch_name. 'メールアドレスはメンバー未登録のアドレスを設定して下さい</li>';
                }
            }

            //管理者アドレスにメーリングリストアドレスの登録不可
            if( $ml_moderators == ML_NAME . '@' . DOMAIN_NAME ){
                $flag = false;
                $this->errMsg .= '<li>' . $catch_name . 'メールアドレスにメーリングリストアドレスを登録できません</li>';
            }
        }

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->GetHtml_AdminOption();
            exit;
        }

        //fml設定ファイルの更新
        $this->mCtrlFml->SetTitleName( $ml_name );
        $this->mCtrlFml->SetTitleForm( $ml_subject_mode );
        $this->mCtrlFml->SetSendLimit( $ml_limit_send );
        $this->mCtrlFml->SetMailSizeLimit( $ml_limit_size );
        $this->mCtrlFml->SetDenyCommandMail( $ml_climit_member, $ml_climit_news, $ml_climit_status );

        //ファイルの添付可否
        //$this->mCtrlFml->SetFileTemp($ml_temp_file);
        // HTMLメール・ファイル添付設定
        $this->mCtrlFml->SetContentType($ml_content_type, $ml_temp_file);

        //変更前の管理者アドレス
        $del_moderators = $this->mCtrlFml->GetAdminModerators();

        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            //差出人の設定
            if ($ml_sender_check == 'on'){
                $this->mCtrlFml->SetMLSender( 1 , 0 );
            }else{
                $this->mCtrlFml->SetMLSender( 0 , 0 );
            }
            $this->mCtrlFml->SetSenderName($ml_sender_name);

            //ヘッダーフッターの設定
            $this->mCtrlFml->SetTextFileMailHeader( $ml_add_header . "\n" );
            $this->mCtrlFml->SetTextFileMailFooter( $ml_add_footer . "\n" );

            if ($ml_reply_mode == 1){
                //メールの返信先：メンバー全員へ返信
                $this->mCtrlFml->SetMmReplyAddress('$MAIL_LIST',0);
            }else{
                //メールの返信先：投稿した送信者へ返信
                $this->mCtrlFml->SetMmReplyAddress('$From_address' ,0 );
            }

            //承認権限(管理者)の更新（前管理者を削除して実行）
            $this->mCtrlFml->DeleteCompleteModerators($del_moderators);
            $this->mCtrlFml->DeleteMembersAdmin($del_moderators);
            if ($ml_moderators != ''){
                //「moderators」と「members-admin」ファイルを更新
                $this->mCtrlFml->UpdateModerators($ml_moderators);
                $this->mCtrlFml->UpdateMembersAdmin($ml_moderators);
            }

            //システム・エラーメール受信設定の更新(前登録アドレスを削除して実行）
            $this->mCtrlFml->DeleteAdminMailHook($del_moderators);
            if ( $ml_transmit_val == '2' ){
                $this->mCtrlFml->SetAdminMailHook($ml_moderators);
            }
        }else{
            //ヘッダーフッターの設定
            $this->mCtrlFml->SetTextFileMgHeader( $ml_add_header . "\n");
            $this->mCtrlFml->SetTextFileMgFooter( $ml_add_footer . "\n");

            //メールマガジンの返信先設定
            if (isset($mm_reply_to)){
                   $this->mCtrlFml->SetMmReplyAddress($mm_reply_to , 1);
            }else{
                //返信先の設定がない場合は、メールマガジンのアドレスを設定
                   $this->mCtrlFml->SetMmReplyAddress( '$MAIL_LIST', 0);
            }

            //システム・エラーメール受信設定の更新(前登録アドレスを削除して実行）
            $this->mCtrlFml->DeleteAdminMailHook( array_shift($this->mCtrlFml->GetAdminMailHook()) );
            if ( $ml_transmit_val == 'on' ){
                $this->mCtrlFml->SetAdminMailHook($ml_moderators);
            }

            // HTMLメール配信用
            // 「moderators」、「membaers-admin」、「.alias」更新
            $this->mCtrlFml->DeleteCompleteModerators($del_moderators);
            $this->mCtrlFml->DeleteMembersAdmin($del_moderators);
            $this->mCtrlFml->SetAliasAdminMail('');
            if ( $ml_limit_send == '2' ) {
                $this->mCtrlFml->SetContentType(2, 2);  # HTML/File accept
                $this->mCtrlFml->UpdateModerators($mm_moderators);
                $this->mCtrlFml->UpdateMembersAdmin($mm_moderators);
                $this->mCtrlFml->SetAliasAdminMail($mm_moderators);
            }

        }

        //その他欄の更新
        $this->mConfig->mMlMemberAuth     = $ml_member_auth;
        $this->mConfig->mAdminFromName    = $mm_from_name;
        $this->mConfig->mAdminTitleName   = $mm_title_name;
        $this->mConfig->mAdminAppName     = $ml_app_name;
        $this->mConfig->mAdminAutoNumber  = $auto_number;
        $this->mConfig->Update();

        header( 'Location: ./?page=OptionExit' );
        exit;
    }


    //==============================
    // オプションの保存完了ページ
    //==============================
    function GetHtml_AdminOptionExit() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/option_exit.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_OPTION, $header );

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // システムメールの編集ページ
    //==============================
    function GetHtml_AdminEditSystemMail() {
        $nowpage   = 1;
        $error_txt = '';

        //システムメール内容の保管場所
        $admission = '';
        $withdraw  = '';
        $welcome   = '';
        $goodbye   = '';

        $save_flag = false;

        //----------------------------------------
        // データの読み込み処理
        //----------------------------------------
        if( !isset( $_POST['sb_file_save'] ) ){
            $admission_subject = $this->mConfig->mSystemMailAdmissionSubject;
            $withdraw_subject  = $this->mConfig->mSystemMailWithdrawSubject;
            $welcome_subject   = $this->mConfig->mSystemMailWelcomeSubject;
            $goodbye_subject   = $this->mConfig->mSystemMailGoodbyeSubject;
            $admission = $this->mCtrlFml->GetTextFileAdmission();
            $withdraw  = $this->mCtrlFml->GetTextFileWithdraw();
            $welcome   = $this->mCtrlFml->GetTextFileWelcome();
            $goodbye   = $this->mCtrlFml->GetTextFileGoodbye();
        }else{
            $admission_subject = CCommon::AdjustNullValue( $_POST['subject_01'] );
            $withdraw_subject  = CCommon::AdjustNullValue( $_POST['subject_02'] );
            $welcome_subject   = CCommon::AdjustNullValue( $_POST['subject_03'] );
            $goodbye_subject   = CCommon::AdjustNullValue( $_POST['subject_04'] );
            $admission = CCommon::AdjustNullValue( $_POST['input_file_01'] );
            $withdraw  = CCommon::AdjustNullValue( $_POST['input_file_02'] );
            $welcome   = CCommon::AdjustNullValue( $_POST['input_file_03'] );
            $goodbye   = CCommon::AdjustNullValue( $_POST['input_file_04'] );
        }

        //----------------------------------------
        // データの保存処理
        //----------------------------------------
        if( isset( $_POST['sb_file_save'] ) && isset( $_POST['now_systemmail'] ) ){
            //エラー処理
            $check_txt = '';
            switch( $nowpage ){
                case 1:  $check_txt = $admission;  break;
                case 2:  $check_txt = $withdraw;   break;
                case 3:  $check_txt = $welcome;   break;
                case 4:  $check_txt = $goodbye;   break;
            }

            //データの保存処理
            if( isset( $_POST['sb_file_save'] ) &&
                $check_txt != '' && $error_txt == '' ){
                //選択ページの取得
                $nowpage   = CCommon::AdjustNullValue( $_POST['now_systemmail'] );

                switch( $nowpage ){
                    case 1:
                        $this->mConfig->mSystemMailAdmissionSubject = $admission_subject;
                        $this->mCtrlFml->SetTextFileAdmission( $admission );
                        $save_flag = true;
                        break;
                    case 2:
                        $this->mConfig->mSystemMailWithdrawSubject  = $withdraw_subject;
                        $this->mCtrlFml->SetTextFileWithdraw( $withdraw );
                        $save_flag = true;
                        break;
                    case 3:
                        $this->mConfig->mSystemMailWelcomeSubject   = $welcome_subject;
                        $this->mCtrlFml->SetTextFileWelcome( $welcome );
                        $save_flag = true;
                        break;
                    case 4:
                        $this->mConfig->mSystemMailGoodbyeSubject   = $goodbye_subject;
                        $this->mCtrlFml->SetTextFileGoodbye( $goodbye );
                        $save_flag = true;
                        break;
                    default:
                        $nowpage = 1;
                        $save_flag = true;
                        break;
                }

                // 件名の保存実行
                $this->mConfig->Update();
            }
        }

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/edit_system_mail.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_SYSTEMMAIL, $header );

        //システムメール
        $contents = str_replace( '{$systemmail_admission_subject}', CCommon::EscHtml( $admission_subject ), $contents );
        $contents = str_replace( '{$systemmail_withdraw_subject}',  CCommon::EscHtml( $withdraw_subject ),  $contents );
        $contents = str_replace( '{$systemmail_welcome_subject}',   CCommon::EscHtml( $welcome_subject ),   $contents );
        $contents = str_replace( '{$systemmail_goodbye_subject}',   CCommon::EscHtml( $goodbye_subject ),   $contents );
        $contents = str_replace( '{$systemmail_admission}',  CCommon::EscHtml( $admission ), $contents );
        $contents = str_replace( '{$systemmail_withdraw}',   CCommon::EscHtml( $withdraw ),  $contents );
        $contents = str_replace( '{$systemmail_welcome}',   CCommon::EscHtml( $welcome ),  $contents );
        $contents = str_replace( '{$systemmail_goodbye}',   CCommon::EscHtml( $goodbye ),  $contents );

        $contents = str_replace( '{$error_txt}', $error_txt, $contents );
        $contents = str_replace( '{$now_systemmail}', CCommon::EscHtml( $nowpage ), $contents );

        for( $i=1;$i<=4;$i++ ){
            if( $i == $nowpage ){
                $contents = str_replace( '{$current_file_0' . $i . '}', 'current_tag_file', $contents );
                $contents = str_replace( '{$display_file_0' . $i . '}', 'display:block',    $contents );
            }else{
                $contents = str_replace( '{$current_file_0' . $i . '}', '',                 $contents );
                $contents = str_replace( '{$display_file_0' . $i . '}', 'display:none',     $contents );
            }
        }

        if( $save_flag ){

            $page_name = "";
            if( $_SESSION['ML_MODE'] == 'mailinglist' ){
                switch( $nowpage ){
                    case 1:
                        $page_name = '入会確認メールの\n';
                        break;
                    case 2:
                        $page_name = '退会確認メールの\n';
                        break;
                    case 3:
                        $page_name = '入会完了メールの\n';
                        break;
                    case 4:
                        $page_name = '退会完了メールの\n';
                        break;
                }
            }else{
                switch( $nowpage ){
                    case 1:
                        $page_name = '登録確認メールの\n';
                        break;
                    case 2:
                        $page_name = '退会確認メールの\n';
                        break;
                    case 3:
                        $page_name = '登録受付完了メールの\n';
                        break;
                    case 4:
                        $page_name = '退会申し込み完了メールの\n';
                        break;
                }
            }

            $contents .= "\n";
            $contents .= '<script type="text/javascript">' . "\n";
            $contents .= '<!--' . "\n";
            $contents .= 'alert("' . $page_name . '設定を保存しました。");' . "\n";
            $contents .= '-->' . "\n";
            $contents .= '</script>' . "\n";
        }

        $html     = $header . $contents . $footer;

        echo $html;

    }

    //==============================
    // 設置用タグの表示ページ
    //==============================
    function GetHtml_AdminSetHtmlTag() {
        //設定先URLを生成する
        $user_url    = '';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $user_url    = 'https://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
        } else {
            $user_url    = 'http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
        }
        $user_url    = dirname( dirname( $user_url ) );
        $user_url    = $user_url . '/';

        $action_url  = $user_url . 'mail.php';

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/set_html_tag.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_SETHTML, $header );

        //テンプレートの置換
        $contents = str_replace( '{$user_url}',   $user_url,   $contents );
        $contents = str_replace( '{$action_url}', $action_url, $contents );
        $contents = str_replace( '{identity}', AUTO_KEY, $contents );

        //空メール自動入会機能用のメールアカウント情報
        if (ML_MODE == 'mailinglist'){
            $ml_mode = 'メーリングリスト';
        }else{
            $ml_mode = 'メールマガジン';
        }
        if (AUTO_APPLY_FLG == 1){
            $auto_apply_info = ML_NAME . '-apply@' . DOMAIN_NAME ;
        }else{
            $auto_apply_info = '<div style="color:#ff3333">本'. $ml_mode . 'ではご利用できません。</div>';
        }
        $contents = str_replace( '{$auto_apply_mail}', $auto_apply_info, $contents );

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // 公開設定の表示ページ
    //==============================
    function GetHtml_AdminViewUser() {
        $panel_open      = CCommon::AdjustNullValue( $_POST['panel_open'] ,$this->mConfig->mUserPanelOpen );
        $panel_login     = CCommon::AdjustNullValue( $_POST['panel_login'] ,$this->mConfig->mUserPanelLogin );
        $panel_id        = CCommon::AdjustNullValue( $_POST['panel_user_id'] ,$this->mConfig->mUserPanelID );
        $panel_maillog   = CCommon::AdjustNullValue( $_POST['panel_maillog'] ,$this->mConfig->mUserPanelMailLog );
        $panel_apply     = CCommon::AdjustNullValue( $_POST['panel_apply'] ,$this->mConfig->mUserPanelApply );
        $panel_withdraw  = CCommon::AdjustNullValue( $_POST['panel_withdraw'] ,$this->mConfig->mUserPanelWithdraw );
        $panel_replay    = CCommon::AdjustNullValue( $_POST['panel_reply'] ,$this->mConfig->mUserPanelReply );
        $pnael_log_protect = CCommon::AdjustNullValue( $_POST['pnael_log_protect'] ,$this->mConfig->mUserPanelPassword );

        //メンバー用ツールのログイン情報から値を取得
        $login_pass = $this->mCtrlUserPwd->GetPassword( ML_NAME );

        // エラーメッセージが設定されている場合は、
        // フォームから遷移してきたと判断して、パスワードは空に
        if ($this->errMsg != ''){
            $login_pass = null;
        }

        if(isset($panel_maillog)){
            //パスワードが設定済みであることを認識するために、ダミーで表示する
            if (isset($login_pass)){
                $panel_password = '*********';
                $warningtxt = '';
            }else{
                $panel_password = '';
                $warningtxt = '（※パスワード未設定の場合は、全ての方の閲覧が可能となります）<br />';
            }
        }

        //設定先URLを生成する
        $user_url    = '';
        $user_url    = 'http://' . $_SERVER["SERVER_NAME"] . '/xmailinglist/' . ML_NAME . '/';

        //URLが長い場合、レイアウトが崩れないよう表示用URLに改行を挿入
        if (mb_strlen($user_url,'UTF-8') < 80){
            $disp_url    = $user_url;
        }else{
            $disp_url    = 'http://' . $_SERVER["SERVER_NAME"] . '/xmailinglist/ <br>' . ML_NAME . '/';
        }

        $action_url  = $user_url . 'mail.php';

        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/view_user.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_VIEWUSER, $header );

        //テンプレートの置換
        $contents = str_replace( '{$disp_url}',   $disp_url,   $contents );
        $contents = str_replace( '{$user_url}',   $user_url,   $contents );
        $contents = str_replace( '{$action_url}', $action_url, $contents );

        //エラーメッセージの置換
        if (isset($this->errMsg)){
            $error_txt .= '<ul><li>';
            $error_txt .= $this->errMsg;
            $error_txt .= '</li></ul>';
        }else{
            $error_txt = '';
        }
        $contents = str_replace( '{$error_txt}',   $error_txt,   $contents );

        //ユーザーへの公開情報の置換
        for( $i=0;$i<=1;$i++ ){
            if( $i==$panel_open ){         $contents = str_replace( '{$panel_open_' . $i . '}', 'selected', $contents );
            }else{                         $contents = str_replace( '{$panel_open_' . $i . '}', '',         $contents );
            }
            if( $i==$panel_maillog ){      $contents = str_replace( '{$panel_maillog_' . $i . '}', 'selected', $contents );
            }else{                         $contents = str_replace( '{$panel_maillog_' . $i . '}', '',         $contents );
            }
            if( $i==$panel_apply ){        $contents = str_replace( '{$panel_apply_' . $i . '}', 'selected', $contents );
            }else{                         $contents = str_replace( '{$panel_apply_' . $i . '}', '',         $contents );
            }
            if( $i==$panel_withdraw ){     $contents = str_replace( '{$panel_withdraw_' . $i . '}', 'selected', $contents );
            }else{                         $contents = str_replace( '{$panel_withdraw_' . $i . '}', '',         $contents );
            }
            if( $i==$panel_replay ){       $contents = str_replace( '{$panel_reply_' . $i . '}', 'selected', $contents );
            }else{                         $contents = str_replace( '{$panel_reply_' . $i . '}', '',         $contents );
            }
        }

        $contents = str_replace( '{$panel_user_id}', ML_NAME , $contents );
        $contents = str_replace( '{$panel_user_password}', $panel_password, $contents );
        $contents = str_replace( '（※パスワード未設定の場合は、全ての方の閲覧が可能となります）<br />', $warningtxt , $contents );

        //表示／非表示の切り替え
        if( $panel_open ){  $contents = str_replace( '{$display_table}', 'display:block', $contents );
        }else{              $contents = str_replace( '{$display_table}', 'display:none',  $contents );
        }
        if( $panel_maillog ){ $contents = str_replace( '{$display_login}', 'display:table-row', $contents );
        }else{              $contents = str_replace( '{$display_login}', 'display:none',  $contents );
        }

        if ($pnael_log_protect == 'on'){
            $contents = str_replace( '{pnael_log_protect_checked}', 'checked', $contents );
        }else{
            $contents = str_replace( '{pnael_log_protect_checked}', '', $contents );
        }

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // 公開設定の保存処理
    //==============================
    function GetHtml_AdminViewUserDo() {
        $panel_open      = CCommon::AdjustNullValue( $_POST['panel_open'] ,$this->mConfig->mUserPanelOpen );
        //$panel_login     = CCommon::AdjustNullValue( $_POST['panel_login'] ,$this->mConfig->mUserPanelLogin );
        $panel_id        = CCommon::AdjustNullValue( $_POST['panel_user_id'] ,$this->mConfig->mUserPanelID );
        $panel_password  = CCommon::AdjustNullValue( $_POST['panel_user_password'] );
        $panel_maillog   = CCommon::AdjustNullValue( $_POST['panel_maillog'] ,$this->mConfig->mUserPanelMailLog );
        $panel_apply     = CCommon::AdjustNullValue( $_POST['panel_apply'] ,$this->mConfig->mUserPanelApply );
        $panel_withdraw  = CCommon::AdjustNullValue( $_POST['panel_withdraw'] ,$this->mConfig->mUserPanelWithdraw );
        $panel_replay    = CCommon::AdjustNullValue( $_POST['panel_reply'] ,$this->mConfig->mUserPanelReply );
        $pnael_log_protect = CCommon::AdjustNullValue( $_POST['pnael_log_protect'] ,'' );

        //除外判定
        $flag = true;
        if( ! isset( $_POST['sb_setting_save'] ) ){   $flag = false; }

        //閲覧ページありでパスワード入力ある場合のみ、ログインありに設定
        if( $panel_open == 1){
            if( $panel_maillog == 1 && $pnael_log_protect == 'on'){
                $panel_login = 1;
            }else{
                $panel_login = 0;
            }
        }else{
            $panel_login = 0;
        }

        if ($panel_open == 1 && $panel_login == 1){
            if (mb_strlen($panel_password,'UTF-8') != ''){
                if (mb_strlen($panel_password, 'UTF-8') < 4){
                    $flag = false;
                    $this->errMsg = '保護パスワードを設定する場合は、4文字以上16文字以下で入力して下さい';
                }
                if (16 < mb_strlen($panel_password,'UTF-8')){
                    $flag = false;
                    $this->errMsg = '保護パスワードを設定する場合は、4文字以上16文字以下で入力して下さい';
                }
            }else{
                //画面のパスワードが空の場合、かつファイル未設定の場合エラー
                $password = $this->mCtrlUserPwd->GetPassword(ML_NAME);
                if ($pnael_log_protect == 'on'){
                    $flag = false;
                    $this->errMsg = '保護パスワードを入力して下さい';
                }
            }
        }

        //入力値が不正な場合は、入力画面を表示する
        if( ! $flag ){
            $this->GetHtml_AdminViewUser();
            exit;
        }

        //ユーザー公開設定の更新
        $this->mConfig->mUserPanelOpen     = $panel_open;
        $this->mConfig->mUserPanelLogin    = $panel_login;
        $this->mConfig->mUserPanelID       = $panel_id;
        $this->mConfig->mUserPanelMailLog  = $panel_maillog;
        $this->mConfig->mUserPanelPassword = $pnael_log_protect;
        $this->mConfig->mUserPanelApply    = $panel_apply;
        $this->mConfig->mUserPanelWithdraw = $panel_withdraw;
        $this->mConfig->mUserPanelReply    = $panel_replay;
        $this->mConfig->Update();

        //パスワードを設定
        if ($panel_password == ''){
            file_put_contents(PROJECT_BASE_CONFIG .'/login_info.db', $null);
        }else{
            //if ($panel_password != '' && $panel_password != '*********'){
            if ($pnael_log_protect == 'on' && $panel_password != ''){
                $password = md5($panel_password);
                if( $this->mCtrlUserPwd->SetPassword(ML_NAME, $password)){
                }
                    //変更完了したため、パスワードを変更する
                    $_SESSION['MEMBER_ID'] = ML_NAME;
                    $_SESSION['MEMBER_PASSWORD'] = $password;
            }
        }

        header( 'Location: ./?page=ViewUserExit' );
        exit;
    }

    //==============================
    // 公開設定の保存完了ページ
    //==============================
    function GetHtml_AdminViewUserExit() {
        //----------------------------------------
        // HTMLの作成処理
        //----------------------------------------
        $html     = '';
        $header   = $this->mTemplate->GetHTML( 'admin/header.tpl' );
        $contents = $this->mTemplate->GetHTML( 'admin/view_user_exit.tpl' );
        $footer   = $this->mTemplate->GetHTML( 'admin/footer.tpl' );

        //ヘッダー情報の置換
        $header   = str_replace( '{$ml_app_name}', $this->mAppTitleName , $header );
        $header   = str_replace( '{$select_index}', CCtrlML::eSELECTPAGE_VIEWUSER, $header );

        $html     = $header . $contents . $footer;
        echo $html;
    }

    //==============================
    // ログアウト処理
    //==============================
    function GetHtml_AdminLogout() {
        //$_SESSION = array();
        //session_destroy();
        $_SESSION['MAILINGLIST_ID']         = '';
        $_SESSION['MAILINGLIST_PASSWORD']   = '';
        $_SESSION['MAILINGLIST_PASSTYPE']   = '';

        header( "Location: ./" );
        exit;
    }

    //============================================================
    // 入会用メールの送信
    //============================================================
    function GetHtml_MailReg() {
        $sb_reg   = CCommon::AdjustNullValue( $_POST['sb_reg'] ) ;
        $add_mail = CCommon::AdjustNullValue( $_POST['add_mail'] );
        $identity = CCommon::AdjustNullValue( $_POST['identity'] );

        $flag = true;
        if( $sb_reg   == '' ) { $flag = false; }
        if( $add_mail == '' ) { $flag = false; }
        if( $identity == '' ) { $flag = false; }

        $strResult = 'メール送信に失敗しました。';

        //管理者メールアドレスに登録されていないか確認する
        if ($this->mCtrlFml->CheckModerators($add_mail)){
             $strResult = '「'. CCommon::EscHtml( $add_mail ) .'」は管理者メールアドレスで登録されています。';
            $flag = false;
        }

        // 入会処理
        $ret = $this->mCtrlCmdMail->SendAddMail($add_mail, $identity);

        if ( $flag ) {
            switch ( $ret ) {
                case 0:
                   $strResult = '入会意思確認のメールを送信しました。';
                    break;
                case 99:
                    $strResult = '既に入会済みです。'."<br>". '詳しくは管理者へお問い合わせ下さい。';
                    break;
                case 1000:
                    $strResult = '現在入会受付できません。'."<br>". '詳しくは管理者へお問い合わせ下さい。';
                    break;
            }
        }

        //HTMLの出力
        $html    = $this->mTemplate->GetHTML( 'mail.tpl' );
        $html    = str_replace( '{$mail_result}', $strResult, $html );

        echo $html;
    }

    //============================================================
    // 退会用メールの送信
    //============================================================
    function GetHtml_MailRel() {
        $sb_rel   = CCommon::AdjustNullValue( $_POST['sb_rel'] );
        $delete_mail = CCommon::AdjustNullValue( $_POST['delete_mail'] );
        $identity = CCommon::AdjustNullValue( $_POST['identity'] );

        $flag = true;
        if( $sb_rel   == '' )    { $flag = false; }
        if( $delete_mail == '' ) { $flag = false; }
        if( $identity == '' )    { $flag = false; }

        $strResult = 'メール送信に失敗しました。';

        if ( $flag ) {
            // 退会処理
            $ret = $this->mCtrlCmdMail->SendDeleteMail($delete_mail, $identity);
            switch ( $ret ) {
                case 0:
                    $strResult = '退会意思確認のメールを送信しました。';
                    break;
                case 99:
                    $strResult = '既に退会済みです。'."<br>". '詳しくは管理者へお問い合わせ下さい。';
                    break;
            }
        }

        //HTMLの出力
        $html    = $this->mTemplate->GetHTML( 'mail.tpl' );
        $html    = str_replace( '{$mail_result}', $strResult, $html );

        echo $html;
    }


    //============================================================
    // エラーメールの解析(phpコマンド実行用)
    //============================================================
    function AnalyzeAdminMail() {
        //----------------------------------------
        // エラーメールを解析して集計
        //----------------------------------------
        $ctrlErrMail = new CCtrlErrMail($this->mCtrlFml->GetMLPath());

        // エラーメールを標準入力から取得して解析
        $stdin = file_get_contents('php://stdin');
        $ctrlErrMail->AnalyzeErrorMail($stdin);

        // メールをバックアップ
        $mtime = explode(' ', microtime());
        $savedir = $this->mCtrlFml->mMlPath . 'mailback/';
        $path =  $savedir. date('YmdHis', time(true)) . $mtime[1];
        if (!file_exists($savedir)) {
            mkdir($savedir);
        }
        file_put_contents($path, $stdin);

        // =======================================
        // **テスト用** テスト用のメールデータのパスを定義する
        // =======================================
/*
        $testdata = array(
        );
        foreach($testdata as $path) {
            $ctrlErrMail->AnalyzeErrorMail(file_get_contents($path));
        }
*/
        // =======================================
        // **テスト用** メールフォルダからメールデータを取得して
        //              解析/集計を実行
        //              (過去の解析もクリアされる)
        // =======================================
//        $ctrlErrMail->DeleteFile();
//        $ctrlErrMail->AnalyzeAllErrorMail($this->mCtrlFml->GetAdminMailPath());

        //----------------------------------------
        // 自動削除
        //----------------------------------------
        if ($this->mConfig->mErrMailAutoDelete) {
            // 設定の取得
            $settings = array('sum' => $this->mConfig->mErrMailNum);
            // 削除用データ生成(メモ／権限をコピー)
            $delete_mails = array();
            $list = $ctrlErrMail->GetAutoDeleteUser($settings);
            foreach($list as $address => $data) {
                foreach($this->mMemberInfo as $memberInfo) {
                   if ($address === $memberInfo['mail']) {
                        $list[$address]['memo'] = $memberInfo['memo'];
                        $list[$address]['status'] = $memberInfo['status'];
                        if (isset($memberInfo['memo'])) {
                            array_push($delete_mails, $address);
                        }
                    }
                }
            }
            if (count($delete_mails) > 0) {
                // 削除実行
                $this->MembersDeleteForAddr($delete_mails);
                $ctrlErrMail->DeleteUser($list);

                if ($this->mConfig->mErrMailAutoDelNotify) {
                    // 通知メール送信
                    $members = implode("\n", $delete_mails);
                    //本文を上書き
                    $mailaddress = ML_NAME . '@' . DOMAIN_NAME;
                    $contents = $this->mCtrlFml->GetTextFileWithAutoNotice();
                    $contents = str_replace('###ML_TITLENAME###', $mailaddress , $contents);
                    $contents = str_replace('###NEW_MEMBER###', $members , $contents);

                    CCommon::SendMail(
                            $fromname,
                            $this->mMailData->mMLMainAddress,
                            $this->mMailData->mMLAdminAddress,
                            '自動削除実行',
                            $contents
                    );
                }
            }
        }
    }


    //============================================================
    // 自動登録メール
    //============================================================
    function AutoApplyMail() {

        // 自動登録メールを標準入力から取得して解析
        $mailData = file_get_contents('php://stdin');

        // メールデータをヘッダとボディに分けてヘッダからFromアドレスを取得
        $line = array();
        $tmp = str_replace( array("\r\n","\r"), "\n", $mailData);
        list($head, $body) = explode("\n\n", $tmp, 2);
        $line = explode("\n", $head);

	    // Fromヘッダーが２行にわたる場合のフラグ
        $block_flag = false;

        foreach($line as $row){

	        if (!$block_flag){
                // Fromヘッダーでない場合は次行へ
	            if (strtolower(substr($row,0,4)) !== strtolower('from')){
		            continue;
	            }
                // コンテンツ名とアドレス部を分割して、メールアドレスチェックを行う
                list($item, $user_mail) = explode(":", $row, 2);
            }else{
		        $user_mail = $row;
            }

            // <>囲みを省いて送信元のメールアドレスのみ取得
            $user_mail = deleteFromStart($user_mail, "<", true, true);
            $user_mail = deleteFromEnd($user_mail, ">", true, true);
            $user_mail = trim($user_mail);

            // メールアドレスとして正しくない場合、次へ
            if( !CCommon::IsMailAddress($user_mail) || $this->IsNgMailAddress($user_mail)){
                $user_mail = "";
                $block_flag = true;
                continue;
            }else{
	            break;
            }
        }

        // 想定外のため送信元アドレスが取得できなかった場合は、ヘッダーをログに出力して処理中断
	    if ($user_mail == ''){
           if (file_exists(PROJECT_BASE_CONFIG. '/autoapply.log')){
               $contents = @file_get_contents(PROJECT_BASE_CONFIG. '/autoapply.log');
               $contents.= "\n"."\n".'----------'."\n".$head;
	       }else{
	           $contents = $head;
	       }
           file_put_contents(PROJECT_BASE_CONFIG. '/autoapply.log', $contents);
	       return;
	    }

        // 既にメンバー登録されている場合ははじく
        $error_flag = false;
        if( ML_MODE == 'mailinglist' ){
        	$from = $this->mConfig->mAdminAppName;
            if ($this->mCtrlFml->CheckMemberUser($user_mail) or $this->mCtrlFml->CheckActiveUser($user_mail) or $this->mCtrlFml->CheckMemberAdmin($user_mail)){
                $Message = $user_mail. 'は既に登録されています。';
                $mode = 'メーリングリスト';
                $error_flag = true;
            }
        }else{
	        $from = $this->mConfig->mAdminFromName;
	        if(!$from || $from == ''){
	            $from = $this->mConfig->mAdminAppName;
	        }
            if ($this->mCtrlFml->CheckActiveUser($user_mail)){
                $Message = $user_mail. 'は既に登録されています。';
                $mode = 'メールマガジン';
                $error_flag = true;
            }
        }

        // メンバー登録済みであることを通知
        if ($error_flag){

            //メール本文を編集
            $contents = $this->mCtrlFml->GetTextFileApplyError();
            $contents = str_replace('###ML_MLMODE###', $mode , $contents);
            $contents = str_replace('###ML_TITLENAME###', $this->mAppTitleName, $contents);
            $contents = str_replace('###APPLY_ADDRESS###', ML_NAME. '-apply@'. DOMAIN_NAME  , $contents);
            $contents = str_replace('###NEW_MEMBER###', $user_mail , $contents);

            CCommon::SendMail(
                    //$this->mAppTitleName,
                    $from,
                    $this->mMailData->mMLMainAddress,
                    $user_mail,
                    $this->mConfig->mSystemMailApplyErrorSubject,
                    $contents
            );

            return;
        }

        if( ML_MODE == 'mailinglist' ){

            // 環境設定＞自動入会メンバーの権限設定に応じて登録
            $ml_member_auth  = $this->mConfig->mMlMemberAuth;
            switch ($ml_member_auth) {
                //【メール受信＋メール配信】の場合
                case CCtrlML::ML_AUTH_MEMBERS:
                    $this->mCtrlFml->AddMemberUser( $user_mail );
                    break;
                //【受信のみ】の場合
                case CCtrlML::ML_AUTH_ACTIVES:
                    $this->mCtrlFml->AddActiveUser( $user_mail );
                    break;
                //【配信のみ】の場合
                case CCtrlML::ML_AUTH_MEMBERS_ADMIN:
                    $this->mCtrlFml->AddMembersAdmin( $user_mail );
                    break;
            }

        }else{
            $this->mCtrlFml->AddActiveUser( $user_mail );
        }

        // ユーザーへ登録完了メールを送信
        CCommon::SendMail(
                //$this->mAppTitleName,
                $from,
                $this->mMailData->mMLMainAddress,
                $user_mail,
                $this->mConfig->mSystemMailWelcomeSubject,
                $this->mCtrlFml->GetTextFileWelcome()
        );

        // 管理者へ自動登録メンバー加入通知を送信
        $adminmail = $this->mCtrlFml->GetAdminModerators();
        if (!is_null($adminmail)){
            $mailaddress = ML_NAME . '@' . DOMAIN_NAME;
            $contents = $this->mCtrlFml->GetTextFileAdNotice();
            $contents = str_replace('###ML_TITLENAME###', $this->mAppTitleName , $contents);
            $contents = str_replace('###NEW_MEMBER###', $user_mail , $contents);

            CCommon::SendMail(
                    //$this->mAppTitleName,
                    $from,
                    $this->mMailData->mMLMainAddress,
                    $adminmail,
                    'メンバー加入( ' . $user_mail . ' )',
                    $contents
            );
        }

    }




    //============================================================
    // ページングリンク生成
    // $list        :   リスト
    // $href        :   リンク先
    // $pageno      :   現在のページ
    // $pagequery   :   クエリストリング
    // $query       :   その他クエリ
    //============================================================
    function GetPaging($list, $href = '', $pageno = 1, $pagequery = 'p', $query = null) {
        //ページ送りの作成
        $buf_tags = '';
        //最大ページの取得
        $checkmax = floor(count($list)/CCtrlML::eMAIL_PAGEVIEW_MAX);
        if( (count($list) % CCtrlML::eMAIL_PAGEVIEW_MAX) != 0 ){
            $checkmax += 1;
        }
        //ページが数字以外の場合
        if( ! is_numeric( $pageno ) ){
            $pageno   = 1;
        }
        //最大ページ以上の値の場合
        if( $pageno > $checkmax ){
            $pageno   = $checkmax;
        }
        //０以下の値の場合
        if( $pageno < 1 ){
            $pageno   = 1;
        }
        $query  = is_null($query) ? array() : $query;

        if( count($list) > CCtrlML::eMAIL_PAGEVIEW_MAX ){
            //「戻る」リンク
            if( $pageno > 1 ){
                $query[$pagequery]  = $pageno - 1;
                $buf_tags .= '<a href="'.$href.'?' . http_build_query($query) . '">';
                $buf_tags .= '&lt;&lt;&nbsp;戻る';
                $buf_tags .= '</a>&nbsp;';
            }else{
                $buf_tags .= '&lt;&lt;&nbsp;戻る&nbsp;';
            }
            // ページ番号のリンク作成
            for( $i=0;$i<9;$i++ ){
                $baseno  = ( $pageno - 4 );
                if( $pageno <= 5 ){                  $baseno = 1;                 }
                if( $pageno > ( $checkmax - 5 ) ){   $baseno = ( $checkmax - 8 ); }
                if( $baseno <= 0 ){                  $baseno = 1;                 }

                if( ( $baseno + $i ) > $checkmax ){    continue;    }

                if( $pageno != ( $baseno + $i ) ){
                    $query[$pagequery]  = $baseno + $i;
                    $buf_tags .= '&nbsp;<a href="'.$href.'?' . http_build_query($query) . '">';
                    $buf_tags .= '[' . ( $baseno + $i ) . ']';
                    $buf_tags .= '</a>&nbsp;';
                }else{
                    $buf_tags .= '&nbsp;[' . ( $baseno + $i ) . ']&nbsp;';
                }
            }
            //「次へ」リンク
            if( $pageno < $checkmax ){
                $query[$pagequery]  = $pageno + 1;
                $buf_tags .= '&nbsp;<a href="'.$href.'?' . http_build_query($query) . '">';
                $buf_tags .= '次へ&nbsp;&gt;&gt;';
                $buf_tags .= '</a>';
            }else{
                $buf_tags .= '&nbsp;次へ&nbsp;&gt;&gt;';
            }
        }
        return $buf_tags;
    }
}




    //============================================================
    // 最初から探索し、$needleにあたるまでを削除する
    //
    // @param string    $target       削除する対象
    // @param array     $needle       目印となる文字列
    // @param integer   $deleteNeedle $needleを削除する場合True
    // @param boolean   $complete     最初の$needleまで削除する場合false　最後の$needleまで削除する場合true
    //
    // @return string 削除後の文字列
    //
    //============================================================
    function deleteFromStart($target, $needle, $deleteNeedle=true, $complete = false)
    {
        if(function_exists("mb_stripos")){
            $pos = mb_stripos($target, $needle);

            if($pos === false) { return $target;}
            if($deleteNeedle){
                $pos += mb_strlen($needle);
            }
            $result = mb_substr($target, $pos);

            if ($complete && mb_stripos($result, $needle) !== false){
                $result = deleteFromStart($result, $needle, $deleteNeedle, $complete);
            }
            return $result;
        }else{

            $pos = stripos($target, $needle);

            if($pos === false) { return $target;}
            if(deleteNeedle){
                $pos += mb_strlen($needle);
            }
            $result = substr($target, $pos);

            if ($complete && stripos($result, $needle) !== false){
                $result = deleteFromStart($result, $needle, $deleteNeedle, $complete);
            }
            return $result;
        }



    }

    //============================================================
    // 最後から探索し、$needleにあたるまでを削除する
    //
    // @param string    $target       削除する対象
    // @param array     $needle       目印となる文字列
    // @param integer   $deleteNeedle $needleを削除する場合True
    // @param boolean   $complete     最初の$needleまで削除する場合false　最後の$needleまで削除する場合true
    //
    // @return string 削除後の文字列
    //============================================================
    function deleteFromEnd($target, $needle, $deleteNeedle=true, $complete = false)
    {

        if(function_exists("mb_strripos")){

            $pos = mb_strripos($target, $needle);
            if($pos === false) return $target;
            if(!$deleteNeedle){
                $pos += mb_strlen($needle);
            }

            $result = mb_substr($target,0, $pos);
            if ($complete && mb_strripos($result, $needle) !== false){
                $result = deleteFromEnd($result, $needle, $deleteNeedle, $complete);
            }
            return $result;
        }else{

            $pos = strripos($target, $needle);
            if($pos === false) return $target;
            if(!deleteNeedle){
                $pos += mb_strlen($needle);
            }

            $result = mb_substr($target,0, $pos);
            if ($complete && strripos($result, $needle) !== false){
                $result = deleteFromEnd($result, $needle, $deleteNeedle, $complete);
            }
            return $result;
        }
    }


?>

