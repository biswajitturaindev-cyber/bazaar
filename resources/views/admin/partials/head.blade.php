<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reshera Admin')</title>
    <link rel="shortcut icon" href="{{asset('admin_assets/images/favicon.ico')}}" type="image/x-icon">
    <!-- Remix & Iconior Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/iconoir-icons/iconoir@main/css/iconoir.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/cropperjs/dist/cropper.min.css" />
    
    <script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>
    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{asset('admin_assets/css/style.css')}}">
    <!-- Tailwind Style -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <style>
    .live-badge{
        background:red;
        color:white;
        font-size:10px;
        padding:2px 6px;
        border-radius:4px;
        animation:blink 1s infinite;
    }
    
    @keyframes blink{
        0%{opacity:1;}
        50%{opacity:0.3;}
        100%{opacity:1;}
    }
    </style>
</head>