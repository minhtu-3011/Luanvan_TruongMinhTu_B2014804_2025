<div class="ibox">
    <div class="ibox-title">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <h5>Album Ảnh</h5>
            <div class="upload-album">
                <a href="" class="upload-picture">Chọn Hình</a>
            </div>
        </div>
    </div>

    <div class="ibox-content">
        @php
            $gallery = (isset($album) && count($album) ) ? $album : old('album');

        @endphp
        <div class="row">
            <div class="col-lg-12">

                {{-- Khung khi chưa có ảnh --}}
                <div class="click-to-upload {{ (isset($gallery) && count($gallery)) ? 'hidden' : '' }}">
                    <div class="icon">
                        <a href="" class="upload-picture">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80" role="img" aria-label="Image">
                                <image href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQs9gUXKwt2KErC_jWWlkZkGabxpeGchT-fyw&s"
                                        x="0" y="0" width="80" height="80" preserveAspectRatio="xMidYMid slice"/>
                            </svg>
                        </a>
                    </div>
                    <div class="small-text">Sử dụng nút chọn hình hoặc click vào đây để thêm ảnh</div>
                </div>

                {{-- Khung danh sách ảnh --}}
                <div class="upload-list {{ (isset($gallery) && count($gallery)) ? '' : 'hidden' }}">
                    <ul id="sortable" class="clearfix data-album sortui ui-sortable">
                        @if(isset($gallery))
                            @foreach ($gallery as $val)
                                <li class="ui-state-default">
                                    <div class="thumb">
                                        <span class="span image img-scaledown">
                                            <img src="{{ $val }}" alt="{{ $val }}">
                                            <input type="hidden" name="album[]" value="{{ $val }}">
                                        </span>
                                        <button class="delete-image"><i class="fa fa-trash"></i></button>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
