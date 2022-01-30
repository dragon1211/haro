<?php

// 外部作成のライブラリィの呼び込み
require_once PROJECT_BASE . '/lib/outside/simple_html_dom.php';

//===========================================
//
//  MHonArc の解析クラス
//
//===========================================
class CMHonArc {
    var $mFolderPath;   // フォルダへのパス
    var $mOriginalHtml; // HTMLのデータ原本
    var $mChangeHtml;   // HTMLのデータ変換後
    var $mDomDocment;   // Document
    var $mNumber;        // 記事の連番
    var $mMailDate;     // 配信日時
    var $mDispSet;        // 

    //==============================
    //
    // コンストラクタ
    //
    //==============================
    function __construct(){
        $this->mFolderPath    = '';
        $this->mOriginalHtml  = '';
        $this->mChangeHtml    = '';
        $this->mDomDocment    = new simple_html_dom();
        $this->mNumber        = array();
        $this->mMailDate      = array();
        $this->mDispSet       = '';
    }

    //==============================
    // フォルダパスの設定
    // strPath    : フォルダへのパス
    //==============================
    function SetFolder( $strPath ){
        $this->mFolderPath    = $strPath;
    }

    //==============================
    // HTMLの読み込み
    // strFileName: ファイル名
    // 返り値     : 読み込んだHTMLデータ
    //==============================
    function ReadHtml( $strFileName ){
        //フォルダの有無確認
        if( ! is_dir( $this->mFolderPath ) ){    return '';    }

        //ファイルの有無確認
        if( ! is_file( $this->mFolderPath . $strFileName ) ){    return '';    }

        //HTMLの全データ
        $this->mOriginalHtml = file_get_contents( $this->mFolderPath . $strFileName );

        //HTMLデータの置換
        return $this->ConvertHtml( $this->mOriginalHtml );
    }

    //==============================
    // HTMLデータのコンバート(専用パネル使用)
    // strHtmlData: 置換するHTMLデータ
    // 返り値     : 置換後のHTMLデータ
    //==============================
    function ConvertHtml( $strHtmlData, $isLimitTxt=false ){

        //null チェック
        $objHtmlDom = new simple_html_dom();

        //Bodyタグ内の抜き出し
        $objHtmlDom->load( $strHtmlData, true, false );
        $strChangeHtml     = $objHtmlDom->find( 'body', 0 )->innertext;
        $objHtmlDom        = null;

        //抜き出しに失敗した場合は終了
        if( $strChangeHtml == '' ){
            return '';
        }
        
        //Aタグの変更
        $objHtmlDom = new simple_html_dom();
        $objHtmlDom->load( $strChangeHtml, true, false );
        $countATag  = count( $objHtmlDom->find( 'a' ) );
        for( $i=0;$i<$countATag;$i++ ){
            //リンク先の変更
            $strHref = $objHtmlDom->find( 'a', $i )->getAttribute( 'href' );
            if( strpos( $strHref, 'index.html' ) === 0 ){
                $retValue = 'thread_index';
                $strHref = './?page=Article&mail=' . $strHref;
                
            }elseif( strpos( $strHref, 'threads.html' ) === 0 ){
                $retValue = 'date_index';
                $strHref = './?page=Article&mail=' . $strHref;
                
            }else{
                $strHref = './?page=ArticleContents&mail=' . $strHref;
            }
            $objHtmlDom->find( 'a', $i )->setAttribute( 'href', $strHref );
            
            if( ! $isLimitTxt ){
                continue;
            }

            //リンクの文字数制限
            $strTxt = $objHtmlDom->find( 'a', $i )->getAttribute( 'innertext' );
            $nLen   = mb_strlen( $strTxt, 'UTF-8' );
            if( $nLen > 32 ){
                $strTxt  = mb_substr( $strTxt, 0, 32, 'UTF-8' );
                $strTxt .= '...';
                $objHtmlDom->find( 'a', $i )->setAttribute( 'innertext', $strTxt );
            }
        }

        // 添付ファイルへのアンカーを削除
        $countPTag  = count( $objHtmlDom->find( 'p' ) );
        for( $i=0;$i<$countPTag;$i++ ){
            //リンク先の変更
            $strP = $objHtmlDom->find( 'p', $i )->getAttribute( 'innertext' );
            if( strpos($strP, 'Attachment:') ){
                $strP = '';
            }
            $objHtmlDom->find( 'p', $i )->setAttribute('outertext', $strP);
        }

        //置換後のHTMLの保存
        $this->mChangeHtml = $objHtmlDom->innertext;

        //レイアウトの設定を追加
        $this->mChangeHtml .= '###' .  $retValue;

        return $this->mChangeHtml;
    }

    //==============================
    // HTMLデータの検索
    // strSearch  : 検索する文字列
    // isCheckAll : 全ファイルを検索対象とするか？
    // 返り値     : 検索結果(リンクつきの配列データ)
    //==============================
    function SearchHtml( $strSearch, $isCheckAll = false ){
        $serch_data = '';

        if( ! $objHandle = opendir( $this->mFolderPath ) ){
            return null;
        }

        while( $strFileName = readdir( $objHandle ) ){
            //msg***** 系以外はすべて対象外
            if( strpos( $strFileName, 'msg' ) !== 0 ){
                continue;
            }

            //検索結果の取得
            $list = $this->SearchMHonArcContents( $strSearch, $this->ReadHtml( $strFileName ) );
            foreach( $list as $value ){
                $serch_data .= '<a href="./?page=ArticleContents&mail=' . $strFileName . '">';
                $serch_data .= $strFileName;
                $serch_data .= '</a>';
                $serch_data .= "&nbsp;" . $value;
                $serch_data .= '<br />';
            }
        }

        closedir( $objHandle );

        return $serch_data;
    }
    //==============================
    // HTMLデータの検索
    // strSearch  : 検索する文字列
    // strTarget  : 検索する対象
    // 返り値     : 検索結果(リンクつきの配列データ)
    //==============================
    function SearchMHonArcContents( $strSearch, $strTarget ){

        $arrList = array();
        $counter = 0;

        //検索文字が存在しない場合は
        if( $strSearch == '' ){
            return $arrList;
        }

        //件名の取得
        $MailSubject = '';
        $nSearchPoint= false;
        $nStartPoint = strpos( $strTarget, '<!--X-Subject-Header-Begin-->' );
        $nEndPoint   = strpos( $strTarget, '<!--X-Subject-Header-End-->' );
        if( $nStartPoint !== false && $nEndPoint !== false ){
            $nStartPoint += strlen( "<!--X-Subject-Header-Begin-->" );
            $MailSubject  = substr( $strTarget, $nStartPoint, ( $nEndPoint - $nStartPoint ) );

            //件名内のタグの除去
            $MailSubject  = strip_tags( $MailSubject );
            $nSearchPoint = mb_strpos( $MailSubject, $strSearch, 0, 'UTF-8' );
            if( $nSearchPoint !== false ){
                $arrList[$counter++] = "[件名]&nbsp：　" . mb_substr( $MailSubject, $nSearchPoint, 20, 'UTF-8' );
            }
//          $MailSubject  = htmlspecialchars( $MailSubject );
        }

        //メールヘッダーの取得
        $MailHeader  = '';
        $nSearchPoint= false;
        $nStartPoint = strpos( $strTarget, '<!--X-Head-of-Message-->' );
        $nEndPoint   = strpos( $strTarget, '<!--X-Head-of-Message-End-->' );
        if( $nStartPoint !== false && $nEndPoint !== false ){
            $nStartPoint += strlen( "<!--X-Head-of-Message-->" );
            $MailHeader   = substr( $strTarget, $nStartPoint, ( $nEndPoint - $nStartPoint ) );

            //ヘッダー内のタグの除去
            $MailHeader   = strip_tags( $MailHeader );
            $nSearchPoint = mb_strpos( $MailHeader, $strSearch, 0, 'UTF-8' );
            if( $nSearchPoint !== false ){
                $arrList[$counter++] = "[ヘッダー]&nbsp：　" . mb_substr( $MailHeader, $nSearchPoint, 20, 'UTF-8' );
            }
//          $MailHeader   = htmlspecialchars( $MailHeader );
        }

        //メール本文の取得
        $MailBody    = '';
        $nSearchPoint= false;
        $nStartPoint = strpos( $strTarget, '<!--X-Body-of-Message-->' );
        $nEndPoint   = strpos( $strTarget, '<!--X-Body-of-Message-End-->' );
        if( $nStartPoint !== false && $nEndPoint !== false ){
            $nStartPoint += strlen( "<!--X-Body-of-Message-->" );
            $MailBody     = substr( $strTarget, $nStartPoint, ( $nEndPoint - $nStartPoint ) );

            //本文内のタグの除去
            $MailBody     = strip_tags( $MailBody );
            $nSearchPoint = mb_strpos( $MailBody, $strSearch, 0, 'UTF-8' );
            if( $nSearchPoint !== false ){
                $arrList[$counter++] = "[本文]&nbsp：　" . mb_substr( $MailBody, $nSearchPoint, 20, 'UTF-8' );
            }
//            $MailBody     = htmlspecialchars( $MailBody );
        }

        return $arrList;
    }

    
}

?>
