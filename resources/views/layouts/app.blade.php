<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'e-Cidade')</title>
    <style>
        :root {
            --ecidade-contass-default-btn-bg: #429566;
            --ecidade-contass-danger-btn-bg: #dc3545;
        }

        body {
            font-family: Arial, Helvetica, serif, sans-serif, verdana;
            font-size: 12px;
            color: #000000;
        }

        body.body-default {
            margin: 0;
            background: #F5FFFB;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/accessibility.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.0/dist/sweetalert2.all.min.js"></script>

</head>
<body class="body-default no-menu">

<main class="container mt-4" id="app_main_container">
    @include('layouts.flash-messages')
    @yield('content')
</main>

@include('layouts.accessibility-toolbar')
<script src="{{ asset('assets/js/accessibility.js') }}"></script>

</body>
</html>
