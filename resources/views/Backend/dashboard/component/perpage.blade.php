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