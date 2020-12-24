<x-app-layout>

    <x-slot name="customHead">
        <link rel="stylesheet" href="{{ asset('/back/redactor/redactor.css') }}">

        <script src="{{ asset('/back/redactor/redactor.js') }}"></script>
        <script>
            // Defer initiation when dom is ready
            document.addEventListener('DOMContentLoaded', function(){
                if(document.querySelectorAll('.redactor-editor').length > 0) {
                    $R('.redactor-editor', {
                        paragraphize: false,
                    });
                }
            });

        </script>
        @include('squanto::_preventDuplicateSubmissions')
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Squanto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
