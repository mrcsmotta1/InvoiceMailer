<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>@yield('title') - {{ config('app.name') }}</title>
</head>
<body>
    <head>
        @yield('header')
    </head>
    <div class="content">
        @yield('content')
    </div>
    <footer>
       @yield('footer')
    </footer>
</body>
</html>