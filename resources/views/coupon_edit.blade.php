@extends('layouts.app')

@section('title', __(isset($coupon) ? 'クーポン編集' : 'クーポン追加'))
@section('page_title', __(isset($coupon) ? 'クーポン編集' : 'クーポン追加'))

@section('content')
<div class="m-portlet m-portlet--tab">
    <!--begin::Form-->
<form class="m-form m-form--fit m-form--label-align-right" action="/coupon/update" method="POST" enctype="multipart/form-data" id="theform">
        {{ csrf_field() }}
        <input type="hidden" name="no" value="{{ isset($coupon) ?  $coupon->id : '' }}" />
        <div class="m-portlet__body">
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label"></label>
                <div class="col-6">
                  <label class="m-radio">
                    <input type="radio" name="type" value="0" @if (!isset($coupon) || (isset($coupon) && $coupon->type == 0)) checked="checked" @endif> ハルト&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span></span>
                  </label>
                  <label class="m-radio">
                    <input type="radio" name="type" value="1" @if (isset($coupon) && $coupon->type == 1) checked="checked" @endif> ハルトtypeF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span></span>
                  </label>
                  <label class="m-radio">
                    <input type="radio" name="type" value="2" @if (isset($coupon) && $coupon->type == 2) checked="checked" @endif> その他
                    <span></span>
                  </label>
                </div>
              </div>
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">クーポン名</label>
                <div class="col-6">
                    <input class="form-control m-input" type="text" name="title" value="{{ isset($coupon) ? $coupon->title : '' }}"
                    required data-msg-required="クーポン名を選択してください.">
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">クーポン内容</label>
                <div class="col-6">
                    <textarea class="form-control m-input m-input--air" name="content" required
                    data-msg-required="クーポン内容を選択してください." rows="3">{{ isset($coupon) ? $coupon->content : '' }}</textarea>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">割引内容</label>
                <div class="col-4">
                    <input class="form-control m-input" type="text" name="amount" value="{{ isset($coupon) ? $coupon->amount : '' }}"
                    required data-msg-required="割引内容を選択してください.">
                </div>
                <div class="col-2">
                    <label class="m-radio">
                      @if (isset($coupon) && $coupon->unit == 0)
                        <input type="radio" name="unit" value="0" checked="checked">円引き
                      @elseif(!isset($counpon))
                        <input type="radio" name="unit" value="0" checked="checked">円引き
                      @else
                        <input type="radio" name="unit" value="0">円引き
                      @endif
                      <span></span>
                    </label>
                    <div class='col-1'></div>
                    <label class="m-radio">
                      @if (isset($coupon) && $coupon->unit == 1)
                        <input type="radio" name="unit" value="1" checked="checked">％引き
                      @else
                        <input type="radio" name="unit" value="1">％引き
                      @endif
                      <span></span>
                    </label>
                  </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="example-datetime-local-input" class="col-2 col-form-label">有効期限</label>
                <div class="col-3">
                    <input class="form-control m-input" type="date-local" name="from_date" id="from_date"
                    value="{{ isset($coupon) ? $coupon->from_date : '' }}" required data-msg-required="有効期限を選択してください."
                    {{ isset($coupon) ? 'readonly' : '' }}>
                </div>
                ~
                <div class="col-3">
                    <input class="form-control m-input" type="date-local" name="to_date" id="to_date"
                    value="{{ isset($coupon) ? $coupon->to_date : '' }}" required data-msg-required="有効期限を選択してください."
                    {{ isset($coupon) ? 'readonly' : '' }}>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="exampleSelect1" class="col-2 col-form-label">ショップ</label>
                <div class="col-6">
                    <select class="form-control m-input" name="shop" {{ isset($coupon) ? 'disabled' : '' }}>
                        @if (isset($coupon) && $coupon->shop_id == 0)
                            <option value="0" selected>共通</option>
                        @else
                            <option value="0">共通</option>
                        @endif
                        @foreach ($shops as $ind => $shop)
                            @if (isset($coupon) && $shop->id == $coupon->shop_id)
                                <option value="{{ $shop->id }}" selected>{{ $shop->name }}</option>
                            @else
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="exampleSelect1" class="col-2 col-form-label">再使用</label>
                <div class="col-6">
                    <select class="form-control m-input" name="reuse">
                        @if (isset($coupon) && $coupon->reuse == 0)
                            <option value="0" selected>一回きり</option>
                            <option value="1">期間内無制限</option>
                        @else
                          <option value="0">一回きり</option>
                          <option value="1" selected>期間内無制限</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="exampleInputEmail1" class="col-2 col-form-label">画像</label>
                <div class="col-6">
                    <div class="input-group">
                        <input type="text" class="form-control m-input" name="thumb" id="path_dsp"
                            value="{{ isset($coupon) ? $coupon->image : '' }}" required
                            placeholder="画像を選択してください." data-msg-required="画像を選択してください." readonly>

                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" onclick="$('#path').click();" >Browse...</button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="file" name="thumbnail" id="path" style="display: none;" accept="image/*">
            <div class="form-group m-form__group row">
                <div class="offset-2 col-md-9">
                    <div id="div_img">
                        @if (isset($coupon))
                            <img src="{{ asset( $image_url.$coupon->image ) }}" style="width: 480px;height:320px">
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions">
                <div class="row">
                    <div class="col-2 offset-2">
                        <button type="button" class="btn btn-success btn-block" onclick="onSubmit()">OK</button>
                    </div>
                    <div class="col-2">
                        <a href="{{ url('/coupon') }}" class="btn btn-secondary btn-block">Cancel</a>
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
        @if (!isset($coupon))
          $('#from_date').datepicker({
              language: 'ja',
              orientation:"bottom left"
          });

          $('#to_date').datepicker({
              language: 'ja',
              orientation:"bottom left"
          });
        @endif


        $('input[name="thumbnail"]').on('change', function() {
            $('input[name="change_thumb"]').val(1);
            if (this.files.length === 0)
                return;
            var f = this.files[0];
            var reader = new FileReader();
            reader.onload = (function (file) {
                return function(e) {
                    $('#div_img').addClass('img');
                    $('#div_img').html('<img src="' + e.target.result + '" style="height:100%">');
                };
            })(f);
            reader.readAsDataURL(f);
        });

        $('#path').on('change', function () {
            if ($(this).val() === '')
                return;
            $('#path_dsp').val($(this).val());
        });
    })

    function onSubmit() {
      if ($('#from_date').val() > $('#to_date').val()) {
        swal({title:"警告",
          text:"完了日を開始日以降に設定してください。",
          showCancelButton: false,
          confirmButtonText:"はい",
        });
        return;
      }
      $('#theform').submit();
    }

</script>
@endsection
