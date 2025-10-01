<div class="ibox">
    <div class="ibox-title">Chọn ảnh đại diện</div>
    <div class="ibox-content">
        <div class="row mb10">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target">
                        <img src="{{ old('image', $model->image ?? '/backend/img/notfound.jpg') }}" alt="">
                    </span>
                    <input type="hidden" name="image" 
                           value="{{ old('image', $model->image ?? '') }}">
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
                                <option value="{{ $key }}" 
                                    {{ $key == old('publish', $model->publish ?? '') ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb10">
                        <select name="follow" id="follow" class="form-control">
                            @foreach(config('apps.general.follow') as $key => $val)
                                <option value="{{ $key }}" 
                                    {{ $key == old('follow', $model->follow ?? '') ? 'selected' : '' }}>
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
