
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-title"></div>
                <div class="panel_description">
                    <p>Bạn muốn xoá {{ $model->name ?? $model->email ?? 'bản ghi này' }}?</p>
                    <p class="text-danger">Lưu ý: thông tin không thể khôi phục sau khi xoá!</p>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb10">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Name</label>
                                    <span class="text-danger">(*)</span>
                                    <input type="text" name="name"
                                           value="{{ old('name', $model->name ?? '') }}"
                                           class="form-control"
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right mt-3">
            <button class="btn btn-danger" type="submit">Xoá dữ liệu</button>
        </div>
    </div>

