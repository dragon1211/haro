@extends('layouts.app')

@section('title', __('施工一覧'))
@section('page_title', __('施工一覧'))

@section('content')
<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-md-12">
                <form class="navbar-form navbar-right" role="search" action="{{ url('/master/carrying') }}">
                    <div class="form-group m-form__group pull-right" style="width: 50%">
                        <div class="input-group">
                            <input type="text" class="form-control" name="customer" value="{{ $old['customer'] }}" placeholder="ユーザーID">
                            <input type="text" class="form-control" name="shop" value="{{ $old['shop'] }}" placeholder="施工店舗">
                            <input type="text" class="form-control" name="date" value="{{ $old['date'] }}" id="carry_date" placeholder="施工日">
                            <input type="text" class="form-control" name="goods" value="{{ $old['goods'] }}" placeholder="商品名">
                            <input type="text" class="form-control" name="performer" value="{{ $old['performer'] }}" placeholder="施工担当者">
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
                            <td>受付No</td>
                            <td>ユーザーID</td>
                            <td>施工店舗</td>
                            <td>ショップエリア</td>
                            <td>ショップエリア詳細</td>
                            <td>代理店名</td>
                            <td>施工担当者</td>
                            <td>施工日</td>
                            <td>施工商品</td>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($carries as $ind => $u)
                        @php
                          $shop = $u->shop;
                        @endphp
                        <tr class="row-{{ (($carries->currentPage() - 1) * $per_page + $ind + 1)%2 }}" ref="{{ $u->id }}">
                            <td>{{ str_pad($u->id, 10, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $u->customer_id }}</td>
                            <td>{{ $shop ? $shop->name : '' }}</td>
                            <td>{{ $shop ? $shop->a_province : '' }}</td>
                            <td>{{ $shop ? $shop->a_detail : '' }}</td>
                            <td>{{ $shop ? $shop->brand : '' }}</td>
                            <td>{{ $u->performer }}</td>
                            <td>{{ $u->date }}</td>
                            <td>{{ $u->goods }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="100" class="no-items">検索結果がないです.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="pull-right">{{ $carries->appends(['date' => $old['date'], 'goods' => $old['goods']])->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function() {
        $('#carry_date').datepicker({
            language: 'ja',
            orientation:"bottom left"
        });
    });
</script>
@endsection
