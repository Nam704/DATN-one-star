$(document).ready(function() {
    $('#showNewAttributeForm').click(function() {
        $('#newAttributeForm').toggle();
    });

    // Validate before form submit
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Get values
        let attributeId = $('.attribute-select').val();
        let attributeValue = $('input[name="attribute_values[]"]').val();
        let newAttributeName = $('input[name="new_attribute"]').val();
        let newAttributeValue = $('input[name="new_attribute_value"]').val();

        // Check if either existing attribute or new attribute is filled
        if (!((attributeId && attributeValue) || (newAttributeName && newAttributeValue))) {
            isValid = false;
            alert('Please either select an existing attribute with value or create a new attribute with value');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
