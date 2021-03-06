@extends(config('squanto.template','back._layouts.master'))

@section('page-title')
    <a href="{{ route('squanto.index') }}">Translations</a>
@stop

@section('topbar-right')
    @if(Auth::user()->isSquantoDeveloper())
        <a type="button" href="{{ route('squanto.lines.create') }}" class="btn btn-default btn-sm btn-rounded"><i class="fa fa-plus"></i> add new line</a>
    @endif
@stop

@section('content')

    <div class="panel">
        <table class="table admin-form">
            <thead>
            <tr class="bg-light">
                <th>Title</th>
                <th></th>
                <th style="width:9%;"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($pages as $page)

                <tr>
                    <td>
                        <a href="{{ route('squanto.edit',$page->id) }}">
                            {{ $page->label }}
                        </a>
                    </td>
                    <td class="subtle">
                        {{ $page->description }}
                    </td>
                    <td class="text-right">
                        <a title="Edit {{ $page->label }}" href="{{ route('squanto.edit',$page->id) }}" class="btn btn-rounded btn-success btn-xs"><i class="fa fa-edit"></i> </a>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>


@stop