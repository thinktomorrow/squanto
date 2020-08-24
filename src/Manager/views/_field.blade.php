<div class="form-group">
    <label class="col-lg-3 control-label">
        {{ $line->label() }}
    </label>
    <div class="col-lg-9 bs-component">

        @foreach($locales as $locale)

            <label>{{ $locale }}</label>

            @if($line->isFieldTypeEditor())
                <textarea name="squanto[{{ $line->key() }}][{{ $locale }}]" id="{{ $line->id() }}-{{ $locale }}-inputValue" class="form-control redactor-editor" rows="5">{!! old('squanto['.$line->key().']['.$locale.']',$line->value($locale)) !!}</textarea>
            @elseif($line->isFieldTypeTextarea())
                <textarea name="squanto[{{ $line->key() }}][{{ $locale }}]" id="{{ $line->id() }}-{{ $locale }}-inputValue" class="form-control" rows="5">{!! old('squanto['.$line->key().']['.$locale.']',$line->value($locale)) !!}</textarea>
            @else
                <input type="text" name="squanto[{{ $line->key() }}][{{ $locale }}]" id="{{ $line->id() }}-{{ $locale }}-inputValue" class="form-control" value="{!! old('squanto['.$line->key().']['.$locale.']',$line->value($locale)) !!}"/>
            @endif

        @endforeach

        @if($line->description())
            <p class="subtle">{{ $line->description() }}</p>
        @endif
    </div>
</div>
