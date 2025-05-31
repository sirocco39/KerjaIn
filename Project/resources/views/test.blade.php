<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Test</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="p-5">

    <h1 class="mb-4">Test Warna Bootstrap + Custom</h1>

    <div class="mb-3 p-3 text-white bg-primary">Primary</div>
    <div class="mb-3 p-3 text-white bg-secondary">Secondary</div>
    <div class="mb-3 p-3 text-dark bg-success">Success</div>
    <div class="mb-3 p-3 text-white bg-danger">Danger</div>
    <div class="mb-3 p-3 text-dark bg-light border">Light</div>
    <div class="mb-3 p-3 text-white bg-dark">Dark</div>

    <hr>

    <div class="mb-3 p-3 text-dark bg-chat-gray">Custom: Chat Gray</div>
    <div class="mb-3 p-3 text-dark bg-stroke-gray">Custom: Stroke Gray</div>
    <div class="mb-3 p-3 text-white bg-dark-gray">Custom: Dark Gray</div>

</body>
</html>
