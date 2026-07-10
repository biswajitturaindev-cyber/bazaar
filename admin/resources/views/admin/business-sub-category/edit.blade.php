@extends('admin.layouts.master')

@section('title')
    Edit Business Sub Category
@endsection

@section('breadcrumb')
    Edit Business Sub Category
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center p-5 border-b">

                <a href="{{ route('business-sub-categories.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back
                </a>
            </div>

            <form action="{{ route('business-sub-categories.update', $subCategory->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Parent Category -->
                    <div>
                        <label class="block mb-2 font-medium">Business Category</label>

                        <select name="business_category_id"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                            <option value="">-- Select Category --</option>

                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('business_category_id', $subCategory->business_category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('business_category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sub Category Name -->
                    <div>
                        <label class="block mb-2 font-medium">Sub Category Name</label>
                        <input type="text" name="name" maxlength="50" value="{{ old('name', $subCategory->name) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block mb-2 font-medium">Image</label>
                        <input type="file" name="image"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Preview -->
                        <div class="mt-3">
                            <img id="previewImage"
                                src="{{ $subCategory->image ? asset('storage/business_sub_category/' . $subCategory->image) : '' }}"
                                class="rounded"
                                style="width:80px;height:80px;object-fit:cover;{{ $subCategory->image ? '' : 'display:none;' }}">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Commission</label>
                        <input type="text" name="commission" maxlength="50" id="commission" value="{{ $subCategory->commission }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                            placeholder="Enter Commission">
                        <p id="commission_error" class="text-red-500 text-sm mt-1 hidden"></p>
                        <p id="commission_success" class="text-green-600 text-sm mt-1 hidden"></p>
                    </div>


                    <!-- Status -->
                    <div>
                        <label class="block mb-2 font-medium">Status</label>

                        <select name="status" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                            <option value="1" {{ old('status', $subCategory->status) == 1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0" {{ old('status', $subCategory->status) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Submit -->
                <div class="p-5 border-t flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Update Sub Category
                    </button>
                </div>

            </form>




        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.querySelector('input[name="image"]').addEventListener('change', function(e) {
            const [file] = this.files;
            if (file) {
                let preview = document.getElementById('previewImage');
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        });
    </script>
@endsection
