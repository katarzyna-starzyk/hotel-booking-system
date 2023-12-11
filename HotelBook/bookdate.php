<?php
    include 'php/functions.php';

    session_start();
    
    if (!isset($_SESSION['user_id']))
    {
        $_SESSION['booking'] = $_POST['date'];
        $_SESSION['booking_room'] = $_POST['room_id'];
        header("Location: login.php");
        exit();
    }

    if (!isset($_SESSION['booking']))
    {
        $date = $_POST['date'];
        $room_id = $_POST['room_id'];
    }
    else
    {
        $date = $_SESSION['booking'];
        $room_id = $_SESSION['booking_room'];
    }

    // if ($_SERVER['REQUEST_METHOD'] == 'POST')
    // {
    if (!isset($room_id))
    {
        header("Location: a_user.php");
        exit();
    }
    else
    {
        if (str_contains($date, 'to'))
        {
            $dates = explode(' to ', $date);
            $start_date = $dates[0].' 12:00:00';;
            $end_date = $dates[1].' 10:00:00';;
        }
        else
        {
            $start_date = $date;
            $end_date = $date;
        }
        $room = getRoom($room_id);
        $res = bookRoom($_SESSION['user_id'], $room_id, $start_date, $end_date);
        if (!$res)
        {
            header("Location: hotels.php");
            exit();
        }
    }

        //echo $room['number'].": Data od ".$start_date." do ".$end_date;
        // $_SESSION['booking'] = NULL;
        // $_SESSION['booking_room'] = NULL;
    //}
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
                echo "<h2>Zarezerwowano termin</h2>";

                echo "<div class='naglowek row mb-2 align-items-center justify-content-center'>";
                    echo "<div class='col-4'>Pokój nr. </div>";
                    echo "<div class='col-4'>Data zameldowania: </div>";
                    echo "<div class='col-4'>Data wymeldowania: </div>";
                echo "</div>";

                echo "<div class='row mb-2 align-items-center justify-content-center'>";
                    echo "<div class='col-4'>{$room['number']}</div>";
                    echo "<div class='col-4'>{$start_date}</div>";
                    echo "<div class='col-4'>{$end_date}</div>";
                echo "</div>";

                echo "<div class='row mb-2 align-items-center justify-content-center'>";
                    echo "<a class='btn btn-secondary' href='a_user.php'>Sprawdź swoje rezerwacje</a>";
                echo "</div>";

            ?>
        </div>
    </main>

    <?php
        include("php/footer.php");
    ?>

    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>
<?php
    unset($_SESSION['booking']);
    unset($_SESSION['booking_room']);
    unset($room_id);
    // unset($_POST['date']);
    // unset($_POST['room_id']);;
?>