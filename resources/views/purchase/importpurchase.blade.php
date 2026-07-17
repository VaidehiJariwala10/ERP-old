@extends('layout.app')

@section('title', 'Import Purchase List')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Import Purchase List</h4>
            <h6>Upload CSV, XLS, or XLSX files with Supplier and document details.</h6>
        </div>
        <div class="back-button">
            <a href="{{ route('purchase.lists') }}" class="btn btn-cancel">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="purchaseImportForm" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8 col-md-10 col-12">
                        <div class="alert alert-light border mb-3">
                            <strong>Supported columns:</strong>
                            Supplier, Doc.Date, Doc.No., Doc.Value, Doc.Status, Purchase Type, Ref.Doc.Date,
                            Ref.Doc.No., I/M Code, Item/Model, IMEI No., Activation Date, Rate/Unit, Qty.,
                            Free Qty., Amount, DISC %, DISC Amount, Taxable Amount, CGST Amt., SGST Amt.,
                            IGST Amt., Net Amt.
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-10 col-12">
                        <div class="form-group">
                            <label>Upload Purchase List File <span class="text-danger">*</span></label>
                            <div class="image-upload">
                                <input type="file" id="purchaseImportFile" name="file" accept=".csv,.xls,.xlsx" required>
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}" alt="Upload Icon">
                                    <h4 id="purchaseImportFileName">Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            <div class="text-danger mt-1" id="purchaseImportFileError"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-submit me-2" id="purchaseImportSubmit">
                            Import
                        </button>
                        <a href="{{ route('purchase.lists') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#purchaseImportFile').on('change', function() {
        const fileName = this.files && this.files.length ? this.files[0].name : 'Drag and drop a file to upload';
        $('#purchaseImportFileName').text(fileName);
        $('#purchaseImportFileError').text('');
    });

    $('#purchaseImportForm').on('submit', function(e) {
        e.preventDefault();

        const fileInput = $('#purchaseImportFile')[0];
        if (!fileInput.files.length) {
            $('#purchaseImportFileError').text('Please select a CSV or Excel file.');
            return;
        }

        const $button = $('#purchaseImportSubmit');
        const originalHtml = $button.html();
        const formData = new FormData(this);
        formData.append('selectedSubAdminId', localStorage.getItem('selectedSubAdminId') || '');

        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Importing...');
        $('#purchaseImportFileError').text('');

        $.ajax({
            url: '{{ route('purchase.import-list') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                const errors = response.errors && response.errors.length
                    ? '<br><small>' + response.errors.slice(0, 5).join('<br>') + '</small>'
                    : '';

                Swal.fire({
                    title: 'Import Complete',
                    html: `Imported: ${response.imported || 0}<br>Updated: ${response.updated || 0}<br>Skipped: ${response.skipped || 0}${errors}`,
                    icon: 'success',
                    confirmButtonColor: '#ff9f43'
                }).then(function() {
                    window.location.href = '{{ route('purchase.lists') }}';
                });
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Import failed. Please check the file and try again.';
                $('#purchaseImportFileError').text(message);
                Swal.fire('Import Failed', message, 'error');
            },
            complete: function() {
                $button.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
@endpush
