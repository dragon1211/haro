@extends('layouts.app')

@section('title', __('管理者編集'))
@section('page_title', __('管理者編集'))

@section('content')
<div class="m-portlet m-portlet--tab">
    <!--begin::Form-->
    <form class="m-form m-form--fit m-form--label-align-right" action="/master/admins/{{ $admin->id }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="no" value="{{ isset($admin) ?  $admin->id : '' }}" />
        <input name="_method" type="hidden" value="PUT">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">名前</label>
                <div class="col-6">
                    <input class="form-control m-input" type="text" name="name" value="{{ isset($admin) ? $admin->name : '' }}"
                    required data-msg-required="おしらせジャンルを選択してください.">
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">メール</label>
                <div class="col-6">
                    <input class="form-control m-input" type="text" name="email" value="{{ isset($admin) ? $admin->email : '' }}"
                    required data-msg-required="おしらせタイトルを選択してください.">
                </div>
            </div>
            <div class="form-group m-form__group row">
              <label for="example-text-input" class="col-2 col-form-label">パスワード</label>
              <div class="col-6">
                  <input class="form-control m-input" type="password" name="password" value=""
                  data-msg-required="おしらせタイトルを選択してください.">
              </div>
            </div>
            <div class="form-group m-form__group row">
              <label for="example-text-input" class="col-2 col-form-label">パスワード確認</label>
              <div class="col-6">
                  <input class="form-control m-input" type="password" name="password_confirm" value=""
                  data-msg-required="おしらせタイトルを選択してください.">
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
                        <a href="{{ url('/master/admins') }}" class="btn btn-secondary btn-block">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('script')
<script>
  function onSubmit() {
    var pwd = $('input[name=password]').val();
    var cpwd = $('input[name=password_confirm]').val();
    if (pwd != '' &&  pwd != cpwd) {
      return;
    }
    $('#userform').submit();
  }
</script>
@endsection
