<div class="row">
    <div class="col-lg-5">
        <div class="ibox">
            <div class="ibox-content">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" class="">
                                    Liên kết tự tạo
                                </a>
                            </h5>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="true" style="">
                            <div class="panel-body">
                                <div class="panel-title">Tạo menu</div>
                                <div class="panel-description">
                                    <p>+ Cài đặt Menu mà bạn muốn hiển thị.</p>
                                    <p><small class="text-danger">* Khi khởi tạo menu bạn phải chắc chắn rằng đường dẫn của menu có hoạt động. Đường dẫn trên website được khởi tạo tại các module: Bài viết, Sản phẩm, Dự án, ...</small></p>
                                    <p><small class="text-danger">* Tiêu đề và đường dẫn của menu không được bỏ trống.</small></p>
                                    <p><small class="text-danger">* Hệ Thống chỉ hỗ trợ tối đa 5 cấp menu.</small></p>
                                    <a style="color:#000;border-color:#c4cdd5;display:inline-block !important;" href="" title="" class="btn btn-default add-menu m-b m-r right">Thêm đường dẫn</a>
                                </div>

                            </div>
                        </div>
                    </div>
                    @foreach(__('module.model') as $key => $val)

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" 
                                data-parent="#accordion" 
                                href="#{{$key}}" 
                                aria-expanded="false" 
                                class="collapsed menu-module"
                                data-model="{{$key}}"
                                >
                                    {{$val}}
                                </a>
                            </h5>
                        </div>
                        <div id="{{$key}}" class="panel-collapse collapse " aria-expanded="false" style="">
                            <div class="panel-body">
                                <div class="form-row">
                                    <input
                                        type="text"
                                        value=""
                                        class="form-control search-menu"
                                        name="keyword"
                                        placeholder="Nhập 2 ký tự để tìm kiếm..."
                                    >
                                    <div class="menu-list mt20">
                                        

                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                </div>

            </div>
        </div>
    </div>
    
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">

                <div class="row">
                    <div class="col-lg-4"><label for="">Tên menu</label></div>
                    <div class="col-lg-4"><label for="">Đường dẫn</label></div>
                    <div class="col-lg-2"><label for="">Vị trí</label></div>
                    <div class="col-lg-2"><label for="">Xoá</label></div>
                </div>
                <div class="hr-line-dashed" style="margin: 10px 0"></div>
                <div class="menu-wrapper">
                    <div class="notification text-center {{(is_array(old('menu')) && count(old('menu'))) ? 'none' : ''}}">
                        <h4 style="font-weight:500;font-size:16px;color:#000">Danh sách liên kết này chưa có bất kì đường dẫn nào.</h4>
                        <p style="color:#555;margin-top:10px;">Hãy nhấn vào 
                            <span style="color:blue;">Thêm đường dẫn</span> để bắt đầu thêm.
                        </p>
                    </div>
                    @if(is_array(old('menu')) && count(old('menu')))
                        @foreach(old('menu')['name'] as $key => $val)
                            <div class="row mb10 menu-item">
                                <div class="col-lg-4">
                                    <input type="text" value="{{$val}}" class="form-control" name="menu[name][]">
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" value="{{old('menu')['canonical'][$key]}}" class="form-control" name="menu[canonical][]">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" value="{{old('menu')['order'][$key]}}" class="form-control int text-right" name="menu[order][]">
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-row text-center">
                                        <a class="delete-menu"><img src="backend/close.png"></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
