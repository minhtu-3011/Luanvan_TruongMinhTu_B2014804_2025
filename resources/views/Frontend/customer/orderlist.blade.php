@extends('frontend.homepage.layout')
@section('content')

<div class="order-wrapper">
    <h1 class="text-center">Danh sách đơn hàng của bạn</h1>
    <table class="table table-striped table-bordered order-table" style="margin-top: 20px">
        <thead>
        <tr>
            
            <th>Mã</th>
            <th>Ngày tạo</th>
            <th>Khách hàng</th>
            <th class="text-right">Giảm giá (vnđ)</th>
            {{-- <th class="text-right">Phí ship (vnđ)</th> --}}
            <th class="text-right">Tổng cuối (vnđ)</th>

            <th>Hình thức</th>
        </tr>
        </thead>
        <tbody>
            @if(isset($orders) && $orders->count())
        @foreach($orders as $order)
        <tr>


            <td>
                {{-- <a href="{{ route('order.detail', $order->id) }}">{{ $order->code }}</a> --}}
                <a href="{{ route('customer.feorder', $order->id) }}">{{ $order->code }}</a>


                
            </td>

            <td>
                {{ convertDateTime($order->created_at, 'd-m-Y') }}
            </td>

            <td>
                <div><b>N:</b> {{ $order->fullname }}</div>
                <div><b>P:</b> {{ $order->phone }}</div>
                <div><b>A:</b> {{ $order->address }}</div>
            </td>

            <td class="text-right">
                {{ convert_price($order->promotion['discount'] ?? 0, true) }}
            </td>

            <td class="text-right">
                {{ convert_price($order->cart['cartTotal'] ?? 0, true) }}
            </td>

        

            <td class="text-center">
                @if($order->confirm != 'cancle')
                    <img style="max-width:54px;"
                        src="{{ array_column(__('payment.method'), 'image', 'name')[$order->method] ?? '-' }}"
                        title="{{ array_column(__('payment.method'), 'title', 'name')[$order->method] ?? '-' }}">
                @else
                    -
                @endif
            </td>
        </tr>
        @endforeach
    @endif

        </tbody>
    </table>
</div>


@endsection
