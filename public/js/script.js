function validateDate(date) {
	var re = /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/;
	return re.test(date);
}

function validateRollNo(rollNo) {
	var re = /^[1-9][0-9]*$/;
	return re.test(rollNo);
}