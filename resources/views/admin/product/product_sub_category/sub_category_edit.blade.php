@extends('admin.layouts.master')

@section('title')
Add Product Sub Category
@endsection

@section('breadcrumb')
Category / Add Sub Category
@endsection

@section('content')

<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Add Product Sub Category</h2>

            <a href="{{ route('admin.product.sub.category.list') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                Back
            </a>
        </div>

        <div class="p-5">

            <form id="subCategoryForm" action="{{ route('admin.product.sub.category.update',$subcategory->id) }}" method="POST">
    @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-2 text-sm font-medium">Category</label>
                        <select name="category_id"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                {{ $subcategory->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                                </option>
                                @endforeach
                                
                        </select>
                        @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">Sub Category Name</label>
                        
                        <input type="text" id="subCategoryName" name="name"
                        value="{{$subcategory->name}}"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter sub category name">
                        
                        <p id="nameError" class="text-red-500 text-sm mt-1"></p>
                        
                        @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">Status</label>

                        <select name="status"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <option value="1" {{ old('status', $subcategory->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $subcategory->status) == 0 ? 'selected' : '' }}>Inactive</option>

                        </select>

                    </div>


                    {{-- Description --}}
                    <div class="md:col-span-2">

                        <label class="block mb-2 text-sm font-medium">Description</label>

                        <textarea name="description" rows="4"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter description">{{ old('description', $subcategory->description) }}</textarea>

                        @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

                {{-- Submit Button --}}
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000
    };
$(document).ready(function(){

    // NAME VALIDATION + CHECK EXISTING
    $('#subCategoryName').on('keyup', function(){

        let name = $(this).val();
        let regex = /^[A-Za-z0-9 .]+$/;

        if(name.length > 0){

            if(!regex.test(name)){
                $('#nameError').text('Only letters, numbers, spaces and dots are allowed.');
                return;
            }else{
                $('#nameError').text('');
            }

            $.ajax({
                url: "{{ route('admin.product.sub.category.check.name') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name
                },
                success: function(response){

                    if(response.exists){
                        $('#nameError').text('Sub category already exists');
                    }else{
                        $('#nameError').text('');
                    }

                }
            });

        }else{
            $('#nameError').text('');
        }

    });


    // FORM SUBMIT AJAX
    $('#subCategoryForm').submit(function(e){

        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let formData = form.serialize();

        $('.text-red-500').text('');

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            success: function(response){

                if(response.success){

                    toastr.success(response.message);

                    setTimeout(function(){
            window.location.href = "{{ route('admin.product.sub.category.list') }}";
        },1000);

                }

            },
            error: function(xhr){

                if(xhr.status === 422){

                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function(key, value){

                        if(key == 'category_id'){
                            $('[name="category_id"]').after('<p class="text-red-500 text-sm">'+value[0]+'</p>');
                        }

                        if(key == 'name'){
                            $('#nameError').text(value[0]);
                        }

                        if(key == 'description'){
                            $('[name="description"]').after('<p class="text-red-500 text-sm">'+value[0]+'</p>');
                        }

                    });

                }

            }

        });

    });

});
</script>
@endsection