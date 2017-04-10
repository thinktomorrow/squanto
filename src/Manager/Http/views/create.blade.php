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

    })(jQuery);
</script>
@endpush

@section('page-title','Add new translation key')

@section('content')

    <form method="POST" action="{{ route('squanto.lines.store') }}" role="form" class="form-horizontal">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-9">
                <div class="panel mb25">
                    <div class="panel-body">

                        <div class="form-group">
                            <label class="col-lg-3 control-label" for="inputKey">
                                Key
                            </label>
                            <div class="col-lg-8 bs-component">
                                <input name="key" type="text" id="inputKey" class="form-control" />
                                <span class="subtle">Unique identifier for usage in your view files. e.g. about.button.label or homepage.intro.text. The first segment of this key determines the page where this element will be stored under.</span>
                            </div>
                        </div>

                        <hr>

                        @foreach($available_locales as $locale)
                            <div class="form-group">
                                <label class="col-lg-3 control-label" for="{{$locale}}-inputValue">
                                    {{ $locale }} value
                                </label>
                                <div class="col-lg-8 bs-component">
                                    <textarea name="trans[{{ $locale }}]" id="{{ $locale }}-inputValue" class="form-control" rows="5">{!! old('trans['.$locale.']',$line->getValue($locale,false)) !!}</textarea>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <div class="bs-component text-center">
                        <button class="btn btn-success btn-lg" type="submit"><i class="fa fa-check"></i> Add translation line</button>
                    </div>
                    <div class="text-center">
                        <a class="subtle" id="remove-faq-toggle" href="{{ URL::previous() }}"> cancel</a>
                    </div>
                </div>
            </div><!-- end sidebar column -->
        </div>
    </form>

@stop

