<x-squanto::app-layout>

@section('page-title')
    <a href="{{ route('squanto.index') }}">Translations</a>
@stop

<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <ul class="divide-y divide-gray-200">

        @foreach($pages as $page)
            <?php $completionPercentage = $page->completionPercentage(); ?>
            <li>
                <a class="block hover:bg-gray-50 px-4 py-4 sm:px-6" href="{{ route('squanto.edit',$page->slug()) }}">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-800 truncate">
                                {{ $page->label() }}
                            </p>
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
    </ul>
</div>


</x-squanto::app-layout>
