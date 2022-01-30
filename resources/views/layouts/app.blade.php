<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!--begin::Global Theme Styles -->
    <link href="{{ asset('assets/vendors/base/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/demo/default/base/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles -->
    <!--begin::Page Vendors Styles -->
    <link href="{{ asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Page Vendors Styles -->
    <!--begin::Custom Styles -->
    <link href="{{ asset('assets/global.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Custom Styles -->
  </head>
  <body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
    <div class="m-grid m-grid--hor m-grid--root m-page">
      <!-- BEGIN: Header -->
      <header id="m_header" class="m-grid__item    m-header " m-minimize-offset="200" m-minimize-mobile-offset="200">
          <div class="m-container m-container--fluid m-container--full-height">
          <div class="m-stack m-stack--ver m-stack--desktop">

              <!-- BEGIN: Brand -->
              <div class="m-stack__item m-brand  m-brand--skin-dark ">
              <div class="m-stack m-stack--ver m-stack--general">
                  <div class="m-stack__item m-stack__item--middle m-brand__logo">
                  <a href="#" class="m-brand__logo-wrapper"><h2>HARUTO</h2></a>
                  </div>
                  <div class="m-stack__item m-stack__item--middle m-brand__tools">

                  <!-- BEGIN: Left Aside Minimize Toggle -->
                  <a href="javascript:;" id="m_aside_left_minimize_toggle"
                      class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block">
                      <span></span>
                  </a>

                  <!-- END -->

                  <!-- BEGIN: Responsive Aside Left Menu Toggler -->
                  <a href="javascript:;" id="m_aside_left_offcanvas_toggle"
                      class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
                      <span></span>
                  </a>
                  <!-- END -->
                  </div>
              </div>
              </div>

              <!-- END: Brand -->
              <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
              <!-- BEGIN: Horizontal Menu -->
              <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-dark"
                      id="m_aside_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
              <div id="m_header_menu"
                      class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
                <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
                  <li class="m-menu__item m-menu__item--submenu m-menu__item--rel">
                      <h4 class="m-menu__link-text sub_header_title">@yield('page_title')</h4>
                  </li>
                </ul>
              </div>
              <!-- END: Horizontal Menu -->

              <!-- BEGIN: Topbar -->
              <div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general">
                  <div class="m-stack__item m-topbar__nav-wrapper"
                      style="vertical-align: middle; padding-right: 10px;">
                  <ul class="m-topbar__nav m-nav m-nav--inline">
                      <li id="m_quick_sidebar_toggle" class="m-nav__item">
                      <a href="{{ url('/logout') }}" class="m-nav__link m-dropdown__toggle">
                          <span class="m-nav__link-icon"><i class="flaticon-logout"></i></span>
                      </a>
                      </li>
                  </ul>
                  </div>
              </div>
              <!-- END: Topbar -->
              </div>
          </div>
          </div>
      </header>
      <!-- END: Header -->

      <!-- begin::Body -->
      <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn"><i
          class="la la-close"></i></button>
        <div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">

          <!-- BEGIN: Aside Menu -->
          <div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark "
              m-menu-vertical="1" m-menu-scrollable="1" m-menu-dropdown-timeout="500" style="position: relative;">
            <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow">
              <li class="m-menu__item <?php if (isset($main) && $main == MAIN_HOME) echo 'm-menu__item--active';?>" aria-haspopup="true">
                <a href="{{ url('/dashboard') }}" class="m-menu__link ">
                  <span class="m-menu__item-here"></span>
                  <i class="m-menu__link-icon fa fa-home"></i>
                  <span class="m-menu__link-text">Dashboard</span>
                </a>
              </li>
              <li class="m-menu__section ">
                <h4 class="m-menu__section-text">会員用機能</h4>
                <i class="m-menu__section-icon flaticon-more-v2"></i>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                  aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/coupon') }}" class="m-menu__link m-menu__toggle">
                    <span class="m-menu__item-here"></span>
                    <i class="m-menu__link-icon fa fa-yen-sign"></i>
                    <span class="m-menu__link-text">クーポン発行</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                  aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/topic') }}" class="m-menu__link m-menu__toggle">
                    <span class="m-menu__item-here"></span>
                    <i class="m-menu__link-icon fa fa-list-ul"></i>
                    <span class="m-menu__link-text">トピック</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/shop') }}" class="m-menu__link m-menu__toggle">
                    <span class="m-menu__item-here"></span>
                    <i class="m-menu__link-icon fa fa-warehouse"></i>
                    <span class="m-menu__link-text">ショップ登録・編集</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                    aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/notice') }}" class="m-menu__link m-menu__toggle">
                    <span class="m-menu__item-here"></span>
                    <i class="m-menu__link-icon fa fa-comment-dots"></i>
                    <span class="m-menu__link-text">お知らせ送信</span>
                </a>
              </li>
              <li class="m-menu__section ">
                <h4 class="m-menu__section-text">代理店用機能</h4>
                <i class="m-menu__section-icon flaticon-more-v2"></i>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                  aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/agency_usages') }}" class="m-menu__link m-menu__toggle">
                  <span class="m-menu__item-here"></span>
                  <i class="m-menu__link-icon fa fa-list-alt"></i>
                  <span class="m-menu__link-text">代理店利用規約</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                  aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/coupon_application') }}" class="m-menu__link m-menu__toggle">
                  <span class="m-menu__item-here"></span>
                  <i class="m-menu__link-icon fa fa-check-double"></i>
                  <span class="m-menu__link-text">代理店からの申請</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                    aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/tossup') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-briefcase"></i>
                <span class="m-menu__link-text">トスアップ申請</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                  aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/atec') }}" class="m-menu__link m-menu__toggle">
                  <span class="m-menu__item-here"></span>
                  <i class="m-menu__link-icon fa fa-exchange-alt"></i>
                  <span class="m-menu__link-text">アーテック通信</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                    aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/manual') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-list-alt"></i>
                <span class="m-menu__link-text">施工マニュアル</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/suggest_tools') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-list-alt"></i>
                <span class="m-menu__link-text">提案ツール</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_ITEM) echo 'm-menu__item--active';?>"
                aria-haspopup="true" m-menu-submenu-toggle="hover">
              <a href="{{ url('/manager') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-id-card"></i>
                <span class="m-menu__link-text">代理店デバイス管理</span>
              </a>
            </li>
              <li class="m-menu__section ">
                <h4 class="m-menu__section-text">マスター管理機能</h4>
                <i class="m-menu__section-icon flaticon-more-v2"></i>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_MEMBER) echo 'm-menu__item--active';?>"
                  aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/master/customer') }}" class="m-menu__link m-menu__toggle">
                  <span class="m-menu__item-here"></span>
                  <i class="m-menu__link-icon fa fa-users"></i>
                  <span class="m-menu__link-text">顧客一覧</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_PRIVACY) echo 'm-menu__item--active';?>"
                  aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/master/carrying') }}" class="m-menu__link m-menu__toggle">
                  <span class="m-menu__item-here"></span>
                  <i class="m-menu__link-icon fa fa-list-ul"></i>
                  <span class="m-menu__link-text">施工履歴一覧</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_PRIVACY) echo 'm-menu__item--active';?>"
                    aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/master/inquiry') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-comments"></i>
                  <span class="m-menu__link-text">業務連絡一覧確認</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_PRIVACY) echo 'm-menu__item--active';?>"
                    aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/master/policy') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-comments"></i>
                <span class="m-menu__link-text">利用規約</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_PRIVACY) echo 'm-menu__item--active';?>"
                    aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/master/faq') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-comments"></i>
                <span class="m-menu__link-text">よくある質問</span>
                </a>
              </li>
              <li class="m-menu__item m-menu__item--submenu <?php if (isset($main) && $main == MAIN_PRIVACY) echo 'm-menu__item--active';?>"
                aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/master/carrying_goods') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-comments"></i>
                <span class="m-menu__link-text">施工商品一覧</span>
                </a>
              </li>
              @if(Auth::user()->supervisor == 1)
              <li class="m-menu__section ">
                <h4 class="m-menu__section-text">管理者</h4>
                <i class="m-menu__section-icon flaticon-more-v2"></i>
              </li>
              <li class="m-menu__item m-menu__item--submenu" aria-haspopup="true" m-menu-submenu-toggle="hover">
                <a href="{{ url('/master/admins') }}" class="m-menu__link m-menu__toggle">
                <span class="m-menu__item-here"></span>
                <i class="m-menu__link-icon fa fa-comments"></i>
                <span class="m-menu__link-text">管理者一覧</span>
                </a>
              </li>
              @endif
            </ul>
          </div>
          <!-- END: Aside Menu -->
        </div>

        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
          <div class="m-content">
            @section('content')
            @show
          </div>
        </div>
      </div>

      <!-- begin::Footer -->
      <footer class="m-grid__item    m-footer ">
          <div class="m-container m-container--fluid m-container--full-height m-page__container">
              <div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
                  <div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last">
                      <span class="m-footer__copyright">2021 &copy; HARUTO APP</span>
                  </div>
              </div>
          </div>
      </footer>
      <!-- end::Footer -->
    </div>
    <!-- end:: Page -->

    <!-- begin::Scroll Top -->
    <div id="m_scroll_top" class="m-scroll-top">
      <i class="la la-arrow-up"></i>
    </div>
    <!-- end::Scroll Top -->

    <!--begin::Global Theme Bundle -->
    <script src="{{ asset('assets/vendors/base/vendors.bundle.js') }}"></script>
    <script src="{{ asset('assets/demo/default/base/scripts.bundle.js') }}"></script>
    <!--end::Global Theme Bundle -->

    <!--begin::Page Vendors -->
    <script src="{{ asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <!--end::Page Vendors -->

    <!--begin::datepicker Scripts -->
    <script src="{{ asset('assets/demo/default/custom/crud/forms/widgets/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-datepicker.ja.js') }}"></script>
    <!--end::datepicker Scripts -->

    <script src="{{ asset('assets/demo/default/custom/crud/forms/validation/form-controls.js') }}"></script>

    <!--begin::Page Scripts -->
    @section('script')
    @show
  </body>
</html>
