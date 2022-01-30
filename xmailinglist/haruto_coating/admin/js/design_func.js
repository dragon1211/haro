//========================
// セル情報の変更
//========================
function ChangeCell( nRows, nCells ) {
    var objTable  = document.getElementById( 'members_table' );
    var strTxt    = objTable.rows[nRows].cells[nCells].innerText;
    var strChange = '';
    if( strTxt == '○' ){    strChange = '×';
    }else{                   strChange = '○';
    }

    //セル情報の変更
    objTable.rows[nRows].cells[nCells].innerHTML = '<a href="javascript:void(0);" onclick="ChangeCell( ' + nRows + ', ' + nCells + ' )" >' + strChange + '</a>';
}


//========================
// メールアドレスの削除確認
//========================
function DeleteMail() {
    return confirm('メールアドレスを削除します。\r\nよろしいですか？');
}


//========================
// ML差出人の設定
//========================
function senderDisable(){
    if($("input[name=ml_sender_check]").prop("checked")){
            $("input[name=ml_sender_name]").removeAttr("disabled");
    }
    else{
            $("input[name=ml_sender_name]").attr("disabled", "disabled");
    }
}
$(document).ready(function(){
    $("input[name=ml_transmit_val]").click(senderDisable);
    senderDisable();
});

//========================
// 表示ファイルの切り替え
//========================
function ChangeVewFile( nFileIndex, nItemMax ) {
    var strBaseName    = 'contents_file_0';
    var strBaseTag     = 'sub_tag_file_0';
    var i;

    for( i=1;i<=nItemMax;i++ ){
        if( i == nFileIndex ){
            //選択中のタグ
            document.getElementById( strBaseName + i ).style.display  = 'block';
            document.getElementById( strBaseTag + i ).className       = 'current_tag_file';
        }else{
            //非選択のタグ
            document.getElementById( strBaseName + i ).style.display  = 'none';
            document.getElementById( strBaseTag + i ).className       = ' ';
        }
    }

    //表示項目の識別子の変更
    document.main_menu.now_systemmail.value = nFileIndex;
}

//==================================
// ユーザーパネル情報の表示切替
//==================================
function ViewPanelInfo() {
    if( document.option_menu.panel_open.value != 0 ){
        document.getElementById( "userpanel_table" ).style.display  = 'block';
    }else{
        document.getElementById( "userpanel_table" ).style.display  = 'none';
    }
}


//==================================
// 新規投稿フォームを開く
//==================================
function OpenWriteForm( strTitleName ){
    $('#write_form').dialog({
        title: strTitleName
    });
    $('#write_form').dialog('open');
    SetCenterWriteForm();
}

//==================================
// 新規投稿フォームを移動する
//==================================
var nWindowWidth  = 0;
var nWindowHeight = 0;
function SetCenterWriteForm(){
    if( nWindowWidth != $(window).width() || nWindowHeight != $(window).height() ){
        var nLeft = Math.floor( ($(window).width()  - $('#write_form').width())  / 2 );
        var nTop  = Math.floor( ($(window).height() - $('#write_form').height()) / 2 );
        if( nLeft < 0 ){ nLeft = 0 }
        if( nTop  < 0 ){ nTop  = 0 }
        $('#write_form').dialog({
                position: [ nLeft, nTop ]
        });
    }
    
    nWindowWidth  = $(window).width();
    nWindowHeight = $(window).height();
}


//==================================
// デザインフォームのリサイズ命令郡
//==================================
function onResizeDesign() {
    SetCenterWriteForm();
}

//==================================
// メールの送信
//==================================
function Sendmail() {
    $.post( "./../mail.php",
            {},
            function(data){}
          );
}

//--------------------------
// jQueryの操作
//--------------------------
$(function(){
    $('#write_form').dialog({
        autoOpen:false,
        width:500,
        height:360,
        modal:true,
        buttons:{
            "新規投稿する":function(){
                Sendmail();
                $(this).dialog("close");
            },
            "キャンセル":function(){
                $(this).dialog("close");
            }
        }
    });
});

$(window).resize( function(){
    onResizeDesign();
});



//  ページが読み込まれたときに実行
$(document).ready(function(){
    userShowFlag();
    userPassCheck();
});

function userShowFlag(){
    var $select = $("select[name=panel_maillog]");
    var $targetRow = $("#userpanel_login");
    var func = function(){
        if ( $("select[name=panel_maillog] option:selected").val() != "0"){
    //        $targetRow.slideDown();
            $targetRow.show();
        }
        else{
    //        $targetRow.slideUp();
            $targetRow.hide();
        }
    }
    $select.change(func);
}


function userPassCheck(){
    var $sentence = $("#password_warning");
    var $checkbox = $("input[name=pnael_log_protect]");
    var $pass = $("input[name=panel_user_password]");
    
    $sentence.data("text", $sentence.html());
    var func = function(event){
        if($checkbox.attr("checked")){
            $sentence.html("　");
            $pass.removeAttr("disabled")
        }else{
            $sentence.html($sentence.data("text"));
            $pass.attr("disabled", "disabled")
        }
    }
    $checkbox.click(func).change(func).change();
}
