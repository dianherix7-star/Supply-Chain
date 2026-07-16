<!DOCTYPE html>
<html>
<head>
    <title>Dashboard User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-primary bg-primary">
    <div class="container">

        <span class="navbar-brand text-white">
            Supply Chain Management
        </span>

        <span class="text-white">
            User
        </span>

    </div>
</nav>

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body">

            <h2>Dashboard User</h2>

            <hr>

            <h5>Selamat Datang, {{ Auth::user()->name }}</h5>

            <p>Role : {{ Auth::user()->role }}</p>

        </div>

    </div>

</div>

</body>
</html>