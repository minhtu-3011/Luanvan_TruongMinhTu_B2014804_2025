<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>

            <th>Họ tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Địa chỉ</th>
            <th>Nhóm thành viên</th>
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($users) && is_object($users))
            @foreach($users as $user)
                <tr>
                    <td>
                        <input type="checkbox" value="{{$user->id}}"  class="input-checkbox checkboxItem">
                    </td>

                    <td>
                        <div class="user-item name">Họ tên: {{$user->name}}</div>


                    </td>
                    <td>
                        <div class="user-item email"> {{$user->email}}</div>
                    </td>
                    <td>
                        <div class="user-item phone"> {{$user->phone}}</div>
                    </td>
                    <td>
                        <div class="address-item address">{{$user->address}}</div>

                    </td>
                    <td>
                       {{$user->user_catalogues->name}}

                    </td>

                    <td class="js-switch-{{$user->id}}">
                        <input type="checkbox" value="{{$user->publish}}" class="js-switch status" data-field = "publish" data-model = "User" 
                        {{($user->publish == 1)?'checked':'' }} data-modelid = "{{$user->id}}"/>
                    </td>
                    <td class="text-center">
                        <a href="{{route('user.edit', $user->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{route('user.delete', $user->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{
    $users->Links('pagination::bootstrap-4')
}}