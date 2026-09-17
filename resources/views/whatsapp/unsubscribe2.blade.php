<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Whatsapp Unsubscribe</title>
</head>
<body>
    @if (isset($post))
        <p>Plesase Click on confirm button to stop whatsapp message!</p>
        <p>कृपया व्हाट्सएप संदेश रोकने के लिए पुष्टि बटन पर क्लिक करें!</p>
        <form action="{{ url('whatsapp/unsubscribe/request/allcontact/'.$post->random_string.'/'.$post->allcontact_id.'/stop') }}" method="POST">
            @csrf
            <button type="submit">Unsubscribe</button>
        </form>
    @else
        <h3>Failed</h3>
    @endif
</body>
</html>
