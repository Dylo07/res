@extends('layouts.app')

@section('content')

@php
$date = request()->get('date');
@endphp

<div class="container">
  <div class="row">
    <div class="col">
      <form action="{{ route('pettycash.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
        <table class="table table-dark">
          <tbody>
            <tr>
              <th scope="row">Date</th>
              <td>
                <input type="date" name="trans_date" id="trans_date" value="{{ isset($date) ? $date : '' }}" class="form-control" onchange="checkDate()" />
                @error('trans_date')
                  <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                @enderror
              </td>
            </tr>
            <tr>
              <th scope="row">Type Of Transaction</th>
              <input type="hidden" id="tran_id" name="tran_id">
              <td>
                  <select class="form-control" name="TypeOfTrans" id="TypeOfTrans">
                    <option value="Cash_Withdraw">Cash Withdraw</option>
                    <option value="Salary_Advance">Salary Advance</option>
                    <option value="Salary">Salary</option>         
                    <option value="Grocery_Payment">Grocery Item</option>
                    <option value="Grocery_Payment">Juice Bar Items</option>
                    <option value="Card_Payment">Card Payment</option>
                    <option value="Maintenance_Work">Maintenance Work</option>
                    <option value="Softdrink_Payment">Chemicals</option>
                    <option value="Liquor_Bill">Soft Drink</option>                                     
                    <option value="Electricity_Bill">Electricity</option>
                    
                  </select>
                  @error('TypeOfTrans')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                  @enderror
              </td>
            </tr>
            <tr>
              <th scope="row">Employee</th>
              <td>
                  <select class="form-control" name="Employee" id="Employee">
                    <option value="None">None</option>
                    <option value="MD">MD</option>
                    <option value="Samarakkodi">Samarakkodi</option>
                    <option value="Neranjan">Neranjan</option>
                    <option value="Siril">Siril</option>
                    <option value="Nadeera">Nadeera</option>
                    <option value="Appuhami">Appuhami</option>
                    <option value="Dayananda">Dayananda</option>
                    <option value="Mahinda">Mahinda</option>
                    <option value="Ashan">Ashan</option>
                    <option value="Karunasena">Karunasena</option>
                    <option value="Himanka">Himanka</option>
                    <option value="Pasidu">Pasidu</option>
                    <option value="Dammika">Dammika</option>
                    <option value="Manike">Manike</option>
                    <option value="Sisira">Sisira</option>
                    

                    
                  </select>
                  @error('Employee')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                  @enderror
              </td>
            </tr>
            
            <tr>
              
              <th scope="row">Description</th>
              <td>
                <div class="form-group">
                  <textarea class="form-control" rows="5" id="Description" name="Description"></textarea>
                </div>
                @error('Description')
                  <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                @enderror
              </td>
            </tr>
            <tr>
              <th scope="row">Amount</th>
              <td>
                <div class="form-outline">
                  <input  type="number" name="Amount" class="form-control" id="Amount"/>
                  @error('Amount')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                  @enderror
                </div>
              </td>
          </tbody>
        </table>
        <button type="submit" class="btn btn-success btn-lg btn-block" style="display: none;" id="dynamicBtn">ADD</button>
      </form>
</div>

<div class="col">
  @if($date)
    <p class="p-2 mb-1 bg-primary text-white">Monthly Petty Cash Summary for {{ date('F Y', strtotime($date)) }}</p>    
    <table class="table table-dark">
      <thead class="text-light bg-success">
        <tr>
          <th scope="col">Description</th>
          <th scope="col">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php $gt = 0; ?>
        @foreach ($summeries as $sum)
          <tr>
            <td scope="row">{{ $sum->TypeOfTrans }}</td>
            <td scope="col">{{ $sum->total }}</td>
          </tr>
          <?php $gt += $sum->total; ?>
        @endforeach
        <tr>
          <td scope="row">Total Monthly Expenses</td>
          <td scope="col">{{ $gt }}</td>
        </tr>
      </tbody>
    </table>
  @else
    <p class="text-warning">Please select a date to view the monthly summary.</p>
  @endif
</div>

<div class="p-5">
@if($date)
  <table class="table table-dark" id="transactions">
  <pr class="p-2 mb-1 bg-primary text-white">Daily Petty Cash Summery</pr> 
    <thead class="text-light bg-success">
      <tr>
        <th scope="col">Type of Transaction</th>
        <th scope="col">Employee</th>
        <th scope="col">Description</th>
        <th scope="col">Amount</th>
        <th scope="col">Date</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody id="transactions_body">
    @foreach ($trans as $tran)
      <tr>
        <th scope="row">{{ $tran->TypeOfTrans() }}</th>
        <td>{{ $tran->Employee }}</td>
        <td>{{ $tran->Description }}</td>
        <td>{{ $tran->Amount }}</td>
        <td>{{ $tran->trans_date }}</td>
        <td>
              <a class="btn btn-primary get_data" onclick="updateData({{ json_encode($tran) }})">Edit</a>
              <a class="btn btn-danger" style="display: none;"  href="{{ route('pettycash.destroy',$tran->id) }}">Delete</a>
        </td>

        
      </tr>
      @endforeach
    </tbody>
  </table>
  <p><p>
    
  <div class="col">
    <tr>            
      <table class="table table-dark">
        
         
        
          <tr>
            <th scope="row">Total Daily Expence</th>
            <th scope="col">{{number_format($ggg)}}</th>
          </tr>
        </tbody>
      </table>
    </tr>
  </div>




  {!! $trans->links("pagination::bootstrap-4") !!}
  @endif
</div>

</div>
</div>
<script>
function updateData(data) {

  document.getElementById("tran_id").value = data.id;
  document.getElementById("trans_date").value = data.trans_date;
  document.getElementById("TypeOfTrans").value = data.TypeOfTrans;
  document.getElementById("Employee").value = data.Employee;
  document.getElementById("Description").value = data.Description;
  document.getElementById("Amount").value = data.Amount;

  document.getElementById("dynamicBtn").innerHTML = 'Update';
}
$( document ).ready(function() {
    onClickDate = document.getElementById('trans_date').value;

    const date = new Date();
    let day = date.getDate();
    let month = date.getMonth()+1;
    let year = date.getFullYear();

    currDate = year+'-'+month.toString().padStart(2, '0')+'-'+day.toString().padStart(2, '0')

    let param_date = `{{ $date }}`;
    if(onClickDate != currDate){
      document.getElementById("dynamicBtn").style.display = "none";
    }else{
      document.getElementById("dynamicBtn").style.display = "block  ";
    }
});
function checkDate(){
  onClickDate = document.getElementById('trans_date').value;

  const date = new Date();
  let day = date.getDate();
  let month = date.getMonth()+1;
  let year = date.getFullYear();

  currDate = year+'-'+month.toString().padStart(2, '0')+'-'+day.toString().padStart(2, '0')

  let param_date = `{{ $date }}`;
  if(onClickDate != currDate){
    document.getElementById("dynamicBtn").style.display = "none";
  }else{
    document.getElementById("dynamicBtn").style.display = "block  ";
  }
  let url = `{{ url('pettycash?date=') }}`;
  window.location.href= url+onClickDate;
}
</script>
@endsection

