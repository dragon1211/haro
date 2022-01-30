//==================================
// 日付を取得する
//==================================
function NowYear() {
    var data = new Date();
    var nowYear = data.getFullYear();
    document.write(nowYear);
}


//==================================
// 2重ポストの禁止
//==================================
function ExeNow(b)
{
  b.disabled = true;
  ExeDisabled("exe_submit_button1");
  b.value = '処理中です';
  b.form.submit();
}
function ReturnNow(b)
{
  ExeDisabled("exe_submit_button2");
  b.form.submit();
}
function ExeDisabled(e)
{
  var TargetElement = document.getElementById(e);
  TargetElement.disabled = true;
}
