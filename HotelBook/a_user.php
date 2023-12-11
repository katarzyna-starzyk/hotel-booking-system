<?php
    session_start();

    include("php/functions.php");

    $userId = $_SESSION['user_id'];

    if ($_SESSION['isAdmin'] == 2)
    {
        header("Location: a_admin.php");
        exit();
    }

    if (isset($_SESSION['booking']))
    {
        header("Location: bookdate.php");
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

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editReservation']))
    {
        $bookingId = $_POST['bookingId'];
        $newStartDate = $_POST['newStartDate']." 12:00:00";
        $newEndDate = $_POST['newEndDate']." 10:00:00";

        $edit = editReservation($userId, $bookingId, $newStartDate, $newEndDate);

        if ($edit == 0)
        {
            header("Location: a_user.php");
            exit();
        }
        else
        {
            $error = "Wystąpił błąd podczas edycji rezerwacji - ".$edit;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancelReservation']))
    {
        $bookingId = $_POST['bookingId'];

        $cancel = cancelReservation($userId, $bookingId);
    
        if ($cancel == 0)
        {
            header("Location: a_user.php");
            exit();
        }
        else
        {
            $error = "Wystąpił błąd podczas usuwania rezerwacji - ".$cancel;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addRatingReview']))
    {
        $bookingId = $_POST['bookingId'];
        $rating = $_POST['rating'];
        $review = $_POST['review'];

        $rating = addRatingAndReview($userId, $bookingId, $rating, $review);
    
        if ($rating == 0)
        {
            header("Location: a_user.php");
            exit();
        }
        else
        {
            $error = "Wystąpił błąd podczas dodawania oceny i recenzji - ".$rating;
        }
    }

    $userData = getUserData($_SESSION['user_id']);
    $bookings = getUserReservations($_SESSION['user_id']);

    $pastBookings = [];
    $futureBookings = [];

    foreach ($bookings as $booking)
    {
        if ($booking['expired'])
        {
            $pastBookings[] = $booking;
        }
        else
        {
            $futureBookings[] = $booking;
        }
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

                    <h2>Dane Użytkownika</h2>

                    <div class="row py-3 justify-content-around">
                        <div class="col-5 text-uppercase text-right">
                            <div class="row">Login</div>
                            <div class="row">Imię</div>
                            <div class="row">Nazwisko</div>
                            <div class="row">E-mail</div>
                            <div class="row">Data urodzenia</div>
                        </div>
                        <div class="col-5">
                            <div class="row"><?php echo $userData['username']; ?></div>
                            <div class="row"><?php echo $userData['first_name']; ?></div>
                            <div class="row"><?php echo $userData['last_name']; ?></div>
                            <div class="row"><?php echo $userData['email']; ?></div>
                            <div class="row"><?php echo $userData['birth_date']; ?></div>
                        </div>
                    </div>
                    <a href="?logout=true" class="btn btn-danger mt-3">Wyloguj się</a>

                </div>


                <div class="col-xl-6 py-2">

                    <h2>Rezerwacje</h2>

                    <div class="row">
                        <table class="table table-striped table-hover caption-top">
                            <caption>Nadchodzące</caption>
                            <thead>
                                <tr>
                                <th scope="col">Pokój</th>
                                <th scope="col">Data zameldowania</th>
                                <th scope="col">Data wymeldowania</th>
                                <th scope="col">Cena</th>
                                <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach ($futureBookings as $booking)
                                    {
                                        echo '<tr>';
                                            echo '<th scope="row">'.$booking["room_id"].'</th>';
                                            echo '<td>'.$booking["start_date"].'</td>';
                                            echo '<td>'.$booking["end_date"].'</td>';
                                            echo '<td>'.$booking["final_price"].'</td>';
                                            echo '<td>';
                                            if (strtotime($booking['start_date']) > strtotime("+1 day"))
                                            {
                                                echo '<a href="user_edit_reservation.php?id='.$booking["booking_id"].'" class="btn btn-warning btn-sm">Edytuj</a>';
                                                echo '<a href="user_cancel_reservation.php?id='.$booking["booking_id"].'" class="btn btn-danger btn-sm">Usuń</a>';
                                            }
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <table class="table table-striped table-hover caption-top">
                            <caption>Historia</caption>
                            <thead>
                                <tr>
                                <th scope="col">Pokój</th>
                                <th scope="col">Data pobytu</th>
                                <th scope="col">Ocena</th>
                                <th scope="col">Recenzja</th>
                                <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach ($pastBookings as $booking)
                                    {
                                        echo '<tr>';
                                            echo '<th scope="row">'.$booking["room_id"].'</th>';
                                            echo '<td>OD: '.$booking["start_date"].'<br/>DO: '.$booking["end_date"].'</td>';
                                            if ($booking['rating'] == null)
                                            {
                                                echo '<td colspan="3">';
                                                echo '<a href="user_add_review.php?id='.$booking["booking_id"].'" class="btn btn-warning btn-sm">Oceń</a>';
                                                echo '</td>';
                                            }
                                            else
                                            {
                                                echo '<td>'.$booking["rating"].'</td>';
                                                echo '<td>'.$booking["review"].'</td>';
                                                echo '<td>';
                                                echo '<a href="user_add_review.php?id='.$booking["booking_id"].'" class="btn btn-warning btn-sm">Edytuj ocenę</a>';
                                                echo '</td>';
                                            }
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <?php
        include("php/footer.php");
    ?>

    <script src="js/bootstrap.bundle.min.js"></script>

    <?php
        if (isset($error))
        {
            echo '<script>alert("'.$error.'");</script>';
            unset($_POST['editReservation']);
            unset($error);
        }
        ?>
</body>

</html>