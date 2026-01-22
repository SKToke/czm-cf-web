document.addEventListener('DOMContentLoaded', function () {
    const userCountry = document.querySelector('#country');
    const userType = document.querySelector('#user_user_type');
    const bangladeshiAddress = document.querySelector('#bangladeshi-address');
    const contactFields = document.querySelector('#contact-fields');
    const contactPersonName = document.querySelector('#user_contact_person_name');
    const contactPersonMobile = document.querySelector('#user_contact_person_mobile');
    const contactPersonDesignation = document.querySelector('#user_contact_person_designation');

    function toggleContactFields() {

        //to check if usertype is bussiness type
        if (userType && userType.value == 2) {
            contactFields.style.display = 'block';
            contactPersonName.required = true;
            contactPersonMobile.required = true;
            contactPersonDesignation.required = true;
        } else {
            contactFields.style.display = 'none';
            contactPersonName.required = false;
            contactPersonMobile.required = false;
            contactPersonDesignation.required = false;
        }
    }

    function toggleAddressFields() {

        //to check weather country is bangladesh
        if (userCountry && userCountry.value == 14) {
            bangladeshiAddress.style.display = 'block';
        } else {
            bangladeshiAddress.style.display = 'none';
        }
    }

    // Initial toggle on page load
    toggleContactFields();
    toggleAddressFields();

    // Event listeners
    userType && userType.addEventListener('change', toggleContactFields);
    userCountry && userCountry.addEventListener('change', toggleAddressFields);
});
