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

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addHotel']))
    {
        $name = $_POST['name'];
        $street = $_POST['street'];
        $building_nr = $_POST['building_nr'];
        $apartment_nr = isset($_POST['apartment_nr']) ? intval($_POST['apartment_nr']) : 0;
        $zip_code = $_POST['zip_code'];
        $city = $_POST['city'];
        $type_id = intval($_POST['type_id']);
        addHotel($name, $street, $building_nr, $apartment_nr, $zip_code, $city, $type_id);
        header("Location: admin_add_hotels.php");
        exit();
    }

    $hotels = getAllHotels();
    $hotelstypes = getHotelsTypes();

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

                <div class="table-responsive">
                <form action="admin_add_hotels.php" method="post">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Nazwa</th>
                                <th scope="col">Ulica</th>
                                <th scope="col">Numer budynku</th>
                                <th scope="col">Numer mieszkania</th>
                                <th scope="col">Kod pocztowy</th>
                                <th scope="col">Miasto</th>
                                <th scope="col">Typ</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                                <tr>
                                    <td scope="col"><input type="text" name="name" required></td>
                                    <td scope="col"><input type="text" name="street" required></td>
                                    <td scope="col"><input type="number" name="building_nr" required></td>
                                    <td scope="col"><input type="number" name="apartment_nr"></td>
                                    <td scope="col"><input type="text" name="zip_code" required pattern="[0-9]{2}-[0-9]{3}"></td>
                                    <td scope="col"><input type="text" name="city" required></td>
                                    <td scope="col">
                                        <select name="type_id" id="type">
                                        <?php
                                            foreach ($hotelstypes as $type)
                                            {
                                                echo '<option value="'.$type['type_id'].'">'.$type['type'].'</option>';
                                            }
                                        ?>
                                        </select>
                                    </td>
                                </tr>
                                
                        </tbody>
                    </table>
                    <input type="submit" name="addHotel" value="dodaj">
                    </form>
                </div>

                <div class="row">
                    <h2>Lista hoteli</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th scope="col">ID hotelu</th>
                            <th scope="col">Nazwa</th>
                            <th scope="col">Typ</th>
                            <th scope="col">Adres</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach ($hotels as $hotel)
                                {
                                    echo '<tr>';
                                        echo '<th scope="row">'.$hotel["hotel_id"].'</th>';
                                        echo '<td>'.$hotel["name"].'</td>';
                                        echo '<td>'.$hotel["type"].'</td>';
                                        echo '<td>'.$hotel["street"].' '.$hotel["building_nr"];
                                        if (!is_null($hotel["apartment_nr"])){echo '/'.$hotel["apartment_nr"];}
                                        echo '<br/>'.$hotel["zip_code"].' '.$hotel["city"].'</td>';
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