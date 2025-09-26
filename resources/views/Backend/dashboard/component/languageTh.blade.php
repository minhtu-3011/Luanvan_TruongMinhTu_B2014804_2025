@foreach ($languages as $language)
    @if(session('app_locale') === $language->canonical) 
        @continue
    @endif
    <th style="width: 100px"><span class="image img-scaledown" ><img style="height:30px" src="{{$language->image}}" alt=""></span></th>
@endforeach