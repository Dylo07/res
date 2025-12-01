<script>
function toggleRoomInput(checkbox) {
   document.getElementById('roomSection').style.display = checkbox.checked ? 'flex' : 'none';
   if (!checkbox.checked) {
       // Clear all room checkboxes when hiding the section
       document.querySelectorAll('input[name="room_numbers[]"]').forEach(cb => {
           cb.checked = false;
       });
   }
}

function togglePoolInput(checkbox) {
   document.getElementById('poolSection').style.display = checkbox.checked ? 'flex' : 'none';
   if (!checkbox.checked) {
       // Clear pool counts when hiding the section
       document.querySelector('input[name="adult_pool_count"]').value = "0";
       document.querySelector('input[name="kids_pool_count"]').value = "0";
   }
}

function showAlert(message, type = 'success') {
   const alert = `
       <div class="alert alert-${type} alert-dismissible fade show">
           ${message}
           <button type="button" class="close" data-dismiss="alert">&times;</button>
       </div>`;
   const alertsContainer = document.querySelector('.card-body');
   alertsContainer.insertAdjacentHTML('afterbegin', alert);
}

function updateTeam(id, team) {
   fetch(`/vehicle-security/${id}/update-team`, {
       method: 'POST',
       headers: {
           'Content-Type': 'application/json',
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
       },
       body: JSON.stringify({ team: team })
   })
   .then(response => response.json())
   .then(data => {
       if(data.success) {
           const row = document.querySelector(`tr[data-vehicle-id="${id}"]`);
           row.className = team ? `team-${team.replace(' ', '')}` : '';
           row.classList.add('highlight');
           showAlert('Team updated successfully');
       }
   })
   .catch(error => {
       showAlert('Error updating team', 'danger');
       console.error('Error:', error);
   });
}

function addVehicle(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    // Handle room numbers
    const selectedRooms = [];
    form.querySelectorAll('input[name="room_numbers[]"]:checked').forEach(checkbox => {
        selectedRooms.push(checkbox.value);
    });
    formData.set('room_numbers', JSON.stringify(selectedRooms));

    // Handle note status
    const isNote = event.submitter && event.submitter.name === 'is_note';
    if (isNote) {
        formData.set('is_note', '1');
    }

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const newRow = createVehicleRow(data.vehicle);
            document.querySelector('#vehicleTableBody').insertAdjacentHTML('afterbegin', newRow);
            form.reset();
            document.getElementById('roomSection').style.display = 'none';
            document.getElementById('poolSection').style.display = 'none';
            showAlert(data.message);
        }
    })
    .catch(error => {
        showAlert('Error creating entry', 'danger');
        console.error('Error:', error);
    });
}

function createVehicleRow(vehicle) {
   const date = new Date(vehicle.created_at);
   const formattedDate = date.toLocaleString('en-US', {
       year: 'numeric',
       month: '2-digit',
       day: '2-digit',
       hour: '2-digit',
       minute: '2-digit',
       hour12: true
   });

   let actionsCell = '';
   if(!vehicle.is_note) {
       if(vehicle.checkout_time) {
           actionsCell = `
               <span class="checkout-status-badge">
                   <i class="fas fa-check-circle"></i> Checked Out
               </span>
           `;
       } else {
           actionsCell = `
               <div class="btn-group">
                   <button type="button" class="btn btn-sm btn-primary" onclick="editVehicle(${vehicle.id})">
                       <i class="fas fa-edit"></i> Edit
                   </button>
                   <form action="/vehicle-security/${vehicle.id}/checkout" method="POST" style="display:inline;" 
                         onsubmit="checkoutVehicle(event, ${vehicle.id})">
                       @csrf
                       <button type="submit" class="btn btn-lg main-checkout-btn mx-2">
                           <i class="fas fa-sign-out-alt"></i> CHECK OUT
                       </button>
                   </form>
                   ${!vehicle.is_temp_out ? 
                       `<button type="button" class="btn btn-sm btn-info" onclick="tempCheckout(${vehicle.id})">
                           <i class="fas fa-clock"></i> Temp Out
                       </button>` : 
                       `<button type="button" class="btn btn-sm btn-success" onclick="tempCheckin(${vehicle.id})">
                           <i class="fas fa-undo"></i> Temp In
                       </button>`
                   }
               </div>
           `;
       }
   }

   return `
       <tr data-vehicle-id="${vehicle.id}" class="${vehicle.team ? 'team-'.str_replace(' ', '', vehicle.team) : ''}">
           <td class="align-middle">
               <span class="checkin-badge">
                   ${formattedDate}
               </span>
           </td>
           <td class="align-middle vehicle-number">${vehicle.vehicle_number}</td>
           <td class="align-middle matter">${vehicle.matter}</td>
           <td class="align-middle description">${vehicle.description || ''}</td>
           <td class="align-middle room">
               ${vehicle.room_numbers ? JSON.parse(vehicle.room_numbers).map(room => 
                   `<span class="room-badge">${room}</span>`
               ).join('') : ''}
           </td>
           <td class="align-middle pool-cell">
               ${vehicle.adult_pool_count || vehicle.kids_pool_count ? 
                   `<span class="pool-badge">
                       ${vehicle.adult_pool_count}/${vehicle.kids_pool_count}
                   </span>` : ''
               }
           </td>
           <td class="align-middle checkout-cell">
               ${vehicle.checkout_time ? 
                   `<span class="checkout-badge">
                       ${new Date(vehicle.checkout_time).toLocaleString('en-US', {
                           year: 'numeric',
                           month: '2-digit',
                           day: '2-digit',
                           hour: '2-digit',
                           minute: '2-digit',
                           hour12: true
                       })}
                       ${vehicle.duration_hours ? 
                           `<br><small class="duration-time">Duration: ${vehicle.duration_hours} hours</small>` 
                           : ''
                       }
                   </span>` :
                   vehicle.temp_checkout_time ?
                       `<span class="temp-badge">
                           Temp Out: ${new Date(vehicle.temp_checkout_time).toLocaleString('en-US', {
                               year: 'numeric',
                               month: '2-digit',
                               day: '2-digit',
                               hour: '2-digit',
                               minute: '2-digit',
                               hour12: true
                           })}<br>
                           ${vehicle.temp_checkin_time ?
                               `<small class="temp-in-time">
                                   Temp In: ${new Date(vehicle.temp_checkin_time).toLocaleString('en-US', {
                                       year: 'numeric',
                                       month: '2-digit',
                                       day: '2-digit',
                                       hour: '2-digit',
                                       minute: '2-digit',
                                       hour12: true
                                   })}
                               </small>` : ''
                           }
                       </span>` :
                       `<span class="text-warning">-</span>`
               }
           </td>
           <td class="align-middle team-cell">
               ${!vehicle.checkout_time ?
                   `<select class="form-control form-control-sm" onchange="updateTeam(${vehicle.id}, this.value)">
                       <option value="">Select Team</option>
                       ${[1,2,3,4,5,6,7,8,9,10].map(i => 
                           `<option value="Team ${i}" ${vehicle.team === `Team ${i}` ? 'selected' : ''}>
                               Team ${i}
                           </option>`
                       ).join('')}
                   </select>` :
                   vehicle.team ? 
                       `<span class="team-badge team-badge-${vehicle.team.replace(' ', '')}">
                           ${vehicle.team}
                       </span>` : ''
               }
           </td>
           <td class="align-middle actions-cell">${actionsCell}</td>
       </tr>
   `;
}

function checkoutVehicle(event, id) {
    event.preventDefault();
    const form = event.target;
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const row = document.querySelector(`tr[data-vehicle-id="${id}"]`);
            
            // Update checkout time cell
            row.querySelector('.checkout-cell').innerHTML = `
                <span class="checkout-badge">
                    ${data.checkout_time}
                    <br>
                    <small class="duration-time">Duration: ${data.duration_hours} hours</small>
                </span>
            `;
            
            // Update action cell to show checked out status
            row.querySelector('.actions-cell').innerHTML = `
                <span class="checkout-status-badge">
                    <i class="fas fa-check-circle"></i> Checked Out
                </span>
            `;
            
            showAlert('Vehicle checked out successfully');
        }
    })
    .catch(error => {
        showAlert('Error checking out vehicle', 'danger');
        console.error('Error:', error);
    });
}

function editVehicle(id) {
   fetch(`/vehicle-security/${id}/edit`)
       .then(response => response.json())
       .then(data => {
           if (data.checkout_time) {
               showAlert('Cannot edit checked-out vehicle', 'warning');
               return;
           }
           const form = document.getElementById('editForm');
           form.action = `/vehicle-security/${id}`;
           form.elements.vehicle_number.value = data.vehicle_number;
           form.elements.matter.value = data.matter;
           form.elements.description.value = data.description || '';
           
           // Clear previous room selections
           form.querySelectorAll('input[name="room_numbers[]"]').forEach(checkbox => {
               checkbox.checked = false;
           });
           
           // Set selected rooms
           // Set selected rooms
if (data.room_numbers) {
    const selectedRooms = JSON.parse(data.room_numbers);
    selectedRooms.forEach(room => {
        const checkbox = form.querySelector(`input[value="${room}"]`);
        if (checkbox) checkbox.checked = true;
    });
}
           form.elements.adult_pool_count.value = data.adult_pool_count || 0;
           form.elements.kids_pool_count.value = data.kids_pool_count || 0;
           
           $('#editModal').modal('show');
       })
       .catch(error => {
           showAlert('Error loading vehicle data', 'danger');
           console.error('Error:', error);
       });
}

function tempCheckout(event, id) {
    event.preventDefault();
    const form = event.target;
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const row = document.querySelector(`tr[data-vehicle-id="${id}"]`);
            
            // Update checkout time display
            row.querySelector('.checkout-cell').innerHTML = `
                <span class="temp-badge">
                    Temp Out: ${new Date(data.vehicle.temp_checkout_time).toLocaleString()}
                </span>
            `;
            
            // Update action buttons
            updateActionButtons(row, data.vehicle);
            showAlert('Vehicle temporarily checked out');
        }
    })
    .catch(error => {
        showAlert('Error in temporary checkout', 'danger');
        console.error('Error:', error);
    });
}

function tempCheckin(event, id) {
    event.preventDefault();
    const form = event.target;
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const row = document.querySelector(`tr[data-vehicle-id="${id}"]`);
            
            // Update checkout time display to show both times
            row.querySelector('.checkout-cell').innerHTML = `
                <span class="temp-badge">
                    Temp Out: ${new Date(data.vehicle.temp_checkout_time).toLocaleString()}
                    <br>
                    <small class="temp-in-time">
                        Temp In: ${new Date(data.vehicle.temp_checkin_time).toLocaleString()}
                    </small>
                </span>
            `;
            
            // Update action buttons
            updateActionButtons(row, data.vehicle);
            showAlert('Vehicle checked back in');
        }
    })
    .catch(error => {
        showAlert('Error in temporary check-in', 'danger');
        console.error('Error:', error);
    });
}

function updateActionButtons(row, vehicle) {
    const actionsCell = row.querySelector('.actions-cell');
    if (vehicle.is_temp_out) {
        actionsCell.innerHTML = `
            <button type="button" class="btn btn-sm btn-primary" onclick="editVehicle(${vehicle.id})">
                <i class="fas fa-edit"></i> Edit
            </button>
            <form action="/vehicle-security/${vehicle.id}/checkout" method="POST" style="display:inline;" 
                  onsubmit="checkoutVehicle(event, ${vehicle.id})">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fas fa-sign-out-alt"></i> Check Out
                </button>
            </form>
            <form action="/vehicle-security/${vehicle.id}/temp-checkin" method="POST" style="display:inline;" 
                  onsubmit="tempCheckin(event, ${vehicle.id})">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-undo"></i> Temp In
                </button>
            </form>
        `;
    } else {
        actionsCell.innerHTML = `
            <button type="button" class="btn btn-sm btn-primary" onclick="editVehicle(${vehicle.id})">
                <i class="fas fa-edit"></i> Edit
            </button>
            <form action="/vehicle-security/${vehicle.id}/checkout" method="POST" style="display:inline;" 
                  onsubmit="checkoutVehicle(event, ${vehicle.id})">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fas fa-sign-out-alt"></i> Check Out
                </button>
            </form>
            <form action="/vehicle-security/${vehicle.id}/temp-checkout" method="POST" style="display:inline;" 
                  onsubmit="tempCheckout(event, ${vehicle.id})">
                @csrf
                <button type="submit" class="btn btn-sm btn-info">
                    <i class="fas fa-clock"></i> Temp Out
                </button>
            </form>
        `;
    }
}


function updateVehicle(event) {
   event.preventDefault();
   const form = event.target;
   const formData = new FormData(form);
   
   // Collect selected room numbers
   const selectedRooms = [];
   form.querySelectorAll('input[name="room_numbers[]"]:checked').forEach(checkbox => {
       selectedRooms.push(checkbox.value);
   });
   formData.set('room_numbers', JSON.stringify(selectedRooms));
   
   fetch(form.action, {
       method: 'POST',
       body: formData,
       headers: {
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
       }
   })
   .then(response => response.json())
   .then(data => {
       if(data.success) {
           $('#editModal').modal('hide');
           const row = document.querySelector(`tr[data-vehicle-id="${data.vehicle.id}"]`);
           updateRowData(row, data.vehicle);
           row.classList.add('highlight');
           showAlert('Vehicle updated successfully');
       }
   })
   .catch(error => {
       showAlert('Error updating vehicle', 'danger');
       console.error('Error:', error);
   });
}

function updateRowData(row, data) {
   row.querySelector('.vehicle-number').textContent = data.vehicle_number;
   row.querySelector('.matter').textContent = data.matter;
   row.querySelector('.description').textContent = data.description || '';
   
   // Update room badges
   const roomBadges = data.room_numbers ? 
       JSON.parse(data.room_numbers)
           .map(room => `<span class="room-badge">${room}</span>`)
           .join('') : '';
   row.querySelector('.room').innerHTML = roomBadges;
   
   if(data.adult_pool_count || data.kids_pool_count) {
       row.querySelector('.pool-cell').innerHTML = `
           <span class="pool-badge">
               ${data.adult_pool_count}/${data.kids_pool_count}
           </span>
       `;
   } else {
       row.querySelector('.pool-cell').innerHTML = '';
   }
}
</script>