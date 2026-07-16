<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body{
            background:#f4f6f9;
        }

        .sidebar{
            width:250px;
            height:100vh;
            position:fixed;
            background:#0d6efd;
            color:white;
        }

        .sidebar a{
            color:white;
            text-decoration:none;
            display:block;
            padding:15px 20px;
            transition:.3s;
        }

        .sidebar a:hover{
            background:#0b5ed7;
        }

        .content{
            margin-left:250px;
        }

        .navbar{
            height:65px;
        }

        .card{
            border:none;
            border-radius:15px;
        }
    </style>

</head>

<body>

<div class="sidebar">

    <h3 class="text-center mt-4">
        SCM
    </h3>

    <hr>

    <a href="{{ route('admin.dashboard') }}">
    <i class="bi bi-speedometer2"></i>
    Dashboard
</a>

<a href="{{ route('countries.index') }}">
    <i class="bi bi-globe"></i>
    Country
</a>

<a href="#">
    <i class="bi bi-cloud-sun"></i>
    Weather
</a>

<a href="#">
    <i class="bi bi-currency-dollar"></i>
    Currency
</a>

<a href="#">
    <i class="bi bi-newspaper"></i>
    News
</a>

<a href="#">
    <i class="bi bi-exclamation-triangle"></i>
    Risk Score
</a>

    <a href="#">
        <i class="bi bi-box-arrow-right"></i>
        Logout
    </a>

</div>


<div class="content">

<nav class="navbar navbar-light bg-white shadow-sm px-4">

    <h4>
        Supply Chain Management
    </h4>

</nav>

<div class="container-fluid mt-4">

    @yield('content')

</div>

</div>

</body>
</html>