<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Squanto translations admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    @yield('custom-styles')
</head>

<body>

<div id="main">

    <section id="content_wrapper">
        <header id="topbar">
            <div class="topbar-left">
                <h3>
                    @yield('page-title')
                    <div class="topbar-inside-right">
                        @yield('topbar-right')
                    </div>
                </h3>
            </div>
        </header>

        @include('squanto::_defaults.errors')
        @include('squanto::_defaults.messages')

        <section id="content">
            @yield('content')
        </section>

        @include('squanto::_defaults.footer')
    </section>

</div>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
@yield('custom-scripts')

</body>
</html>