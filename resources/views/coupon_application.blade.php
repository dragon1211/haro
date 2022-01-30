@extends('layouts.app')

@section('title', __('クーポン申請一覧'))
@section('page_title', __('クーポン申請一覧'))

@section('content')
<form class="m-form m-form--fit m-form--label-align-right" id="agree_form" action="/coupon_application/agree" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type=hidden id="agree_no" name="agree_no" />
    <input type=hidden id="agree" name="agree" />
<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-12 m--padding-bottom-15">
                <a href="" class="btn btn-primary m-btn--square pull-left">
                    <span>
                        <span>クーポン申請一覧</span>
                    </span>
                </a>
                <a href="{{ url('/notice_application') }}" class="btn btn-secondary m-btn--square pull-left">
                    <span>
                        <span>おしらせ申請一覧</span>
                    </span>
                </a>
            </div>
            <div class="col-md-12">
                <table width="100%" class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>クーポン名</td>
                            <td>クーポン内容</td>
                            <td>有効期限</td>
                            <td>ショップ名</td>
                            <td>ショップエリア</td>
                            <td>ショップエリア詳細</td>
                            <td>代理店名</td>
                            <td>再使用</td>
                            <td>画像</td>
                            <td>動作</td>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($coupons as $ind => $u)
                        @if ($u->shop != null)
                            <tr class="row-{{ (($coupons->currentPage() - 1) * $per_page + $ind + 1)%2 }}" ref="{{ $u->id }}">
                                <td>{{ ($coupons->currentPage() - 1) * $per_page + $ind + 1 }}</td>
                                <td>{{ $u->title }}</td>
                                <td>
                                {{ $u->amount }}
                                @if ($u->unit == 0)
                                    円引き
                                @else
                                    ％引き
                                @endif
                                </td>
                                <td>{{ $u->from_date }} ~ {{ $u->to_date }}</td>
                                @if ($u->shop_id != 0)
                                    <td>{{ $u->shop->name }}</td>
                                    <td>{{ $u->shop->a_province }}</td>
                                    <td>{{ $u->shop->a_detail }}</td>
                                    <td>{{ $u->shop->brand }}</td>
                                @else
                                    <td>共通</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif
                                <td>
                                    @if ($u->reuse == 0)
                                    一回きり
                                    @else
                                    期間内無制限
                                    @endif
                                </td>
                                <td>
                                    <div><img src="{{ $u->thumbnail ? $u->thumbnail : $u->image_path }}" style="height:50px"/></div>
                                </td>
                                <td  width="170">
                                    <div class="p-action">
                                        <a href="#" onclick="agree_confirm('{{ $u->id }}');" class="btn btn-outline-primary ">承認</a>
                                        <a href="#" onclick="disagree_confirm('{{ $u->id }}');" class="btn btn-outline-primary ">非承認</a>
                                    </div>
                                </td>
                            </tr>
                        @endif 
                    @empty
                        <tr><td colspan="100" class="no-items">検索結果がないです.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="pull-right">{{ $coupons->links() }}</div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('script')
<script>
        function agree_confirm(agree_no){
            swal({title:"承認しますか？",
                text:"承認するとクーポンが発行されます。",
                showCancelButton:!0,
                confirmButtonText:"はい",
                cancelButtonText:"いいえ",
            })
            .then(function(e) {
                if (e.value == 1) {
                    $('#agree_no').val(agree_no);
                    $('#agree').val(1);
                    $('#agree_form').submit();
                }
            })
        }

        function disagree_confirm(agree_no){
            swal({title:"非承認しますか？",
                text:"非承認するとクーポンが発行されません。",
                showCancelButton:!0,
                confirmButtonText:"はい",
                cancelButtonText:"いいえ",
            })
            .then(function(e) {
                if (e.value == 1) {
                    $('#agree_no').val(agree_no);
                    $('#agree').val(2);
                    $('#agree_form').submit();
                }
            })
        }
</script>
@endsection
