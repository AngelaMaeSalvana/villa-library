<?php

session_start();

// Check if the form was submitted with data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have a database connection established already
    // You need to modify the following code based on your database schema and connection method
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $affiliation = $_POST['affiliation'];

    // Store form data in session variables
    $_SESSION['first_name'] = $first_name;
    $_SESSION['middle_name'] = $middle_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['contact_number'] = $contact_number;
    $_SESSION['email'] = $email;
    $_SESSION['affiliation'] = $affiliation;

    // Redirect to another page or perform other actions
    header("Location: generate_qr_code.php");
    exit();
}
?>
