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