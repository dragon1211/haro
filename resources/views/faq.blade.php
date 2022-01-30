@extends('layouts.app')

@section('title', __('よくある質問'))
@section('page_title', __('よくある質問'))

@section('content')
<div class="m-portlet">
  <form action={{url('/master/save_faq')}} method="POST">
    @csrf
    <div class="m-portlet__body">
      <ul class="nav nav-tabs  m-tabs-line" role="tablist">
        <li class="nav-item m-tabs__item">
          <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_1_1" role="tab">ハルトコーティング</a>
        </li>
        <li class="nav-item m-tabs__item">
          <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_1_3" role="tab">ハルトコーティング TypeF</a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="m_tabs_1_1" role="tabpanel">
          <textarea class="summernote" name="policy">{!! isset($data) ? $data->policy : '' !!}</textarea>
        </div>
        <div class="tab-pane" id="m_tabs_1_3" role="tabpanel">
          <textarea class="summernote" name="privacy">{!! isset($data) ? $data->privacy : '' !!}</textarea>
        </div>
      </div>
      <div class="row m--margin-top-15">
        <div class="col-2 offset-5">
          <button type="submit" class="btn btn-primary btn-block">Save</button>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@section('script')
<script>
  $(function() {
    $(".summernote").summernote({
      height:500,
    });
  });
</script>
@endsection
