<form method="get" action="{{route('language.index')}}">
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
                @php
                    $publishArray = ['Không xuất bản', 'Xuất bản'];
                    // $publish = request('publish')?:old('publish');
                    $publish = request('publish') ?? old('publish') ?? '-1';
                @endphp
                <select name="publish" class="form-control mr10">
                    {{-- <option value="-1" selected="selected">Chọn tinh trang</option> --}}
                    <option value="-1" {{ $publish == '-1' ? 'selected' : '' }}>Chọn tình trạng</option>

                    @foreach($publishArray as $key => $val)
                        <option {{ $publish == $key ? 'selected' : '' }} value="{{$key}}">{{$val}}</option>
                    @endforeach
                    
                </select>
                

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
                <a href="{{route('language.create')}}" class="btn btn-danger"><i class="fa fa-plus">Thêm mới Ngôn ngữ</i></a>
            </div>

        </div>
    </div>
</div>
</form>