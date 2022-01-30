<?php

require_once PROJECT_BASE . '/lib/CMailData.php';
require_once PROJECT_BASE . '/lib/CCtrlFml.php';
require_once PROJECT_BASE . '/lib/CCheckConfirm.php';

class CCtrlCmdMail {


    //=====================
    // 定数の定義
    //=====================
    //メーリングリストメンバー上限値
    const ML_MAX_MEMBER = 500;

    //メールマガジン上限値
    const MG_MAX_MEMBER = 1000;

    //=====================
    // 変数の定義
    //=====================
    var $mMailData;
    var $mCtrlFml;
    var $mCheckConfirm;
    var $mConfig;

    //===================================
    //
    // コンタクター
    //
    //===================================
    function CCtrlCmdMail() {
        $this->mMailData      = new CMailData();
        $this->mCtrlFml       = new CCtrlFml();
        $this->mCheckConfirm  = new CCheckConfirm();
        $this->mConfig        = new CConfig();
    }

    //================================
    // メーリングリスト入会用の意思確認メールの送信
    // 返り値        true : 0   = メール送信
    //              false : -1  = メール送信なし
    //                false : 99  = 既に登録済みの場合
    //                false : 1000= メンバー数が上限値の場合
    //================================
    function SendAddMail($mail, $identity) {
        //アクセス確認
        if ($identity != AUTO_KEY){
            return -1;
        }
        //メンバーが既に上限値の場合は、入会確認メール送信しない
        $member = $this->mCtrlFml->GetMemberCount();
        $actives = $this->mCtrlFml->GetActiveCount();

        // 上限値の取得
        if ( $_SESSION['ML_MODE'] == 'mailinglist' ){
            $maxMember = CCtrlCmdMail::ML_MAX_MEMBER;
        } else {
            $maxMember = CCtrlCmdMail::MG_MAX_MEMBER;
        }

        // 上限数チェック
        if( $maxMember <= $member + $actives ){
            return 1000;
        }

        //メールアドレスのチェック
        if( ! $this->IsMailGrammar( $mail ) ){
            return -1;
        }

        //参加メンバーであるかの確認
        //※参加済みの場合は、メールを送らない
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            if( $this->mCtrlFml->CheckMemberUser( $mail ) ||  $this->mCtrlFml->CheckActiveUser( $mail ) || $this->mCtrlFml->CheckMemberAdmin( $mail ) ){    return 99;    }
        }else{
            if( $this->mCtrlFml->CheckActiveUser( $mail ) ){   return 99;    }
        }

        $param  = CCommon::GetRandomString();

        //認証コードを登録する
        $this->mCheckConfirm->AddAdmisionMail( $mail, $param, '' );

        //設定先URLを生成する
        $url    = '';
        $url    = 'http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
        $url    = dirname( $url );
        #$url    = ' ' . $url . '/?page=Admission&id='.AUTO_KEY. '&param=' . $param . '&mail=' . $mail . ' ';
        $url    = ' ' . $url . '/?page=Admission&id=' . $param;

        $contents  = '';
        $contents .= $this->mCtrlFml->GetTextFileAdmission() . "\n";
        if( mb_strpos( $contents, '{$$eML_NAME$$}@{$$eDOMAIN_NAME$$}', 0, 'UTF-8' ) !== false ){
            //置換文字がある場合は、置換文字を変換する
            $contents = str_replace( '{$$eML_NAME$$}@{$$eDOMAIN_NAME$$}', $_SESSION['MAILINGLIST_ID'], $contents);
        }

        if( mb_strpos( $contents, '###認証ＵＲＬ###', 0, 'UTF-8' ) !== false ){
            //置換文字がある場合は、置換文字を変換する
            $contents = str_replace( '###認証ＵＲＬ###', $url, $contents );
        }else{
            //置換文字がない場合は、最後尾に連結する
            $contents .= "\n";
			$contents .= "入会確認用URL" . "\n";
            $contents .= $url . "\n";
        }

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

        //ユーザーへ入会の意思確認メールを送信する
        $ret = $this->CtrlSendMail(
                $fromname,
                $this->mMailData->mMLMainAddress,
                $mail,
                $this->mConfig->mSystemMailAdmissionSubject,
                $contents
            );

        if ($ret){
            return 0;
        }else{
            return -1;
        }

        return -1;
    }


    //================================
    // メーリングリスト入会用の意思確認メールの送信
    // 返り値         :なし
    //================================
    function SendAddMailLearge($address) {
		$mail = '';
        $contents = '';

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

        foreach ($address as $value){
			list($mail, $memo) = explode("," , $value);
            $param  = CCommon::GetRandomString();

            //認証コードを登録する
            $this->mCheckConfirm->AddAdmisionMail( $mail, $param, $memo );

            //設定先URLを生成する
            $url    = '';
            $url    = 'http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
            $url    = dirname(dirname( $url ));
            #$url    = ' ' . $url . '/?page=Admission&param=' . $param . '&mail=' . $mail . ' ';
            $url    = ' ' . $url . '/?page=Admission&id=' . $param;

            $contents  = '';
            $contents .= $this->mCtrlFml->GetTextFileAdmission() . "\n";
            if( mb_strpos( $contents, '{$$eML_NAME$$}@{$$eDOMAIN_NAME$$}', 0, 'UTF-8' ) !== false ){
                //置換文字がある場合は、置換文字を変換する
                $contents = str_replace( '{$$eML_NAME$$}@{$$eDOMAIN_NAME$$}', $_SESSION['MAILINGLIST_ID'], $contents);
            }

            if( mb_strpos( $contents, '###認証ＵＲＬ###', 0, 'UTF-8' ) !== false ){
                //置換文字がある場合は、置換文字を変換する
                $contents = str_replace( '###認証ＵＲＬ###', $url, $contents );
            }else{
                //置換文字がない場合は、最後尾に連結する
                $contents .= "\n";
				$contents .= "入会確認用URL" . "\n";
                $contents .= $url . "\n";
            }

            //ユーザーへ入会の意思確認メールを送信する
            $ret = $this->CtrlSendMail(
                    $fromname,
                    $this->mMailData->mMLMainAddress,
                    $mail,
                    $this->mConfig->mSystemMailAdmissionSubject,
                    $contents
            );

        }

    }


    //================================
    // メーリングリスト退会用の意思確認メールの送信
    // 返り値        true : 0   = メール送信
    //              false : -1  = メール送信なし
    //                false : 99  = 既に登録済みの場合
    //================================
    function SendDeleteMail($mail, $identity) {
        //アクセス確認
        if ($identity != AUTO_KEY){
            return -1;
        }
        //メールアドレスのチェック
        if( ! $this->IsMailGrammar( $mail ) ){
            return -1;
        }

        //参加メンバーであるかの確認
        //※退会済みの場合は、メールを送らない
        if( $_SESSION['ML_MODE'] == 'mailinglist' ){
            if( !($this->mCtrlFml->CheckMemberUser( $mail )) && !($this->mCtrlFml->CheckActiveUser( $mail )) && !($this->mCtrlFml->CheckMemberAdmin( $mail )) ){   return 99;    }
        }else{
            if( ! $this->mCtrlFml->CheckActiveUser( $mail ) ){   return 99;    }
        }

        $param  = CCommon::GetRandomString();

        //認証コードを登録する
        $this->mCheckConfirm->AddWithDrawMail( $mail, $param );

        //設定先URLを生成する
        $url    = '';
        $url    = 'http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
        $url    = dirname( $url );
        #$url    = ' ' . $url . '/?page=Withdraw&param=' . $param . '&mail=' . $mail . ' ';
        $url    = ' ' . $url . '/?page=Withdraw&id=' . $param;

        $contents  = '';
        $contents .= $this->mCtrlFml->GetTextFileWithDraw() . "\n";
        if( mb_strpos( $contents, '{$$eML_NAME$$}@{$$eDOMAIN_NAME$$}', 0, 'UTF-8' ) !== false ){
            //置換文字がある場合は、置換文字を変換する
            $contents = str_replace( '{$$eML_NAME$$}@{$$eDOMAIN_NAME$$}', $_SESSION['MAILINGLIST_ID'], $contents);
        }
        if( mb_strpos( $contents, '###認証ＵＲＬ###', 0, 'UTF-8' ) !== false ){
            //置換文字がある場合は、置換文字を変換する
            $contents = str_replace( '###認証ＵＲＬ###', $url, $contents );
        }else{
            //置換文字がない場合は、最後尾に連結する
            $contents .= "\n";
			$contents .= "退会確認用URL" . "\n";
            $contents .= $url . "\n";
        }

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

        //ユーザーへ退会の意思確認メールを送信する
        $ret = $this->CtrlSendMail(
                $fromname,
                $this->mMailData->mMLMainAddress,
                $mail,
                $this->mConfig->mSystemMailWithdrawSubject,
                $contents
            );

        if ($ret){
            return 0;
        }else{
            return -1;
        }

        return -1;
    }

    //================================
    // メールアドレスの文法チェック
    // 確認する文字列がメールアドレスの形式化を判定します
    // $strMailAddress   : 確認するメールアドレス
    // 返り値            : true(メールアドレス形式)、false
    //================================
    function IsMailGrammar( $strMailAddress ) {
        if ( ! preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\.\+_-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $strMailAddress ) ){
            return false;
        }
        return true;
    }

    //================================
    // メールの送信
    // $strFromMailAddress : 送信元のメールアドレス
    // $strToMailAddress   : 送信先のメールアドレス
    // $strMailSubject     : メールの件名
    // $strMailContents    : メールの本文
    // 返り値              : true(メールアドレス形式)、false
    //================================
    function CtrlSendMail( $strFromName, $strFromMailAddress, $strToMailAddress, $strMailSubject, $strMailContents ) {
        mb_language("ja");

        $orgEncoding = mb_internal_encoding();
        mb_internal_encoding('ISO-2022-JP');
        $strMailSubject  = mb_convert_encoding( $strMailSubject, 'ISO-2022-JP', 'UTF-8' );
        $strMailSubject  = mb_encode_mimeheader( $strMailSubject, 'ISO-2022-JP');
        $strFromName  = mb_convert_encoding( $strFromName, 'ISO-2022-JP', 'UTF-8' );
        $strFromName  = mb_encode_mimeheader( $strFromName, 'ISO-2022-JP');
        mb_internal_encoding($orgEncoding);

        $strMailContents = mb_convert_encoding( $strMailContents, 'ISO-2022-JP', 'UTF-8' );

        //ヘッダの構成
        $AddHeader  = '';
        $AddHeader .= "Content-Type: text/plain;charset=ISO-2022-JP\r\n";
        $AddHeader .= "Content-Transfer-Encoding: 7bit\r\n";
        $AddHeader .= "MIME-Version: 1.0\r\n";
        $AddHeader .= "From: " . $strFromName . "<" . $strFromMailAddress . ">" . "\n";

        //送信実行
        $ret    = mail(
                $strToMailAddress,
                $strMailSubject,
                $strMailContents,
                $AddHeader );

        return $ret;
    }
}
?>

