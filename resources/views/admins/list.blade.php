@extends('layouts.app')

@section('title', __('管理者一覧'))
@section('page_title', __('管理者一覧'))

@section('content')
<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-12 m--padding-bottom-15">
                <a href="{{ url('/master/admins/create') }}" class="btn btn-primary">
                  <span>
                      <i class="fa flaticon-add-circular-button"></i>
                      <span>&nbsp;&nbsp;管理者追加&nbsp;&nbsp;</span>
                  </span>
                </a>
            </div>
            <div class="col-md-12">
                <table width="100%" class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>名前</td>
                            <td>メール</td>
                            <td>動作</td>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($admins as $ind => $u)
                        <tr class="row-{{ (($admins->currentPage() - 1) * $per_page + $ind + 1)%2 }}" ref="{{ $u->id }}">
                            <td>{{ ($admins->currentPage() - 1) * $per_page + $ind + 1 }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                <div class="p-action">
                                    <a href="/master/admins/{{ $u->id }}/edit" class="btn btn-outline-primary m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-edit"></i></a>
                                    <a href="#" onclick="delete_confirm('{{ $u->id }}');" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="pull-right">{{ $admins->links() }}</div>
            </div>
        </div>
    </div>
</div>
<form class="m-form m-form--fit m-form--label-align-right" name="del_form" id="del_form" action="" method="POST" enctype="multipart/form-data">
  {{ csrf_field() }}
  <input name="_method" type="hidden" value="DELETE">
</form>
@endsection

@section('script')
<script>
        function delete_confirm(del_id){
          swal({
            title:"本当に削除しますか？",
            text:"削除すると元に戻せません",
            showCancelButton:!0,
            confirmButtonText:"はい",
            cancelButtonText:"キャンセル",
          }).then(function(e){
            if (e.value == 1)
            {
              document.del_form.action = "/master/admins/" + del_id;
              $('#del_form').submit();
            }
          })
        }
</script>
@endsection
