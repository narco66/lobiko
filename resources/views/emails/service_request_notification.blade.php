<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande {{ $module }}</title>
</head>
<body>
    <h2>Nouvelle demande {{ $module }}</h2>
    <p>Une nouvelle demande a été soumise via le site.</p>
    <ul>
        @foreach($data as $key => $value)
            @if(!is_array($value) && $value !== null && $value !== '')
                <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }} :</strong> {{ $value }}</li>
            @endif
        @endforeach
    </ul>
</body>
</html>
