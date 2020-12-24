<x-squanto::app-layout>

    <form method="POST" action="{{ route('squanto.update', $page->slug()) }}" role="form">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">

        @foreach(collect($lines)->groupBy(function($lineViewModel){ return $lineViewModel->sectionKey(); }) as $sectionKey => $groupedLines)
            <div class="{{ $loop->first ? '' : 'mt-10' }}">
                <div class="md:grid md:grid-cols-4 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0 py-3">
                            <h2 class="text-lg font-medium leading-6 text-gray-900">{{ ucfirst($sectionKey) }}</h2>
                            <p class="mt-1 text-sm text-gray-600"></p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-3">
                        <div class="shadow overflow-hidden sm:rounded-md">
                            <div class="px-4 py-5 bg-white sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    @foreach($groupedLines as $lineViewModel)
                                        <div class="col-span-6">
                                            @include('squanto::_field')
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


        <div class="mt-6 mb-10">
            <div class="md:grid md:grid-cols-4">
                <div class="md:col-span-1"></div>
                <div class="mt-5 md:mt-0 md:col-span-3">
                    <div class="px-4 py-3 sm:px-6 flex items-center justify-left">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Bewaar aanpassingen
                        </button>
                        <a class="ml-4 text-gray-600 text-sm" href="{{ route('squanto.index') }}">Terug naar overzicht</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

</x-squanto::app-layout>

