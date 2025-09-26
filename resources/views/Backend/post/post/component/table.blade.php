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
        @if(isset($posts) && is_object($posts))
       
            @foreach($posts as $post)
                <tr>
                    <td>
                        <input type="checkbox" value="{{$post->id}}"  class="input-checkbox checkboxItem">
                    </td>
                    <td>
                        {{ $post->name }}

                        <div class="catalogue">
                            <span class="text-danger">Nhóm hiển thị: </span>
                            @foreach($post->post_catalogues as $val)
                                @foreach($val->post_catalogue_language as $cat)
                                    <a href="{{route('post.index', ['post_catalogue_id'=>$val->id])}}" title="">{{ $cat->name }}</a>
                                @endforeach
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <input value="{{$post->order}}" type="text" name="order" class="form-control sort-order" data-id="{{$post->id}}" data-model="{{$config['model']}}">
                    </td>
                    @include('backend.dashboard.component.languageTd', ['model' => $post, 'modeling' => 'Post'])


                    
                    

                    <td class="js-switch-{{$post->id}}">
                        <input type="checkbox" value="{{$post->publish}}" class="js-switch status" data-field = "publish" data-model = "{{$config['model']}}" 
                        {{($post->publish == 1)?'checked':'' }} data-modelid = "{{$post->id}}"/>
                    </td>
                    <td class="text-center">
                        <a href="{{route('post.edit', $post->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{route('post.delete', $post->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{
    $posts->links('pagination::bootstrap-4')
}}