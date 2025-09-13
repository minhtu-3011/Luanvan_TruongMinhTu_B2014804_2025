@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
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
    $url = ($config['method'] == 'create') ? route('language.store') : route('language.update', $language->id);
@endphp


<form action="{{$url}}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-title">Thông tin chung</div>
                <div class="panel_description">Nhập thông tin của Nhóm</div>

            </div>
            <div class="col-lg-7">
                <div class="ibox">

                    <div class="ibox-content">
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Tên nhóm</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="name"  value="{{ old('name', $language->name ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Canonical</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="canonical" value="{{old('canonical',$language->canonical ?? '')}}" class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                        </div>
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Ảnh đại diện</label>
                                    
                                    <input type="text" name="image"  value="{{ old('image', $language->image ?? '') }}"  class="form-control upload-image" placeholder=""
                                        autocomplete="off" id="" data-type='Images'>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Ghi chú</label>
                                    
                                    <input type="text" name="description" value="{{old('description',$language->description ?? '')}}" class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </div>
            </div>
        </div>
        <hr>
        
        <div class="text-right">
            <button class="btn btn-primary" type="submit" name="send" value="send">Save</button>
        </div>
    </div>
</form>


