@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])



<form action="{{route('generate.destroy', $generate->id)}}" method="post" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-title"></div>
                <div class="panel_description">
                    <p>Bạn muốn xoá module : {{$generate->email}}</p>
                    <p>Lưu ý thông tin không thể khôi phục sau khi xoá !</p>
                </div>

            </div>
            <div class="col-lg-7">
                <div class="ibox">

                    <div class="ibox-content">
                        <div class="row mb10">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-lable text-right">Name</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="name"  value="{{ old('name', $generate->name ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="" readonly>
                                </div>
                            </div>
                            
                        </div>
                     
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button class="btn btn-danger" type="submit" name="send" value="send">Xoá dữ liệu</button>
        </div>
    </div>
</form>

