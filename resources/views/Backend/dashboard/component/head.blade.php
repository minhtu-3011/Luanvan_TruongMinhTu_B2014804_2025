<base href="{{config('app.url')}}">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{csrf_token()}}">
<title>INSPINIA | Dashboard v.2</title>

<link href="/backend/css/bootstrap.min.css" rel="stylesheet">
<link href="/backend/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="/backend/css/animate.css" rel="stylesheet">
<link href="/backend/plugin/jquery-ui.css" rel="stylesheet">

@if(isset($config['css']) && is_array($config['css']))
    @foreach ($config['css'] as $key => $val)
        {!!'<link  href="' . $val . '" rel="stylesheet"></script>'!!}
    @endforeach
@endif
<link href="/backend/css/style.css" rel="stylesheet">
<link href="/backend/css/custom.css" rel="stylesheet">
<script src="/backend/js/jquery-3.1.1.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    var BASE_URL = '{{ rtrim(config('app.url'), "/")."/" }}';
    
    var SUFFIX = '{{ rtrim(config('apps.general.suffix'), "/") }}';

</script>