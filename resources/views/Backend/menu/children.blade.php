@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['children'].' '. $menu->languages->first()->pivot->name])
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $url = ($config['method'] == 'create') 
    ? route('menu.store') 
    : (($config['method'] == 'children') 
        ? route('menu.save.children', [$menu->id]) 
        : route('menu.update', $menu->id));

@endphp


<form action="{{$url}}" method="post" class="box menuContainer">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">

        @include('backend.menu.component.list')
        
        <div class="text-right">
            <button class="btn btn-primary" type="submit" name="send" value="send">Save</button>
        </div>
    </div>
</form>

 @include('backend.menu.component.popup')


