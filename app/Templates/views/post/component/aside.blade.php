<div class="ibox">
    <div class="ibox-content">
        <div class="row mb10">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Chọn danh mục cha</label>
                    <span class="text-danger">(*)</span>
                    <span class="text-danger noitce">Chọn root nếu ko có danh mục cha</span>
                    <select name="{module}_catalogue_id" class="form-control select2">
                        @foreach($dropdown as $key => $val)
                            {{-- <option value="{{ $key }}" 
                                {{ $key == old('{module}_catalogue_id', ${module}->{module}_catalogue_id ?? '' ? 'selected') : '' }}>
                                {{ $val }}
                            </option> --}}

                            <option value="{{ $key }}" 
                                {{ $key == old('{module}_catalogue_id', isset(${module}->{module}_catalogue_id) ? ${module}->{module}_catalogue_id : '') ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                        @endforeach
                    </select>


                </div>

        
            </div>
        </div>

        @php
            $catalogue = [];

            if (isset(${module}) ) {
                foreach (${module}->{module}_catalogues as $key => $val) {
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
                            @if($key != old('{module}_catalogue_id', ${module}->{module}_catalogue_id ?? ''))
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
    <div class="ibox-title">Chọn ảnh đại diện</div>
    <div class="ibox-content">
        <div class="row mb10">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target">
                        <img src="{{ old('image', ${module}->image ?? '/backend/img/notfound.jpg') }}" alt="">
                    </span>
                    <input type="hidden" name="image" value="{{ old('image', ${module}->image ?? '') }}">
                    
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
                                <option value="{{ $key }}" {{ $key == old('publish', ${module}->publish ?? '') ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb10">
                        <select name="follow" id="follow" class="form-control">
                            @foreach(config('apps.general.follow') as $key => $val)
                                <option value="{{ $key }}" {{ $key == old('follow', ${module}->follow ?? '') ? 'selected' : '' }}>
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