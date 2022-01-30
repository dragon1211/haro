@extends('layouts.app')

@section('title', __('代理店デバイス管理'))
@section('page_title', __('代理店デバイス管理'))

@section('content')
<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
      <div class="row">
        <div class="col-md-12">
          <form class="navbar-form navbar-right" role="search" action="{{ url('/manager') }}">
            <div class="form-group m-form__group pull-right" style="width: 75%">
              <div class="input-group">
                <input type="text" class="form-control" name="shop" value="{{ $old['shop'] }}" placeholder="店舗">
                <input type="text" class="form-control" name="brand" value="{{ $old['brand'] }}" placeholder="代理店名">
                <input type="text" class="form-control" name="province" value="{{ $old['province'] }}" placeholder="ショップエリア">
                <input type="text" class="form-control" name="county" value="{{ $old['county'] }}" placeholder="ショップエリア詳細">
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
      </div>
        <div class="row">
            <div class="col-md-12">
                <table width="100%" class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                        <tr>
                            <td>No</td>
                            <td width="120">申請日時</td>
                            <td>店舗</td>
                            <td>ショップエリア</td>
                            <td>ショップエリア詳細</td>
                            <td>代理店名</td>
                            <td>端末識別番号</td>
                            <td>ID</td>
                            <td>パスワード</td>
                            <td>許可状態</td>
                            <td>動作</td>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($managers as $ind => $u)
                        {{-- @php
                          $shop = $u->shop;
                          $area = $shop ? $shop->area : null;
                        @endphp --}}
                        <tr class="row-{{ (($managers->currentPage() - 1) * $per_page + $ind + 1)%2 }}" ref="{{ $u->id }}">
                            <td>{{ ($managers->currentPage() - 1) * $per_page + $ind + 1 }}</td>
                            <td>{{ date_format(new DateTime($u->created_at), '20y年 m月 d日') }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->a_province }}</td>
                            <td>{{ $u->a_detail }}</td>
                            <td>{{ $u->brand }}</td>
                            <td>{{ $u->device_id }}</td>
                            <td>{{ $u->lid }}</td>
                            <td>{{ $u->real_password }}</td>
                            <td>
                              @if ($u->allow == 1)
                                許可
                              @else
                                禁止
                              @endif
                            </td>
                            <td>
                              <div class="p-action">
                                @if ($u->allow == 0)
                                  <a href="/manager/allow/{{ $u->id }}" class="btn btn-outline-primary">許可</a>
                                @else
                                  <a href="/manager/allow/{{ $u->id }}" class="btn btn-outline-primary">禁止</a>
                                @endif
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
                <div class="pull-right">{{ $managers->links() }}</div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function manager_allow(id){
      swal({title:"本当に削除しますか？",
          text:"削除すると元に戻せません",
          showCancelButton:!0,
          confirmButtonText:"はい",
          cancelButtonText:"キャンセル",
      })
      .then(function(e){
          if (e.value == 1)
          {
              $('#id').val(id);
              $('#allow').val(allow);
              $('#form').submit();
          }
      })
    }
</script>
@endsection
