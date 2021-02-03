<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name') }}</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
  <div id="app">
    <header-component></header-component>
    <router-view></router-view>
  </div>
  
  <script src="{{ mix('/js/app.js') }}" defer></script>
</body>

</html>