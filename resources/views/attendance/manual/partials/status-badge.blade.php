@if($status == 'present')
    <span class="badge badge-success">Present</span>
@elseif($status == 'half')
    <span class="badge badge-warning">Half Day</span>
@elseif($status == 'absent')
    <span class="badge badge-danger">Absent</span>
@else
    <span class="badge badge-secondary">Not Marked</span>
@endif