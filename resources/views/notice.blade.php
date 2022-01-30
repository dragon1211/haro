@extends('layouts.app')

@section('title', __('お知らせ一覧'))
@section('page_title', __('お知らせ一覧'))

@section('content')
  <form class="m-form m-form--fit m-form--label-align-right" id="del_form" action="/notice/delete" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type=hidden id="del_no" name="del_no" />
  </form>
  <div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-12 m--padding-bottom-15">
                <a href="{{ url('/notice/edit') }}" class="btn btn-primary">
                  <span>
                    <i class="fa flaticon-add-circular-button"></i>
                    <span>&nbsp;&nbsp;おしらせ追加&nbsp;&nbsp;</span>
                  </span>
                </a>
                <form class="navbar-form navbar-right" role="search" action="{{ url('/notice') }}">
                  <div class="form-group m-form__group pull-right" style="width: 60%">
                    <div class="input-group">
                      <input type="text" class="form-control" name="shop" value="{{ $old['shop'] }}" placeholder="対象ショップ">
                      <input type="text" class="form-control" name="area" value="{{ $old['area'] }}" placeholder="ショップエリア">
                      <input type="text" class="form-control" name="brand" value="{{ $old['brand'] }}" placeholder="代理店名">
                      <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                          <span>
                            <i class="fa fa-search"></i>
                            <span>&nbsp;&nbsp;検 索&nbsp;&nbsp;</span>
                          </span>
                        </button>
                      </div>
                    </div>
                  </div>
                </form>
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
                            <td>ショップエリア</td>
                            <td>ショップエリア詳細</td>
                            <td>代理店名</td>
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
                            @if ($u->shop_id != 0)
                                <td>{{ $u->shop->name }}</td>
                                <td>{{ $u->shop->a_province }}</td>
                                <td>{{ $u->shop->a_detail }}</td>
                                <td>{{ $u->shop->brand }}</td>
                            @else
                                <td>全員</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                            <td>{{ $u->created_at }}</td>
                            <td>
                                <div><img src="{{ $u->thumbnail ? $u->thumbnail : $u->image_path }}" style="height:50px"/></div>
                            </td>
                            <td>
                              <div class="p-action">
                                  <a href="/notice/edit/{{ $u->id }}" class="btn btn-outline-primary m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-edit"></i></a>
                                  <a href="#" onclick="delete_confirm('{{ $u->id }}');" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-trash"></i></a>
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
