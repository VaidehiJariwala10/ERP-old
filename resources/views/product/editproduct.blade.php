@extends('layout.app')

@section('title', 'Product Edit')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .select2-container {
                width: 0 !important;
            }

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
    </style>
    <div class="content">
        {{-- <div class="page-header">
            <div class="page-title">
                <h4>Edit Product</h4>
            </div>
        </div> --}}
        <div class="page-header ">
            <div class="page-title">
                <h4>Edit Product</h4>
            </div>
             <div class="back-button">
                <a href="{{ route('product.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i> Back</a></br>
                            <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="productForm">
                    <input type="hidden" name="product_id" id="product_id">
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
                                <input type="number" name="SKU" id="SKU" class="form-control" step="1"
                                    min="0">
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
                                    <label class="form-label-icon mb-0"><i class="fa-solid fa-ruler-combined"></i> Unit</label>
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
                                <input type="number" name="quantity" id="quantity" class="form-control" step="1"
                                    min="0">
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
                                <input type="number" name="price" id="price" class="form-control" step="0.01"
                                    min="0">
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
                                <input type="text" name="barcode" id="barcode" class="form-control">
                                <span class="error_barcode text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-align-left"></i> Description</label>
                                <textarea class="form-control" name="description" id="description"></textarea>
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
                            <a href="{{ route('product.list') }}" class="btn btn-cancel">Cancel</a><br>
                            <span class="success_submit text-danger"></span>
                        </div>
                    </div>
                </form>

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
            const IMAGE_PATH = "{{ rtrim(env('ImagePath'), '/') }}";
        </script>

        <script>
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
            $(document).on("click", ".remove-image", function(e) {
                e.preventDefault(); // ❗ prevent page reload and form submission

                var authToken = localStorage.getItem("authToken");
                var productId = $(this).data("id");
                var imageName = $(this).data("image");
                var imageContainer = $(this).parent();

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you really want to delete this image?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel",
                    confirmButtonColor: "#ff9f43",
                    cancelButtonColor: "#6c757d"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/api/remove-product-image",
                            type: "POST",
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            data: {
                                product_id: productId,
                                image: imageName,
                                _token: $('meta[name="csrf-token"]').attr("content")
                            },
                            success: function(response) {
                                if (response.success) {
                                    imageContainer.remove();
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: "Product image removed successfully!",
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43"
                                    }).then(() => {

                                    });
                                } else {
                                    Swal.fire("Error", "Error removing image.", "error");
                                }
                            },
                            error: function() {
                                Swal.fire("Error", "Something went wrong.", "error");
                            }
                        });
                    }
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
                    $('.error_name, .error_category_id, .error_brand_id, .error_SKU, .error_hsn_code, .error_gst_option, .error_product_gst, .error_quantity, .error_unit_id, .error_price, .error_status, .error_barcode, .error_description, .error_availablility, .error_images, .error_imei_no')
                        .text('');

                    $.ajax({
                        url: "/api/updateProduct",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        success: function(response) {
                            $btn.html(originalText).prop('disabled', false);

                            if (response.status) {
                                Swal.fire({
                                    title: "Success!",
                                    text: "Product updated successfully!",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43" // your custom color
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                        "{{ route('product.list') }}";
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            $btn.html(originalText).prop('disabled', false);

                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    let errorKey = key.split('.')[0];
                                    $('.error_' + errorKey).text(value[0]);
                                });
                            } else {
                                Swal.fire("Error", "Something went wrong.", "error");
                            }
                        }
                    });
                });
            });

            $(document).ready(function() {
                const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                var authToken = localStorage.getItem("authToken");
                let url = window.location.pathname;
                let productId = url.split("/").pop(); // e.g. /product/edit/5 -> 5

                function get_unit(selectedId = null) {
                    let url = sub_branch_id ?
                        `/api/get-units?sub_branch_id=${sub_branch_id}` :
                        `/api/get-units`;

                    $.ajax({
                        url: url,
                        type: "GET",
                        headers: {
                            "Authorization": "Bearer " + authToken
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
                            }
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
                            "Authorization": "Bearer " + authToken
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
                            }
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
                            "Authorization": "Bearer " + authToken
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
                            }
                        }
                    });
                }

                // 🔹 Select2 Init
                $('#brand_id').select2({
                    placeholder: "Select or Add Brand",
                    tags: true,
                    createTag: createTagOnlyWhenNew('#brand_id'),
                    width: '100%',
                    allowClear: true
                });
                $('#category_id').select2({
                    placeholder: "Select or Add Category",
                    tags: true,
                    createTag: createTagOnlyWhenNew('#category_id'),
                    width: '100%',
                    allowClear: true
                });
                $('#product_type_id').select2({
                    placeholder: "Select or Add Product Type",
                    tags: true,
                    createTag: createTagOnlyWhenNew('#product_type_id'),
                    width: '100%',
                    allowClear: true
                });
                $('#unit_id').select2({
                    placeholder: "Select or Add Unit",
                    tags: true,
                    createTag: createTagOnlyWhenNew('#unit_id'),
                    width: '100%',
                    allowClear: true
                });

                // 🔹 Load Product and prefill form
                $.ajax({
                    url: `/api/edit_product/${productId}`,
                    method: 'GET',
                    headers: {
                        Authorization: `Bearer ${authToken}`
                    },
                    success: function(response) {
                        if (!response.status) {
                            alert("Product not found!");
                            return;
                        }

                        const product = response.product;

                        // // console.log("Fetched product data:", product);

                        // ✅ Populate form fields
                        $('#product_id').val(product.id);
                        $('#expiry_date').val(product.expiry_date);
                        $('#name').val(product.name);
                        $('#SKU').val(product.SKU);
                        $('#hsn_code').val(product.hsn_code);
                        $('#quantity').val(product.quantity);
                        $('#price').val(product.price);
                        $('#barcode').val(product.barcode);
                        $('#description').val(product.description);
                        $('#availablility').val(product.availablility).trigger('change');
                        $('#status').val(product.status).trigger('change');
                        $('#warranty').val(product.warranty).trigger('change');
                        $('#voltage').val(product.voltage).trigger('change');
                        $('#capacity').val(product.capacity).trigger('change');
                        $('#gst_option').val(product.gst_option || 'without_gst').trigger('change');
                        // ✅ New fields from Excel import
                        $('#cost_price').val(product.cost_price);
                        $('#landing_cost').val(product.landing_cost);
                        $('#product_code').val(product.product_code);
                        $('#supplier_code').val(product.supplier_code);
                        $('#international_code').val(product.international_code);
                        $('#serial_no_status').val(product.serial_no_status || '').trigger('change');
                        $('#non_inventory_type').val(product.non_inventory_type);
                        $('#stock_validation_status').val(product.stock_validation_status || '').trigger('change');
                        $('#item_type').val(product.item_type || '').trigger('change');
                        $('#discount_print_status').val(product.discount_print_status || '').trigger('change');
                        $('#imei_no').val(product.imei_no || '[]');
                        if (product.item_created_on) {
                            $('#item_created_on').val(product.item_created_on.substring(0, 10));
                        }
                        // Clear old preview first
                        $('.image-preview').empty();

                        if (product.images) {
                            let images = JSON.parse(product.images);

                            images.forEach(function(img) {
                                $('.image-preview').append(`
                                <div class="image-container" style="position: relative; display: inline-block; margin:5px;">
                                    <img src="${IMAGE_PATH}/storage/${img}"
                                        style="width:100px;height:100px;object-fit:cover;border-radius:5px;">

                                    <button class="remove-image"
                                            data-id="${product.id}"
                                            data-image="${img}"
                                            style="
                                                position:absolute;
                                                top:-6px;
                                                right:-6px;
                                                background:red;
                                                color:white;
                                                border:none;
                                                border-radius:50%;
                                                width:22px;
                                                height:22px;
                                                cursor:pointer;
                                                font-size:14px;
                                            ">
                                        &times;
                                    </button>
                                </div>
                            `);
                            });
                        }
                        // ✅ Call dropdown loaders with preselected value
                        get_unit(product.unit_id);
                        get_brand(product.brand_id);
                        get_category(product.category_id);
                        // get_product_type(product.product_type_id);

                        // ✅ Existing images show
                        if (product.images) {
                            let images = JSON.parse(product.images);
                            let html = "";
                            images.forEach(function(img) {
                                html += `
                        <div class="image-container" data-image="${img}" style="position: relative;">
                            <img src="${IMAGE_PATH}/storage/${img}" width="100" height="100" style="border-radius: 5px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1);">
                            <button class="remove-image" data-id="${product.id}" data-image="${img}" style="
                                position: absolute;
                                top: -5px;
                                right: -5px;
                                background: red;
                                color: white;
                                border: none;
                                border-radius: 50%;
                                width: 20px;
                                height: 20px;
                                cursor: pointer;
                                font-size: 14px;
                            ">&times;</button>
                        </div>
                    `;
                            });
                            $(".existing-images").html(html).show();
                        }

                        // Load GST if with_gst is selected
                        if (product.gst_option === 'with_gst') {
                            let gstIds = [];
                            if (product.product_gst) {
                                let gstData = JSON.parse(product.product_gst);
                                gstIds = gstData.map(item => item.tax_id);
                            }
                            fetch_gst_rates_edit(gstIds);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        // window.location.href = "{{ route('product.list') }}";
                    }
                });

                let modalOpen = false;
                let currentType = null;
                let currentCustomValue = null;

                // 🔹 Add Category / Brand / Unit button click → open modal directly
                $(document).on('click', '#openCategoryModal, #openBrandModal, #openUnitModal', function() {
                    if (modalOpen) return;

                    const buttonId = $(this).attr('id');
                    const typeMap = {
                        openCategoryModal: 'category',
                        openBrandModal: 'brand',
                        openUnitModal: 'unit'
                    };

                    const type = typeMap[buttonId];
                    if (!type) return;

                    modalOpen = true;
                    currentType = type;
                    currentCustomValue = null;
                    $('#customModalLabel').text("Add New " + type.charAt(0).toUpperCase() + type.slice(1));
                    $('#custom_name').val('');
                    $(".error_custom_name").text('');
                    $(".error_model").text('');
                    $('#customModal').modal('show');
                });

                // 🔹 Listen for Select2 New Tag
                $('#brand_id, #category_id, #unit_id').on('select2:select', function(e) {
                    if (modalOpen) return;

                    let data = e.params.data;
                    let selectedValue = data.id;
                    let selectedText = data.text;
                    let targetId = $(this).attr('id');

                    // A value is "custom" (user-typed tag) if the option was NOT loaded from the API
                    const $opt = $(this).find(`option[value="${selectedValue}"]`);
                    const isNewTag = !$opt.data('from-api');

                    if (isNewTag) {
                        if (selectExistingOptionIfDuplicateTag($(this), selectedText)) return;

                        currentCustomValue = selectedValue;
                        modalOpen = true;

                        if (targetId === "brand_id") {
                            currentType = "brand";
                        } else if (targetId === "category_id") {
                            currentType = "category";
                        } else if (targetId === "unit_id") {
                            currentType = "unit";
                        }

                        // Open Modal
                        $('#customModalLabel').text("Add New " + currentType.charAt(0).toUpperCase() +
                            currentType.slice(1));
                        $('#custom_name').val(selectedText);
                        $(".error_model").text('');
                        $('#customModal').modal('show');
                    }
                });

                // Reset modal state when closed
                $('#customModal').on('hidden.bs.modal', function() {
                    modalOpen = false;
                    currentCustomValue = null;
                    currentType = null;
                    $(".error_custom_name").text('');
                });
                $('#saveCustomBtn').on('click', function() {
                    const name = $('#custom_name').val().trim();
                    let sub_admin_id = sub_branch_id;

                    $(".error_custom_name").text(""); // reset error

                    if (name === "") {
                        $(".error_custom_name").text("Please enter a name");
                        return;
                    }

                    let apiUrl = "";
                    let targetDropdown = "";
                    let postData = {
                        sub_admin_id: sub_admin_id,
                        name: name
                    };

                    if (currentType === "brand") {
                        apiUrl = "/api/addBrand";
                        targetDropdown = "#brand_id";
                    } else if (currentType === "category") {
                        apiUrl = "/api/addcategory";
                        targetDropdown = "#category_id";
                    } else if (currentType === "unit") {
                        apiUrl = "/api/add-units";
                        targetDropdown = "#unit_id";
                        postData = {
                            unitname: name,
                            selectedSubAdminId: sub_branch_id
                        };
                    }

                    if (!apiUrl) return;

                    // 🔹 Show loader
                    let btn = $("#saveCustomBtn");
                    btn.prop("disabled", true).html("Saving... <i class='fa fa-spinner fa-spin'></i>");

                    $.ajax({
                        url: apiUrl,
                        type: "POST",
                        data: postData,
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        success: function(response) {
                            let id = null;
                            let text = null;

                            if (currentType === "brand") {
                                let brandData = response.brand ?? response.data ?? null;
                                if (brandData) { id = brandData.id; text = brandData.name; }
                            } else if (currentType === "category") {
                                if (response.category) { id = response.category.id; text = response.category.name; }
                            } else if (currentType === "unit") {
                                let unitData = response.data ?? response.unit ?? null;
                                if (unitData) { id = unitData.id; text = unitData.unit_name ?? unitData.name ?? null; }
                            }

                            if (!id) {
                                $(".error_custom_name").text("Invalid response format");
                                btn.prop("disabled", false).text("Save");
                                return;
                            }

                            // remove temp option
                            if (currentCustomValue && !/^\d+$/.test(String(currentCustomValue))) {
                                $(targetDropdown).find(`option[value="${currentCustomValue}"]`).remove();
                            }

                            // add new option
                            if ($(targetDropdown).find(`option[value="${id}"]`).length === 0) {
                                $(targetDropdown).append(new Option(text, id, true, true));
                            }

                            // select new
                            $(targetDropdown).val(id).trigger('change');

                            // refresh dropdown data
                            if (currentType === "brand") {
                                get_brand(id);
                            } else if (currentType === "category") {
                                get_category(id);
                            } else if (currentType === "unit") {
                                get_unit(id);
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
                        error: function(xhr) {
                            let msg = "Error while saving " + currentType;
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    let firstKey = Object.keys(xhr.responseJSON.errors)[0];
                                    msg = xhr.responseJSON.errors[firstKey][0];
                                } else if (xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                            }
                            $(".error_custom_name").text(msg);
                            btn.prop("disabled", false).text("Save");
                            console.error(xhr.responseText);
                        }
                    });
                });

                // 🔹 GST Option Change Handler
                $(document).on('change', '#gst_option', function() {
                    const gstOption = $(this).val();
                    const gstContainer = $('#gst_dropdown_container');

                    if (gstOption === 'with_gst') {
                        gstContainer.show();
                        fetch_gst_rates_edit();
                        $('.error_product_gst').text('');
                    } else {
                        gstContainer.hide();
                        $('#product_gst').val(null).trigger('change');
                        $('.error_product_gst').text('');
                    }
                });

                // 🔹 Fetch GST Rates for Edit
                function fetch_gst_rates_edit(preSelectIds = []) {
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

                                if (preSelectIds.length > 0) {
                                    select.val(preSelectIds).trigger('change');
                                }

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

                // Trigger update on page load/ready
                setTimeout(updateSerialUI, 1000); // short delay to ensure select option is set and loaded
            });
        </script>
    @endpush
