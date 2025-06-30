<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>

    <script src="https://js.pusher.com/8.3.0/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <div class="chat">

        <div class="top">
            <div>
                <p>Ross Edlin</p>
                <small>Online</small>
            </div>
        </div>

        <div class="message">
            @include('receive', ['message' => 'Hello, how are you?'])
        </div>

        <div class="bottom">
            <form>
                <input type="text" id="message" name='message' placeholder="Type a message..." autocomplete="off">
                <button type="submit"></button>
            </form>
        </div>

    </div>
</body>
</html>