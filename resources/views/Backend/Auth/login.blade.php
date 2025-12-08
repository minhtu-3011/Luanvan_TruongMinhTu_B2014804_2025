<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập hệ thống</title>

    <link href="/backend/css/bootstrap.min.css" rel="stylesheet">
    <link href="/backend/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="/backend/css/animate.css" rel="stylesheet">
    <link href="/backend/css/style.css" rel="stylesheet">
    <link href="/backend/css/custom.css" rel="stylesheet">

</head>

<body class="gray-bg">

    <div class="loginColumns animated fadeInDown">
        <div class="row">

            <div class="col-md-6">
                <h2 class="font-bold">Chào mừng bạn đến với hệ thống quản trị</h2>

                <p>
                    Hệ thống quản trị dành cho website thương mại điện tử.
                </p>
                <p>
                    Vui lòng đăng nhập để tiếp tục quản lý sản phẩm, đơn hàng và các chức năng khác.
                </p>
            </div>

            <div class="col-md-6">
                <div class="ibox-content">
                    <form class="m-t" method="POST" role="form" action="{{ route('auth.login') }}">
                        @csrf

                        <div class="form-group">
                            <input type="text" name="email" class="form-control" placeholder="Email hoặc tên đăng nhập"
                                required value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="error-message"> *
                                    {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                            @if ($errors->has('password'))
                                <span class="error-message"> *
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary block full-width m-b">Đăng nhập</button>

                        <a href="#">
                            <small>Quên mật khẩu?</small>
                        </a>

                    </form>

                    <p class="m-t">
                        <small>Hệ thống quản trị &copy; {{ date('Y') }}</small>
                    </p>

                </div>
            </div>

        </div>
        <hr />
        <div class="row">
            <div class="col-md-6">
                © {{ date('Y') }} Truong Minh Tu B2014804
            </div>
            <div class="col-md-6 text-right">
                <small>Phiên bản quản trị</small>
            </div>
        </div>
    </div>

</body>

</html>
