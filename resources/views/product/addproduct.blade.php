@extends('layout.app')

@section('title', 'Product Add')

@section('content')

    <style>

        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 10px !important
            }
        }

        .image-upload .image-uploads h4 {
            font-size: 12px !important;
        }
        a.btn.back-button {
    background: #ff9f43;
    color: #fff;
}

        .form-label-icon {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-label-icon i {
            color: #ff9f43;
            font-size: 13px;
            width: 14px;
            text-align: center;
        }

        .form-label-icon .required {
            color: #dc3545;
            margin-left: 2px;
        }

        .barcode-input-group {
            display: flex;
            align-items: stretch;
            gap: 8px;
        }

        .barcode-input-group .form-control {
            flex: 1;
        }

        .barcode-scan-btn {
            min-width: 46px;
            border: 1px solid #dee2e6;
            background: #fff;
            color: #ff9f43;
            border-radius: 6px;
        }

        .barcode-scan-btn:hover,
        .barcode-scan-btn:focus {
            background: #ff9f43;
            color: #fff;
        }

        .field-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }

        .quick-add-btn {
            border: 0;
            background: #ff9f43;
            color: #fff;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        .quick-add-btn:hover,
        .quick-add-btn:focus {
            background: #f38e27;
            color: #fff;
        }

        #barcode-qr-reader {
            width: 100%;
            min-height: 300px;
        }


    </style>
    <div class="content">
        <div class="page-header ">
            <div class="page-title">
                <h4>Add Product</h4>
            </div>
             <div class="back-button">
                <a href="{{ route('product.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i> Back</a></br>
                            <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="productForm">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-box"></i> Product Name <span class="required">*</span></label>
                                <input type="text" name="name" id="name">
                                <span class="error_name text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <div class="field-label-row">
                                    <label class="form-label-icon mb-0"><i class="fa-solid fa-layer-group"></i> Category <span class="required">*</span></label>
                                    <button type="button" class="quick-add-btn" id="openCategoryModal">Add Category</button>
                                </div>
                                <select class="select" name="category_id" id="category_id">
                                    <option value="">Choose Category</option>
                                </select>
                                <span class="error_category_id text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <div class="field-label-row">
                                    <label class="form-label-icon mb-0"><i class="fa-solid fa-tag"></i> Brand</label>
                                    <button type="button" class="quick-add-btn" id="openBrandModal">Add Brand</button>
                                </div>
                                <select class="select" name="brand_id" id="brand_id">
                                    <option value="">Choose Brand</option>

                                </select>
                                <span class="error_brand_id text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-barcode"></i> SKU</label>
                                <input type="number" class="form-control" name="SKU" id="SKU">
                                <span class="error_SKU text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-hashtag"></i> HSN Code</label>
                                <input type="text" class="form-control" name="hsn_code" id="hsn_code">
                                <span class="error_hsn_code text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-percent"></i> GST Option</label>
                                <select class="select" name="gst_option" id="gst_option">
                                    <option value="">Choose GST Option</option>
                                    <option value="without_gst">Without GST</option>
                                    <option value="with_gst">With GST</option>
                                </select>
                                <span class="error_gst_option text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6" id="gst_dropdown_container" style="display: none;">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-receipt"></i> Product GST</label>
                                <select class="select" name="product_gst[]" id="product_gst" multiple>
                                    <option value="">Choose GST Rate</option>
                                </select>
                                <span class="error_product_gst text-danger"></span>
                            </div>
                        </div>

                         <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <div class="field-label-row">
                                    <label class="form-label-icon mb-0"><i class="fa-solid fa-ruler-combined"></i> Unit <span class="required">*</span></label>
                                    <button type="button" class="quick-add-btn" id="openUnitModal">Add Unit</button>
                                </div>
                                <select class="select" name="unit_id" id="unit_id">
                                    <option value="">Choose Unit</option>
                                </select>
                                <span class="error_unit_id text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-cubes"></i> Quantity <span class="required">*</span></label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="0"
                                    step="1">
                                <span class="error_quantity text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6 serial-no-container" style="display: none;">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-fingerprint"></i> IMEI No <a href="javascript:void(0)" class="edit-serial-btn text-warning ml-1" title="Edit Serial Numbers"><i class="fas fa-edit"></i></a></label>
                                <div class="serial-status text-muted" style="font-size: 11px; line-height: 1.2;">0/0 IMEI numbers added</div>
                                <input type="hidden" id="imei_no" name="imei_no" value="[]">
                                <span class="error_imei_no text-danger"></span>
                            </div>
                        </div>

                       

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-indian-rupee-sign"></i> Price <span class="required">*</span></label>
                                <input type="number" name="price" id="price" class="form-control" min="0"
                                    step="0.01">
                                <span class="error_price text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-indian-rupee-sign"></i> Cost Price</label>
                                <input type="number" name="cost_price" id="cost_price" class="form-control" min="0" step="0.01" placeholder="0.00">
                                <span class="error_cost_price text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-truck-ramp-box"></i> Landing Cost</label>
                                <input type="number" name="landing_cost" id="landing_cost" class="form-control" min="0" step="0.01" placeholder="0.00">
                                <span class="error_landing_cost text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-code"></i> Product Code</label>
                                <input type="text" name="product_code" id="product_code" class="form-control" placeholder="e.g. 10025">
                                <span class="error_product_code text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-truck"></i> Supplier Code</label>
                                <input type="text" name="supplier_code" id="supplier_code" class="form-control" placeholder="Supplier I/M Code">
                                <span class="error_supplier_code text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-globe"></i> International Code</label>
                                <input type="text" name="international_code" id="international_code" class="form-control" placeholder="International I/M Code">
                                <span class="error_international_code text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-fingerprint"></i> Serial No Status</label>
                                <select class="select" name="serial_no_status" id="serial_no_status">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                <span class="error_serial_no_status text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-layer-group"></i> Non Inventory Type</label>
                                <input type="text" name="non_inventory_type" id="non_inventory_type" class="form-control" placeholder="e.g. *None">
                                <span class="error_non_inventory_type text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-check-circle"></i> Stock Validation</label>
                                <select class="select" name="stock_validation_status" id="stock_validation_status">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                <span class="error_stock_validation_status text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-box-open"></i> Item Type</label>
                                <select class="select" name="item_type" id="item_type">
                                    <option value="">Select</option>
                                    <option value="Goods">Goods</option>
                                    <option value="Service">Service</option>
                                </select>
                                <span class="error_item_type text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-print"></i> Discount Print Status</label>
                                <select class="select" name="discount_print_status" id="discount_print_status">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                <span class="error_discount_print_status text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-calendar"></i> Item Created On</label>
                                <input type="date" name="item_created_on" id="item_created_on" class="form-control">
                                <span class="error_item_created_on text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-toggle-on"></i> Status</label>
                                <select class="select" name="status" id="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">InActive</option>
                                </select>
                                <span class="error_status text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-warehouse"></i> Stock</label>
                                <select class="select" name="availablility" id="availablility" disabled>
                                    <option value="in_stock">In Stock</option>
                                    <option value="out_stock">Out Of Stock</option>
                                </select>
                                <span class="error_availablility text-danger"></span>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const qtyInput = document.getElementById('quantity');

                                if (qtyInput) {
                                    qtyInput.addEventListener('input', function() {
                                        const qty = parseInt(this.value);

                                        // Update using jQuery + Select2
                                        if (!isNaN(qty) && qty === 0) {
                                            $('#availablility').val('out_stock').trigger('change');
                                        } else {
                                            $('#availablility').val('in_stock').trigger('change');
                                        }

                                        // console.log("Quantity changed to", qty);
                                    });
                                }
                            });
                        </script>


                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-qrcode"></i> Barcode</label>
                                <div class="barcode-input-group">
                                    <input type="text" name="barcode" id="barcode" class="form-control">
                                    <button type="button" cl    ass="btn barcode-scan-btn" id="openBarcodeScanner" title="Scan barcode">
                                        <i class="fa-solid fa-camera"></i>
                                    </button>
                                </div>
                                <span class="error_barcode text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-align-left"></i> Description</label>
                                <textarea class="form-control" name="description" id="description" rows="1"></textarea>
                                <span class="error_description text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-image"></i> Product Image</label>
                                <div class="image-upload">
                                    <input type="file" name="images[]" id="images" accept="image/*" multiple>
                                    <div class="image-uploads">
                                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/upload.svg' }}"
                                            alt="img">
                                        <h4>Drag and drop a file to upload</h4>
                                    </div>
                                </div>
                                <div class="image-preview" style="margin-top: 10px;"></div>
                                <span class="error_images text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <a href="javascript:void(0);" class="btn btn-submit me-2 submit">Submit</a>
                            <a href="{{ route('product.list') }}" class="btn btn-cancel">Cancel</a></br>
                            <span class="success_submit text-danger"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- Custom Modal -->
    <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customModalLabel">Add New</h5>
                    <button type="button" class="btn-close bg-white text-black" data-bs-dismiss="modal"
                        aria-label="Close" onclick="modalOpen=false;">x</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="custom_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="custom_name" placeholder="Enter name">
                        <span class="error_custom_name text-danger"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        onclick="modalOpen=false;">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveCustomBtn">Save</button>
                </div>
                <span class="text-danger error_model"></span>
            </div>
        </div>
    </div>

    <div class="modal fade" id="barcodeScannerModal" tabindex="-1" aria-labelledby="barcodeScannerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeScannerModalLabel">Scan Barcode</h5>
                    <button type="button" class="btn-close bg-white text-black" data-bs-dismiss="modal"
                        aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div id="barcode-qr-reader"></div>
                    <div id="barcode-scan-message" class="text-center mt-2 small text-muted">
                        Initializing camera...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Serial Number Modal -->
    <div class="modal fade" id="serialNumberModal" tabindex="-1" aria-labelledby="serialNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serialNumberModalLabel">Enter Serial Numbers (IMEI)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body" id="serialNumberModalBody">
                    <!-- Dynamic inputs will be appended here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveSerialNumbersBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        function isCustomSelectValue(value) {
            // A value is "custom" (user-typed tag) if it does not exist
            // as a pre-loaded option in ANY of the API-populated dropdowns.
            // We check by seeing if the option was created by Select2 tags
            // (Select2 marks new tag options with the class "select2-results__option--data").
            // The simplest reliable check: if the option element was NOT part of the
            // original server-fetched list, it's custom. We track this via a data attribute.
            // Fallback: treat ALL selected values as potentially custom — the modal will
            // only actually be shown when the selected option is a new tag.
            const str = String(value ?? '').trim();
            if (!str || str === '') return false;
            // If it was pre-loaded from API it will be in one of the populated selects.
            // Check each relevant select for a matching option that has data-from-api="true"
            const selects = ['#brand_id', '#category_id', '#unit_id'];
            for (const sel of selects) {
                const $opt = $(sel).find(`option[value="${CSS.escape(str)}"]`);
                if ($opt.length && $opt.data('from-api')) {
                    return false; // it's a real DB record
                }
            }
            return true; // not found as a real record → it's a new tag
        }

        function normalizeSelectText(value) {
            return String(value ?? '').trim().replace(/\s+/g, ' ').toLowerCase();
        }

        function findExistingOptionByText($select, text) {
            const normalizedText = normalizeSelectText(text);
            if (!normalizedText) return $();

            let $match = $();
            $select.find('option').each(function() {
                const $option = $(this);
                if (!$option.val() || !$option.data('from-api')) return;

                if (normalizeSelectText($option.text()) === normalizedText) {
                    $match = $option;
                    return false;
                }
            });

            return $match;
        }

        function selectExistingOptionIfDuplicateTag($select, text) {
            const $existingOption = findExistingOptionByText($select, text);
            if (!$existingOption.length) return false;

            $select.val($existingOption.val()).trigger('change');
            return true;
        }

        function createTagOnlyWhenNew(selector) {
            return function(params) {
                const term = $.trim(params.term || '');
                if (!term) return null;

                return findExistingOptionByText($(selector), term).length ? null : {
                    id: term,
                    text: term,
                    newTag: true
                };
            };
        }

        function isWarrantyCategorySelected() {
            return normalizeSelectText($('#category_id option:selected').text()) === 'warranty';
        }

        function applyWarrantyDefaults() {
            if (!isWarrantyCategorySelected()) return;

            if ($('#quantity').val() === '') {
                $('#quantity').val('0');
            }

            if ($('#price').val() === '') {
                $('#price').val('0');
            }

            $('.error_quantity, .error_price').text('');
        }

        document.getElementById("images").addEventListener("change", function(event) {
            var files = Array.from(event.target.files);
            var previewDiv = document.querySelector(".image-preview");
            var errorSpan = document.querySelector(".error_images");
            var validFiles = files.filter(file => file.type.startsWith("image/"));

            previewDiv.innerHTML = "";
            errorSpan.textContent = "";

            if (validFiles.length !== files.length) {
                errorSpan.textContent = "Only image files are allowed.";
                event.target.value = "";
                return;
            }

            validFiles.forEach((file) => {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var imageContainer = document.createElement("div");
                    imageContainer.style.position = "relative";
                    imageContainer.style.display = "inline-block";

                    var img = document.createElement("img");
                    img.src = e.target.result;
                    img.alt = "Preview";
                    img.style.maxWidth = "100px";
                    img.style.maxHeight = "100px";
                    img.style.borderRadius = "5px";
                    img.style.boxShadow = "2px 2px 10px rgba(0,0,0,0.1)";
                    img.style.marginRight = "5px";

                    var removeBtn = document.createElement("button");
                    removeBtn.innerHTML = "&times;";
                    removeBtn.style.position = "absolute";
                    removeBtn.style.top = "0";
                    removeBtn.style.right = "0";
                    removeBtn.style.background = "red";
                    removeBtn.style.color = "white";
                    removeBtn.style.border = "none";
                    removeBtn.style.borderRadius = "50%";
                    removeBtn.style.width = "20px";
                    removeBtn.style.height = "20px";
                    removeBtn.style.cursor = "pointer";
                    removeBtn.style.display = "flex";
                    removeBtn.style.alignItems = "center";
                    removeBtn.style.justifyContent = "center";
                    removeBtn.style.fontSize = "14px";

                    removeBtn.addEventListener("click", function() {
                        imageContainer.remove();
                    });

                    imageContainer.appendChild(img);
                    imageContainer.appendChild(removeBtn);
                    previewDiv.appendChild(imageContainer);
                };

                reader.readAsDataURL(file);
            });
        });
        $(document).on("input", "#price", function() {
            let value = parseFloat($(this).val());
            let errorSpan = $(".error_price");

            if (value < 0) {
                $(this).val(""); // clear invalid value
                errorSpan.text("Price cannot be negative.");
            } else {
                errorSpan.text(""); // clear error if valid
            }
        });
        $(document).on("change", "#category_id", applyWarrantyDefaults);
        $(document).ready(function() {
            $(document).on('click', '.submit', function(e) {
                e.preventDefault();
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                // console.log(selectedSubAdminId);
                var $btn = $(this); // cache the button
                var originalText = $btn.html();
                // Show loading text and disable the button
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                ).prop('disabled', true);

                var authToken = localStorage.getItem("authToken");
                applyWarrantyDefaults();
                let formData = new FormData($('#productForm')[0]);

                if (isWarrantyCategorySelected()) {
                    formData.set('quantity', $('#quantity').val() || '0');
                    formData.set('price', $('#price').val() || '0');
                }

                // Remove product_gst if "without_gst" is selected
                const gstOption = $('#gst_option').val();
                if (gstOption !== 'with_gst') {
                    formData.delete('product_gst[]');
                }

                if (selectedSubAdminId) {
                    formData.append("sub_admin_id", selectedSubAdminId);
                }
                // Clear error texts
                $('.error_name, .error_category_id, .error_brand_id, .error_SKU, .error_hsn_code, .error_gst_option, .error_product_gst, .error_quantity, .error_unit_id, .error_price, .error_status, .error_barcode, .error_description, .error_availablility, .error_images, .error_imei_no')
                    .text('');

                $.ajax({
                    url: "/api/createProduct",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);

                        if (response.status) {
                            Swal.fire({
                                title: "Success",
                                text: "Product added successfully",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        "{{ route('product.list') }}";
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('.error_name, .error_category_id, .error_brand_id, .error_SKU, .error_hsn_code, .error_gst_option, .error_product_gst, .error_quantity, .error_unit_id, .error_price, .error_status, .error_barcode, .error_description, .error_availablility, .error_images, .error_imei_no')
                                .text('');

                            $.each(errors, function(key, value) {
                                let errorKey = key.split('.')[0];
                                let errorMsg = value.join(' ');
                                $('.error_' + errorKey).text(errorMsg);
                            });
                        }
                    }
                });
            });
        });

        $(document).ready(function() {
            const sub_branch_id = localStorage.getItem('selectedSubAdminId');
            var authToken = localStorage.getItem("authToken");

            // 🔹 Fetch Units
            function get_units(selectedId = null) {
                let url = sub_branch_id ?
                    `/api/get-units?sub_branch_id=${sub_branch_id}` :
                    `/api/get-units`;
                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let select = $("#unit_id");
                            select.empty().append('<option value="">Choose Unit</option>');

                            $.each(response.data, function(key, item) {
                                select.append(
                                    $('<option>', { value: item.id, text: item.unit_name }).data('from-api', true)
                                );
                            });

                            if (selectedId) {
                                select.val(selectedId).trigger('change');
                            }
                        } else {
                            console.warn("No Units found.");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching Units:", xhr.responseText);
                    }
                });
            }

            // 🔹 Fetch Brands
            function get_brand(selectedId = null) {
                let url = sub_branch_id ?
                    `/api/get-brand?sub_branch_id=${sub_branch_id}` :
                    `/api/get-brand`;

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let select = $("#brand_id");
                            select.empty().append('<option value="">Choose Brand</option>');

                            $.each(response.data, function(key, item) {
                                select.append(
                                    $('<option>', { value: item.id, text: item.name }).data('from-api', true)
                                );
                            });

                            if (selectedId) {
                                select.val(selectedId).trigger('change');
                            }
                        } else {
                            console.warn("No Brand found.");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching Brand:", xhr.responseText);
                    }
                });
            }

            // 🔹 Fetch Categories
            function get_category(selectedId = null) {
                let url = sub_branch_id ?
                    `/api/get-category?sub_branch_id=${sub_branch_id}` :
                    `/api/get-category`;

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let select = $("#category_id");
                            select.empty().append('<option value="">Choose Category</option>');

                            $.each(response.data, function(key, item) {
                                select.append(
                                    $('<option>', { value: item.id, text: item.name }).data('from-api', true)
                                );
                            });

                            if (selectedId) {
                                select.val(selectedId).trigger('change');
                            }
                        } else {
                            console.warn("No Category found.");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching Category:", xhr.responseText);
                    }
                });
            }

            // 🔹 Select2 Init
            $('#brand_id').select2({
                placeholder: "Select or Add Brand",
                tags: true,
                createTag: createTagOnlyWhenNew('#brand_id'),
                width: '100%',
                allowClear: true,
            });

            $('#category_id').select2({
                placeholder: "Select or Add Category",
                tags: true,
                createTag: createTagOnlyWhenNew('#category_id'),
                width: '100%',
                allowClear: true,
            });

            $('#product_type_id').select2({
                placeholder: "Select or Add Product Type",
                tags: true,
                createTag: createTagOnlyWhenNew('#product_type_id'),
                width: '100%',
                allowClear: true,
            });

            $('#unit_id').select2({
                placeholder: "Select or Add Unit",
                tags: true,
                createTag: createTagOnlyWhenNew('#unit_id'),
                width: '100%',
                allowClear: true,
            });

            // 🔹 Modal Logic
            window.modalOpen = false;
            window.currentCustomValue = null;
            window.currentType = null;

            // Brand event
            $('#brand_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "brand";
                    $('#customModalLabel').text("Add New Brand");
                    $('#custom_name').val(name);
                    $('#customModal').modal('show');
                }
            });

            // Category event
            $('#category_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "category";
                    $('#customModalLabel').text("Add New Category");
                    $('#custom_name').val(name);
                    $('#customModal').modal('show');
                }
            });

            // Product Type event
            $('#product_type_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "product_type";
                    $('#customModalLabel').text("Add New Product Type");
                    $('#custom_name').val(name);
                    $('#customModal').modal('show');
                }
            });

            // Unit event
            $('#unit_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "unit";
                    $('#customModalLabel').text("Add New Unit");
                    $('#custom_name').val(name);
                    $('#customModal').modal('show');
                }
            });
            // 🔹 Save custom item from modal
            $('#saveCustomBtn').on('click', function() {
                const name = $('#custom_name').val().trim();
                let sub_admin_id = sub_branch_id;

                $(".error_custom_name").text(""); // reset error

                if (name === "") {
                    $(".error_custom_name").text("Please enter a name");
                    return;
                }

                let url = "";
                let targetDropdown = "";
                let postData = {
                    sub_admin_id: sub_admin_id,
                    name: name

                };

                if (currentType === "brand") {
                    url = "/api/addBrand";
                    targetDropdown = "#brand_id";
                } else if (currentType === "category") {
                    url = "/api/addcategory";
                    targetDropdown = "#category_id";
                } else if (currentType === "unit") {
                    url = "/api/add-units";
                    targetDropdown = "#unit_id";
                    postData = {
                        unitname: name,
                        selectedSubAdminId: sub_branch_id
                    };
                    // postData.status = "Active";
                }

                //  else if (currentType === "product_type") {
                //     url = "api/product-type-add";
                //     targetDropdown = "#product_type_id";
                //     postData.status = "Active"; // ✅ Default Active
                // }

                // 🔹 Show loader on button
                let btn = $("#saveCustomBtn");
                btn.prop("disabled", true).html("Saving... <i class='fa fa-spinner fa-spin'></i>");

                $.ajax({
                    url: url,
                    type: "POST",
                    data: postData,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        // // console.log(response);

                        let id = null;
                        let text = null;
                        let brandData = response.brand ?? response.data ?? null;
                        // ✅ Brand Response
                        // if (currentType === "brand" && response.brand) {
                        //     id = response.brand.id;
                        //     text = response.brand.name;
                        // }
                        if (currentType === "brand" && brandData) {
                            id = brandData.id;
                            text = brandData.name;
                        }

                        // ✅ Category Response
                        else if (currentType === "category" && response.category) {
                            id = response.category.id;
                            text = response.category.name;
                        }
                        // ✅ Product Type Response
                        else if (currentType === "product_type" && response.data) {
                            id = response.data.id;
                            text = response.data.name;
                        }
                        // ✅ Unit Response
                        else if (currentType === "unit") {
                            let unitData = response.data ?? response.unit ??
                                null; // handle both keys
                            if (unitData) {
                                id = unitData.id;
                                text = unitData.unit_name ?? unitData.name ?? null; // fallback
                            }
                        }

                        if (!id) {
                            $(".error_custom_name").text("Invalid response format");
                            btn.prop("disabled", false).text("Save");
                            return;
                        }

                        // remove temp option
                        if (currentCustomValue && isCustomSelectValue(currentCustomValue)) {
                            $(targetDropdown).find(`option[value="${currentCustomValue}"]`)
                                .remove();
                        }

                        // add new option
                        if ($(targetDropdown).find(`option[value="${id}"]`).length === 0) {
                            $(targetDropdown).append(new Option(text, id, true, true));
                        }

                        // select new
                        $(targetDropdown).val(id).trigger('change');

                        // refresh dropdowns
                        if (currentType === "brand") {
                            get_brand(id);
                        } else if (currentType === "category") {
                            get_category(id);
                        } else if (currentType === "unit") {
                            get_units(id);
                        }

                        // ✅ Success: show SweetAlert then close modal
                        btn.prop("disabled", false).text("Save");
                        const typeName = currentType.charAt(0).toUpperCase() + currentType.slice(1);
                        $('#customModal').modal('hide');
                        modalOpen = false;
                        currentCustomValue = null;
                        currentType = null;
                        Swal.fire({
                            title: "Added!",
                            text: typeName + " added successfully.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                            timer: 2000,
                            timerProgressBar: true,
                        });
                    },
                    // error: function(xhr) {
                    //     let msg = "Error while saving " + currentType;
                    //     if (xhr.responseJSON && xhr.responseJSON.message) {
                    //         msg = xhr.responseJSON.message;
                    //     }
                    //     $(".error_model").text(msg);

                    //     // reset loader
                    //     btn.prop("disabled", false).text("Save");
                    //     console.error(xhr.responseText);
                    // }
                    error: function(xhr) {
                        let msg = "Error while saving " + currentType;

                        // ✅ Handle Laravel validation errors
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                // Take the first validation error from the object
                                let firstKey = Object.keys(xhr.responseJSON.errors)[0];
                                msg = xhr.responseJSON.errors[firstKey][0];
                            } else if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                        }

                        $(".error_custom_name").text(msg);

                        // reset loader
                        btn.prop("disabled", false).text("Save");
                        console.error(xhr.responseText);
                    }

                });
            });

            $('#unit_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "unit";
                    $('#customModalLabel').text("Add New Unit");
                    $('#custom_name').val(name);
                    $(".error_model").text('');
                    $('#customModal').modal('show');
                }
            });

            // Reset flag when modal closes
            $('#customModal').on('hidden.bs.modal', function() {
                modalOpen = false;
                currentCustomValue = null;
                currentType = null;
                $(".error_custom_name").text('');
            });

            // ✅ Page load par call karo
            let barcodeScanner = null;
            let barcodeScriptLoading = null;

            function setSelectValue(selector, value) {
                if (value === null || value === undefined || value === '') {
                    return;
                }

                const stringValue = String(value);
                const select = $(selector);

                if (select.find(`option[value="${stringValue}"]`).length === 0) {
                    select.append(new Option(stringValue, stringValue, false, false));
                }

                select.val(stringValue).trigger('change');
            }

            function fetch_gst_rates_with_selection(selectedIds = []) {
                const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                let url = sub_branch_id ?
                    `/api/get-tax-rates?sub_branch_id=${sub_branch_id}` :
                    `/api/get-tax-rates`;

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (!response.status) {
                            return;
                        }

                        let select = $("#product_gst");
                        select.empty();

                        $.each(response.data, function(key, item) {
                            select.append(
                                `<option value="${item.id}">${item.tax_name} (${item.tax_rate}%)</option>`
                            );
                        });

                        const values = (selectedIds || []).map(function(id) {
                            return String(id);
                        });

                        select.val(values).trigger('change');
                    },
                    error: function(xhr) {
                        console.error("Error fetching GST rates:", xhr.responseText);
                    }
                });
            }

            function handleScannedBarcode(barcode) {
                const cleanedBarcode = String(barcode || '').trim();

                if (!cleanedBarcode) {
                    return false;
                }

                $('#barcode').val(cleanedBarcode);
                return true;
            }

            function stopBarcodeScanner() {
                if (!barcodeScanner) {
                    return;
                }

                if (barcodeScanner.isScanning) {
                    barcodeScanner.stop().then(function() {
                        barcodeScanner = null;
                    }).catch(function() {
                        barcodeScanner = null;
                    });
                } else {
                    barcodeScanner = null;
                }
            }

            function showManualBarcodeInput() {
                $('#barcode-scan-message').hide();
                $('#barcode-qr-reader').html(`
                    <div style="text-align:center; padding: 30px 20px;">
                        <p style="color:#666; font-size:14px; margin-bottom:16px;">
                            Camera is not available.<br>Enter barcode manually below:
                        </p>
                        <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                            <input type="text" id="manualBarcodeInput" class="form-control"
                                placeholder="Enter barcode / product code"
                                style="max-width:260px; font-size:14px;">
                            <button type="button" class="btn btn-primary" id="manualBarcodeSubmit">Add</button>
                        </div>
                        <div id="manualBarcodeError" class="text-danger" style="font-size:14px; margin-top:8px;"></div>
                    </div>
                `);
            }

            function submitManualBarcode() {
                const barcode = $('#manualBarcodeInput').val().trim();

                if (!barcode) {
                    $('#manualBarcodeError').text('Please enter a barcode.');
                    return;
                }

                $('#manualBarcodeSubmit').prop('disabled', true).text('Adding...');
                $('#manualBarcodeError').text('');

                try {
                    const found = handleScannedBarcode(barcode);

                    if (found) {
                        $('#barcodeScannerModal').modal('hide');
                        $('#manualBarcodeInput').val('');
                    }
                } catch (error) {
                    $('#manualBarcodeError').text('Unable to add barcode.');
                } finally {
                    $('#manualBarcodeSubmit').prop('disabled', false).text('Add');
                }
            }

            function bindManualBarcodeEvents() {
                $('#barcode-qr-reader').off('click', '#manualBarcodeSubmit').on('click', '#manualBarcodeSubmit',
                    function() {
                        submitManualBarcode();
                    });

                $('#barcode-qr-reader').off('keydown', '#manualBarcodeInput').on('keydown',
                    '#manualBarcodeInput',
                    function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            submitManualBarcode();
                        }
                    });
            }

            function onBarcodeScanSuccess(decodedText) {
                stopBarcodeScanner();
                $('#barcodeScannerModal').modal('hide');
                $('#barcode-scan-message').text('');
                handleScannedBarcode(decodedText);
            }

            function onBarcodeScanError() {}

            function loadBarcodeScannerLibrary() {
                if (typeof Html5Qrcode !== 'undefined') {
                    return Promise.resolve();
                }

                if (barcodeScriptLoading) {
                    return barcodeScriptLoading;
                }

                barcodeScriptLoading = new Promise(function(resolve, reject) {
                    const script = document.createElement('script');
                    script.src =
                        'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js';
                    script.onload = resolve;
                    script.onerror = reject;
                    document.body.appendChild(script);
                });

                return barcodeScriptLoading;
            }

            function startBarcodeScanner() {
                loadBarcodeScannerLibrary().then(function() {
                    $('#barcode-scan-message').show().text('Starting camera...').css('color', '');
                    $('#barcode-qr-reader').html('').css('min-height', '300px');

                    try {
                        barcodeScanner = new Html5Qrcode("barcode-qr-reader");
                    } catch (error) {
                        console.error('Failed to initialize barcode scanner:', error);
                        showManualBarcodeInput();
                        bindManualBarcodeEvents();
                        return;
                    }

                    const config = {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    };

                    Html5Qrcode.getCameras().then(function(devices) {
                        if (!devices || !devices.length) {
                            stopBarcodeScanner();
                            showManualBarcodeInput();
                            bindManualBarcodeEvents();
                            return;
                        }

                        let cameraId = null;

                        for (let i = 0; i < devices.length; i++) {
                            const label = (devices[i].label || '').toLowerCase();
                            if (label.includes('back') || label.includes('rear')) {
                                cameraId = devices[i].id;
                                break;
                            }
                        }

                        if (!cameraId) {
                            cameraId = devices[0].id;
                        }

                        barcodeScanner.start(
                            cameraId,
                            config,
                            onBarcodeScanSuccess,
                            onBarcodeScanError
                        ).then(function() {
                            $('#barcode-scan-message').show().text('Point camera at barcode').css(
                                'color',
                                'green');
                        }).catch(function(error) {
                            console.error('Camera start failed:', error);
                            stopBarcodeScanner();
                            $('#barcode-scan-message').show().text(
                                'Unable to access camera. You can enter barcode manually below.'
                            ).css('color', 'red');
                            showManualBarcodeInput();
                            bindManualBarcodeEvents();
                        });
                    }).catch(function(error) {
                        console.error('Camera detection failed:', error);
                        stopBarcodeScanner();
                        $('#barcode-scan-message').show().text(
                            'Unable to access camera. You can enter barcode manually below.'
                        ).css('color', 'red');
                        showManualBarcodeInput();
                        bindManualBarcodeEvents();
                    });
                }).catch(function() {
                    $('#barcode-scan-message').show().text(
                        'Scanner library failed to load. Enter barcode manually below.'
                    ).css('color', 'red');
                    showManualBarcodeInput();
                    bindManualBarcodeEvents();
                });
            }

            $('#openBarcodeScanner').on('click', function() {
                $('#barcodeScannerModal').modal('show');
            });

            $('#barcode').on('change', function() {
                const barcode = $(this).val().trim();

                if (!barcode) {
                    return;
                }
                $('#barcode').val(barcode);
            });

            $('#barcodeScannerModal').on('shown.bs.modal', function() {
                $('#barcode-qr-reader').html('').css('min-height', '300px');
                $('#barcode-scan-message').show().text('Initializing camera...').css('color', '');
                startBarcodeScanner();
            });

            $('#barcodeScannerModal').on('hidden.bs.modal', function() {
                stopBarcodeScanner();
                $('#barcode-qr-reader').html('').css('min-height', '300px');
                $('#barcode-scan-message').show().text('Initializing camera...').css('color', '');
            });

            get_brand();
            get_category();
            get_units();
            // get_product_type();
        });
        $(document).ready(function() {
            const sub_branch_id = localStorage.getItem('selectedSubAdminId');
            var authToken = localStorage.getItem("authToken");

            function get_units(selectedId = null) {
                const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                const authToken = localStorage.getItem("authToken");
                let url = sub_branch_id ?
                    `/api/get-units?sub_branch_id=${sub_branch_id}` :
                    `/api/get-units`;
                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let select = $("#unit_id");
                            select.empty().append('<option value="">Choose Unit</option>');

                            $.each(response.data, function(key, item) {
                                select.append(
                                    $('<option>', { value: item.id, text: item.unit_name }).data('from-api', true)
                                );
                            });

                            if (selectedId) {
                                select.val(selectedId).trigger('change');
                            }

                            // Initialize Select2
                            if (!select.hasClass('select2-hidden-accessible')) {
                                select.select2({
                                    placeholder: "Select or Add Unit",
                                    tags: true,
                                    createTag: createTagOnlyWhenNew('#unit_id'),
                                    width: '100%',
                                    allowClear: true,
                                });
                            }
                        } else {
                            console.warn("No Units found.");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching Units:", xhr.responseText);
                    }
                });
            }
            // 🔹 Fetch Brands
            function get_brand(selectedId = null) {
                let url = sub_branch_id ?
                    `/api/get-brand?sub_branch_id=${sub_branch_id}` :
                    `/api/get-brand`;

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let select = $("#brand_id");
                            select.empty().append('<option value="">Choose Brand</option>');

                            $.each(response.data, function(key, item) {
                                select.append(
                                    $('<option>', { value: item.id, text: item.name }).data('from-api', true)
                                );
                            });

                            if (selectedId) {
                                select.val(selectedId).trigger('change');
                            }
                        } else {
                            console.warn("No Brand found.");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching Brand:", xhr.responseText);
                    }
                });
            }

            // 🔹 Fetch Categories
            function get_category(selectedId = null) {
                let url = sub_branch_id ?
                    `/api/get-category?sub_branch_id=${sub_branch_id}` :
                    `/api/get-category`;

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let select = $("#category_id");
                            select.empty().append('<option value="">Choose Category</option>');

                            $.each(response.data, function(key, item) {
                                select.append(
                                    $('<option>', { value: item.id, text: item.name }).data('from-api', true)
                                );
                            });

                            if (selectedId) {
                                select.val(selectedId).trigger('change');
                            }
                        } else {
                            console.warn("No Category found.");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching Category:", xhr.responseText);
                    }
                });
            }

            // 🔹 Select2 Init
            $('#brand_id').select2({
                placeholder: "Select or Add Brand",
                tags: true,
                createTag: createTagOnlyWhenNew('#brand_id'),
                width: '100%',
                allowClear: true,
            });

            $('#category_id').select2({
                placeholder: "Select or Add Category",
                tags: true,
                createTag: createTagOnlyWhenNew('#category_id'),
                width: '100%',
                allowClear: true,
            });

            $('#product_type_id').select2({
                placeholder: "Select or Add Product Type",
                tags: true,
                createTag: createTagOnlyWhenNew('#product_type_id'),
                width: '100%',
                allowClear: true,
            });

            $('#unit_id').select2({
                placeholder: "Select or Add Unit",
                tags: true,
                createTag: createTagOnlyWhenNew('#unit_id'),
                width: '100%',
                allowClear: true,
            });

            // 🔹 Modal Logic
            window.modalOpen = false;
            window.currentCustomValue = null;
            window.currentType = null;

            function openCustomModal(type, value = null, name = '') {
                modalOpen = true;
                currentType = type; // brand, category, product_type
                currentCustomValue = value; // temporary value if editing
                $('#customModalLabel').text("Add New " + capitalizeFirstLetter(type));
                $('#custom_name').val(name);
                $(".error_model").text('');
                $('#customModal').modal('show');
            }

            $(document).on('click', '#openCategoryModal, #openBrandModal, #openUnitModal', function() {
                if (modalOpen) return;

                const buttonId = $(this).attr('id');
                const typeMap = {
                    openCategoryModal: 'category',
                    openBrandModal: 'brand',
                    openUnitModal: 'unit'
                };

                openCustomModal(typeMap[buttonId] || '');
            });

            // Capitalize helper
            function capitalizeFirstLetter(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // Trigger modal on select2 custom option
            $('#brand_id, #category_id, #product_type_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                // Check via data-from-api marker instead of regex
                const $opt = $(this).find(`option[value="${value}"]`);
                const isCustom = !$opt.data('from-api');

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    let type = '';
                    if ($(this).attr('id') === 'brand_id') type = 'brand';
                    else if ($(this).attr('id') === 'category_id') type = 'category';
                    else if ($(this).attr('id') === 'product_type_id') type = 'product_type';

                    openCustomModal(type, value, name);
                }
            });
            // Brand event
            $('#brand_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "brand";
                    $('#customModalLabel').text("Add New Brand");
                    $('#custom_name').val(name);
                    $('#customModal').modal('show');
                }
            });

            // Category event
            $('#category_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "category";
                    $('#customModalLabel').text("Add New Category");
                    $('#custom_name').val(name);
                    $('#customModal').modal('show');
                }
            });

            // Product Type event
            $('#product_type_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "product_type";
                    $('#customModalLabel').text("Add New Product Type");
                    $('#custom_name').val(name);
                    $('#customModal').modal('show');
                }
            });

            $('#unit_id').on('select2:select', function(e) {
                if (modalOpen) return;

                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isCustomSelectValue(value);

                if (isCustom) {
                    if (selectExistingOptionIfDuplicateTag($(this), name)) return;

                    modalOpen = true;
                    currentCustomValue = value;
                    currentType = "unit";
                    $('#customModalLabel').text("Add New Unit");
                    $('#custom_name').val(name);
                    $(".error_model").text('');
                    $('#customModal').modal('show');
                }
            });

            // 🔹 Save custom item from modal

            // Reset flag when modal closes
            $('#customModal').on('hidden.bs.modal', function() {
                modalOpen = false;
                currentCustomValue = null;
                currentType = null;
                $(".error_custom_name").text('');
            });

            // 🔹 GST Option Change Handler
            $(document).on('change', '#gst_option', function() {
                const gstOption = $(this).val();
                const gstContainer = $('#gst_dropdown_container');

                if (gstOption === 'with_gst') {
                    gstContainer.show();
                    fetch_gst_rates();
                    $('.error_product_gst').text('');
                } else {
                    gstContainer.hide();
                    $('#product_gst').val(null).trigger('change');
                    $('.error_product_gst').text('');
                }
            });

            // 🔹 Fetch GST Rates
            function fetch_gst_rates() {
                const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                let url = sub_branch_id ?
                    `/api/get-tax-rates?sub_branch_id=${sub_branch_id}` :
                    `/api/get-tax-rates`;

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let select = $("#product_gst");
                            select.empty();

                            $.each(response.data, function(key, item) {
                                select.append(
                                    `<option value="${item.id}">${item.tax_name} (${item.tax_rate}%)</option>`
                                );
                            });

                            if (select.hasClass('select2-hidden-accessible')) {
                                select.trigger('change');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching GST rates:", xhr.responseText);
                    }
                });
            }

            // IMEI / Serial Number Handling
            function updateSerialUI() {
                let categorySelect = $('#category_id');
                if(!categorySelect.length) return;
                let categoryText = categorySelect.find('option:selected').text().trim().toLowerCase();
                let serialContainer = $('.serial-no-container');
                let qty = parseFloat($('#quantity').val()) || 0;
                let imeiInput = $('#imei_no');

                if (categoryText.includes('mobile')) {
                    serialContainer.show();
                    let existingSerials = [];
                    try {
                        let val = imeiInput.val();
                        existingSerials = val ? JSON.parse(val) : [];
                    } catch(e) {}
                    let count = existingSerials.length;
                    serialContainer.find('.serial-status').text(`${count}/${qty} IMEI numbers added`);
                    if (count < qty && qty > 0) {
                        serialContainer.find('.serial-status').removeClass('text-success').addClass('text-danger');
                    } else {
                        serialContainer.find('.serial-status').removeClass('text-danger').addClass('text-success');
                    }
                } else {
                    serialContainer.hide();
                    imeiInput.val('[]');
                }
            }

            $(document).on('click', '.edit-serial-btn', function() {
                let qty = parseFloat($('#quantity').val()) || 0;
                
                if (qty <= 0) {
                    alert("Please enter a valid quantity first.");
                    return;
                }

                let existingSerials = [];
                try {
                    let val = $('#imei_no').val();
                    existingSerials = val ? JSON.parse(val) : [];
                } catch(e) {}

                let modalBody = $('#serialNumberModalBody');
                modalBody.empty();

                for (let i = 0; i < qty; i++) {
                    let val = existingSerials[i] || '';
                    modalBody.append(`
                        <div class="form-group mb-2">
                            <label>IMEI No ${i + 1}</label>
                            <input type="text" class="form-control serial-input-item" value="${val}" placeholder="Enter IMEI / Serial No">
                        </div>
                    `);
                }

                $('#serialNumberModal').modal('show');
            });

            $(document).on('click', '#saveSerialNumbersBtn', function() {
                let serials = [];
                $('#serialNumberModalBody .serial-input-item').each(function() {
                    let val = $(this).val().trim();
                    if (val !== '') {
                        serials.push(val);
                    }
                });

                $('#imei_no').val(JSON.stringify(serials));
                updateSerialUI();
                $('#serialNumberModal').modal('hide');
            });

            $(document).on('change', '#category_id', function() {
                updateSerialUI();
            });

            $(document).on('input change', '#quantity', function() {
                updateSerialUI();
            });

            // ✅ Page load par call karo
            get_brand();
            get_category();
            get_units();
            updateSerialUI();
        });
    </script>
@endpush
