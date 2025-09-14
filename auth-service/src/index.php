<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <style>
    body {
      background-color: #f4f7fc;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      text-align: center;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 50px;
      width: 100%;
      max-width: 500px;
    }

    .btn {
      font-size: 18px;
      margin: 10px 0;
      padding: 15px 30px;
      border-radius: 50px;
      transition: all 0.3s ease;
      width: 100%;
    }

    .btn:hover {
      transform: scale(1.05);
    }

    .btn-admin {
      background-color: #007bff;
      color: white;
    }

    .btn-doctor {
      background-color: #28a745;
      color: white;
    }

    .btn-pharmacist {
      background-color: #dc3545;
      color: white;
    }

    .btn-reception {
      background-color: #ffc107;
      color: white;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1 class="mb-4">Welcome to the Healthcare System</h1>
    <p class="lead mb-5">Please choose your role to proceed</p>

    <a href="/admin/" class="btn btn-admin">Admin</a>
    <a href="/doc/" class="btn btn-doctor">Doctor</a>
    <a href="/pharmacy/" class="btn btn-pharmacist">Pharmacist</a>
    <a href="backend/reception/indexRecep.php" class="btn btn-reception">Reception</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>