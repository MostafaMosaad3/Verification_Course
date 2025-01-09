<!DOCTYPE html>

<html
    lang="en"
    class="light-style customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{asset('assets-merchant')}}/"
    data-template="vertical-menu-template-free"
>
@include('merchant.partials.auth.head')


<body>
<!-- Content -->


@yield('content')


<!-- / Content -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
@include('merchant.partials.auth.scripts')
</body>
</html>
