@extends('admin.layouts.master')

@section('title')
Edit Product
@endsection

<style>
    .cke_notifications_area {
        display: none !important;
    }

    .cke_notification {
        display: none !important;
    }

    .input-error {
        border-color: #ef4444 !important;
    }

    .error-text {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
    }
</style>

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

<div class="bg-white p-6 rounded-lg shadow">

    <form id="productForm" action="{{ route('admin.product.update',encrypt($product->id)) }}" method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

            <!-- Category -->
            <div>
                <label class="block mb-1 font-medium">Category</label>

                <select name="category_id" id="category_id" class="border rounded w-full p-2">

                    <option value="">Select</option>

                    @foreach($categories as $category)

                    <option value="{{ $category->id }}" {{ $product->category_id==$category->id?'selected':'' }}>

                        {{ $category->name }}

                    </option>

                    @endforeach

                </select>
            </div>

            <!-- Sub Category -->
            <div>

                <label class="block mb-1 font-medium">Sub Category</label>

                <select name="sub_category_id" id="sub_category_id" class="border rounded w-full p-2">

                    <option value="">Select Sub Category</option>

                </select>

            </div>

            <!-- Name -->
            <div>
                <label class="block mb-1 font-medium">Name</label>

                <input type="text" name="name" id="name" value="{{ $product->name }}" class="border rounded w-full p-2">
            </div>

            <!-- Price -->
            <div>
                <label class="block mb-1 font-medium">Price</label>

                <input type="number" name="price" id="price" value="{{ $product->price }}"
                    class="border rounded w-full p-2">
            </div>

            <!-- BV -->
            <div>
                <label class="block mb-1 font-medium">BV</label>

                <input type="number" name="prod_bv" value="{{ $product->prod_bv }}" class="border rounded w-full p-2">
            </div>

            <!-- PV -->
            <div>
                <label class="block mb-1 font-medium">PV</label>

                <input type="number" name="prod_pv" value="{{ $product->prod_pv }}" class="border rounded w-full p-2">
            </div>

            <!-- HSN -->
            <div>
                <label class="block mb-1 font-medium">HSN</label>

                <select name="hsn_code" id="hsn_code" class="border rounded w-full p-2">

                    <option value="">Select HSN Code</option>

                    @foreach($hsnList as $hsn)

                    <option value="{{ $hsn->id }}" data-gst="{{ $hsn->iGst }}" {{ $product->sku==$hsn->id?'selected':''
                        }}>

                        {{ $hsn->hsnCode }} - {{ $hsn->description }}

                    </option>

                    @endforeach

                </select>

            </div>
            
<!--            <div>-->
<!--    <label class="block mb-1 font-medium">HSN</label>-->

<!--    <select name="hsn_code" id="hsn_code" class="border rounded w-full p-2">-->
<!--        <option value="">Select HSN Code</option>-->

<!--        @foreach($hsnList as $hsn)-->
<!--        <option value="{{ $hsn->id }}" -->
<!--            data-gst="{{ $hsn->iGst }}"-->
<!--            {{ $product->sku == $hsn->id ? 'selected' : '' }}>-->
<!--            {{ $hsn->hsnCode }} - {{ $hsn->description }}-->
<!--        </option>-->
<!--        @endforeach-->

<!--    </select>-->
<!--</div>-->

            <!-- GST -->
            <div>
                <label class="block mb-1 font-medium">GST</label>

                <input type="number" id="gst" readonly class="border rounded w-full p-2">
            </div>

            <!-- Stock -->
            <div>
                <label class="block mb-1 font-medium">Stock</label>

                <input type="number" name="stock" value="{{ $product->stock }}" class="border rounded w-full p-2">
            </div>

            <!-- Status -->
            <div>

                <label class="block mb-1 font-medium">Status</label>

                <select name="status" class="border rounded w-full p-2">

                    <option value="1" {{ $product->status==1?'selected':'' }}>
                        Active
                    </option>

                    <option value="0" {{ $product->status==0?'selected':'' }}>
                        Inactive
                    </option>

                </select>

            </div>

        </div>

        <br>

        <!-- Description -->

        <label class="font-medium">Description</label>

        <textarea name="description" id="description">

{!! $product->description !!}

</textarea>

        <br>
<input type="hidden" name="deleted_images" id="deleted_images">
        <!-- Existing Images -->

<label class="font-medium">Existing Images</label>

<div class="flex flex-wrap gap-3 mt-3">

@php
$images = json_decode($product->image,true);
$imageCount = count($images);
@endphp

@foreach($images as $img)

<div class="relative existingImage">

<img src="{{ asset('uploads/products/'.$img) }}"
class="w-20 h-20 object-cover rounded">

<button type="button"
class="deleteImage absolute top-0 right-0 bg-red-500 text-white px-2 rounded"
data-image="{{ $img }}">

x

</button>

</div>

@endforeach

</div>

        <br>

        <!-- Add New Images -->

        <label class="font-medium">Add New Images</label>

        <table class="w-full border rounded-lg mt-2">

            <thead class="bg-gray-100">

                <tr>

                    <th class="p-2 text-left">Preview</th>

                    <th class="p-2 text-left">Image</th>

                    <th class="p-2 text-center">Action</th>

                </tr>

            </thead>

            <tbody id="imageBody">

                <tr>

                    <td class="p-2">
                        <img src="" class="h-14 hidden preview">
                    </td>

                    <td class="p-2">

                        <input type="file" name="images[]" class="imageInput border rounded px-3 py-2 w-full"
                            accept=".jpg,.jpeg,.png,.webp">

                    </td>

                    <td class="p-2 text-center">

                        <button type="button" class="addRow bg-green-500 text-white px-3 py-1 rounded">

                            +

                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

        <br>

        <!--<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">-->

        <!--    Update Product-->

        <!--</button>-->
        <button id="submitBtn" type="submit"
class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded flex items-center gap-2">

<span id="btnText">Update Product</span>

<span id="btnLoader" class="hidden animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>

</button>

    </form>

</div>

<!-- Crop Modal -->
<div id="cropModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999; align-items:center; justify-content:center;">
    
    <div style="background:#fff; padding:20px; width:600px; border-radius:8px;">
        
        <h3 style="font-weight:bold; margin-bottom:10px;">Crop Image</h3>

        <div style="max-height:400px;">
            <img id="cropImage" style="max-width:100%;">
        </div>

        <div style="margin-top:15px; text-align:right;">
            <button id="cropCancel" class="bg-gray-500 text-white px-4 py-2 rounded">
                Cancel
            </button>

            <button id="cropSave" class="bg-blue-600 text-white px-4 py-2 rounded">
                Crop & Use
            </button>
        </div>

    </div>
</div>
<!-- JS -->

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
$(document).ready(function () {

    $('#hsn_code').select2({
        placeholder: "Search HSN Code",
        allowClear: true,
        width: '100%'
    });

});
</script>
<script>
let existingImageCount = {{ $imageCount }};
let deletedImages = [];
</script>
<script>

    CKEDITOR.replace('description');

</script>


<script>

    /* Category -> SubCategory */

    let selectedSubCategory = "{{ $product->sub_category_id }}";

    function loadSubCategories(category_id, selectedSub = null) {

        if (!category_id) return;

        $.ajax({

            url: "{{ route('get.subcategories','') }}/" + category_id,

            type: "GET",

            success: function (data) {

                $('#sub_category_id').html('<option value="">Select</option>');

                $.each(data, function (key, value) {

                    let selected = (selectedSub == value.id) ? 'selected' : '';

                    $('#sub_category_id').append(

                        '<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>'

                    );

                });

            }

        });

    }

    $('#category_id').change(function () {

        loadSubCategories($(this).val());

    });

    $(document).ready(function () {

        let category_id = $('#category_id').val();

        if (category_id) {

            loadSubCategories(category_id, selectedSubCategory);

        }

    });
    
    
    
    let cropper;
let currentInput;

$(document).on("change", ".imageInput", function () {

    let file = this.files[0];
    if (!file) return;

    currentInput = this;

    let reader = new FileReader();

    reader.onload = function (e) {

        $("#cropImage").attr("src", e.target.result);
        $("#cropModal").css("display","flex");

        if (cropper) {
            cropper.destroy();
        }

        cropper = new Cropper(document.getElementById('cropImage'), {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1
        });

    };

    reader.readAsDataURL(file);

});


$("#cropSave").click(function(){

    let canvas = cropper.getCroppedCanvas({
        width:800,
        height:800
    });

    canvas.toBlob(function(blob){

        let file = new File([blob], "cropped.png", {type:"image/png"});

        let dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);

        currentInput.files = dataTransfer.files;

        let preview = $(currentInput).closest("tr").find(".preview");

        preview.attr("src", URL.createObjectURL(blob)).removeClass("hidden");

        $("#cropModal").hide();
        cropper.destroy();

    });

});

</script>

<script>

    /* HSN GST */

    $('#hsn_code').change(function () {

        let gst = $(this).find(':selected').data('gst');

        $('#gst').val(gst);

    }).trigger('change');

</script>

<script>

    /* Add Image Row */

/* IMAGE LIMIT CONTROL */

function getTotalImages(){

    let newRows = $("#imageBody tr").length;

    let deletedCount = deletedImages.length;

    return (existingImageCount - deletedCount) + newRows;

}


/* ADD IMAGE ROW */

$(document).on("click", ".addRow", function () {

    let fileInput = $(this).closest("tr").find(".imageInput")[0];

    if(fileInput.files.length === 0){
        toastr.error("Please select an image first");
        return;
    }

    if(getTotalImages() >= 4){
        toastr.error("Maximum 4 images allowed");
        return;
    }

    let row = `
<tr>

<td class="p-2">
<img src="" class="h-14 hidden preview">
</td>

<td class="p-2">
<input type="file"
name="images[]"
class="imageInput border rounded px-3 py-2 w-full"
accept=".jpg,.jpeg,.png,.webp">
</td>

<td class="p-2 text-center">
<button type="button"
class="removeRow bg-red-500 text-white px-3 py-1 rounded">

Remove

</button>
</td>

</tr>
`;

    $("#imageBody").append(row);

});


/* REMOVE NEW IMAGE ROW */

$(document).on("click", ".removeRow", function () {

    $(this).closest("tr").remove();

});


/* DELETE EXISTING IMAGE */

$(document).on("click", ".deleteImage", function () {

    let image = $(this).data("image");

    if(confirm("Delete image?")){

        deletedImages.push(image);

        $("#deleted_images").val(JSON.stringify(deletedImages));

        $(this).closest(".existingImage").remove();

        existingImageCount--;

        toastr.warning("Image will be deleted after update");

    }

});

$(document).ready(function(){

    if(existingImageCount >= 4){

        $(".imageInput").prop("disabled", true);

        $(".addRow").prop("disabled", true);

        toastr.warning("Maximum 4 images already exist");

    }

});
    /* Remove Row */

    $(document).on("click", ".removeRow", function () {

        $(this).closest("tr").remove();

    });


    /* Image Preview */

    $(document).on("change", ".imageInput", function () {

        let file = this.files[0];

        if (!file) return;

        let reader = new FileReader();

        let preview = $(this).closest("tr").find(".preview");

        reader.onload = function (e) {

            preview.attr("src", e.target.result).removeClass("hidden");

        };

        reader.readAsDataURL(file);

    });

</script>





<script>

    /* Submit */

$("#productForm").submit(function (e) {

    e.preventDefault();

    let formData = new FormData(this);

    formData.set(
        "description",
        CKEDITOR.instances.description.getData()
    );

    formData.append(
        "deleted_images",
        $("#deleted_images").val()
    );


    $("#submitBtn").prop("disabled", true);
    $("#btnText").text("Updating...");
    $("#btnLoader").removeClass("hidden");


    $.ajax({

        url: $(this).attr("action"),

        type: "POST",

        data: formData,

        processData: false,

        contentType: false,

        success: function(res){

            if(res.status){

                toastr.success(res.message);

                setTimeout(function(){

                    window.location.href="{{ route('admin.product.list') }}";

                },1500);

            }else{

                enableButton();

                toastr.error(res.message);

            }

        },

        error:function(){

            enableButton();

            toastr.error("Something went wrong");

        }

    });

});


function enableButton(){

    $("#submitBtn").prop("disabled",false);

    $("#btnText").text("Update Product");

    $("#btnLoader").addClass("hidden");

}

</script>

@endsection