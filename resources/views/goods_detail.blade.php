@extends('layouts.app')

@section('title', __('施工商品詳細'))
@section('page_title', __('施工商品詳細'))

@section('content')
<div class="m-portlet m-portlet--tab">
<form class="m-form m-form--fit m-form--label-align-right" action="/master/carrying_goods/detail/{{$goods->id}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="no" value="{{ $goods->id }}" />
        <div class="m-portlet__body">
          <div class="form-group row">
            <div class="offset-2 col-md-6">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <td>サイズ</td>
                    <td>価格</td>
                    <td></td>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($goods->details as $d)
                    <tr>
                      <td>{{ $d->name }}</td>
                      <td>{{ $d->price }}</td>
                      <td>
                        <button type="button" class="btn btn-danger btn-block" onclick="deleteDetails({{ $d->id }})">削除</button>
                      </td>
                    </tr>
                  @endforeach
                  <tr>
                    <td>
                      <input class="form-control m-input" type="text" name="name" required data-msg-required="サイズ名を選択してください.">
                    </td>
                    <td>
                      <input class="form-control m-input" type="number" name="price" required data-msg-required="数を入力してください.">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions">
                <div class="row">
                    <div class="col-2 offset-2">
                      <button type="submit" class="btn btn-success btn-block">OK</button>
                    </div>
                    <div class="col-2">
                      <a href="{{ url('/master/carrying_goods/edit/'.$goods->id) }}" class="btn btn-secondary btn-block">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    $(function() {
        $('form').validate();
    })
    function deleteDetails(id) {
      location.href = "/master/carrying_goods/detail/delete/" + id;
    }
</script>
@endsection
