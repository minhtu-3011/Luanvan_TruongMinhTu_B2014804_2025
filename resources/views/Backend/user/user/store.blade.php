@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $url = ($config['method'] == 'create') ? route('user.store') : route('user.update', $user->id);
@endphp


<form action="{{$url}}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-title">Thông tin chung</div>
                <div class="panel_description">Nhập thông tin của người sử dụng</div>

            </div>
            <div class="col-lg-7">
                <div class="ibox">

                    <div class="ibox-content">
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Email</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="email"  value="{{ old('email', $user->email ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Họ tên</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="name" value="{{old('name',$user->name ?? '')}}" class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                        </div>
                        @php
                            $userCatalogue = [
                                '[Chọn nhóm thành viên]',
                                '[Quan tri vien]',
                                '[Cong tac vien]',
                            ]
                        @endphp
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Nhóm thành viên</label>
                                    <span class="text-danger">(*)</span>
                                    <select name="user_catalogue_id" id="">
                                        @foreach($userCatalogue as $key => $item)
                                            <option value="{{ $key }}"
                                                {{ old('user_catalogue_id', $user->user_catalogue_id ?? '') == $key ? 'selected' : '' }}>
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Ngày sinh</label>

                                    <input type="date"
                                    name="birthday"
                                    value="{{ old('birthday', isset($user->birthday) ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}"
                                    class="form-control"
                                    autocomplete="off">

                                </div>
                            </div>
                        </div>
                        @if($config['method'] == 'create')
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Mật khẩu</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="password" name="password" value="" class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Nhập lại mật khẩu</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="password" name="re_password" value="" class="form-control"
                                        placeholder="" autocomplete="off" id="">
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row mb10">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Ảnh đại diện</label>

                                    <input type="text" name="image" value="{{old('image',$user->image ?? '')}}" class="form-control upload-image" placeholder=""
                                        autocomplete="off" id="" data-upload="Images">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-title">Thông tin liên hệ</div>
                <div class="panel_description">Nhập thông tin liên hệ của người sử dụng</div>

            </div>
            <div class="col-lg-7">
                <div class="ibox">

                    <div class="ibox-content">
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Thành phố</label>
                                    <span class="text-danger">(*)</span>
                                    <select name="province_id" id="" class="province location" data-target="districts">
                                        <option value="0">[Chọn thành phố]</option>
                                        @if(isset($provinces))
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->code }}"
                                                    {{ old('province_id') }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Quận huyện</label>
                                    <span class="text-danger"></span>
                                    <select name="district_id" id="" class="districts location" data-target="wards">
                                        <option value="0">[Chọn quận huyện]</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Phường xã</label>
                                    <span class="text-danger"></span>
                                    <select name="ward_id" id="" class="setupSelect2 wards">
                                        <option value="0">[Chọn phường xã]</option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Địa chỉ</label>

                                    <input type="text" name="address" value="{{old('address',$user->address ?? '')}}" class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                        </div>
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Số điện thoại</label>
                                    <span class="text-danger"></span>
                                    <input type="text" name="phone" value="{{old('phone',$user->phone ?? '')}}" class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Ghi chú</label>
                                    <span class="text-danger"></span>
                                    <input type="text" name="description" value="{{old('description')}}" class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" type="submit" name="send" value="send">Save</button>
        </div>
    </div>
</form>

<script>
    var province_id = '{{ old('province_id', $user->province_id ?? '') }}';
    var district_id = '{{ old('district_id', $user->district_id ?? '') }}';
    var ward_id     = '{{ old('ward_id', $user->ward_id ?? '') }}';
</script>
