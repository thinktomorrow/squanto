

@foreach($locales as $i => $locale)

    <?php $fieldId = $lineViewModel->id() . '_' . $locale; ?>

    <div class="flex justify-between">
        @if($i === 0)
            <div class="mt-3 text-sm font-medium text-gray-700 mr-4 w-1/6">
                <label for="{{ $fieldId }}" class="block">{{ $lineViewModel->label() }}</label>
                @if($lineViewModel->description())
                    <p class="text-xs text-gray-500 mt-2">{{ $lineViewModel->description() }}</p>
                @endif
            </div>

        @else
            <span class="w-1/6 mr-4"></span>
        @endif
        <div class="w-5/6 flex justify-between mb-3">
            <span class="mr-2 mt-3 w-1/12 text-right text-gray-500 text-sm">{{ $locale }}</span>
            <div class="w-full">
                @if($lineViewModel->isFieldTypeTextarea() || $lineViewModel->isFieldTypeEditor())
                    <textarea
                        name="squanto[{{ $lineViewModel->key() }}][{{ $locale }}]"
                        id="{{ $fieldId }}"
                        class="{{ $lineViewModel->isFieldTypeEditor() ? 'redactor-editor' : '' }} mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{!! old('squanto['.$lineViewModel->key().']['.$locale.']',$lineViewModel->value($locale)) !!}</textarea>
                @else
                    <input
                        type="text"
                        name="squanto[{{ $lineViewModel->key() }}][{{ $locale }}]"
                        id="{{ $fieldId }}"
                        value="{!! old('squanto['.$lineViewModel->key().']['.$locale.']',$lineViewModel->value($locale)) !!}"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @endif
            </div>
        </div>
    </div>
@endforeach
