<?php

//===========================================
//
//  共通クラス
//
//===========================================
class CCommon {
    //================================
    // メールの送信
	// $strFromName        : 送信者名
    // $strFromMailAddress : 送信元のメールアドレス
    // $strToMailAddress   : 送信先のメールアドレス
    // $strMailSubject     : メールの件名
    // $strMailContents    : メールの本文
    // $xmlKey              : ヘッダに付加するメールマガジンのKey
    // 返り値              : true(メールアドレス形式)、false
    //================================
    public static function SendMail( $strFromName, $strFromMailAddress, $strToMailAddress, $strMailSubject, $strMailContents, $xmlKey="") {
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
        if($xmlKey != ""){
            $AddHeader .= "X-MG-KEY: " . $xmlKey . "\n";
        }

        //送信実行
        $ret    = mail(
                $strToMailAddress,
                $strMailSubject,
                $strMailContents,
                $AddHeader
            );

        return $ret;
    }

    //=====================================
    // メールアドレスの判定
    //=====================================
    public static function IsMailAddress( $strMail ){
        if ( ! preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\.\+_-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $strMail) ){
            return false;
        }
        return true;
    }

    //=====================================
    // mbstring.encoding_translation = On 用の対策
    //=====================================
    public static function CheckTranslationEncoding(){
        $default     = 'UTF-8';
        $translaton  = ini_get('mbstring.encoding_translation');
        $encoding    = ini_get('mbstring.internal_encoding');
        if( $translaton ){
            $_GET       = CCommon::CheckArrayStringEncode( $_GET,     $default, $encoding );
            $_POST      = CCommon::CheckArrayStringEncode( $_POST,    $default, $encoding );
            $_REQUEST   = CCommon::CheckArrayStringEncode( $_REQUEST, $default, $encoding );
            $_COOKIE    = CCommon::CheckArrayStringEncode( $_COOKIE,  $default, $encoding );
        }
    }
    private static function CheckArrayStringEncode( $arrData, $strOutputEncode, $strInputEncode ) {
        $strDefaultEncode = 'UTF-8';
        if( ! CCommon::CheckEffectiveEncode( $strInputEncode ) ){
            $strInputEncode = $strDefaultEncode;
        }
        if( ! CCommon::CheckEffectiveEncode( $strOutputEncode ) ){
            $strOutputEncode = $strDefaultEncode;
        }
        if( is_array( $arrData ) ){
            foreach( $arrData as $key => $value ) {
                $arrData[$key] = CCommon::CheckArrayStringEncode( $value, $strOutputEncode, $strInputEncode );
            }
            return $arrData;
        }
        return mb_convert_encoding( $arrData, $strOutputEncode, $strInputEncode );
    }

    //既存の文字コードであるかを判定する
    public static function CheckEffectiveEncode( $strEncode ){
        switch( $strEncode ){
            case 'ASCII':  return true;
            case 'JIS':    return true;
            case 'UTF-8':  return true;
            case 'EUC-JP': return true;
            case 'SJIS':   return true;
        }
        return false;
    }


    //==============================
    // get_magic_quotes_gpc対策
    //==============================
    public static function CheckMagicQuotesGpc(){
        if( ini_get('magic_quotes_gpc') ){
            $_GET       = CCommon::CheckStripslashes( $_GET );
            $_POST      = CCommon::CheckStripslashes( $_POST );
            $_REQUEST   = CCommon::CheckStripslashes( $_REQUEST );
            $_COOKIE    = CCommon::CheckStripslashes( $_COOKIE );
        }
    }
    private static function CheckStripslashes( $arrData ) {
        if( is_array( $arrData ) ){
            foreach( $arrData as $key => $value ){
                $arrData[$key] = CCommon::CheckStripslashes( $value );
            }
            return $arrData;
        }
        return stripslashes( $arrData );
    }

    //==============================
    // ランダムな文字列を取得
    //==============================
    public static function GetRandomString( $nLengthRequired = 8 ){
        $sCharList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
        mt_srand( CCommon::MakeSeed() );
        $sRes = "";
        for($i = 0; $i < $nLengthRequired; $i++){
            $sRes .= $sCharList[mt_rand(0, strlen($sCharList) - 1)];
        }
        return $sRes;
    }

    public static function MakeSeed(){
        list( $usec, $sec ) = explode( ' ', microtime() );
        return (float) $sec + ((float) $usec * 100000 );
    }


    //==============================
    //  $valの値がnullかどうかを確認し、nullの場合は、""(空文字)を返す。
    //  nullでない場合は、そのまま$valを返す。
    //  $defaultに指定があれば空文字の変わりにそれを返す
    //==============================
    public static function AdjustNullValue( &$val, $default="" ){
        if(isset( $val )){
            return $val;
        }else{
            return $default;
        }
    }


    //==============================
    // htmlspecialcharsを実行するエイリアス
    //==============================
    public static function EscHtml( &$str ){
        $val = CCommon::AdjustNullValue( $str );
        return htmlspecialchars( $val,  ENT_QUOTES, "UTF-8" );
    }


    //==============================
    // mhonarcが生成したhtmlをパース
    //==============================
    public static function ParseMailHtml( &$str ){

        $separator = '###';
        $tmp_data = array();
        $res = strrpos($str, $separator);
        $tmp_data[0] = substr($str, 0, $res);
        $tmp_data[1] = substr($str, $res + (strlen($separator)));

        return $tmp_data;
    }
}

?>
