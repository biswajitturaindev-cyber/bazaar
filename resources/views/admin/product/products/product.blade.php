@extends('admin.layouts.master')

@section('title')
Add Product
@endsection

@section('breadcrumb')
Product
@endsection

<style>
.cke_notifications_area{display:none!important;}
.cke_notification{display:none!important;}

.input-error{
border-color:#ef4444 !important;
}
.error-text{
color:#ef4444;
font-size:12px;
margin-top:4px;
}

</style>

@section('content')

<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
<div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

<div class="flex justify-between items-center p-5 border-b">
<h2 class="text-lg font-semibold">Add Product</h2>

<a href="{{ route('admin.product.list') }}"
class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
Back
</a>
</div>

<div class="p-5">

<form id="productForm" action="{{ route('admin.product.add') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="grid grid-cols-1 md:grid-cols-4 gap-5">

<!-- Category -->
<div>
<label class="block mb-2 text-sm font-medium">Category</label>

<select name="category_id" id="category_id" class="w-full border rounded-lg px-3 py-2">
<option value="">Select Category</option>

@foreach($categories as $category)
<option value="{{ $category->id }}">{{ $category->name }}</option>
@endforeach

</select>
</div>

<!-- Subcategory -->
<div>
<label class="block mb-2 text-sm font-medium">Sub Category</label>

<select name="sub_category_id" id="sub_category_id" class="w-full border rounded-lg px-3 py-2">
<option value="">Select Sub Category</option>
</select>
</div>

<!-- Product Name -->
<div>
<label class="block mb-2 text-sm font-medium">Product Name</label>

<input type="text"
name="name"
id="name"
class="w-full border rounded-lg px-3 py-2"
placeholder="Enter product name">
</div>

<!-- Price -->
<div>
<label class="block mb-2 text-sm font-medium">Price</label>

<input type="number"
step="0.01"
name="price"
id="price"
class="w-full border rounded-lg px-3 py-2"
placeholder="Enter price">
</div>

<!-- BV -->
<div>
<label class="block mb-2 text-sm font-medium">Product BV</label>

<input type="number"
name="prod_bv"
id="prod_bv"
class="w-full border rounded-lg px-3 py-2"
placeholder="Enter BV">
</div>

<!-- PV -->
<div>
<label class="block mb-2 text-sm font-medium">Product PV</label>

<input type="number"
name="prod_pv"
id="prod_pv"
class="w-full border rounded-lg px-3 py-2"
placeholder="Enter PV">
</div>

<!-- HSN -->
<div>
<label class="block mb-2 text-sm font-medium">HSN Code</label>

<select name="hsn_code" id="hsn_code" class="w-full border rounded-lg px-3 py-2">
<option value="">Select HSN Code</option>

@foreach($hsnList as $hsn)
<option value="{{ $hsn->id }}"
data-gst="{{ $hsn->iGst }}">
{{ $hsn->hsnCode }} - {{ $hsn->description }}
</option>
@endforeach

</select>
</div>

<!-- GST -->
<div>
<label class="block mb-2 text-sm font-medium">GST</label>

<input type="number"
name="gst"
id="gst"
readonly
class="w-full border rounded-lg px-3 py-2">
</div>

<!-- Stock -->
<div>
<label class="block mb-2 text-sm font-medium">Stock</label>

<input type="number"
name="stock"
id="stock"
class="w-full border rounded-lg px-3 py-2"
placeholder="Enter stock quantity">
</div>

<!-- Image -->
<div class="md:col-span-4">

<label class="block mb-2 text-sm font-medium">Product Images</label>

<table class="w-full border rounded-lg" id="imageTable">
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
<input type="file"
name="images[]"
class="imageInput border rounded px-3 py-2 w-full"
accept=".jpg,.jpeg,.png,.webp">
</td>

<td class="p-2 text-center">
<button type="button"
class="addRow bg-green-500 text-white px-3 py-1 rounded">+</button>
</td>

</tr>

</tbody>
</table>

</div>

<!-- Status -->
<div>
<label class="block mb-2 text-sm font-medium">Status</label>

<select name="status" id="status" class="w-full border rounded-lg px-3 py-2">
<option value="1">Active</option>
<option value="0">Inactive</option>
</select>
</div>

<!-- Description -->
<div class="md:col-span-4">

<label class="block mb-2 text-sm font-medium">Description</label>

<textarea
name="description"
id="description"
rows="4"
class="w-full border rounded-lg px-3 py-2"
placeholder="Enter description"></textarea>

</div>

</div>

<div class="mt-6">
<button type="submit"
class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
Save Product
</button>
</div>

</form>

</div>
</div>
</div>
<!-- Crop Modal -->
<div id="cropModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    
    <div style="background:#fff; padding:20px; width:500px; border-radius:10px;">
        
        <h3 class="text-lg font-semibold mb-3">Crop Image</h3>

        <div>
            <img id="cropImage" style="max-width:100%;">
        </div>

        <div class="mt-4 flex justify-end gap-3">
            <button id="cropCancel" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
            <button id="cropSave" class="bg-blue-600 text-white px-4 py-2 rounded">Crop & Save</button>
        </div>

    </div>

</div>
<!-- jQuery -->

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
CKEDITOR.replace('description');
</script>
<script>
$(document).ready(function() {
    $('#hsn_code').select2({
        placeholder: "Search HSN Code",
        allowClear: true,
        width: '100%'
    });
});
</script>
<script>

/* HSN -> GST */

$('#hsn_code').change(function(){

let gst = $(this).find(':selected').data('gst');

$('#gst').val(gst ? gst : '');

});

/* Category -> Subcategory */

$('#category_id').change(function(){

let category_id = $(this).val();

if(category_id){

$.ajax({

url:"{{ route('get.subcategories','') }}/"+category_id,

type:"GET",

success:function(data){

$('#sub_category_id').html('<option value="">Select Sub Category</option>');

$.each(data,function(key,value){

$('#sub_category_id').append(

'<option value="'+value.id+'">'+value.name+'</option>'

);

});

}

});

}

});

/* Validation */

function validate(value,title,rules=[]){

for(let rule of rules){

let [type,param] = rule.split(":");

switch(type){

case "required":

if(!value || value.trim()===""){
return title+" is required";
}

break;

case "min":

if(value.length < param){
return title+" must be at least "+param+" characters";
}

break;

case "number":

if(!/^[0-9]+(\.[0-9]+)?$/.test(value)){
return title+" must be numeric";
}

break;

}

}

return null;

}

const rules = {

category_id:{title:"Category",rules:["required"]},
sub_category_id:{title:"Sub Category",rules:["required"]},
name:{title:"Product Name",rules:["required","min:3"]},
price:{title:"Price",rules:["required","number"]},
prod_bv:{title:"Product BV",rules:["required","number"]},
prod_pv:{title:"Product PV",rules:["required","number"]},
hsn_code:{title:"HSN Code",rules:["required"]},
stock:{title:"Stock",rules:["required","number"]},
description:{title:"Description",rules:["required","min:5"]}

};

$(document).on("blur","input,select",function(){

    let id=$(this).attr("id");

    if(!rules[id]) return;

    let value=$(this).val().trim();

    let config=rules[id];

    let error=validate(value,config.title,config.rules);

    if(error){

        showError(id,error);

    }else{

        clearError(id);

    }

});

function showError(id,message){

    let input=$("#"+id);

    input.addClass("input-error");

    if($("#"+id+"_error").length===0){

        input.after('<div id="'+id+'_error" class="error-text"></div>');

    }

    $("#"+id+"_error").text(message);

}

function clearError(id){

    $("#"+id).removeClass("input-error");

    $("#"+id+"_error").remove();

}

toastr.options = {
closeButton: true,
progressBar: true,
positionClass: "toast-top-right",
timeOut: 3000
};


$("#productForm").submit(function(e){

    e.preventDefault();

    let fields=[
        "category_id",
        "sub_category_id",
        "name",
        "price",
        "prod_bv",
        "prod_pv",
        "hsn_code",
        "stock",
        "description"
    ];

    for(let id of fields){

        let value;

        if(id==="description"){
            value = CKEDITOR.instances.description.getData().trim();
        }else{
            value = $("#"+id).val().trim();
        }

        let config = rules[id];
        let error = validate(value,config.title,config.rules);

        if(error){

            showError(id,error);
            toastr.error(error);
            $("#"+id).focus();

            return false;
        }

    }
    
    
    // IMAGE VALIDATION
    let imageInputs = $(".imageInput");
    let imageCount = 0;

    imageInputs.each(function(){
        if(this.files.length > 0){
            imageCount++;
        }
    });

    if(imageCount === 0){
        toastr.error("At least one product image is required");
        return false;
    }

    if(imageCount > 4){
        toastr.error("Maximum 4 images allowed");
        return false;
    }

    /* AJAX SUBMIT */

    let formData = new FormData(this);
    formData.set("description", CKEDITOR.instances.description.getData());

    $.ajax({

        url: $(this).attr("action"),
        type: "POST",
        data: formData,
        processData:false,
        contentType:false,

        beforeSend:function(){
            $("button[type=submit]").prop("disabled",true).text("Saving...");
        },

        success:function(response){

            $("button[type=submit]").prop("disabled",false).text("Save Product");

            if(response.status){
                toastr.success(response.message);

                setTimeout(function(){
                    window.location.href="{{ route('admin.product.list') }}";
                },1500);
            }

        },

        error:function(xhr){

            $("button[type=submit]").prop("disabled",false).text("Save Product");

            if(xhr.status===422){

                let errors = xhr.responseJSON.errors;

                $.each(errors,function(key,value){
                    toastr.error(value[0]);
                });

            }else{
                toastr.error("Something went wrong");
            }

        }

    });

});/* Only numbers */

$('#price,#prod_bv,#prod_pv,#stock').keypress(function(e){

if(e.which < 48 || e.which > 57){
return false;
}

});


$('#name').keypress(function(e){
    var char = String.fromCharCode(e.which);

    if(!/^[a-zA-Z ]+$/.test(char)){
        return false;
    }
});


/* Image validation */
$('#image').on('change', function(){

    let file = this.files[0];

    if(!file) return;

    let allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp'
    ];

    if(!allowedTypes.includes(file.type)){
        toastr.error("Only JPG, JPEG, PNG, WEBP images allowed");
        $(this).val('');
        return;
    }

});


/* Add Image Row */

$(document).on("click",".addRow",function(){

    let currentRow = $(this).closest("tr");
    let fileInput = currentRow.find(".imageInput")[0];

    // Check if current row image is selected
    if(fileInput.files.length === 0){
        toastr.error("Please select an image first");
        return;
    }

    // Limit total images
    let totalRows = $("#imageBody tr").length;

    if(totalRows >= 4){
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
/* Remove Row */

$(document).on("click",".removeRow",function(){

$(this).closest("tr").remove();

});


/* Image Preview */

// $(document).on("change",".imageInput",function(){

// let file = this.files[0];

// if(!file) return;

// let allowedTypes = [
// 'image/jpeg',
// 'image/jpg',
// 'image/png',
// 'image/webp'
// ];

// if(!allowedTypes.includes(file.type)){

// toastr.error("Only JPG, JPEG, PNG, WEBP allowed");

// $(this).val("");

// return;

// }

// let reader = new FileReader();

// let preview = $(this).closest("tr").find(".preview");

// reader.onload = function(e){

// preview.attr("src",e.target.result).removeClass("hidden");

// };

// reader.readAsDataURL(file);

// });

let cropper;
let currentInput;

$(document).on("change",".imageInput",function(){

    let file = this.files[0];

    if(!file) return;

    let allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp'
    ];

    if(!allowedTypes.includes(file.type)){
        toastr.error("Only JPG, JPEG, PNG, WEBP allowed");
        $(this).val("");
        return;
    }

    currentInput = this;

    let reader = new FileReader();

    reader.onload = function(e){

        $("#cropImage").attr("src", e.target.result);
        $("#cropModal").css("display","flex");

        if(cropper){
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
        width:600,
        height:600
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
        cropper = null;

    }, "image/png");

});
$("#cropCancel").click(function(){

    $("#cropModal").hide();

    if(cropper){
        cropper.destroy();
    }

});
</script>

@endsection