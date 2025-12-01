@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
   <div class="card shadow-sm">
       <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
           <h3 class="mb-0 font-weight-bold">Vehicle Security Management</h3>
           <input type="date" class="form-control bg-light w-auto" 
                  value="{{ $selectedDate ?? date('Y-m-d') }}" 
                  onchange="window.location.href='{{ route('vehicle-security.by-date', '') }}/' + this.value">
       </div>

       <div class="card-body">
           @if(session('success'))
               <div class="alert alert-success alert-dismissible fade show">
                   {{ session('success') }}
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
               </div>
           @endif

           <div class="alert alert-info alert-dismissible fade show">
               <i class="fas fa-info-circle"></i> 
               Showing all unchecked-out vehicles and vehicles from {{ $selectedDate ?? date('Y-m-d') }}
               <button type="button" class="close" data-dismiss="alert">&times;</button>
           </div>

           <form action="{{ route('vehicle-security.store') }}" method="POST" class="mb-4 bg-light p-4 rounded shadow-sm" onsubmit="addVehicle(event)">
               @csrf
               <div class="row">
                   <div class="col-md-3">
                       <div class="form-group">
                           <label class="font-weight-bold">Vehicle Number</label>
                           <input type="text" name="vehicle_number" class="form-control" required>
                       </div>
                   </div>
                   
                   <div class="col-md-3">
                       <div class="form-group">
                           <label class="font-weight-bold">Matter</label>
                           <select name="matter" class="form-control" required>
                               <option value="">Select Matter</option>
                               @foreach($matterOptions as $option)
                                   <option value="{{ $option }}">{{ $option }}</option>
                               @endforeach
                           </select>
                       </div>
                   </div>
                   
                   <div class="col-md-6">
                       <div class="form-group">
                           <label class="font-weight-bold">Description</label>
                           <textarea name="description" class="form-control" rows="2"></textarea>
                       </div>
                   </div>
               </div>

               <div class="row mt-3">
                   <div class="col-md-12">
                       <div class="custom-control custom-switch mr-4 d-inline">
                           <input type="checkbox" name="showRoom" value="1" id="showRoom" class="custom-control-input" onchange="toggleRoomInput(this)">
                           <label class="custom-control-label" for="showRoom">Add Room</label>
                       </div>
                       <div class="custom-control custom-switch d-inline">
                           <input type="checkbox" name="showPool" value="1" id="showPool" class="custom-control-input" onchange="togglePoolInput(this)">
                           <label class="custom-control-label" for="showPool">Add Pool Count</label>
                       </div>
                   </div>
               </div>

               <div id="roomSection" class="row mt-3" style="display: none;">
                   <div class="col-md-12">
                       <label class="font-weight-bold">Room Numbers</label>
                       <div class="room-checkboxes">
                           @foreach($roomOptions as $room)
                               <div class="custom-control custom-checkbox custom-control-inline">
                                   <input type="checkbox" 
                                          class="custom-control-input" 
                                          id="room_{{ $room }}" 
                                          name="room_numbers[]" 
                                          value="{{ $room }}">
                                   <label class="custom-control-label" for="room_{{ $room }}">{{ $room }}</label>
                               </div>
                           @endforeach
                       </div>
                   </div>
               </div>

               <div id="poolSection" class="row mt-3" style="display: none;">
                   <div class="col-md-3">
                       <div class="form-group">
                           <label class="font-weight-bold">Adult Pool Count</label>
                           <input type="number" name="adult_pool_count" class="form-control" min="0" value="0">
                       </div>
                   </div>
                   <div class="col-md-3">
                       <div class="form-group">
                           <label class="font-weight-bold">Kids Pool Count</label>
                           <input type="number" name="kids_pool_count" class="form-control" min="0" value="0">
                       </div>
                   </div>
               </div>

               <button type="submit" class="btn btn-primary mt-3 mr-2">
    <i class="fas fa-save mr-1"></i> Save Entry
</button>
<button type="submit" name="is_note" value="1" class="btn btn-secondary mt-3">
    <i class="fas fa-sticky-note mr-1"></i> Save as Note
</button>
<div class="col-md-12 mt-3">
    <div class="bg-white border rounded p-3 mb-3">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-door-open text-primary mr-2"></i>
            <span class="font-weight-bold text-primary">Today Available Rooms : {{ count($availableRooms) }}</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @forelse($availableRooms as $room)
                <span class="room-badge">{{ $room }}</span>
            @empty
                <span class="text-muted font-italic">No rooms available</span>
            @endforelse
        </div>
    </div>
</div>
           </form>
           <div class="table-responsive">
               <table class="table table-bordered table-hover shadow-sm">
                   <thead class="thead-dark">
                       <tr>
                           <th><i class="far fa-clock mr-1"></i> Check In</th>
                           <th><i class="fas fa-car mr-1"></i> Vehicle Number</th>
                           <th><i class="fas fa-tasks mr-1"></i> Matter</th>
                           <th><i class="fas fa-comment-alt mr-1"></i> Description</th>
                           <th><i class="fas fa-door-open mr-1"></i> Room</th>
                           <th><i class="fas fa-swimming-pool mr-1"></i> Pool Count (A/K)</th>
                           <th><i class="far fa-clock mr-1"></i> Check Out</th>
                           <th><i class="fas fa-users mr-1"></i> Team</th>
                           <th><i class="fas fa-cog mr-1"></i> Actions</th>
                       </tr>
                   </thead>
                   <tbody id="vehicleTableBody">
                       @foreach($vehicles as $vehicle)
                       <tr data-vehicle-id="{{ $vehicle->id }}" class="{{ $vehicle->team ? 'team-'.str_replace(' ', '', $vehicle->team) : '' }}">
                           <td class="align-middle">
                               <span class="checkin-badge">
                                   {{ $vehicle->created_at->format('Y-m-d H:i') }}
                               </span>
                           </td>
                           <td class="align-middle vehicle-number">{{ $vehicle->vehicle_number }}</td>
                           <td class="align-middle matter">{{ $vehicle->matter }}</td>
                           <td class="align-middle description">{{ $vehicle->description }}</td>
                           <td class="align-middle room">
                               @if($vehicle->room_numbers)
                                   @foreach(json_decode($vehicle->room_numbers) as $room)
                                       <span class="room-badge">{{ $room }}</span>
                                   @endforeach
                               @endif
                           </td>
                           <td class="align-middle pool-cell">
                               @if($vehicle->adult_pool_count || $vehicle->kids_pool_count)
                                   <span class="pool-badge">
                                       {{ $vehicle->adult_pool_count }}/{{ $vehicle->kids_pool_count }}
                                   </span>
                               @endif
                           </td>
                           <td class="align-middle checkout-cell">
    @if($vehicle->checkout_time)
        <span class="checkout-badge">
            {{ $vehicle->checkout_time->format('Y-m-d h:i A') }}<br>
            <span class="duration-badge">
                Duration: {{ 
                    number_format(
                        $vehicle->created_at->diffInMinutes($vehicle->checkout_time) / 60, 
                        1
                    ) 
                }} hours
            </span>
        </span>
    @elseif($vehicle->temp_checkout_time)
        <span class="temp-badge">
            Temp Out: {{ $vehicle->temp_checkout_time->format('Y-m-d h:i A') }}<br>
            @if($vehicle->temp_checkin_time)
                <small class="temp-in-time">
                    Temp In: {{ $vehicle->temp_checkin_time->format('Y-m-d h:i A') }}
                </small>
            @endif
        </span>
    @else
        <span class="text-warning">-</span>
    @endif
</td>
                           <td class="align-middle team-cell">
                               @if(!$vehicle->checkout_time)
                                   <select class="form-control form-control-sm" onchange="updateTeam({{ $vehicle->id }}, this.value)">
                                       <option value="">Select Team</option>
                                       @for($i = 1; $i <= 10; $i++)
                                           <option value="Team {{ $i }}" {{ $vehicle->team == "Team $i" ? 'selected' : '' }}>
                                               Team {{ $i }}
                                           </option>
                                       @endfor
                                   </select>
                               @else
                                   <span class="team-badge team-badge-{{ str_replace(' ', '', $vehicle->team) }}">
                                       {{ $vehicle->team }}
                                   </span>
                               @endif
                           </td>
                           <td class="align-middle actions-cell">
    @if(!$vehicle->is_note)
        @if(!$vehicle->checkout_time)
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary" onclick="editVehicle({{ $vehicle->id }})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                
                <form action="{{ route('vehicle-security.checkout', $vehicle->id) }}" 
                      method="POST" style="display:inline;" 
                      onsubmit="checkoutVehicle(event, {{ $vehicle->id }})">
                    @csrf
                    <button type="submit" class="btn btn-lg main-checkout-btn mx-2">
                        <i class="fas fa-sign-out-alt"></i> CHECK OUT
                    </button>
                </form>

                @if(!$vehicle->is_temp_out)
                    <button type="button" class="btn btn-sm btn-info" 
                            onclick="tempCheckout({{ $vehicle->id }})">
                        <i class="fas fa-clock"></i> Temp Out
                    </button>
                @else
                    <button type="button" class="btn btn-sm btn-success" 
                            onclick="tempCheckin({{ $vehicle->id }})">
                        <i class="fas fa-undo"></i> Temp In
                    </button>
                @endif
            </div>
        @else
            <span class="checkout-status-badge">
                <i class="fas fa-check-circle"></i> Checked Out
            </span>
        @endif
    @endif
</td>
                       </tr>
                       @endforeach
                   </tbody>
               </table>
           </div>
       </div>
   </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header bg-primary text-white">
               <h5 class="modal-title">
                   <i class="fas fa-edit mr-1"></i> Edit Vehicle Entry
               </h5>
               <button type="button" class="close text-white" data-dismiss="modal">
                   <span>&times;</span>
               </button>
           </div>
           <div class="modal-body">
               <form id="editForm" method="POST" onsubmit="updateVehicle(event)">
                   @csrf
                   @method('PUT')
                   <div class="row">
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>Vehicle Number</label>
                               <input type="text" name="vehicle_number" class="form-control" required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>Matter</label>
                               <select name="matter" class="form-control" required>
                                   @foreach($matterOptions as $option)
                                       <option value="{{ $option }}">{{ $option }}</option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                       <div class="col-md-12">
                           <div class="form-group">
                               <label>Description</label>
                               <textarea name="description" class="form-control" rows="2"></textarea>
                           </div>
                       </div>
                       <div class="col-md-12">
                           <div class="form-group">
                               <label>Room Numbers</label>
                               <div class="room-checkboxes">
                                   @foreach($roomOptions as $room)
                                       <div class="custom-control custom-checkbox custom-control-inline">
                                           <input type="checkbox" 
                                                  class="custom-control-input" 
                                                  id="edit_room_{{ $room }}" 
                                                  name="room_numbers[]" 
                                                  value="{{ $room }}">
                                           <label class="custom-control-label" for="edit_room_{{ $room }}">{{ $room }}</label>
                                       </div>
                                   @endforeach
                               </div>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Adult Pool Count</label>
                               <input type="number" name="adult_pool_count" class="form-control" min="0">
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Kids Pool Count</label>
                               <input type="number" name="kids_pool_count" class="form-control" min="0">
                           </div>
                       </div>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">
                   <i class="fas fa-times mr-1"></i> Close
               </button>
               <button type="submit" form="editForm" class="btn btn-primary">
                   <i class="fas fa-save mr-1"></i> Save changes
               </button>
           </div>
       </div>
   </div>
</div>

<style>
.card-header {
    background: linear-gradient(45deg, #1a237e, #283593);
}

.checkin-badge {
    background: linear-gradient(45deg, #6c757d, #495057);
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.9rem;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.checkout-badge {
    background: linear-gradient(45deg, #20c997, #28a745);
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.9rem;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pool-badge {
    background: linear-gradient(45deg, #0dcaf0, #0d6efd);
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.9rem;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.room-badge {
    background: linear-gradient(45deg, #17a2b8, #138496);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85rem;
    display: inline-block;
    margin: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.room-checkboxes {
    max-height: 150px;
    overflow-y: auto;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    background: #fff;
}

.custom-checkbox {
    margin: 5px 10px;
}

.team-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-weight: 500;
    font-size: 0.9rem;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Team colors with button-like appearance */
.team-Team1 { 
    background: linear-gradient(to bottom, #e6e6e6, #deb5f7) !important;
    border: 1px solid #b3b3b3;
}
.team-Team2 { 
    background: linear-gradient(to bottom, #b3ffb3, #80ff80) !important;
    border: 1px solid #4dff4d;
}
.team-Team3 { 
    background: linear-gradient(to bottom, #b3d9ff, #80bfff) !important;
    border: 1px solid #4da6ff;
}
.team-Team4 { 
    background: linear-gradient(to bottom, #ffd9b3, #ffbf80) !important;
    border: 1px solid #ffa64d;
}
.team-Team5 { 
    background: linear-gradient(to bottom, #ffb3ff, #ff80ff) !important;
    border: 1px solid #ff4dff;
}
.team-Team6 { 
    background: linear-gradient(to bottom, #b3fff9, #80fff5) !important;
    border: 1px solid #4dfff2;
}
.team-Team7 { 
    background: linear-gradient(to bottom, #ffcccc, #ff9999) !important;
    border: 1px solid #ff6666;
}
.team-Team8 { 
    background: linear-gradient(to bottom, #d9ffcc, #b3ff99) !important;
    border: 1px solid #8cff66;
}
.team-Team9 { 
    background: linear-gradient(to bottom, #ccd9ff, #99b3ff) !important;
    border: 1px solid #668cff;
}
.team-Team10 { 
    background: linear-gradient(to bottom, #fff2cc, #ffe699) !important;
    border: 1px solid #ffd966;
}

.highlight {
    animation: highlight 1s ease-out;
}

@keyframes highlight {
    0% { background-color: rgba(255, 255, 0, 0.5); }
    100% { background-color: transparent; }
}

.checkout-status-badge {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Action Buttons */
.btn-primary {
    background: linear-gradient(45deg, #0d6efd, #0b5ed7);
    border: none;
    color: white !important;
}

.btn-warning, .btn-check-out {
    background: linear-gradient(45deg, #dc3545, #c82333) !important;
    border: none !important;
    color: white !important;
}

.btn-info {
    background: linear-gradient(45deg, #17a2b8, #0dcaf0);
    border: none;
    color: white !important;
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    color: white !important;
}

.btn {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}

/* Update button styles */
.btn-check-out {
    background: linear-gradient(45deg, #dc3545, #c82333) !important;
    border: none !important;
    color: white !important;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 10px 20px !important;
}

/* Target specifically the checkout button in the table */
.actions-cell .btn-check-out {
    font-size: 1rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(220, 53, 69, 0.3);
}

.actions-cell .btn-check-out:hover {
    background: linear-gradient(45deg, #c82333, #bd2130) !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 8px rgba(220, 53, 69, 0.4);
}

.duration-badge {
    display: inline-block;
    margin-top: 4px;
    padding: 2px 8px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    font-size: 0.85rem;
    color: #fff;
}
</style>

@endsection

@push('scripts')
    @include('vehicle-security.scripts')
@endpush