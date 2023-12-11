<?php
    include 'php/functions.php';
    $distinctCities = getDistinctFieldValues('city', 'hotels');
    $distinctTypes = getDistinctFieldValues('type', 'hotelstypes');

    $cityFilter = isset($_GET['city']) ? $_GET['city'] : '';
    $typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        $hotels = getFilteredHotels($cityFilter, $typeFilter);
    }
    else
    {
        $hotels = getHotels();
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
        <div class="container-fluid">

            <h2 class="display-2">Lista Hoteli</h2>

            <form method="get" action="">

                <label for="city">Miasto:</label>
                <select name="city" id="city">

                    <option value="">Wszystkie</option>
                    <?php
                    foreach ($distinctCities as $city)
                    {
                        echo "<option value=\"$city\"";
                        if (isset($_GET['city']) && $_GET['city'] == $city) {echo " selected";}
                        echo ">$city</option>";
                    }
                    ?>

                </select>

                <label for="type">Typ hotelu:</label>
                <select name="type" id="type">

                    <option value="">Wszystkie</option>
                    <?php
                    foreach ($distinctTypes as $type)
                    {
                        echo "<option value=\"$type\"";
                        if (isset($_GET['type']) && $_GET['type'] == $type) {echo " selected";}
                        echo ">$type</option>";
                    }
                    ?>

                </select>

                <button class="btn btn-secondary" type="submit">Filtruj</button>
                
            </form>

            <?php
                echo '<section class="py-2">';
                echo '<div class="container px-4 px-lg-5 mt-5">';
                echo '<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">';

                if (!empty($hotels))
                {
                    foreach ($hotels as $hotel)
                    {
                        echo '<div class="col mb-5">';
                            echo '<div class="card h-100">';
                                echo '<img class="card-img-top" src="media/6c757d.jpg" alt="'.$hotel["type"].'" />';
                                echo '<div class="card-body p-4">';
                                    echo '<div class="text-center">';
                                        echo '<h5 class="fw-bolder">'.$hotel["type"].' "'.$hotel["name"].'"</h5>';
                                        echo '<p>'.$hotel["street"].' '.$hotel["building_nr"];
                                        if (!is_null($hotel["apartment_nr"])){echo '/'.$hotel["apartment_nr"];}
                                        echo '<br/>'.$hotel["zip_code"].' '.$hotel["city"].'</p>';
                                    echo '</div>';
                                echo '</div>';
                                echo '<div class="card-footer p-4 pt-0 border-top-0 bg-transparent">';
                                    echo '<div class="text-center">';
                                    echo '<a class="btn btn-secondary mt-auto" href="hotel_rooms.php?hotel_id='.$hotel["hotel_id"].'">Zobacz pokoje</a>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    }
                }
                else
                {
                    echo "<p></p>";
                }

                echo '</div></div></section>';
            ?>

        </div>

    </main>

    <?php
        include("php/footer.php");
    ?>

    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>