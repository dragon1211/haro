@extends('layouts.app')

@if ($type == 0)
  @section('title', __('施工マニュアル一覧'))
  @section('page_title', __('施工マニュアル一覧'))
@elseif ($type == 1)
  @section('title', __('提案ツール一覧'))
  @section('page_title', __('提案ツール一覧'))
@else
  @section('title', __('代理店利用規約'))
  @section('page_title', __('代理店利用規約'))
@endif


@section('content')
<div class="m-portlet">
    <div class="m-portlet__body">
        <div class="row">
            @if (isset($data) && count($data) > 0)
                @foreach ($data as $key => $v)
                    <div class="col-3 mb-3 p-2">
                        <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-success alert-dismissible text-center" role="alert">
                            <button type="button" class="close" onclick="delete_manual({{$v->id}}, {{$type}})">
                            </button>
                            <p><i class="fa fa-file-pdf" style="font-size: 40px;"></i></p>
                            <a href="{{$v->url}}">{{$v->display_name}}</a>

                        </div>
                        <div class="col-12 d-flex justify-content-center">
                            @if ($key > 0)
                                <button type="button" class="btn btn-info p-3" onclick="reorder({{ $data }}, {{ $type }}, {{ $key }}, 0)">
                                    <i class="la la-arrow-left"></i>
                                </button>
                            @endif

                            @if ($key > 0 && $key < count($data) - 1)
                                <div class="col-1">
                                </div>
                            @endif

                            @if ($key < count($data) - 1)
                                <button type="button" class="btn btn-info p-3" onclick="reorder({{ $data }}, {{ $type }}, {{ $key }}, 1)">
                                    <i class="la la-arrow-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>                
                @endforeach
            @else
                <div class="col-12">
                    <div class="m-alert m-alert--icon alert alert-danger" role="alert">
                        <div class="m-alert__icon">
                            <i class="la la-warning"></i>
                        </div>
                        <div class="m-alert__text">
                            <h5>登録された{{$type == 0 ? '施工マニュアル' : ($type == 1 ? '提案ツール' : '代理店利用規約')}}がないです.</h5>
                            {{$type == 0 ? '施工マニュアル' : ($type == 1 ? '提案ツール' : '代理店利用規約')}}を登録してください.
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="row m--margin-top-15">
            <div class="col-2 offset-5">
                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#m_modal_1">登録</button>
            </div>
        </div>
    </div>
  </div>
  <div class="modal fade" id="m_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{$type == 0 ? '施工マニュアル' : ($type == 1 ? '提案ツール' : '代理店利用規約')}}登録</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="m-form m-form--fit m-form--label-align-right"
                        action="{{ url('/manual/add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="form-group m-form__group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile"
                                    name="file" accept="application/pdf"
                                    required data-msg-required="登録するファイルを選択してください。">
                                <label class="custom-file-label" for="customFile">登録するファイルを選択してください。</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    function delete_manual(id, type) {
        if (!id) return;
        message = '施工マニュアルを削除しますか?';
        if (type == 0) {
            message = '施工マニュアルを削除しますか?';
        } else if (type == 1) {
            message = '提案ツールを削除しますか?';
        } else {
            message = '代理店利用規約を削除しますか?';
        }

        if (confirm(message)) {
            location.href="{{url('/manual/delete')}}" + '/' + id;
        }
    }

    $(function() {
        $('form').validate();
        $('#submit').on('click', function() {
            $('form').submit();
        });
    });

    function reorder(details, type, currentIndex, order) {
        isNextOrder = false;
        newOrder = 0;
        currentOrder = 0;

        if (order == 0) {
            if (currentIndex == 0) {
                return;
            } else {
                currentOrder = details[currentIndex].order_no;
                newOrder = details[currentIndex - 1].order_no;
            }
        } else {
            if (currentIndex >= details.length - 1) {
                return;
            } else {
                currentOrder = details[currentIndex].order_no;
                newOrder = details[currentIndex + 1].order_no;
            }
        }

        $.ajax({
            url: "/manual/edit/" + type + "/" + currentOrder + "/" + newOrder,
            type: "GET",
            async: false,
            data: {
            },
            success: function (data) {
                window.location.reload();
            }
        })
    }
</script>
@endsection
