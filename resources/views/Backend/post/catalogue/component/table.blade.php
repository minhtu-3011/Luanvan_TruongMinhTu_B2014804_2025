<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th style="width: 100px">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th >Tên Nhóm</th>
            <th style="width: 100px">Tình trạng</th>

            {{-- <th>Tình trạng</th> --}}
            <th class="text-center" style="width: 100px">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($postCatalogues) && is_object($postCatalogues))
       
            @foreach($postCatalogues as $postCatalogue)
                <tr>
                    <td>
                        <input type="checkbox" value="{{$postCatalogue->id}}"  class="input-checkbox checkboxItem">
                    </td>
                    <td>
                        {{ str_repeat('|----', ($postCatalogue->level > 0 ? $postCatalogue->level - 1 : 0)) . $postCatalogue->name }}




                    </td>

                    
                    

                    <td class="js-switch-{{$postCatalogue->id}}">
                        <input type="checkbox" value="{{$postCatalogue->publish}}" class="js-switch status" data-field = "publish" data-model = "{{$config['model']}}" 
                        {{($postCatalogue->publish == 1)?'checked':'' }} data-modelid = "{{$postCatalogue->id}}"/>
                    </td>
                    <td class="text-center">
                        <a href="{{route('post.catalogue.edit', $postCatalogue->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{route('post.catalogue.delete', $postCatalogue->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{
    $postCatalogues->links('pagination::bootstrap-4')
}}