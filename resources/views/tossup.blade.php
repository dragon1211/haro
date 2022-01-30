@extends('layouts.app')

@section('title', __('トスアップ申請一覧'))
@section('page_title', __('トスアップ申請一覧'))

@section('content')
<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-12">
                <table width="100%" class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>申請内容</td>
                            <td>申請店舗</td>
                            <td>ショップエリア</td>
                            <td>ショップエリア詳細</td>
                            <td>代理店名</td>
                            <td>日付</td>
                            <td>動作</td>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($tossups as $ind => $u)
                        <tr class="row-{{ (($tossups->currentPage() - 1) * $per_page + $ind + 1)%2 }}" ref="{{ $u->id }}">
                            <td>{{ ($tossups->currentPage() - 1) * $per_page + $ind + 1 }}</td>
                            <td>
                                {{ $u->content }}
                            </td>
                            <td>{{ $u->shopO->name }}</td>
                            <td>{{ $u->shopO->a_province }}</td>
                            <td>{{ $u->shopO->a_detail }}</td>
                            <td>{{ $u->shopO->brand }}</td>
                            <td>{{ $u->created_at }}</td>
                            <td>
                                <div class="p-action">
                                    <a href="#" class="btn btn-outline-primary btn-sm" onclick ="tossup_record('{{ $u->id }}')">トスアップ</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="100" class="no-items">検索結果がないです.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="pull-right">{{ $tossups->links() }}</div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="m_modal_shop_select" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">トスアップ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="m-form m-form--fit m-form--label-align-right" id="toss_form" action="/tossup/tossup" method="POST" enctype="multipart/form-data">
                <input type=hidden id="toss_no" name="toss_no"/>
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="form-control-label">ショップを選択してください.</label>
                        <select class="form-control m-input" name="shop">
                            @foreach ($shops as $ind => $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary ">はい</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">いいえ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function tossup_record(toss_no){
        $('#toss_no').val(toss_no);
        $('#m_modal_shop_select').modal('show');
    }
</script>
@endsection
