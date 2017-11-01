/**
 * Script for login form toggle animation and handle cognito API call 
 */

(function( $ ) {
		
	/******************************************
	 * Toggle animation script
	 ******************************************/	
	 
	$(document).on('click','.aws-cognito-login-toggle-button',function(){
	  $('.aws-cognito-login-toggle').animate({
			height: "toggle",
			opacity: "toggle"
		  }, "slow");
	});
	$(document).on('click','.aws-cognito-forgot-toggle-button',function(){
	  $('.aws-cognito-forgot-toggle').animate({
			height: "toggle",
			opacity: "toggle"
		  }, "slow");
	});
	$(document).on('click','.aws-cognito-confirm-toggle-button',function(){
	  $('.aws-cognito-confirm-toggle').animate({
			height: "toggle",
			opacity: "toggle"
		  }, "slow");
	});
	$(document).on('click','.aws-cognito-hidden-login-toggle-button',function(){
	  $('.aws-cognito-hidden-login-toggle').animate({
			height: "toggle",
			opacity: "toggle"
		  }, "slow");
	});
	$(document).on('click','.aws-cognito-forgot-verification-toggle-button',function(){
	  $('.aws-cognito-forgot-verification-toggle').animate({
			height: "toggle",
			opacity: "toggle"
		  }, "slow");
	});	
	
	
	/*********************************************************
	 * Login form AJAX and cognito API function
	 *	
	 *	 Function list
	 * 		- cognitoCheckUserSession() 									: to check user login session 
	 *		- cognitoSignOut() 												: sign out current user 
	 *		- cognitoLogin(loginUser,loginPass)								: login
	 *		- cognitoForgotPassword(forgotUser)								: get verification code for forgot password user 
	 *		- cognitoRegister(registerUser,registerPass,registerEmail)		: register
	 *		- cognitoResendVerification()									: resend register verification code
	 *		- cognitoConfirmVerification(verificationCode)					: confirm register verification code 	 
	 *		- wpAjaxLogin
	 *		- wpAjaxRegister
	 *		- wpAjaxActivateUser
	 *
	 *	 Input class and id list 
	 *		- aws-cognito-login-password									: login form password 
	 *		- aws-cognito-login-username									: login form username 
	 *		- aws-cognito-register-username									: register form username 
	 *		- aws-cognito-register-password									: register form password
	 *		- aws-cognito-register-email									: register form email
	 *		- aws-cognito-register-confirm-password							: register confirm password
	 *		- aws-cognito-confirm-verfication-code							: verification form verification code 
	 *		- aws-cognito-forgot-username									: forgot password form username
	 *
	 *	 Button class list 
	 *		- .aws-cognito-login-button
	 *		- .aws-cognito-forgot-button
	 *		- .aws-cognito-register-button
	 *		- .aws-cognito-confirm-verification-button
	 *		- .aws-cognito-forgot-button
	 *
	 *********************************************************/	
	 
	$(document).ready(function(){
	 
	 
		/**
		 * Data declaration for all function
		 */	
		var username_global;
		var CognitoUserPool = AmazonCognitoIdentity.CognitoUserPool;	
		AWSCognito.config.region = user_pool_id.split('_')[0];		 //Get region from user pool
		var poolData = { 
			UserPoolId :user_pool_id,
			ClientId :client_id
		};
		
		$('.aws-cognito-login-box').append('<b class="error_message" > Your user pool ID or Client ID is incorrect  </b>');  
		
		var userPool = new AmazonCognitoIdentity.CognitoUserPool(poolData);
		
		$('.error_message').hide();
				
		/**
		 * Onclick login button funciton
		 */
		$(document).on('click','.aws-cognito-login-button',function(){			
			  //getting input value 			  
			 var loginUser = $('input[name="aws-cognito-login-username"]').val();			 	 
			 var loginPass = $('input[name="aws-cognito-login-password"]').val();	 			 
			 username_global=loginUser;	
			 $('.aws-cognito-loader-box').show();
			 aws_acl_cognitoLogin(loginUser,loginPass);
		});

		
		/**
		 * Onclick forgot password button function
		 */
		$(document).on('click','.aws-cognito-forgot-button',function(){		   
		   var forgotUser =   $("input[name='aws-cognito-forgot-username']").val()
		   aws_acl_cognitoForgotPassword(forgotUser);
		});

		
		/**
		 * Onclick confirm forgot password verification button function 
		 */
		$(document).on('click','.aws-cognito-forgot-verification-button',function(){		   
		   var forgotCode =   $("input[name='aws-cognito-forgot-verification']").val()
		   var forgotPass =   $("input[name='aws-cognito-forgot-password']").val()
		   aws_acl_cognitoForgotPasswordVerification(forgotCode,forgotPass);
		});
		

		/**
		 * Onclick resend forgot password verification code button function
		 */
		$(document).on('click','.aws-cognito-forgot-resend-button',function(){				   
			aws_acl_cognitoResendForgotVerification();
		});
		
		
		/**
		 * Onclick resend verification code button function
		 */
		$(document).on('click','.aws-cognito-resend-button',function(){				   
			aws_acl_cognitoResendVerification();
		});


		/**
		 * Onclick confirm verification code button function
		 */
		$(document).on('click','.aws-cognito-confirm-verification-button',function(){	
			   var verificationCode = $("input[name='aws-cognito-confirm-verfication-code']").val();
			   aws_acl_cognitoConfirmVerification(verificationCode);
		});

		
		/**
		 * Onclick register button function
		 */
		$(document).on('click','.aws-cognito-register-button',function(){	
		  
		  var registerUser = $("input[name='aws-cognito-register-username']").val();		  
		  var registerPass = $("input[name='aws-cognito-register-password']").val();
		  var registerEmail = $("input[name='aws-cognito-register-email']").val();
		  var registerConfirmPass = $("input[name='aws-cognito-register-confirm-password']").val()
		  username_global=registerUser;
		  
		  if(registerPass != registerConfirmPass)		  
			  alert("Password does not match the confirm password.");		  
		  else {
			  $('.aws-cognito-loader-box').show();
		  	  aws_acl_cognitoRegister(registerUser,registerPass,registerEmail);
		  }
		  
		});


		/**
		 * Wordpress AJAX reset password 
		 */
		function aws_acl_wpAjaxReset(username, pass){			
			jQuery.ajax({
			 type : "post",
			 url : cognito_ajax.ajax_url,
			 data : {
				 action: "aws_cognito_reset", 
				 forgotUser : username,
				 forgotPass : pass			 
			 },
			 success: function(response) {		
				 				
			 }
		  });   			
		}
		
		
		/**
		 * Wordpress AJAX check replace account 
		 */
		function aws_acl_wpCheckReplaceUser(username, pass,email, state ){
			var response='' ;      			
			jQuery.ajax({
			 type : "post",
			 async: false,
			 url : cognito_ajax.ajax_url,
			 data : {
				 action: "aws_cognito_replace_check", 
				 replaceUser : username,
				 replacePass : pass,
				 replaceEmail: email
			 },
			 success: function(response) {	
				 $('.aws-cognito-loader-box').hide();
				alert(response.message);	
				if(state =="register")
				{					
					checkReponse(response.state);
				}
			 }
		  });		  
		  function checkReponse(state) 
	   	  {
			response = state ;
		  }
		  return response;
		}		
		
		
		/**
		 * Wordpress AJAX login 
		 */
		function aws_acl_wpAjaxLogin(username, pass,email ){			
			jQuery.ajax({
			 type : "post",
			 url : cognito_ajax.ajax_url,
			 data : {
				 action: "aws_cognito_login", 
				 loginUser : username,
				 loginPass : pass,
				 loginEmail : email
			 },
			 success: function(response) {		
				 		 
				 if(response.state=="failed")
				 {					 
					 if(response.message == "email existed")
					 {
						 if(confirm('The email address of this cognito account already used by wordpress account, you want to delete wordpress account and login with cognito account ?'))
						 {
							 var replaceUser = prompt("Please insert username of that email address account.");						 
							 var replacePass = prompt("Please insert password of that email address account.");						 
							 $('.aws-cognito-loader-box').show();
							 aws_acl_wpCheckReplaceUser(replaceUser,replacePass,email,'login');
						 }
						 else 
							$('.aws-cognito-loader-box').hide();						 
					 }
					 else 
					 {
						$('.aws-cognito-loader-box').hide();
						alert(response.message);
					 }					
				 }
				 else 
					 window.location=document.location.origin;		
			 }
		  });   			
		}	
		

		/**
		 * Wordpress AJAX register
		 */
		function aws_acl_wpAjaxRegister(username,pass,email){
			jQuery.ajax({
			 type : "post",
			 url : cognito_ajax.ajax_url,
			 data : {
				 action: "aws_cognito_register", 
				 registerUser : username,
				 registerPass : pass,
				 registerEmail : email
			 },
			 success: function(response) {
				 //response.state to check response status 
				 alert(response.message);
				 if(response.state!="failed")
				 {
					 $('.aws-cognito-loader-box').hide();
					 $('.aws-cognito-confirm-toggle').animate({
							height: "toggle",
							opacity: "toggle"
					}, "slow");
					$('#aws-cognito-hidden-button').attr('class', 'aws-cognito-confirm-toggle-button aws-cognito-cancel-button'); 	
				 }
			 }
		  });   
		}	

		
		/**
		 * Wordpress AJAX check user exists
		 */
		function aws_acl_wpCheckUserExists(username,email){	
			var response='' ;
			jQuery.ajax({
			 type : "post",
			 async: false,
			 url : cognito_ajax.ajax_url,
			 data : {
				 action: "aws_cognito_check_user", 
				 checkUser : username,
				 checkEmail : email
			 },
			 success: function(response) {				
				 if(response.state=="true")
				 {
					checkReponse(response.message);
				 }
				 
			 }
			});
			
			function checkReponse( state ) 
			{
				response = state ;
			}
			
			return response;
		}	
	
		/**
		 * Wordpress AJAX activate user
		 */
		function aws_acl_wpAjaxActivateUser(username){
			jQuery.ajax({
			 type : "post",
			 url :cognito_ajax.ajax_url,
			 data : {
				 action: "aws_cognito_activation", 
				 username :username
			 },
			 success: function() {
				//no response for this ajax call
			 }
		  });   
		}
	
		
		/**
		 * Cognito check user login session
		 */
		function aws_acl_cognitoCheckUserSession(){
			var cognitoUser = userPool.getCurrentUser();
			if (cognitoUser != null) {
				cognitoUser.getSession(function(err, session) {
				   if (err) {
					   alert(err);
					   return;
				   }
			   });
			}
		}


		/**
		 * Cognito signout function
		 */
		function aws_acl_cognitoSignOut(){
			var cognitoUser = userPool.getCurrentUser();
			cognitoUser.signOut();
		}		
		
		
		/**
		 * Cognito login function
		 */
		function aws_acl_cognitoLogin(loginUser, loginPass){
			var authenticationData = {
					 Username : loginUser,
					 Password : loginPass
		 	 };
			 var authenticationDetails = new AmazonCognitoIdentity.AuthenticationDetails(authenticationData);			 
			 var userData = {
				 Username : loginUser,
				 Pool : userPool
			 };
			 var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);			 
			 cognitoUser.authenticateUser(authenticationDetails, {
				 onSuccess: function (result) {
					 //alert("Login success!");	
					  
					  //get user attribute 
					  cognitoUser.getUserAttributes(function(err, result) {
						 for (i = 0; i < result.length; i++) {
							 if( result[i].getName() == "email")
							 {
								var email = result[i].getValue();
								$('.aws-cognito-loader-box').hide();
								aws_acl_wpAjaxLogin(loginUser,loginPass,email);
							 }
						 }
					});						
				 },
				 onFailure: function(err) {				 
					 if(err=="UserNotConfirmedException: User is not confirmed.")
					 {
						 $('.aws-cognito-loader-box').hide();
						  $('.aws-cognito-confirm-login-toggle').animate({
								height: "toggle",
								opacity: "toggle"
						  }, "slow"); 						  
					 }
					 else 
					 {
						 $('.aws-cognito-loader-box').hide();
						 alert(err);
					 }				 				 
				 },
			 });						 
		}	

		
		/**
		 * Cognito forgot password function
		 */
		function aws_acl_cognitoForgotPassword(forgotUser){
		   username_global = forgotUser;
		   var userData = {
			   Username : forgotUser,
			   Pool : userPool
		   };
		   var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
		   cognitoUser.forgotPassword({
				 onSuccess: function (data) {
					 // successfully initiated reset password request
					 console.log('CodeDeliveryData from forgotPassword: ' + data);
				 },
				 onFailure: function(err) {
					 alert(err);
				 },
				 //Optional automatic callback
				 inputVerificationCode: function(data) {					
					  $('.aws-cognito-forgot-verification-toggle').animate({
							height: "toggle",
							opacity: "toggle"
					  }, "slow");
				 }
			 });						
		}			


		/**
		 * Cognito resend forgot password verification code function 
		 */			
		function aws_acl_cognitoResendForgotVerification(){
			var userData = {
			   Username : username_global,
			   Pool : userPool
		   };
			var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
			cognitoUser.forgotPassword({
				 onSuccess: function (data) {
					 // successfully initiated reset password request
					 console.log('CodeDeliveryData from forgotPassword: ' + data);
				 },
				 onFailure: function(err) {
					 alert(err);
				 },
				 //Optional automatic callback
				 inputVerificationCode: function(data) {	
						alert("Verification code sent");
				}
			 });						
		}							 
				
		
		/**
		 * Cognito forgot password verification function 
		 */		
		function aws_acl_cognitoForgotPasswordVerification(code,pass){			
		   var userData = {
			   Username : username_global,
			   Pool : userPool
		   };			
			var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);		
			var verificationCode = code;
			var newPassword = pass
			cognitoUser.confirmPassword(verificationCode, newPassword, {
					
						onSuccess: (result) => {
							alert("New password changed");
							$('.aws-cognito-forgot-login-toggle').animate({
								height: "toggle",
								opacity: "toggle"
							}, "slow");	
							aws_acl_wpAjaxReset(username_global,pass);							
						},

						onFailure: (err) => {
							alert(err);
						}
			});
		}

		/**
		 * Cognito register function
		 */
		function aws_acl_cognitoRegister(registerUser,registerPass,registerEmail){
			
		     // Check username from wordpress website first 
			 var response  = aws_acl_wpCheckUserExists( registerUser , registerEmail);			 
			 if(response == "username")
			 {
				 $('.aws-cognito-loader-box').hide();
				 alert("Username already exists in wordpress ");
			 } 
			 else if (response == "email")
			 {
				 
				 if(confirm('The email address already used by wordpress account, you want to delete wordpress account and create new cognito account ?'))
				 {
					 var replaceUser = prompt("Please insert username of that email address account.");						 
					 var replacePass = prompt("Please insert password of that email address account.");						 
					 $('.aws-cognito-loader-box').show();
					 var response = aws_acl_wpCheckReplaceUser(replaceUser,replacePass,registerEmail,'register');
					 if(response == "success")
					 {
						 //Auto register again 
						  aws_acl_cognitoRegister(registerUser,registerPass,registerEmail);
					 }
				 }
				 else 
					 $('.aws-cognito-loader-box').hide();										
			 }
			 else 
			 {
				 
				 //Then check username from cognito and register new account in cognito
				var CognitoUserPool = AmazonCognitoIdentity.CognitoUserPool;
			  
				  //add user attribute
				  var attributeList = [];
				  var dataEmail = {
					   Name : 'email',
					   Value : registerEmail
				  };			 
							 
				  var attributeEmail = new AmazonCognitoIdentity.CognitoUserAttribute(dataEmail);
				  attributeList.push(attributeEmail);			  
				  userPool.signUp(registerUser, registerPass, attributeList, null, function(err, result){
					   if (err) {					 
							alert(err);
							$('.aws-cognito-loader-box').hide();
							return;				   
					   }
					   else 
					   {		
							 aws_acl_wpAjaxRegister(registerUser,registerPass,registerEmail);				  
					   }
				});	
			 }				
		}		

		
		/**
		 * Cognito resend verification code 
		 */
		function aws_acl_cognitoResendVerification(){
		   var userData = {
			   Username : username_global,
			   Pool : userPool
		   };
		   var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
		   cognitoUser.resendConfirmationCode(function(err, result) {
			if (err) {
				alert(err);
				return;
			}else 
			{
				alert("Verification code sent");
			}					
		  });			
			
		}	

		
		/**
		 * Cognito confirm verification code
		 */
		function aws_acl_cognitoConfirmVerification(verificationCode){
			   var userData = {
				   Username : username_global,
				   Pool : userPool
			   };
			   var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
			   cognitoUser.confirmRegistration(verificationCode, true, function(err, result) {
				   if (err) {
					   alert(err);
					   return;
				   }
				   else 
				   {
						alert("Verify success");
						$('.aws-cognito-confirm-login-toggle').animate({
							height: "toggle",
							opacity: "toggle"
						}, "slow");		
						aws_acl_wpAjaxActivateUser(username_global);
				   }			   				   
			   });						
		}		
	});
})( jQuery );
