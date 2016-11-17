@extends('back._layouts.master')

@section('page-title')
    Translations
@stop

@section('topbar-right')
    <a href="{{ route('back.squanto.lines.create') }}" class="btn btn-success btn-sm btn-rounded"><i class="fa fa-plus"></i> add a new line</a>
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

            @if($pages->count() < 1)
                <tr>
                    <td>
                        Hey there, you currently have no translations set. You can import them from your language files via the <code>artisan squanto:import</code> or add new with the + add translation button
                    </td>
                </tr>
            @endif

            @foreach($pages as $page)

                <tr>
                    <td>
                        <a href="{{ route('back.squanto.edit',$page->id) }}">
                            {{ $page->label }}
                        </a>
                    </td>
                    <td class="subtle">
                        {{ $page->description }}
                    </td>
                    <td class="text-right">
                        <a title="Edit {{ $page->label }}" href="{{ route('back.squanto.edit',$page->id) }}" class="btn btn-rounded btn-success btn-xs"><i class="fa fa-edit"></i> </a>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>


@stop