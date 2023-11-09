var schoolIdLabel = $('label[for="schoolIdInput"]');
var schoolIdInput = $('#schoolIdInput');

if (isStudentVerificationOn) {
// Toggle to Passenger Name mode
schoolIdLabel.text('Passenger Name:');
schoolIdInput.attr('placeholder', 'Enter passenger name');
isStudentVerificationOn = false;

// Change the button class and text
$(this).removeClass('btn-success').addClass('btn-danger').text('Verification Off');
} else {
// Toggle back to School Id mode
schoolIdLabel.text('School Id:');
schoolIdInput.attr('placeholder', 'Enter school ID');
isStudentVerificationOn = true;
// Change the button class and text
$('#schoolIdInput').on('keyup', function() {
var schoolId = $(this).val();

// Perform an AJAX request to check the school ID
$.ajax({
url: 'verify_schoolId.php', // Replace with the actual endpoint URL
type: 'POST', // Use GET or POST as per your server's API
data: {
schoolId: schoolId
},
success: function(response) {
if (response.exists) {
// School ID exists in the reference table, enable the "Confirm" button
$('#confirmButton').prop('disabled', false);
} else {
// School ID does not exist, disable the "Confirm" button
$('#confirmButton').prop('disabled', true);
}
},
error: function() {
// Handle errors if the AJAX request fails
}
});
});

$(this).removeClass('btn-danger').addClass('btn-success').text('Verification On');
}