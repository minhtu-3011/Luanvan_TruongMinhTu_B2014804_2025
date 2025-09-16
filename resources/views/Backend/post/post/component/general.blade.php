<div class="row mb10">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tieu de nhom bai viet</label>
                                    <input type="text" name="name"  value="{{ old('name', $post->name ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                            </div>
                        </div>
                        <div class="row mb50">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mo ta ngan</label>
                                    <textarea type="text" name="description"  value=""  class="form-control ck-editor" placeholder=""
                                        autocomplete="off" id="description" data-height="150px">{{ old('description', $post->description ?? '') }} </textarea>
                                    
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
                                        <textarea type="text" name="content" class="form-control ck-editor" 
                                            placeholder="" autocomplete="off" id="content" data-height="500px">
                                            {{ old('content', $post->content ?? '') }}
                                        </textarea>
                                    </div>
                            </div>
                        </div>