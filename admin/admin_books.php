<?php
session_start();

// Check if the User_ID session variable is not set or empty
if (!isset($_SESSION["User_ID"]) || empty($_SESSION["User_ID"])) {
    // Redirect to index.php
    header("Location: ../index.php");
    exit(); // Ensure script execution stops after redirection
}

$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Define the HTML code for the toast element
echo '<div class="toastNotif hide" id="toastNotif">
 <div class="toast-content">
     <i class="bx bx-check check"></i>
     <div class="message">
         <span class="text text-1" id="toastTitle"></span>
         <!-- this message can be changed to "Success" and "Error"-->
         <span class="text text-2" id="toastMessage"></span>
         <!-- specify based on the if-else statements -->
     </div>
 </div>
 <i class="bx bx-x close" id="toastClose"></i>
 <div class="progress"></div>
</div>';

// Define JavaScript functions to handle the toast
echo '<script>
 function showToast(type, message) {
     var toast = document.getElementById("toastNotif");
     var progress = toast.querySelector(".progress");
     var text1 = document.getElementById("toastTitle");
     var text2 = document.getElementById("toastMessage");
     
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
         toast.classList.remove("hide");
         toast.classList.add("show");
         progress.classList.add("show");

         // Hide the toast and progress after 5 seconds
         setTimeout(() => {
             toast.classList.remove("show");
             progress.classList.remove("show");
             toast.classList.add("hide");
         }, 5000);
     } else {
         console.error("Toast elements not found");
     }
 }

 function closeToast() {
     var toast = document.getElementById("toastNotif");
     var progress = toast.querySelector(".progress");
     toast.classList.remove("show");
     progress.classList.remove("show");
     toast.classList.add("hide");
 }

 document.getElementById("toastClose").addEventListener("click", closeToast);

 function redirectToPage(url, delay) {
     setTimeout(() => {
         window.location.href = url;
     }, delay);
 }
</script>';





// Check if the request contains the accessionCode and action in the POST data
if (isset($_POST['accessionCode']) && isset($_POST['action'])) {
    // Get the Accession_Code and action from the POST data
    $accessionCode = $_POST['accessionCode'];
    $action = $_POST['action'];

    // Check which action was triggered
    if ($action === 'archive') {
        // Prepare and execute the SQL query to update the book status to 'Archived'
        $sql = "UPDATE tbl_books SET tb_status = 'Archived' WHERE Accession_Code = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            // Error handling for prepared statement creation
            // echo json_encode(["success" => false, "message" => "Failed to prepare statement: " . $conn->error]);
            exit();
        }

        $stmt->bind_param("s", $accessionCode); // Bind the parameter to the query
        $stmt->execute();

        if ($stmt->error) {
            // Error handling for query execution
            // echo json_encode(["success" => false, "message" => "Failed to execute query: " . $stmt->error]);
            exit();
        }

        // Check if the query was successful
        if ($stmt->affected_rows > 0) {
            // Book was successfully archived
            echo '<script>
            // Call showToast with "success" message type after successful archiving
            showToast("success", "Book archived successfully.");
            // Redirect to this page after 3 seconds
            redirectToPage("admin_books.php", 3000);
            </script>';
        } else {
            // No rows affected, possibly the book with the given Accession_Code was not found
            echo '<script>
            // Call showToast with "error" message type after failed archiving
            showToast("error", "Failed to archive book: No rows affected");
            </script>';
        }

        // Close the prepared statement
        $stmt->close();
    } else  if ($action === 'save_changes' && isset($_POST['quantity'])) {
        $quantity = $_POST['quantity'];


        // Validate the quantity (assuming it should be a positive integer)
        if (!ctype_digit($quantity) || intval($quantity) < 0) {
            // echo json_encode(["success" => false, "message" => "Invalid quantity"]);
            echo '<script>
        // Call showToast with "error" message type after failed archiving
        showToast("error", "Invalid quantity");
        </script>';
        }

        // Prepare and execute the SQL query to update the quantity
        $sql = "UPDATE tbl_books SET Quantity = ? WHERE Accession_Code = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            // Error handling for prepared statement creation
            // echo json_encode(["success" => false, "message" => "Failed to prepare statement: " . $conn->error]);
            echo '<script>
        // Call showToast with "error" message type after failed archiving
        showToast("error", "Failed to prepare statement");
        </script>';
        }

        $stmt->bind_param("is", $quantity, $accessionCode); // Bind the parameters to the query
        $stmt->execute();

        if ($stmt->error) {
            // Error handling for query execution
            echo '<script>
        // Call showToast with "error" message type after failed archiving
        showToast("error", "Failed to Update book Quantity: No rows affected");
        </script>';
        }

        // Check if the query was successful
        if ($stmt->affected_rows > 0) {
            // Quantity was successfully updated
            echo '<script>
        // Call showToast with "success" message type after successful archiving
        showToast("success", "Book archived successfully.");
        // Redirect to this page after 3 seconds
        redirectToPage("admin_books.php", 1500);
        </script>';
        } else {
            // No rows affected, possibly the book with the given Accession_Code was not found
            echo '<script>
        // Call showToast with "error" message type after failed archiving
        showToast("error", "Failed to archive book: No rows affected");
        </script>';
        }

        // Close the prepared statement
        $stmt->close();
    }
}





// Set the status and page variables
$status = isset($_GET['status']) ? $_GET['status'] : 'Available';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
// $recordsPerPage = 3;
// $offset = ($page - 1) * $recordsPerPage;

// Update book status based on quantity
$sql = "UPDATE tbl_books SET tb_status = 'Unavailable' WHERE Quantity = 0";
$conn->query($sql);

$sqlUpdate = "UPDATE tbl_books SET tb_status = 'Available' WHERE Quantity > 0 AND tb_status != 'Archived' AND tb_status = 'Unavailable'";
$conn->query($sqlUpdate);



// Get the status from the query string
$status = isset($_GET['status']) ? $_GET['status'] : 'Available';

// Your SQL query with the status parameter
$sql = "SELECT
            tbl_books.Accession_Code, 
            tbl_books.Book_Title, 
            tbl_books.Authors_ID, 
            tbl_books.Publisher_Name, 
            tbl_books.Section_Code, 
            tbl_books.shelf, 
            tbl_books.tb_edition, 
            tbl_books.Year_Published, 
            tbl_books.ISBN, 
            tbl_books.Bibliography, 
            tbl_books.Quantity, 
            tbl_books.tb_status, 
            tbl_books.Price, 
            tbl_section.Section_uid, 
            tbl_section.Section_Name, 
            tbl_section.Section_Code, 
            tbl_authors.Authors_Name
        FROM
            tbl_books
        INNER JOIN
            tbl_section ON tbl_books.Section_Code = tbl_section.Section_uid
        INNER JOIN
            tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID
        WHERE
            tbl_books.tb_status = '$status' ";

// Execute the query and process the results
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="./admin.css" rel="stylesheet">
    <link href="toast.css" rel="stylesheet">
    <link rel="icon" href="../images/lib-icon.png ">
    <style>
        .toastNotif {
            display: none;
            /* Add your styling here */
            /* Example: position: fixed; bottom: 20px; right: 20px; background-color: #333; color: #fff; padding: 10px; border-radius: 5px; */
        }
        .toastNotif.show {
            display: block;
        }
        #bookTable {
    margin-top: 25px;
}
    </style>
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"><!--sidenav container-->
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <h2>Villa<span>Read</span>Hub</h2>
            <img src="../images/lib-icon.png" style="width: 45px;" alt="lib-icon" />
        </a><!--header container-->
        <div class="user-header  d-flex flex-row flex-wrap align-content-center justify-content-evenly"><!--user container-->
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
                // Fetch the First_Name from $userData
                $firstName = $userData['First_Name'];
                $role = $userData['tb_role'];
            }
            ?>
            <?php if (!empty($userData['image_data'])) : ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_data']); ?>" alt="User Image" width="50" height="50" class="rounded-circle me-2">
            <?php else : ?>
                <img src="../images/default-user-image.png" alt="Default Image" width="50" height="50" class="rounded-circle me-2">
            <?php endif; ?>
            <strong><span><?php echo  $firstName . "<br/>" .  $role; ?></span></strong>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto"><!--navitem container-->
            <li class="nav-item "> <a href="./admin_dashboard.php" class="nav-link link-body-emphasis "> <i class='bx bxs-home'></i>Dashboard </a> </li>
            <li class="nav-item active"> <a href="./admin_books.php" class="nav-link link-body-emphasis"><i class='bx bxs-book'></i>Books</a> </li>
            <li class="nav-item"> <a href="./admin_transactions.php" class="nav-link link-body-emphasis"><i class='bx bxs-customize'></i>Transactions</a> </li>
            <li class="nav-item"> <a href="./admin_staff.php" class="nav-link link-body-emphasis"><i class='bx bxs-user'></i>Manage Staff</a> </li>
            <li class="nav-item"> <a href="./admin_log.php" class="nav-link link-body-emphasis"><i class='bx bxs-user-detail'></i>Log Record</a> </li>
            <li class="nav-item"> <a href="./admin_fines.php" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Fines</a> </li>
            <li class="nav-item"> <a href="./admin_generate_report.php" class="nav-link link-body-emphasis"><i class='bx bxs-cloud'></i>Generate Report</a> </li>
            <hr>
            <li class="nav-item"> <a href="./admin_settings.php" class="nav-link link-body-emphasis"><i class='bx bxs-cog'></i>Settings</a> </li>
            <li class="nav-item"> <a href="" data-bs-toggle="modal" data-bs-target="#logOut" class="nav-link link-body-emphasis"><i class='bx bxs-wallet'></i>Log Out</a> </li>
        </ul>
    </div>
    <div class="board container-fluid"><!--board container--> 
        <div class="header1"> 
            <div class="text"> 
                <div class="title">
                    <h2>Books</h2>
                </div>
                <div class="form-group"> 
                    <form id="statusFilterForm" method="GET" action="admin_books.php">
                        <select id="statusFilter" name="status" class="form-select mb-3">
                            <option value="Available" <?php echo $status == 'Available' ? 'selected' : ''; ?>>Available</option>
                            <option value="Archived" <?php echo $status == 'Archived' ? 'selected' : ''; ?>>Archived</option>
                            <option value="Request" <?php echo $status == 'Request' ? 'selected' : ''; ?>>Request</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="searchbar">
                <form action="">
                    <i class='bx bx-search' id="search-icon"></i>
                    <input type="search" id="searchInput"  placeholder="Search..." required> 
                </form>
            </div> 
        </div>
        <div class="books container-fluid">  
           <?php
                $recordsPerPage = 4;
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $sqlCount = "SELECT COUNT(*) AS totalRecords FROM tbl_books WHERE tb_status = '$status'";
                $resultCount = $conn->query($sqlCount);
                $totalRecords = $resultCount->fetch_assoc()['totalRecords'];
                $totalPages = ceil($totalRecords / $recordsPerPage);
                $offset = ($page - 1) * $recordsPerPage;
                $tableHTML = '';

                if ($status === 'Request') {
                    $sql = "SELECT tbl_requestbooks.* FROM tbl_requestbooks ORDER BY CASE WHEN 
                    tb_status = 'Pending' THEN 0 ELSE 1 END, Request_ID ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $tableHTML = '<table class="table table-hover table-sm">
                                    <thead class="bg-light sticky-top">
                                    <tr>
                                    <th>#</th>
                                    <th>Book Title</th>
                                    <th>Authors</th> 
                                    <th>Publisher</th> 
                                    <th>Edition</th> 
                                    <th>Year Published</th> 
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>';
                        while ($row = $result->fetch_assoc()) {
                             $tableHTML .= '<tr>
                                <td>'. $row["Request_ID"] .'</td>
                                <td>' . $row["Book_Title"] . '</td>
                                <td>' . $row["Authors_Name"] . '</td>
                                <td>' . $row["Publisher_Name"] . '</td>
                                <td>' . $row["tb_edition"] . '</td>
                                <td>' . $row["Year_Published"] . '</td>
                                <td>' . $row["Quantity"] . '</td>
                                <td>' . $row["price"] . '</td>
                                <td>' . $row["tb_status"] . '</td>
                                <td>';
                                    if ($row["tb_status"] === "Approved") {
                                        $tableHTML .= "<button type='button' class='btn btn-secondary' disabled>Process</button>";
                                    } elseif ($row["tb_status"] === "Cancelled") { 
                                        $tableHTML .= "<button type='button' class='btn btn-secondary' disabled>Process</button>";
                                    } else{
                                        $tableHTML .= "<a href='process_data_book.php?id=" . $row["Request_ID"] . "' class='btn btn-primary'>Process</a>";
                                    }
                            $tableHTML .= '<table class="table table-hover table-sm">
                                        <thead class="bg-light sticky-top">
                                        <tr>
                                        <th>#</th>
                                        <th>Book Title</th>
                                        <th>Authors</th> 
                                        <th>Publisher</th> 
                                        <th>Edition</th> 
                                        <th>Year Published</th> 
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                        <td>No Requests Found</td>
                                        </tr>
                                        </tbody>
                                        </table>'; 
                        } 
                        
                    } else {
                        $tableHTML = ' <table class="table table-striped table-sm">
                        <thead class="bg-light sticky-top">
                        <tr>
                        <th>#</th>
                        <th>Book Title</th>
                        <th>Authors</th> 
                        <th>Publisher</th> 
                        <th>Edition</th> 
                        <th>Year Published</th> 
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                        <td colspan="10">No Requests Found</td>
                        </tr>
                        </tbody>
                        </table>';  
                    }

    
                } else {
                    $sql = "SELECT
                                tbl_books.Accession_Code,   tbl_books.Book_Title, 
                                tbl_books.Authors_ID, tbl_books.Publisher_Name, 
                                tbl_books.Section_Code,  tbl_books.shelf, 
                                tbl_books.tb_edition,   tbl_books.Year_Published, 
                                tbl_books.ISBN,  tbl_books.Bibliography, 
                                tbl_books.Quantity,  tbl_books.tb_status, 
                                tbl_books.Price,  tbl_section.Section_uid, 
                                tbl_section.Section_Name,  tbl_section.Section_Code, 
                                tbl_authors.Authors_Name
                            FROM
                                tbl_books
                            INNER JOIN
                                tbl_section ON tbl_books.Section_Code = tbl_section.Section_uid
                            INNER JOIN
                                tbl_authors ON tbl_books.Authors_ID = tbl_authors.Authors_ID
                            WHERE
                                tbl_books.tb_status = '$status' ";
                            $result = $conn->query($sql);
                    echo '<table class="table table-hover table-sm">
                        <thead class="bg-light sticky-top">
                        <tr>
                        <th>Accession Code</th>
                        <th>Book Title</th>
                        <th>Authors</th>
                        <th>Publisher</th>
                        <th>Section</th> 
                        <th>Shelf</th>
                        <th>Edition</th> 
                        <th>Year Published</th> 
                        <th>Quantity</th> 
                        <th>Price</th> 
                        <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>';

                    while ($row = $result->fetch_assoc()) {
                        $tableHTML .= '<tr>
                            <td>' . $row['Accession_Code'] . '</td>
                            <td>' . $row['Book_Title'] . '</td>
                            <td>' . $row['Authors_Name'] . '</td>
                            <td>' . $row['Publisher_Name'] . '</td>
                            <td>' . $row['Section_Name'] . '</td>
                            <td>' . $row['shelf'] . '</td>
                            <td>' . $row['tb_edition'] . '</td>
                            <td>' . $row['Year_Published'] . '</td> 
                            <td>' . $row['Quantity'] . '</td>
                            <td>' . $row['Price'] . '</td> 
                            <td>';

                            if ($row['tb_status'] == 'Available') {
                                $tableHTML .= '<button type="button" class="btn btn-primary btn-sm archive-btn" data-bs-toggle="modal" data-bs-target="#archiveModal" data-accession-code="' . $row['Accession_Code'] . '"><i class="bx bxs-edit"><i></button>';
                            } elseif ($row['tb_status'] == 'Archived') {
                                $tableHTML .= '<button type="button" class="btn btn-primary btn-sm archive-btn" data-bs-toggle="modal" data-bs-target="#unarchiveModal" data-accession-code="' . $row['Accession_Code'] . ' "disabled><i class="bx bxs-archive-out"><i></button>';
                            }
                        $tableHTML .= '</td>
                            </tr>';
                    }
                }

                $tableHTML .= '</tbody>
                    </table>';

                echo $tableHTML; 
            ?>
        </div> 
        <div class="btn-con">
            <a href="./admin_bookCatalog.php" class="btn btn-secondary">Catalog</a>
            <a href="./admin_addBook.php" class="btn btn-success">Add New Book</a>
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

    <div id="archiveModal" class="modal fade" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveModalLabel">Update Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div> 
                <div class="modal-footer d-flex flex-row justify-content-center">
                    <form id="archiveForm" method="POST" action="">
                        <p><strong>Updated Quantity:</strong> <input type="text" id="qty" placeholder="Update Quantity" oninput="updateHiddenQuantity()"></p>
                            
                            <input type="hidden" id="hiddenQuantity" name="quantity">
                            <input type="hidden" id="archiveAccessionCode" name="accessionCode">
                            <input type="hidden" id="action" name="action" value="">

                            <br>
                            <button type="button" class="btn btn-primary" onclick="saveChanges()">Save Changes</button>
                            <button type="button" class="btn btn-primary" onclick="setActionAndSubmit('archive')">Archive Book</button>
                    </form> 
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript code for search functionality
        document.getElementById("searchInput").addEventListener("input", function() {
            let searchValue = this.value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                let cells = row.querySelectorAll("td");
                let found = false;
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        found = true;
                    }
                });
                if (found) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });  

        function setAction(action) {
            document.getElementById('action').value = action;
            console.log('Action set to:', action);
        }


        // Function to update hidden input with quantity value
        function updateHiddenQuantity() {
            const quantityInput = document.getElementById('qty');
            const hiddenQuantityInput = document.getElementById('hiddenQuantity');
            hiddenQuantityInput.value = quantityInput.value;
            console.log('Hidden quantity updated to:', hiddenQuantityInput.value);
        }

        // Function to handle Save Changes button click
        function saveChanges() {
            // Set the action to save_changes
            setAction('save_changes');
            // Update hidden input with quantity value
            updateHiddenQuantity();
            // Submit the form
            document.getElementById('archiveForm').submit();
        }

        // Function to handle Archive button click and submit the form
        function setActionAndSubmit(action) {
            setAction(action);
            document.getElementById('archiveForm').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');

            // Add event listener for input on the quantity field
            quantityInput.addEventListener('input', function() {
                // Remove non-numeric characters from input
                this.value = this.value.replace(/\D/g, '');
                console.log('Quantity input value:', this.value);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const archiveButtons = document.querySelectorAll('.archive-btn');

            archiveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const accessionCode = this.getAttribute('data-accession-code');
                    document.getElementById('archiveAccessionCode').value = accessionCode;
                    fetchBookDetails(accessionCode);
                });
            });

            // Function to fetch book details and populate modal
            function fetchBookDetails(accessionCode) {
                fetch('queries/fetch_book_request.php?accessionCode=' + accessionCode)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Populate modal fields with retrieved data
                            document.getElementById('bookTitle').value = data.data.Book_Title;
                            document.getElementById('authors').value = data.data.Authors_Name;
                            document.getElementById('publisher').value = data.data.Publisher_Name;
                            document.getElementById('section').value = data.data.Section_Name;
                            document.getElementById('shelf').value = data.data.shelf;
                            document.getElementById('edition').value = data.data.tb_edition;
                            document.getElementById('yearPublished').value = data.data.Year_Published;
                            document.getElementById('isbn').value = data.data.ISBN;
                            document.getElementById('bibliography').value = data.data.Bibliography;
                            document.getElementById('quantity').value = data.data.Quantity;
                            document.getElementById('price').value = data.data.Price;
                            document.getElementById('status').value = data.data.tb_status;

                            // Show the modal
                            const archiveModal = new bootstrap.Modal(document.getElementById('archiveModal'));
                            archiveModal.show();
                        } else {
                            // Display error message if book not found
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error fetching book details:', error));
            }
        });

        // Add event listener to the select element for both functionalities
        document.getElementById('statusFilter').addEventListener('change', function() {
            var status = this.value; // Get the selected value
            // Show loading spinner

            // Update form action with the selected status
            document.getElementById('statusFilterForm').action = 'admin_books.php?status=' + encodeURIComponent(status);
            // Submit the form
            document.getElementById('statusFilterForm').submit();

            // Call function to update books
            updateBooks(status);
        });

        // Function to update books using AJAX
        function updateBooks(status) {
            // Send AJAX request to update books
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'admin_books.php?status=' + encodeURIComponent(status), true);
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    // Success
                    // Update the books display with the response from the server
                    var tableResponsive = document.querySelector('.table-responsive');
                    if (tableResponsive) {
                        tableResponsive.innerHTML = xhr.responseText;
                    } else {
                        console.error('.table-responsive element not found');
                    }
                } else {
                    // Error
                    console.error('Request failed');
                }
                // Hide loading spinner after request completes

            };
            xhr.onerror = function() {
                // Connection error
                console.error('Connection error');
                // Hide loading spinner on error

            };
            xhr.send();
        }
    </script>
 
</body> 
</html>