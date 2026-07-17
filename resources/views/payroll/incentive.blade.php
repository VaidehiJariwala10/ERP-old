@extends('layout.app')
@section('title', 'Incentive Calculation')
@section('content')
<style>
   .capitalize-text { text-transform: capitalize; }
   .summary-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: center; }
   .summary-card h3 { color: #FF9F43; margin-bottom: 0; font-weight: bold; }
</style>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title mb-0">Incentive Calculation</h4>
            <div class="d-flex align-items-center gap-3">
                <input type="month" id="salaryMonth" name="salaryMonth" class="form-control form-control-sm"
                    value="{{ $month }}" />
                <button type="button" class="btn btn-primary" id="saveAllBtn" style="white-space: nowrap;">
                    {{ collect($employees)->contains('is_saved', true) ? 'Update All' : 'Save All' }}
                </button>
                <a href="/payrollview" class="btn btn-primary" style="white-space: nowrap;">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="summary-card">
            <p class="mb-1 text-muted" style="font-size: 16px;">Total Incentive Payout for {{ date('F Y', strtotime($month)) }}</p>
            <h3>₹{{ number_format($totalIncentivePayout, 2) }}</h3>
        </div>

        <form id="incentiveForm">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <div class="table-responsive">
            <table class="table table-striped" id="incentive-table">
              <thead>
                <tr>
                  <th>Staff Name</th>
                  <th>Designation</th>
                  <th style="width: 15%;">Individual Total Sales (₹)</th>
                  <th style="width: 15%;">Incentive Percentage (%)</th>
                  <th style="width: 15%;">Calculated Incentive (₹)</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($employees as $emp)
                <tr>
                    <td class="capitalize-text">
                        <div class="d-flex align-items-center gap-2">
                            @if (!empty($emp['profile_image']))
                                <img src="{{ env('ImagePath') }}/storage/{{ $emp['profile_image'] }}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @else
                                <img src="{{ env('ImagePath') }}/admin/assets/img/customer/customer5.jpg" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @endif
                            <span>{{ $emp['firstname'] }}</span>
                        </div>
                    </td>
                    <td>{{ $emp['designation'] }}</td>
                    <td>
                        <input type="hidden" name="employee_id[]" value="{{ $emp['id'] }}">
                        <div class="input-group input-group-sm" style="width: 130px; margin: 0 auto;">
                            <span class="input-group-text px-2">₹</span>
                            <input type="number" step="0.01" class="form-control total-sales-input text-end" name="total_sales[]" value="{{ $emp['total_sales'] }}" style="padding: 0.25rem 0.5rem;">
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm" style="width: 90px; margin: 0 auto;">
                            <input type="number" step="0.01" class="form-control incentive-percent-input text-end" name="incentive_percentage[]" value="{{ $emp['incentive_percentage'] }}" style="padding: 0.25rem 0.5rem;">
                            <span class="input-group-text px-2">%</span>
                        </div>
                    </td>
                    <td style="font-weight: bold; color: #28a745; text-align: center;">
                        <div class="input-group input-group-sm" style="width: 120px; margin: 0 auto;">
                            <span class="input-group-text px-2 border-0 bg-transparent">₹</span>
                            <input type="text" class="form-control incentive-amount-input text-start" name="incentive_amount[]" value="{{ $emp['incentive_amount'] }}" readonly style="background-color: transparent; border: none; padding: 0.25rem 0; font-weight: bold; color: #28a745;">
                        </div>
                    </td>
                    <td>
                        <button type="button"
                            class="btn btn-sm {{ $emp['is_saved'] ? 'btn-outline-warning' : 'btn-outline-primary' }} save-row-btn"
                            data-id="{{ $emp['id'] }}">
                            {{ $emp['is_saved'] ? 'Update' : 'Save' }}
                        </button>
                    </td>
                </tr>
                @endforeach
              </tbody>
            </table>
        </div>
        </form>

      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const salaryMonthInput = document.getElementById('salaryMonth');
    if (salaryMonthInput) {
      salaryMonthInput.addEventListener('change', function() {
        const selectedMonth = this.value;
        if (selectedMonth) {
          window.location.href = `{{ route('payroll.incentive') }}?month=${selectedMonth}`;
        }
      });
    }

    let dt = null;
    if($.fn.DataTable) {
        dt = $('#incentive-table').DataTable({
            "bFilter": true,
            "bLengthChange": true,
            "pageLength": 25,
            "ordering": false // Disable ordering so inputs don't jump around when typing
        });
    }

    // Dynamic Calculation
    $('#incentive-table').on('input', '.total-sales-input, .incentive-percent-input', function() {
        const row = $(this).closest('tr');
        const sales = parseFloat(row.find('.total-sales-input').val()) || 0;
        const percent = parseFloat(row.find('.incentive-percent-input').val()) || 0;
        
        const amount = (sales * (percent / 100)).toFixed(2);
        row.find('.incentive-amount-input').val(amount);
    });

    // Save All
    $('#saveAllBtn').click(function() {
        // Serialize all data from datatable (including hidden pages)
        const formData = dt ? dt.$('input').serialize() : $('#incentiveForm').serialize();
        const month = $('input[name="month"]').val();
        const token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ route("payroll.incentive.save") }}',
            type: 'POST',
            data: formData + '&month=' + month + '&_token=' + token,
            success: function(response) {
                if(response.status) {
                    Swal.fire({
                        title: "Success!",
                        text: "All incentives saved successfully!",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(err) {
                Swal.fire({
                    title: "Error!",
                    text: "Error saving incentives.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });

    // Save Single Row
    $('#incentive-table').on('click', '.save-row-btn', function() {
        const row = $(this).closest('tr');
        const id = $(this).data('id');
        const sales = row.find('.total-sales-input').val();
        const percent = row.find('.incentive-percent-input').val();
        const amount = row.find('.incentive-amount-input').val();
        const month = $('input[name="month"]').val();
        const token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ route("payroll.incentive.save") }}',
            type: 'POST',
            data: {
                _token: token,
                month: month,
                employee_id: [id],
                total_sales: [sales],
                incentive_percentage: [percent],
                incentive_amount: [amount]
            },
            success: function(response) {
                if(response.status) {
                    Swal.fire({
                        title: "Success!",
                        text: "Incentive saved successfully!",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(err) {
                Swal.fire({
                    title: "Error!",
                    text: "Error saving incentive.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });
});
</script>
@endsection
