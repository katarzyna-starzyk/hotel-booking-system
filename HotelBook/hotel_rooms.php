<?php
    $hotelId = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;
    include 'php/functions.php';
    $hotel = getHotel($hotelId);

?>

<!DOCTYPE html>
<html lang="en">

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
            <?php
                echo "<h2 style='display-2'>{$hotel['type']} \"{$hotel['name']}\"</h2>";

                $rooms = getRoomsInHotel($hotelId);

                echo '<section class="py-2">';
                echo '<div class="container px-4 px-lg-5 mt-5">';
                echo '<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 justify-content-center">';

                if (!empty($rooms))
                {
                
                    foreach ($rooms as $room)
                    {
                        echo '<div class="col mb-5">';
                            echo '<div class="card h-100">';
                                echo '<img class="card-img-top" src="media/6c757d.jpg" alt="'.$room["number"].'" />';
                                echo '<div class="card-body p-4">';
                                    echo '<div class="text-center">';
                                        echo '<h5 class="fw-bolder">Pokój numer '.$room["number"].'</h5>';
                                        echo '<p>Dla '.$room["max_guests"];
                                        if ($room["max_guests"]==1){echo ' osoby<br/>';} else {echo ' osób<br/>';}
                                        echo $room["price"].' zł</p>';
                                    echo '</div>';
                                echo '</div>';
                                echo '<div class="card-footer p-4 pt-0 border-top-0 bg-transparent">';
                                    echo '<div class="text-center">';
                                    echo '<a class="btn btn-secondary mt-auto" href="hotel_room.php?room_id='.$room["room_id"].'">Zobacz pokój</a>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    }
                }
                else
                {
                    echo '<p>Brak dostępnych pokoi.</p>';
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