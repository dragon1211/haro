<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>Reset Password</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

  <!--begin::Global Theme Styles -->
  <link href="{{ asset('assets/vendors/base/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/demo/default/base/style.bundle.css') }}" rel="stylesheet" type="text/css" />
  <!--end::Global Theme Styles -->
  <link rel="shortcut icon" href="{{ asset('assets/demo/default/media/img/logo/favicon.ico') }}" />
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body style="display: flex; flex: 1;">

<!-- begin:: Page -->
<div style="display: flex; flex: 1;
flex-direction: column;
margin-top: 100px; border-width: 3px; border-color: black; height: 400px;align-items: center">

    <H3>Reset Password</H3>
    <form action="{{ url('/api/client/doResetPassword') }}" method="POST">
      @csrf
      <input type="hidden" value="{{$customerID}}" name="customerID" />
        <div style="width: 700px;display: flex;flex-direction: column">
            <div style="display: flex">
                <div style="flex: 1;text-align: right;padding-right: 5px;display: flex;justify-content: end;align-items: center;">
                    <label>パスワード</label>
                </div>
                <input type="password" placeholder="パスワード" name="password" style="flex: 3; height: 30px;"
                       required data-msg-required="パスワードを入力してください.">
            </div>
            <div style="display: flex; margin-top: 10px">
                <div style="flex: 1;text-align: right;padding-right: 5px;display: flex;justify-content: end;align-items: center;">
                    <label>パスワード(再度入力)</label>
                </div>
                <input type="password" placeholder="パスワード(再度入力)" name="password_confirm" style="flex: 3; height: 30px;"
                       required data-msg-required="パスワードを入力してください.">
            </div>
            <button type="submit" name="submit" style="width: 200px; height: 50px; font-size: large; margin-top: 30px; align-self: center">Reset</button>
        </div>
    </form>
</div>
<!-- end:: Page -->

<!--begin::Global Theme Bundle -->
<script src="{{ asset('assets/vendors/base/vendors.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/demo/default/base/scripts.bundle.js') }}" type="text/javascript"></script>
<!--end::Global Theme Bundle -->

<!--begin::Page Scripts -->
<script src="{{ asset('assets/snippets/custom/pages/user/login.js') }}" type="text/javascript"></script>
<script>
    $(function() {
        $('button[name="submit"]').on('click', function () {
            if ($('input[name="password"]').val() === $('input[name="password_confirm"]').val()) {
                $('form').submit();
            } else {
                alert('암호를 정확히 입력해주세요.');
            }
        })
    });
</script>
<!--end::Page Scripts -->
</body>
<!-- end::Body -->
</html>
