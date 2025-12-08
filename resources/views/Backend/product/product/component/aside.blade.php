<div class="ibox">
    <div class="ibox-content">
        <div class="row mb10">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Chọn danh mục cha</label>
                    <span class="text-danger">(*)</span>
                    <span class="text-danger noitce">Chọn root nếu ko có danh mục cha</span>
                    
                    <select name="product_catalogue_id" class="form-control select2">
                        @foreach($dropdown as $key => $val)
                            {{-- <option value="{{ $key }}" 
                                {{ $key == old('product_catalogue_id', $product->product_catalogue_id ?? '' ? 'selected') : '' }}>
                                {{ $val }}
                            </option> --}}
                            
                            <option value="{{ $key }}" 
                                {{ $key == old('product_catalogue_id', isset($product->product_catalogue_id) ? $product->product_catalogue_id : '') ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                        @endforeach
                    </select>


                </div>

        
            </div>
        </div>

        @php
            $catalogue = [];

            if (isset($product) ) {
                foreach ($product->product_catalogues as $key => $val) {
                    $catalogue[] = $val->id;
                }
            }
        @endphp
        <div class="row mb10">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">Danh mục phụ</label>
                    <select multiple name="catalogue[]" class="form-control select2" id="">
                        @foreach($dropdown as $key => $val)
                            @if($key != old('product_catalogue_id', $product->product_catalogue_id ?? ''))
                                <option
                                    @if(is_array(old('catalogue', (isset($catalogue) && count($catalogue)) ? $catalogue : [])) 
                                        && in_array($key, old('catalogue', (isset($catalogue)) ? $catalogue : [])))
                                        selected
                                    @endif 
                                    value="{{ $key }}">
                                    {{ $val }}
                                </option>
                            @endif
                        @endforeach
                    </select>



                </div>

        
            </div>
        </div>
    </div>
</div>



<div class="ibox">
    <div class="ibox-title">Thông tin chung</div>
    <div class="ibox-content">
        <div class="row mb10">
            <div class="col-lg-12">
                <div class="form-row mb-3">
                    <label for="code" class="control-label">Mã sản phẩm</label>
                    <input type="text" name="code" id="code" 
                           value="{{ old('code', $product->code ?? '') }}" 
                           class="form-control " placeholder="Nhập mã sản phẩm">
                </div>

                <div class="form-row mb-3">
                    <label for="origin" class="control-label">Xuất xứ</label>
                    <input type="text" name="origin" id="origin" 
                           value="{{ old('origin', $product->origin ?? '') }}" 
                           class="form-control " placeholder="Nhập xuất xứ sản phẩm">
                </div>

                <div class="form-row mb-3">
                    <label for="price" class="control-label">Giá bán</label>
                    <input type="number" name="price" id="price" 
                           value="{{ old('price', $product->price ?? '') }}" 
                           class="form-control " placeholder="Nhập giá bán sản phẩm">
                </div>
            </div>
        </div>
    </div>
</div>




<div class="ibox">
    <div class="ibox-title">Chọn ảnh đại diện</div>
    <div class="ibox-content">
        <div class="row mb10">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target">
                        <img src="{{ old('image', $product->image ?? '/backend/img/notfound.jpg') }}" alt="">
                    </span>
                    <input type="hidden" name="image" value="{{ old('image', $product->image ?? '') }}">
                    
                </div>

        
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">Cấu hình nâng cao</div>
    <div class="ibox-content">
        <div class="row mb10">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb10">
                        <select name="publish" id="publish" class="form-control">
                            @foreach(config('apps.general.publish') as $key => $val)
                                <option value="{{ $key }}" {{ $key == old('publish', $product->publish ?? '') ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb10">
                        <select name="follow" id="follow" class="form-control">
                            @foreach(config('apps.general.follow') as $key => $val)
                                <option value="{{ $key }}" {{ $key == old('follow', $product->follow ?? '') ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>