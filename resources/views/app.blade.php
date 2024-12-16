<!DOCTYPE html>
<html>
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <link rel="icon" href="assets/logo/logo.png" type="image/png">



    <title>MDC School Event Calendar</title>
    @vite('resources/js/app.js')  
    @inertiaHead
  </head>
  <body>
    @inertia
  </body>
</html>