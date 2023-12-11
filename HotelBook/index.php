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

            <div id="carouselDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
                
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    <button type="button" data-bs-target="#carouselDark" data-bs-slide-to="3" aria-label="Slide 4"></button>
                    <button type="button" data-bs-target="#carouselDark" data-bs-slide-to="4" aria-label="Slide 5"></button>
                    <button type="button" data-bs-target="#carouselDark" data-bs-slide-to="5" aria-label="Slide 6"></button>
                    <button type="button" data-bs-target="#carouselDark" data-bs-slide-to="6" aria-label="Slide 7"></button>
                    <button type="button" data-bs-target="#carouselDark" data-bs-slide-to="7" aria-label="Slide 8"></button>
                </div>

                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <a href="hotels.php?city=&type=Hostel">
                            <img src="media/photo.jpg" class="d-block w-100" alt="hostele">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Hostele</h5>
                            </div>
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="hotels.php?city=&type=Hotel">
                            <img src="media/photo.jpg" class="d-block w-100" alt="hotele">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Hotele</h5>
                            </div>
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="hotels.php?city=&type=Hotel+2*">
                            <img src="media/photo.jpg" class="d-block w-100" alt="hotele 2-gwiazdkowe">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Hotele 2-gwiazdkowe</h5>
                            </div>
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="hotels.php?city=&type=Hotel+3*">
                            <img src="media/photo.jpg" class="d-block w-100" alt="hotele 3-gwiazdkowe">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Hotele 3-gwiazdkowe</h5>
                            </div>
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="hotels.php?city=&type=Hotel+4*">
                            <img src="media/photo.jpg" class="d-block w-100" alt="hotele 4-gwiazdkowe">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Hotele 4-gwiazdkowe</h5>
                            </div>
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="hotels.php?city=&type=Hotel+5*">
                            <img src="media/photo.jpg" class="d-block w-100" alt="hotele 5-gwiazdkowe">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Hotele 5-gwiazdkowe</h5>
                            </div>
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="hotels.php?city=&type=Apartament">
                            <img src="media/photo.jpg" class="d-block w-100" alt="apartamenty">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Apartamenty</h5>
                            </div>
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="hotels.php?city=&type=Domek+letniskowy">
                            <img src="media/photo.jpg" class="d-block w-100" alt="domki letniskowe">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Domki letniskowe</h5>
                            </div>
                        </a>
                    </div>

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselDark" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#carouselDark" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>

            </div>
        </div>

    </main>

    <?php
        include("php/footer.php");
    ?>

    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>