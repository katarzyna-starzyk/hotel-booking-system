<?php
    session_start();
    include('php/functions.php');

    if (!isset($_SESSION['user_id']))
    {
        session_destroy();
        header("Location: login.php");
        exit();
    }
    else
    {
        if (!isset($_GET['id']))
        {
            header("Location: a_user.php");
            exit();
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

                <h2>Edytuj rezerwację</h2>

                <form method="post" action="a_user.php">
                    <label for="newStartDate">Nowa Data Rozpoczęcia:</label>
                    <input type="date" name="newStartDate" required> <br/>
                    <label for="newEndDate">Nowa Data Zakończenia:</label>
                    <input type="date" name="newEndDate" required> 
                    <input type="hidden" name="bookingId" value=<?php echo $_GET['id'];?>> <br/>
                    <button type="submit" name="editReservation">Edytuj</button>
                </form>

            </div>

        </div>

    </main>

    <?php
        include("php/footer.php");
    ?>

    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>