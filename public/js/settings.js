$(document).ready(function() {

    // --- User settings ---

    $('#formUserSettings').validate({
        rules: {
            name: {
                required: true,
            },
            email: {
                required: true,
                email: true,
                remote: {
                    url: '/account/validate-email',
                    data: {
                        ignore_id: function () {
                            return userID;
                        }
                    }
                }
            },
            password: {
                minlength: 6,
                validPassword: true
            },
        },
        messages: {
            email: {
                remote: "Istnieje już konto zarejstrowane na podany adres email."
            }
        }
    });

    $('#inputPassword').hideShowPassword({
        show: false,
        innerToggle: 'focus',
        toggle: {
            className: 'btn customHideShow',
            verticalAlign: 'middle',
        },
        states: {
            shown: {
                toggle: {
                    content: "Ukryj"
                }
            },
            hidden: {
                toggle: {
                    content: "Pokaż"
                }
            }
        }
    });

     /*

    if(url.includes('userUpdate')) {

        // $('ul.nav-tabs a').removeClass('active');
        $('a#user-tab').addClass('active');

        // $('.tab-pane').removeClass('active');
        $('#user').addClass('show active');
        $('#userModal').modal('show');

    }
    else {
        $('a#income-categories-tab').addClass('active');
        $('#income-categories').addClass('show active');
    }

     */

    let url = location.href;

    if(url.includes('user-update')) {
        $('#userModal').modal('show');
    }

    if(url.includes('income-category-update')) {
        $('#incomeCategoryEditModal').modal('show');
    }

    if(url.includes('expense-category-update')) {
        $('#expenseCategoryEditModal').modal('show');
    }


    // --- Income categories settings ---

    // For expense category edit - ignore actual category name
    let incomeCategoryIgnoreID = null;

    $('button[data-target="#incomeCategoryEditModal"]').click(function () {

        const element = $(this).parent().siblings();

        const id = element.attr('data-id');
        const category = element.text();
        incomeCategoryIgnoreID = id;

        $('form#formIncomeCategoryEdit input[name="category_id"]').val(id);
        $('form#formIncomeCategoryEdit input[name="category_name"]').val(category);

    });

    $('button[data-target="#incomeCategoryDeleteModal"]').click(function () {

        $('form#formIncomeCategoryDelete .alert').addClass('d-none');

        const element = $(this).parent().siblings();
        const id = element.attr('data-id');
        const category = element.text();

        $('form#formIncomeCategoryDelete input[name="category_id"]').val(id);
        $('form#formIncomeCategoryDelete label strong').text(category);


        $.ajax({
            url : `/account/find-income-by-category?category_id=${id}`,
            success : function(response) {
                let incomes = JSON.parse(response);
                if(incomes.length) {
                    $('form#formIncomeCategoryDelete .alert').removeClass('d-none');

                    let text = `<p>Spowoduje to usunięcie wszystkich przychodów z wybranej kategorii:</p>

                            <table class="table table-light table-sm">
                              <tr>
                                <th>Lp.</th>
                                <th>Kwota</th>
                                <th>Data</th>
                              </tr>`;

                    $.each( incomes, function( index, value ){

                        text += `<tr>
                                    <td>${++index}.</td>
                                    <td>${value.amount}</td>
                                    <td>${value.date_of_income}</td>
                                </tr>`;
                    });

                    text += '</table>';

                    $('form#formIncomeCategoryDelete .alert').html(text);
                }
            },
            error : function() {
                console.log("Wystąpił błąd z połączeniem");
            }
        });

    });

    $.validator.addMethod('validCategoryName',
        function(value, element, param) {
            if (value != '') {
                if (value.match(/^[a-zA-Z\s]+$/) == null) {
                    return false;
                }
            }
            return true;
        },
        'Nazwa kategori może składać się z liter oraz spacji.'
    );

    $('#formIncomeCategoryEdit').validate({
        rules: {
            category_name: {
                required: true,
                validCategoryName: true,
                remote: {
                    url: '/account/validate-income-category',
                    data: {
                        ignore_id: function () {
                            return incomeCategoryIgnoreID;
                        }
                    }
                }
            }
        },
        messages: {
            category_name: {
                remote: "Istnieje już kategoria o podanej nazwie."
            }
        }
    });

    $('#formIncomeCategoryAdd').validate({
        rules: {
            category_name: {
                required: true,
                validCategoryName: true,
                remote: '/account/validate-income-category'
            }
        },
        messages: {
            category_name: {
                remote: "Istnieje już kategoria o podanej nazwie."
            }
        }
    });

    // --- Expense categories settings ---

    $('button[data-target="#expenseCategoryEditModal"]').click(function () {

        // Reset limit input
        $('input#editCategoryLimitCheck').prop('checked', false);
        $('input#editCategoryLimitInput').prop('disabled', true);
        $('input#editCategoryLimitInput').val('');


        const element = $(this).parent().siblings('p');

        const id = element.attr('data-id');
        const category = element.children('span').text();
        const limit = element.find('i').find('span').text();

        expenseCategoryIgnoreID = id;

        if(limit) {
            $('input#editCategoryLimitCheck').prop('checked', true);
            $('input#editCategoryLimitInput').prop('disabled', false);
            $('input#editCategoryLimitInput').val(limit);
        }

        $('form#formExpenseCategoryEdit input[name="category_id"]').val(id);
        $('form#formExpenseCategoryEdit input[name="category_name"]').val(category);

    });

    $('button[data-target="#expenseCategoryDeleteModal"]').click(function () {

        $('form#formExpenseCategoryDelete .alert').addClass('d-none');

        const element = $(this).parent().siblings('p');

        const id = element.attr('data-id');
        const category = element.children('span').text();

        $('form#formExpenseCategoryDelete input[name="category_id"]').val(id);
        $('form#formExpenseCategoryDelete label strong').text(category);


        $.ajax({
            url : `/account/find-expense-by-category?category_id=${id}`,
            success : function(response) {
                let expenses = JSON.parse(response);
                if(expenses.length) {
                    $('form#formExpenseCategoryDelete .alert').removeClass('d-none');

                    let text = `<p>Spowoduje to usunięcie wszystkich wydatków z wybranej kategorii:</p>

                            <table class="table table-light table-sm">
                              <tr>
                                <th>Lp.</th>
                                <th>Kwota</th>
                                <th>Data</th>
                              </tr>`;

                    $.each( expenses, function( index, value ){

                        text += `<tr>
                                    <td>${++index}.</td>
                                    <td>${value.amount}</td>
                                    <td>${value.date_of_expense}</td>
                                </tr>`;
                    });

                    text += '</table>';

                    $('form#formExpenseCategoryDelete .alert').html(text);
                }
            },
            error : function() {
                console.log('Wystąpił błąd z połączeniem');
            }
        });

    });

    $.validator.addMethod('validMonthlyLimit',
        function(value, element, param) {
            if (value != '') {
                if (value.match(/^\d{1,15}(\.\d{0,2})?$/) == null) {
                    return false;
                }
            }
            return true;
        },
        'Podaj poprawną wartość miesięcznego limitu dla kategorii - maksymalnie 17 cyfr w tym 2 po przecinku.'
    );

    $('#formExpenseCategoryEdit').validate({
        rules: {
            category_name: {
                required: true,
                validCategoryName: true,
                remote: {
                    url: '/account/validate-expense-category',
                    data: {
                        ignore_id: function () {
                            return expenseCategoryIgnoreID;
                        }
                    }
                }
            },
            monthly_limit: {
                validMonthlyLimit: true
            }
        },
        messages: {
            category_name: {
                remote: 'Istnieje już kategoria o podanej nazwie.'
            }
        }
    });

    $('#formExpenseCategoryAdd').validate({
        rules: {
            category_name: {
                required: true,
                validCategoryName: true,
                remote: '/account/validate-expense-category'
            },
            monthly_limit: {
                validMonthlyLimit: true
            }
        },
        messages: {
            category_name: {
                remote: 'Istnieje już kategoria o podanej nazwie.'
            }
        }
    });

    $('input#addCategoryLimitCheck').change(function() {
        if(this.checked) {
            $('input#addCategoryLimitInput').prop('disabled', false);
        } else {
            $('input#addCategoryLimitInput').prop('disabled', true);
        }
    });

    $('input#editCategoryLimitCheck').change(function() {
        if(this.checked) {
            $('input#editCategoryLimitInput').prop('disabled', false);
        } else {
            $('input#editCategoryLimitInput').prop('disabled', true);
            $('#editCategoryLimitInput').val('');
        }
    });


});
