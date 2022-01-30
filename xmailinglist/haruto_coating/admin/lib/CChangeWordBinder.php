<?php

//===================================
//
//対象文字の置換クラスのバインダー
//
//===================================
class CChangeWordBinder{
    var $mChangefiles;    //バインダー
    var $mChangewords;    //バインダー
    var $mFileCounter;    //アイテム数のカウンター
    var $mWordCounter;    //アイテム数のカウンター

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct(){
        $this->mChangefiles = array();
        $this->mChangewords = array();
        $this->mFileCounter = 0;
        $this->mWordCounter = 0;
    }

    //=====================================
    // 変更対象のファイル名
    //=====================================
    function AddFileName( $strFileName ){
        $this->mChangefiles[$this->mFileCounter++]  = $strFileName;
    }

    //=====================================
    // 文字列の追加
    //=====================================
    function AddWord( $objObjectWord, $objTargetWord ){
        $this->mChangewords[$this->mWordCounter++]    = new CChangeWord( $objObjectWord, $objTargetWord );
    }

    //=====================================
    // ファイル内容から指定文字を置換する
    //=====================================
    function ChangeData(){
        foreach( $this->mChangefiles as $fileName ){
            //ファイル情報を取得する
            $contents = file_get_contents( $fileName );

            //指定文字を置換する
            foreach( $this->mChangewords as $changeWord ){
                $contents = $changeWord->ChangeWordForText( $contents );
            }

            //ファイル情報を出力する
            file_put_contents( $fileName, $contents );
        }
    }
}


//===================================
//
// 対象文字の置換クラス
//
//===================================
class CChangeWord{
    var $mSearchWord;    //変更対象の文字
    var $mChangeWord;    //変更後の文字

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $search, $change ){
        $this->mSearchWord      = $search;
        $this->mChangeWord      = $change;
    }

    //=====================================
    // 文字列の変更
    // $strText : 変更を行うテキスト
    // 返り値   : 対象文字を置換したテキスト
    //=====================================
    function ChangeWordForText( $strText ){
        return str_replace( $this->mSearchWord, $this->mChangeWord, $strText );
    }
}


?>
