<!DOCTYPE html>
<html lang="id" data-footer="true" data-url-prefix="/">
@include('layouts.head')

<body>
    <div id="root">
        @include('layouts.nav')
        <main>
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>

    @include('layouts.theme-settings')
    @include('layouts.theme-modals')

    @include('layouts.scripts')
    @stack('scripts')
</body>

</html>
