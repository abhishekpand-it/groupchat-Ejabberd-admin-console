/**
 * 
 */

var globalRoomName = '';
var globalRoomOptions;

function loginAction(){

	var userName = document.getElementById("inputEmail3").value ;    
    var password = document.getElementById("inputPassword3").value;

		var request = $.ajax({
        url : 'api/login?username='+userName+'&password='+password,
        type: "get",   
        success: function(response) {
    		if(response.status == "Success"){
    		localStorage.setItem("username", userName);
    		window.location.href = 'dashboard.html?username='+userName;
    		}
		else{
				$.notify("Login failure. Incorrect username/password. Please try again.", "error");
		}
      
        },   
        failure: function(msg)
        {		$.notify("login failed", "error");
        }
    });
    
}

function getMucRooms(){


		var request = $.ajax({
        url : 'api/get_rooms',
        type: "get",   
        success: function(response) {
            //sen request to next page.
    		if(response.status == "Success"){

				$("#mytable").find("tr:gt(0)").remove();
				var trHTML = '';
			        data = response.data;        
console.log("DATA IS ", data);
			        $.each(data, function (i, item) {
		        	    trHTML = '';
				    trHTML += '<tr><td id='+ data[i] + '> ' + data[i] +  '</td>'+
							'<td><span data-placement="top" data-toggle="tooltip" title="" data-original-title="Delete"><button id='+ data[i] + ' onclick="deleteGroup(this)" class="btn btn-danger btn-xs" data-title="Delete" data-toggle="modal" data-target="#delete"><span class="glyphicon glyphicon-trash"></span></button>'+
						'<button style="margin-left:10px;" id='+ data[i] + ' onclick="showUsers(this)" class="btn btn-primary btn-xs" data-title="Users" data-toggle="modal" data-target="#users"><span class="glyphicon glyphicon-user"></span></button>'+
						'<button style="margin-left:10px;" id='+ data[i] + ' onclick="showChangeRoomOption(this)" class="btn btn-success btn-xs" data-title="Edit" data-toggle="modal" data-target="#edit"><span class="glyphicon glyphicon-pencil"></span></button></span></p></td>'+
					      	+'</tr>';
						
						 $('#mytable').append(trHTML);
						 
			        });
	

    		}
		else{
				$.notify("Unable to fetch rooms.", "error");
		}
      
        },   
        failure: function(msg)
        {		$.notify("Fetch failed", "error");
        }
    });


}


function getRoomUsers(roomName){


	var request = $.ajax({
		url : 'api/get_room_users?room_name=' + roomName,
		type: "get",
		success: function(response) {
			//sen request to next page.
			if(response.status == "Success"){

				$("#usersTable").find("tr:gt(0)").remove();
				var trHTML = '';
				data = response.data;
				console.log("DATAROOM IS ", data);
				$.each(data, function (i, item) {
					data[i] = data[i].split("@")[0];
					trHTML = '';
					trHTML += '<tr><td id='+ data[i] + '> ' + '<input class="usersDeleteCheckbox" type="checkbox" value="'+ data[i] + '" name="deleteId">' +  '</td>'+
						'<td>'+ data[i] + '</td>'+
						+'</tr>';

					$('#usersTable').append(trHTML);

				});


			}
			else{
				$.notify("Unable to fetch users.", "error");
			}

		},
		failure: function(msg)
		{		$.notify("Fetch failed", "error");
		}
	});


}


function getRoomOptions(roomName){


	var request = $.ajax({
		url : 'api/get_room_options?room_name=' + roomName,
		type: "get",
		success: function(response) {
			//sen request to next page.
			if(response.status == "Success"){

				$("#roomOptionsTable").find("tr:gt(0)").remove();
				var trHTML = '';
				data = response.data;
				console.log("DATAROOM IS ", data);
				globalRoomOptions = data;
				$.each(data, function (i, item) {
					data[i] = data[i].split("@")[0];
					trHTML = '';
					trHTML += '<tr>';
					trHTML += '<td>' + i +  '</td>';
					if(item != "true" && item !="false")
					{
						trHTML += '<td><input type="text" id="'+ i +'opt" value="'+ item +'"></td>';
					}else{
						trHTML += '<td>';
						trHTML +=	'<select id="'+ i +'opt">';
								if(item == "false"){
									trHTML +=	'<option value="false" selected>false</option>';
									trHTML +=   '<option value="true">true</option>';
								}else{
									trHTML +=	'<option value="false">false</option>';
									trHTML +=	'<option value="true" selected>true</option>';
								}
						trHTML +=	'</select>';
						trHTML +=	'</td>';
					}
					trHTML += '</tr>';

					$('#roomOptionsTable').append(trHTML);

				});


			}
			else{
				$.notify("Unable to fetch options.", "error");
			}

		},
		failure: function(msg)
		{		$.notify("Fetch failed", "error");
		}
	});


}


function updateUsersInRoom(roomName, usersSelected, affiliation){


	var request = $.ajax({
		url : 'api/update_user?room_name=' + roomName + '&user_jid=' + usersSelected + '&affiliation=' + affiliation,
		type: "get",
		success: function(response) {
			//sen request to next page.
			if(response.status == "Success"){

				$("#usersTable").find("tr:gt(0)").remove();

				if(affiliation == "outcast")
					$.notify("Users removed from room successfully", "success");
				else
					$.notify("Users added to room successfully", "success");

				getRoomUsers(roomName);

			}
			else{
				$.notify("Unable to update user.", "error");
			}

		},
		failure: function(msg)
		{		$.notify("Fetch failed", "error");
		}
	});


}

function sendRoomDetails(){

	var roomName = document.getElementById("room_name").value ;

	if(roomName.indexOf(" ") > -1){
		$.notify("You cannot use whitespace in room name", "warn");
		return;
	}
	if(roomName.indexOf("private") > -1){
		$.notify("You cannot use keyword \"private\" in room name", "warn");
		return;
	}
	var serviceName = document.getElementById("service_name").value;
	var hostName = document.getElementById("host_name").value;
	var term = '';
	if(serviceName != '' && hostName != ''){
		term = '?room_name=' + roomName + '&service_name=' + service_name + '&host_name=' + host_name;
	}else {
		term = '?room_name=' + roomName;
	}
	var request = $.ajax({
		url : 'api/create_room' + term,
		type: "get",
		success: function(response) {
			console.log(response);
			if(response.status == "Success"){
				$.notify("Room created successfully", "success");
				getMucRooms();
				closeCreateGroup();
			}
			else{
				$.notify("Failed", "error");
			}

		},
		failure: function(msg)
		{		$.notify("Save failed", "error");
		}
	});

}

function createGroup(){
	$('#createGroup').show();
}

function showUsers(element){
	var tr = element;

	var groupId = tr.getAttribute('id');
	var roomName = groupId.split("@")[0];

	globalRoomName = roomName;
	$('#showGroupUsers').show();
	$('#usersTable').find("tr:gt(0)").remove();
	getRoomUsers(roomName);
}

function showChangeRoomOption(element){

	var tr = element;

	var groupId = tr.getAttribute('id');
	var roomName = groupId.split("@")[0];

	$('#showRoomOptions').show();
	$('#roomOptionsTable').find("tr:gt(0)").remove();

	globalRoomName = roomName;
	getRoomOptions(roomName);
}

function closeGroupUsers(){
	$('#showGroupUsers').hide();
}
function closeCreateGroup(){
	$('#createGroup').hide();
}
function closeRoomOptions(){
	$('#showRoomOptions').hide();
}

function deleteGroup(element){
	console.log("DELETING CALLED");
	console.log(element);
	var tr = element;

	var groupId = tr.getAttribute('id');
	console.log("GORUP ID " + groupId);

	var roomName = groupId.split("@")[0];

	if(confirm("Room " + roomName + " will be deleted. You want to continue?")){

		destroyMucRoom(roomName);

	}

}

function deleteUsersFromRoom(){

	var usersSelected = [];
	$("input:checkbox[name=deleteId]:checked").each(function(){
		usersSelected.push($(this).val());
	});
	console.log("VALUES ARE", usersSelected);

	updateUsersInRoom(globalRoomName, usersSelected, "outcast");
}

function addUsersToRoom(){

	var usersSelected = $("#userValues").val();
	usersSelected = usersSelected.trim();
	console.log("VALUES ARE", usersSelected);

	updateUsersInRoom(globalRoomName, usersSelected, "member");

}

function saveMucRoomOptions(){


	var data = {'data' : globalRoomOptions};
	var request = $.ajax({
		url : 'api/save_multiple_options?room_name=' + globalRoomName + '&data=' + JSON.stringify(globalRoomOptions),
		type: "get",
		success: function(response) {
			//sen request to next page.
			if(response.status == "Success"){
				$('#loadingImg').hide();
				$.notify("Option saved successfully.", "success");
			}
			else{
				$('#loadingImg').hide();
				$.notify("Unable to save data.", "error");
			}

		},
		failure: function(msg)
		{
			$('#loadingImg').hide();
			$.notify("Fetch failed", "error");
		}
	});

}

function saveRoomOptions(){

	$('#loadingImg').show();

	if(globalRoomOptions){

		$.each(globalRoomOptions, function (i, item) {


			var e = document.getElementById(i + 'opt');

			if(item != "true" && item !="false")
			{
				globalRoomOptions[i] = e.value;
			}else{
				globalRoomOptions[i] = e.options[e.selectedIndex].text;
			}


		});

		saveMucRoomOptions();

	}
}

function destroyMucRoom(roomName){

	var request = $.ajax({
		url : 'api/destroy_room?room_name=' + roomName,
		type: "get",
		success: function(response) {
			if(response.status == "Success"){
				$.notify("Room deleted successfully", "success");
				getMucRooms();
			}
			else{
				$.notify("Server error. Failed to delete room.", "error");
			}

		},
		failure: function(msg)
		{		$.notify("Server error. Failed to delete room", "error");
		}
	});

}

