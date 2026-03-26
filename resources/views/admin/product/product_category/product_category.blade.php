@extends('admin.layouts.master')

@section('title')
    Add Product Category
@endsection

@section('breadcrumb')
    Products / Add Category
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center p-5 border-b">
                <h2 class="text-lg font-semibold">Add Product Category</h2>

                <a href="{{ route('admin.product.category.list') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back
                </a>
            </div>

            <form id="categoryForm" action="{{ route('admin.product.category.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Category Name -->
                    <div>
                        <label class="block mb-2 font-medium">Category Name</label>
                        <input type="text" name="category_name" maxlength="50" id="category_name"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                            placeholder="Enter Category Name">
                        <p id="category_name_error" class="text-red-500 text-sm mt-1 hidden"></p>
                        <p id="category_name_success" class="text-green-600 text-sm mt-1 hidden"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 font-medium">Category Description</label>
                        <textarea name="description" id="description" rows="4" maxlength="200"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" placeholder="Enter category description"></textarea>
                        <p id="description_error" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>


                    <div>
                        <label class="block mb-2 font-medium">Commission</label>
                        <input type="text" name="commission" maxlength="50" id="commission"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                            placeholder="Enter Commission">
                        <p id="commission_error" class="text-red-500 text-sm mt-1 hidden"></p>
                        <p id="commission_success" class="text-green-600 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Image</label>
                        <input type="file" name="image" id="image"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                        <p id="image_error" class="text-red-500 text-sm mt-1 hidden"></p>
                        <p id="image_success" class="text-green-600 text-sm mt-1 hidden"></p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block mb-2 font-medium">Status</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </div>

                <!-- Submit -->
                <div class="p-5 border-t flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Save Category
                    </button>
                </div>

            </form>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        function validate(value, title, rules = []) {

            for (let rule of rules) {

                let [type, param] = rule.split(":");

                switch (type) {

                    case "required":
                        if (!value || value.trim() === "") {
                            return `${title} is required`;
                        }
                        break;

                    case "min":
                        if (value.length < param) {
                            return `${title} must be at least ${param} characters`;
                        }
                        break;

                    case "max":
                        if (value.length > param) {
                            return `${title} must be less than ${param} characters`;
                        }
                        break;

                    case "alphanum_space_dot":

                        if (!/^[A-Za-z0-9 .]+$/.test(value)) {
                            return `${title} can contain only letters, numbers, spaces and '.'`;
                        }

                        break;

                }

            }

            return null;

        }


        const validationRules = {

            category_name: {
                title: "Category Name",
                rules: ["required", "min:3", "max:50", "alphanum_space_dot"]
            }

        };


        /* ---------- SHOW ERROR ---------- */

        function showError(id, message) {

            const input = document.getElementById(id);
            const error = document.getElementById(id + "_error");

            input.classList.add("border-red-500");

            error.innerText = message;
            error.classList.remove("hidden");

        }


        /* ---------- CLEAR ERROR ---------- */

        function clearError(id) {

            const input = document.getElementById(id);
            const error = document.getElementById(id + "_error");

            input.classList.remove("border-red-500");

            error.innerText = "";
            error.classList.add("hidden");

        }


        /* ---------- SHOW SUCCESS ---------- */

        function showSuccess(id, message) {

            const success = document.getElementById(id + "_success");

            success.innerText = message;
            success.classList.remove("hidden");

        }

        /* ---------- CLEAR SUCCESS ---------- */

        function clearSuccess(id) {

            const success = document.getElementById(id + "_success");

            success.innerText = "";
            success.classList.add("hidden");

        }


        /* ---------- FIELD BLUR VALIDATION ---------- */

        document.addEventListener("blur", function(e) {

            const fieldId = e.target.id;

            if (!validationRules[fieldId]) return;

            const config = validationRules[fieldId];

            const value = e.target.value.trim();

            const error = validate(value, config.title, config.rules);

            if (error) {

                showError(fieldId, error);

            } else {

                clearError(fieldId);

            }

        }, true);


        /* ---------- CLEAR ERROR ON INPUT ---------- */

        document.addEventListener("input", function(e) {

            const id = e.target.id;

            if (validationRules[id]) {
                clearError(id);
                clearSuccess(id);
            }

        });


        /* ------------------------Ajax ----------------------------*/
        $('#category_name').keyup(function() {

            let categoryName = $(this).val().trim();

            if (categoryName.length < 3) {
                clearSuccess("category_name");
                return;
            }

            $.ajax({
                url: "{{ route('product.category.check') }}",
                type: "POST",
                data: {
                    category_name: categoryName,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {

                    if (response.exists) {

                        clearSuccess("category_name");
                        showError("category_name", "Category already exists");

                    } else {

                        clearError("category_name");
                        showSuccess("category_name", "Category available");

                    }

                }
            });

        });


        /* ---------- FORM SUBMIT VALIDATION ---------- */
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };
        $('#categoryForm').submit(function(e) {

            e.preventDefault();

            let valid = true;

            for (let field in validationRules) {

                let input = document.getElementById(field);
                let value = input.value.trim();
                let config = validationRules[field];

                let error = validate(value, config.title, config.rules);

                if (error) {
                    showError(field, error);
                    valid = false;
                }
            }

            if (!valid) return;

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.product.category.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function(response) {

                    if (response.success) {

                        toastr.success(response.message); // success toast

                        $('#categoryForm')[0].reset();

                        clearSuccess("category_name");
                        clearError("category_name");

                    }

                },

                error: function(xhr) {

                    if (xhr.responseJSON && xhr.responseJSON.errors) {

                        let errors = xhr.responseJSON.errors;

                        if (errors.category_name) {
                            showError("category_name", errors.category_name[0]);
                            toastr.error(errors.category_name[0]);
                        }

                        if (errors.description) {
                            showError("description", errors.description[0]);
                            toastr.error(errors.description[0]);
                        }

                        if (errors.commission) {
                            showError("commission", errors.commission[0]);
                            toastr.error(errors.commission[0]);
                        }

                        if (errors.image) {
                            showError("image", errors.image[0]);
                            toastr.error(errors.image[0]);
                        }


                    } else {
                        toastr.error("Something went wrong");
                    }

                }

            });

        });
        /* ---------- ALLOW ONLY VALID CHARACTERS WHILE TYPING ---------- */

        $('#category_name').keypress(function(e) {

            var charCode = e.which;

            if (
                (charCode >= 48 && charCode <= 57) || // 0-9
                (charCode >= 65 && charCode <= 90) || // A-Z
                (charCode >= 97 && charCode <= 122) || // a-z
                charCode == 32 || // space
                charCode == 46 // .
            ) {
                return true;
            }

            return false;

        });


        $('#description').keypress(function(e) {

            let charCode = e.which;

            if (
                (charCode >= 48 && charCode <= 57) || // 0-9
                (charCode >= 65 && charCode <= 90) || // A-Z
                (charCode >= 97 && charCode <= 122) || // a-z
                charCode == 32 || // space
                charCode == 44 || // ,
                charCode == 46 // .
            ) {
                return true;
            }

            return false;

        });
    </script>
@endsection
