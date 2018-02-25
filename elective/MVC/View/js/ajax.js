function courseDescription(id){
    $.ajax({                                      
    	url: 'courseDesc',      
    	data: "courseID="+id,
    	type: "GET",
    	dataType: 'text',
    	success: function(data){
			$('#dialog').html(data);
			$('#courseDescModal').modal('show');
    	} 
	});
}

function popularCourses(id){
    $.ajax({                                      
    	url: 'admin/popularCourses',
		data: "limit="+id,      
    	type: "POST",
    	dataType: 'text',
    	success: function(data){
			$('#popularCourses').html(data);
    	} 
	});
}

function addCSVUserToDB(csfr){
	$.ajax({                                      
    	url: 'addCSVUsersDB',
		data: "token="+csfr,      
    	type: "POST",
    	dataType: 'text',
    	success: function(data){
			$('#csvAddUser').html(data);
			$('csvAddUser').remove();
    	} 
	});
}

function usersTable(){
	$.ajax({                                      
    	url: 'admin/usersTable',    
    	type: "POST",
    	dataType: 'text',
    	success: function(data){
			$('#usersTableWrapper').html(data);
			//$('#usersTable').remove();
    	} 
	});
}

function modifyUser(type, userID){//edit, add, del
	$.ajax({                                      
    	url: '/admin/userTask',    
    	type: "POST",
    	dataType: 'text',
		data: {type: type, userID: userID},
    	success: function(data){
			$('#userDialogModal').html(data);
    	} 
	});
}

function renderStatisticsTables(type){//studentWithoutSelection teacherWithoutCourse
	$.ajax({                                      
    	url: '/admin/statisticsTable',    
    	type: "POST",
    	dataType: 'text',
		data: {type: type},
    	success: function(data){
			$('#ajax-'+type).html(data);
    	} 
	});
}

function adminCourseTable(){
	$.ajax({                                      
    	url: '/admin/courseTable',    
    	type: "POST",
    	dataType: 'text',
    	success: function(data){
			$('#coursesTableWrapper').html(data);
    	} 
	});
}

function modifyCourse(type, courseID, data){//del, ignore
	$.ajax({                                      
    	url: '/admin/courseTask',    
    	type: "POST",
    	dataType: 'text',
		data: {type: type, courseID: courseID, data: data},
    	success: function(data){
			adminCourseTable();
    	} 
	});
}


