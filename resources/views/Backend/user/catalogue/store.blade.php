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
    $url = ($config['method'] == 'create') ? route('user.catalogue.store') : route('user.catalogue.update', $userCatalogue->id);
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
                                    <label for="" class="control-lable text-right">Tên nhóm</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="name"  value="{{ old('name', $userCatalogue->name ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Ghi chú</label>
                                    <
                                    <input type="text" name="description" value="{{old('name',$userCatalogue->description ?? '')}}" class="form-control" placeholder=""
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


