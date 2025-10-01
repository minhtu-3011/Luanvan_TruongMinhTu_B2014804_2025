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
        @if(isset(${module}s) && is_object(${module}s))
       
            @foreach(${module}s as ${module})
                <tr>
                    <td>
                        <input type="checkbox" value="{{${module}->id}}"  class="input-checkbox checkboxItem">
                    </td>
                    <td>
                        {{ ${module}->name }}

                        <div class="catalogue">
                            <span class="text-danger">Nhóm hiển thị: </span>
                            @foreach(${module}->{module}_catalogues as $val)
                                @foreach($val->{module}_catalogue_language as $cat)
                                    <a href="{{route('{module}.index', ['{module}_catalogue_id'=>$val->id])}}" title="">{{ $cat->name }}</a>
                                @endforeach
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <input value="{{${module}->order}}" type="text" name="order" class="form-control sort-order" data-id="{{${module}->id}}" data-model="{{$config['model']}}">
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => ${module}, 'modeling' => '{Module}'])


                    
                    

                    <td class="js-switch-{{${module}->id}}">
                        <input type="checkbox" value="{{${module}->publish}}" class="js-switch status" data-field = "publish" data-model = "{{$config['model']}}" 
                        {{(${module}->publish == 1)?'checked':'' }} data-modelid = "{{${module}->id}}"/>
                    </td>
                    <td class="text-center">
                        <a href="{{route('{module}.edit', ${module}->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{route('{module}.delete', ${module}->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{
    ${module}s->links('pagination::bootstrap-4')
}}