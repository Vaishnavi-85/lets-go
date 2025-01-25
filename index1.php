<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Bill Complaint System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body data-userid="<?php echo $_SESSION['Userid']; ?>">
    <div id="complaint-container">
        <!-- Section to show previous complaints -->
        <div id="previous-complaints">
            <h2><i class="fas fa-clipboard-list"></i> Previous Complaints</h2>
            <table id="complaint-table">
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Complaint Info</th>
                        <th>Status</th>
                        <th>Action Taken</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="complaint-list"></tbody>
            </table>
            <button id="new-complaint-btn"><i class="fas fa-plus-circle"></i> File New Complaint</button>
        </div>

        <!-- Form to submit a new complaint -->
        <div id="new-complaint-form" style="display: none;">
            <h2><i class="fas fa-file-alt"></i> File New Complaint</h2>
            <form id="complaint-form">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="complaint-info">Complaint Information:</label>
                    <textarea id="complaint-info" name="complaint-info" required></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="submit-btn"><i class="fas fa-paper-plane"></i> Submit</button>
                    <button type="button" id="cancel-btn" class="cancel-btn"><i class="fas fa-times"></i> Cancel</button>
                    <button type="button" id="back-btn" class="back-btn"><i class="fas fa-arrow-left"></i> Back</button>
                </div>
            </form>
            <div id="status-message" class="status-message"></div>
        </div>

        <!-- Section to display complaint details -->
        <div id="complaint-details" style="display: none;">
            <h2><i class="fas fa-info-circle"></i> Complaint Details</h2>
            <p><strong>Complaint ID:</strong> <span id="detail-id"></span></p>
            <p><strong>Complaint Info:</strong> <span id="detail-info"></span></p>
            <p><strong>Status:</strong> <span id="detail-status"></span></p>
            <p><strong>Action Taken:</strong> <span id="detail-action"></span></p>
            <button id="back-btn-details" class="back-btn"><i class="fas fa-arrow-left"></i> Back</button>
            <button id="cancel-pending-btn" style="display: none;" class="cancel-btn"><i class="fas fa-times"></i> Cancel Complaint</button>
        </div>
    </div>

    <script src="script1.js"></script>
</body>
</html>
