<?php

require_once PROJECT_BASE . '/lib/CChangeWordBinder.php';

//========================================================
//
// FML4.0 の操作クラス
//
//========================================================
class CCtrlFml
{
    //--------------------------------
    //識別用のパラメータ
    const eFILE_MLLIST        = 'mllist';        //メーリングリストの一覧
    const eFILE_CONFIG        = 'config';        //メーリングリストの設定ファイル
    const eFILE_ADMIN         = 'admin';         //メーリングリストの管理者
    const eFILE_MEMBER        = 'member';        //メーリングリストへの投稿権限の保持者
    const eFILE_ACTIVE        = 'active';        //メーリングリストのメール受信者
    const eFILE_MAIL          = 'mailhook';      //メーリングリストの管理者メールへのHook用aliases ファイル
    const eFILE_MODERATORS    = 'moderators';    //投稿制限等の承認者保存ファイル
    const eFILE_MEMBERSADMIN  = 'members-admin'; //管理者の保存ファイル
    const eFILE_MEMBERSNAME   = 'members-name';   //メーリングリストユーザーの名前とアドレス一覧(members + actives)

    //--------------------------------
    //ユーザー操作系のテキストファイル
    const eTXT_FILE_GUIDE       = 'guide';      //メーリングリストの紹介
    const eTXT_FILE_DENY        = 'deny';       //メンバー以外から投稿を行った場合の返信メッセージ
    const eTXT_FILE_OBJECTIVE   = 'objective';  //メーリングリストの目的
    const eTXT_FILE_HELP        = 'help';       //メンバー用のヘルプ
    const eTXT_FILE_HELPADMIN   = 'help-admin'; //管理者用のヘルプ
    const eTXT_FILE_CONFIRM     = 'confirm';    //subscribe 実行の際の確認用のメッセージ

    const eTXT_FILE_MAILHEADER  = 'mail_header'; //メールヘッダー
    const eTXT_FILE_MAILFOOTER  = 'mail_footer'; //メールフッター
    const eTXT_FILE_MGHEADER    = 'mg_header';   //メルマガヘッダー
    const eTXT_FILE_MGFOOTER    = 'mg_footer';   //メルマガフッター


    const eTXT_FILE_ADMISSION   = 'admission';   //入会の意思確認メール
    const eTXT_FILE_WITHDRAW    = 'withdraw';    //退会の意思確認メール
    //const eTXT_FILE_REISSUE     = 'reissue';    //パスワード再発行メール
    const eTXT_FILE_WELCOME     = 'welcome';     //入会完了時のメール  入会者用
    const eTXT_FILE_GOODBYE     = 'goodbye';     //退会完了時のメール  退会者用
    const eTXT_FILE_ADMISSION_NOTICE   = 'admission_notice';   //入会完了時のメール  管理者用
    const eTXT_FILE_WITHDRAW_NOTICE    = 'withdraw_notice';    //退会完了時のメール  退会者用
    const eTXT_FILE_WITHDRAW_AUTO_NOTICE    = 'withdraw_auto_notice';    //自動削除実行時のメール  管理者用
    const eTXT_FILE_APPLY_ERROR    = 'apply-error';    //自動入会で重複申し込みの際のエラー通知メール

    //--------------------------------
    //メーリングリストのタイトル形式
    const eTITLEFORM_SBRACKET_COLON = 1;    //表示形式  [Name:ID]
    const eTITLEFORM_RBRACKET_COLON = 2;    //表示形式  (Name:ID)
    const eTITLEFORM_SBRACKET_COMMA = 3;    //表示形式  [Name,ID]
    const eTITLEFORM_RBRACKET_COMMA = 4;    //表示形式  (Name,ID)
    const eTITLEFORM_SBRACKET_ID    = 5;    //表示形式  [ID]
    const eTITLEFORM_RBRACKET_ID    = 6;    //表示形式  (ID)
    const eTITLEFORM_SBRACKET_NAME  = 7;    //表示形式  [Name]
    const eTITLEFORM_RBRACKET_NAME  = 8;    //表示形式  (Name)
    const eTITLEFORM_BRACKET_NONE   = 0;    //表示なし

    //--------------------------------
    //容認の種類
    const ePERMIT_ANYONE            = 0;    //誰でもOK
    const ePERMIT_MENBERS_ONLY      = 1;    //メンバーのみ可能
    const ePERMIT_MODERATOR         = 2;    //管理者の容認者

    //--------------------------------
    //メールコマンドの制限設定
    const eMCLIMIT_MEMBER_LIST      = 1;    //メンバー（actives/members）の一覧取得
    const eMCLIMIT_ARTICLE_LIST     = 2;    //過去記事の取得
    const eMCLIMIT_OPTION_LIST      = 3;    //設定状況の取得

    //--------------------------------
    //メールサイズの制限設定
    const eMLSIZE_LIMIT_500KB      = 500000;       //メールサイズ500KBまで
    const eMLSIZE_LIMIT_1MB        = 1000000;       //メールサイズ1MBまで
    const eMLSIZE_LIMIT_3MB        = 3000000;       //メールサイズ3MBまで
    const eMLSIZE_LIMIT_10MB       = 10000000;      //メールサイズ10MBまで
    const eMLSIZE_LIMIT_15MB       = 15000000;      //メールサイズ15MBまで
    const eMLSIZE_LIMIT_20MB       = 20000000;      //メールサイズ20MBまで
    const eMLSIZE_LIMIT_DEFAULUT   = 0;       //メールサイズ制限なし0（当社規定30MB）

    //--------------------------------
    //メンバ
    var $mDomain;    // 対象ドメイン
    var $mUserName;  // 対象サーバーID
    var $mFmlName;   // fml のフォルダ名
    var $mMlName;    // メーリングリスト名
    var $mPath;      // 設定ファイルへのパス
    var $mContents;  // 設定ファイルの内容保存場所

    //--------------------------------
    // クラス専用
    var $mBasePath;     //基本となるパス
    var $mFmlPath;        //FML設置先フォルダパス
    var $mMlPath;       //該当メーリングリストへのパス
    var $mHttpPath;     //HTTPへのパス
    var $mHtdocsPath;   //MHonArcのhtml出力先パス

    //==================================
    //
    // コンストラクタ
    //
    //==================================
    function __construct() {

        $this->Initialize();
    }

//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//
// 個別処理
//
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
    //==================================
    // 初期化処理
    //==================================
    function Initialize()
    {
        $this->mDomain      = '';       //対象ドメイン
        $this->mUserName    = '';       //対象サーバーID
        $this->mMlName      = '';       //メーリングリスト名

        //設定ファイルへのパス
        $this->mPath = array(
            CCtrlFml::eFILE_MLLIST => NULL,
            CCtrlFml::eFILE_CONFIG => NULL,
            CCtrlFml::eFILE_ADMIN  => NULL,
            CCtrlFml::eFILE_MEMBER => NULL,
            CCtrlFml::eFILE_ACTIVE => NULL,
            CCtrlFml::eFILE_MODERATORS => NULL,
            CCtrlFml::eFILE_MEMBERSADMIN => NULL,
            CCtrlFml::eFILE_MEMBERSNAME   => NULL
        );

        //設定ファイルの内容
        $this->mContents = array(
            CCtrlFml::eFILE_MLLIST => NULL,
            CCtrlFml::eFILE_CONFIG => NULL,
            CCtrlFml::eFILE_ADMIN  => NULL,
            CCtrlFml::eFILE_MEMBER => NULL,
            CCtrlFml::eFILE_ACTIVE => NULL,
            CCtrlFml::eFILE_MODERATORS => NULL,
            CCtrlFml::eFILE_MEMBERSADMIN => NULL,
            CCtrlFml::eFILE_MEMBERSNAME   => NULL
        );

        //--------------------------------
        // クラス専用
        $this->mBasePath    = '';   //基本となるパス
        $this->mFmlPath     = '';   //FML設置先フォルダパス
        $this->mMlPath      = '';   //該当メーリングリストへのパス
        $this->mHttpPath    = '';   //HTTPへのパス
        $this->mHtdocsPath  = '';   //MHonArcの出力先パス

        //メーリングリストの操作用の初期設定
        $this->SetUsername( SERVER_ID );
        $this->SetDomain( DOMAIN_NAME );
        $this->SetFmlName( FML_NAME );
        $this->SetMLName( ML_NAME );
        $this->SetPath();
    }

    //==================================
    // メーリングリストの一覧を取得する
    // 返り値    : メーリングリスト一覧を返す
    //==================================
    function GetMailingList()
    {
        $list = explode( "\n", $this->mContents[CCtrlFml::eFILE_MLLIST] );
        $mailinglist = array();
        $text = '';
        $st_num  = 0;
        $ed_num  = 0;
        $counter = 0;
        $flag = true;

        foreach( $list as $value ){
            if( $value == "" )                                    continue;

            //開始位置の取得
            if( strpos( $value, $this->mBasePath ) === false )    continue;
            $st_num   = strpos( $value, $this->mBasePath );
            $st_num  += strlen( $this->mBasePath );

            $text = substr( $value, $st_num, strlen( $value ) - $st_num );

            //終了位置の取得
            if( strpos( $text, '/' ) === false )    continue;
            $ed_num   = strpos( $text, '/' );
            $text = substr( $text, 0, $ed_num );

            //テキストが存在することを確認する
            if( $text == "" )    continue;

            //既に登録済みかを確認する
            $flag = true;
            foreach( $mailinglist as $mllist ){
                if( $text == $mllist ){
                    $flag = false;
                    break;
                }
            }
            if( ! $flag )    continue;

            //必要な項目が全て存在するかを確認する
            if( strpos( $this->mContents[CCtrlFml::eFILE_MLLIST], $text . ': :include:' ) === false )       continue;
            if( strpos( $this->mContents[CCtrlFml::eFILE_MLLIST], $text . '-ctl: :include:' ) === false )   continue;
            if( strpos( $this->mContents[CCtrlFml::eFILE_MLLIST], $text . '-request: ' ) === false )        continue;
            if( strpos( $this->mContents[CCtrlFml::eFILE_MLLIST], $text . '-admin: ' ) === false )          continue;
            if( strpos( $this->mContents[CCtrlFml::eFILE_MLLIST], 'owner-' . $text ) === false )            continue;
            if( strpos( $this->mContents[CCtrlFml::eFILE_MLLIST], 'owner-' . $text . '-ctl: ' ) === false ) continue;

            //登録する
            $mailinglist[$counter++] = $text;
        }
        return $mailinglist;
    }

    //==================================
    // メーリングリストの追加
    // $strMailinglist : メーリングリスト名
    // $strMailaddress : 管理者のメールアドレス
    // 返り値          : なし
    //==================================
    function AddMailingList( $strMailinglist, $strMailaddress )
    {
        $mailinglist_now = $this->GetMailingList();
        $mailinglist_new = array();
        $flag = true;

        //パスを再設定する
        $this->SetMLName( $strMailinglist );
        $this->SetPath();

        //現在のメーリングリストとのチェック
        foreach( $mailinglist_now as $value ){
            if( $value == $strMailinglist ){
                $flag = false;
                break;
            }
        }

        //メーリングリストが存在する場合は処理を行わない
        if( !$flag )    return false;

        //----------------------------------------------------------
        // 定義 ( aliases ) の追加
        foreach( $mailinglist_now as $value ){
            $mailinglist_new[$counter++] = $value;
        }
        $mailinglist_new[$counter++] = $strMailinglist;
        $this->WriteMailingList( $mailinglist_new );

        //----------------------------------------------------------
        // フォルダの追加
        $this->SetMailFolder( $strMailinglist );

        //HTMLの出力先フォルダの作成
        if( $this->GetNewsView() ){
            shell_exec( 'mkdir ' . $this->mHttpPath . '/htdocs/' . $this->mMlName );
        }else{
            shell_exec( 'mkdir ' . $this->mHtdocsPath );
        }

        //----------------------------------------------------------
        // メールアドレスの追加
        $this->AddAdminUser( $strMailaddress );

        //----------------------------------------------------------
        // メールの aliases ( Hook部分 ) の追加
        $this->SetMailHook();

        return true;
    }

    //==================================
    // メーリングリストの削除
    // $strMailinglist : メーリングリスト名
    // 返り値          : なし
    //==================================
    function DeleteMailingList( $strMailinglist )
    {
        $mailinglist_now = $this->GetMailingList();
        $mailinglist_new = array();
        $counter = 0;
        $flag = false;

        //現在のメーリングリストとのチェック
        foreach( $mailinglist_now as $value ){
            if( $value == $strMailinglist ){
                $flag = true;
                break;
            }
        }

        //メーリングリストが存在しない場合は処理を行わない
        if( !$flag )    return;

        //----------------------------------------------------------
        // 定義 ( aliases ) の削除
        foreach( $mailinglist_now as $value ){
            if( $value == $strMailinglist )        continue;
            $mailinglist_new[$counter++] = $value;
        }
        $this->WriteMailingList( $mailinglist_new );

        //----------------------------------------------------------
        // フォルダの削除


        //----------------------------------------------------------
        // メールの aliases ( Hook部分 ) の削除
        $this->DeleteMailHook();

    }

    //==================================
    // メーリングリストの出力
    // $arrMailinglist : メーリングリスト名
    // 返り値          : なし
    //==================================
    function WriteMailingList( $arrMailinglist )
    {
        $strTxt = '';

        foreach( $arrMailinglist as $list ){
            $strTxt .= $list . ': :include:' . $this->mBasePath . $list . '/include' . "\n";
            $strTxt .= $list . '-ctl: :include:' . $this->mBasePath . $list . '/includectl' . "\n";
            $strTxt .= $list . '-request: ' . $list . '-admin' . "\n";
            $strTxt .= $list . '-admin: ' . $username . "\n";
            $strTxt .= 'owner-' . $list . ': ' . $username . "\n";
            $strTxt .= 'owner-' . $list . '-ctl: ' . $username . "\n";
            $strTxt .= "\n";
        }

        $this->mContents[CCtrlFml::eFILE_MLLIST] = $strTxt;

        $this->Update( $this->mPath[CCtrlFml::eFILE_MLLIST], $this->mContents[CCtrlFml::eFILE_MLLIST] );
    }


    //==================================
    // メーリングリストのフォルダ設置
    // $strMailinglist : メーリングリスト名
    // 返り値          : なし
    //==================================
    function SetMailFolder( $strMailinglist )
    {
        $path = $this->mBasePath . $strMailinglist;

        //フォルダの作成
        shell_exec( 'mkdir ' . $path );

        //zipファイルを解凍
        shell_exec( 'unzip ' . getcwd() . '/ml.zip -d ' . $path );

        //ファイル名の置換処理
        $bind = new CChangeWordBinder();

        //変更を行うFMLのファイル名の一覧
        $bind->AddFileName( $path . '/aliases' );
        $bind->AddFileName( $path . '/cf' );
        $bind->AddFileName( $path . '/config.ph' );
        $bind->AddFileName( $path . '/confirm' );
        $bind->AddFileName( $path . '/crontab' );
        $bind->AddFileName( $path . '/deny' );
        $bind->AddFileName( $path . '/fmlwrapper.c' );
        $bind->AddFileName( $path . '/guide' );
        $bind->AddFileName( $path . '/help' );
        $bind->AddFileName( $path . '/include' );
        $bind->AddFileName( $path . '/include-ctl' );
        $bind->AddFileName( $path . '/include-mead' );
        $bind->AddFileName( $path . '/Makefile' );
        $bind->AddFileName( $path . '/mhonarc.sh' );
        $bind->AddFileName( $path . '/objective' );
        $bind->AddFileName( $path . '/welcome' );

        //置換する文字の一覧
        $bind->AddWord( '{$$eINSTALL_FOLDER$$}', $this->mFmlName );     //インストールフォルダ
        $bind->AddWord( '{$$eSERVER_NAME$$}',    $this->mDomain );      //サーバー名
        $bind->AddWord( '{$$eSERVER_ID$$}',      $this->mUserName );    //サーバーID
        $bind->AddWord( '{$$eDOMAIN_NAME$$}',    $this->mDomain );      //ドメイン名
        $bind->AddWord( '{$$eDOMAIN_FQDN$$}',    $this->mDomain );      //FQDN
        $bind->AddWord( '{$$eML_NAME$$}',        $this->mMlName );      //メーリングリスト名

        //置換データを更新する
        $bind->ChangeData();
    }

    //==================================
    // メールの aliases ( Hook部分 ) の追加
    // 返り値          : なし
    //==================================
    function SetMailHook()
    {
        //------------------------------
        // メーリングリストのアドレスへの操作
        //------------------------------
        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '@' . $this->mDomain . '/.alias';
        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //テキストの書き込み内容
        $text  = '';
        $text .= 'cc "| ' . $this->mFmlPath . 'fml.pl ';
        $text .=  $this->mBasePath . $this->mMlName . '" ' . "\n";
        $text .= 'cc "| ' . $this->mMlPath . 'mhonarc.sh"' . "\n";
        $text .= 'exit'."\n";


        //.alias に記載がない場合は、末尾に追記
        if( strpos( $nowAlias, $text ) === false ){
            $text = $nowAlias . "\n" . $text;
            $this->Update( $path, $text );
        }

        //------------------------------
        // メーリングリストのコマンド用アドレスへの操作
        //------------------------------
        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '-ctl@' . $this->mDomain . '/.alias';

        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //テキストの書き込み内容
        $text  = '';
        $text .= 'cc "| ' . $this->mFmlPath . 'fml.pl ';
        $text .= $this->mBasePath . $this->mMlName . ' --ctladdr"' . "\n";

        //.alias に記載がない場合は、末尾に追記
        if( strpos( $nowAlias, $text ) === false ){
            $text = $nowAlias . "\n" . $text;
            $this->Update( $path, $text );
        }

    }

    //==================================
    // メールの aliases ( Hook部分 ) の削除
    // 返り値          : なし
    //==================================
    function DeleteMailHook()
    {
        //------------------------------
        // メーリングリストのアドレスへの操作
        //------------------------------
        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '@' . $this->mDomain . '/.alias';

        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //テキストの置換対象
        $text  = '';
        $text .= 'cc "| ' . $this->mFmlPath . 'fml.pl ';
        $text .=  $this->mBasePath . $this->mMlName . '" ' . "\n";
        $text .= 'cc "| ' . $this->mMlPath . '/mhonarc.sh"' . "\n";
        $text .= 'exit'."\n";


        //対象テキストを削除する
        $text = str_replace( $text, '', $nowAlias );

        $this->Update( $path, $text );

        //------------------------------
        // メーリングリストのコマンド用アドレスへの操作
        //------------------------------
        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '-ctl@' . $this->mDomain . '/.alias';

        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //テキストの置換対象
        $text  = '';
        $text .= 'cc "| ' . $this->mFmlPath . 'fml.pl ';
        $text .= $this->mBasePath . $this->mMlName . ' --ctladdr"' . "\n";

        //対象テキストを削除する
        $text = str_replace( $text, '', $nowAlias );

        $this->Update( $path, $text );

    }

    //==================================
    // -admin@メールの転送設定
    // $strMailaddress ：転送先アドレス
    // 返り値           : なし
    //==================================
    function SetAdminMailHook($strMailaddress)
    {
        //------------------------------
        // メーリングリストのAdminアドレスへの操作
        //------------------------------
        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '-admin@' . $this->mDomain . '/.alias';
        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //テキストの書き込み内容
        $text  = '';
        $text .= 'cc "!' . $strMailaddress .'"' . "\n";

        //.alias に記載がない場合は、末尾に追記
        if( strpos( $nowAlias, $text ) === false ){
            $text = $nowAlias . "\n" . $text;
            $this->Update( $path, $text );
        }
    }

    //==================================
    // -admin@メールの転送設定の削除
    // 返り値          : なし
    //==================================
    function DeleteAdminMailHook($strMailaddress)
    {
        //------------------------------
        // メーリングリストのAdminアドレスへの操作
        //------------------------------
        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '-admin@' . $this->mDomain . '/.alias';
        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //テキストの置換対象
        $text  = '';
        $text .= 'cc "!' . $strMailaddress .'"' . "\n";

        //対象テキストを削除する
        $text = str_replace( $text, '', $nowAlias );
        $this->Update( $path, $text );

    }


    //==================================
    // -admin@メールの転送設定の確認
    // $strMailaddress ：転送先アドレス
    // 返り値           : 1= 受信しない、2=受信する
    //==================================
    function CheckAdminMailHook( $strMailaddress )
    {
        //------------------------------
        // メーリングリストのAdminアドレスへの操作
        //------------------------------
        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '-admin@' . $this->mDomain . '/.alias';
        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //テキストの書き込み内容
        $text  = '';
        $text .= 'cc "!' . $strMailaddress .'"' . "\n";

        //.alias に記載がない場合は、末尾に追記
        if( strpos( $nowAlias, $text ) === false ){
            return '1';
        }
        return '2';
    }

    //==================================
    // -admin@メールの転送設定アドレス取得
    // 返り値           : 配列で取得
    //==================================
    function GetAdminMailHook()
    {
        $result = array();

        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '-admin@' . $this->mDomain . '/.alias';
        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        $nowAlias = trim($nowAlias);
        $nowAlias = str_replace(array("cc \"!", "\""), "", $nowAlias);
        $result   = array_merge($result, explode("\n", $nowAlias));

        foreach ($result as $key => $val) {
            if ( !CCommon::IsMailAddress($val) ) {
                unset($result[$key]);
            }
        }

        return $result;
    }


//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//======================================
//  ユーザー設定系
//======================================
    //==================================
    // 管理者の一覧を取得する
    // 返り値    : メール受信者の一覧を返す
    //==================================
    function GetAdminUserList(){
        if( $this->mContents[CCtrlFml::eFILE_ADMIN] == '' ){
            $this->mContents[CCtrlFml::eFILE_ADMIN] = $this->Load( $this->mPath[CCtrlFml::eFILE_ADMIN] );
        }
        $list = explode( "\n", $this->mContents[CCtrlFml::eFILE_ADMIN] );
        return $list;
    }

    //==================================
    // 管理者の確認
    // $strMailaddress : 確認するメールアドレス
    // 返り値          : 確認結果
    //==================================
    function CheckAdminUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_ADMIN] == '' ){
            $this->mContents[CCtrlFml::eFILE_ADMIN] = $this->Load( $this->mPath[CCtrlFml::eFILE_ADMIN] );
        }
        return $this->CheckStringContent( $this->mContents[CCtrlFml::eFILE_ADMIN], $strMailaddress );
    }

    //==================================
    // 管理者の追加
    // $strMailaddress : 追加するメールアドレス
    // 返り値          : なし
    //==================================
    function AddAdminUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_ADMIN] == '' ){
            $this->mContents[CCtrlFml::eFILE_ADMIN] = $this->Load( $this->mPath[CCtrlFml::eFILE_ADMIN] );
        }
        $this->mContents[CCtrlFml::eFILE_ADMIN] = $this->AddStringContent( $this->mContents[CCtrlFml::eFILE_ADMIN], $strMailaddress, true, false, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_ADMIN], $this->mContents[CCtrlFml::eFILE_ADMIN] );
    }

    //==================================
    // 管理者の削除
    // $strMailaddress : 削除するメールアドレス
    // 返り値          : なし
    //==================================
    function DeleteAdminUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_ADMIN] == '' ){
            $this->mContents[CCtrlFml::eFILE_ADMIN] = $this->Load( $this->mPath[CCtrlFml::eFILE_ADMIN] );
        }
        $this->mContents[CCtrlFml::eFILE_ADMIN] = $this->DeleteStringContent( $this->mContents[CCtrlFml::eFILE_ADMIN], $strMailaddress, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_ADMIN], $this->mContents[CCtrlFml::eFILE_ADMIN] );
    }


    //==================================
    // 投稿権限の保持者の一覧を取得する
    // 返り値    : メール受信者の一覧を返す
    //==================================
    function GetMemberUser(){
        if( $this->mContents[CCtrlFml::eFILE_MEMBER] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBER] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBER] );
        }
        $list = explode( "\n", $this->mContents[CCtrlFml::eFILE_MEMBER] );
        return $list;
    }

    //==================================
    // 投稿権限の保持者の確認
    // $strMailaddress : 確認するメールアドレス
    // 返り値          : 確認結果
    //==================================
    function CheckMemberUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_MEMBER] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBER] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBER] );
        }
        return $this->CheckStringContent( $this->mContents[CCtrlFml::eFILE_MEMBER], $strMailaddress );
    }

    //==================================
    // 投稿権限の保持者の追加
    // $strMailaddress : 追加するメールアドレス
    // 返り値          : なし
    //==================================
    function AddMemberUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_MEMBER] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBER] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBER] );
        }
        $this->mContents[CCtrlFml::eFILE_MEMBER] = $this->AddStringContent( $this->mContents[CCtrlFml::eFILE_MEMBER], $strMailaddress, true, false, true );

        $this->Update( $this->mPath[CCtrlFml::eFILE_MEMBER], $this->mContents[CCtrlFml::eFILE_MEMBER] );
    }

    //==================================
    // 投稿権限の保持者の削除
    // $strMailaddress : 削除するメールアドレス
    // 返り値          : なし
    //==================================
    function DeleteMemberUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_MEMBER] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBER] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBER] );
        }
        $this->mContents[CCtrlFml::eFILE_MEMBER] = $this->DeleteStringContent( $this->mContents[CCtrlFml::eFILE_MEMBER], $strMailaddress, true, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MEMBER], $this->mContents[CCtrlFml::eFILE_MEMBER] );
    }

    //==================================
    // 投稿権限の保持者の削除
    // $strMailaddress : 削除するメールアドレス
    // 返り値          : なし
    //==================================
    function DeleteCompleteMemberUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_MEMBER] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBER] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBER] );
        }
        $this->mContents[CCtrlFml::eFILE_MEMBER] = $this->DeleteComplete( $this->mContents[CCtrlFml::eFILE_MEMBER], $strMailaddress, true, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MEMBER], $this->mContents[CCtrlFml::eFILE_MEMBER] );
    }

    //==================================
    // メール受信者の一覧を取得する
    // 返り値    : メール受信者の一覧を返す
    //==================================
    function GetActiveUser(){
        if( $this->mContents[CCtrlFml::eFILE_ACTIVE] == '' ){
            $this->mContents[CCtrlFml::eFILE_ACTIVE] = $this->Load( $this->mPath[CCtrlFml::eFILE_ACTIVE] );
        }
        $list = explode( "\n", $this->mContents[CCtrlFml::eFILE_ACTIVE] );
        return $list;
    }

    //==================================
    // メール受信者の確認
    // $strMailaddress : 確認するメールアドレス
    // 返り値          : 確認結果
    //==================================
    function CheckActiveUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_ACTIVE] == '' ){
            $this->mContents[CCtrlFml::eFILE_ACTIVE] = $this->Load( $this->mPath[CCtrlFml::eFILE_ACTIVE] );
        }
        return $this->CheckStringContent( $this->mContents[CCtrlFml::eFILE_ACTIVE], $strMailaddress );
    }

    //==================================
    // メール受信者の追加
    // $strMailaddress : 追加するメールアドレス
    // 返り値          : なし
    //==================================
    function AddActiveUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_ACTIVE] == '' ){
            $this->mContents[CCtrlFml::eFILE_ACTIVE] = $this->Load( $this->mPath[CCtrlFml::eFILE_ACTIVE] );
        }
        $this->mContents[CCtrlFml::eFILE_ACTIVE] = $this->AddStringContent( $this->mContents[CCtrlFml::eFILE_ACTIVE], $strMailaddress, true, false, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_ACTIVE], $this->mContents[CCtrlFml::eFILE_ACTIVE] );
    }

    //==================================
    // メール受信者をコメントアウト
    // $strMailaddress : 削除するメールアドレス
    // 返り値          : なし
    //==================================
    function DeleteActiveUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_ACTIVE] == '' ){
            $this->mContents[CCtrlFml::eFILE_ACTIVE] = $this->Load( $this->mPath[CCtrlFml::eFILE_ACTIVE] );
        }
        $this->mContents[CCtrlFml::eFILE_ACTIVE] = $this->DeleteStringContent( $this->mContents[CCtrlFml::eFILE_ACTIVE], $strMailaddress, true, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_ACTIVE], $this->mContents[CCtrlFml::eFILE_ACTIVE] );
    }

    //==================================
    // メール受信者の完全削除
    // $strMailaddress : 削除するメールアドレス
    // 返り値          : なし
    //==================================
    function DeleteCompleteActiveUser( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_ACTIVE] == '' ){
            $this->mContents[CCtrlFml::eFILE_ACTIVE] = $this->Load( $this->mPath[CCtrlFml::eFILE_ACTIVE] );
        }
        $this->mContents[CCtrlFml::eFILE_ACTIVE] = $this->DeleteComplete( $this->mContents[CCtrlFml::eFILE_ACTIVE], $strMailaddress, true, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_ACTIVE], $this->mContents[CCtrlFml::eFILE_ACTIVE] );
    }


    //==================================
    // member-admin(管理者)の一覧を取得する
    // 返り値    : 管理者の一覧を返す
    //==================================
    function GetMemberAdmin(){
        if( $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBERSADMIN] );
        }
        $list = explode( "\n", $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] );
        return $list;
    }

    //==================================
    // member-admin(管理者/配信者)の確認
    // $strMailaddress : 確認するメールアドレス
    // 返り値          : 確認結果
    //==================================
    function CheckMemberAdmin( $strMailaddress ){
        $list   = $this->GetMemberAdmin();
        return array_search($strMailaddress, $list) !== FALSE;
    }

    //==================================
    // member-admin(管理者)の追加
    // $strMadmin   : 追加する管理者
    // 返り値       : なし
    //==================================
    function AddMembersAdmin( $strMadmin ){
        if( $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBERSADMIN] );
        }
        $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] = $this->AddStringContent( $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN], $strMadmin, true, false, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MEMBERSADMIN], $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] );
    }

    //==================================
    // member-admin(管理者)の上書き
    // $strMadmin   : 上書きする管理者
    // 返り値       : なし
    //==================================
    function UpdateMembersAdmin( $strMadmin ){
        $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] = $this->AddStringContent( $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN], $strMadmin, true, false, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MEMBERSADMIN], $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] );
    }

    //==================================
    // member-admin(管理者)の削除
    // $strMadmin   : 削除する管理者
    // 返り値       : なし
    //==================================
    function DeleteMembersAdmin( $strMadmin ){
        if( $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBERSADMIN] );
        }
        $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] = $this->DeleteStringContent( $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN], $strMadmin, true, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MEMBERSADMIN], $this->mContents[CCtrlFml::eFILE_MEMBERSADMIN] );
    }

    //==================================
    // moderatorの確認
    // $strMailaddress : 確認するメールアドレス
    // 返り値          : 確認結果
    //==================================
    function CheckModerators( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_MODERATORS] == '' ){
            $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->Load( $this->mPath[CCtrlFml::eFILE_MODERATORS] );
        }
        return $this->CheckStringContent( $this->mContents[CCtrlFml::eFILE_MODERATORS], $strMailaddress );
    }

    //==================================
    // 承認権限(管理者)の一覧を取得する
    // 返り値    : 承認権限(管理者)の一覧を返す
    //==================================
    function GetModerators(){
        if( $this->mContents[CCtrlFml::eFILE_MODERATORS] == '' ){
            $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->Load( $this->mPath[CCtrlFml::eFILE_MODERATORS] );
        }
        $list = explode( "\n", $this->mContents[CCtrlFml::eFILE_MODERATORS] );
        return $list;
    }

    //==================================
    // 承認権限(管理者)のうち、１番目の管理者を取得する
    // 返り値    : １番目の承認権限(管理者)を返す
    //==================================
    function GetAdminModerators(){
        $moderator = $this->GetModerators();
        if(count($moderator) > 0){
            return $moderator[0];
        }else{
            return NULL;
        }
    }

    //==================================
    // 承認権限(管理者)の追加
    // $strModerator   : 追加する承認権限(管理者)
    // 返り値          : なし
    //==================================
    function AddModerators( $strModerator ){
        if( $this->mContents[CCtrlFml::eFILE_MODERATORS] == '' ){
            $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->Load( $this->mPath[CCtrlFml::eFILE_MODERATORS] );
        }
        $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->AddStringContent( $this->mContents[CCtrlFml::eFILE_MODERATORS], $strModerator, true, false, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MODERATORS], $this->mContents[CCtrlFml::eFILE_MODERATORS] );
    }

    //==================================
    // 承認権限(管理者)の上書き
    // $strModerator   : 上書きする承認権限(管理者)
    // 返り値          : なし
    //==================================
    function UpdateModerators( $strModerator ){
        $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->AddStringContent( $this->mContents[CCtrlFml::eFILE_MODERATORS], $strModerator, true, false, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MODERATORS], $this->mContents[CCtrlFml::eFILE_MODERATORS] );
    }

    //==================================
    // 承認権限(管理者)の削除
    // $strModerator   : 削除する承認権限(管理者)
    // 返り値          : なし
    //==================================
    function DeleteModerators( $strModerator ){
        if( $this->mContents[CCtrlFml::eFILE_MODERATORS] == '' ){
            $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->Load( $this->mPath[CCtrlFml::eFILE_MODERATORS] );
        }
        $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->DeleteStringContent( $this->mContents[CCtrlFml::eFILE_MODERATORS], $strModerator, true, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MODERATORS], $this->mContents[CCtrlFml::eFILE_MODERATORS] );
    }


    //==================================
    // メール受信者の完全削除
    // $strMailaddress : 削除するメールアドレス
    // 返り値          : なし
    //==================================
    function DeleteCompleteModerators( $strMailaddress ){
        if( $this->mContents[CCtrlFml::eFILE_MODERATORS] == '' ){
            $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->Load( $this->mPath[CCtrlFml::eFILE_MODERATORS] );
        }
        $this->mContents[CCtrlFml::eFILE_MODERATORS] = $this->DeleteComplete( $this->mContents[CCtrlFml::eFILE_MODERATORS], $strMailaddress, true, true );
        $this->Update( $this->mPath[CCtrlFml::eFILE_MODERATORS], $this->mContents[CCtrlFml::eFILE_MODERATORS] );
    }


//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
    //==================================
    // ファイルを全て読み込みます
    // 返り値          : なし
    //==================================
    function LoadAllFile(){
        //ファイルへのパスが設定されているファイルを全て読み込みます
        foreach( $this->mPath as $key => $value ){
            $this->mContents[$key] = $this->Load( $value );
        }
    }

    //==================================
    // ファイルを読み込みます
    // $strType        : 読み込むファイルのタイプを選択します
    // 返り値          : ファイル読み込みの成否
    //==================================
    function LoadFile( $strType ){
        if( ! isset( $this->mPath[$strType] ) )    return false;
        $this->mContents[$strType] = $this->Load( $this->mPath[$strType] );
        return true;
    }

    //==================================
    // $srtContent 内に $strCheckText の有効な記載があるかどうか
    // $srtContent     : テキスト
    // $strCheckText   : 確認するテキスト
    // 返り値          : 判定結果
    //==================================
    function CheckStringContent( $srtContent, $strCheckText ){
        $list = explode( "\n", $srtContent );
        // 既にテキストが存在するかの確認
        foreach( $list as $key => $value ){
            $value = trim( $value );

            if( $value == "" )    continue;
            if( $value === $strCheckText ){
                return true;
            }
        }

        return false;
    }


    //==================================
    // $srtContent にテキストの追記を行うかどうか
    // $srtContent     : テキスト
    // $strAddText     : 追加を行うテキスト
    // $isSpaceRemove  : 空行の削除
    // $isDoubleOK     : $strAddText が既に存在する場合に、再度に追記を行うか
    // $isCommentOut   : コメントアウトされている場合の処理（コメントアウトの解除：ture）
    // 返り値          : 処理後のテキスト
    //==================================
    function AddStringContent( $srtContent, $strAddText, $isSpaceRemove=false, $isDoubleOK=false, $isCommentOut=false ){
        $list = explode( "\n", $srtContent );
        $text = '';
        $flag = true;

        // 既にテキストが存在するかの確認
        if( ! $isDoubleOK ){
            foreach( $list as $key => $value ){
                $value = trim( $value );

                if( $value == "" )    continue;
                if( $value === $strAddText ){
                    $flag = false;
                    break;
                }

                //コメントアウトを解除する
                if( $isCommentOut ){
                    if( mb_strpos( $value, '#', 0, 'JIS' ) === 0 ){
                        $arrCode = explode( ' ', $value );
                        $nCode   = count( $arrCode );
                        if( $nCode < 2 ){continue;}
                        if( $arrCode[($nCode-1)] !== $strAddText ){continue;}
                        if( $flag ){
                            $list[$key] = $arrCode[($nCode-1)];
                            $flag = false;
                        }else{
                            $list[$key] = '';
                        }
                    }
                }
            }
        }

        //テキストのコピー作成
        foreach( $list as $value ){
            if( $isSpaceRemove && $value == "" )    continue;
            $text .= $value . "\n";
        }

        //テキストの追記
        if( $flag ){
            $text .= $strAddText . "\n";
        }

        return $text;
    }

    //==================================
    // $srtContent にてテキストのコメントアウトを行う
    // $srtContent     : 削除するメールアドレス
    // $strAddText     : 追加を行うテキスト
    // 返り値          : 処理後のテキスト
    //==================================
    function DeleteStringContent( $srtContent, $strDeleteText, $isSpaceRemove=false, $isCommentOut=false ){
        $list = explode( "\n", $srtContent );
        $text = '';

        //テキストの作成
        foreach( $list as $value ){
            $value = trim( $value );

            if( $isSpaceRemove && $value == "" )    continue;

            if( $value === $strDeleteText ){
                if( $isCommentOut ){
                    $text .= '###ComentOut ' . $value . "\n";
                }
            }elseif( $value !== $strDeleteText ){
                $text .= $value . "\n";
            }
        }

        return $text;
    }


    //==================================
    // $srtContent の完全削除を行う
    // $srtContent     : 削除するメールアドレス
    // $strAddText     : 追加を行うテキスト
    // 返り値          : 処理後のテキスト
    //==================================
    function DeleteComplete( $srtContent, $strDeleteText, $isSpaceRemove=false, $isCommentOut=false ){
        $list = explode( "\n", $srtContent );
        $text = '';

        //テキストの作成
        foreach( $list as $value ){
            $value = trim( $value );

            if( $isSpaceRemove && $value == "" )    continue;

            if( $value === $strDeleteText ){
                if( $isCommentOut ){
                    //$text .= '###ComentOut ' . $value . "\n";
                }
            }elseif( $value !== $strDeleteText ){
                $text .= $value . "\n";
            }
        }

        return $text;
    }



//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//======================================
//  ファイル操作系
//======================================
    //テキストデータの取得
    function GetTextFileGuide(){        return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_GUIDE ),     'UTF-8', 'JIS' );  }   // 「メーリングリストの紹介」の取得
    function GetTextFileDeny(){         return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_DENY ),      'UTF-8', 'JIS' );  }   // 「メンバー以外から投稿を行った場合の返信メッセージ」の取得
    function GetTextFileObjective(){    return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_OBJECTIVE ), 'UTF-8', 'JIS' );  }   // 「メーリングリストの目的」の取得
    function GetTextFileHelp(){         return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_HELP ),      'UTF-8', 'JIS' );  }   // 「メンバー用のヘルプ」の取得
    function GetTextFileHelpAdmin(){    return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_HELPADMIN ), 'UTF-8', 'JIS' );  }   // 「管理者用のヘルプ」の取得
    function GetTextFileConfirm(){      return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_CONFIRM ),   'UTF-8', 'JIS' );  }   // 「subscribe 実行の際の確認用のメッセージ」の取得

    function GetTextFileMailHeader(){   return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_MAILHEADER ), 'UTF-8', 'JIS' );  }  // メール本文のヘッダー
    function GetTextFileMailFooter(){   return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_MAILFOOTER ), 'UTF-8', 'JIS' );  }  // メール本文のフッダー
    function GetTextFileMgHeader(){   return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_MGHEADER ), 'UTF-8', 'JIS' );    }  // メルマガ用本文のヘッダー
    function GetTextFileMgFooter(){   return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_MGFOOTER ), 'UTF-8', 'JIS' );    }  // メルマガ用本文のフッダー


    function GetTextFileAdmission(){    return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_ADMISSION ), 'UTF-8', 'JIS' );  }   // 入会の意思確認用メール
    function GetTextFileWithdraw(){     return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_WITHDRAW ), 'UTF-8', 'JIS' );   }   // 退会の意思確認用メール
    function GetTextFileWelcome(){      return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_WELCOME ), 'UTF-8', 'JIS' );    }   // 入会完了用メール  入会者
    function GetTextFileGoodbye(){      return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_GOODBYE ), 'UTF-8', 'JIS' );    }   // 退会完了用メール  退会者
    function GetTextFileAdNotice(){      return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_ADMISSION_NOTICE ), 'UTF-8', 'JIS' );    }   // 入会完了用メール  管理者
    function GetTextFileWithNotice(){      return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_WITHDRAW_NOTICE ), 'UTF-8', 'JIS' );    }   // 退会完了用メール  管理者
    //function GetTextFileReissue(){      return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_REISSUE ),  'UTF-8', 'JIS' );   }   // パスワード再発行用メール
    function GetTextFileWithAutoNotice(){      return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_WITHDRAW_AUTO_NOTICE ), 'UTF-8', 'JIS' );    }   // 自動削除実行メール  管理者
    function GetTextFileApplyError(){      return  mb_convert_encoding( $this->Load( $this->mMlPath . CCtrlFml::eTXT_FILE_APPLY_ERROR ), 'UTF-8', 'JIS' );    }   // 自動入会で重複申し込みアドレスへの通知

    //テキストデータの出力
    function SetTextFileGuide( $strContent ){       $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_GUIDE,    mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 「メーリングリストの紹介」の出力
    function SetTextFileDeny( $strContent ){        $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_DENY,     mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 「メンバー以外から投稿を行った場合の返信メッセージ」の出力
    function SetTextFileObjective( $strContent ){   $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_OBJECTIVE,mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 「メーリングリストの目的」の出力
    function SetTextFileHelp( $strContent ){        $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_HELP,     mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 「メンバー用のヘルプ」の出力
    function SetTextFileHelpAdmin( $strContent ){   $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_HELPADMIN,mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 「管理者用のヘルプ」の出力
    function SetTextFileConfirm( $strContent ){     $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_CONFIRM,  mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 「subscribe 実行の際の確認用のメッセージ」の出力

    function SetTextFileMailHeader( $strContent ){  $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_MAILHEADER,mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // メール本文のヘッダー
    function SetTextFileMailFooter( $strContent ){  $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_MAILFOOTER,mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // メール本文のフッダー
    function SetTextFileMgHeader( $strContent ){  $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_MGHEADER,mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );    }  // メルマガ本文のヘッダー
    function SetTextFileMgFooter( $strContent ){  $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_MGFOOTER,mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );    }  // メルマガ本文のフッダー


    function SetTextFileAdmission( $strContent ){   $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_ADMISSION, mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 入会の意思確認用メール
    function SetTextFileWithdraw( $strContent ){    $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_WITHDRAW,  mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 退会の意思確認用メール
    function SetTextFileWelcome( $strContent ){     $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_WELCOME,   mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 入会完了時のメール  入会者
    function SetTextFileGoodbye( $strContent ){     $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_GOODBYE,   mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 退会完了時のメール  退会者
    function SetTextFileAdNotice($strContent ){     $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_ADMISSION_NOTICE,   mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 入会完了時のメール  管理者
    function SetTextFileWithNotice($strContent ){   $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_WITHDRAW_NOTICE,   mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // 退会完了時のメール  管理者
    //function SetTextFileReissu( $strContent ){      $this->Update( $this->mMlPath . CCtrlFml::eTXT_FILE_REISSUE,   mb_convert_encoding( $strContent, 'JIS', 'UTF-8' ) );  }  // パスワード再発行用メール


//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//======================================
//  オプション操作系
//======================================

    //==================================
    // メールの題名の形式を設定します
    // $nTileForm      : 設定形式を設定します
    // 返り値          : なし
    //==================================
    function SetTitleForm( $nTileForm ){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $form = '';
        switch( $nTileForm ){
            case CCtrlFml::eTITLEFORM_SBRACKET_COLON:   $form = '[:]';      break;  //表示形式  [Name:ID]
            case CCtrlFml::eTITLEFORM_RBRACKET_COLON:   $form = '(:)';      break;  //表示形式  (Name:ID)
            case CCtrlFml::eTITLEFORM_SBRACKET_COMMA:   $form = '[,]';      break;  //表示形式  [Name,ID]
            case CCtrlFml::eTITLEFORM_RBRACKET_COMMA:   $form = '(,)';      break;  //表示形式  (Name,ID)
            case CCtrlFml::eTITLEFORM_SBRACKET_ID:      $form = '[ID]';     break;  //表示形式  [ID]
            case CCtrlFml::eTITLEFORM_RBRACKET_ID:      $form = '(ID)';     break;  //表示形式  (ID)
            case CCtrlFml::eTITLEFORM_SBRACKET_NAME:    $form = '[]';       break;  //表示形式  [Name]
            case CCtrlFml::eTITLEFORM_RBRACKET_NAME:    $form = '()';       break;  //表示形式  (Name)
            case CCtrlFml::eTITLEFORM_BRACKET_NONE:     $form = '';         break;  //表示なし
        }
        //設定変更後のテキストを設定する
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$SUBJECT_TAG_TYPE', $form );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // メールの題名の形式を取得します
    // 返り値          : タイトルフォームのタイプを返します
    //==================================
    function GetTitleForm(){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $form = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$SUBJECT_TAG_TYPE' );
        switch( $form ){
            case '[:]':    return CCtrlFml::eTITLEFORM_SBRACKET_COLON;     //表示形式  [Name:ID]
            case '(:)':    return CCtrlFml::eTITLEFORM_RBRACKET_COLON;     //表示形式  (Name:ID)
            case '[,]':    return CCtrlFml::eTITLEFORM_SBRACKET_COMMA;     //表示形式  [Name,ID]
            case '(,)':    return CCtrlFml::eTITLEFORM_RBRACKET_COMMA;     //表示形式  (Name,ID)
            case '[ID]':   return CCtrlFml::eTITLEFORM_SBRACKET_ID;        //表示形式  [ID]
            case '(ID)':   return CCtrlFml::eTITLEFORM_RBRACKET_ID;        //表示形式  (ID)
            case '[]':     return CCtrlFml::eTITLEFORM_SBRACKET_NAME;      //表示形式  [Name]
            case '()':     return CCtrlFml::eTITLEFORM_RBRACKET_NAME;      //表示形式  (Name)
        }
        return '';
    }

    //==================================
    // メールの題名を設定します
    // $strTileName    : タイトル名の設定
    // 返り値          : なし
    //==================================
    function SetTitleName( $strTileName ){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        $strTileName = mb_encode_mimeheader($strTileName, "iso-2022-jp", "B");
        $strTileName = str_replace(array("\r\n", "\n", "\r", " "), "", $strTileName);


        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$BRACKET', $strTileName );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // メールの題名を取得します
    // 返り値          : タイトル名
    //==================================
    function GetTitleName(){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $strTileName = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$BRACKET' );

        mb_language('ja');
        mb_internal_encoding('UTF-8');
        $strTileName = mb_decode_mimeheader( $strTileName );

        return $strTileName;
    }

    //==================================
    // 投稿者の制限設定を行います
    // $nLimitMode     : 制限を行うタイプを設定します
    // 返り値          : なし
    //==================================
    function SetSendLimit( $nLimitMode ){
        $type = 'members_only';
        switch( $nLimitMode ){
            case CCtrlFml::ePERMIT_ANYONE:          $type = 'anyone';       break;  //誰でもOK
            case CCtrlFml::ePERMIT_MENBERS_ONLY:    $type = 'members_only'; break;  //メンバーのみ可能
            case CCtrlFml::ePERMIT_MODERATOR:       $type = 'moderator';    break;  //管理者の容認者
        }

        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$PERMIT_POST_FROM', $type );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }


    //==================================
    // メーリングリストの差出人の設定を行います。
    // 返り値          : なし
    //==================================
    function SetMLSender( $param, $isNum ){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionHook( $this->mContents[CCtrlFml::eFILE_CONFIG], '$SET_SENDER_NAME', $param, $isNum );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }


    //==================================
    // メーリングリストの差出人の設定を取得します。
    // 返り値          : 差出人の設定
    //==================================
    function GetMLSender(){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $ret = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$SET_SENDER_NAME' );

        return $ret;
    }


    //==================================
    // メールの題名を設定します
    // $strTileName    : タイトル名の設定
    // 返り値          : なし
    //==================================
    function SetSenderName( $strSenderName ){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        $strSenderName = mb_encode_mimeheader($strSenderName, "iso-2022-jp", "B", "\n");
        $strSenderName = str_replace(array("\r\n", "\n", "\r", " "), "", $strSenderName);
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$SENDER_NAME', $strSenderName );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // メールの題名を取得します
    // 返り値          : タイトル名
    //==================================
    function GetSenderName(){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $strSenderName = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$SENDER_NAME' );

        mb_language('ja');
        mb_internal_encoding('UTF-8');
        $strSenderName = mb_decode_mimeheader( $strSenderName );

        return $strSenderName;
    }


    //==================================
    // メルマガの返信先の設定を行います。
    // 返り値          : 返信先設定アドレス
    // 返り値          : なし
    //==================================
    function SetMmReplyAddress( $address, $isNum ){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionHook( $this->mContents[CCtrlFml::eFILE_CONFIG], '$REPLY_ADDRESS', $address, $isNum );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }


    //==================================
    // メルマガの返信先を取得します。
    // 返り値          : 返信先設定アドレス
    //==================================
    function GetMmReplyAddress(){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $address = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$REPLY_ADDRESS' );

        return $address;
    }

    //==================================
    // 投稿者の制限設定を取得します
    // 返り値          : 投稿者の制限値
    //==================================
    function GetSendLimit(){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $type = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$PERMIT_POST_FROM' );

        $limit_mode = CCtrlFml::ePERMIT_MENBERS_ONLY;
        switch( $type ){
            case 'anyone':       $limit_mode = CCtrlFml::ePERMIT_ANYONE;        break;  //誰でもOK
            case 'members_only': $limit_mode = CCtrlFml::ePERMIT_MENBERS_ONLY;  break;  //メンバーのみ可能
            case 'moderator':    $limit_mode = CCtrlFml::ePERMIT_MODERATOR;     break;  //管理者の容認者
        }
        return $limit_mode;
    }

    //==================================
    // メールの返信先設定を取得します
    // 返り値          : 返信設定
    //==================================
    function GetMailReply(){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        //configから該当行を取得
        $arrList = explode( "\n", $this->mContents[CCtrlFml::eFILE_CONFIG]);
        $strKey = '&DEFINE_FIELD_FORCED';
        $reply_mode = 1;        //default設定：メンバー全員へ返信
        foreach( $arrList as $line ){
            if(strstr( $line, $strKey )){
                //コメントアウトされている場合は無視
                $text = trim( $line );
                if( strpos( $text, '#' ) === 0 ){
                    continue;
                }else{
                    $getKey = trim( $line );
                    break;
                }
            }
        }

        //設定を取得
        if (strstr($getKey,'$From_address')){
            $reply_mode = 2;
        }elseif ( strstr($getKey,'$MAIL_LIST')){
            $reply_mode = 1;
        }else{
            $reply_mode = 1;
        }
        return $reply_mode;
    }

    //==================================
    // メルマガの返信先の設定を行います。
    // 返り値          : 返信先設定アドレス
    // 返り値          : なし
    //==================================
    function SetFileTemp( $strValue ){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetAddContent( $this->mContents[CCtrlFml::eFILE_CONFIG], $strValue);
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // ファイルの添付設定を取得します。
    // 返り値          : 1＝許可しない、2＝許可する、3＝設定なし
    //==================================
    function GetFileTemp(){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $iTemp = $this->GetOptionHandler( $this->mContents[CCtrlFml::eFILE_CONFIG], '&ADD_CONTENT_HANDLER' );

        return $iTemp;
    }

    //==================================
    // HTMLメール設定、ファイルの添付設定を行います。
    // $valHtml    : HTMLメール設定（1:許可しない or 2:許可する）
    // $valFile    : ファイル添付設定（1:許可しない or 2:許可する）
    // 返り値          : なし
    // file|html|val
    //   0 | 0  | 1
    //   0 | 1  | 2
    //   1 | 0  | 3
    //   1 | 1  | 4
    //=================================
    function SetContentType( $valHtml, $valFile ) {
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        $val    = 1;
        $val    += ($valHtml > 1) ? 1 : 0;
        $val    += ($valFile > 1) ? 2 : 0;

        $pattern    = "/MAIL_CONTENT_TYPE\s*=\s*\d;/";
        $subject    = "MAIL_CONTENT_TYPE    = $val;";

        $this->mContents[CCtrlFml::eFILE_CONFIG]    = preg_replace($pattern, $subject, $this->mContents[CCtrlFml::eFILE_CONFIG]);
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // HTMLメール設定、ファイルの添付設定を取得します。
    // 返り値          : array(HTML設定値, ファイル添付設定値)
    //==================================
    function GetContentType(){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        $valHtml    = 1;
        $valFile    = 1;

        // 1＝プレーンテキストのみ
        // 2＝プレーンテキスト＋HTMLメール
        // 3＝プレーンテキスト＋ファイル添付（デフォルト）
        // 4＝すべて許可
        $val    = 3;

        preg_match("/MAIL_CONTENT_TYPE\s*=\s*\d;/", $this->mContents[CCtrlFml::eFILE_CONFIG], $matches);

        if ( count($matches) > 0 ) {
           $val    = substr($matches[0], -2, 1);
        }

        $valHtml    = ( $val % 2 == 0 ) ? 2 : 1;
        $valFile    = ( $val >= 3 ) ? 2 : 1;

        return array($valHtml, $valFile);
        #return $valHtml;
    }

    //==================================
    // ファイルの添付可否設定
    // $strContents    : オプションのテキスト
    // $strValue       : 設定する値
    // 返り値          : 設定後のテキスト
    //==================================
    function SetAddContent( $strContents, $strValue){
        $arrList     = explode( "\n", $strContents );
        $newContents = '';
        $editFlg = 0;

        foreach( $arrList as $row => $line ){
            $line = trim($line);
            if( strpos( $line, '&ADD_CONTENT_HANDLER' ) === 0 || strpos( $line, '#&ADD_CONTENT_HANDLER' ) === 0){
                if ($strValue == 1){
                    //ファイル添付許可しない場合、コメント解除
                    $newContents .= "&ADD_CONTENT_HANDLER('multipart/mixed', '.*/.*',     'reject');" . "\n";
                }else{
                    //ファイル添付を許可する場合、コメント設定
                    $newContents .= "#&ADD_CONTENT_HANDLER('multipart/mixed', '.*/.*',     'reject');" . "\n";
                }
            }else{
                //変更なしの行
                $newContents .= $line . "\n";
            }
        }

        return $newContents;
    }

    //==================================
    // メールコマンドの制限設定を行います
    // $nLimitMode     : 制限を行うタイプを設定します
    // 返り値          : なし
    //==================================
    function SetCommandLimit( $nLimitMode ){
        $type = 'members_only';
        switch( $nLimitMode ){
            case CCtrlFml::ePERMIT_ANYONE:            $type = 'anyone';        break;    //誰でもOK
            case CCtrlFml::ePERMIT_MENBERS_ONLY:    $type = 'members_only';    break;    //メンバーのみ可能
            case CCtrlFml::ePERMIT_MODERATOR:        $type = 'moderator';    break;    //管理者の容認者
        }

        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$PERMIT_COMMAND_FROM', $type );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // メールコマンドの制限設定を取得します
    // 返り値          : 制限を行うタイプ
    //==================================
    function GetCommandLimit(){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $type = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$PERMIT_COMMAND_FROM' );

        $limit_mode = CCtrlFml::ePERMIT_MENBERS_ONLY;
        switch( $type ){
            case 'anyone':       $limit_mode = CCtrlFml::ePERMIT_ANYONE;        break;  //誰でもOK
            case 'members_only': $limit_mode = CCtrlFml::ePERMIT_MENBERS_ONLY;  break;  //メンバーのみ可能
            case 'moderator':    $limit_mode = CCtrlFml::ePERMIT_MODERATOR;     break;  //管理者の容認者
        }

        return $limit_mode;
    }

    //==================================
    // メールのサイズ制限設定を行います
    // $nLimitSize     : 制限を行うタイプを設定します
    // 返り値          : なし
    //==================================
    function SetMailSizeLimit( $nLimitSize ){
        $type = CCtrlFml::eMLSIZE_LIMIT_DEFAULUT ;
        switch( $nLimitSize ){
            case 1:            $type = CCtrlFml::eMLSIZE_LIMIT_500KB;        break;
            case 2:            $type = CCtrlFml::eMLSIZE_LIMIT_1MB;          break;
            case 3:            $type = CCtrlFml::eMLSIZE_LIMIT_3MB;          break;
            case 4:            $type = CCtrlFml::eMLSIZE_LIMIT_10MB;         break;
            case 5:            $type = CCtrlFml::eMLSIZE_LIMIT_15MB;         break;
            case 6:            $type = CCtrlFml::eMLSIZE_LIMIT_20MB;         break;
            case 0:            $type = CCtrlFml::eMLSIZE_LIMIT_DEFAULUT;     break;
        }

        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionHook( $this->mContents[CCtrlFml::eFILE_CONFIG], '$INCOMING_MAIL_SIZE_LIMIT', $type,0 );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // メールのサイズ制限設定を取得します
    // 返り値          : メールサイズの制限値
    //==================================
    function GetMailSizeLimit(){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $type = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$INCOMING_MAIL_SIZE_LIMIT' );

        $limit_mode = CCtrlFml::eMLSIZE_LIMIT_DEFAULUT;
        switch( $type ){
            case CCtrlFml::eMLSIZE_LIMIT_500KB:       $limit_mode = 1;        break;
            case CCtrlFml::eMLSIZE_LIMIT_1MB:         $limit_mode = 2;        break;
            case CCtrlFml::eMLSIZE_LIMIT_3MB:         $limit_mode = 3;        break;
            case CCtrlFml::eMLSIZE_LIMIT_10MB:        $limit_mode = 4;        break;
            case CCtrlFml::eMLSIZE_LIMIT_15MB:        $limit_mode = 5;        break;
            case CCtrlFml::eMLSIZE_LIMIT_20MB:        $limit_mode = 6;        break;
            case CCtrlFml::eMLSIZE_LIMIT_DEFAULUT:    $limit_mode = 0;        break;
        }
        return $limit_mode;
    }

    //==================================
    // ヘッダーのテキストの設定を行います
    // $strHeaderTxt   : 設定を行うテキスト情報
    // 返り値          : なし
    //==================================
    function SetHeaderTxt( $strHeaderTxt ){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $strHeaderTxt = mb_convert_encoding( $strHeaderTxt, 'JIS', 'UTF-8' );
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$PREAMBLE_MAILBODY', $strHeaderTxt );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // ヘッダーのテキストを取得します
    // 返り値          : ヘッダーのテキスト情報
    //==================================
    function GetHeaderTxt(){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $strHeaderTxt = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$PREAMBLE_MAILBODY' );
        $strHeaderTxt = mb_convert_encoding( $strHeaderTxt, 'UTF-8', 'JIS' );
        return $strHeaderTxt;
    }

    //==================================
    // フッターのテキストの設定を行います
    // $strFooterTxt   : 設定を行うテキスト情報
    // 返り値          : なし
    //==================================
    function SetFooterTxt( $strFooterTxt ){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $strFooterTxt = mb_convert_encoding( $strFooterTxt, 'JIS', 'UTF-8' );
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$TRAILER_MAILBODY', $strFooterTxt );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // フッターのテキストを取得します
    // 返り値          : フッターのテキスト情報
    //==================================
    function GetFooterTxt(){
        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $strFooterTxt = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '$TRAILER_MAILBODY' );
        $strFooterTxt = mb_convert_encoding( $strFooterTxt, 'UTF-8', 'JIS' );
        return $strFooterTxt;
    }


    //==================================
    // メールコマンド制限の設定
    // ※ 変数が true の場合は制限を行います
    // $isMember       : メンバーの一覧取得「member」「members」「active」「actives」
    // $isNews         : 過去記事の取得「get」「mget」「summary」
    // $isStatus       : 設定状況の取得「stat」「status」
    // 返り値          : なし
    //==================================
    function SetDenyCommandMail( $isMember, $isNews, $isStatus ){
        $txt    = '';
        if( $isMember ){
            if( $txt !== '' ){ $txt .= ', '; }
            $txt    .= "'member', 'members', 'active', 'actives'";
        }
        if( $isNews ){
            if( $txt !== '' ){ $txt .= ', '; }
            $txt    .= "'get', 'mget', 'summary'";
        }
        if( $isStatus ){
            if( $txt !== '' ){ $txt .= ', '; }
            $txt    .= "'stat', 'status'";
        }

        //設定変更後のテキストを設定する
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }
        $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->SetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '@DenyProcedure', '(' . $txt . ')' );
        $this->Update( $this->mPath[CCtrlFml::eFILE_CONFIG], $this->mContents[CCtrlFml::eFILE_CONFIG] );
    }

    //==================================
    // メールコマンド制限状況の取得（参加者の一覧取得）
    // 返り値          : 制限状況の取得
    //==================================
    function GetDenyCommandMail_Member(){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        //コマンド制限リストの取得
        $strList = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '@DenyProcedure' );

        $strList = mb_convert_encoding( $strList, 'UTF-8', 'JIS' );
        //制限コマンドが全て制限されているかの確認を行う
        if( strpos( $strList, "'member'" ) === false ){    return false;    }
        if( strpos( $strList, "'members'" ) === false ){   return false;    }
        if( strpos( $strList, "'active'" ) === false ){    return false;    }
        if( strpos( $strList, "'actives'" ) === false ){   return false;    }

        return true;
    }

    //==================================
    // メールコマンド制限状況の取得（過去記事の取得）
    // 返り値          : 制限状況の取得
    //==================================
    function GetDenyCommandMail_News(){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        //コマンド制限リストの取得
        $strList = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '@DenyProcedure' );
        $strList = mb_convert_encoding( $strList, 'UTF-8', 'JIS' );

        //制限コマンドが全て制限されているかの確認を行う
        if( strpos( $strList, "'get'" ) === false ){       return false;    }
        if( strpos( $strList, "'mget'" ) === false ){      return false;    }
        if( strpos( $strList, "'summary'" ) === false ){   return false;    }

        return true;
    }

    //==================================
    // メールコマンド制限状況の取得（設定状況の取得）
    // 返り値          : 制限状況の取得
    //==================================
    function GetDenyCommandMail_Status(){
        if( $this->mContents[CCtrlFml::eFILE_CONFIG] == '' ){
            $this->mContents[CCtrlFml::eFILE_CONFIG] = $this->Load( $this->mPath[CCtrlFml::eFILE_CONFIG] );
        }

        //コマンド制限リストの取得
        $strList = $this->GetOptionValue( $this->mContents[CCtrlFml::eFILE_CONFIG], '@DenyProcedure' );
        $strList = mb_convert_encoding( $strList, 'UTF-8', 'JIS' );

        //制限コマンドが全て制限されているかの確認を行う
        if( strpos( $strList, "'stat'" ) === false ){      return false;    }
        if( strpos( $strList, "'status'" ) === false ){    return false;    }

        return true;
    }

    //==================================
    // メールアドレスのメモリストを取得する
    //==================================
    function getMemberNameList(){
        //if( $this->mContents[CCtrlFml::eFILE_MEMBERSNAME] == '' ){
            $this->mContents[CCtrlFml::eFILE_MEMBERSNAME] = $this->Load( $this->mPath[CCtrlFml::eFILE_MEMBERSNAME] );
        //}

        $content = str_replace("\r\n","\n",$this->mContents[CCtrlFml::eFILE_MEMBERSNAME]);
        $content_list = explode("\n",$content);
        foreach($content_list as $line){
            list($mailaddress, $name) = explode("\t",$line,2);
            $list[$mailaddress] = $name;
        }
        return $list;
    }

    //==================================
    // メールアドレスのメモを登録する
    //==================================
    function putMemberName($mailaddress,$name){
        if($name === "" || $name === NULL){
            $this->deleteMemberName($mailaddress);
            exit;
        }

        $list = $this->getMemberNameList();
        $list[$mailaddress] = $name;
        $this->_updateMemberNameList($list);
    }

    //==================================
    // メールアドレスのメモを削除する
    //==================================
    function deleteMemberName($mailaddress){
        $list = $this->getMemberNameList();
        unset($list[$mailaddress]);
        $this->_updateMemberNameList($list);
    }

    //==================================
    // メールアドレスのメモリストを更新する
    //==================================
    function _updateMemberNameList($list){
        foreach($list as $mailaddress => $name){
            $content_list[] = $mailaddress."\t".$name;
        }
        $content = implode("\n",$content_list);
        $this->Update( $this->mPath[CCtrlFml::eFILE_MEMBERSNAME], $content );
    }


    //==================================
    // メールのパスを取得
    // 返り値          : メールパス
    //==================================
    function GetMailPath(){
        $path  = '/home/' . $this->mUserName . '/' . $this->mDomain . '/mail/' . $this->mDomain . '/';
        // . $this->mMlName . '-admin@' . $this->mDomain . '/new';
        return $path;
    }

    //==================================
    // -adminメールアカウントのパスを取得
    // 返り値          : -adminメールパス
    //==================================
    function GetAdminMailPath(){

        $path  = $this->GetMailPath() . $this->mMlName . '-admin@' . $this->mDomain . '/new';
        return $path;
    }


    //==================================
    // spool/mlのパスを取得
    // 返り値          : mlのパス
    //==================================
    function GetMLPath(){

        $path  = $this->mMlPath;
        return $path;
    }


    //==================================
    // メールのKeyを生成して取得
    // 返り値          : メールのKey
    //==================================
    function CreateMailKey(){

        $keyValue = CCommon::GetRandomString();
        $keyFolderPath = $this->GetMLPath();

        //キー格納用フォルダを作成
        if (!file_exists($keyFolderPath .'spool')){
            mkdir($keyFolderPath .'spool');
        }
        if (!file_exists($keyFolderPath .'spool/msgkey')){
            mkdir($keyFolderPath .'spool/msgkey');
        }

        //ハッシュ値のファイルを作成
        file_put_contents($keyFolderPath .'spool/msgkey/'. $keyValue , $keyValue);

        return $keyValue;
    }


//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>

    //==================================
    // 過去記事の公開状態を取得します
    // 返り値          : true( 公開 ) / false( 非公開 )
    //==================================
    function GetNewsView(){
        $path    = $this->mMlPath . 'mhonarc.sh';

        $text    = $this->Load( $this->mMlPath . 'mhonarc.sh' );

        //公開先ディレクトリ
        $pathHtml    = $this->mHtdocsPath;

        if( strpos( $text, $pathHtml ) === false ){
            return false;
        }
        return true;
    }

    //==================================
    // 過去記事の公開／非公開を行います
    // $isOpen         : true( 公開 ) / false( 非公開 )
    // 返り値          : なし
    //==================================
    function SetNewsView( $isOpen ){
        $path    = $this->mMlPath . 'mhonarc.sh';
        $pathHtml    = $this->mHttpPath;
        $pathInner    = $this->mHtdocsPath;

        $strTxt  = '';
        $strTxt .= "#!/bin/sh\n";
        $strTxt .= $this->mFmlPath . 'MHonArc/bin/mhonarc \\' . "\n";
        $strTxt .= '-rcfile ' . $this->mFmlPath .'MHonArc/bin/rcfile \\' . "\n";
        $strTxt .= '-add ' . $this->mMlPath . 'spool/ \\' . "\n";


        // MHonArc を使用しているため、
        // 設定ファイルではなく、メールのHook先を変更する必要があります。
        if( $isOpen ){
            //出力先をpublic_html以下に変更
            $strTxt .= '-outdir ' . $pathHtml . "\n";
            $this->Update( $path, $strTxt );

            //HTML 格納フォルダの移動
            shell_exec( 'mv ' . $pathInner . ' ' . $pathHtml );
        }else{
            //出力先をメーリングリスト格納先に変更
            $strTxt .= '-outdir ' . $pathInner . "\n";
            $this->Update( $path, $strTxt );

            //HTML 格納フォルダの移動
            shell_exec( 'mv ' . $pathHtml . ' ' . $pathInner);
        }
    }

    //==================================
    // 過去記事のパスを取得する
    // 返り値          : 過去記事の格納パス
    //==================================
    function GetNewsFolderPath(){
        $pathFolder  = '';

        if( $this->GetNewsView() ){ $pathFolder = $this->mHttpPath;
        }else{                      $pathFolder = $this->mHtdocsPath;
        }

        return $pathFolder;
    }

    //==================================
    // 過去記事のデータを取得する
    // $strFileName    : ファイル名
    // 返り値          : なし
    //==================================
    function GetNewsData( $strFileName ){
        $pathFolder  = '';
        $strHtmlData = '';

        if( $this->GetNewsView() ){ $pathFolder = $this->mHttpPath;
        }else{                      $pathFolder = $this->mHtdocsPath;
        }

        //フォルダ／ファイルがない場合は、NULLを返す
        if( ! is_dir( $pathFolder ) ){                 return NULL; }
        if( ! is_file( $pathFolder . $strFileName ) ){ return NULL; }

        //ファイルデータの取得
        $strHtmlData = file_get_contents( $pathFolder . $strFileName );

        return $strHtmlData;
    }

    //==================================
    // オプションへの値の設定
    // $strContents    : オプションのテキスト
    // $strKey         : 設定するオプション名
    // $strValue       : 設定する値
    // $isNum          : 設定する値が数値かどうか( 数値：true、文字列：false )
    // 返り値          : 値の設定されたオプションのテキスト
    //==================================
    function SetOptionValue( $strContents, $strKey, $strValue, $isNum=false ){
        $arrList     = explode( "\n", $strContents );
        $newContents = '';

        foreach( $arrList as $line ){
            $line = trim($line);
            //配信者名の改行複数ある場合、既存の不要文字列は削除する
            if( strpos( $line, '=?ISO-2022-JP?B?' ) !== false  ){
                if (mb_substr($line, 0, 16) == '=?ISO-2022-JP?B?'){
                    continue;
                }
            }
            if( strpos( $line, $strKey ) !== 0 || strpos( $line, '#' ) === 0 ){
                $newContents .= $line . "\n";
            }else{
                if( $isNum ){
                    $newContents .= $strKey . ' = ' . $strValue . ';' . "\n";
                }else{
                    $newContents .= $strKey . ' = "' . $strValue . '";' . "\n";
                }
            }
        }

        return $newContents;
    }

    //==================================
    // オプションへの値の設定
    // $strContents    : オプションのテキスト
    // $strKey         : 設定するオプション名
    // $strValue       : 設定する値
    // $isNum          : 設定する値が数値かどうか( 数値：true、文字列：false )
    // 返り値          : 値の設定されたオプションのテキスト
    //==================================
    function SetOptionHook( $strContents, $strKey, $strValue, $isNum){
        $arrList     = explode( "\n", $strContents );
        $newContents = '';

        foreach( $arrList as $line ){
            $line = trim($line);
            if( strpos( $line, $strKey ) !== 0 || strpos( $line, '#' ) === 0 ){
                $newContents .= $line . "\n";
            }else{
                if( $isNum == 0 ){
                    $newContents .= $strKey . ' = ' . $strValue . ';' . "\n";
                }elseif( $isNum == 1 ){
                    $newContents .= $strKey . " = '" . $strValue . "';" . "\n";
                }else{
                    $newContents .= $strKey . ' = "' . $strValue . '";' . "\n";
                }
            }
        }

        return $newContents;
    }


    //==================================
    // オプションの値の取得
    // $strContents    : オプションのテキスト
    // $strKey         : 取得するオプション名
    // $isNum          : 取得する値が数値かどうか( 数値：true、文字列：false )
    // 返り値          : 取得した値(取得失敗時:NULL)
    //==================================
    function GetOptionValue( $strContents, $strKey, $isNum=false ){
        $arrList = explode( "\n", $strContents );
        $text = '';
        $num  = 0;
        foreach( $arrList as $line ){
            //取得値の設定行ではない場合は次の行へ
            if( strpos( $line, $strKey ) === false )    continue;

            //コメントアウトされている場合は無視
            $text = trim( $line );
            if( strpos( $text, '#' ) === 0 )    continue;

            //固定形式であるかを確認する
            if( ! preg_match( '/^' . str_replace( '$', '\$', $strKey ) . '\s*=\s*\S+\s*;$/', $line ) ){
                if( strpos( $strKey, '@' ) === 0 ){

                }else{
                    continue;
                }
            }

            //識別子を除外する
            $num  = strpos( $text, $strKey );
            $num  = $num + strlen( $strKey );
            $text = substr( $text, $num, strlen($text) - $num );
            $text = trim( $text );

            //「 = 」を除外する
            if( strpos( $text, '=' ) === 0 ){
                $text = substr( $text, 1, strlen($text) - 1 );
            }
            $text = trim( $text );

            //終端子「;」を除外する
            if( strrpos( $text, ';' ) === ( strlen($text) - 1 ) ){
                $text = substr( $text, 0, strlen($text) - 1 );
            }

            //「 "" 」を除外する
            if( strpos( $text, '"' ) === 0 && strrpos( $text, '"' ) === ( strlen($text) - 1 ) ){
                $text = substr( $text, 1, strlen($text) - 1 );
                $text = substr( $text, 0, strlen($text) - 1 );
            }

            return $text;
        }

        return NULL;
    }


    //==================================
    // オプションのHANDLER値の取得
    // $strContents    : オプションのテキスト
    // $strKey         : 取得するオプション名
    // 返り値          : 1＝許可しない、2＝許可、3＝設定なし
    //==================================
    function GetOptionHandler( $strContents, $strKey){
        $arrList = explode( "\n", $strContents );
        $text = '';
        $num  = 0;
        foreach( $arrList as $line ){
            //取得値の設定行ではない場合は次の行へ
            if( strpos( $line, $strKey ) === false ){
                continue;
            }
            //コメントアウトされている場合は2
            $text = trim( $line );
            if( substr($text,0,1) === '#' ){
                return 2;
            }else{
                return 1;
            }
        }
        return 3;
    }


    //==================================
    // membersファイルのメンバー数を取得
    // 返り値          : メンバー数
    //==================================
    function GetMemberCount(){

        //メンバーファイルの中身を取得
        $list = $this->GetMemberUser();
        $member = array();
        $count = 0;

        foreach($list as $line){

            //空白コメントは除外する
            if( $line == '' ){     continue;}
            if( mb_strpos( $line, '#', 0, 'UTF-8' ) === 0 ){     continue;}

            $member[$count] = $line;
            $count++;

        }

        return count($member);

    }

    //==================================
    // activesファイルの受信メンバー数を取得
    // 返り値          : 受信メンバー数
    //==================================
    function GetActiveCount(){

        //メンバーファイルの中身を取得
        $list = $this->GetActiveUser();
        $member = array();
        $count = 0;

        foreach($list as $line){

            //空白コメントは除外する
            if( $line == '' ){     continue;}
            if( mb_strpos( $line, '#', 0, 'UTF-8' ) === 0 ){     continue;}

            $member[$count] = $line;
            $count++;

        }

        return count($member);
    }

    //==================================
    // ML_NAME/.aliasファイル受信許可アドレス設定
    // $strAddr        : 受信許可させるアドレス(削除時は空文字)
    // 返り値          : なし
    //==================================
    function SetAliasAdminMail($strAddr) {
        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '@' . $this->mDomain . '/.alias';
        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //テキストの置換対象
        $pattern    = "/ADMIN_ADDRESS=\".+\"/";
        $replace    = "ADMIN_ADDRESS=\"";
        $replace    .= (( strlen($strAddr) > 0 ) ? "$strAddr|$this->mMlName" : "$this->mMlName")."\"";

        //対象テキストを置換
        $text = preg_replace($pattern, $replace, $nowAlias);
        $this->Update( $path, $text );
    }

    //==================================
    // ML_NAME/.aliasファイル受信許可アドレス取得
    // 返り値          : 配列形式で設定しているアドレスを返す（ML_NAME除く）
    //==================================
    function GetAliasAdminMail() {
        $result = array();

        //パス
        $path  = $this->GetMailPath() . $this->mMlName . '@' . $this->mDomain . '/.alias';
        //現在のaliasの取得する
        $nowAlias = $this->Load( $path );

        //対象テキスト
        $pattern    = "/(ADMIN_ADDRESS=\")(?P<address>.+)(\")/";

        //対象テキストを取得
        preg_match($pattern, $nowAlias, $matches);
        $result = array_merge($result, explode('|', $matches['address']));
        // ML_NAME除去
        for ($i=0; $i < count($result); $i++) {
            if ( $result[$i] == $this->mMlName ) {
                unset($result[$i]);
                $i--;
            }
        }
        if ( count($result) <= 0 ) {
            $result[]   = "";
        }

        return $result;
    }

//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
//
// 共通
//
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
    //==================================
    // ファイル内容の設定
    //==================================
    function Input( $type, $content ){
        $this->mContents[$type] = $content;
    }

    //==================================
    // ファイル内容を返す
    //==================================
    function Output( $type ){
        return $this->mContents[$type];
    }

    //==================================
    // ファイルの読み込み
    //==================================
    function Load( $filename ){
        $contents = file_get_contents( $filename );
        $contents = str_replace( "\r\n", "\n", $contents );
        $contents = str_replace( "\r",   "\n", $contents );
        return $contents;
    }

    //==================================
    // ファイルへの書き込み
    //==================================
    function Update( $filename, $contents ){
        $contents = $this->PrepareContent( $contents );
        file_put_contents( $filename, $contents );
    }

    //==================================
    // 改行整理
    //==================================
    function PrepareContent( $content="" ){
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
        return $content;
    }

    //==================================
    // ユーザを登録
    //==================================
    function SetUsername( $username ){
        $this->mUserName = $username;
    }

    //==================================
    // ドメインを登録
    //==================================
    function SetDomain($domain){
        $this->mDomain = $domain;
    }

    //==================================
    // FML の設置先フォルダ
    //==================================
    function SetFmlName( $fml_name ){
        $this->mFmlName = $fml_name;
    }

    //==================================
    // メーリングリスト名の登録
    //==================================
    function SetMLName( $ml_name ){
        $this->mMlName = $ml_name;
    }

    //==================================
    // 各種パスを現在の設定に合わせて作成します
    //==================================
    function SetPath(){

        $this->mBasePath    =   dirname(FML_BASE) . '/';  //  '/home/' . $this->mUserName . '/' . $this->mDomain . '/' . $this->mFmlName . '/';
        $this->mFmlPath     =   FML_BASE . '/';           //  $this->mBasePath . 'lib/';
        $this->mMlPath      = $this->mBasePath . $this->mMlName . '/';
        $this->mHttpPath    =   dirname(PROJECT_BASE) . '/';  //'/home/' . $this->mUserName . '/' . $this->mDomain . '/public_html/xmailinglist/' . $this->mMlName . '/';
        $this->mHtdocsPath  = $this->mMlPath . 'htdocs/';
        /*
        echo $this->mBasePath . "<br>";
        echo $this->mFmlPath . "<br>";
        echo $this->mMlPath . "<br>";
        echo $this->mHttpPath . "<br>";
        echo $this->mHtdocsPath . "<br><br><br>";
        */
        $this->mPath[CCtrlFml::eFILE_MLLIST]       = $this->mBasePath . 'etc/aliases';
        $this->mPath[CCtrlFml::eFILE_CONFIG]       = $this->mMlPath   . 'config.ph';
        $this->mPath[CCtrlFml::eFILE_ADMIN]        = $this->mMlPath   . 'members-admin';
        $this->mPath[CCtrlFml::eFILE_MEMBER]       = $this->mMlPath   . 'members';
        $this->mPath[CCtrlFml::eFILE_ACTIVE]       = $this->mMlPath   . 'actives';
        $this->mPath[CCtrlFml::eFILE_MODERATORS]   = $this->mMlPath   . 'moderators';
        $this->mPath[CCtrlFml::eFILE_MEMBERSADMIN] = $this->mMlPath   . 'members-admin';
        $this->mPath[CCtrlFml::eFILE_MEMBERSNAME]   = $this->mMlPath   . 'members-name';
    }

}
?>
