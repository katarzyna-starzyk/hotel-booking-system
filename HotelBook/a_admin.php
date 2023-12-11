<?php
    session_start();

    include("php/functions.php");

    $userId = $_SESSION['user_id'];

    if ($_SESSION['isAdmin'] == 1)
    {
        header("Location: a_hotel.php");
        exit();
    }

    else if ($_SESSION['isAdmin'] == 0)
    {
        header("Location: a_user.php");
        exit();
    }

    if (!isset($_SESSION['user_id']))
    {
        header("Location: login.php");
        exit();
    }

    if (isset($_GET['logout']))
    {
        session_destroy();
        header("Location: login.php");
        exit();
    }


?>

<!DOCTYPE html>
<html lang="pl">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HotelBook</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="container-fluid h-100 text-center p-0" data-bs-theme="dark">

    <?php
        include("php/header.php");
    ?>
    
    <main class="container-md bg-body-tertiary">

        <div class="container-fluid py-5">

            <div class="row bg-body-secondary justify-content-around align-items-center py-5">

                <div class="col-xl-6 py-2">

                    <h2>Panel admina</h2>

                    <div class="row">
                        <table class="table table-striped table-hover">
                            <tbody>
                                <tr><td><a href="admin_manage_hotels.php" class="btn btn-warning btn-sm">Usuwaj hotele</a></td></tr>
                                <tr><td><a href="admin_add_hotels.php" class="btn btn-warning btn-sm">Dodawaj hotele</a></td></tr>
                                <tr><td><a href="admin_manage_reservations.php" class="btn btn-warning btn-sm">Usuwaj rezerwacje</a></td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <a href="?logout=true" class="btn btn-danger mt-3">Wyloguj się</a>

        </div>

    </main>

    <?php
        include("php/footer.php");
    ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>