<form method="get" action="{{route('generate.index')}}">
    <div class="filter-wrapper">
    <div class="uk-flex uk-flex-middle uk-flex-space-between">
        <div class="perpage">
            @php
                $perpage = request('perpage')?:old('perpage');
            @endphp
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <select name="perpage" class="form-control input-sm perpage filter mr10">
                    @foreach([10, 20, 50, 100, 200] as $size)
                        <option value="{{ $size }}" {{ $perpage == $size ? 'selected' : '' }}>
                            {{ $size }} bản ghi
                        </option>
                    @endforeach
                </select>



            </div>

        </div>
        <div class="action">
            <div class="uk-flex uk-flex-middle">
                
                

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
                <a href="{{route('generate.create')}}" class="btn btn-danger"><i class="fa fa-plus">Tạo module mới</i></a>
            </div>

        </div>
    </div>
</div>
</form>