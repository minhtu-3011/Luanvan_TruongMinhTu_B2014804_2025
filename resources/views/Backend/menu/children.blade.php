
{{-- @include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['children'].' '. $menu->languages->first()->pivot->name]) --}}
@include('backend.dashboard.component.breadcrumb', [
    'title' => $config['seo']['create']['children'].' '. $currentMenu->languages->first()->pivot->name
])
@include('backend.dashboard.component.formError')

@php
    $url = ($config['method'] == 'create') 
    ? route('menu.store') 
    : (($config['method'] == 'children') 
        ? route('menu.save.children', [$currentMenu->id]) 
        : route('menu.update', $currentMenu->id));

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


