@extends('layouts.app')

@section('title', __('Dashboard'))
@section('page_title', __('Dashboard'))

@section('content')
<div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    <b>会員用機能</b>
                </h3>
            </div>
        </div>
    </div>
    <div class="m-portlet__body">
        <div class="m-demo__preview m-demo__preview--btn">
            <a href = "{{ url('/coupon') }}" class="btn btn-outline-brand btn-lg">クーポン発行</a>
            <a href = "{{ url('/shop') }}" class="btn btn-outline-success btn-lg">ショップ登録・編集</a>
            <a href = "{{ url('/notice') }}" class="btn btn-outline-info btn-lg">お知らせ送信</a>
        </div>
    </div>
</div>

<div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    <b>代理店用機能</b>
                </h3>
            </div>
        </div>
    </div>
    <div class="m-portlet__body">
        <div class="m-demo__preview m-demo__preview--btn">
            <a href = "{{ url('/coupon_application') }}" class="btn btn-outline-brand btn-lg">代理店からの申請</a>
            <a href = "{{ url('/tossup') }}" class="btn btn-outline-success btn-lg">トスアップ申請</a>
            <a href = "{{ url('/atec') }}" class="btn btn-outline-info btn-lg">アーテック通信</a>
            <a href = "{{ url('/manual') }}" class="btn btn-outline-warning btn-lg">施工マニュアル</a>
        </div>
    </div>
</div>

<div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    <b>マスター管理機能</b>
                </h3>
            </div>
        </div>
    </div>
    <div class="m-portlet__body">
        <div class="m-demo__preview m-demo__preview--btn">
            <a href = "{{ url('/master/customer') }}" class="btn btn-outline-brand btn-lg">顧客一覧</a>
            <a href = "{{ url('/master/carrying') }}" class="btn btn-outline-success btn-lg">施工履歴一覧</a>
            <a href = "{{ url('/master/inquiry') }}" class="btn btn-outline-info btn-lg">業務連絡一覧確認</a>
        </div>
    </div>
</div>
@endsection

@section('script')
@endsection
