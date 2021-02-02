$.validator.addMethod('validPassword',
    function(value, element, param) {
        if (value != '') {
            if (value.match(/.*[a-z]+.*/i) == null) {
                return false;
            }
            if (value.match(/.*\d+.*/) == null) {
                return false;
            }
        }
        return true;
    },
    'Hasło musi posiadać co najmniej jedną literę i jedną cyfrę.'
);

$.validator.addMethod('validAmount',
    function(value, element, param) {
        if (value != '') {
            if (value.match(/^\d{1,8}(\.\d{0,2})?$/) == null) {
                return false;
            }
        }
        return true;
    },
    'Podaj poprawną wartość - maksymalnie 10 cyfr w tym 2 po przecinku.'
);

$.validator.addMethod('validDate',
    function(value, element, param) {
        if (value != '') {
            if (value.match(/^\d{4}-\d{2}-\d{2}$/) == null) {
                return false;
            }
        }
        return true;
    },
    'Podaj datę w prawidłowym formacie.'
);

jQuery.extend(jQuery.validator.messages, {
    required: "To pole jest wymagane.",
    email: "Wprowadź adres email w prawidłowym formacie.",
    equalTo: "Podane hasła muszą się zgadzać.",
    minlength: jQuery.validator.format("Wprowadź co najmniej {0} znaków.")
});

$(document).ready(function () {

    //Add expense/income - remember data in select
    if(document.getElementById('fr_paymentMethod') != null ) {
        if(document.getElementById('fr_paymentMethod').value)
            document.getElementById('paymentMethod').value = document.getElementById('fr_paymentMethod').value;
    }

    if(document.getElementById('fr_category') != null) {
        if(document.getElementById('fr_category').value)
            document.getElementById('category').value = document.getElementById('fr_category').value;
    }

    // Incomes categories - radio input remember data
    if($("input:radio[name=category]") != null) {
        if($("#fr_income_category").val()) {
            let fr_income_category = $("#fr_income_category").val();
            $(`input:radio[name=category][value=${fr_income_category}]`).attr('checked', true);
        } else {
            $("input:radio[name=category]:first").attr('checked', true);
        }
    }

    // Add expense, add income - if isset date - remember it
    if(document.getElementById('date') != null) {
        if (document.getElementById('date').value != '') {
            $('input#date').val(document.getElementById('date').value);
        } else {

            let currentDate = new Date();
            let currentDay = currentDate.getDate();
            let currentMonth = currentDate.getMonth() + 1;
            let currentYear = currentDate.getFullYear();

            if (currentDay < 10)
                currentDay = '0' + currentDay;

            if (currentMonth < 10)
                currentMonth = '0' + currentMonth;

            $('input#date').val(`${currentYear}-${currentMonth}-${currentDay}`);
        }
    }

    // --------- FINANCIAL BALANCE ---------

    if(document.getElementById('startDate') != null) {
        console.log(document.getElementById('startDate').value);
        if (document.getElementById('startDate').value != '') {
            $('input#date').val(document.getElementById('startDate').value);
        }
    }
    if(document.getElementById('endDate') != null) {
        console.log(document.getElementById('endDate').value);
        if (document.getElementById('endDate').value != '') {
            $('input#date').val(document.getElementById('endDate').value);
        }
    }

    if(document.getElementById('periodError') != null){
        $('#customPeriod').modal('show');
    }
});


