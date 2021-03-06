<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <!-- begin::Head -->
  <head>
    <meta charset="utf-8" />
    <title>Haruto Coating Store</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
      WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
        </script>

    <!--end::Web font -->

    <!--begin::Global Theme Styles -->
    <link href="{{ asset('assets/vendors/base/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/demo/default/base/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="{{ asset('assets/demo/default/media/img/logo/favicon.ico') }}" />
  </head>

  <!-- end::Head -->

  <!-- begin::Body -->
  <body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

    <!-- begin:: Page -->
    <div class="m-grid m-grid--hor m-grid--root m-page">
      <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2" style="background-image: url({{ asset('assets/app/media/img//bg/bg-3.jpg') }});">
        <div class="m-grid__item m-grid__item--fluid  m-login__wrapper">
          <div class="m-login__container">
            <div class="m-login__logo">
              <img src="{{ asset('images/harutogw-L.jpg') }}" style="width: 100%">
            </div>
            <div class="m-login__signin">
              <form class="m-login__form m-form" data-toggle="validator">
                <div class="m-login__form-action">
                  <a 
                    href="/apks/haruto-coating-store.apk"
                    class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary" 
                    download
                  >
                    ?????????????????????DL
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
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
            $('form').each(function() {$(this).validate();});
        });
    </script>
    <!--end::Page Scripts -->
  </body>
  <!-- end::Body -->
</html>
