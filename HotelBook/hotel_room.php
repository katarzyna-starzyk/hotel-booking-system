<?php
    $roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
    include 'php/functions.php';
    $room = getRoom($roomId);

    $currentDate = new DateTime();
    $endDate = clone $currentDate;
    $endDate->modify('+1 year');
    $occupiedDates = getDates($roomId);
    //header("room.php?room_id=$roomId");
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HotelBook</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        window.addEventListener("pageshow", function ( event )
        {
            var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
            if ( historyTraversal )
            {
                window.location.reload();
            }
        });
        document.addEventListener('DOMContentLoaded', function ()
        {
            var disabledRanges = <?php echo json_encode(array_map(function ($range){ return [$range['start_date'], $range['end_date']];}, $occupiedDates)); ?>;
            var disabledDates = [];
            for (var j = 0; j < disabledRanges.length; j++)
            {
                disabledDates.push({
                    from: disabledRanges[j][0],
                    to: disabledRanges[j][1]
                });
            }

            console.log(disabledRanges);
            flatpickr('#calendar',
            {
                input: 'disable',
                inline: true,
                minDate: 'today',
                dateFormat: 'Y-m-d',
                disable: disabledDates,
                mode: 'range',
            });
        });
    </script>

</head>

<body class="container-fluid h-100 text-center p-0" data-bs-theme="dark">

    <?php
        include("php/header.php");
    ?>
    
    <main class="container-md bg-body-tertiary">
        <div class="container-fluid">
            <?php
                echo "<h2>Pokój nr. {$room['number']}</h2>";

                echo "<div class='naglowek row mb-2 align-items-center justify-content-center'>";
                    echo "<div class='col-3'>Liczba łóżek</div>";
                    echo "<div class='col-3'>Maksymalna liczba gości</div>";
                    echo "<div class='col-3'>Cena</div>";
                echo "</div>";

                echo "<div class='row mb-2 align-items-center justify-content-center'>";
                    echo "<div class='col-3'>{$room['beds']}</div>";
                    echo "<div class='col-3'>{$room['max_guests']}</div>";
                    echo "<div class='col-3'>{$room['price']}</div>";
                echo "</div>";

                echo "<div class='row mb-2 mt-5 align-items-center justify-content-center'>";
                    echo "<h3>Sprawdź wolne terminy</h3>";
                    echo "<form action='bookdate.php' method='POST'>";
                    echo "<input class='invisible' type='number' name='room_id' value={$room["room_id"]}>";
                    echo "<input id='calendar' name='date' placeholder='Dostępne daty...' class='date' />";
                    echo "<input type='submit' value='Zarezerwuj termin' />";
                    echo "</form>";
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