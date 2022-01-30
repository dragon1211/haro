<?php
class CCtrlErrMail {

    //=====================
    // 定数の定義
    //=====================
    //エラー種別
    const ERR_PERMANENT = 0;    //恒久的なエラー
    const ERR_TEMPORARY = 1;    //一時的なエラー
    const ERR_UNKNOWN = 99;     //不明なエラー

    const ERR_SPAN          = 7;        //エラー期間
    const ERR_DEFAULT_NUM   = 3;        //エラー回数設定値 デフォルト
    const ERR_MIN_NUM       = 1;        //エラー回数設定値 最小値
    const ERR_MAX_NUM       = 10;       //エアー回数設定値 最大値

    //エラーアドレス一覧ファイル名
    const eERR_ADDRESS = 'error_address';

    //エラーメール集計結果ファイル名
    const eERR_SUMMARY = 'error_summary';

    //削除ユーザー一覧ファイル名
    const eERR_DELETE = 'error_delete';

    //=====================
    // 変数の定義
    //=====================

    //ＦＭＬの操作クラス
    var $mCtrlFml;

    // エラーリスト
    var $mErrList;

    // 集計結果リスト
    var $mSummaryList;

    // 削除アドレスリスト
    var $mDeleteList;

    // 解析結果ファイルのパス
    var $mPathAddress;  // アドレス一覧
    var $mPathSummary;  // 集計結果
    var $mPathDelete;   // 削除アドレス一覧

    // エラー解析用配列 (添え字をエラー種別のconstにあわせる)
    var $mStandardCode = array(
        array(   // 恒久的なエラー(ERR_PERMANENT = 0)
                # 'undefined'   => array('5.0.0', '5.5.1', '5.5.2', '5.5.3', '5.5.4', '5.5.5'),
                'userunknown'   => array('5.1.1', '5.1.0', '5.1.3'),   //; 5.1.3 ?
                'hostunknown'   => array('5.1.2'),
                'hasmoved'      => array('5.1.6'),
                'rejected'      => array('5.1.8', '5.1.7'),
                'filtered'      => array('5.2.1', '5.2.0'),
                'mailboxfull'   => array('5.2.2'),
                'exceedlimit'   => array('5.2.3'),
                'systemfull'    => array('5.3.1'),
                'notaccept'     => array('5.3.2'),
                'mesgtoobig'    => array('5.3.4'),
                'systemerror'   => array('5.3.5', '5.3.0', '5.4.0', '5.4.1', '5.4.2', '5.4.3', '5.4.4', '5.4.5', '5.4.6'),
                'expired'       => array('5.4.7'),
                'mailererror'   => array('5.2.4'),
                'contenterr'    => array('5.6.0'),
                'securityerr'   => array('5.7.0', '5.7.1'),
        ),
        array(   // 一時的なエラー(ERR_TEMPORARY = 1)
                # 'undefined'   => array('4.0.0'),
                'hasmoved'      => array('4.1.6'),
                'rejected'      => array('4.1.8'),
                'mailboxfull'   => array('4.2.2'),
                'exceedlimit'   => array('4.2.3'),
                'systemfull'    => array('4.3.1'),
                'systemerror'   => array('4.3.5'),
                'expired'       => array('4.4.7', '4.4.1'),
       ),
    );


    //===================================
    // コンストラクタ
    // $pathAnalyzeResult: 解析結果ファイルの出力先ディレクトリ
    //===================================
    function __construct($pathAnalyzeResult) {
        $this->mCtrlFml = new CCtrlFml();

        //各リストデータ格納用配列の初期化
        $this->mErrList = null;
        $this->mSummaryList = null;
        $this->mDeleteList = null;

        //各パスの初期化
        $this->mPathAddress = $pathAnalyzeResult . self::eERR_ADDRESS;  // アドレス一覧
        $this->mPathSummary = $pathAnalyzeResult . self::eERR_SUMMARY;  // 集計結果
        $this->mPathDelete = $pathAnalyzeResult . self::eERR_DELETE;    // 削除アドレス一覧
    }


    //==================================
    // エラーメールリスト取得
    // $address          : 検索アドレス
    // 返り値            : エラーメールのリスト
    //==================================
    function GetErrorMailList($address = null) {
        //エラーリストが生成されてない場合は、生成を実行
        if (is_null($this->mErrList)) {
            $this->ReadErrorMailList();
        }

        //$addressが指定されていない場合
        if (is_null($address)) {
            return $this->mErrList;
        }

        //$addressが指定されている場合は、該当アドレスのリストを生成
        $list = array();
        $n = count($this->mErrList);
        for($i = 0; $i < $n; $i++) {
            if ($address == $this->mErrList[$i]['address']) {
                array_push($list, $this->mErrList[$i]);
            }
        }

        return $list;
    }


    //==================================
    // 集計結果リスト取得
    // 返り値            : 集計結果リスト
    //==================================
    function GetSummaryList() {
        //集計結果リストが生成されてない場合は、生成を実行
        if (is_null($this->mSummaryList)) {
            $this->ReadSummaryList();
        }
        return $this->mSummaryList;
    }


    //==================================
    // 削除アドレスリスト取得
    // 返り値            : 削除アドレスリスト
    //==================================
    function GetDeleteList() {
        //削除アドレスリストが生成されてない場合は、生成を実行
        if (is_null($this->mDeleteList)) {
            $this->ReadDeleteList();
        }
        return $this->mDeleteList;
    }


    //==================================
    // 削除アドレスリストクリア
    // 返り値            : 削除アドレスリスト
    //==================================
    function ClearDeleteList() {
        @unlink($this->mPathDelete);
        $this->mDeleteList = null;
    }


    //==================================
    // メールデータ解析
    // $data           : メールデータ
    //==================================
    function AnalyzeErrorMail($mailData) {
        //メールデータを解析
        $result = $this->AnalyzeMailData($mailData);

        //集計データ読み込み
        $this->ReadErrorMailList();

        //解析結果を配列に追加
        foreach($result as $val) {
            array_push($this->mErrList, $val);
        }

        //集計データ更新
        $this->UpdateSummaryList();

        //解析データ書き込み
        $this->WriteErrorMailList();
    }

    //==================================
    // 解析データ書き込み
    // $data           : メールデータ
    //==================================
    function WriteErrorMailList() {
        //書き込みデータ生成
        $f = fopen($this->mPathAddress, 'w');
        if (!is_null($this->mErrList)) {
            foreach($this->mErrList as $val) {
                $out = $val['address']
                . ",". $val['status']
                . ",". $val['kind']
                . ",". $val['date']
                . "\n";
                //追記
                fwrite($f, $out);
            }
        }
        fclose($f);
    }


    //==================================
    // エラーメールリストの読み込み
    //==================================
    function ReadErrorMailList() {
        $this->mErrList = array();
        $f = @fopen($this->mPathAddress, 'r');
        if (!$f) {
            return;
        }

        // 解析結果ファイルを読み込んでリストに格納
        $sort = array();
        while(!feof($f)) {
            $buf = fgets($f);
            if (strlen($buf) == 0) {
                continue;
            }
            $buf = str_replace("\n", '', $buf);
            $tmp = explode(",", $buf);
            $data = array('address' => $tmp[0],
                           'status' => $tmp[1],
                           'kind' => $tmp[2],
                           'date' => $tmp[3],
                        );
            array_push($this->mErrList, $data);
            array_push($sort, $tmp[3]); // 日付をソートキーに
        }
        fclose($f);

        // ソート実行
        array_multisort($sort, SORT_DESC, $this->mErrList);
    }


    //==================================
    // 集計リストの読み込み
    //==================================
    function ReadSummaryList() {
        $tmp = @file_get_contents($this->mPathSummary);
        $this->mSummaryList = array();
        if ($tmp) {
            $this->mSummaryList = unserialize($tmp);
        }
    }
    //==================================
    // 集計リストの書き込み
    //==================================
    function WriteSummaryList() {
        file_put_contents($this->mPathSummary, serialize($this->mSummaryList));
    }
    //==================================
    // 集計リストの更新
    //==================================
    function UpdateSummaryList() {
        // 集計リストを初期化
        $this->mSummaryList = array();

        //初期化データ
        $initData = array('permanent' => 0, 'temporary' => 0, 'unknown' => 0, 'sum' => 0);

        //集計期間
        $limit = date('Y-m-d H:i:s', strtotime("-" . self::ERR_SPAN . " day"));

        foreach($this->mErrList as $analyzeData) {
            $address = $analyzeData['address'];

            //集計期間を外れたらbrake
            //$this->mErrListは、'date'降順でソート済み
            if ($analyzeData['date'] < $limit) {
                break;
            }

            if (!isset($this->mSummaryList[$address])) {
                $this->mSummaryList[$address] = $initData;
            }

            //エラー発生最終日時
            $this->mSummaryList[$address]['date'] = $analyzeData['date'];

            switch($analyzeData['kind']) {
                case self::ERR_PERMANENT:
                    $this->mSummaryList[$address]['permanent']++;
                    break;
                case self::ERR_TEMPORARY:
                    $this->mSummaryList[$address]['temporary']++;
                    break;
                case self::ERR_UNKNOWN:
                    $this->mSummaryList[$address]['unknown']++;
                    break;
            }
            //合計
            $this->mSummaryList[$address]['sum']++;
         }

        //集計データ書き込み
        $this->WriteSummaryList();
    }


    //==================================
    // エラーメールリストの読み込み
    //==================================
    function ReadDeleteList() {
        $this->mDeleteList = array();
        $tmp = @file_get_contents($this->mPathDelete);
        if ($tmp) {
            $this->mDeleteList = unserialize($tmp);
        }
    }
    //==================================
    // エラーメールリストの書き込み
    //==================================
    function WriteDeleteList() {
        file_put_contents($this->mPathDelete, serialize($this->mDeleteList));
    }


    //==================================
    // メールデータ解析
    // $data           : メールデータ
    // 返り値          : 解析結果
    //==================================
    function AnalyzeMailData($mailData) {
        //配信エラーメールアドレス用リスト
        $result = array();
        $from = '';
        $subject = '';

        // メールヘッダ/ボディ取得
        $tmp = str_replace( array("\r\n","\r"), "\n", $mailData );
        list($head, $body) = explode("\n\n", $tmp, 2);

        // バウンスメールかチェック
        $pos = strpos($head, 'multipart/report; report-type=delivery-status');
        if ($pos === false){
            return $result;
        }

        // メールボディから"Delivery report"を取得
        preg_match('/boundary="(.+)"/', $head, $matchs);
//        preg_match(/boundary="(?P<name>.+)"/, $head, $matches);
        $boundary = $matchs[1];

        // メールボディをバウンダリで分割
        $parts = explode('--' . $boundary, $body);
        foreach($parts as $part) {
            $pos = strpos($part, 'Content-Type: message/delivery-status');
            if ($pos !== false) {
                $partReport = $part;
            }
        }

        if ($partReport === ''){
            return $result;
        }

        // レポートを解析
        $reports = explode("\n\n", $partReport);
        foreach ($reports as $report) {
            // 到着日時
            $pos = strpos($line, 'Arrival-Date:');
            if ($pos !== false) {
                $ret = explode( ":", $line, 2);
                $date = trim($ret[1]);
                $date = str_replace(' (JST)', '',  $date);
                $date = date('Y-m-d H:i', strtotime($date));
            }

            $lines = explode("\n", $report);
            $address = '';
            $status = '';
            foreach ($lines as $line) {
                //送信先アドレス
                $pos = strpos($line, 'Original-Recipient: rfc822;');
                if ($pos !== false) {
                    $ret = explode( ";", $line);
                    $address = trim($ret[1]);
                    continue;
                }

                //ステータス
                $pos = strpos($line, 'Status:');
                if ($pos !== false) {
                    $ret = explode( ":", $line);
                    $status = trim($ret[1]);
                    continue;
                }
            }

            //メールアドレス
            if ($address != '') {
                $tmp = array('address' => $address,
                             'status' => $status,
                             'kind' => $this->GetKind($status),
                             'date' => $date,
                            );
                array_push($result, $tmp);
            }
        }

        return $result;
    }


    //==================================
    // ステータス取得
    // $path           : ステータスコード
    // 返り値          : エラーメールの内容詳細
    //==================================
    function GetKind($statusCode) {
        $return = self::ERR_UNKNOWN;

        // ステータスコードからエラー種別を取得
        foreach($this->mStandardCode as $key => $kind) {
            foreach($kind as $codes) {
                foreach($codes as $code) {
                    if ($statusCode == $code) {
                        return $key;
                    }
                }
            }
        }
        return $return;
    }


    //==================================
    // エラー種別の表示文字列取得
    // $kind           : コード
    // 返り値          : 表示文字列
    //==================================
    function GetErrKindStr($code) {
        $return = '不明なエラー';
        switch ($code) {
            case self::ERR_PERMANENT:
                $return = '恒久的なエラー';
                break;
            case self::ERR_TEMPORARY:
                $return = '一時的なエラー';
                break;
        }
        return $return;
    }


    //==================================
    // エラーの表示文字列取得
    // $statusCode     : コード
    // 返り値          : 表示文字列
    //==================================
    function GetStarusStr($statusCode) {
        $return = '';
        foreach($this->mStandardCode as $kind) {
            foreach($kind as $status => $codes) {
                foreach($codes as $code) {
                    if ($statusCode == $code) {
                        return $status;
                    }
                }
            }
        }
        return $return;
    }


    //==================================
    // 自動削除対象ユーザーの取得
    //==================================
    function GetAutoDeleteUser($settings) {
        //集計結果リストが生成されてない場合は、生成を実行
        $this->GetSummaryList();

        //削除対象のリスト生成
        $sum = $settings['sum'];

        $list = array();
        foreach($this->mSummaryList as $address => $val) {
            if ($sum != '-1' && $sum <= $val['sum']) {
                $val['date'] = date("Y-m-d H:i", time());
                $val['reason'] = 'sum';
                $list[$address] = $val;
            }
        }

        return $list;
    }

    //==================================
    // ユーザーの削除
    //==================================
    function DeleteUser($list) {
        // 削除対象が無い場合は処理を抜ける
        if (count($list) == 0) {
            return;
        }

        //集計結果リストが生成されてない場合は、生成を実行
        $this->GetSummaryList();

        //削除アドレスリストが生成されてない場合は、生成を実行
        $this->GetDeleteList();

        //------------------------------------------
        // 削除処理
        // (1)削除ユーザーをエラーメールリストから削除
        // (2)削除ユーザーを集計結果リストから削除
        // (3)ユーザーの削除
        // (4)削除ユーザーリストに追加
        //   ⇒(3)(4)は有効なユーザーのみ
        //     ($listに'memo'が設定されている場合を有効と判断)
        //------------------------------------------
//$start = explode(' ', microtime());
//$start = $start[1] + $start[0];

        //エラーメールデータ取得
        $tmp = @file_get_contents($this->mPathAddress);
        $lines = explode("\n", $tmp);

        //削除処理実行
        foreach($list as $address => $val) {
            //該当ユーザーをエラーメール一覧から削除
            $out = array();
            foreach($lines as $line) {
                $pos = strpos($line, $address);
                if ($pos === false) {
                    array_push($out, $line);
                }
            }
            $lines = $out;

            //集計結果から削除
            unset($this->mSummaryList[$address]);

            //有効なユーザーの場合は、削除＆削除ユーザーリストに追加
            if (isset($val['memo'])) {
                //削除リストに追加
                $this->mDeleteList[$address] = $val;
            }
        }
//_vd($this->mSummaryList);
//$end = explode(' ', microtime());
//$end = $end[1] + $end[0];
//print "*** use-time[" . round($end - $start, 4) . "]<br>";

        // エラーメールデータ更新
        file_put_contents($this->mPathAddress, implode("\n", $lines));

        //集計結果データ更新
        $this->WriteSummaryList();

        //削除ユーザーデータ更新
        $this->WriteDeleteList();
    }


    //==================================
    // 全エラーメール解析
    //==================================
    function AnalyzeAllErrorMail($pathAdminMail) {
        // ファイル毎に解析
        if ($handle = opendir($pathAdminMail)) {
             while (false !== ($file = readdir($handle))) {
                if($file != '.' && $file != '..'){
                    $path = $pathAdminMail . "/" . $file;
                    $data = @file_get_contents($path);
                    $this->AnalyzeErrorMail($data);
                }
            }
            closedir($handle);
        }
    }


    //==================================
    // 集計結果リストから該当のメールアドレスを削除
    //==================================
    function UnsetSummaryData($list) {
        //集計結果リストが生成されてない場合は、生成を実行
        $this->GetSummaryList();

        //解析データ読み込み
        $this->GetErrorMailList();

        foreach($list as $address) {
            unset($this->mSummaryList[$address]);
 
            $tmp = array();
            foreach($this->mErrList as $val) {
                if ($val['address'] != $address) {
                    array_push($tmp, $val);
                }
            }
            $this->mErrList = $tmp;
        }

        //集計データ書き込み
        $this->WriteSummaryList();
        //解析データ書き込み
        $this->WriteErrorMailList();
    }


    //==================================
    // 削除リストから該当のメールアドレスを削除
    //==================================
    function UnsetDeleteData($list) {
        //削除アドレスリストが生成されてない場合は、生成を実行
        $this->GetDeleteList();

        foreach($list as $address => $val) {
            unset($this->mDeleteList[$address]);
        }

        //更新
        $this->WriteDeleteList();
    }


    //==================================
    // 全エラーメールの集計
    //==================================
    function SummaryAllErrorMail() {
        //エラーリストが生成されてない場合は、生成を実行
        if (is_null($this->mErrList)) {
            $this->ReadErrorMailList();
        }

        //集計リスト読み込み
        $this->ReadSummaryList();

        //集計リスト更新
        foreach($this->mErrList as  $data){
            $this->UpdateSummaryList($data);
        }

        //集計リスト書き込み
        $this->WriteSummaryList();
    }


    //==================================
    // 各ファイルの削除
    //==================================
    function DeleteFile() {
        @unlink($this->mPathAddress);
        @unlink($this->mPathSummary);
        @unlink($this->mPathDelete); 
    }
}




function _pr($v) {
    print "[" . $v . "]<br>";
}
function _vd($v) {
    print "<pre>";
    var_dump($v);
    print "</pre>";
    print "<hr><br>";
}
?>
