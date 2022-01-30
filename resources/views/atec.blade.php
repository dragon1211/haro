@extends('layouts.app')

@section('title', __('アーテック通信一覧'))
@section('page_title', __('アーテック通信一覧'))

@section('content')
<form class="m-form m-form--fit m-form--label-align-right" id="del_form" action="/atec/delete" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type=hidden id="del_no" name="del_no" />
<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-12 m--padding-bottom-15">
                <a href="{{ url('/atec/edit') }}" class="btn btn-primary pull-right">
                    <span>
                        <i class="fa flaticon-add-circular-button"></i>
                        <span>&nbsp;&nbsp;アーテック通信追加&nbsp;&nbsp;</span>
                    </span>
                </a>
            </div>
            <div class="col-md-12">
                <table width="100%" class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>通信ジャンル</td>
                            <td>アーテック通信タイトル</td>
                            <td>アーテック通信詳細</td>
                            <td>日付</td>
                            <td>画像</td>
                            <td>動作</td>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($atecs as $ind => $u)
                        <tr class="row-{{ (($atecs->currentPage() - 1) * $per_page + $ind + 1)%2 }}" ref="{{ $u->id }}">
                            <td>{{ ($atecs->currentPage() - 1) * $per_page + $ind + 1 }}</td>
                            <td>{{ $u->kind }}</td>
                            <td>{{ $u->title }}</td>
                            <td>{{ $u->content }}</td>
                            <td>{{ $u->created_at }}</td>
                            <td>
                                <div><img src="{{ $u->thumbnail ? $u->thumbnail : $u->image_path }}" style="height:50px"/></div>
                            </td>
                            <td>
                                <div class="p-action">
                                    <a href="/atec/edit/{{ $u->id }}" class="btn btn-outline-primary m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-edit"></i></a>
                                    <a href="#" onclick="delete_confirm('{{ $u->id }}');" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-trash"></i></a>
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
                <div class="pull-right">{{ $atecs->links() }}</div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('script')
<script>
        function delete_confirm(del_no){

            swal({title:"本当に削除しますか？",
                    text:"削除すると元に戻せません",
                    showCancelButton:!0,
                    confirmButtonText:"はい",
                    cancelButtonText:"キャンセル",
                })
                .then(function(e){
                    if (e.value == 1)
                    {
                        $('#del_no').val(del_no);
                        $('#del_form').submit();
                    }
                })

        }
</script>
@endsection
