<x-squanto::app-layout>

@php
    $searchTerm = $searchTerm ?? '';
    $results = $results ?? [];
@endphp

@section('page-title')
    <a href="{{ route('squanto.index') }}">Translations</a>
@stop

<div class="mb-6">
    <form method="GET" action="{{ route('squanto.index') }}" class="flex items-center gap-3">
        <input
            type="text"
            name="search"
            value="{{ $searchTerm }}"
            placeholder="Search translations"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        >

        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            Search
        </button>

        @if($searchTerm !== '')
            <a href="{{ route('squanto.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <ul class="divide-y divide-gray-200">
        @if($searchTerm !== '')
            @forelse($results as $result)
                @php
                    $key = $result->key();
                    $slug = \Illuminate\Support\Str::before($key, '.');
                    $label = ucfirst(str_replace('_', ' ', \Illuminate\Support\Str::afterLast($key, '.')));
                    $matchingText = null;

                    foreach (config('squanto.locales', []) as $locale) {
                        $value = (string) ($result->value($locale) ?? '');

                        if ($value !== '' && \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($value), \Illuminate\Support\Str::lower($searchTerm))) {
                            $matchingText = $value;
                            break;
                        }
                    }

                    if (! $matchingText) {
                        $matchingText = (string) ($result->value(app()->getLocale()) ?? '');
                    }

                    $matchingText = \Illuminate\Support\Str::limit(trim(strip_tags($matchingText)), 160);
                @endphp

                <li>
                    <a class="block hover:bg-gray-50 px-4 py-4 sm:px-6" href="{{ route('squanto.edit', $slug) }}#{{ $result->id() }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $label }}</p>
                                <p class="text-xs text-gray-500">{{ $key }}</p>
                            </div>
                        </div>

                        @if($matchingText)
                            <p class="mt-1 text-sm text-gray-600">{{ $matchingText }}</p>
                        @endif
                    </a>
                </li>
            @empty
                <li class="px-4 py-4 text-sm text-gray-600 sm:px-6">
                    No translations found for <strong>{{ $searchTerm }}</strong>.
                </li>
            @endforelse
        @else
            @foreach($pageGroups ?? ['App' => $pages] as $sourceLabel => $groupedPages)
                <li class="bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">
                    {{ $sourceLabel }}
                </li>

                @foreach($groupedPages as $page)
                    <?php $completionPercentage = $page->completionPercentage(); ?>
                    <li>
                        <a class="block hover:bg-gray-50 px-4 py-4 sm:px-6" href="{{ route('squanto.edit',$page->slug()) }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-800 truncate">
                                        {{ $page->label() }}
                                    </p>

                                    @if($page->namespace())
                                        <p class="mt-1 text-xs text-gray-500">{{ $page->namespace() }}::{{ $page->group() }}</p>
                                    @endif
                                </div>

                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                     {{ $completionPercentage == 100 ? 'bg-green-100 text-green-800' : ($completionPercentage < 50 ? 'bg-pink-100 text-pink-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $completionPercentage }}%
                                    </p>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            @endforeach
        @endif
    </ul>
</div>

</x-squanto::app-layout>
