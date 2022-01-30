<?php

//===========================================
//
//  テンプレート制御
//
//===========================================
class CTemplate {
    var $mFolderPath;  //フォルダへのパス
    var $mValues;      //置換用の変数配列

    //==============================
    //
    // コンストラクタ
    //
    //==============================
    function __construct(){
        $this->mFolderPath  = '';
        $this->mValues      = array();
    }

    //==============================
    // フォルダパスの設定
    // strPath    : フォルダへのパス
    //==============================
    function SetFolder( $strPath ){
        $this->mFolderPath    = $strPath;
    }

    //==============================
    // 変数の初期化
    //==============================
    function InitValues(){
        unset( $this->mValues );
        $this->mValues      = null;
        $this->mValues      = array();
    }

    //==============================
    // ＨＴＭＬの取得
    // strFileName : 読込対象のファイル名
    // 返り値      : 変数置換後のＨＴＭＬ
    //==============================
    function GetHTML( $strFileName ){
        //フォルダの有無確認
        if( ! is_dir( $this->mFolderPath ) ){    return '';    }

        //ファイルの有無確認
        if( ! is_file( $this->mFolderPath . $strFileName ) ){    return '';    }

        //ＨＴＭＬデータ
        $strHtmlData = file_get_contents( $this->mFolderPath . $strFileName );

        //変数の置換
        foreach( $this->mValues as $keyData => $valueData ){
            $strHtmlData = str_replace( $keyData, $valueData, $strHtmlData );
        }

        return $strHtmlData;
    }

    //==============================
    // 置換を行うための変数を設定します
    // strKey    : 置換対象の文字列を設定します
    // strValue  : 置換後の文字列を設定します
    // 返り値    : 登録の成否
    //==============================
    function SetValue( $strKey, $strValue ){
        if( $strKey == '' ){
            return false;
        }
        $this->mValues[$strKey]    = $strValue;
        return true;
    }
}

?>
