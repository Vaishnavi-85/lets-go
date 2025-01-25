// Sample complaint data
const currentUserId = document.body.getAttribute('data-userid');
let previousComplaints = [];
function fetchComplaints() {
    fetch('get_complaints.php')
        .then(response => response.json())
        .then(data => {
            previousComplaints = data; // Update complaints array with new data from the database
            displayPreviousComplaints(); // Refresh the display
        })
        .catch(error => console.error('Error fetching complaints:', error));
}
fetchComplaints();
// Function to display previous complaints in a table
function displayPreviousComplaints() {
    const complaintList = document.getElementById("complaint-list");
    complaintList.innerHTML = ""; // Clear the list first

    previousComplaints.forEach(complaint => {
        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${complaint.id}</td>
            <td>${complaint.info}</td>
            <td>${complaint.status}</td>
            <td>${complaint.action}</td>
            <td>
                <button class="view-details-btn" data-id="${complaint.id}">View Details</button>
                ${complaint.status === 'Pending' ? `<button class="cancel-btn" data-id="${complaint.id}">Cancel</button>` : ''}
            </td>
        `;
        complaintList.appendChild(row);
    });

    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            const complaintId = e.target.getAttribute('data-id');
            viewComplaintDetails(complaintId);
        });
    });

    // Add event listener for all "Cancel" buttons
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            const complaintId = e.target.getAttribute('data-id');
            cancelComplaint(complaintId);
        });
    });
}

// Function to fetch complaints from the database


// Function to handle new complaint submission
function handleNewComplaintSubmit(event) {
    event.preventDefault();
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const complaintInfo = document.getElementById("complaint-info").value;

    // Send the complaint data to the PHP script
    fetch('submit_complaint.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&complaint_info=${encodeURIComponent(complaintInfo)}&userid=${encodeURIComponent(sessionStorage.getItem('userid'))}` // Ensure userid is sent
    })
    .then(response => response.json()) // Expect a JSON response
    .then(data => {
        if (data.success) {
            const newComplaint = {
                id: previousComplaints.length + 1, // Assuming the PHP response includes the new complaint ID
                info: complaintInfo,
                status: "Pending",
                action: "Awaiting Review"
            };
            previousComplaints.push(newComplaint); // Add new complaint to the array

            // Reset the form inputs
            document.getElementById("complaint-form").reset();
            document.getElementById("status-message").textContent = "Complaint successfully filed!";
            document.getElementById("status-message").style.display = "block"; // Display status message

            // Refresh the complaint list after submission
            displayPreviousComplaints();
            fetchComplaints();
            // Show previous complaints and hide new complaint form
            document.getElementById("new-complaint-form").style.display = "none";
            document.getElementById("previous-complaints").style.display = "block";
        } else {
            console.error(data.message); // Handle error message
            document.getElementById("status-message").textContent = data.message;
            document.getElementById("status-message").style.display = "block"; // Show error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Function to view complaint details
function viewComplaintDetails(id) {
    
    const userid = sessionStorage.getItem('userid'); // Get the logged-in user's ID

    fetch(`get_complaint_details.php?id=${id}&userid=${userid}`) // Fetch details from the PHP script
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const complaint = data.complaint; // Get the complaint details from the response
                document.getElementById("detail-id").textContent = complaint.id;
                document.getElementById("detail-info").textContent = complaint.complaint_info; // Update based on your DB structure
                document.getElementById("detail-status").textContent = complaint.status;
                document.getElementById("detail-action").textContent = complaint.action;

                if (complaint.status === "Pending") {
                    document.getElementById("cancel-pending-btn").style.display = "inline-block";
                    document.getElementById("cancel-pending-btn").onclick = () => cancelComplaint(id);
                } else {
                    document.getElementById("cancel-pending-btn").style.display = "none";
                }

                document.getElementById("previous-complaints").style.display = "none";
                document.getElementById("new-complaint-form").style.display = "none";
                document.getElementById("complaint-details").style.display = "block";
            } else {
                console.error(data.message); // Handle error if the complaint is not found
            }
        })
        .catch(error => {
            console.error('Error fetching complaint details:', error); // Log any network errors
        });
}

// Function to cancel a pending complaint
function cancelComplaint(id) {
    const currentUserId = sessionStorage.getItem('userid');
    
    // First log the attempt
    console.log('Attempting to cancel complaint:', { id, currentUserId });
    
    const complaint = previousComplaints.find(c => String(c.id) === String(id));
    
    
    // Log the complaint being cancelled
    console.log('Found complaint:', complaint);

    if (complaint.status !== "Pending") {
        console.error('Invalid status:', complaint.status);
        alert("Only pending complaints can be cancelled.");
        return;
    }

    // Show loading state
    const cancelButton = document.querySelector(`button[data-id="${id}"]`);
    if (cancelButton) {
        cancelButton.disabled = true;
        cancelButton.textContent = 'Cancelling...';
    }

    fetch('cancel_complaint.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${encodeURIComponent(id)}&userid=${encodeURIComponent(currentUserId)}`,
        credentials: 'include' // Include cookies if you're using sessions
    })
    .then(response => {
        // Log the raw response
        console.log('Raw response:', response);
        
        // Check if response is OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Log the parsed data
        console.log('Response data:', data);
        
        if (data.success) {
            // Update the complaint's status locally
            complaint.status = "Cancelled";
            complaint.action = "Complaint Cancelled";
            
            // Refresh the displays
            displayPreviousComplaints();
            
            // Show success message
            const messageDiv = document.createElement("div");
            messageDiv.textContent = "Complaint successfully cancelled.";
            messageDiv.style.color = "green";
            messageDiv.style.padding = "10px";
            document.getElementById("previous-complaints").insertBefore(messageDiv, document.getElementById("previous-complaints").firstChild);
            
            // Remove message after 3 seconds
            setTimeout(() => messageDiv.remove(), 3000);

            // Update details if they're being displayed
            const detailsSection = document.getElementById("complaint-details");
            if (detailsSection.style.display !== "none") {
                document.getElementById("detail-status").textContent = "Cancelled";
                document.getElementById("detail-action").textContent = "Complaint Cancelled";
                document.getElementById("cancel-pending-btn").style.display = "none";
            }
        } else {
            throw new Error(data.message || 'Cancellation failed');
        }
    })
    .catch(error => {
        console.error('Error in cancellation:', error);
        
        // Re-enable the cancel button
        if (cancelButton) {
            cancelButton.disabled = false;
            cancelButton.textContent = 'Cancel';
        }
        
        // Show error message
        const errorDiv = document.createElement("div");
        errorDiv.textContent = "Failed to cancel complaint. Please try again.";
        errorDiv.style.color = "red";
        errorDiv.style.padding = "10px";
        document.getElementById("previous-complaints").insertBefore(errorDiv, document.getElementById("previous-complaints").firstChild);
        
        // Remove error message after 5 seconds
        setTimeout(() => errorDiv.remove(), 5000);
    });
}


// Event listeners
document.getElementById("new-complaint-btn").addEventListener("click", () => {
    document.getElementById("previous-complaints").style.display = "none";
    document.getElementById("new-complaint-form").style.display = "block";
    
    // Clear any previous status message
    document.getElementById("status-message").textContent = "";
    document.getElementById("complaint-form").reset(); // Reset the form fields
});

document.getElementById("complaint-form").addEventListener("submit", handleNewComplaintSubmit);

document.getElementById("cancel-btn").addEventListener("click", () => {
    document.getElementById("status-message").textContent = "Complaint cancelled.";
    document.getElementById("new-complaint-form").style.display = "none";
    document.getElementById("previous-complaints").style.display = "block";
});

document.getElementById("back-btn").addEventListener("click", () => {
    document.getElementById("new-complaint-form").style.display = "none";
    document.getElementById("previous-complaints").style.display = "block";
    
    // Clear any previous status message and reset the form
    document.getElementById("status-message").textContent = "";
    document.getElementById("complaint-form").reset(); // Reset the form fields
});

document.getElementById("back-btn-details").addEventListener("click", () => {
    document.getElementById("complaint-details").style.display = "none";
    document.getElementById("previous-complaints").style.display = "block";
});

// Initial fetch of complaints on page load

