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
    $url = ($config['method'] == 'create') ? route('generate.store') : route('generate.update', $generate->id);
@endphp


<form action="{{$url}}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-title">Thông tin chung</div>
                <div class="panel_description">Nhập thông tin của ngon ngu</div>

            </div>
            <div class="col-lg-7">
                <div class="ibox">

                    <div class="ibox-content">
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên model</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="name"  value="{{ old('name', $generate->name ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên chức năng</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="module"  value="{{ old('module', $generate->module ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                            
                        </div>
                        
                        
                        <div class="row mb10">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Loại module</label>
                                    <span class="text-danger">(*)</span>
                                    <select name="module_type" id="" class="form-control select2">
                                        <option value="0">Chọn loại module</option>
                                        <option value="catalogue">Module danh mục</option>
                                        <option value="detail">Module chi tiết</option>
                                        <option value="difference">Module khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Đường dẫn</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="path"  value="{{ old('path', $generate->path ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
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
                <div class="panel-title">Thông tin schema</div>

            </div>
            <div class="col-lg-7">
                <div class="ibox">

                    <div class="ibox-content">
                        <div class="row mb10">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Schema</label>
                                    <span class="text-danger">(*)</span>
                                    <textarea type="text" name="schema"  value="{{ old('schema', $generate->schema ?? '') }}"  class="form-control schema" id=""> </textarea>
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


