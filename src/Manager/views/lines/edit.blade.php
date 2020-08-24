@extends(config('thinktomorrow.squanto.template','back._layouts.master'))

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

@section('page-title','Edit line')

@section('content')

    <form method="POST" action="{{ route('squanto.lines.update',$line->id) }}" role="form" class="form-horizontal">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        <div class="row">

            <div class="col-md-9">
                <div class="panel mb25">
                    <div class="panel-body">

                        <div class="form-group">
                            <label class="col-lg-3 control-label" for="inputKey">
                                Key
                            </label>
                            <div class="col-lg-8 bs-component">
                                <input name="key" type="text" id="inputKey" class="form-control" value="{{ old('key',$line->key) }}" />
                                <span class="subtle">unique identifier for usage in your view files. e.g. button.label or intro.text</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label" for="inputLabel">
                                Label
                            </label>
                            <div class="col-lg-8 bs-component">
                                <input name="label" type="text" id="inputLabel" class="form-control" value="{{ old('label',$line->label) }}" />
                                <span class="subtle">Descriptive label (only shown in admin)</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label" for="inputDescription">
                                Description
                            </label>
                            <div class="col-lg-8 bs-component">
                                <input name="description" type="text" id="inputDescription" class="form-control" value="{{ old('description',$line->description) }}" />
                                <span class="subtle">Optional information for webmaster (only shown in admin)</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-8 col-lg-offset-3 bs-component">
                                <input type="radio" name="type" value="text" id="inputText" {{ ($line->editInTextInput()) ? 'checked="checked"' : '' }}>
                                <label for="inputText">text (default)</label>
                            </div>
                            <div class="col-lg-8 col-lg-offset-3 bs-component">
                                <input type="radio" name="type" value="textarea" id="inputTextarea" {{ ($line->editInTextArea()) ? 'checked="checked"' : '' }}>
                                <label for="inputTextarea">textarea (plain text)</label>
                            </div>
                            <div class="col-lg-8 col-lg-offset-3 bs-component">
                                <input type="radio" name="type" value="editor" id="inputEditor" {{ ($line->editInEditor()) ? 'checked="checked"' : '' }}>
                                <label for="inputEditor">editor (html)</label>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="col-lg-8 col-lg-offset-3 bs-component">
                                <h3>Translations</h3>
                            </div>
                        </div>


                        @foreach($available_locales as $locale)
                            <div class="form-group">
                                <label class="col-lg-3 control-label" for="{{$locale}}-inputValue">
                                    {{ $locale }} value
                                </label>

                                <div class="col-lg-8 bs-component">

                                    @if($line->editInEditor())
                                        <textarea name="trans[{{ $locale }}]" id="{{ $locale }}-inputValue" class="form-control redactor-editor" rows="5">{!! old('trans['.$locale.']',$line->getValue($locale,false)) !!}</textarea>
                                    @elseif($line->editInTextarea())
                                        <textarea name="trans[{{ $locale }}]" id="{{ $locale }}-inputValue" class="form-control" rows="5">{!! old('trans['.$locale.']',$line->getValue($locale,false)) !!}</textarea>
                                    @else
                                        <input type="text" name="trans[{{ $locale }}]" id="{{ $locale }}-inputValue" class="form-control" value="{!! old('trans['.$locale.']',$line->getValue($locale,false)) !!}"/>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <div class="bs-component text-center">
                        <button class="btn btn-success btn-lg" type="submit"><i class="fa fa-check"></i> Save changes</button>
                    </div>
                    <div class="text-center">
                        <a href="#" class="subtle subtle-danger" id="remove-line-toggle" data-toggle="modal" data-target="#remove-line-modal"><i class="fa fa-remove"></i> Remove this line?</a>
                    </div>
                </div>
            </div><!-- end sidebar column -->
        </div>
    </form>

    @include('squanto::_deletemodal')

@stop

