<div class="ibox">
                    <div class="ibox-title">
                        <h5>Cau hinh SEO</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="seo-container">
                            <div class="translate_meta-title">
                            {{ old('meta_title', $model->meta_title ?? 'Bạn chưa có tiêu đề SEO') }}
                        </div>
                        <div class="canonical">
                            {{ old('canonical', $model->canonical ?? 'https://duong-dan-cua-ban.html') 
                                ? config('app.url') . old('canonical', $model->canonical ?? '') . config('apps.general.suffix') 
                                : 'https://duong-dan-cua-ban.html' }}
                        </div>
                        <div class="translate_meta-description">
                            {{ old('meta_description', $model->meta_description ?? 'Bạn chưa có mô tả SEO') }}
                        </div>
                        </div>

                        <div class="seo-wrapper">
                            <div class="row mb10">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                    <label for="" class="control-label text-left">Mo ta SEO</label>
                                    <input type="text" name="translate_meta_title"  value="{{ old('meta_title', $model->meta_title ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                                </div>
                            </div>
                            <div class="row mb10">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                    <label for="" class="control-label text-left">
                                        <span>Tu khoa SEO</span>
                                    </label>
                                    <input type="text" name="translate_meta_keyword"  value="{{ old('meta_keyword', $model->meta_keyword ?? '') }}"  class="form-control" placeholder=""
                                        autocomplete="off" id="">
                                </div>
                                </div>
                            </div>

                            <div class="row mb10">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                    <label for="" class="control-label text-left">
                                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                            <span>Mô tả SEO</span>
                                            <span class="count_meta-description">0 ky tu</span>
                                        </div>
                                    </label>
                                    <textarea type="text" name="translate_meta_description"  value=""  class="form-control" placeholder=""
                                        autocomplete="off" id="">{{ old('meta_description', $model->meta_description ?? '') }} </textarea>
                                </div>
                                </div>
                            </div>

                            <div class="row mb10">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                    <label for="" class="control-label text-left">
                                        <span>Đường dẫn <span class="text-danger">*</span></span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" name="translate_canonical"  value="{{ old('canonical', $model->canonical ?? '') }}"  class="form-control " placeholder=""
                                        autocomplete="off" id="" style="padding-left: 156px;">
                                        <span class="baseUrl">{{config('app.url')}}</span>
                                    </div>
                                </div>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                </div>