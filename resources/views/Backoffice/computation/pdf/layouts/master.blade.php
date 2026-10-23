<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Computation of Income – {{ $computation->client->name ?? 'N/A' }}</title>
    @include('Backoffice.computation.pdf.layouts.styles')
</head>
<body>
    @include('Backoffice.computation.pdf.layouts.header')

    <div class="pdf-content">
        @yield('content')
    </div>

    @include('Backoffice.computation.pdf.layouts.footer')
</body>
</html>