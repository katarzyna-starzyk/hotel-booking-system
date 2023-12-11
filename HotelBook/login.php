<?php
    include 'php/functions.php';

    session_start();

    if (isset($_SESSION['user_id']))
    {
        if ($_SESSION['isAdmin'] == 1)
        {
            header("Location: a_hotel.php");
            exit();
        }

        else if ($_SESSION['isAdmin'] == 2)
        {
            header("Location: a_admin.php");
            exit();
        }

        else if ($_SESSION['isAdmin'] == 0)
        {
            header("Location: a_user.php");
            exit();
        }

        else
        {
            session_destroy();
            header("Location: login.php");
            exit();
        }
    }

    else if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (login($username, $password))
        {
            if ($_SESSION['isAdmin'] == 1)
            {
                header("Location: a_hotel.php");
                exit();
            }

            else if ($_SESSION['isAdmin'] == 2)
            {
                header("Location: a_admin.php");
                exit();
            }

            else if ($_SESSION['isAdmin'] == 0)
            {
                header("Location: a_user.php");
                exit();
            }

            else
            {
                session_destroy();
                $loginError = 'Błędne dane logowania. Spróbuj ponownie.';
                //exit();
            }
        }
        else
        {
            $loginError = 'Błędne dane logowania. Spróbuj ponownie.';
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

        <div class="container-fluid">

            <div class="row d-flex justify-content-center align-items-center h-100">

                <div class="col-12 col-md-6 mx-auto">

                    <form method="post" action="login.php">

                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0">LOGOWANIE</p>
                        </div>

                        <div class="form-outline mb-4">
                            <input type="text" name="username" class="form-control form-control-lg" placeholder="login" />
                        </div>

                        <div class="form-outline mb-3">
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="hasło" />
                        </div>

                        <div class="d-flex mb-3 justify-content-center align-items-center">
                            <button type="submit" class="btn btn-secondary btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem;">Zaloguj</button>
                        </div>

                        <div class="d-flex mb-3 justify-content-center align-items-center">
                            <span> Nie masz konta? <a href="register.php" class="text-body"> Zarejestruj się</a> </span>
                        </div>

                        <?php
                            if (isset($loginError))
                            {
                                echo "<div class='d-flex mb-3 justify-content-center align-items-center'><p style='color: red;'>$loginError</p></div>";
                            }
                        ?>

                    </form>

                </div>

            </div>

        </div>
        
    </main>

    <?php
        include("php/footer.php");
    ?>

    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>
