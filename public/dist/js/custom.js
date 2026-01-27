$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let validator;
let emailExists = false;
let emailTimer = null;
let lastCheckedEmail = '';
var usersTable = null;
// multiple selected dropdown for categories
$(document).ready(function () {
    $('.select2').select2({
        width: '100%'
    });
});


$(document).ready(function () {

    $('#categorySelect').select2({
        width: '100%'
    });

    $('#categorySelect').on('select2:select', function (e) {

        if (e.params.data.id === 'all') {

            let allValues = [];
            $('#categorySelect option').each(function () {
                if ($(this).val() !== 'all') {
                    allValues.push($(this).val());
                }
            });

            let selected = $('#categorySelect').val() || [];

            // TOGGLE LOGIC
            if (selected.length === allValues.length) {
                // UNSELECT ALL
                $('#categorySelect')
                    .val(null)
                    .trigger('change');
            } else {
                // SELECT ALL
                $('#categorySelect')
                    .val(allValues)
                    .trigger('change');
            }

            // Always keep "Select All" unchecked
            $('#categorySelect option[value="all"]').prop('selected', false);
        }
    });

});
$(document).ready(function () {
    $('#stateSelect').select2({
        theme: 'bootstrap4',
        placeholder: 'Select State',
        allowClear: true,
        width: '100%'
    });
});

$(document).ready(function () {

    $('#stateSelect, #citySelect').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    $('#stateSelect').on('change.select2', function () {
        $(this).valid();
    });

    const states = [
        "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh",
        "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka",
        "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram",
        "Nagaland", "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu",
        "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"
    ];

    states.forEach(state => {
        $('#stateSelect').append(`<option value="${state}">${state}</option>`);
    });

    $('#stateSelect').on('change', function () {

        let state = $(this).val();

        // $('#citySelect')
        //     .html('<option value="">Loading...</option>')
        //     .trigger('change');

        if (!state) return;

        fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ country: "India", state })
        })
            .then(res => res.json())
            .then(data => {
                $('#citySelect').html('<option value="">Select City</option>');
                data.data.forEach(city => {
                    $('#citySelect').append(`<option value="${city}">${city}</option>`);
                });
                $('#citySelect').trigger('change');
            });
    });
});


// preview image on create user time
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('imagePreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    }
}

//create user  form validation
$('#email').on('keyup change', function () {

    clearTimeout(emailTimer);

    let email = $(this).val();
    let form = $('#createUserForm');
    let validator = form.data('validator');

    //  do nothing if email didn't change
    if (email === lastCheckedEmail) {
        return;
    }

    // clear only when value CHANGES
    emailExists = false;
    $(this).removeClass('is-invalid');
    if (validator) validator.hideErrors();

    if (email.length < 5) return;

    emailTimer = setTimeout(function () {
        $.ajax({
            url: window.CHECK_EMAIL_URL,
            type: 'POST',
            data: {
                email: email,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {

                lastCheckedEmail = email;

                if (res.exists) {
                    emailExists = true;

                    if (validator) {
                        validator.showErrors({
                            email: "Email already exists"
                        });
                    }

                    $('#email').addClass('is-invalid');
                } else {
                    emailExists = false;
                    $('#email').removeClass('is-invalid');
                }
            }
        });
    }, 500);
});
$(document).ready(function () {
    let isEdit = $('#createUserForm').data('mode') === 'edit';
    $('#createUserForm').validate({
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: function () {
                    return !isEdit; // ✅ required only on create
                },
                minlength: 6
            },
            cpassword: {
                required: function () {
                    return !isEdit; // ✅ required only on create
                },
                equalTo: '[name="password"]'
            },
            number: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            role: {
                required: true
            },
            states: {
                required: true
            },
            city: {
                required: true
            },
            category_ids: {
                required: function () {
                    return $('[name="role"]').val() === 'customer';
                }
            },
            image: {
                required: true,
                extension: "jpg|jpeg|png|webp|gif"
            }
        },

        messages: {
            name: "Please enter name",
            email: "Please enter valid email",
            password: {
                required: "Please enter password",
                minlength: "Password must be at least 6 characters"
            },
            cpassword: {
                required: "Please confirm password",
                equalTo: "Password does not match"
            },
            number: {
                required: "Please enter mobile number",
                digits: "Only numbers allowed",
                minlength: "Enter valid mobile number",
                maxlength: "Enter valid mobile number"
            },
            role: "Please select role",
            city: "Please select city",
            states: "Please select state",
            category_ids: "Please select category",
            image: "Please upload profile image"
        },

        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');

            if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2-container'));
            } else {
                error.insertAfter(element);
            }
        },


        // highlight: function (element) {
        //     $(element).addClass('is-invalid');
        // },

        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },

        submitHandler: function (form) {

            let validator = $('#createUserForm').data('validator');

            // 🔒 HARD BLOCK SUBMIT
            if (emailExists) {
                validator.showErrors({
                    email: "Email already exists"
                });
                return false;
            }
            form.submit(); // ✅ only submit if valid
        }
    });

});

//category to sub category select
$(function () {

    // CATEGORY → SUBCATEGORY
    $('#category').on('change', function () {

        let categoryId = $(this).val();
        let url = $(this).data('subcategory-url');

        $('#subcategory').html('<option>Loading...</option>');
        $('#product').html('<option value="">Select Product</option>');

        if (!categoryId) {
            $('#subcategory').html('<option value="">Select Subcategory</option>');
            return;
        }

        $.get(url + '/' + categoryId, function (data) {

            let options = '<option value="">Select Subcategory</option>';

            data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('#subcategory').html(options);
        });
    });

    // SUBCATEGORY → PRODUCT
    $('#subcategory').on('change', function () {

        let subcategoryId = $(this).val();
        let url = $('#category').data('product-url');

        $('#product').html('<option>Loading...</option>');

        if (!subcategoryId) {
            $('#product').html('<option value="">Select Product</option>');
            return;
        }

        $.get(url + '/' + subcategoryId, function (data) {

            let options = '<option value="">Select Product</option>';

            data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('#product').html(options);
        });
    });

});




const CSRF_TOKEN = "{{ csrf_token() }}";













