@extends('layouts.app')

@section('title', __((isset($goods)) ? '施工商品編集' : '施工商品追加'))
@section('page_title', __((isset($goods)) ? '施工商品編集' : '施工商品追加'))

@section('content')
<div class="m-portlet m-portlet--tab">
    <!--begin::Form-->
<form class="m-form m-form--fit m-form--label-align-right" action="/master/carrying_goods/update" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="no" value="{{ isset($goods) ?  $goods->id : '' }}" />
        <input type="hidden" name="page_no" value="{{ $page_no }}" />
        <div class="m-portlet__body">
            <div class="form-group m-form__group row">
                <label for="exampleSelect1" class="col-2 col-form-label">種類</label>
                <div class="col-9" style="margin-top: 8px;">
                  <label class="m-radio">
                    <input type="radio" name="type" value="0" @if (!isset($goods) || (isset($goods) && $goods->type == 0)) checked="checked" @endif> ハルトコーティング&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span></span>
                  </label>
                  <label class="m-radio">
                    <input type="radio" name="type" value="1" @if (isset($goods) && $goods->type == 1) checked="checked" @endif> ハルトコーティングtypeF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span></span>
                  </label>
                  <label class="m-radio">
                    <input type="radio" name="type" value="2" @if (isset($goods) && $goods->type == 2) checked="checked" @endif> その他&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span></span>
                  </label>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">施工商品名</label>
                <div class="col-6">
                    <input class="form-control m-input" type="text" name="name" value="{{ isset($goods) ? $goods->name : '' }}"
                    required data-msg-required="施工商品名を選択してください.">
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="example-text-input" class="col-2 col-form-label">価格</label>
                <div class="col-6">
                    <input class="form-control m-input" type="number" name="price" value="{{ isset($goods) ? $goods->price : '' }}"
                    data-msg-required="価格を選択してください." data-msg-number='数を入力してください'>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <label for="exampleInputEmail1" class="col-2 col-form-label">画像</label>
                <div class="col-6">
                    <div class="input-group">
                        <input type="text" class="form-control m-input" name="thumb" id="path_dsp"
                            value="{{ isset($goods) ? $goods->image : '' }}" required
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
                        @if (isset($goods))
                            <img src="{{ asset( $image_url.$goods->image ) }}" style="width: 480px;height:320px">
                        @endif
                    </div>
                </div>
            </div>
            @if (isset($goods))
            <div class="form-group row">
              <div class="col-md-2 col-sm-6 offset-2">
                <button type="button" class="btn btn-success btn-block" onclick="goToDetails()">サイズ追加</button>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-1 offset-1">

              </div>
              <div class="col-md-6">
                <table class="table table-bordered table-striped" id="datatable">
                  <thead>
                    <tr>
                      <td>サイズ</td>
                      <td>価格</td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($goods->details as $key => $d)
                      <tr>
                        <td id="goodsDetailName-{{ $d->id }}">{{ $d->name }}</td>
                        <td id="goodsDetailPrice-{{ $d->id }}">{{ $d->price }}</td>

                        <td>
                          @if ($key > 0)
                            <button type="button" class="btn btn-info" onclick="reorder({{ $goods->details }}, {{ $key }}, 0)">
                              <i class="la la-arrow-up"></i>
                            </button>
                          @endif
                          @if ($key < count($goods->details) - 1)
                            <button type="button" class="btn btn-info" onclick="reorder({{ $goods->details }}, {{ $key }}, 1)">
                              <i class="la la-arrow-down"></i>
                            </button>
                          @endif
                        </td>
                        <input type="hidden" name="orders[]" id="order-{{ $key }}" value="{{ $d->order_no }}">
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endif
            <div class="form-group m-form__group row">
                <label for="exampleSelect1" class="col-2 col-form-label">同意文・確認事項</label>
                <div class="col-9" style="margin-top: 8px;">
                  <label class="m-radio">
                    <input type="radio" name="agree_kind" value="0" @if (!isset($goods) || (isset($goods) && $goods->agree_kind == 0)) checked="checked" @endif> ハルトコーティング&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span></span>
                  </label>
                  <label class="m-radio">
                    <input type="radio" name="agree_kind" value="1" @if (isset($goods) && $goods->agree_kind == 1) checked="checked" @endif> ハルトコーティングtypeF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span></span>
                  </label>
                  <label class="m-radio">
                    <input type="radio" name="agree_kind" value="2" @if (isset($goods) && $goods->agree_kind == 2) checked="checked" @endif> 小物&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span></span>
                  </label>
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
                        <a href="/master/carrying_goods?page={{$page_no}}" class="btn btn-secondary btn-block">Cancel</a>
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

    function goToDetails() {
      location.href = "/master/carrying_goods/detail/" + "{{ isset($goods) ?  $goods->id : '' }}";
    }  

    function reorder(details, currentIndex, order) {
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
            url: "/master/carrying_goods/edit/" + "{{ isset($goods) ?  $goods->id : '' }}/" + currentOrder + "/" + newOrder,
            type: "GET",
            async: false,
            data: {
            },
            success: function (data) {
                // $(".display").html(data);
                window.location.reload();
            }
        })
    }

    let table = document.getElementById('datatable');
    let editingTd;
    table.onclick = function(event) {
      let target = event.target.closest('.edit-cancel,.edit-ok');
      if (!table.contains(target)) return;

      if (target.className == 'edit-cancel') {
        finishTdEdit(editingTd.elem, false);
      } else if (target.className == 'edit-ok') {
        finishTdEdit(editingTd.elem, true);
      }
    }

    table.ondblclick = function(event) {
      let target = event.target.closest('td');
      if (!table.contains(target)) return;
      if (!target.id.includes('goodsDetailName-') && !target.id.includes('goodsDetailPrice-')) return;
      if (target.nodeName == 'TD') {
        if (editingTd) return;
        makeTdEditable(target);
      }
    }
    
    function makeTdEditable(td) {
      editingTd = {
        elem: td,
        data: td.innerHTML
      };

      td.classList.add('edit-td'); // td is in edit state, CSS also styles the area inside

      let textArea = document.createElement('textarea');
      textArea.style.width = td.clientWidth + 'px';
      textArea.style.height = td.clientHeight + 'px';
      textArea.className = 'edit-area';

      textArea.value = td.innerHTML;
      td.innerHTML = '';
      td.appendChild(textArea);
      textArea.focus();

      td.insertAdjacentHTML("beforeEnd",
        '<div class="edit-controls"><button class="edit-ok">OK</button><button class="edit-cancel">CANCEL</button></div>'
      );
    }

    function finishTdEdit(td, isOk) {
      if (isOk) {
        td.innerHTML = td.firstChild.value;
        if (td.id.includes('goodsDetailName-')) {
          let itemID = td.id.replace('goodsDetailName-', '');
          $.ajax({
              url: '/master/carrying_goods/edit/' + itemID + '/name',
              type: 'POST',
              async: false,
              data: {
                _token: "{{ csrf_token() }}",
                name: td.innerHTML,
              },
              success: function (data) {
                window.location.reload();
              },
            })

        } else if (td.id.includes('goodsDetailPrice-')) {
          let itemID = td.id.replace('goodsDetailPrice-', '');
          $.ajax({
              url: '/master/carrying_goods/edit/' + itemID + '/price',
              type: 'POST',
              async: false,
              data: {
                _token: "{{ csrf_token() }}",
                price: parseFloat(td.innerHTML).toString(),
              },
              success: function (data) {
                window.location.reload();
              },
            })
        }
      } else {
        td.innerHTML = editingTd.data;
      }
      td.classList.remove('edit-td');
      editingTd = null;
    }
</script>
@endsection
