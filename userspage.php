<?php
// Database Connection
$host = 'localhost';
$username = 'root';
$password = ''; 
$database = 'login';

// Establish Database Connection
$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare trip data from form
    $destination = $_POST['destination'] ?? '';
    $startDate = $_POST['travel_dates'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $totalBudget = $_POST['total_budget'] ?? 0;
    $travelersCount = $_POST['travelers'] ?? 0;
    $transportationType = $_POST['transportation'] ?? '';

    // Prepare SQL statement
    $sql = "INSERT INTO trips 
            (destination, start_date, end_date, total_budget, travelers_count, transportation_type) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Check for prepare errors
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        exit();
    }

    // Bind parameters
    $stmt->bind_param("sssdis", $destination, $startDate, $endDate, $totalBudget, $travelersCount, $transportationType);

    // Execute the statement
    $stmt->execute();

    // Close statement after execution
    $stmt->close(); 
}

// Handle trip cancellation
if (isset($_GET['cancel_trip'])) {
    $trip_id = $_GET['cancel_trip']; 

    $sql = "DELETE FROM trips WHERE id = ?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $trip_id); 
    $stmt->execute();

    // Close statement after execution
    $stmt->close(); 

    header("Location: userspage.php"); // Redirect to the same page after cancellation
    exit();
}

// Retrieve existing trips
$trips_query = "SELECT * FROM trips ORDER BY start_date";
$trips_result = $conn->query($trips_query);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Trip Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for styling */
        body {
            font-family: 'Poppins', sans-serif; 
        }
        .container {
            max-width: 960px;
            margin: 0 auto;
        }
        .header {
            background-color: #007bff; /* Blue header */
            color: #fff;
            text-align: center;
            padding: 2rem 0;
        }
        .header h1 {
            font-size: 2.5rem;
        }
        .tab-button {
            border: none;
            background-color: transparent;
            color: #333;
            font-weight: bold;
            padding: 1rem 2rem;
            cursor: pointer;
        }
        .tab-button.tab-active {
            color: #007bff; 
            border-bottom: 2px solid #007bff; 
        }
        .tab-content {
            padding: 2rem;
            border: 1px solid #ddd;
            border-top: none; 
            margin-top: -1px; 
        }
        .input-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
        }
        input[type="text"], 
        input[type="date"], 
        input[type="number"], 
        select, 
        textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 1rem 2rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .trip-card {
            background-color: #f8f9fa; 
            border-radius: 5px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .trip-card p {
            margin-bottom: 0.5rem;
        }
        .cancel-trip {
            color: #dc3545; 
            text-decoration: none;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container">
        <header class="header">
            <h1>Professional Trip Planner</h1>
        </header>

        <form method="POST" class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="flex border-b">
                <button type="button" class="flex-1 py-4 text-center tab-button tab-active" data-tab="details">Trip Details</button>
                <button type="button" class="flex-1 py-4 text-center tab-button" data-tab="budget">Budget</button>
                <button type="button" class="flex-1 py-4 text-center tab-button" data-tab="activities">Activities</button>
            </div>

            <div id="details" class="tab-content p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="destination">Destination</label>
                        <input type="text" name="destination" id="destination" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Enter destination" required>
                    </div>
                    <div>
                        <label for="travel_dates">Start Date</label>
                        <input type="date" name="travel_dates" id="travel_dates" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label for="travelers">Number of Travelers</label>
                        <input type="number" name="travelers" id="travelers" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Enter number" required>
                    </div>
                    <div>
                        <label for="transportation">Transportation</label>
                        <select name="transportation" id="transportation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option>Flight</option>
                            <option>Car</option>
                            <option>Train</option>
                            <option>Bus</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="budget" class="tab-content p-6 hidden">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="total_budget">Total Budget</label>
                        <input type="number" name="total_budget" id="total_budget" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Enter total budget" required>
                    </div>
                </div>
            </div>

            <div id="activities" class="tab-content p-6 hidden">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="specific_activities">Specific Activities</label>
                        <textarea name="specific_activities" id="specific_activities" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4" placeholder="List specific activities">Write here</textarea>                </div>
                <div class="p-6 bg-gray-50 text-right">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700" required>Save Trip Plan</button>
                </div>
            </div>

            <div id="summary" class="tab-content p-6 hidden">
                <div class="bg-gray-100 p-4 rounded-md">
                    <h3 class="text-xl font-semibold mb-4">Saved Trips</h3>
                    <?php if ($trips_result && $trips_result->num_rows > 0): ?>
                        <?php while($trip = $trips_result->fetch_assoc()): ?>
                            <div class="trip-card">
                                <p><strong>Destination:</strong> <?php echo htmlspecialchars($trip['destination']); ?></p>
                                <p><strong>Dates:</strong> <?php echo htmlspecialchars($trip['start_date']); ?> to <?php echo htmlspecialchars($trip['end_date']); ?></p>
                                <p><strong>Budget:</strong> $<?php echo number_format($trip['total_budget'], 2); ?></p>
                                <p><strong>Travelers:</strong> <?php echo htmlspecialchars($trip['travelers_count']); ?></p>
                                <p><strong>Transportation:</strong> <?php echo htmlspecialchars($trip['transportation_type']); ?></p>
                                <a href="userspage.php?cancel_trip=<?php echo $trip['id']; ?>" class="cancel-trip">Cancel Trip</a> 
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-600">No trips saved yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        </form>
    </div>
    <script>
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');

                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('tab-active');
                });

                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                button.classList.add('tab-active');
                document.getElementById(tabId).classList.remove('hidden');

                // Show button only on Activities tab
                const submitButton = document.querySelector('button[type="submit"]');
                submitButton.style.display = tabId === 'activities' ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close(); 
?>