@extends('layouts.app')

@section('title', __('施工商品一覧'))
@section('page_title', __('施工商品一覧'))

@section('content')
<form class="m-form m-form--fit m-form--label-align-right" id="del_form" action="/master/carrying_goods/delete" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type=hidden id="del_no" name="del_no" />
    <input type=hidden id="page_no" name="page_no" />
<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-12 m--padding-bottom-15">
              <a href="{{ url('/master/carrying_goods') }}" class="btn @if ($type == -1) btn-primary @endif">
                <span>
                  <span>&nbsp;&nbsp;すべて&nbsp;&nbsp;</span>
                </span>
              </a>
              <a href="{{ url('/master/carrying_goods/haruto') }}" class="btn @if ($type == 0) btn-primary @endif">
                <span>
                  <span>&nbsp;&nbsp;ハルトコーティング&nbsp;&nbsp;</span>
                </span>
              </a>
              <a href="{{ url('/master/carrying_goods/typef') }}" class="btn @if ($type == 1) btn-primary @endif">
                <span>
                  <span>&nbsp;&nbsp;ハルトコーティングtypeF&nbsp;&nbsp;</span>
                </span>
              </a>
              <a href="{{ url('/master/carrying_goods/other') }}" class="btn @if ($type == 2) btn-primary @endif">
                <span>
                  <span>&nbsp;&nbsp;その他&nbsp;&nbsp;</span>
                </span>
              </a>
              <a href="{{ url('/master/carrying_goods/edit') }}" class="btn btn-primary pull-right">
                <span>
                  <i class="fa flaticon-add-circular-button"></i>
                  <span>&nbsp;&nbsp;施工商品追加&nbsp;&nbsp;</span>
                </span>
              </a>
            </div>
            <div class="col-md-12">
                <table width="100%" class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>商品名</td>
                            <td>価格（税抜き）</td>
                            <td>画像</td>
                            <td>動作</td>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($goods as $ind => $u)
                        <tr class="row-{{ (($goods->currentPage() - 1) * $per_page + $ind + 1)%2 }}" ref="{{ $u->id }}">
                            <td>{{ ($goods->currentPage() - 1) * $per_page + $ind + 1 }}</td>
                            <td>{{ $u->name }}</td>
                            <td>
                              @if ($u->price)
                                {{ number_format($u->price) }}円
                              @else
                                @foreach ($u->details as $d)
                                  {{ number_format($d->price) }}円
                                  <br />
                                @endforeach
                              @endif
                            </td>
                            <td>
                                <div><img src="{{ $image_url.$u->image }}" style="height:50px"/></div>
                            </td>
                        <td>
                            <div class="p-action">
                                <a href="/master/carrying_goods/edit/{{ $u->id }}/{{ $goods->currentPage() }}" class="btn btn-outline-primary m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-edit"></i></a>
                                <a href="#" onclick="delete_confirm('{{ $u->id }}', '{{ $goods->currentPage() }}');" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-trash"></i></a>
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
                <div class="pull-right">{{ $goods->links() }}</div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('script')
<script>
        function delete_confirm(del_no, page_no) {
            swal({title:"本当に削除しますか？",
                    text:"削除すると元に戻せません",
                    showCancelButton:!0,
                    confirmButtonText:"はい",
                    cancelButtonText:"キャンセル",
                })
                .then(function(e) {
                    if (e.value == 1) {
                        $('#del_no').val(del_no);
                        $('#page_no').val(page_no);
                        $('#del_form').submit();
                    }
                })

        }
</script>
@endsection
