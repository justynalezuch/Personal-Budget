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

jQuery.extend(jQuery.validator.messages, {
    required: "To pole jest wymagane.",
    email: "Wprowadź adres email w prawidłowym formacie.",
    equalTo: "Podane hasła muszą się zgadzać.",
    minlength: jQuery.validator.format("Wprowadź co najmniej {0} znaków.")
});