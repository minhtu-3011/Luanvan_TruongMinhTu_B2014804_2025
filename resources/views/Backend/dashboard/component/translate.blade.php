<div class="row mb10">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left">Tieu de nhom bai viet</label>
            <input type="text" name="translate_name"  value="{{ old('name', $model->name ?? '') }}"  class="form-control" placeholder=""
                autocomplete="off" id="">
        </div>
    </div>
</div>
<div class="row mb50">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left">Mo ta ngan</label>
            <textarea type="text" name="translate_description"  value=""  class="form-control ck-editor" placeholder=""
                autocomplete="off" id="description1" data-height="150px">{{ old('description', $model->description ?? '') }} </textarea>
            
        </div>
    </div>
</div>
<div class="row mb10">
    <div class="col-lg-12">
        <div class="form-row">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <label for="" class="control-label text-left">Noi dung</label>
                    <a href="#" class="multipleUploadImageCkeditor" data-target="content">Upload nhiều hình ảnh</a>
                </div>
                <textarea type="text" name="translate_content" class="form-control ck-editor" 
                    placeholder="" autocomplete="off" id="content1" data-height="500px">
                    {{ old('content', $model->content ?? '') }}
                </textarea>
            </div>
    </div>
</div>