@extends('frontend.homepage.layout')

@section('content')
<div class="login-container">
    <div class="uk-container uk-container-center">
        <div class="login-page">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-3-3">
                    <div class="login-form">
                        {{-- Hiển thị thông báo session --}}
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('customer.register.submit') }}" method="post">
                            @csrf
                            <div class="form-heading">Đăng ký</div>
                            <div class="form-row">
                                <input 
                                    type="text" 
                                    class="input-text" 
                                    name="name"
                                    value="{{ old('name') }}" 
                                    placeholder="Họ và tên"
                                >
                                @if($errors->has('name'))
                                    <span class="error-message">* {{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="form-row">
                                <input 
                                    type="text" 
                                    class="input-text" 
                                    name="email"
                                    value="{{ old('email') }}" 
                                    placeholder="Email"
                                >
                                @if($errors->has('email'))
                                    <span class="error-message">* {{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="form-row">
                                <input 
                                    type="text" 
                                    class="input-text" 
                                    name="phone"
                                    value="{{ old('phone') }}" 
                                    placeholder="Số điện thoại"
                                >
                                @if($errors->has('phone'))
                                    <span class="error-message">* {{ $errors->first('phone') }}</span>
                                @endif
                            </div>

                            <div class="form-row">
                                <input 
                                    type="password" 
                                    name="password"
                                    class="input-text" 
                                    placeholder="Mật khẩu"
                                    autocomplete="off"
                                >
                                @if($errors->has('password'))
                                    <span class="error-message">* {{ $errors->first('password') }}</span>
                                @endif
                            </div>
                            <div class="form-row">
                                    
                                    <input 
                                        type="password"
                                        name="re_password"
                                        value=""
                                        class="input-text"
                                        placeholder="Nhập lại mật khẩu"
                                        autocomplete="off"
                                    >
                                </div>

                            <input type="hidden" name="customer_catalogue_id" value="1">
                            <input 
                                type="hidden" 
                                name="code" 
                                value="{{ old('code', ($customer->code) ?? time() ) }}"
                            >
                            <input type="hidden" name="source_id" value="1">
                            <div>
                                <button type="submit" class="btn btn-primary block full-width m-b">
                                    Đăng ký
                                </button>
                                
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
