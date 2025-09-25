<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AccessTree Error</title>
    <link rel="stylesheet" href="{{ asset('css/error.css') }}">

</head>

<body>
    <div class="accesstree-error-wrapper">
        <div class="accesstree-error-box">
            <h1>AccessTree Error</h1>
            <p>{{ $message ?? 'An unknown error occurred.' }}</p>
            <a href="{{ url()->previous() }}">Go Back</a>
        </div>
    </div>
</body>

</html>
