<?php

require_once PROJECT_BASE . '/lib/CCommon.php';

// ログイン制御
$mLogin = new CLogin();

//チェック
if( ! $mLogin->Login() ){

    //ログイン中でない場合は、ログインページを表示する
    echo $mLogin->GetLoginPage();
    exit;
}


//===========================================
//
//  ログイン制御
//
//===========================================
class CLogin {
    var $mConfig;

    function __construct() {
        //settingファイルを取得
        $this->mConfig = new CConfig();
        $this->mConfig->Load();
    }



    //===============================================
    // ログインチェックを行う
    // 返り値：ログイン中 true、ログイン前 false
    //===============================================
    public function Login() {
                
        if($this->isLoginSession()){
            if ( file_exists(PROJECT_BASE_CONFIG .'/otpwd.db')){
                 unlink(PROJECT_BASE_CONFIG . '/otpwd.db' );
            }
            return true;
        }
        
        return $this->isLoginPost();
    }


    //=========================
    //  セッション変数にログイン情報があるかどうか
    //  返り値：ログイン中 true、ログイン前 false
    //=========================
    function isLoginSession(){

        //  セッション変数の代入
        $s_MlId =       CCommon::AdjustNullValue($_SESSION['MAILINGLIST_ID']);
        $s_PassType =   CCommon::AdjustNullValue($_SESSION['MAILINGLIST_PASSTYPE']);
        $s_Pass =       CCommon::AdjustNullValue($_SESSION['MAILINGLIST_PASSWORD']);
                //セッション変数のログイン状態を確認
        if( $s_MlId == ID ){
            if( $s_PassType == 'PASSWORD'  && $s_Pass == PASSWORD  ){ return true; }
            if( $s_PassType == 'PASSWORD2' && $s_Pass == PASSWORD2 ){ return true; }
        }
        return false;
    }
    
    
    //=========================
    //  POST変数にログイン情報があるかどうか
    //  返り値：ログイン中 true、ログイン前 false
    //=========================
    function isLoginPost(){
    
        //  Post変数の代入
        $p_LoginType =   CCommon::AdjustNullValue($_POST['login_type']);
        $p_UserName =   CCommon::AdjustNullValue($_POST['username']);
        $p_PassMd5 =    md5( CCommon::AdjustNullValue($_POST['password']));
        //サーバーパネルからのログインの場合
        if (isset($p_LoginType) && $p_LoginType == 'server_panel'){
            if ( file_exists(PROJECT_BASE_CONFIG .'/otpwd.db')){
                //ワンタイムパスワードを取得
                $otPwdList = array();
                $data = file_get_contents( PROJECT_BASE_CONFIG . '/otpwd.db' );
                //ファイル削除
                unlink(PROJECT_BASE_CONFIG . '/otpwd.db' );
                $otPwdList = unserialize($data);
                //ワンタイムパスワードが一致している場合、ファイルを破棄して通常のパスワードをセッションにセットする
                if ($p_PassMd5 === $otPwdList[$p_UserName]){
                    $p_PassMd5 = PASSWORD2;
                    $_SESSION['MAILINGLIST_ID']         = $p_UserName;
                    $_SESSION['MAILINGLIST_PASSWORD']   = $p_PassMd5;
                    $_SESSION['MAILINGLIST_PASSTYPE']   = 'PASSWORD2';
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
            
        }else{
        
            //ポスト値でのログインの確認
            if($p_UserName == ID ){

                $passType = '';

                //  PasswordType２種類でのログインチェック
                if($p_PassMd5 == PASSWORD){
                    $passType = 'PASSWORD';
                }
                if($p_PassMd5 == PASSWORD2){
                    $passType = 'PASSWORD2';
                }else{
                    //  どちらのログイン状況にも対応していない場合falseを返す
                    return false;
                }
                
                //  以下、Post値によってログインが認証された場合の処理

                //セッション変数へ保存する
                $_SESSION['MAILINGLIST_ID']         = $p_UserName;
                $_SESSION['MAILINGLIST_PASSWORD']   = $p_PassMd5;
                $_SESSION['MAILINGLIST_PASSTYPE']   = $passType;
            
                //  デバッグモード用    -----------------------------------------------------
            
                $debug_mode = CCommon::AdjustNullValue($_POST['debug_mode']);
            
                if(isset($_POST['debug_mode'])){
                    if($debug_mode == 'mailinglist' || $debug_mode == 'mailmagazine'){
                        $_SESSION['ML_MODE'] = $debug_mode;
                    }else{
                        $_SESSION['ML_MODE'] = ML_MODE;
                    }
                }else{
                    $_SESSION['ML_MODE'] = ML_MODE;
                }
                
                //  デバッグモード用    -----------------------------------------------------

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

        $strTxt = file_get_contents(PROJECT_BASE . '/template_' . ML_MODE . '/admin/login.tpl' );
        $errTxt = '';
		if (ML_MODE == 'mailinglist'){
			$ml_name = 'メーリングリストアドレス';
		}else{
			$ml_name = 'メールマガジンアドレス';
		}
		
        if( isset( $_POST['login_button'] ) ){
            if( $_POST['login_button'] == 'ログイン' ){
                //ログインに失敗したときのみエラー文字を表示
                $errTxt .= '<br />';
                $errTxt .= '<div class="red_txt">';
                $errTxt .= '<ul><li>';
                $errTxt .= $ml_name . 'もしくはパスワードが間違っています';
                $errTxt .= '</li></ul>';
                $errTxt .= '</div>';
            }
        }

        //debugモードの切替
        $tmp = get_defined_constants();
        $def = $tmp['DEBUG_MODE'];
        $debug_mode = '';
        if ($def){
            if(DEBUG_MODE == 'debug_on'){
                $debug_mode .= '<tr>';
                $debug_mode .= '<th scope="row">DEBUG_MODE</th>';
                $debug_mode .= '<td><input type="text" name="debug_mode" size="40" /></td>';
                $debug_mode .= '</tr>';
            }
        }

        //置換用文字列を変更する
        $strTxt = str_replace( '{$error_txt}', $errTxt, $strTxt );
        $strTxt = str_replace( '{DEBUG_MODE}', $debug_mode, $strTxt );
        $strTxt = str_replace( '{$ml_app_name}', $this->mConfig->mAdminAppName  , $strTxt );

        return $strTxt;
    }

}

?>
