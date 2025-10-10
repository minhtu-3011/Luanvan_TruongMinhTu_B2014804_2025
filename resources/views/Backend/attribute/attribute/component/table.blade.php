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
        @if(isset($attributes) && is_object($attributes))
       
            @foreach($attributes as $attribute)
                <tr>
                    <td>
                        <input type="checkbox" value="{{$attribute->id}}"  class="input-checkbox checkboxItem">
                    </td>
                    <td>
                        {{ $attribute->name }}

                        <div class="catalogue">
                            <span class="text-danger">Nhóm hiển thị: </span>
                            @foreach($attribute->attribute_catalogues as $val)
                                @foreach($val->attribute_catalogue_language as $cat)
                                    <a href="{{route('attribute.index', ['attribute_catalogue_id'=>$val->id])}}" title="">{{ $cat->name }}</a>
                                @endforeach
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <input value="{{$attribute->order}}" type="text" name="order" class="form-control sort-order" data-id="{{$attribute->id}}" data-model="{{$config['model']}}">
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => $attribute, 'modeling' => 'Attribute'])


                    
                    

                    <td class="js-switch-{{$attribute->id}}">
                        <input type="checkbox" value="{{$attribute->publish}}" class="js-switch status" data-field = "publish" data-model = "{{$config['model']}}" 
                        {{($attribute->publish == 1)?'checked':'' }} data-modelid = "{{$attribute->id}}"/>
                    </td>
                    <td class="text-center">
                        <a href="{{route('attribute.edit', $attribute->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{route('attribute.delete', $attribute->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{
    $attributes->links('pagination::bootstrap-4')
}}