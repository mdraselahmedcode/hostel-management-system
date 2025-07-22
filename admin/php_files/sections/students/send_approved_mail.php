<?php 
require_once __DIR__ . '/../../../../config/config.php'; 
require_once BASE_PATH . '/config/db.php'; 
require_once BASE_PATH . '/student/php_files/send_email.php'; 
require_once BASE_PATH . '/config/auth.php'; 

require_admin(); 

function sendStudentApprovalNotification($studentId, $hostelId, $floorId, $roomId) {
    global $conn;

    // 1. Get student details
    $studentQuery = "SELECT first_name, email FROM students WHERE id = ?";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $stmt->bind_result($firstName, $email);
    $stmt->fetch();
    $stmt->close();

    // 2. Get hostel, floor, room details including address ID and contact
    $hostelQuery = "
        SELECT h.hostel_name, h.contact_number, h.address_id, 
               f.floor_name, r.room_number
        FROM hostels h
        JOIN floors f ON f.id = ?
        JOIN rooms r ON r.id = ?
        WHERE h.id = ?
    ";
    $stmt = $conn->prepare($hostelQuery);
    $stmt->bind_param("iii", $floorId, $roomId, $hostelId);
    $stmt->execute();
    $stmt->bind_result($hostelName, $contactNumber, $addressId, $floorName, $roomNumber);
    $stmt->fetch();
    $stmt->close();

    // 3. Get hostel address details
    $addressQuery = "
        SELECT country_id, state, division, district, sub_district, village,
               postalcode, street, house_no, detail
        FROM addresses WHERE id = ?
    ";
    $stmt = $conn->prepare($addressQuery);
    $stmt->bind_param("i", $addressId);
    $stmt->execute();
    $stmt->bind_result($countryId, $state, $division, $district, $subDistrict, $village,
                       $postalcode, $street, $houseNo, $detail);
    $stmt->fetch();
    $stmt->close();

    $addressParts = array_filter([
        $street,
        $houseNo,
        $detail,
        $village,
        $subDistrict,
        $district,
        $division,
        $state,
        "Postal Code: $postalcode"
    ]);

    $fullAddress = implode(', ', $addressParts);

    // 4. Get current roommates (excluding this student)
    $roommates = [];
    $roommateQuery = "
        SELECT first_name, email, contact_number FROM students 
        WHERE room_id = ? AND id != ?
    ";
    $stmt = $conn->prepare($roommateQuery);
    $stmt->bind_param("ii", $roomId, $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $roommates[] = $row;
    }
    $stmt->close();

    // 5. Construct email body
    $body = "
        Hi {$firstName},<br><br>
        Your hostel application has been <strong>approved</strong>.<br><br>
        <strong>Hostel Name:</strong> {$hostelName}<br>
        <strong>Floor:</strong> {$floorName}<br>
        <strong>Room:</strong> {$roomNumber}<br>
        <strong>Contact Number:</strong> {$contactNumber}<br>
        <strong>Hostel Address:</strong> {$fullAddress}<br><br>
    ";

    if (!empty($roommates)) {
        $body .= "Your current roommates are:<ul>";
        foreach ($roommates as $mate) {
            $matePhone = $mate['contact_number'] ?? 'N/A';
            $body .= "<li>{$mate['first_name']} ({$mate['email']}, Phone: {$matePhone})</li>";
        }
        $body .= "</ul><br>";
    }


    $body .= "
        <br><br>Your hostel application has been approved by the admin.<br>
        Please visit the hostel to complete your check-in process.<br><br>
        Regards,<br>Hostel Admin Team
    ";

    // 6. Send the email
    return sendEmail($email, "Hostel Application Approved", $body);
}
