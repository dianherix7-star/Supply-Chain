<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">
            Supply Chain Management
        </span>

        <span class="text-white">
            Admin
        </span>
    </div>
</nav>
@extends('layouts.app')

@section('content')

<h2 class="mb-4">
    Dashboard Admin
</h2>

<div class="row">

    <div class="col-md-3">

        <div class="card shadow">

            <div class="card-body">

                <h5>Total Country</h5>

                <h2>0</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card shadow">

            <div class="card-body">

                <h5>Weather Data</h5>

                <h2>0</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card shadow">

            <div class="card-body">

                <h5>News</h5>

                <h2>0</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card shadow">

            <div class="card-body">

                <h5>Risk Score</h5>

                <h2>0</h2>

            </div>

        </div>

    </div>

</div>

@endsection
</body>
</html>