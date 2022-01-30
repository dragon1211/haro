<?php

//require_once dirname(dirname(__FILE__)) . '/config/common.ini';
require_once PROJECT_BASE . '/lib/CConfig.php';
require_once PROJECT_BASE . '/lib/CCtrlUserPwd.php';
require_once PROJECT_BASE . '/lib/CCtrlFml.php';


// ログイン処理
$mUserLogin = new CUserLogin();
while( true ){
    //ログイン情報の確認
    if( $mUserLogin->Login() ){
        break;
    }
    echo $mUserLogin->GetLoginPage();
    exit;
}


//===========================================
//
//  ログイン制御
//
//===========================================
class CUserLogin {
    var $mConfig;
    var $mCtrlUserPwd;

    //===============================================
    //
    // コンストラクタ
    //
    //===============================================
    function CUserLogin(){
        $this->mConfig = new CConfig();
        $this->mConfig->Load();
        $this->mCtrlUserPwd = new CCtrlUserPwd();

    }

    //===============================================
    // ログインチェックを行う
    // 返り値：ログイン中 true、ログイン前 false
    //===============================================
    public function Login() {
	
	    //入退会はログイン必要なし
	    $login = CCommon::AdjustNullValue( $_GET['login'] );
		if ($login == 1){
			return true;
		}
		
        //ログインが必要ない場合は、最初にハジく
        if( ! $this->mConfig->mUserPanelLogin ){
            return true;
        }

        //セッション変数を確認
        if( isset( $_SESSION['USER_ID'] ) && isset( $_SESSION['USER_PASSWORD'] ) ){
            if( $this->mCtrlUserPwd->CheckUserPwd( $_SESSION['USER_ID'], $_SESSION['USER_PASSWORD'] ) ){
                return true;
            }
        }

        //ポスト値の確認
        if( isset( $_POST['username'] ) && isset( $_POST['password'] ) ){
            //入力フォームからの入力の場合は「PASSWORD」
            if( $this->mCtrlUserPwd->CheckUserPwd( $_POST['username'], md5($_POST['password']) ) ){
                //セッション変数へ保存する
                $_SESSION['USER_ID']         = $_POST['username'];
                $_SESSION['USER_PASSWORD']   = md5( $_POST['password'] );

                //変数内部の値を初期化する(次の画面の影響を抑えるため)
                $_POST['username']      = '';
                $_POST['password']      = '';
                $_POST['login_button']  = '';
                return true;
            }
        }

        return false;
    }

    //===============================================
    // ログイン状態の確認
    // 返り値：ログイン中 true、ログイン前 false
    //===============================================
    public function GetLoginPage(){
        if( ! $this->mConfig->mUserPanelMailLog ){
            header( 'Location: ./?page=Apply' );
            exit;
        }

        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template_' . ML_MODE . '/login.tpl' );
        $errTxt = '';
        if( isset( $_POST['login_button'] ) ){
            if( $_POST['login_button'] == 'ログイン' ){
                //ログインに失敗したときのみエラー文字を表示
                $errTxt .= '<br />';
                $errTxt .= '<div class="red_txt">';
                $errTxt .= '<ul><li>';
                $errTxt .= 'パスワードが間違っています';
                $errTxt .= '</li></ul>';
                $errTxt .= '</div>';
            }
        }

        $linkTxt = '';
        if( $this->mConfig->mUserPanelApply &&  $this->mConfig->mUserPanelWithdraw ){
            $linkTxt = '<li>[<a href="./?page=Apply&login=1">入退会手続き</a>]</li>';
        }elseif ( $this->mConfig->mUserPanelApply &&  !$this->mConfig->mUserPanelWithdraw ){
            $linkTxt = '<li>[<a href="./?page=Apply&login=1">入会手続き</a>]</li>';
        }elseif ( !$this->mConfig->mUserPanelApply &&  $this->mConfig->mUserPanelWithdraw ){
            $linkTxt = '<li>[<a href="./?page=Apply&login=1">退会手続き</a>]</li>';
        }else{
            $linkTxt = '';
        }

        $admissionTxt = '';
        if( $this->mConfig->mUserPanelApply ){
            $admissionTxt = '※メーリングリストに入会希望の方は、上記「入退会手続き」より入会手続きを行ってください。';
        }

        //置換用文字列を変更する
        $strTxt = str_replace( '{$error_txt}',     $errTxt,       $strTxt );
        $strTxt = str_replace( '{$ml_url_apply}',  $linkTxt,      $strTxt );
        $strTxt = str_replace( '{$admission_txt}', $admissionTxt, $strTxt );
        $strTxt = str_replace( '{$ml_app_name}', $this->mConfig->mAdminAppName  , $strTxt );
        $strTxt = str_replace( '{user_name}', ML_NAME, $strTxt );

        return $strTxt;
    }

}

?>
