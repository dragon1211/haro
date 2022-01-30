<?php

require_once PROJECT_BASE . '/lib/CCtrlFml.php';
require_once PROJECT_BASE . '/lib/CCtrlErrMail.php';

//===========================================
//
//  設定ファイル(config)の操作クラス
//  -- 2013.12.10 sato メルマガ配信ナンバーファイル (auto_number) 操作追加
//
//===========================================
class CConfig {
    //==============================
    // 変数の定義
    //==============================
    //設定データの識別用
    var $mCheckString;

    //設定ファイルのデータ
    var $mUserPanelOpen;        //ユーザーパネルの表示
    var $mUserPanelLogin;       //ユーザーパネルのログイン有無
    var $mUserPanelID;          //ユーザーパネルのログイン用ＩＤ
    var $mUserPanelPassword;    //ユーザーパネルのログイン用パスワード
    var $mUserPanelMailLog;     //ユーザーパネルのメールログ画面の有無
    var $mUserPanelApply;       //ユーザーパネルの入会フォーム有無
    var $mUserPanelWithdraw;    //ユーザーパネルの退会フォーム有無
    var $mUserPanelReply;       //ユーザーパネルの返信メール有無
    var $mMlMemberAuth;         //管理者ツールのメンバーの自動入会時の権限
	var $mAdminFromName;        //メルマガ管理者ツールの送信者名
	var $mAdminTitleName;	    //メルマガ管理者ツールのタイトル
	var $mAdminAppName;	        //メーリングリスト名／メールマガジン名
    var $mAdminAutoNumber;      // メルマガ配信ナンバー
    
    var $mErrMailAutoDelete;    // エラーメール自動削除有効化  1:有効, 0:無効
    var $mErrMailFatalErrNum;   // エラーメール自動削除「恒久的」回数
    var $mErrMailTempErrNum;    // エラーメール自動削除「一時的」回数
    var $mErrMailUnknownErrNum; // エラーメール自動削除「その他」回数
    var $mErrMailNum;           // エラーメール回数設定値
    var $mErrMailAutoDelNotify; // エラーメール自動削除後にメール通知  1:有効, 0:無効

    //==============================
    //
    // コンストラクタ
    //
    //==============================
    function __construct() {
        $this->Load();
    }

    //==============================
    // 設定ファイルの初期化
    //==============================
    function Init() {
        $this->mUserPanelOpen      = 0;
        $this->mUserPanelLogin     = 0;
        $this->mUserPanelID        = '';
        $this->mUserPanelPassword  = '';
        $this->mUserPanelMailLog   = 0;
        $this->mUserPanelApply     = 0;
        $this->mUserPanelWithdraw  = 0;
        $this->mUserPanelReply     = 0;
        $this->mMlMemberAuth       = 1;
        $this->mSystemMailAdmissionSubject = '';
        $this->mSystemMailWithdrawSubject  = '';
        $this->mSystemMailWelcomeSubject   = '';
        $this->mSystemMailGoodbyeSubject   = '';
        $this->mSystemMailApplyErrorSubject   = '';
		$this->mAdminFromName      = ML_NAME;
		$this->mAdminTitleName = 0;
        $this->mAdminAppName = "";
        $this->mAdminAutoNumber = 1;
        $this->mErrMailAutoDelete   = 1;
        $this->mErrMailFatalErrNum  = 5;
        $this->mErrMailTempErrNum   = -1;
        $this->mErrMailUnknownErrNum= -1;
        $this->mErrMailNum          = CCtrlErrMail::ERR_DEFAULT_NUM;
        $this->mErrMailAutoDelNotify= ($_SESSION['ML_MODE'] == 'mailinglist') ? 1 : 0;  // default ML:ON, MM:OFF

        $count = 0;
        //確認を行う文字列郡
        $this->mCheckString       = array();
        $this->mCheckString[$count++] = 'USER_PANEL_OPEN';
        $this->mCheckString[$count++] = 'USER_PANEL_LOGIN';
        $this->mCheckString[$count++] = 'USER_PANEL_ID';
        $this->mCheckString[$count++] = 'USER_PANEL_PASSWORD';
        $this->mCheckString[$count++] = 'USER_PANEL_MAILLOG';
        $this->mCheckString[$count++] = 'USER_PANEL_APPLY';
        $this->mCheckString[$count++] = 'USER_PANEL_WITHDRAW';
        $this->mCheckString[$count++] = 'USER_PANEL_REPLY';
        $this->mCheckString[$count++] = 'ML_MEMBER_AUTH';
        $this->mCheckString[$count++] = 'SYSTEM_MAIL_ADMISSION_SUBJECT';
        $this->mCheckString[$count++] = 'SYSTEM_MAIL_WITHDRAW_SUBJECT';
        $this->mCheckString[$count++] = 'SYSTEM_MAIL_WELCOME_SUBJECT';
        $this->mCheckString[$count++] = 'SYSTEM_MAIL_GOODBY_SUBJECT';
        $this->mCheckString[$count++] = 'SYSTEM_MAIL_APPLYERROR_SUBJECT';
        $this->mCheckString[$count++] = 'ADMIN_FROM_NAME';
        $this->mCheckString[$count++] = 'ADMIN_TITLE_NAME';
        $this->mCheckString[$count++] = 'ADMIN_APP_NAME';
        $this->mCheckString[$count++] = 'ADMIN_AUTO_NUMBER';
        $this->mCheckString[$count++] = 'ERROR_MAIL_AUTO_DELETE';
        $this->mCheckString[$count++] = 'ERROR_MAIL_FATAL_ERROR_NUM';
        $this->mCheckString[$count++] = 'ERROR_MAIL_TEMP_ERROR_NUM';
        $this->mCheckString[$count++] = 'ERROR_MAIL_UNKNOWN_ERROR_NUM';
        $this->mCheckString[$count++] = 'ERROR_MAIL_NUM';
        $this->mCheckString[$count++] = 'ERROR_MAIL_AUTO_DELETE_NOTIFY';
    }

    //==============================
    // 設定ファイルの呼び込み
    //==============================
    function Load() {
        //設定の初期化
        $this->Init();
        $lines = array();

        //設定ファイルの呼び込み
        $lines = file( PROJECT_BASE_CONFIG . '/config' );
        
        // auto_number 追加
        $auto_number    = @file_get_contents( PROJECT_BASE_CONFIG . '/auto_number');
        $lines[]    = 'BEGIN_ADMIN_AUTO_NUMBER 1';
        $lines[]    = ($auto_number !== FALSE) ? $auto_number : $this->mAdminAutoNumber;
        $lines[]    = 'END_ADMIN_AUTO_NUMBER';
        
        $strKey   = '';
        $nLine    = 0;

        foreach( $lines as $value ) {
            //空白の削除
            $value = trim($value);

            //開始／終了キーの確認
            if( $strKey ){
                if( $nLine > 0 ){
                    //読み込み保障期間は強制的に読み込む
                    $nLine--;
                }else{
                    //終了タグのチェック
                    if( $value === 'END_' . $strKey ){
                        $strKey = '';
                        $nLine  = 0;
                        continue;
                    }
                }
            }else{
                //空行の場合は判定を行わない
                if( $value == "" ){
                    continue;
                }

                //開始タグの検索
                foreach( $this->mCheckString as $chkWord ){
                    if( preg_match( '/^BEGIN_' . $chkWord . '\s[1-9]+[0-9]*$/', $value ) ){
                        $strKey   = $chkWord;
                        
                        //読み込み保障期間の取得
                        $arrBuf   = explode( ' ', $value );
                        if( count($arrBuf) == 2 ){
                            if( is_numeric($arrBuf[1]) ){
                                $nLine = $arrBuf[1];
                            }
                        }
                        break;
                    }
                }

               continue;
            }

            switch( $strKey ){
                case 'USER_PANEL_OPEN':      $this->mUserPanelOpen      = $value; break;
                case 'USER_PANEL_LOGIN':     $this->mUserPanelLogin     = $value; break;
                case 'USER_PANEL_ID':        $this->mUserPanelID        = $value; break;
                case 'USER_PANEL_PASSWORD':  $this->mUserPanelPassword  = $value; break;
                case 'USER_PANEL_MAILLOG':   $this->mUserPanelMailLog   = $value; break;
                case 'USER_PANEL_APPLY':     $this->mUserPanelApply     = $value; break;
                case 'USER_PANEL_WITHDRAW':  $this->mUserPanelWithdraw  = $value; break;
                case 'USER_PANEL_REPLY':     $this->mUserPanelReply     = $value; break;
                case 'ML_MEMBER_AUTH':       $this->mMlMemberAuth       = $value; break;
                case 'SYSTEM_MAIL_ADMISSION_SUBJECT':   $this->mSystemMailAdmissionSubject    = $value; break;
                case 'SYSTEM_MAIL_WITHDRAW_SUBJECT':    $this->mSystemMailWithdrawSubject     = $value; break;
                case 'SYSTEM_MAIL_WELCOME_SUBJECT':     $this->mSystemMailWelcomeSubject      = $value; break;
                case 'SYSTEM_MAIL_GOODBY_SUBJECT':      $this->mSystemMailGoodbyeSubject      = $value; break;
                case 'SYSTEM_MAIL_APPLYERROR_SUBJECT':  $this->mSystemMailApplyErrorSubject   = $value; break;
				case 'ADMIN_FROM_NAME':      $this->mAdminFromName      = $value; break;
				case 'ADMIN_TITLE_NAME':     $this->mAdminTitleName     = $value; break;
                case 'ADMIN_APP_NAME':       $this->mAdminAppName       = $value; break;
                case 'ADMIN_AUTO_NUMBER':    $this->mAdminAutoNumber    = $value; break;
                case 'ERROR_MAIL_AUTO_DELETE':          $this->mErrMailAutoDelete   = $value; break;
                case 'ERROR_MAIL_FATAL_ERROR_NUM':      $this->mErrMailFatalErrNum  = $value; break;
                case 'ERROR_MAIL_TEMP_ERROR_NUM':       $this->mErrMailTempErrNum   = $value; break;
                case 'ERROR_MAIL_UNKNOWN_ERROR_NUM':    $this->mErrMailUnknownErrNum= $value; break;
                case 'ERROR_MAIL_NUM':                  $this->mErrMailNum          = $value; break;
                case 'ERROR_MAIL_AUTO_DELETE_NOTIFY':    $this->mErrMailAutoDelNotify= $value; break;
            }
            
            //旧ファイルにない項目を追加
            if ($this->mSystemMailApplyErrorSubject == ''){
                if( ML_MODE == 'mailinglist' ){
                    $this->mSystemMailApplyErrorSubject = 'メーリングリストのご入会に関して';
                }else{
                    $this->mSystemMailApplyErrorSubject = 'メールマガジンへのご登録に関して';
                }
            }
            
        }
    }

    //==============================
    // 設定ファイルへの書き込み
    //==============================
    function Update() {
        $put_contents  = '';
        $txt_data      = '';
        
        foreach( $this->mCheckString as $value ){
            //無駄なスペースを削除する
            $value = trim( $value );

            //内容
            $txt_data = '';
            switch( $value ){
                case 'USER_PANEL_OPEN':      $txt_data  .= $this->mUserPanelOpen;      break;
                case 'USER_PANEL_LOGIN':     $txt_data  .= $this->mUserPanelLogin;     break;
                case 'USER_PANEL_ID':        $txt_data  .= $this->mUserPanelID;        break;
                case 'USER_PANEL_PASSWORD':  $txt_data  .= $this->mUserPanelPassword;  break;
                case 'USER_PANEL_MAILLOG':   $txt_data  .= $this->mUserPanelMailLog;   break;
                case 'USER_PANEL_APPLY':     $txt_data  .= $this->mUserPanelApply;     break;
                case 'USER_PANEL_WITHDRAW':  $txt_data  .= $this->mUserPanelWithdraw;  break;
                case 'USER_PANEL_REPLY':     $txt_data  .= $this->mUserPanelReply;     break;
                case 'ML_MEMBER_AUTH':       $txt_data  .= $this->mMlMemberAuth;       break;
                case 'SYSTEM_MAIL_ADMISSION_SUBJECT':   $txt_data .= $this->mSystemMailAdmissionSubject;  break;
                case 'SYSTEM_MAIL_WITHDRAW_SUBJECT':    $txt_data .= $this->mSystemMailWithdrawSubject;   break;
                case 'SYSTEM_MAIL_WELCOME_SUBJECT':     $txt_data .= $this->mSystemMailWelcomeSubject;    break;
                case 'SYSTEM_MAIL_GOODBY_SUBJECT':      $txt_data .= $this->mSystemMailGoodbyeSubject;    break;
                case 'SYSTEM_MAIL_APPLYERROR_SUBJECT':  $txt_data.= $this->mSystemMailApplyErrorSubject; break;
				case 'ADMIN_FROM_NAME':      $txt_data  .= $this->mAdminFromName;      break;
				case 'ADMIN_TITLE_NAME':     $txt_data  .= $this->mAdminTitleName;     break;
                case 'ADMIN_APP_NAME':       $txt_data  .= $this->mAdminAppName;       break;
                case 'ERROR_MAIL_AUTO_DELETE':          $txt_data  .= $this->mErrMailAutoDelete; break;
                case 'ERROR_MAIL_FATAL_ERROR_NUM':      $txt_data  .= $this->mErrMailFatalErrNum; break;
                case 'ERROR_MAIL_TEMP_ERROR_NUM':       $txt_data  .= $this->mErrMailTempErrNum; break;
                case 'ERROR_MAIL_UNKNOWN_ERROR_NUM':    $txt_data  .= $this->mErrMailUnknownErrNum; break;
                case 'ERROR_MAIL_NUM':                  $txt_data  .= $this->mErrMailNum;           break;
                case 'ERROR_MAIL_AUTO_DELETE_NOTIFY':    $txt_data  .= $this->mErrMailAutoDelNotify; break;
            }
            //無駄なスペースを削除する
            $txt_data = trim( $txt_data );

            //開始タグ
            $put_contents  .= 'BEGIN_' . $value . ' ';
            $put_contents  .= count( explode( "\n", $txt_data ) );
            $put_contents  .= "\r\n";

            //内容
            $put_contents  .= $txt_data;
            $put_contents  .= "\r\n";

            //終了タグ
            $put_contents  .= 'END_' . $value . "\r\n";
            $put_contents  .= "\r\n";
        }

        //ファイルに出力を実行
        file_put_contents(PROJECT_BASE_CONFIG . '/config',$put_contents);
        
        // auto_number 出力
        file_put_contents(PROJECT_BASE_CONFIG . '/auto_number', $this->mAdminAutoNumber);
    }
    
    

}

?>
