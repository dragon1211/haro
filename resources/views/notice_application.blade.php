@extends('layouts.app')

@section('title', __('おしらせ申請一覧'))
@section('page_title', __('おしらせ申請一覧'))

@section('content')
<form class="m-form m-form--fit m-form--label-align-right" id="agree_form" action="/notice_application/agree" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type=hidden id="agree_no" name="agree_no" />
    <input type=hidden id="agree" name="agree" />
<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
        <div class="row">
                <div class="col-md-12 m--padding-bottom-15">
                        <a href="{{ url('/coupon_application') }}" class="btn btn-secondary m-btn--square pull-left">
                            <span>
                                <span>クーポン申請一覧</span>
                            </span>
                        </a>
                        <a href="" class="btn btn-primary m-btn--square pull-left">
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
                            <td>おしらせジャンル</td>
                            <td>おしらせタイトル</td>
                            <td>おしらせ詳細</td>
                            <td>対象ショップ</td>
                            <td>日付</td>
                            <td>画像</td>
                            <td>動作</td>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($notices as $ind => $u)
                        @if ($u->shop != null)
                            <tr class="row-{{ (($notices->currentPage() - 1) * $per_page + $ind + 1)%2 }}" ref="{{ $u->id }}">
                                <td>{{ ($notices->currentPage() - 1) * $per_page + $ind + 1 }}</td>
                                <td>{{ $u->kind }}</td>
                                <td>{{ $u->title }}</td>
                                <td>{{ $u->content }}</td>
                                <td>
                                    @if ($u->shop_id != 0)
                                        {{ $u->shop->name }}
                                    @else
                                        全員
                                    @endif
                                </td>
                                <td>{{ $u->created_at }}</td>
                                <td>
                                    <div><img src="{{ $u->thumbnail ? $u->thumbnail : $u->image_path }}" style="height:50px"/></div>
                                </td>
                                <td width="170">
                                    <div class="p-action">
                                        <a href="#" onclick="agree_confirm('{{ $u->id }}');" class="btn btn-outline-primary">承認</a>
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
                <div class="pull-right">{{ $notices->links() }}</div>
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
                text:"承認時に通知が送信されます。",
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
                text:"非承認時に通知が送信されません。",
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
