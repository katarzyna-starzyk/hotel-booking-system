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

    if (isset($_GET['id']))
    {
        deleteHotel($_GET['id']);
        header("Location: admin_manage_hotels.php");
        exit();
    }

    $hotels = getDeletableHotels();

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

                <h2>Hotele możliwe do usunięcia</h2>

                <div class="row">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th scope="col">ID hotelu</th>
                            <th scope="col">Nazwa</th>
                            <th scope="col">Adres</th>
                            <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach ($hotels as $hotel)
                                {
                                    echo '<tr>';
                                        echo '<th scope="row">'.$hotel["hotel_id"].'</th>';
                                        echo '<td>'.$hotel["name"].'</td>';
                                        echo '<td>'.$hotel["street"].' '.$hotel["building_nr"];
                                        if (!is_null($hotel["apartment_nr"])){echo '/'.$hotel["apartment_nr"];}
                                        echo '<br/>'.$hotel["zip_code"].' '.$hotel["city"].'</td>';
                                        echo '<td><a href="?id='.$hotel["hotel_id"].'" class="btn btn-warning btn-sm">Usuń hotel</a></td>';
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                    </table>
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