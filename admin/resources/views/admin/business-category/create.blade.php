@extends('admin.layouts.master')

@section('title')
    Add Business Category
@endsection

@section('breadcrumb')
    Business Category
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center p-5 border-b">
                <h2 class="text-lg font-semibold">Add Business Category</h2>

                <a href="{{ route('business-categories.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back
                </a>
            </div>

            <form action="{{ route('business-categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Category Name -->
                    <div>
                        <label class="block mb-2 font-medium">Business Category Name</label>
                        <input type="text" name="name" maxlength="50" value="{{ old('name') }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                            placeholder="Enter Category Name">

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
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block mb-2 font-medium">Status</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>

                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Submit -->
                <div class="p-5 border-t flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Save Business Category
                    </button>
                </div>

            </form>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@endsection
