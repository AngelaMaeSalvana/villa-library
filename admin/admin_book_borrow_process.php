<?php

session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

 // Define the HTML code for the toast element
 echo '<div class="toastNotif hide">
 <div class="toast-content">
     <i class="bx bx-check check"></i>
     <div class="message">
         <span class="text text-1"></span>
         <!-- this message can be changed to "Success" and "Error"-->
         <span class="text text-2"></span>
         <!-- specify based on the if-else statements -->
     </div>
 </div>
 <i class="bx bx-x close"></i>
 <div class="progress"></div>
</div>';

// Define JavaScript functions to handle the toast
echo '<script>
 function showToast(type, message) {
     var toast = document.querySelector(".toastNotif");
     var progress = document.querySelector(".progress");
     var text1 = toast.querySelector(".text-1");
     var text2 = toast.querySelector(".text-2");
     
     if (toast && progress && text1 && text2) {
         // Update the toast content based on the message type
         if (type === "success") {
             text1.textContent = "Success";
             toast.classList.remove("error");
         } else if (type === "error") {
             text1.textContent = "Error";
             toast.classList.add("error");
         } else {
             console.error("Invalid message type");
             return;
         }
         
         // Set the message content
         text2.textContent = message;
         
         // Show the toast and progress
         toast.classList.add("showing");
         progress.classList.add("showing");
         
         // Hide the toast and progress after 5 seconds
         setTimeout(() => {
             toast.classList.remove("showing");
             progress.classList.remove("showing");
              window.location.href = "staff_log.php";
         }, 5000);
     } else {
         console.error("Toast elements not found");
     }
 }

 function closeToast() {
     var toast = document.querySelector(".toastNotif");
     var progress = document.querySelector(".progress");
     toast.classList.remove("showing");
     progress.classList.remove("showing");
 }

  function redirectToPage(url, delay) {
     setTimeout(() => {
         window.location.href = url;
     }, delay);
 }


</script>';



// Check if the borrower_id session variable is set
if (isset($_SESSION['borrower_id'])) {

    // Retrieve the borrower_id from the session
    $borrower_id = $_SESSION['borrower_id'];
    $User_ID = $_SESSION["User_ID"];

    // Now you can use $borrower_id in your code as needed

} else {
    // Handle the case where the session variable is not set
    echo '<script>alert("Borrower ID unavailable"); window.location.href = "staff_borrow_dash.php";</script>';
}



$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Retrieve the bookDetails array from the URL
$bookDetails = isset($_GET['bookDetails']) ? json_decode($_GET['bookDetails']) : [];
echo "<script>console.log('Book Details:', " . json_encode($bookDetails) . ");</script>";

// Function to generate unique transaction code
function generateTransactionCode() {
    $prefix = 'TXN'; // Prefix for the transaction code
    $timestamp = microtime(true); // Current timestamp with microseconds
    $unique_id = uniqid(); // Generate a unique identifier
    $random = mt_rand(1000, 9999); // Generate a random number

    // Generate a unique transaction code using the prefix, current timestamp, unique identifier, and random number
    $transaction_code = $prefix . '_' . $timestamp . '_' . $unique_id . '_' . $random;

    return $transaction_code;
}

// Retrieve the bookDetails array from the URL
$bookDetails = isset($_GET['bookDetails']) ? json_decode($_GET['bookDetails']) : [];
echo "<script>console.log('Book Details:', " . json_encode($bookDetails) . ");</script>";

// Check if $bookDetails is not empty before proceeding
if (!empty($bookDetails)) {
    // Initialize an array to store book Accession Codes
    $bookAccessionCodes = [];

    // Loop through each book detail
    foreach ($bookDetails as $accessionCode) {
        // Add the book Accession Code to the array
        $bookAccessionCodes[] = "'" . $accessionCode . "'";
    }

    // Convert the array of book Accession Codes to a comma-separated string for the SQL query
    $bookAccessionCodesStr = implode(",", $bookAccessionCodes);
    // Save the book Accession Codes string into a session variable
    $_SESSION['bookAccessionCodesStr'] = $bookAccessionCodesStr;

    echo "<script>console.log('Book CODES:', " . json_encode($_SESSION['bookAccessionCodesStr']) . ");</script>";

    // Check if $bookAccessionCodesStr is not empty before executing the SQL query
    if (!empty($bookAccessionCodesStr)) {
        // Retrieve book details from the database
        $sql = "SELECT tbl_books.*, tbl_authors.Authors_Name 
                FROM tbl_books
                INNER JOIN tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID 
                WHERE tbl_books.Accession_Code IN ($bookAccessionCodesStr)";

        $result = $conn->query($sql);

        $Status = 'Pending';
        $currentDate = date('Y-m-d');

    } else {
        echo "No book Accession Codes available";
    }
} else {
    echo "No book details available";
}

if (isset($_POST['submit'])) {
    $dueDate = date('Y-m-d', strtotime('+3 days', strtotime($currentDate)));
    $uid = $_SESSION["User_ID"];
    $_SESSION["due"] = $dueDate;
    
    if ($_POST['due_date'] == 'custom') {
        $dueDate = null; // Set $dueDate to null if 'custom' option is selected
    } else {
        $dueDate = date('Y-m-d', strtotime($_POST['due_date']));
    }

    if (isset($_SESSION['bookAccessionCodesStr']) && !empty($_SESSION['bookAccessionCodesStr'])) {
        echo "<script>console.log('Page Submit');</script>";

        $bookAccessionCodes = explode(",", $_SESSION['bookAccessionCodesStr']);

        foreach ($bookAccessionCodes as $accessionCode) {
            echo "<script>console.log('Book Accession Code:', '" . $accessionCode . "');</script>"; // Debugging

            // Generate a unique transaction code for each book
            $transactionCode = generateTransactionCode();

            $sql_update_quantity = "UPDATE tbl_books SET Quantity = Quantity - 1 WHERE Accession_Code = $accessionCode";

            if ($conn->query($sql_update_quantity) === TRUE) {
                echo "<script>console.log('Quantity updated for Accession Code: $accessionCode');</script>";

                $sql_borrow = "INSERT INTO tbl_borrow (User_ID, Borrower_ID, Accession_Code, Date_Borrowed, Due_Date, tb_status, Transaction_Code) 
                               VALUES ('$User_ID', '$borrower_id', $accessionCode, '$currentDate', ";

                if ($dueDate === null) {
                    $sql_borrow .= "NULL";
                } else {
                    $sql_borrow .= "'$dueDate'";
                }

                $sql_borrow .= ", '$Status', '$transactionCode')";

                if ($conn->query($sql_borrow) === TRUE) {
                    echo "<script>console.log('Inserted into for Accession Code: $accessionCode with Transaction Code: $transactionCode');</script>";

                    $sql_borrowdetails = "INSERT INTO tbl_borrowdetails (Borrower_ID, Accession_Code, Quantity, tb_status, Transaction_Code) 
                                          VALUES ('$borrower_id', $accessionCode, '1', '$Status', '$transactionCode')";

                    if ($conn->query($sql_borrowdetails) === TRUE) {
                        echo "<script>console.log('Inserted into borrow details for Accession Code: $accessionCode');</script>";

                        $sql_returndetails = "INSERT INTO tbl_returningdetails (BorrowDetails_ID, tb_status, Transaction_Code) 
                                              VALUES ('$borrower_id', 'Borrowed', '$transactionCode')";

                        if ($conn->query($sql_returndetails) === TRUE) {
                            echo "<script>console.log('Returning Details done for Accession Code: $accessionCode');</script>";

                            $sql_return = "INSERT INTO tbl_returned (User_ID, Borrower_ID, Date_Returned, tb_status, Transaction_Code) 
                                           VALUES ('$uid', '$borrower_id', NULL, 'Pending', '$transactionCode')";

                            if ($conn->query($sql_return) === TRUE) {
                                echo "<script>console.log('Return Details done for Accession Code: $accessionCode');</script>";
                            } else {
                                echo "Error inserting into tbl_returned: " . $conn->error;
                            }
                        } else {
                            echo "Error inserting into tbl_returningdetails: " . $conn->error;
                        }
                    } else {
                        echo "Error inserting into tbl_borrowdetails: " . $conn->error;
                    }
                } else {
                    echo "Error inserting into tbl_borrow: " . $conn->error;
                }
            } else {
                echo "Error updating quantity: " . $conn->error;
            }
        }

        echo '<script>
                showToast("success", "Book Borrow Success.");
                redirectToPage("queries/print_borrow.php", 3000);
              </script>';
    } else {
        echo "No book details available";
    }
}



$conn->close();
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <title>Select Due Date</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
            <!-- Display user image -->
            <?php
            $conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
            $userID = $_SESSION["User_ID"];
            $sql = "SELECT User_ID, First_Name, Middle_Name, Last_Name, tb_role, Contact_Number, E_mail, tb_address, image_data 
                    FROM tbl_employee 
                    WHERE User_ID = $userID";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                echo "Error: " . mysqli_error($conn);
            } else {
                $userData = mysqli_fetch_assoc($result);
            }
            ?>
            <?php if (!empty($userData['image_data'])) : ?>
                <!-- Assuming the image_data is in JPEG format, change the MIME type if needed -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <!-- Change the path to your actual default image -->
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo $userData['First_Name'] . "<br/>" . $_SESSION["role"]; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item"> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis " > <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item active"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-report'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bx-log-out'></i>Log Out</a> </li>
        </ul>
    </div>


    <div class="board1 container-fluid"><!--board container-->
        <div class="header1">
            <div class="text">
                <div class="back-btn">
                    <a href="./admin_book_borrow_find.php"><i class='bx bx-arrow-back'></i></a>
                </div>
                <div class="title">
                    <h2>Select Due Date</h2>
                </div>
            </div>
        </div>
        <div class="books container-fluid">
            <div class="header1">
                <!-- Header content -->
                <h2 class="mb-4">Book Borrow Process</h2>
            </div>

            <form method="POST" action="">
                <?php
                    // Retrieve book details from the database
                    $sql = "SELECT tbl_books.*, tbl_authors.Authors_Name 
                            FROM tbl_books
                            INNER JOIN tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID 
                            WHERE tbl_books.Accession_Code IN ($bookAccessionCodesStr)";
                    $result = $conn->query($sql);

                    // Check if the result set is not empty
                    if ($result && $result->num_rows > 0) {
                        // Fetch each row from the result set
                        while ($row = $result->fetch_assoc()) {
                ?>
                            <div class="card mb-4" style="width: 500px">
                                <div class="card-body">
                                    <h5 class="card-title"><strong>Title:</strong> <?php echo $row['Book_Title']; ?></h5>
                                    <p class="card-text"><strong>Author:</strong> <?php echo $row['Authors_Name']; ?></p>
                                    <p class="card-text"><strong>Availability:</strong> <?php echo $row['Quantity']; ?></p>
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label"><strong>Quantity:</strong></label>
                                        <!-- Input field for quantity with max attribute set to available quantity -->
                                        <input type="number" id="quantity" name="quantity[]" min="1" max="<?php echo $row['Quantity']; ?>" value="1" readonly>
                                        <!-- Hidden input field to store the book ID or accession code for processing -->
                                        <input type="hidden" name="accession_code[]" value="<?php echo $row['Accession_Code']; ?>">
                                    </div>
                                    <p class="card-text"><strong>Date Today:</strong> <?php echo $currentDate; ?></p>
                                </div>
                            </div>
                <?php
                        }
                ?>
                <div class="mb-3" style="width: 200px;">
                    <label for="due_date" class="form-label">Select Due Date:</label>
                    <!-- Datepicker input field -->
                    <input type="text" name="due_date" id="due_date" class="form-control" required>
                </div>

                <?php
                } else {
                    // Book not found or error occurred
                    echo "<p class='alert alert-warning'>No books found with the provided Accession Codes</p>";
                }
                ?>

                <!-- Success and error messages -->
                <?php if (!empty($successMessage)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <!-- Submit and cancel buttons -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary me-2" id="submit" name="submit">Submit</button>
                    <a href="staff_return.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div> 
    </div>
    
    <!--Logout Modal -->
    <div class="modal fade" id="logOut" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Logging Out...</h1>
                </div>
                <div class="modal-body">
                    Do you want to log out?
                </div>
                <div class="modal-footer d-flex flex-row justify-content-center">
                    <a href="javascript:history.go(0)"><button type="button" class="btn" data-bs-dismiss="modal">Cancel</button></a>
                    <a href="../logout.php"><button type="button" class="btn">Log Out</button></a>
                </div>
            </div>
        </div>
    </div>

    <div class="toastNotif" class="hide">
        <div class="toast-content">
            <i class='bx bx-check check'></i>

            <div class="message">
                <span class="text text-1">Success</span><!-- this message can be changed to "Success" and "Error"-->
                <span class="text text-2"></span> <!-- specify based on the if-else statements -->
            </div>
        </div>
        <i class='bx bx-x close'></i>
        <div class="progress"></div>
    </div>

    <script>
        $(function() {
            console.log("Datepicker initialization script executed");
            $("#due_date").datepicker();
        });
    </script>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"> </script>
    <script>
        let date = new Date().toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            weekday: 'long',
        });
        // document.getElementById("currentDate").innerText = date; 

        setInterval(() => {
            let time = new Date().toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: 'true',
            })
            // document.getElementById("currentTime").innerText = time; 

        }, 1000)


        let navItems = document.querySelectorAll(".nav-item"); //adding .active class to navitems 
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                document.querySelector('.active')?.classList.remove('active');
                item.classList.add('active');


            })

        })
    </script>
</body>

</html>