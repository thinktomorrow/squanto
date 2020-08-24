@extends(config('thinktomorrow.squanto.template'))

@section('custom-styles')
    <link rel="stylesheet" href="{{ asset('assets/back/vendor/redactor/redactor.css') }}">
@stop

@push('custom-scripts')
    <script src="{{ asset('assets/back/vendor/redactor/redactor.js') }}"></script>
    <script>
        ;(function ($) {

            $('.redactor-editor').redactor({
                focus: false,
                pastePlainText: true,
                buttons: ['html', 'formatting', 'bold', 'italic',
                    'unorderedlist', 'orderedlist', 'outdent', 'indent',
                    'link', 'alignment','image','horizontalrule'],
            });

            // Delete modal
            $("#remove-line-toggle").magnificPopup();

        })(jQuery);
    </script>
@endpush

@section('page-title')
    <a href="{{ route('squanto.index') }}">Translations</a> <span class="subtle"> > {{ $page->label() }}</span>
@stop

@section('content')

    <form method="POST" action="{{ route('squanto.update',$page->slug()) }}" role="form" class="form-horizontal">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">

        @foreach($lines as $line)
            @include('squanto::_field')
        @endforeach

        <div class="text-right">
            <button class="btn btn-success btn-lg" type="submit"><i class="fa fa-check"></i> Save your changes</button>
        </div>
    </form>


@stop

