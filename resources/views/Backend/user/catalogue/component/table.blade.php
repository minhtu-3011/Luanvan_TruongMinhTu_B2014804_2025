<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>

            <th>Tên Nhóm</th>
            <th>Số thành viên Nhóm</th>
            <th>Mô tả</th>
            <th>Tình trạng</th>

            {{-- <th>Tình trạng</th> --}}
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($userCatalogues) && is_object($userCatalogues))
            @foreach($userCatalogues as $userCatalogue)
                <tr>
                    <td>
                        <input type="checkbox" value="{{$userCatalogue->id}}"  class="input-checkbox checkboxItem">
                    </td>

                    <td>
                        {{$userCatalogue->name}}


                    </td>
                    <td>
                        {{$userCatalogue->users_count}} Người


                    </td>
                    <td>
                        {{$userCatalogue->description}}


                    </td>
                    

                    <td class="js-switch-{{$userCatalogue->id}}">
                        <input type="checkbox" value="{{$userCatalogue->publish}}" class="js-switch status" data-field = "publish" data-model = "{{$config['model']}}" 
                        {{($userCatalogue->publish == 1)?'checked':'' }} data-modelid = "{{$userCatalogue->id}}"/>
                    </td>
                    <td class="text-center">
                        <a href="{{route('user.catalogue.edit', $userCatalogue->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{route('user.catalogue.delete', $userCatalogue->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{
    $userCatalogues->Links('pagination::bootstrap-4')
}}