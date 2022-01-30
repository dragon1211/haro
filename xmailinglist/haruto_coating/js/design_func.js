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

