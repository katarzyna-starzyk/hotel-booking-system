<?php

    function connectToDatabase()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "hotel_db";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error)
        {
            die("Błąd połączenia z bazą danych: " . $conn->connect_error);
        }

        return $conn;
    }

    function login($username, $password)
    {
        $conn = connectToDatabase();

        $username = $conn->real_escape_string($username);
        $password = $conn->real_escape_string($password);

        $query = "SELECT * FROM users WHERE username = '$username';";
        $result = $conn->query($query);

        if ($result->num_rows == 1)
        {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password']))
            {
                session_start();

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['isAdmin'] = $user['isAdmin'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['birth_date'] = $user['birth_date'];

                $conn->close();

                return true;
            }
        }

        $conn->close();

        return false;
    }

    function checklogins($username)
    {
        $conn = connectToDatabase();

        $username = $conn->real_escape_string($username);
        $query = "SELECT * FROM users WHERE username = '$username';";
        $result = $conn->query($query);

        if ($result->num_rows != 0)
        {
            $conn->close();
            return false;
        }
        else
        {
            $conn->close();
            return true;
        }
    }

    function checkemails($email)
    {
        $conn = connectToDatabase();

        $email = $conn->real_escape_string($email);
        $query = "SELECT * FROM users WHERE email = '$email';";
        $result = $conn->query($query);

        if ($result->num_rows != 0)
        {
            $conn->close();
            return false;
        }
        else
        {
            $conn->close();
            return true;
        }
    }

    function register($username, $password, $email, $firstname, $lastname, $birthdate)
    {
        $conn = connectToDatabase();

        $username = $conn->real_escape_string($username);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $email = $conn->real_escape_string($email);
        $firstname = $conn->real_escape_string($firstname);
        $lastname = $conn->real_escape_string($lastname);

        $query = "INSERT INTO users VALUES (NULL, '$username', '$passwordHash', '$email', '$firstname', '$lastname', '$birthdate', 0);";
        $result = $conn->query($query);

        $conn->close();
        return $result;
    }

    function getHotels()
    {
        $conn = connectToDatabase();

        $query = "SELECT hotel_id, name, street, building_nr, apartment_nr, zip_code, city, hotels.type_id, type FROM hotels NATURAL JOIN hotelstypes ORDER BY hotels.city";
        $result = $conn->query($query);

        $hotels = [];

        while ($row = $result->fetch_assoc())
        {
            $hotels[] = $row;
        }

        $conn->close();
        return $hotels;
    }

    function getFilteredHotels($cityFilter, $typeFilter)
    {
        $conn = connectToDatabase();

        $query = "SELECT hotel_id, name, street, building_nr, apartment_nr, zip_code, city, hotels.type_id, type FROM hotels NATURAL JOIN hotelstypes WHERE 1";

        if (!empty($cityFilter))
        {
            $cityFilter = mysqli_real_escape_string($conn, $cityFilter);
            $query .= " AND city = '$cityFilter'";
        }

        if (!empty($typeFilter))
        {
            $typeFilter = mysqli_real_escape_string($conn, $typeFilter);
            $query .= " AND type = '$typeFilter'";
        }

        $result = $conn->query($query);

        if (!$result)
        {
            die("Błąd zapytania: " . mysqli_error($conn));
            $conn->close();
        }

        $hotels = [];

        while ($row = $result->fetch_assoc())
        {
            $hotels[] = $row;
        }

        $conn->close();
        return $hotels;
    }

    function getDistinctFieldValues($fieldName, $table)
    {
        $conn = connectToDatabase();
    
        $query = "SELECT DISTINCT $fieldName FROM $table";
    
        $result = $conn->query($query);
        $values = [];

        if ($result->num_rows > 0)
        {
            while ($row = $result->fetch_assoc())
            {
                $values[] = $row[$fieldName];
            }
        }
    
        $conn->close();
        return $values;
    }

    function getHotel($hotel_id)
    {
        $conn = connectToDatabase();

        $query = "SELECT name, street, building_nr, apartment_nr, city, hotels.type_id, type FROM hotels NATURAL JOIN hotelstypes WHERE hotel_id = $hotel_id ORDER BY hotels.city";
        $result = $conn->query($query);
        $hotel = $result->fetch_assoc();

        $conn->close();
        return $hotel;
    }

    function getRoomsInHotel($hotelId)
    {
        $conn = connectToDatabase();

        $query = "SELECT * FROM rooms WHERE hotel_id = $hotelId";
        $result = $conn->query($query);

        $rooms = [];

        while ($row = $result->fetch_assoc())
        {
            $rooms[] = $row;
        }

        $conn->close();
        return $rooms;
    }

    function getRoom($room_id)
    {
        $conn = connectToDatabase();

        $query = "SELECT * FROM rooms WHERE room_id = $room_id";
        $result = $conn->query($query);
        $room = $result->fetch_assoc();

        $conn->close();
        return $room;
    }

    function getDates($roomId)
    {
        $conn = connectToDatabase();

        $query = "SELECT start_date, end_date FROM bookings WHERE room_id = $roomId";
        $result = $conn->query($query);
        $occupiedDates = [];

        while ($row = $result->fetch_assoc())
        {
            $occupiedDates[] =
            [
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date']
            ];
        }

        $conn->close();

        return $occupiedDates;
    }

    function bookRoom($userId, $roomId, $checkInDate, $checkOutDate)
    {
        $conn = connectToDatabase();
        $u = (int)$userId;
        $r = (int)$roomId;

        $query = "SELECT * FROM bookings WHERE user_id = $u AND room_id = $r AND start_date = '$checkInDate' AND end_date = '$checkOutDate'";
        $result = $conn->query($query);

        if ($result->num_rows != 0)
        {
            $conn->close();
            return false;
        }
        else
        {
            $query2 = "INSERT INTO bookings VALUES (NULL, $u, $r, '$checkInDate', '$checkOutDate', NULL, NULL, NULL, 0)";
            $conn->query($query2);

            $conn->close();
            return true;
        }
        
    }

    function getUserData($userId)
    {
        $conn = connectToDatabase();
    
        $id = (int)$userId;
        $query = "SELECT * FROM users WHERE user_id = $id";
        $result = $conn->query($query);
    
        if ($result->num_rows == 1)
        {
            $userData = $result->fetch_assoc();

            $conn->close();
            return $userData;
        }
        else
        {
            $conn->close();
            return false;
        }
    }

    function getUserReservations($userId)
    {
        $conn = connectToDatabase();

        $id = (int)$userId;
        $query = "SELECT * FROM bookings WHERE user_id = $id";
        $result = $conn->query($query);

        $reservations = [];
        while ($row = $result->fetch_assoc())
        {
            $reservations[] = $row;
        }

        $conn->close();

        return $reservations;
    }

    function editReservation($userId, $reservationId, $newStartDate, $newEndDate)
    {
        $conn = connectToDatabase();
        $uid = (int)$userId;
        $bid = (int)$reservationId;
    
        $query = "UPDATE bookings SET start_date = '$newStartDate', end_date = '$newEndDate' WHERE booking_id = $bid AND user_id = $uid";
        try
        {
            $result = $conn->query($query); 
            $w = 0;
        }
        catch (mysqli_sql_exception $e)
        {
            $w = $e->getMessage();
        }
        finally
        {
            $conn->close();
            return $w; 
        } 

    }
    
    function cancelReservation($userId, $reservationId)
    {
        $conn = connectToDatabase();
        $uid = (int)$userId;
        $bid = (int)$reservationId;

        $query = "DELETE FROM bookings WHERE booking_id = $bid AND user_id = $uid";
        try
        {
            $result = $conn->query($query); 
            $w = 0;
        }
        catch (mysqli_sql_exception $e)
        {
            $w = $e->getMessage();
        }
        finally
        {
            $conn->close();
            return $w; 
        } 
    }

    function addRatingAndReview($userId, $reservationId, $rating, $review)
    {
        $conn = connectToDatabase();
        $uid = (int)$userId;
        $bid = (int)$reservationId;
    
        $query = "UPDATE bookings SET rating = $rating, review = '$review' WHERE booking_id = $bid AND user_id = $uid";
        try
        {
            $result = $conn->query($query); 
            $w = 0;
        }
        catch (mysqli_sql_exception $e)
        {
            $w = $e->getMessage();
        }
        finally
        {
            $conn->close();
            return $w; 
        } 
    }

    function getAllReservations()
    {
        $conn = connectToDatabase();

        $query = "SELECT * FROM bookings";
        $result = $conn->query($query);

        $reservations = [];
        while ($row = $result->fetch_assoc())
        {
            $reservations[] = $row;
        }

        $conn->close();

        return $reservations;
    }

    function getAllHotels()
    {
        $conn = connectToDatabase();
        $query = "SELECT hotel_id, name, street, building_nr, apartment_nr, zip_code, city, hotels.type_id, type FROM hotels NATURAL JOIN hotelstypes ORDER BY hotels.hotel_id DESC";
        
        //$query = "SELECT * FROM hotels";
        $result = $conn->query($query);

        $hotels = [];
        while ($row = $result->fetch_assoc())
        {
            $hotels[] = $row;
        }

        $conn->close();

        return $hotels;
    }

    function deleteHotel($hotelId)
    {
        $conn = connectToDatabase();
        $hid = (int)$hotelId;
        $query = "DELETE FROM hotels where hotel_id = $hid";
        $result = $conn->query($query);
        $conn->close();

        return $result;
    }

    function getAllUsers()
    {
        $conn = connectToDatabase();

        $query = "SELECT * FROM users";
        $result = $conn->query($query);

        $users = [];
        while ($row = $result->fetch_assoc())
        {
            $users[] = $row;
        }

        $conn->close();

        return $users;
    }

    function getDeletableHotels()
    {
        $conn = connectToDatabase();

        $query = "SELECT h.* FROM hotels h WHERE NOT EXISTS ( SELECT 1 FROM rooms r WHERE r.hotel_id = h.hotel_id AND r.room_id IN (SELECT DISTINCT b.room_id FROM bookings b));";
        $result = $conn->query($query);

        $hotels = [];
        while ($row = $result->fetch_assoc())
        {
            $hotels[] = $row;
        }

        $conn->close();

        return $hotels;
    }

    function getHotelsTypes()
    {
        $conn = connectToDatabase();

        $query = "SELECT DISTINCT hotels.type_id, type FROM hotels NATURAL JOIN hotelstypes";
        $result = $conn->query($query);

        $types = [];
        while ($row = $result->fetch_assoc())
        {
            $types[] = $row;
        }

        $conn->close();

        return $types;
    }

    function addHotel($name, $street, $building_nr, $apartment_nr, $zip_code, $city, $type_id)
    {
        $conn = connectToDatabase();

        $building_nr = (int)$building_nr;
        $type_id = (int)$type_id;
        if ($apartment_nr == 0)
        {
            $query = "INSERT INTO hotels VALUES (NULL, '$name', '$street', $building_nr, NULL, '$zip_code', '$city', $type_id)";
        }
        else
        {
            $query = "INSERT INTO hotels VALUES (NULL, '$name', '$street', $building_nr, $apartment_nr, '$zip_code', '$city', $type_id)";
        }
        $result = $conn->query($query);

        $conn->close();

        return $result;

    }

?>
