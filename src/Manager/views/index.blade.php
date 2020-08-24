@extends(config('thinktomorrow.squanto.template'))

@section('page-title')
    <a href="{{ route('squanto.index') }}">Translations</a>
@stop

@section('content')
    <div class="panel">
        <table class="table admin-form">
            <thead>
            <tr class="bg-light">
                <th>Title</th>
                <th style="width:9%;"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($pages as $page)

                <tr>
                    <td>
                        <a href="{{ route('squanto.edit',$page->slug()) }}">
                            {{ $page->label() }}
                        </a>
                    </td>
                    <td class="text-right">
                        <a title="Edit {{ $page->label() }}" href="{{ route('squanto.edit',$page->slug()) }}" class="btn btn-rounded btn-success btn-xs"><i class="fa fa-edit"></i> </a>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>


@stop
