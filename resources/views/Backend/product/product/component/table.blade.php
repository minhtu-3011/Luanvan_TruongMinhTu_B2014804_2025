<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th style="width: 100px">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th >Tên Nhóm</th>
            <th style="width: 80px">Vi tri</th>
            @include('backend.dashboard.component.languageTh')

            <th style="width: 100px">Tình trạng</th>

            {{-- <th>Tình trạng</th> --}}
            <th class="text-center" style="width: 100px">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($products) && is_object($products))
       
            @foreach($products as $product)
                <tr>
                    <td>
                        <input type="checkbox" value="{{$product->id}}"  class="input-checkbox checkboxItem">
                    </td>
                    <td>
                        @if(!empty($product->image))
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                                style="width:60px; height:60px; object-fit:cover; margin-right:8px; border:1px solid #ddd; border-radius:4px;">
                        @endif
                        {{ $product->name }}

                        <div class="catalogue">
                            <span class="text-danger">Nhóm hiển thị: </span>
                            @foreach($product->product_catalogues as $val)
                                @foreach($val->product_catalogue_language as $cat)
                                    <a href="{{route('product.index', ['product_catalogue_id'=>$val->id])}}" title="">{{ $cat->name }}</a>
                                @endforeach
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <input value="{{$product->order}}" type="text" name="order" class="form-control sort-order" data-id="{{$product->id}}" data-model="{{$config['model']}}">
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => $product, 'modeling' => 'Product'])


                    
                    

                    <td class="js-switch-{{$product->id}}">
                        <input type="checkbox" value="{{$product->publish}}" class="js-switch status" data-field = "publish" data-model = "{{$config['model']}}" 
                        {{($product->publish == 1)?'checked':'' }} data-modelid = "{{$product->id}}"/>
                    </td>
                    <td class="text-center">
                        <a href="{{route('product.edit', $product->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{route('product.delete', $product->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{
    $products->links('pagination::bootstrap-4')
}}