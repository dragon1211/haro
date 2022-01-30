<?php

//require_once dirname(dirname(__FILE__)) . '/config/common.ini';

//=====================================
// メールアドレスの情報を保持します
//=====================================
class CMailData {
    var $mMLMainAddress;        //メーリングリストのメインアドレス
    var $mMLCtrlAddress;        //メーリングリストの制御用アドレス
    var $mMLAdminAddress;       //メーリングリストの管理用アドレス
    var $mMLmoderatoAddress;    //メーリングリストモデレート用アドレス


    //================================
    //
    // コンストラクタ
    //
    //================================
    function __construct(){
        $this->InitMailData();
    }

    //================================
    // メールアドレスの初期化
    // 返り値            : なし
    //================================
    function InitMailData() {
        //管理用のメールアドレスの設定
        $this->mMLMainAddress  = ML_NAME . '@' . DOMAIN_NAME;
        $this->mMLCtrlAddress  = ML_NAME . '-ctl@' . DOMAIN_NAME;
        $this->mMLAdminAddress = ML_NAME . '-admin@' . DOMAIN_NAME;
    }

}

?>
