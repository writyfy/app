var functionPath = "functions/functions.php?"
var apiPath = "api.php"
//Search bs that needs moving out of being a global...
var lastQuery;
var isOpen = false;

$(document).ready(function(){

	if (window.location.hash != "") {
		console.log("attempting to load" + window.location.hash.substring(2));
		loadHashedPage(window.location.hash.substring(2));
	} else {
		
	}
	loadBooks();
	//Checking if logged in to dertermine if to show head login box or nav
	$.post(apiPath + "/core/valid_user/", function(data) {
		if(data == '1'){
			$("#dynamicHeaderArea").load("html/headerLoggedIn.html");
			loadNotifications();
			loadUserTab();
			loadUserBooks();
		}else if (data == '0'){
			$("#dynamicHeaderArea").load("html/headerLoggedOut.html");
			hideUserTab();
		}else{ 
			alert("Something went wrong!");
		}
	});

	//General Things for handling modals
	$('#theModal').on('hide.bs.modal', function (e) {
		setLocationHash("");
		$('.modal-body').html("");
		$("html").css("margin-right", "0px");

	});
	$('#theModal').on('show.bs.modal', function (e) {
		 $("html").css("margin-right", "-16px");
	});

	/////Book Shelf///
	$(".shelf").owlCarousel({
	    autoPlay : true,
	    stopOnHover : true,
	    lazyLoad : true
	});



	//Add a tooltip to the search input field when in focus
	$('#searchInput').tooltip({'trigger':'focus', 'title': 'Titles, Genres and Keywords'});

	//Set cursor to pointer when hovering over book title
	$('.bookTitle').css( 'cursor', 'pointer' );
	//Initialzing bootstrap tabs for In progress and Completed
	$('#tabs').tab();
	//Prevent scrolling down of fullpage when scrolling through search results
	function preventScroll (textbox){
		//#searchResults
		$("#" + textbox).bind('mousewheel', function(e) {
		    if(e.originalEvent.wheelDelta < 0 && 
		    ($(this).scrollTop() + $(this).height()) >= this.scrollHeight) return false;
		});
	}

	preventScroll("searchQueresults");
	preventScroll("editor");

	//Opens search results, will contain search functions when added in php

	//Need to add on enter key pressed
	$("#btnSearch").click(function(){
		doSearch($('#searchInput').val());
	});

	$(document).keypress(function(e) {
		//Handling for enter key pressed
		if(e.which == 13){
		   	//For logging in
		    if($("#usernameInput").is(":focus") || $("#passwordInput").is(":focus")  ) {
		    LogIn();
		    }
		    if($("#searchInput").is(":focus")  ) {
		    	doSearch($('#searchInput').val());
		    }
		}
	});


	//The dynamic area requires .on apposed to .click to select the button
	$("#dynamicHeaderArea").on("click","#btnLogin",function(e){
		LogIn();
	});	

	$("#dynamicHeaderArea").on("click","#btnLogout",function(){
		logOut();
	});

	$("#dynamicHeaderArea").on("click","#btnRegister",function(){
		register();
	});

	$("#dynamicHeaderArea").on("click","#btnSettings",function(){
		settings();
	});

	$("#dynamicHeaderArea").on("click","#btnNew",function(){
		newBook();
	});
	$("#dynamicHeaderArea").on("click","#btnNotifcations",function(){
		loadNotifications();
	});

	$(".tab-content").on("click","#btnNoBookMakeNew",function(){
		newBook();
	});
	/*$("#flipbook").turn({
	  width: 400,
	  height: 300,
	  autoCenter: true
	});*/

	workInProgress();

	$(".tab-content").on("click",".Read",function(){
			//load book reader
		openBookReader($(this).attr('id'));
	});
//End On Doc Ready	
});


//View Control Stuff
var currentPage	= ""
function loadHashedPage(newPage) {
	var arr = newPage.split("/");
	console.log(arr);
	console.log("atempting to load: " + newPage);
	if (currentPage != newPage) {
		console.log("read as: " + newPage.substring(1,7));
		if(arr[1] == "search"){
			doSearch(arr[2]);
			//setLocationHash(newPage + arr[2]);
		}
		else if(arr[1] == "new"){

				$.post(apiPath + "/core/valid_user/", function(data) {
				if(data == '1'){
					//logged in
					newBook();
				}
				else if (data == '0'){
					//not logged 
					//must be logged in
					alert("You must be logged in to access this page");
					setLocationHash("");
	
				}else{
					alert("Something went wrong!")
				}
			});
			
		}
		else if(arr[1] == "book"){
			
		}


		currentPage = newPage;

	}
}
var allowHashToUpdateApp = true;

function getLocationHash() {
	console.log("the location hash is: " + window.location.hash.substring(2));
	return window.location.hash.substring(2);
}

function setLocationHash(str) {
	allowHashToUpdateApp = false;
	console.log("String to set: " + str);
	window.location.hash = "!/" + str ;
}
window.onhashchange = function(e) {
	if (allowHashToUpdateApp) {
		loadHashedPage(getLocationHash());
	} else {
		allowHashToUpdateApp = true;
	}
};
window.onload = function() {
	var hashValue = getLocationHash();
	if (hashValue) {
		loadHashedPage(hashValue);
	}
};
//

function LogIn(){
	$.post(apiPath + "/core/valid_user/", function(data) {
		if(data == '1'){
			alert("Already logged in!")
		}else if (data == '0'){
			//setting variables to the respective input boxes
			var usernameVal = $("#usernameInput").val();
			var passwordVal = $("#passwordInput").val();
			$.ajax({
				type: "POST",
				url: apiPath + "/core/login/",
				data: { username: usernameVal, password: passwordVal},
				success: function (data1) {
					console.log(data1);
					switch(data1){
						case "1" :
							$("#dynamicHeaderArea").load("html/headerLoggedIn.html");
							loadUserTab("loggingIn");
							loadNotifications();
							break;
						case "2" :
							$(".loginBit").addClass("has-error");
							alert("User and Password combination not correct");

							break;
						case "3" :
							alert("Account has not been verified");
							break;
						default:
							alert("Something went wrong!");
							break;
					}
				}
			});
		}else{
			alert("Something went wrong!");
		}
	});
}
function logOut(){
	$.post(apiPath + "/core/valid_user/", function(data) {
		if(data == '1'){
			$.post(apiPath + "/core/logout/",function(data){
				switch(data){
					case "0":
						alert("Logout Failed");
						break;
					case "1":
						$("#dynamicHeaderArea").load("html/headerLoggedOut.html");
						hideUserTab();
						break;
					default:
						alert("Something went wrong!");
						break;
				}
			});
		}else if (data == '0'){
			alert("Already logged out!")
		}else{
			alert("Something went wrong!");
		}
	});
}

function returnTemplate(filename){
	return $.get( "html/" + filename + ".html", function(data) { 
		});
}

	function doSearch(query){
		if($("#searchInput").val() == ""){
			$("#searchInput").val(query);
		}
		console.log("the query was" + query);
	 	var searchQuery = query
	 	if(searchQuery == ""){
	 		console.log("searchQuery doesnt contain anything therefore .:. bad boy");
	 		//Space for validation
	 	}else if(searchQuery == lastQuery && isOpen){
	 		console.log("searchQuery is the same as the last one searched .:. bad boy ");
	 		//Space for validation
	 	}else{
	 		//Something prettier needs doing here

			returnTemplate("searchTab").done(function(data1){
		$(".tab-content").append(data1);
		$("#tabSearch .shelf").owlCarousel({
		    autoPlay : true,
		    stopOnHover : true,
		    lazyLoad : true
		});
		if($("#tbSearch")[0]){
			$("#tbSearch").remove();
		}
		$('#tabs').append("<li style='display:none;' id='tbSearch'><a href='#tabSearch' data-toggle='tab'><button class='close closeTab' id='closeSearch' type='button'>x</button>Search: " + query + "</a></li>");
		//Was gonna use slideUp here but it doesnt work due to positioning.
		$("#tbSearch").fadeIn(function(){
			$('#tbSearch a[href="#tabSearch"]').tab('show');
				$("#closeSearch").click(function(e){
					e.preventDefault();
					setLocationHash("");
					
					$("#tbSearch").fadeOut(function(){
						$("#tbSearch").remove();
						$("#tabSearch").remove();
					});
					if($("#tbSearch").hasClass("active")){
						$('#tbInProgress a[href="#tabInProgress"]').tab('show');
					}
					
				});
						


		});
	});


	 			//show results of search here
	 	$.getJSON(apiPath + "/search/field/" + searchQuery, function(data) {
			if (data.length > 1){
				//$("#owl-SearchResults").data('owlCarousel').destroy();
			for (var i = 0; i < data.length; i++){
				var book = data[i];
				var postContent = ''
				postContent += '<div>';
				postContent += '<div>';
				postContent += "<figure class='book'>";
				postContent += "<ul class='hardcover_front'>";
				postContent += "<li>";
				postContent += '<img src="'+book.BookCoverLocation+'" alt="" width="100%" height="100%">';
				postContent += "</li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='page'>";
				postContent += "<li></li>";
				postContent += "<li>";
				postContent += '<center><a class="bookPlacerButton" href="#" id='+book.ID+'>Read</a>';
				postContent += '<a class="bookProfile bookPlacerButton" href="#" id='+book.ID+'>Book Profile</a></center>';
				postContent += "</li>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='hardcover_back'>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='book_spine'>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "</figure>";
				postContent += '</div>';
				postContent += '<div>';
				postContent += '<h5 ><i class="fa fa-user"></i> ' + book.CreatorsUsername + '</h5>';
				postContent += '<h5 ><i class="fa fa-users"></i> ' + book.Likes + ' </h5>';
				postContent += '<h5><span class="badge" id="genre">' + book.Genre1 + '</span></h5>';
				postContent += '<h5 ><span class="badge" id="genre">' + book.Genre2 + '</span></h5>';
				postContent += '<h5 ><span class="badge" id="genre">' + book.Genre3 + '</span></h5>';
				postContent += '</div>';
				postContent += '</div>';
				$("#owl-SearchResults").data('owlCarousel').addItem(postContent);
			}
						//Initialize tooltip for booktitles and set the container to body so that it fills the full area
				$('.bookTitle').tooltip({container: 'body'});
				$(".bookProfile").click(function(){
					loadBookProfile($(this).attr('id'));
				});
			} else{
				console.log("No books");
				//$('#tabSearch .well').html("<h3>Oh noes, you have no books! Create a <a id='btnNoBookMakeNew' class='btn btn-primary btn-xs'> New Book</a> </h3>");
			}
			setLocationHash("search/" + query);
		});
	
	 	}
	}

function register(username,password){
	returnTemplate('register').done(function(data1){
		$('#theModal').modal("show");
		$(".modal-body").append(data1);
		$("#theModalLabel").html("Register")
		$("#registerUsername").val($("#usernameInput").val());
		//needs fixing
		$('#registerDOB',this).datepicker({
                    format: "dd/mm/yyyy"
                });  

	});

	$('#theModal').on('shown.bs.modal', function (e) {
		$("#registerUsername").focus();
		$("#btnRegister").click(function(){
		var username = $("#registerUsername").val();
		var fname = $("#registerFname").val();
		var lname = $("#registerLname").val();
		var email = $("#registerEmail").val();
		var dob = $("#registerDOB").val();
		var pass1 = $("#registerPassword").val();
		var pass2 = $("#registerConPassword").val();
		$.ajax({
		  type: "POST",
		  url: apiPath + "/core/register/",
		  data: { Username: username, Password1: pass1, Password2: pass2, DOB: dob, Email: email, FirstName: fname, LastName: lname},
		  success: function (data1) {
            console.log(data1);
	            switch(data1){
	            	case "0":
	            		alert("Please enter a Username");
	            		break
	            	case "1":
	            		alert("Please enter a Email");
						break;
	            	case "2":
	            		alert("Please enter a DOB");
						break;
	            	case "3":
	            		alert("Please enter the first password");
						break;
	            	case "4":
	            		alert("Please enter the seccond password");
						break;
	            	case "5":
	            		alert("Please enter a first name");
						break;
	            	case "6":
	            		alert("Please enter a last name");
						break;
	            	case "7":
	            		alert("Passwords do not match!");
						break;
	            	case "8":
	            		alert("This username already exist");
						break;
	            	case "9":
	            		alert("This email already exist");
						break;
					case "10" :
						alert("Check your email for a verification email!");
						break;
					default:
						alert("Something went wrong!");
				}
            }
		});
			console.log("Register clicked");
		});

	});

}

function settings(userID){
	setLocationHash("settings");
	returnTemplate('settings').done(function(data1){
		$('#theModal').modal("show");
		$(".modal-body").append(data1);
		$("#theModalLabel").html("Settings")
		//load all users deets based upon the userID passed
		//poo that into the template
		$("#btnEdit").click(function(){
		 $(".form-control").prop('disabled',false);
		});
	});
}
/*
	$.post(functionPath + "mode=5", function(data) {
		if(data == '1'){
			//logged in
		}else if (data == '0'){
			//not logged 
		}else{
			alert("Something went wrong!")
		}
	});
*/

function addToBook(bookID){
	$.post(apiPath + "/core/valid_user/", function(data) {
		if(data == '1'){
			setLocationHash("book/" + bookID);
			//logged in
				returnTemplate('bookSnippet').done(function(data1){
				//setPageTitle("Edit");
				$('#theModal').modal("show");
				$(".modal-body").append(data1);
				$("#theModalLabel").html("Contribute");
				$("#btnContribute").click(function(){
					console.log("moo");
					$.getJSON(apiPath + "/books/" + bookID, function(data2){
						console.log(data2);
						$("#btnContribute").slideUp("fast", function(){
							returnTemplate('editor').done(function(data3){
								$(".panel-body").append(data3);
							});

						});
					});
				});
				});
		}else if (data == '0'){
			//not logged 
				returnTemplate('bookSnippet').done(function(data1){
					//setPageTitle("Edit");
					$('#theModal').modal("show");
					$(".modal-body").append(data1);
					$("#theModalLabel").html("Contribute")
					$("#btnContribute").attr({
						disabled: 'true',
					});
		
				});
		}else{
			alert("Something went wrong!")
		}
	});

}

function workInProgress(){

	setLocationHash("notification");
	returnTemplate('workInProgress').done(function(data1){
		$('#theModal').modal("show");
		$(".modal-body").append(data1);
		$("#theModalLabel").html("Message from Server");
		//load all users deets based upon the userID passed
		//poo that into the template
		$("#btnEdit").click(function(){
		 $(".form-control").prop('disabled',false);
		});
	});

}

function newBook(){
		setLocationHash("new");
		returnTemplate('newBook').done(function(data1){
			//setPageTitle("Edit");
			$('#theModal').modal("show");
			$('#theModal').on('shown.bs.modal', function () {
			  $('.chosen-select', this).chosen();
			  $( ".slider",this).slider({
			  	min : 50,
			  	max : 500,
			  	step : 50
			  	});
			});
			$(".modal-body").append(data1);
			$("#theModalLabel").html("New");
			limitWords("#editor", 3);

		});

}

function loadNotifications(){
		$.getJSON(apiPath + "/core/notifications/6", function(data) {
			if (data.length > 1){
				var postContent = ''
				for (var i = 0; i < data.length; i++){
					var note = data[i];
					postContent += '<li role="presentation"><a class="miniNotification" role="menuitem" tabindex="-1" href="#" mode="'+note.Mode+'" BookID="'+note.BookID+'" UserID="'+note.UserID+'">' + note.Message + '</a></li>';
				}
				$(".page-header").on("click",".miniNotification",function(){
					console.log($(this).attr('mode')+" "+$(this).attr('bookid'));
					switch($(this).attr('mode')){
						case "1":
						//go to book
						loadBookProfile($(this).attr('bookid'));
						break;
						case "2":
						//go to user
						break;
						case "3":
						//go to book
						loadBookProfile($(this).attr('bookid'));
						break;
						case "4":
						//go to book
						loadBookProfile($(this).attr('bookid'));
						break;
					}
				});
				$("#btnNotifcations").html(postContent);
			} else{
				console.log("No Notifications");

			}
		});
}
function readBook(title,bookID){
	returnTemplate('book').done(function(data1){
		$('#theModal').modal("show");
		$(".modal-body").append(data1);
		$("#theModalLabel").html("Read")
		$('.bookTitleContainer').append(title);
	});
}
/*	Old new //these need to load after each other somehow
	loadEditor();
	loadSnippetTemplate();
	$('.bookTitleContainer').append(title);
	limitWords("#editor", 200);
	$('#theModal').modal("show");
	*/
	/* OLD

*/


//This needs to happen after the user has loggen in
function loadUserTab(logging){
	returnTemplate("userTab").done(function(data1){
		$(".tab-content").append(data1);
		$("#tabUser .shelf").owlCarousel({
		    autoPlay : true,
		    stopOnHover : true,
		    lazyLoad : true
		});

		$('#tabs').append("<li style='display:none;' id='userTab'><a href='#tabUser' data-toggle='tab'>Username</a></li>");
		//Was gonna use slideUp here but it doesnt work due to positioning.
		$("#userTab").fadeIn(function(){
			if(logging == "loggingIn"){
				$('#userTab a[href="#tabUser"]').tab('show');		
				loadUserBooks();	
			}

		});
	});
}

function hideUserTab(){
	if($("#tabUser").hasClass("active")){
		$('#tbInProgress a[href="#tabInProgress"]').tab('show');
	}
	$("#userTab").fadeOut(function(){
		$("#owl-userAllBooks").data('owlCarousel').removeItem();
		$("#owl-userCompleted").data('owlCarousel').removeItem();
		$("#owl-userNearingCompletion").data('owlCarousel').removeItem();
		$("#userTab").remove();
	});

}

function setPageTitle(title){
		$.getJSON(apiPath + "/search/user/-1", function(data) {
			if (data.length > 1){
			for (var i = 0; i < data.length; i++){
				var book = data[i];
				var postContent = ''
				postContent += '<div class="item bookCompleted" style="width=100%;">';
				postContent += '<a id="bookCover"><img style="width:150px;height:200px;" src=' + book.BookCoverLocation + '></a>';
				postContent += '<div class="imgOverlay">';
				postContent +=	'<p class="bookTitle" data-placement="bottom" data-toggle="tooltip" title="' + book.Description +'">' + book.Title + '</p>';
				postContent +=	'</div>';
				postContent +=	'</div>';
				$("#owl-userAllBooks").data('owlCarousel').addItem(postContent);
				$("#owl-userCompleted").data('owlCarousel').addItem(postContent);
				$("#owl-userNearingCompletion").data('owlCarousel').addItem(postContent);
			}
						//Initialize tooltip for booktitles and set the container to body so that it fills the full area

				$('.bookTitle').tooltip({container: 'body'});
				/*$("div .bookCompleted").click(function(){
					var bookID = ($(this).attr('id'));
					addToBook(bookID);
				});*/
			} else{
				console.log("No books");
				$('#tabUser .well').html("<h3>Oh noes, you have no books! Create a <a id='btnNoBookMakeNew' class='btn btn-primary btn-xs'> New Book</a> </h3>");

			}
		});

}

function loadUserBooks(){
		$.getJSON(apiPath + "/search/user/-1", function(data) {
			if (data.length > 1){
			for (var i = 0; i < data.length; i++){
				var book = data[i];
				var postContent = ''
				postContent += '<div>';
				postContent += '<div>';
				postContent += "<figure class='book'>";
				postContent += "<ul class='hardcover_front'>";
				postContent += "<li>";
				postContent += '<img src="'+book.BookCoverLocation+'" alt="" width="100%" height="100%">';
				postContent += "</li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='page'>";
				postContent += "<li></li>";
				postContent += "<li>";
				postContent += '<center><a class="bookPlacerButton" href="#" id='+book.ID+'>Read</a>';
				postContent += '<a class="bookProfile bookPlacerButton" href="#" id='+book.ID+'>Book Profile</a></center>';
				postContent += "</li>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='hardcover_back'>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='book_spine'>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "</figure>";
				postContent += '</div>';
				postContent += '<div>';
				postContent += '<h5 ><i class="fa fa-user"></i> ' + book.CreatorsUsername + '</h5>';
				postContent += '<h5 ><i class="fa fa-users"></i> ' + book.Likes + ' </h5>';
				postContent += '<h5><span class="badge" id="genre">' + book.Genre1 + '</span></h5>';
				postContent += '<h5 ><span class="badge" id="genre">' + book.Genre2 + '</span></h5>';
				postContent += '<h5 ><span class="badge" id="genre">' + book.Genre3 + '</span></h5>';
				postContent += '</div>';
				postContent += '</div>';
				$("#owl-userAllBooks").data('owlCarousel').addItem(postContent);
			}
						//Initialize tooltip for booktitles and set the container to body so that it fills the full area

				$('.bookTitle').tooltip({container: 'body'});
				$(".bookProfile").click(function(){
					loadBookProfile($(this).attr('id'));
				});
			} else{
				console.log("No books");
				$('#tabUser .well').html("<h3>Oh noes, you have no books! Create a <a id='btnNoBookMakeNew' class='btn btn-primary btn-xs'> New Book</a> </h3>");

			}
		});


		$.getJSON(apiPath + "/search/user/-1/c", function(data) {
			if (data.length > 1){
			for (var i = 0; i < data.length; i++){
				var book = data[i];
				var postContent = ''
				postContent += '<div>';
				postContent += '<div>';
				postContent += "<figure class='book'>";
				postContent += "<ul class='hardcover_front'>";
				postContent += "<li>";
				postContent += '<img src="'+book.BookCoverLocation+'" alt="" width="100%" height="100%">';
				postContent += "</li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='page'>";
				postContent += "<li></li>";
				postContent += "<li>";
				postContent += '<center><a class="bookPlacerButton" href="#" id='+book.ID+'>Read</a>';
				postContent += '<a class="bookProfile bookPlacerButton" href="#" id='+book.ID+'>Book Profile</a></center>';
				postContent += "</li>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='hardcover_back'>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='book_spine'>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "</figure>";
				postContent += '</div>';
				postContent += '<div>';
				postContent += '<h5 ><i class="fa fa-user"></i> ' + book.CreatorsUsername + '</h5>';
				postContent += '<h5 ><i class="fa fa-users"></i> ' + book.Likes + ' </h5>';
				postContent += '<h5><span class="badge" id="genre">' + book.Genre1 + '</span></h5>';
				postContent += '<h5 ><span class="badge" id="genre">' + book.Genre2 + '</span></h5>';
				postContent += '<h5 ><span class="badge" id="genre">' + book.Genre3 + '</span></h5>';
				postContent += '</div>';
				postContent += '</div>';
				$("#owl-userCompleted").data('owlCarousel').addItem(postContent);
			}
						//Initialize tooltip for booktitles and set the container to body so that it fills the full area

				$('.bookTitle').tooltip({container: 'body'});
				$(".bookProfile").click(function(){
					loadBookProfile($(this).attr('id'));
				});
			} else{
				console.log("No books");
				$('#tabUser .well').html("<h3>Oh noes, you have no books! Create a <a id='btnNoBookMakeNew' class='btn btn-primary btn-xs'> New Book</a> </h3>");

			}
		});


		$.getJSON(apiPath + "/search/user/-1/s", function(data) {
			if (data.length > 1){
			for (var i = 0; i < data.length; i++){
				var book = data[i];
				var postContent = ''
				postContent += '<div>';
				postContent += '<div>';
				postContent += "<figure class='book'>";
				postContent += "<ul class='hardcover_front'>";
				postContent += "<li>";
				postContent += '<img src="'+book.BookCoverLocation+'" alt="" width="100%" height="100%">';
				postContent += "</li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='page'>";
				postContent += "<li></li>";
				postContent += "<li>";
				postContent += '<center><a class="bookPlacerButton" href="#" id='+book.ID+'>Read</a>';
				postContent += '<a class="bookProfile bookPlacerButton" href="#" id='+book.ID+'>Book Profile</a></center>';
				postContent += "</li>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='hardcover_back'>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "<ul class='book_spine'>";
				postContent += "<li></li>";
				postContent += "<li></li>";
				postContent += "</ul>";
				postContent += "</figure>";
				postContent += '</div>';
				postContent += '<div>';
				postContent += '<h5 ><i class="fa fa-user"></i> ' + book.CreatorsUsername + '</h5>';
				postContent += '<h5 ><i class="fa fa-users"></i> ' + book.Likes + ' </h5>';
				postContent += '<h5><span class="badge" id="genre">' + book.Genre1 + '</span></h5>';
				postContent += '<h5 ><span class="badge" id="genre">' + book.Genre2 + '</span></h5>';
				postContent += '<h5 ><span class="badge" id="genre">' + book.Genre3 + '</span></h5>';
				postContent += '</div>';
				postContent += '</div>';
				$("#owl-userNearingCompletion").data('owlCarousel').addItem(postContent);
			}
						//Initialize tooltip for booktitles and set the container to body so that it fills the full area

				$('.bookTitle').tooltip({container: 'body'});
				$(".bookProfile").click(function(){
					loadBookProfile($(this).attr('id'));
				});
			} else{
				console.log("No books");
				$('#tabUser .well').html("<h3>Oh noes, you have no books! Create a <a id='btnNoBookMakeNew' class='btn btn-primary btn-xs'> New Book</a> </h3>");

			}
		});

}


function loadBooks(){
	$.getJSON(apiPath + "/search/r/c", function(data) {
		if (data){
		for (var i = 0; i < data.length; i++){
			var book = data[i];
			var postContent = ''
			postContent += '<div>';
			postContent += '<div>';
			postContent += "<figure class='book'>";
			postContent += "<ul class='hardcover_front'>";
			postContent += "<li>";
			postContent += '<img src="'+book.BookCoverLocation+'" alt="" width="100%" height="100%">';
			postContent += "</li>";
			postContent += "<li></li>";
			postContent += "</ul>";
			postContent += "<ul class='page'>";
			postContent += "<li></li>";
			postContent += "<li>";
			postContent += '<center><a class="Read bookPlacerButton" href="#" id='+book.ID+'>Read</a>';
			postContent += '<a class="bookProfile bookPlacerButton" href="#" id='+book.ID+'>Book Profile</a></center>';
			postContent += "</li>";
			postContent += "<li></li>";
			postContent += "<li></li>";
			postContent += "<li></li>";
			postContent += "</ul>";
			postContent += "<ul class='hardcover_back'>";
			postContent += "<li></li>";
			postContent += "<li></li>";
			postContent += "</ul>";
			postContent += "<ul class='book_spine'>";
			postContent += "<li></li>";
			postContent += "<li></li>";
			postContent += "</ul>";
			postContent += "</figure>";
			postContent += '</div>';
			postContent += '<div>';
			postContent += '<h5 ><i class="fa fa-user"></i> ' + book.CreatorsUsername + '</h5>';
			postContent += '<h5 ><i class="fa fa-users"></i> ' + book.Likes + ' </h5>';
			postContent += '<h5><span class="badge" id="genre">' + book.Genre1 + '</span></h5>';
			postContent += '<h5 ><span class="badge" id="genre">' + book.Genre2 + '</span></h5>';
			postContent += '<h5 ><span class="badge" id="genre">' + book.Genre3 + '</span></h5>';
			postContent += '</div>';
			postContent += '</div>';

			$("#owl-featuredBooks").data('owlCarousel').addItem(postContent);
			$("#owl-recentlyCompleted").data('owlCarousel').addItem(postContent);
			$("#owl-topRated").data('owlCarousel').addItem(postContent);
		}
					//Initialize tooltip for booktitles and set the container to body so that it fills the full area

			$('.bookTitle').tooltip({container: 'body'});
			$(".genreButton").click(function(){
				var search = ($(this).attr('id'));
				doSearch(search);
			});
			$(".usernameButton").click(function(){
				var username = ($(this).attr('id'));
				loadUsersProfileTab(username);
			});
			$(".bookProfile").click(function(){
				loadBookProfile($(this).attr('id'));
			});
		} else{
			console.log("No books");
		}

	});
	$.getJSON(apiPath + "/search/r/w", function(data) {
		if(data){
		for (var i = 0; i < data.length; i++){
			var book = data[i];
			var postContent = ''
			postContent += '<div>';
			postContent += '<div>';
			postContent += "<figure class='book'>";
			postContent += "<ul class='hardcover_front'>";
			postContent += "<li>";
			postContent += '<img src="'+book.BookCoverLocation+'" alt="" width="100%" height="100%">';
			postContent += "</li>";
			postContent += "<li></li>";
			postContent += "</ul>";
			postContent += "<ul class='page'>";
			postContent += "<li></li>";
			postContent += "<li>";
			postContent += '<center><a class="Read bookPlacerButton" href="#" id='+book.ID+'>Read</a>';
			postContent += '<a class="bookProfile bookPlacerButton" href="#" id='+book.ID+'>Book Profile</a></center>';
			postContent += "</li>";
			postContent += "<li></li>";
			postContent += "<li></li>";
			postContent += "<li></li>";
			postContent += "</ul>";
			postContent += "<ul class='hardcover_back'>";
			postContent += "<li></li>";
			postContent += "<li></li>";
			postContent += "</ul>";
			postContent += "<ul class='book_spine'>";
			postContent += "<li></li>";
			postContent += "<li></li>";
			postContent += "</ul>";
			postContent += "</figure>";
			postContent += '</div>';
			postContent += '<div>';
			postContent += '<h5 ><i class="fa fa-user"></i> ' + book.CreatorsUsername + '</h5>';
			postContent += '<h5 ><i class="fa fa-users"></i> ' + book.Likes + ' </h5>';
			postContent += '<h5><span class="badge" id="genre">' + book.Genre1 + '</span></h5>';
			postContent += '<h5 ><span class="badge" id="genre">' + book.Genre2 + '</span></h5>';
			postContent += '<h5 ><span class="badge" id="genre">' + book.Genre3 + '</span></h5>';
			postContent += '</div>';
			postContent += '</div>';
			$("#owl-newlyStarted").data('owlCarousel').addItem(postContent);
			$("#owl-recentlyUpdated").data('owlCarousel').addItem(postContent);
			$("#owl-nearingCompletion").data('owlCarousel').addItem(postContent);
		}
					//Initialize tooltip for booktitles and set the container to body so that it fills the full area
		
			$('.bookTitle').tooltip({container: 'body'});
			$(".bookProfile").click(function(){
				loadBookProfile($(this).attr('id'));
			});
		}else{
			console.log("No books");
		}

	});

}





//Johns new js shiz


function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function loadUsersProfileTab(username){

	
}


function loadBookProfile(id){
	console.log("Attempting to load book with ID:"+id);
	var bookID = (id);
	$.getJSON(apiPath + "/books/"+bookID, function(data) {
		if(data){
			if($("#tbBookHome"+data.ID)[0]){
				$("#tbBookHome"+data.ID).remove();
			}
			var html = '';
			html += '<div id="tabBookHome'+data.ID+'" class="tab-pane">';
			html += '<h2>'+data.Title+' by '+data.CreatorsUsername+'</h2>';
			html += '<div class="well">';
			html += '<div class="row">'
			html += '<div class="col-md-6">'
			html += '<div class="left">';
			html += '<a class="item" id="' + data.ID +'"><img style="width:450px;height:600px;" src="' + data.BookCoverLocation + '"></a>';
			html += '</div>';
			html +=  '</div>'
			html += '<div class="col-md-6">'
			html += '<div class="right">';
			html += '<p>Description: '+data.Description+'</p>';
			html += '<p>Author: '+data.CreatorsUsername+'</p>';
			html += '<input class="btn btn-primary btn-lg btn-block Read" id="1" type="submit" value="Read">';
			html += '</div>';
			html += '</div>'
			html += '</div>'
			html += '</div>';
			html += '</div>';
			if($("#tabBookHome"+data.ID)[0]){
				$("#tabBookHome"+data.ID).remove();
			}
			$(".tab-content").append(html);
			if($("#tbBookHome"+data.ID)[0]){
				$("#tbBookHome"+data.ID	).remove();
			}
			$('#tabs').append("<li style='display:none;' id='tbBookHome"+data.ID+"'><a href='#tabBookHome"+data.ID+"' data-toggle='tab'><button class='close closeTab' id='closeBookPreview"+data.ID+"' type='button'>x</button>"+data.Title+"</a></li>");
			//Was gonna use slideUp here but it doesnt work due to positioning.
			$("#tbBookHome"+data.ID).fadeIn(function(){
				$('#tbBookHome'+data.ID+' a[href="#tabBookHome'+data.ID+'"]').tab('show');
				$("#closeBookPreview"+data.ID).click(function(e){
					e.preventDefault();
					setLocationHash("");
					
					$("#tbBookHome"+data.ID).fadeOut(function(){
						$("#tbBookHome"+data.ID).remove();
						$("#tabBookHome"+data.ID).remove();
					});
					if($("#tbBookHome"+data.ID).hasClass("active")){
						$('#tbInProgress a[href="#tabInProgress"]').tab('show');
					}
					
				});
			});
		}else{
			console.log("No book data");
		}
	});
}



function openBookReader(ID){
	$.getJSON(apiPath + "/books/"+ID, function(data) {
		if($("#bookReadID")[0]){
				$("#bookReadID").remove();
			}
		var bookReader ='';
		bookReader +='<div id="bookReadID"> ';
		bookReader +='<div class="panel panel-default" style="width=1000px;">';
		bookReader +='<div class="panel-body">';

		bookReader +='<center>';
		bookReader +='<div id="mybook">';
		bookReader +='<div class="bookCover item"><div><img style="width:100%;height:100%;"src= "'+data.BookCoverLocation+'"></div></div>';
		bookReader +='<div class="bookCoverBack" ></div>';

		bookReader +='<div class="pageCover"> This is the page 1 </div>';
		bookReader +='<div class="pageCover"> This is the page 2 </div>';
		bookReader +='<div class="pageCover"> This is the page 3 </div>';
		bookReader +='<div class="pageCover"> the end </div>';

		
		bookReader +='<div class="bookCoverBack" ></div>';
		bookReader +='<div class="bookCoverBack" ></div>';
		bookReader +='</div>';
		bookReader +='</center>';

		bookReader +='</div>';
		bookReader +='</div>';
		bookReader +='</div>';


		$('#bookContanerModal').modal("show");
		$(".bookContaner").append(bookReader);
		console.log(screenWidth()+" "+screenHeight());
		var ratio = 1.5;
		$('#mybook').wowBook({
	      height : calcModalBookHeight(),
	      hardcovers : true,
	      width  : calcModalBookWidth(),
	      centeredWhenClosed : true
	    });

	});
}



function screenWidth(){
	return screen.width;
}
function screenHeight(){
	return screen.height;
}
function calcModalBookWidth(){
	return(screenWidth()/20)*12;
}
function calcModalBookHeight(){
	return ((calcModalBookWidth()/2)/3)*4;
}

