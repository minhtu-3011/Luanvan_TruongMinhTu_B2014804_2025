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

                        <form action="{{ route('customer.login.submit') }}" method="post">
                            @csrf
                            <div class="form-heading">Đăng nhập</div>

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

                            <input type="hidden" name="customer_catalogue_id" value="1">

                            <div>
                                <button type="submit" class="btn btn-primary block full-width m-b">
                                    Đăng nhập
                                </button>
                                <a href="">Quên mật khẩu?</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
