$(document).ready(function() {

    $("#update_profile").validate({
        rules: {
            first_name: "required",
            last_name: "required",
            user_name: {
                required: true,
                noSpace: true,
                alphanumeric: true
            }
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        }

    });
    $("#change_password").validate({
        rules: {
            old_password: "required",
            new_password: "required",
            confirm_password: {
                required: true,
                equalTo: "#new_password",
            }
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        }

    });

    // validate Main Form
    $("#form").validate({
        rules: {
            //validate the Genaral settings into School Settings
            'to[]': "required",
            start_date: "required",
            end_date: "required",
            project_title: "required",
            due_date: "required",
            milestone_name: "required",
            task_name: "required",
            category: "required",
            comment: "required",
            name: "required",
            email: "required",
            date: "required",
            payment_date: "required",
            expense_category: "required",
            expense_category_id: "required",
            item_name: "required",
            purchase_date: "required",
            amount: "required",
            flag: "required",
            title: "required",
            password: "required",
            confirm_password: {
                required: true,
                equalTo: "#password"
            },
            mobile: {
                required: true,
                number: true
            },
            year: "required",
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        messages: {
            firstname: "Please enter your firstname",
            lastname: "Please enter your lastname",
            username: {
                required: "Please enter a username",
                minlength: "Your username must consist of at least 2 characters"
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            },
            confirm_password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            },
            email: "Please enter a valid email address",
            agree: "Please accept our policy"
        }
    });

});