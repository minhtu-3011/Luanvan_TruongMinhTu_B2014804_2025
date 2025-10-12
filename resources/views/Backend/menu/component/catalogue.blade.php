
<div class="row">
    <div class="col-lg-5">
        <div class="panel-title">Vị trí menu</div>
        <div class="panel_description">Website có các vị trí hiển thị cho từng menu</div>
    </div>
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12 mb10">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div for="" class="text-bold">Chọn vị trí hiển thị <span class="text-danger">(*)</span></div>
                            <button 
                                data-toggle="modal" 
                                data-target="#createMenuCatalogue" 
                                type="button" 
                                name="" 
                                class="createMenuCatalogue btn btn-danger">Tạo vị trí hiển thị</button>
                        </div>
                    </div>
                    <div class="col-lg-12">

                        @if (count($menuCatalogues))
                            <select class="select2" name="menu_catalogue_id" id="">
                                <option value="0">[Chọn vị trí hiển thị]</option>
                                @foreach ($menuCatalogues as $key => $val)
                                    <option {{(isset($menuCatalogue) && $menuCatalogue->id == $val->id) ? 'selected' : ''}} value="{{ $val->id }}">{{ $val->name }}</option>
                                @endforeach
                            </select>
                        @endif

                    </div>
                    {{-- <div class="col-lg-6">

                        @if (count($menuCatalogues))
                            <select class="select2" name="type" id="">
                                <option value="none">[Chọn kiểu menu]</option>
                                @foreach (__('module.type') as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                @endforeach
                            </select>
                        @endif

                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>