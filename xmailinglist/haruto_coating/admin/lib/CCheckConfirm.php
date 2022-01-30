<?php
//===========================================
//
//  認証確認
//
//===========================================
class CCheckConfirm {
    var $mAdmissionList;    //意思確認の入会用リスト
    var $mWithDrawList;     //意思確認の退会用リスト
	var $mAdList;			//意思確認の入会用リスト(キーで取得）
	var $mKey;				//リストのキー
	var $mMailaddress; 		//メールアドレス
	var $mLimitTime;		//有効期間
	var $mMemo; 			//メモ
    //var $mReissueList;        //パスワード再発行用リスト

    //===============================================
    //
    // コンストラクタ
    //
    //===============================================
    function CCheckConfirm(){
        $this->mAdmissionList    = '';
        $this->mWithDrawList     = '';
		$this->mAdList   		 = '';
	    $this->mKey				 = '';
		$this->mMailaddress		 = '';
		$this->mLimitTime		 = '';
		$this->mMemo 			 = '';

        $this->mAdmissionList    = array();
        $this->mWithDrawList     = array();
		$this->mAdList	     	 = array();
        
        $this->mAdmissionList    = $this->GetList( PROJECT_BASE_CONFIG .'/apply' );
        $this->mWithDrawList     = $this->GetList( PROJECT_BASE_CONFIG .'/quit' );

    }

    //===============================================
    // リストの読み込み
    //===============================================
    function GetList( $strFileName ) {
        $file_data = file_get_contents( $strFileName );
        $file_list = explode( "\n", $file_data );
        if( $file_list == NULL ){
            $file_list = array();
        }
		
		foreach( $file_list as $value){
            $data   = explode( "\t", $value);
            switch ( count($data) ) {
                case 3:
                    list($key, $LimitTime, $memo)  = $data;
                    break;
                case 4:
                    list($mail, $key, $LimitTime, $memo)    = $data;
                    break;
            }
            $this->mAdList[$key] = $memo;
		}

        return $file_list;
    }

    //===============================================
    // リストの書き込み
    //===============================================
    function SetList( $strFileName, $arrList ) {
        $contents   = '';
        
        foreach( $arrList as $value ){
            if( $value == '' ){
                continue;
            }
            $contents .= $value . "\n";
        }
        file_put_contents( $strFileName, $contents );
    }

    //===============================================
    // 入会意思確認にアドレスを追加する
    //===============================================
    function AddAdmisionMail( $strMailAddress, $strParam, $memo ) {
        $count = count($this->mAdmissionList);
        #$this->mAdmissionList[$count] = md5( $strMailAddress . $strParam ) . "\t" . mktime( date("H") + 24 ) . "\t". $memo;
        $this->mAdmissionList[$count] = $strMailAddress . "\t" . $strParam . "\t" . mktime( date("H") + 24 ) . "\t". $memo;
        $this->SetList( PROJECT_BASE_CONFIG. '/apply', $this->mAdmissionList );
    }

    //===============================================
    // 退会意思確認にアドレスを追加する
    //===============================================
    function AddWithDrawMail( $strMailAddress, $strParam ) {
        $count = count($this->mWithDrawList);
        #$this->mWithDrawList[$count] = md5( $strMailAddress . $strParam ) . "\t" . mktime( date("H") + 24 );
        $this->mWithDrawList[$count] = $strMailAddress . "\t" . $strParam . "\t" . mktime( date("H") + 24 );
        $this->SetList( PROJECT_BASE_CONFIG . '/quit', $this->mWithDrawList );
    }

    //===============================================
    // 入会意思確認にアドレスをチェックする
    //===============================================
    function CheckAdmisionMail( $strMailAddress, $strParam) {
        foreach( $this->mAdmissionList as $key => $value ){
            $data = explode( "\t", $value );
            if( 4 < count($data)){
                continue;
            }
            
            switch ( count($data) ) {
                case 3:
                    list($hash, $limit, $memo)  = $data;
                    $mail   = $strMailAddress;
                    $pass   = md5( $strMailAddress . $strParam );
                    break;
                case 4:
                    list($mail, $hash, $limit, $memo)   = $data;
                    $pass   = $strParam;
                    break;
            }
            
            //既に時間が経過したものは除外する
            if( strcmp( $limit, time() ) < 0 ){
                $this->mAdmissionList[$key] = '';
                continue;
            }
            if( $hash != $pass ){ continue; }
			
			
            //一度、処理したものは除外する
            $this->mAdmissionList[$key] = '';
            $this->SetList( PROJECT_BASE_CONFIG . '/apply', $this->mAdmissionList );
            return $mail;
        }
        
        $this->SetList( PROJECT_BASE_CONFIG . '/apply', $this->mAdmissionList );
        return false;
    }
	
	
    //===============================================
    // 管理画面からの入会意思確認によるメモの取得
    //===============================================
    function GetApplyMemo( $strMailAddress , $param, $id) {
		$memo = '';
		//$this->GetList( PROJECT_BASE_CONFIG .'/apply' );
        $key  = !empty($id) ? $id : md5( $strMailAddress . $param);
        
		if(isset($this->mAdList[$key])){
			return $this->mAdList[$key];
		}
		return $memo;
    }

    //===============================================
    // 退会意思確認にアドレスをチェックする
    //===============================================
    function CheckWithdrawMail( $strMailAddress, $strParam ) {
        foreach( $this->mWithDrawList as $key => $value ){
            $data = explode( "\t", $value );
            
            if( count($data) < 2 || count($data) > 3 ){
                continue;
            }
            
            switch ( count($data) ) {
                case 2:
                    list($hash, $limit)  = $data;
                    $pass   = md5( $strMailAddress . $strParam );
                    $mail   = $strMailAddress;
                    break;
                case 3:
                    list($mail, $hash, $limit)  = $data;
                    $pass   = $strParam;
                    break;
            }
            
            //既に時間が経過したものは除外する
            if( strcmp( $limit, time() ) < 0 ){
                $this->mWithDrawList[$key] = '';
                continue;
            }
            if( $hash != $pass ){ continue; }

            //一度、処理したものは除外する
            $this->mWithDrawList[$key] = '';
            $this->SetList( PROJECT_BASE_CONFIG . '/quit', $this->mWithDrawList );
            return $mail;
        }
        
        $this->SetList( PROJECT_BASE_CONFIG . '/quit', $this->mWithDrawList );
        return false;
    }

}

?>
