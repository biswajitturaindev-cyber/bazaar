@extends('admin.layouts.master')

@section('title')
Change Password
@endsection

@section('breadcrumb')
Admin / Change Password
@endsection

@section('content')

<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Change Password</h2>

            <a href="{{ route('admin.dashboard') }}"
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
               Back
            </a>
        </div>

        <form id="changePasswordForm" action="" method="POST">
        @csrf

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- New Password -->
            <div>
                <label class="block mb-2 font-medium">New Password</label>
                <input type="password" name="new_password" id="new_password"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                    placeholder="Enter New Password">

                <p id="new_password_error" class="text-red-500 text-sm mt-1 hidden"></p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block mb-2 font-medium">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                    placeholder="Confirm New Password">

                <p id="confirm_password_error" class="text-red-500 text-sm mt-1 hidden"></p>
            </div>

        </div>

        <!-- Submit -->
        <div class="p-5 border-t flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Update Password
            </button>
        </div>

        </form>

    </div>
</div>

<!-- OTP Modal -->
<div id="otpModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">

    <div class="bg-white rounded-lg p-6 w-96">

        <h2 class="text-lg font-semibold mb-4">Enter OTP</h2>

        <p class="text-sm text-gray-500 mb-3">
            OTP has been sent to your email
        </p>

        <input type="text" id="otp_code"
            class="w-full border px-3 py-2 rounded-lg"
            placeholder="Enter OTP">

        <p id="otp_error" class="text-red-500 text-sm mt-1 hidden"></p>

        <div class="flex justify-end mt-4 gap-3">

            <button id="cancelOtp"
                class="px-4 py-2 bg-gray-500 text-white rounded">
                Cancel
            </button>

            <button id="verifyOtp"
                class="px-4 py-2 bg-blue-600 text-white rounded">
                Verify OTP
            </button>

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

/* ---------- VALIDATE FUNCTION ---------- */

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

        }
    }

    return null;
}

/* ---------- VALIDATION RULES ---------- */

const validationRules = {

    new_password: {
        title: "New Password",
        rules: ["required","min:6"]
    },

    confirm_password: {
        title: "Confirm Password",
        rules: ["required","min:6"]
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

/* ---------- FIELD BLUR VALIDATION ---------- */

document.addEventListener("blur", function (e) {

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

document.addEventListener("input", function (e) {

    const id = e.target.id;

    if (validationRules[id]) {
        clearError(id);
    }

});


/* ---------- FORM SUBMIT ---------- */

let passwordData;

$('#changePasswordForm').submit(function(e){

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

    let newPassword = $('#new_password').val();
    let confirmPassword = $('#confirm_password').val();

    if(newPassword !== confirmPassword){
        showError("confirm_password","Passwords do not match");
        valid = false;
    }

    if(!valid) return;

    passwordData = new FormData(this);

    $.ajax({

        url:"{{ route('admin.password.send.otp') }}",
        type:"POST",
        data: passwordData,
        processData:false,
        contentType:false,

        success:function(res){

            if(res.success){

                toastr.success(res.message);

                $('#otpModal').removeClass('hidden').addClass('flex');

            }

        }

    });

});
$('#verifyOtp').click(function(){

    let otp = $('#otp_code').val();

    if(!otp){

        $('#otp_error').text("OTP is required").removeClass('hidden');

        return;

    }

    passwordData.append("otp",otp);

    $.ajax({

        url:"{{ route('admin.password.verify.otp') }}",
        type:"POST",
        data: passwordData,
        processData:false,
        contentType:false,

        success:function(res){

            if(res.success){

                toastr.success(res.message);

                $('#otpModal').addClass('hidden');

                $('#changePasswordForm')[0].reset();

            }else{

                $('#otp_error').text(res.message).removeClass('hidden');

            }

        }

    });

});

$('#resendOtp').click(function(){

    $.ajax({

        url:"{{ route('admin.password.send.otp') }}",
        type:"POST",
        data: passwordData,
        processData:false,
        contentType:false,

        success:function(res){

            if(res.success){
                toastr.success("OTP resent to email");
            }

        }

    });

});
</script>

@endsection