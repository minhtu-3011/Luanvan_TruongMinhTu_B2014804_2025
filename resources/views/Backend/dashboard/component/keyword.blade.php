<div class="uk-search uk-flex uk-flex-middle mr10">
    <div class="input-group">
        <input type="text" name="keyword" value="{{request('keyword')?:old('keyword')}}" placeholder="Nhập Từ khóa bạn muốn tìm kiếm..."
            class="form-control">
        <span class="input-group-btn">
            <button type="submit" name="search" value="search" class="btn btn-primary mb0 btn-sm">
                Tìm Kiếm
            </button>
        </span>
    </div>
</div>