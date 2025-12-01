<style>
/* Base Styles */
.card-header { background: linear-gradient(45deg, #1a237e, #283593); }

/* Badge Base Style */
.badge-base {
   color: white;
   border-radius: 15px;
   font-size: 0.9rem;
   display: inline-block;
   box-shadow: 0 2px 4px rgba(0,0,0,0.1);
   padding: 6px 12px;
}

/* Badges */
.checkin-badge {
   background: linear-gradient(45deg, #6c757d, #495057);
}

.checkout-badge {
   background: linear-gradient(45deg, #20c997, #28a745);
   min-width: 200px;
   padding: 8px 12px;
}

.pool-badge {
   background: linear-gradient(45deg, #0dcaf0, #0d6efd);
}

.room-badge {
   background: linear-gradient(45deg, #17a2b8, #138496);
   padding: 4px 8px;
   border-radius: 12px;
   font-size: 0.85rem;
   margin: 2px;
}

.temp-badge {
   background: linear-gradient(45deg, #17a2b8, #0dcaf0);
   min-width: 200px;
}

.checkout-status-badge {
   background: linear-gradient(45deg, #28a745, #20c997);
   border-radius: 20px;
   padding: 8px 12px;
}

/* Button Base Style */
.btn {
   border: none;
   color: white !important;
   transition: all 0.3s ease;
   box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn:hover {
   transform: translateY(-1px);
   box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}

/* Button Types */
.btn-primary {
   background: linear-gradient(45deg, #0d6efd, #0b5ed7) !important;
}

.btn-warning, .btn-check-out {
   background: linear-gradient(45deg, #dc3545, #c82333) !important;
   font-weight: 600;
   text-transform: uppercase;
   letter-spacing: 1px;
   padding: 10px 20px !important;
}

.btn-info {
   background: linear-gradient(45deg, #17a2b8, #0dcaf0) !important;
}

.btn-success {
   background: linear-gradient(45deg, #28a745, #20c997) !important;
}

/* Action Cell Buttons */
.actions-cell .btn-check-out {
   font-size: 1rem;
   border-radius: 8px;
   box-shadow: 0 4px 6px rgba(220, 53, 69, 0.3);
}

.actions-cell .btn-check-out:hover {
   background: linear-gradient(45deg, #c82333, #bd2130) !important;
   box-shadow: 0 6px 8px rgba(220, 53, 69, 0.4);
}

/* Button Groups */
.btn-group {
   display: flex;
   align-items: center;
   gap: 8px;
}

.btn-group .btn {
   border-radius: 4px;
   margin: 0 2px;
}

/* Team Colors */
.team-Team1 { background: linear-gradient(to bottom, #e6e6e6, #deb5f7) !important; border: 1px solid #b3b3b3; }
.team-Team2 { background: linear-gradient(to bottom, #b3ffb3, #80ff80) !important; border: 1px solid #4dff4d; }
.team-Team3 { background: linear-gradient(to bottom, #b3d9ff, #80bfff) !important; border: 1px solid #4da6ff; }
.team-Team4 { background: linear-gradient(to bottom, #ffd9b3, #ffbf80) !important; border: 1px solid #ffa64d; }
.team-Team5 { background: linear-gradient(to bottom, #ffb3ff, #ff80ff) !important; border: 1px solid #ff4dff; }
.team-Team6 { background: linear-gradient(to bottom, #b3fff9, #80fff5) !important; border: 1px solid #4dfff2; }
.team-Team7 { background: linear-gradient(to bottom, #ffcccc, #ff9999) !important; border: 1px solid #ff6666; }
.team-Team8 { background: linear-gradient(to bottom, #d9ffcc, #b3ff99) !important; border: 1px solid #8cff66; }
.team-Team9 { background: linear-gradient(to bottom, #ccd9ff, #99b3ff) !important; border: 1px solid #668cff; }
.team-Team10 { background: linear-gradient(to bottom, #fff2cc, #ffe699) !important; border: 1px solid #ffd966; }

/* Form Elements */
.room-checkboxes {
   max-height: 150px;
   overflow-y: auto;
   padding: 10px;
   border: 1px solid #dee2e6;
   border-radius: 5px;
   background: #fff;
}

.custom-checkbox { margin: 5px 10px; }
.custom-switch { padding-left: 2.25rem; }

.form-control:focus {
   border-color: #4d5aad;
   box-shadow: 0 0 0 0.2rem rgba(29, 35, 126, 0.25);
}

/* Table Styles */
.table td { vertical-align: middle !important; }
.table tbody tr:hover { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

/* Animation */
@keyframes highlight {
   0% { background-color: rgba(255, 255, 0, 0.5); }
   100% { background-color: transparent; }
}

.highlight { animation: highlight 1s ease-out; }

/* Utility Classes */
.mr-1 { margin-right: 0.25rem !important; }
.ml-1 { margin-left: 0.25rem !important; }
.mt-2 { margin-top: 0.5rem !important; }
.mb-2 { margin-bottom: 0.5rem !important; }
</style>