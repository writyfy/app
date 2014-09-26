var functionPath = "functions/functions.php?"
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
	$.post(functionPath + "mode=5", function(data) {
		if(data == '1'){
			$("#dynamicHeaderArea").load("html/headerLoggedIn.html");
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

	$(".tab-content").on("click","#btnNoBookMakeNew",function(){
		newBook();
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
			newBook();
			
		}
		else if(arr[1] == "book"){
			s
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
	$.post(functionPath + "mode=5", function(data) {
		if(data == '1'){
			alert("Already logged in!")
		}else if (data == '0'){
			//setting variables to the respective input boxes
			var usernameVal = $("#usernameInput").val();
			var passwordVal = $("#passwordInput").val();
			$.ajax({
				type: "POST",
				url: functionPath + "mode=4",
				data: { username: usernameVal, password: passwordVal},
				success: function (data1) {
					console.log(data1);
					switch(data1){
						case "1" :
							$("#dynamicHeaderArea").load("html/headerLoggedIn.html");
							loadUserTab("loggingIn");
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
	$.post(functionPath + "mode=5", function(data) {
		if(data == '1'){
			$.post(functionPath + "mode=3",function(data){
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
	 		$(".row").html("");
	 		$("#searchResults").slideDown("fast",function(){
	 			isOpen = true
	 			$("#btnSearchClose").show();
	 			console.log("Query accepted will search");
	 			lastQuery = searchQuery;
	 			//show results of search here
	 		$.getJSON(functionPath + "mode=15&Data=" + searchQuery, function(data) {
			if (data.length > 0){
			for (var i = 0; i < data.length; i++){
				var book = data[i];
				var postContent = ''
				postContent += '<div class="col-md-6 col-md-4">';
				postContent +=	'<div class="thumbnail">'
				postContent += '<img src=" ' + book.BookCoverLocation + '" width="150" height="200" alt="..." />'
				postContent += '<div class="caption">'
				postContent +=  '<h3>' + book.Title + '</h3>'
				postContent += '<h5>' + book.Description + '</h5>'
				postContent += '<h4><span class="glyphicon glyphicon-edit"></span>Likes</h4>'
				postContent += '<h4><span class="glyphicon glyphicon-user"> </span>' + book.CreatorsUsername + '</h4>'
				postContent += '<div class="tags">'
				postContent +=	'<a href="#">'
				postContent += 	'<span class="badge">'+ book.Genre1 + '</span>'
				postContent	+=	'</a></div></div></div></div>'
           

				$(".row").append(postContent);

			}

			} else{
				console.log("No books");
				$('.row').html("<h3>Oh noes, no books were found!</h3>");

			}
			setLocationHash("search/" + query);
		});
	 		});
	 	}
	 		//Closes search results
		$("#btnSearchClose").click(function(){
			setLocationHash("");
		 	$("#btnSearchClose").hide();
		 	$("#searchResults").slideUp("fast",function(){
		 		isOpen = false;
		 	});
		});
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
		  url: functionPath + "mode=10",
		  data: { Username: username, Password1: pass1, Password2: pass2, DOB: dob, Email: email, FirstName: fname, LastName: lname},
		  success: function (data1) {
            console.log(data1);
	            switch(data1){
					case "10" :
						alert("Check your email for a verification email!")
						break;
					default:
						alert("Something went wrong!")
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

function addToBook(title,bookID){
	$.post(functionPath + "mode=5", function(data) {
		if(data == '1'){
			//logged in
				returnTemplate('bookSnippet').done(function(data1){
				//setPageTitle("Edit");
				$('#theModal').modal("show");
				$(".modal-body").append(data1);
				$("#theModalLabel").html("Contribute");
				$('.bookTitleContainer').append(title);
				limitWords("#editor", 200);
				});
		}else if (data == '0'){
			//not logged 
			returnTemplate('editor').done(function(data2){
				returnTemplate('bookSnippet').done(function(data1){
					var modalContent = data1 + data2;
					//setPageTitle("Edit");
					$('#theModal').modal("show");
					$(".modal-body").append(modalContent);
					$("#theModalLabel").html("Contribute")
					$('.bookTitleContainer').append(title);
					limitWords("#editor", 200);
					$("#editor").attr({
						disabled: 'true',
						placeholder: 'Login to contribute!'
					});
					$("#btnSnippetSubmit").attr("disabled","true");
		
				});
			});
		}else{
			alert("Something went wrong!")
		}
	});

}

function newBook(){
		setLocationHash("new");
		returnTemplate('editor').done(function(data2){
		returnTemplate('newBook').done(function(data1){
			var modalContent = data1 + data2;
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
			$(".modal-body").append(modalContent);
			$("#theModalLabel").html("New");
			limitWords("#editor", 3);

		});
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
		$("#userTab").remove();
	});

}

function setPageTitle(title){
	$(document).prop('title', 'Writyfy ' + title);
}

function loadUserBooks(){
		$.getJSON(functionPath + "mode=1&UserID=-1", function(data) {
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
				$("div .bookCompleted").click(function(){
					var bookTitle = ($(this).find(".bookTitle").html());
					readBook(bookTitle);
				});
			} else{
				console.log("No books");
				$('#tabUser .well').html("<h3>Oh noes, you have no books! Create a <a id='btnNoBookMakeNew' class='btn btn-primary btn-xs'> New Book</a> </h3>");

			}
		});

}


function loadBooks(){
	$.getJSON(functionPath + "mode=1&Data=2", function(data) {
		if (data){
		for (var i = 0; i < data.length; i++){
			var book = data[i];
			var postContent = ''
			postContent += '<div class="item bookCompleted" style="width=100%;">';
			postContent += '<a id="bookCover"><img class="lazyOwl" style="width:150px;height:200px;" data-src=' + book.BookCoverLocation + '></a>';
			postContent += '<div class="imgOverlay">';
			postContent +=	'<p class="bookTitle" data-placement="bottom" data-toggle="tooltip" title="' + book.Description +'">' + book.Title + '</p>';
			postContent +=	'</div>';
			postContent +=	'</div>';
			$("#owl-featuredBooks").data('owlCarousel').addItem(postContent);
			$("#owl-recentlyCompleted").data('owlCarousel').addItem(postContent);
			$("#owl-topRated").data('owlCarousel').addItem(postContent);
		}
					//Initialize tooltip for booktitles and set the container to body so that it fills the full area

			$('.bookTitle').tooltip({container: 'body'});
			$("div .bookCompleted").click(function(){
				var bookTitle = ($(this).find(".bookTitle").html());
				readBook(bookTitle);
			});
		} else{
			console.log("No books");
		}

	});
	$.getJSON(functionPath + "mode=1&Data=1", function(data) {
		if(data){
		for (var i = 0; i < data.length; i++){
			var book = data[i];
			var postContent = ''
		 	postContent+= '<div class="item bookInProgress" style="width=100%;">';
			postContent+= '<a id="bookCover"><img class="lazyOwl" style="width:150px;height:200px;" data-src="' + book.BookCoverLocation + '"></a>';
			postContent+= '<div class="imgOverlay">';
			postContent+= '<p class="bookTitle" data-placement="bottom" data-toggle="tooltip" title="' + book.Description + '">' + book.Title+ '</p>';
			postContent+= '<span class="badge">83%</span>';
			postContent+=	'</div>';
			postContent+=	'<h5 ><i class="fa fa-user"></i> moooo</h5>';
			postContent+= '<h5 ><i class="fa fa-users"></i> 502</h5>';
			postContent+= '<h5><span class="badge">' + book.Genre1 + '</span></h5>';
			postContent+=	'</div>';
			$("#owl-newlyStarted").data('owlCarousel').addItem(postContent);
			$("#owl-recentlyUpdated").data('owlCarousel').addItem(postContent);
			$("#owl-nearingCompletion").data('owlCarousel').addItem(postContent);
		}
					//Initialize tooltip for booktitles and set the container to body so that it fills the full area
		
			$('.bookTitle').tooltip({container: 'body'});
			$("div .bookInProgress").click(function(){
				var bookTitle = ($(this).find(".bookTitle").html());
				addToBook(bookTitle);
			});

		}else{
			console.log("No books");
		}

	});

}
