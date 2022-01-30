@extends('layouts.app')

@section('title', __('おしらせ追加'))
@section('page_title', __('おしらせ追加'))

@section('content')
<div class="m-portlet m-portlet--tab">
    <!--begin::Form-->
    <form class="m-form m-form--fit m-form--label-align-right" action="/topic/update" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="no" value="{{ isset($topic) ?  $topic->id : '' }}" />
        <div class="m-portlet__body">
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">トピック</label>
                <div class="col-6">
                    <textarea class="form-control m-input m-input--air" name="title"
                    required data-msg-required="トピックを選択してください.">{{ isset($topic) ? $topic->title : '' }}</textarea>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">トピック詳細</label>
                <div class="col-6">
                    <textarea class="form-control m-input m-input--air" name="content" required
                    data-msg-required="トピック詳細を選択してください." rows="3">{{ isset($topic) ? $topic->content : '' }}</textarea>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="exampleInputEmail1" class="col-2 col-form-label">画像</label>
                <div class="col-6">
                    <div class="input-group">
                        <input type="text" class="form-control m-input" name="thumb" id="path_dsp"
                            value="{{ isset($topic) ? $topic->image : '' }}" required
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
                        @if (isset($topic))
                          <img src="{{ asset( $image_url.$topic->image ) }}" style="width: 480px;height:320px">
                        @endif
                    </div>
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
                      <a href="{{ url('/topic') }}" class="btn btn-secondary btn-block">Cancel</a>
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

</script>
@endsection
