<?php
    include 'php/functions.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $birthdate = $_POST['birthdate'];
        $vadidatedate = strtotime("-18 years");

        if (checklogins($username))
        {
            if(checkemails($email))
            {
                if (strtotime($birthdate) < $vadidatedate)
                {
                    if (register($username, $password, $email, $firstname, $lastname, $birthdate))
                    {
                        header('Location: login.php');
                        exit();
                    }
                    else
                    {
                        $registrationError = 'Rejestracja nie powiodła się. Spróbuj ponownie.';
                    }
                }
                else
                {
                    $registrationError = 'Musisz mieć więcej niż 18 lat, aby się zarejestrować';
                }
            }
            else
            {
                $registrationError = 'Użytkownik z tym emailem już istnieje.';
            }

        }
        else
        {
            $registrationError = 'Użytkownik o podanym loginie już istnieje.';
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
                    <div class="col-12 col-xl-6 col-md-8 mx-auto">
                        <form method="post" action="register.php">
                            <div class="divider d-flex align-items-center my-4 col-12">
                                <p class="text-center fw-bold mx-3 mb-0">REJESTRACJA</p>
                            </div>

                            <!-- Username input -->
                            <div class="row mb-3">
                                <label for="username" class="col-sm-3 col-form-label">Login</label>
                                <div class="col-sm-9">
                                    <input type="text" name="username" class="form-control" placeholder="login" />
                                </div>
                            </div>

                            <!-- E-mail input -->
                            <div class="row mb-3">
                                <label for="email" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" class="form-control" placeholder="e-mail" />
                                </div>
                            </div>

                            <!-- Password input -->
                            <div class="row mb-3">
                                <label for="password" class="col-sm-3 col-form-label">Hasło</label>
                                <div class="col-sm-9">
                                    <input type="password" name="password" class="form-control" placeholder="hasło" />
                                </div>
                            </div>

                            <!-- First name input -->
                            <div class="row mb-3">
                                <label for="firstname" class="col-sm-3 col-form-label">Imię</label>
                                <div class="col-sm-9">
                                    <input type="text" name="firstname" class="form-control" placeholder="imię" />
                                </div>
                            </div>


                            <!-- Last name input -->
                            <div class="row mb-3">
                                <label for="lastname" class="col-sm-3 col-form-label">Nazwisko</label>
                                <div class="col-sm-9">
                                    <input type="text" name="lastname" class="form-control" placeholder="nazwisko" />
                                </div>
                            </div>

                            <!-- Birth date input -->
                            <div class="row mb-3">
                                <label for="birthdate" class="col-sm-3 col-form-label">Data urodzenia</label>
                                <div class="col-sm-9">
                                    <input type="date" name="birthdate" class="form-control"/>
                                </div>
                            </div>

                            <div class="d-flex mb-3 justify-content-center align-items-center">
                                <button type="submit" class="btn btn-secondary btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem;">Zarejestruj</button>
                            </div>

                            <div class="d-flex mb-3 justify-content-center align-items-center">
                                <span> Masz już konto? <a href="login.php" class="text-body"> Zaloguj się</a> </span>
                            </div>

                            <?php
                                if (isset($registrationError))
                                {
                                    echo "<div class='d-flex mb-3 justify-content-center align-items-center'><p style='color: red;'>$registrationError</p></div>";
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
