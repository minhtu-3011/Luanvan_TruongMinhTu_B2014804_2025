<form method="get" action="{{route('{view}.index')}}">
    <div class="filter-wrapper">
    <div class="uk-flex uk-flex-middle uk-flex-space-between">
        @include('backend.dashboard.component.perpage')
        <div class="action">
            <div class="uk-flex uk-flex-middle">
                @include('backend.dashboard.component.filterPublish')
                @include('backend.dashboard.component.keyword')
                <a href="{{route('{view}.create')}}" class="btn btn-danger"><i class="fa fa-plus">{{__('messages.{module}.create.title')}}</i></a>
            </div>

        </div>
    </div>
</div>
</form>