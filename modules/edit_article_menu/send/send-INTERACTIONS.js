/*
 * Created on Fri Apr 23 2021
	* Latest update on Mon May 17 2021
 * Info - JS for conclude module in edit article menu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

/*******************************************************************************/
/* interactions function */
/*******************************************************************************/

const logHeaderSendInteractions = "[edit article menu : send module]";
sendInteractionsLoadUsersList();

/**
 *  sendInteractionsLoadUsersList is a method calling a function to get users list.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @fires XMLHttpRequest
 */
function sendInteractionsLoadUsersList() {
  let usersList = document.getElementById('usersList');
  /* Prepare request */
  let url = "../modules/edit_article_menu/send/getUsersList.php";
  console.log(logHeaderSendInteractions+" Request users List");
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(null);
  /* Handle request results */
  http.onreadystatechange = function() {
    if (http.readyState === 4) {
        if (http.status === 200) {
          sendInteractionsFillUsersList(JSON.parse(this.response), usersList);
          console.log(logHeaderSendInteractions+' Request users List successfully with status code: '+this.status);
        } else {
          document.querySelector("#send").innerHTML = '<div class="alert alert-danger" role="alert">An error occured. Please reload to use this module</div>';
          console.log(logHeaderSendInteractions+' Request users List failed with status code: '+this.status);
        }
    }
  }
}

/**
 * sendInteractionsFillUsersList is a method to fill the user list.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} usersJSON 
 * @param {*} usersListDOM 
 */
function sendInteractionsFillUsersList(usersJSON, usersListDOM) {
  for (let i = 0; i < usersJSON.length; i++){
    let user = usersJSON[i];
    usersListDOM.innerHTML += '<option value="'+user['name_user']+'" data-email="'+user['email']+'" data-id="'+user['id_user']+'">';
  }
}

/**
 * validateSendInteraction function that will tell the server the new user to which gives the article.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The num_access of the article in the database.
 * @fires XMLHttpRequest
 */
function validateSendInteraction() {
  let id = articleGet("numaccess");
  let origin = articleGet("origin");
  /* Prepare request */
  let newUser = document.getElementById("sendTo").value;
  if(document.querySelector('option[value="' + newUser + '"]') === null) { newUser = -1; } 
  else { newUser = document.querySelector('option[value="' + newUser + '"]').dataset.id; }
  let url = "../modules/edit_article_menu/send/validate.php";
  let params = "ORIGIN="+encodeURIComponent(origin)+"&ID="+encodeURIComponent(id)+"&newUser="+encodeURIComponent(newUser);
  console.log(logHeaderSendInteractions+" Send send with parameters: "+params);
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(params);
  /* Handle request results */
  http.onreadystatechange = function() {
    if (http.readyState === 4) {
        if (http.status === 200) {
          console.log(logHeaderSendInteractions+' Send successfully with status code: '+this.status);
          alert("The article was successfully Sent. Return to your tasks");
          window.close();
        } else {
          console.log(logHeaderSendInteractions+' Send failed with status code: '+this.status);
          alert("An error occured. Please retry.");
        }
    }
  }
}