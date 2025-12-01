<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-role" content="{{ Auth::user()->role }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <title>Booking Calendar</title>
   

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        #booking-form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
        }
        #booking-form h1 {
            color: #007bff;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }
        #booking-form label {
            color: #495057;
            font-weight: bold;
        }
        #booking-form input, 
        #booking-form textarea, 
        #booking-form select {
            border: 1px solid #ced4da;
            border-radius: 5px;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }
        #booking-form .checkbox-group label {
            margin-right: 15px;
            display: inline-block;
        }
        #booking-form button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            font-size: 16px;
            width: 100%;
        }
        #booking-form button:hover {
            background-color: #0056b3;
        }
        .fc-event {
            color: #ffffff !important;
        }
        .calendar-container {
            max-width: 1800px;
            margin: 30px auto;
        }

        /* Add these new styles */
    .payment-record {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
    }

    .payment-checkbox {
        margin-right: 10px;
    }

    .payment-details {
        margin-left: 25px;
    }

    .checkbox-group label {
    display: inline-block;
    margin: 5px;
    padding: 5px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
}

.checkbox-group label:hover {
    background-color: #f8f9fa;
}

#availableRoomsMessage {
    margin-top: 10px;
    font-weight: bold;
}

.checkbox-group input[type="checkbox"]:disabled + span {
    color: #999;
}
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center my-4">Booking Calendar</h1>
        
    <!-- Add the Home Button -->
    <a href="/" class="btn btn-primary mb-3" style="float: right;">Home</a>
        <form id="booking-form">
            <h1>Enter Booking Details</h1>
            <div class="mb-3">
                <label for="function_type" class="form-label">Function Type:</label>
                <select id="function_type" name="function_type" class="form-control" required>
                    <option value="" selected>Select Function Type</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Night In Group">Night In Group</option>
                    <option value="Day Out">Day Out</option>
                    <option value="Couple Package">Couple Package</option>
                    <option value="Room Only">Room Only</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="date" class="form-label">Start Date:</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time:</label>
                    <input type="time" id="end_time" name="end_time" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" class="form-control" required>
            </div>


            <div class="mb-3">
                <label for="time_slot" class="form-label">Advance Payment:</label>
                <input type="text" id="time_slot" name="advance_payment" class="form-control" required>
            </div>

            <!-- New fields -->
<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="bill_number" class="form-label">Bill Number:</label>
            <input type="text" id="bill_number" name="bill_number" class="form-control" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="advance_date" class="form-label">Advance Date:</label>
            <input type="date" id="advance_date" name="advance_date" class="form-control" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method:</label>
            <select id="payment_method" name="payment_method" class="form-control" required>
                <option value="">Select Payment Method</option>
                <option value="online">Online</option>
                <option value="cash">Cash</option>
            </select>
        </div>
    </div>
</div>
            




<div class="mb-3" id="roomNumbersSection" style="display: none;">
    <label for="room_number" class="form-label">Room Numbers:</label>
    <div class="checkbox-group" id="roomCheckboxes">
                    <label><input type="checkbox" name="room_number[]" value="Ahala"> Ahala</label>
                    <label><input type="checkbox" name="room_number[]" value="Sepalika"> Sepalika</label>
                    <label><input type="checkbox" name="room_number[]" value="Sudu Araliya"> Sudu Araliya</label>
                    <label><input type="checkbox" name="room_number[]" value="Orchid"> Orchid</label>
                    <label><input type="checkbox" name="room_number[]" value="Olu"> Olu</label>
                    <label><input type="checkbox" name="room_number[]" value="Nelum"> Nelum</label>
                    <label><input type="checkbox" name="room_number[]" value="Hansa"> Hansa</label>
                    <label><input type="checkbox" name="room_number[]" value="Mayura"> Mayura</label>
                    <label><input type="checkbox" name="room_number[]" value="Lihini"> Lihini</label>
                    <label><input type="checkbox" name="room_number[]" value="121"> 121</label>
                    <label><input type="checkbox" name="room_number[]" value="122"> 122</label>
                    <label><input type="checkbox" name="room_number[]" value="123"> 123</label>
                    <label><input type="checkbox" name="room_number[]" value="124"> 124</label>
                    <label><input type="checkbox" name="room_number[]" value="106"> 106</label>
                    <label><input type="checkbox" name="room_number[]" value="107"> 107</label>
                    <label><input type="checkbox" name="room_number[]" value="108"> 108</label>
                    <label><input type="checkbox" name="room_number[]" value="109"> 109</label>
                    <label><input type="checkbox" name="room_number[]" value="CH Room"> CH Room</label>
                    <label><input type="checkbox" name="room_number[]" value="130"> 130</label>
                    <label><input type="checkbox" name="room_number[]" value="131"> 131</label>
                    <label><input type="checkbox" name="room_number[]" value="132"> 132</label>
                    <label><input type="checkbox" name="room_number[]" value="133"> 133</label>
                    <label><input type="checkbox" name="room_number[]" value="134"> 134</label>
                    <label><input type="checkbox" name="room_number[]" value="101"> 101</label>
                    <label><input type="checkbox" name="room_number[]" value="102"> 102</label>
                    <label><input type="checkbox" name="room_number[]" value="103"> 103</label>
                    <label><input type="checkbox" name="room_number[]" value="104"> 104</label>
                    <label><input type="checkbox" name="room_number[]" value="105"> 105</label>
                    
                    
                </div>
                <div id="availableRoomsMessage" class="text-info mt-2"></div>
                </div>
            
            <div class="mb-3">
                <label for="guest_count" class="form-label">Guest Count (Adults and Kids):</label>
                <input type="text" id="guest_count" name="guest_count" class="form-control" placeholder="E.g., Adults: 2, Kids: 3" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Package Price & Details:</label>
                <textarea id="name" name="name" class="form-control" rows="4" style="resize: none;" required></textarea>
            </div>
            <button type="submit" class="btn">Book</button>
        </form>
    </div>

    <div class="calendar-container">
        <div id="calendar"></div>
    </div>

    <!-- Modal for Available Rooms -->
    <div id="availableRoomsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Available Rooms</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Date:</strong> <span id="selectedDate"></span></p>
                <ul id="availableRoomsList">
                    <!-- Rooms will be listed here dynamically -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



    <div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Calendar Log Details</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Function Type</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>User</th>
                            <th>Advance Payment</th>
                            <th>Guest Count</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="logTableBody">
                        <!-- Data will be populated dynamically -->
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination will be populated dynamically -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>




   
   <!-- Modal for event details -->
<div id="eventModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="booking-info mb-4">
                    <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                    <div id="modalBody">
                        <!-- Basic booking details will be injected here -->
                    </div>
                </div>
                <div class="payment-history">
                    <h6 class="border-bottom pb-2 mb-3">Payment History</h6>
                    <div id="paymentHistoryBody">
                        <!-- Payment history will be injected here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="editEvent" class="btn btn-primary">Edit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="printConfirmation" class="btn btn-info">Print Confirmation</button>
            
            </div>
        </div>
    </div>
</div>


<!-- Edit function   -->

<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Package Price & Details:</label>
                        <textarea id="editName" name="name" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editFunctionType" class="form-label">Function Type:</label>
                        <select id="editFunctionType" name="function_type" class="form-control" required>
                            <option value="Wedding">Wedding</option>
                            <option value="Night In Group">Night In Group</option>
                            <option value="Day Out">Day Out</option>
                            <option value="Couple Package">Couple Package</option>
                            <option value="Room Only">Room Only</option>
                        </select>
                    </div>
                    
                   <!-- Add this after the function type select -->
<div class="mb-3">
    <div class="form-check">
        <input type="checkbox" id="updateDateTime" class="form-check-input">
        <label class="form-check-label" for="updateDateTime">Change Date & Time</label>
    </div>
</div>

<div id="dateTimeFields" style="display: none;">
    <div class="mb-3">
        <label for="editStart" class="form-label">Start Date & Time:</label>
        <input type="datetime-local" id="editStart" name="start" class="form-control">
    </div>
    <div class="mb-3">
        <label for="editEnd" class="form-label">End Date & Time:</label>
        <input type="datetime-local" id="editEnd" name="end" class="form-control">
    </div>
</div>
                    
                    
                    <div class="mb-3">
                        <label for="editContactNumber" class="form-label">Contact Number:</label>
                        <input type="text" id="editContactNumber" name="contact_number" class="form-control" required>
                    </div>
                    <!-- Add a toggle switch at the start of payment fields in editModal -->
<div class="mb-3">
    <div class="form-check">
        <input type="checkbox" id="updatePayment" class="form-check-input">
        <label class="form-check-label" for="updatePayment">Add New Payment</label>
    </div>
</div>

<div id="paymentFields" style="display: none;">
    <div class="mb-3">
        <label for="editAdvancePayment" class="form-label">Advance Payment:</label>
        <input type="text" id="editAdvancePayment" name="advance_payment" class="form-control">
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="editBillNumber" class="form-label">Bill Number:</label>
                <input type="text" id="editBillNumber" name="bill_number" class="form-control">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="editAdvanceDate" class="form-label">Advance Date:</label>
                <input type="date" id="editAdvanceDate" name="advance_date" class="form-control">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="editPaymentMethod" class="form-label">Payment Method:</label>
                <select id="editPaymentMethod" name="payment_method" class="form-control">
                    <option value="">Select Payment Method</option>
                    <option value="online">Online</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
        </div>
    </div>
</div>




                    <div class="mb-3">
    <label for="editRoomNumbers" class="form-label">Room Numbers:</label>
    <div class="checkbox-group" id="editRoomNumbers">

                    <label><input type="checkbox" name="room_number[]" value="Ahala"> Ahala</label>
                    <label><input type="checkbox" name="room_number[]" value="Sepalika"> Sepalika</label>
                    <label><input type="checkbox" name="room_number[]" value="Sudu Araliya"> Sudu Araliya</label>
                    <label><input type="checkbox" name="room_number[]" value="Orchid"> Orchid</label>
                    <label><input type="checkbox" name="room_number[]" value="Olu"> Olu</label>
                    <label><input type="checkbox" name="room_number[]" value="Nelum"> Nelum</label>
                    <label><input type="checkbox" name="room_number[]" value="Hansa"> Hansa</label>
                    <label><input type="checkbox" name="room_number[]" value="Mayura"> Mayura</label>
                    <label><input type="checkbox" name="room_number[]" value="Lihini"> Lihini</label>
                    <label><input type="checkbox" name="room_number[]" value="121"> 121</label>
                    <label><input type="checkbox" name="room_number[]" value="122"> 122</label>
                    <label><input type="checkbox" name="room_number[]" value="123"> 123</label>
                    <label><input type="checkbox" name="room_number[]" value="124"> 124</label>
                    <label><input type="checkbox" name="room_number[]" value="106"> 106</label>
                    <label><input type="checkbox" name="room_number[]" value="107"> 107</label>
                    <label><input type="checkbox" name="room_number[]" value="108"> 108</label>
                    <label><input type="checkbox" name="room_number[]" value="109"> 109</label>
                    <label><input type="checkbox" name="room_number[]" value="CH Room"> CH Room</label>
                    <label><input type="checkbox" name="room_number[]" value="130"> 130</label>
                    <label><input type="checkbox" name="room_number[]" value="131"> 131</label>
                    <label><input type="checkbox" name="room_number[]" value="132"> 132</label>
                    <label><input type="checkbox" name="room_number[]" value="133"> 133</label>
                    <label><input type="checkbox" name="room_number[]" value="134"> 134</label>
                    <label><input type="checkbox" name="room_number[]" value="101"> 101</label>
                    <label><input type="checkbox" name="room_number[]" value="102"> 102</label>
                    <label><input type="checkbox" name="room_number[]" value="103"> 103</label>
                    <label><input type="checkbox" name="room_number[]" value="104"> 104</label>
                    <label><input type="checkbox" name="room_number[]" value="105"> 105</label>
    </div>
</div>

                    <div class="mb-3">
                        <label for="editGuestCount" class="form-label">Guest Count:</label>
                        <input type="text" id="editGuestCount" name="guest_count" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
    
</div>


<script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    
    <script>
// 1. Constants
const functionTypeColors = {
                "Wedding": "#ff5733",
                "Night In Group": "#33ff57",
                "Day Out": "#3375ff",
                "Couple Package": "#ff33b8",
                "Room Only": "#33f8ff",
            };



// 2. React Components - Payment History Component
const PaymentHistory = ({ payments }) => {
    const storedStates = JSON.parse(localStorage.getItem('paymentVerifications') || '{}');
    const [verifications, setVerifications] = React.useState(storedStates);
    
    // Get user role from meta tag
    const userRole = document.querySelector('meta[name="user-role"]')?.content;
    const isAdmin = userRole === 'admin'; // Adjust based on your role naming
    
    const togglePayment = (index, paymentId) => {
        const currentUser = document.querySelector('meta[name="user-name"]')?.content || 'Admin';
        
        const newStates = {
            ...verifications,
            [paymentId]: verifications[paymentId] 
                ? null 
                : {
                    verified: true,
                    date: new Date().toISOString(),
                    verifiedBy: currentUser
                }
        };
        setVerifications(newStates);
        localStorage.setItem('paymentVerifications', JSON.stringify(newStates));
    };
    
    return React.createElement('div', { className: 'payment-history-container' },
        payments.map((payment, index) => {
            const paymentId = `${payment.billNumber}-${payment.date}`;
            const verificationStatus = verifications[paymentId];
            
            return React.createElement('div', { 
                key: index,
                className: 'payment-record'
            }, [
                React.createElement('div', { 
                    className: 'form-check d-flex align-items-center justify-content-between'
                }, [
                    React.createElement('div', {
                        className: 'd-flex align-items-center'
                    }, [
                        // Only show checkbox if user is admin and payment is not verified
                        (isAdmin && !verificationStatus) && React.createElement('input', {
                            type: 'checkbox',
                            className: 'form-check-input payment-checkbox',
                            onChange: () => togglePayment(index, paymentId),
                            id: `payment-${paymentId}`
                        }),
                        React.createElement('h6', { 
                            className: 'mb-0 ms-2'
                        }, `Payment #${index + 1}`)
                    ]),
                    verificationStatus && React.createElement('div', {
                        className: 'text-success ms-2 d-flex align-items-center'
                    }, [
                        React.createElement('span', { 
                            className: 'badge bg-success me-2'
                        }, 'Verified'),
                        React.createElement('small', {
                            className: 'text-muted'
                        }, `on ${new Date(verificationStatus.date).toLocaleDateString()} by ${verificationStatus.verifiedBy}`)
                    ])
                ]),
                React.createElement('div', { 
                    className: 'payment-details mt-2'
                }, [
                    React.createElement('p', { className: 'mb-1' },
                        `Amount: Rs. ${parseFloat(payment.amount).toFixed(2)}`
                    ),
                    React.createElement('p', { className: 'mb-1' },
                        `Bill Number: ${payment.billNumber}`
                    ),
                    React.createElement('p', { className: 'mb-1' },
                        `Date: ${new Date(payment.date).toLocaleDateString()}`
                    ),
                    React.createElement('p', { className: 'mb-1' },
                        `Method: ${payment.method}`
                    )
                ])
            ]);
        })
    );
};

 
// 3. Pagination Variables and Functions       
let currentPage = 1;
const rowsPerPage = 15;
let totalLogs = [];

async function loadLogDetails() {
    try {
        const response = await axios.get('/booking-logs');
        totalLogs = response.data;
        displayPage(currentPage);
        setupPagination();
    } catch (error) {
        console.error('Error loading log details:', error);
    }
}

function displayPage(page) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const pageData = totalLogs.slice(start, end);
    const tableBody = document.getElementById('logTableBody');
    tableBody.innerHTML = '';

    pageData.forEach(log => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${log.function_type}</td>
            <td>${new Date(log.created_at).toLocaleString()}</td>
            <td>${new Date(log.updated_at).toLocaleString()}</td>
            <td>${log.user_name || 'N/A'}</td>
            <td>Rs. ${parseFloat(log.advance_payment).toFixed(2)}</td>
            <td>${log.guest_count}</td>
            <td>${new Date(log.start).toLocaleString()}</td>
            <td>${log.end ? new Date(log.end).toLocaleString() : 'N/A'}</td>
            <td>${getStatusBadge(log.created_at, log.updated_at)}</td>
        `;
        tableBody.appendChild(row);
    });
}

function setupPagination() {
    const totalPages = Math.ceil(totalLogs.length / rowsPerPage);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    // Previous button
    pagination.innerHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }

    // Next button
    pagination.innerHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
        </li>
    `;
}

function changePage(page) {
    if (page < 1 || page > Math.ceil(totalLogs.length / rowsPerPage)) return;
    currentPage = page;
    displayPage(currentPage);
    setupPagination();
}

function getStatusBadge(created, updated) {
    if (created === updated) {
        return '<span class="badge bg-success">Created</span>';
    }
    return '<span class="badge bg-warning">Updated</span>';
}

// 4. Form Submission Handler - Load logs when page loads

     

        document.getElementById("booking-form").addEventListener("submit", async function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);

            const start = `${formData.get("date")}T${formData.get("start_time")}`;
            const endDate = formData.get("end_date") ? formData.get("end_date") : formData.get("date");
            const end = formData.get("end_time") ? `${endDate}T${formData.get("end_time")}` : null;
            const advancePayment = formData.get("advance_payment") || "0.00";


            try {
                await axios.post("/bookings", {
                    start: start,
                    end: end,
                    advance_payment: formData.get("advance_payment"),
                    bill_number: formData.get("bill_number"),       // New field
            advance_date: formData.get("advance_date"),     // New field
            payment_method: formData.get("payment_method"), // New field
                    
                    name: formData.get("name"),
                    function_type: formData.get("function_type"),
                    contact_number: formData.get("contact_number"),
                    room_numbers: formData.getAll("room_number[]"),
                    guest_count: formData.get("guest_count"),
                }, {
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                });

                alert("Booking successful!");
                location.reload();
            } catch (error) {
                console.error("Error making booking:", error.response || error.message);
                alert("Failed to make booking. Please try again.");
            }
        });




        // 5. Calendar Initialization and Event Handlers

       // 5. Calendar Initialization and Event Handlers

// Calendar Configuration Functions
function getCalendarConfig() {
    return {
        initialView: "dayGridMonth",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
        },
        events: "/bookings",
        dateClick: handleDateClick,
        eventContent: formatEventContent,
        eventDidMount: handleEventDidMount,
        eventClick: handleEventClick
    };
}

function handleEventDidMount(info) {
    const functionType = info.event.extendedProps.function_type;
    const color = functionTypeColors[functionType] || "#6c757d";
    info.el.style.backgroundColor = color;
    info.el.style.borderColor = color;
    info.el.style.color = "#fff";
}

function formatEventContent(info) {
    const functionType = info.event.extendedProps.function_type;
    const guestCount = info.event.extendedProps.guest_count;

    const wrapper = document.createElement("div");
    const title = document.createElement("div");
    title.textContent = functionType || "No Function Type";
    title.style.fontWeight = "bold";

    const guest = document.createElement("div");
    guest.textContent = `Guests: ${guestCount || "N/A"}`;
    wrapper.appendChild(title);
    wrapper.appendChild(guest);

    return { domNodes: [wrapper] };
}

async function handleDateClick(info) {
    const selectedDate = info.dateStr;
    document.getElementById("selectedDate").textContent = selectedDate;

    try {
        const response = await axios.get(`/available-rooms`, {
            params: { date: selectedDate },
        });

        const availableRooms = response.data;
        const roomsList = document.getElementById("availableRoomsList");
        roomsList.innerHTML = "";
        
        if (availableRooms.length > 0) {
            availableRooms.forEach((room) => {
                const listItem = document.createElement("li");
                listItem.textContent = room;
                roomsList.appendChild(listItem);
            });
        } else {
            roomsList.innerHTML = "<li>No rooms available</li>";
        }

        const modal = new bootstrap.Modal(document.getElementById("availableRoomsModal"));
        modal.show();
    } catch (error) {
        console.error("Error fetching available rooms:", error);
        alert("Failed to fetch available rooms.");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    loadLogDetails();
    const calendarEl = document.getElementById("calendar");
    const calendar = new FullCalendar.Calendar(calendarEl, getCalendarConfig());
    calendar.render();
});


// 6. Event Click Handler

function handleEventClick(info) {
    const event = info.event;
    const props = event.extendedProps;

        // Add this to your existing event click handler
document.getElementById("printConfirmation").onclick = function() {
    const bookingId = info.event.id;
    // Open in new window
    window.open(`/bookings/${bookingId}/print`, '_blank');
};



    // Set the title and body of the modal with event details
    // Update modal content
    document.getElementById('modalTitle').textContent = `Booking Details - ${event.title}`;
                document.getElementById('modalBody').innerHTML = `
                    <p><strong>Function Type:</strong> ${props.function_type || 'N/A'}</p>
                    <p><strong>Contact Number:</strong> ${props.contact_number || 'N/A'}</p>
                    <p><strong>Room Numbers:</strong> ${props.room_numbers || 'N/A'}</p>
                    <p><strong>Guest Count:</strong> ${props.guest_count || 'N/A'}</p>
                    <p><strong>Description:</strong> ${props.name || 'N/A'}</p>
                    <p><strong>Start Time:</strong> ${new Date(event.start).toLocaleString()}</p>
                    <p><strong>End Time:</strong> ${event.end ? new Date(event.end).toLocaleString() : 'N/A'}</p>
                `;

  // Handle payment history
  handlePaymentHistory(props);

// Show the modal
const modal = new bootstrap.Modal(document.getElementById("eventModal"));
modal.show();

// Set up edit button handler
setupEditHandler(info);
}



// 7. Payment History Handler


function handlePaymentHistory(props) {
    const payments = props.advancePayments || [];
    if (payments.length > 0 || props.advance_payment) {
        const paymentHistoryContainer = document.createElement('div');
        paymentHistoryContainer.id = 'payment-history-root';
        document.getElementById("paymentHistoryBody").innerHTML = '';
        document.getElementById("paymentHistoryBody").appendChild(paymentHistoryContainer);
        
        const paymentData = payments.length > 0 ? payments : [{
            amount: props.advance_payment,
            billNumber: props.bill_number || 'N/A',
            date: props.advance_date || new Date().toISOString(),
            method: props.payment_method || 'N/A'
        }];
        
        const root = ReactDOM.createRoot(paymentHistoryContainer);
        root.render(React.createElement(PaymentHistory, { payments: paymentData }));
    } else {
        document.getElementById("paymentHistoryBody").innerHTML = '<p>No payment history available</p>';
    }
}


// 8. Edit Handler Setup



 // Show the edit modal
 function setupEditHandler(info) {
    document.getElementById("editEvent").onclick = function() {
        populateEditForm(info);
        const editModal = new bootstrap.Modal(document.getElementById("editModal"));
        editModal.show();
    };


        // Add this to your existing JavaScript
document.getElementById('updatePayment').addEventListener('change', function() {
    const paymentFields = document.getElementById('paymentFields');
    paymentFields.style.display = this.checked ? 'block' : 'none';
    
    // Toggle required attribute on payment fields
    const fields = paymentFields.querySelectorAll('input, select');
    fields.forEach(field => {
        field.required = this.checked;
    });
});


// Handle date/time fields toggle
document.getElementById('updateDateTime').addEventListener('change', function() {
    const dateTimeFields = document.getElementById('dateTimeFields');
    dateTimeFields.style.display = this.checked ? 'block' : 'none';
    
    // Don't check room availability when dates change in edit mode
    const fields = dateTimeFields.querySelectorAll('input');
    fields.forEach(field => {
        field.required = this.checked;
    });
});

  // Set up form submission
  setupEditFormSubmission(info);
}



// 9. Edit Form Population

// Add the new function here, before populateEditForm
async function checkRoomAvailabilityForEdit(startDate, endDate, currentBookingId) {
        try {
            const response = await fetch(`/available-rooms?date=${startDate}&endDate=${endDate}&excludeBooking=${currentBookingId}`);
            if (!response.ok) throw new Error('Failed to fetch room availability');
            return await response.json();
        } catch (error) {
            console.error('Error checking room availability:', error);
            return [];
        }
    }




    async function populateEditForm(info) {
        // Populate the edit form
        document.getElementById("editFunctionType").value = info.event.extendedProps.function_type;
        document.getElementById("editName").value = info.event.extendedProps.name || ""; // Ensure name is pre-filled
        document.getElementById("editStart").value = new Date(info.event.start).toISOString().slice(0, 16);
        document.getElementById("editEnd").value = info.event.end ? new Date(info.event.end).toISOString().slice(0, 16) : '';
        document.getElementById("editContactNumber").value = info.event.extendedProps.contact_number;
        document.getElementById("editAdvancePayment").value = info.event.extendedProps.advance_payment;
        document.getElementById("editGuestCount").value = info.event.extendedProps.guest_count;
        // New fields population
    document.getElementById("editBillNumber").value = info.event.extendedProps.bill_number || "";
    document.getElementById("editAdvanceDate").value = info.event.extendedProps.advance_date || "";
    document.getElementById("editPaymentMethod").value = info.event.extendedProps.payment_method || "";


// Get current booking's dates
const startDate = new Date(info.event.start).toISOString();
    const endDate = info.event.end ? new Date(info.event.end).toISOString() : startDate;

    // Set date/time fields if they exist
    const editStart = document.getElementById("editStart");
    const editEnd = document.getElementById("editEnd");
    if (editStart) editStart.value = new Date(info.event.start).toISOString().slice(0, 16);
    if (editEnd) editEnd.value = info.event.end ? new Date(info.event.end).toISOString().slice(0, 16) : '';

    // Get available rooms excluding current booking
    const availableRooms = await checkRoomAvailabilityForEdit(startDate, endDate, info.event.id);

    // Enable all room checkboxes in edit mode
    const roomCheckboxes = document.querySelectorAll("#editRoomNumbers input[type='checkbox']");
    
    // Get the selected rooms
    const selectedRooms = info.event.extendedProps.room_numbers 
        ? JSON.parse(info.event.extendedProps.room_numbers) 
        : [];

    // Clear previous selections and styling
    roomCheckboxes.forEach(checkbox => {
        const label = checkbox.parentElement;
        checkbox.checked = false;
        checkbox.disabled = false;
        // Reset styles
        label.style.backgroundColor = '';
        label.style.borderColor = '';
        label.style.opacity = '1';
    });

    // Style and check rooms
    roomCheckboxes.forEach(checkbox => {
        const label = checkbox.parentElement;
        
        // If room is booked by others (not in available rooms and not in current booking)
        if (!availableRooms.includes(checkbox.value) && !selectedRooms.includes(checkbox.value)) {
            label.style.backgroundColor = '#ffebee'; // Light red background
            label.style.borderColor = '#ffcdd2'; // Red border
            // Add a tooltip
            label.title = 'This room is booked by another reservation';
        }

        // Check if this room was selected in current booking
        if (selectedRooms.includes(checkbox.value)) {
            checkbox.checked = true;
            label.style.backgroundColor = '#e3f2fd'; // Light blue background for selected rooms
        }
    });

    // Add CSS if not already present
    if (!document.getElementById('editRoomStyles')) {
        const style = document.createElement('style');
        style.id = 'editRoomStyles';
        style.textContent = `
            #editRoomNumbers label {
                transition: all 0.3s ease;
                position: relative;
                cursor: pointer;
                padding: 5px 10px;
                margin: 3px;
                border: 1px solid #ddd;
                border-radius: 4px;
                display: inline-block;
            }
            #editRoomNumbers label:hover {
                opacity: 0.8;
            }
        `;
        document.head.appendChild(style);
    }
}

   // 10. Edit Form Submission Setup    

   function setupEditFormSubmission(info) {
    document.getElementById("editForm").onsubmit = async function(e) {
        e.preventDefault();

        const addNewPayment = document.getElementById('updatePayment').checked;
        const updateDateTime = document.getElementById('updateDateTime').checked;

        // Create base data without dates
        const updatedData = {
            name: document.getElementById("editName").value,
            function_type: document.getElementById("editFunctionType").value,
            contact_number: document.getElementById("editContactNumber").value,
            guest_count: document.getElementById("editGuestCount").value,
            room_numbers: Array.from(document.querySelectorAll("#editRoomNumbers input:checked"))
                .map(checkbox => checkbox.value)
        };

        // Only add date/time fields if the checkbox is checked
        if (updateDateTime) {
            updatedData.start = document.getElementById("editStart").value;
            updatedData.end = document.getElementById("editEnd").value;
        }

        // Add payment data if checkbox is checked
        if (addNewPayment) {
            updatedData.advance_payment = document.getElementById("editAdvancePayment").value;
            updatedData.bill_number = document.getElementById("editBillNumber").value;
            updatedData.advance_date = document.getElementById("editAdvanceDate").value;
            updatedData.payment_method = document.getElementById("editPaymentMethod").value;
        }

        try {
            await axios.put(`/bookings/${info.event.id}`, updatedData, {
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
            });

            alert("Booking updated successfully!");
            location.reload();
        } catch (error) {
            console.error("Error updating booking:", error.response || error.message);
            alert("Failed to update booking.");
        }
    };
}


// Add date and time change handlers
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('date');
    const startTime = document.getElementById('start_time');
    const endDate = document.getElementById('end_date');
    const endTime = document.getElementById('end_time');
    const roomSection = document.getElementById('roomNumbersSection');
    const roomCheckboxes = document.querySelectorAll('input[name="room_number[]"]');
    const messageDiv = document.getElementById('availableRoomsMessage');

    async function checkAvailability() {
    if (!startDate.value || !startTime.value) {
        roomSection.style.display = 'none';
        return;
    }

    try {
        const start = `${startDate.value}T${startTime.value}`;
        const end = (endDate.value && endTime.value) ? 
                   `${endDate.value}T${endTime.value}` : 
                   start;

        console.log('Checking availability:', { start, end });

        const response = await fetch(`/available-rooms?date=${start}&endDate=${end}`);
        const availableRooms = await response.json();
        
        console.log('Available rooms response:', availableRooms);

        roomSection.style.display = 'block';

        roomCheckboxes.forEach(checkbox => {
            const isAvailable = availableRooms.includes(checkbox.value);
            console.log('Room check:', {
                room: checkbox.value,
                available: isAvailable,
                exactMatch: availableRooms.indexOf(checkbox.value)
            });
            
            checkbox.disabled = !isAvailable;
            checkbox.checked = false;
            const label = checkbox.parentElement;
            label.style.opacity = isAvailable ? '1' : '0.5';
        });

        messageDiv.textContent = `${availableRooms.length} rooms available for selected dates`;
        messageDiv.style.color = availableRooms.length > 0 ? 'green' : 'red';

    } catch (error) {
        console.error('Error checking room availability:', error);
        messageDiv.textContent = 'Error checking room availability';
        messageDiv.style.color = 'red';
    }
}

    // Add event listeners
    [startDate, startTime, endDate, endTime].forEach(element => {
        if (element) {
            element.addEventListener('change', checkAvailability);
        }
    });
});
</script>
</body>
</html>
