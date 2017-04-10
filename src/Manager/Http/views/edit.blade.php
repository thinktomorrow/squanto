@extends(config('squanto.template','back._layouts.master'))

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

@section('page-title','Translations for '.$page->label)

@section('topbar-right')
    @if(Auth::user()->isSquantoDeveloper())
        <a type="button" href="{{ route('squanto.lines.create',$page->id) }}" class="btn btn-success btn-sm btn-rounded"><i class="fa fa-plus"></i> add new line</a>
    @endif
@stop

@section('content')

    <form method="POST" action="{{ route('squanto.update',$page->id) }}" role="form" class="form-horizontal">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        <div class="row">

            @include('squanto::_formtabs')

            <div class="col-md-3">
                <div class="form-group">
                    <div class="bs-component text-center">
                        <button class="btn btn-success btn-lg" type="submit"><i class="fa fa-check"></i> Save your changes</button>
                    </div>
                </div>
            </div><!-- end sidebar column -->
        </div>
    </form>


@stop

