<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/app.css" rel="stylesheet">
    <style>
        * {

        }
        body{
            background-color: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
        }
    </style>
    <script>
        function printPage() { print(); } //Must be present for Iframe printing
    </script>
</head>
<body id="app">
    @yield('content')
</body>
</html>
