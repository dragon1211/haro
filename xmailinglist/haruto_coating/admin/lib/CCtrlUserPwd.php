<?php

require_once PROJECT_BASE . '/lib/CCtrlFml.php';


//===========================================
//
//  ユーザー用パスワードの管理クラス
//
//===========================================
class CCtrlUserPwd {
    //パスワードの管理クラス
    var $mUserPwdList;
    

    //===============================================
    //
    // コンストラクタ
    //
    //===============================================
    function CCtrlUserPwd(){
        $this->Init();
    }

    //===============================================
    // 初期化
    //===============================================
    function Init(){

        //パスワード情報
        $this->mUserPwdList = array();
        $data = file_get_contents( PROJECT_BASE_CONFIG . '/login_info.db' );
        $this->mUserPwdList = unserialize( $data);

    }

    //===============================================
    // 更新
    //===============================================
    function Update(){
        if( is_null( $this->mUserPwdList ) ){
            return false;
        }

        //出力用データの作成
        $txt_contents = serialize( $this->mUserPwdList );

        //ファイルの出力
        file_put_contents( PROJECT_BASE_CONFIG . '/login_info.db', $txt_contents );

        return true;
    }

    //===============================================
    // ユーザーのパスワードを取得します。
    // 返り値：パスワードが確認できない場合はNULL
    //===============================================
    function GetPassword( $strMail ) {
        if( $strMail == '' ){    return NULL;    }
        if( isset( $this->mUserPwdList[$strMail] ) ){
            return $this->mUserPwdList[$strMail];
        }
        return NULL;
    }

    //===============================================
    // 該当のユーザーとパスワードが存在を確認します。
    // 返り値：確認できる場合は true を返す
    //===============================================
    function CheckUserPwd( $strMail, $strPassword ) {
        if( $strMail == '' ){        return false;    }
        if( $strPassword == '' ){    return false;    }

        if( isset( $this->mUserPwdList[$strMail] ) ){
            if( $this->mUserPwdList[$strMail] == $strPassword ){
                return true;
            }
        }

        return false;
    }

    //===============================================
    // 該当のユーザーMailが存在を確認します。
    // 返り値：確認できる場合は true を返す
    //===============================================
    function CheckUserMail( $strMail) {
        if( $strMail == '' ){        return false;    }
        if( $strPassword == '' ){    return false;    }

        if( isset( $this->mUserPwdList[$strMail] ) ){
            //if( $this->mUserPwdList[$strMail] == $strPassword ){
                return true;
            //}
        }

        return false;
    }


    //===============================================
    // ユーザーのパスワードを変更します。
    // 返り値：変更の成否
    //===============================================
    function SetPassword( $strMail, $strPassword ) {
        if( $strMail == '' ){        return false;    }
        if( $strPassword == '' ){    return false;    }

        //if( isset( $this->mUserPwdList[$strMail] ) ){
            //該当のパスワードを返す
            $this->mUserPwdList[$strMail] = $strPassword;
            $this->Update();
            return true;
        //}
        return false;
    }

    //===============================================
    // ユーザーのパスワードを新規作成します。
    // 返り値：作成されたパスワード
    //         既に作成済みの場合は、現在のパスワードを返す
    //===============================================
    function CreatePassword( $strMail, $strPassword ) {
        if( $strMail == '' ){        return NULL;    }
        if( $strPassword == '' ){    return NULL;    }

        if( isset( $this->mUserPwdList[$strMail] ) ){
            //該当のパスワードを返す
            return $this->mUserPwdList[$strMail];
        }

        $strPassword = md5($strPassword);
        $this->mUserPwdList[$strMail] = $strPassword;
        $this->Update();

        //パスワードを返す
        return $strPassword;
    }

    //===============================================
    // ユーザーを削除します。
    // 返り値：削除の成否
    //===============================================
    function DeletePassword( $strMail ) {
        if( $strMail == '' ){    return false;    }

        if( isset( $this->mUserPwdList[$strMail] ) ){
            $this->mUserPwdList[$strMail]    = '';
            $this->Update();
            $this->Init();
            return true;
        }
        return false;
    }
}

?>
