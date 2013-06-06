$(document).ready(function(){
	//INIT=========================
	//Setup Ajax Object
	$.ajaxSetup({
		url:'php/pages/loginPage/gateway.php',
		type:'POST'
	});
	
	//Close Reg section in order to have only one opened at a time
	$('div#div_reg').slideToggle('fast');
	
	//CLICK EVENTS =================
	
	//Remove: Show graph button
	$("#btn_graph").click(function(){
		chart = new DCChart("graph","php/pages/normalLanding/graphCustomQueries.php?yAxisValues=ADW,ARSON,BURGLARY&xAxisValues=DATE&fromDate=09/13/2011&toDate=10/21/2011&wardID=undefined");
	});
	
	//Toggle sections
	$("div.title").click(function(){
		$('div#div_reg').slideToggle('fast');
		$('div#div_log').slideToggle('fast'); // Rahul -> Armando, this line is redundant, isn't it?
	});
	
	//Login button clicked: call right function
	$("#log").click(function(){
		log_click();
	});
	
	//Registration button clicked: call right function
	$("#reg").click(function(){
		reg_click();
	});
	
	//When ENTER key is pressed in the LOGIN form
	$(".form_log").keypress(function(ev){
		if(ev.which == 13){
			$("#log").focus();
			log_click();
		}
	});
	
	//When ENTER key is pressed in the REGISTRATION form
	$(".form_reg").keypress(function(ev){
		if(ev.which == 13){
			$("#reg").focus();
			reg_click();
		}
	});
	
	//Function when Log button is clicked
	function log_click(){
		$("#results").html("Connecting...");
		
		//Ajax for logging in.			
		var email = $('#log_email').val();
		var password = $('#log_password').val();
		
		//SHOULD use Regex check!
		if(email.length == 0 || password.length == 0){
			alert("Error: Login credentials (Email/Password) needed.");
			$("#results").html("");
			return;
		}
		$.ajax({
			data: {
				'type':'log',
				'email':''+email,
				'password':''+password},
			success:function(d,s,x){
				$("#results").html("");
				if(d == "error"){
					alert("Error: Email/Password combination does not exists");
				}else{
					window.location=""+d;
				}
			}
		});
	}
	//Function when Reg button is clicked
	function reg_click(){
		$("#results").html("Connecting...");
	
		//Ajax for registration.
		var name = $('#reg_name').val();
		var address = $('#reg_address').val();
		var email = $('#reg_email').val();
		var password = $('#reg_password').val();

		//SHOULD use Regex check!
		if(name.length == 0 || address.length == 0 || email.length == 0 || password.length == 0){
			alert("Error: One or more fields are empty. All fields are required to be filled.");
			$("#results").html("");
			return;
		}
		
		$.ajax({
			data: {
				'type':'reg',
				'name':''+name,
				'address':''+address,
				'email':''+email,
				'password':''+password},
			success:function(d,s,x){
				$("#results").html("");
				if(d == "registered"){
					alert("User Registered. Login with your email/password combination now.");
				}else{
					alert("Error: User already registered.");
				}
			}
		});
	}
});