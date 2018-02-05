function validateDate(date) {
	var re = /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/;
	return re.test(date);
}

function validateRollNo(rollNo) {
	var re = /^[1-9][0-9]*$/;
	return re.test(rollNo);
}

function getPrettyDate(date) {
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var date_ary = date.split('-');
	var year = parseInt(date_ary[0]);
	var month = parseInt(date_ary[1]);
	var date = parseInt(date_ary[2]);
	var pretty_date = months[month] + ' ' + date + ', ' + year;
	return pretty_date;
}