/*
 * Created on Fri Apr 23 2021
 * Latest update on Mon May 17 2021
 * Info - JS for conclude module in edit article menu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

/*******************************************************************************/
/* interactions function */
/*******************************************************************************/

const logHeaderConcludeInteractions = "[edit article menu : conclude module]";

/**
 * validateConcludeInteraction function that will give the new status of the article to conclude or proccessed.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The ID of the article in the database.
 * @fires XMLHttpRequest
 */
function validateConcludeInteraction(status) {
  let id = articleGet("numaccess");
  let origin = articleGet("origin");
  /* Prepare request */
  let url = "../modules/edit_article_menu/conclude/validate.php";
  let params = "ORIGIN="+encodeURIComponent(origin)+"&ID="+encodeURIComponent(id)+"&status="+status;
  console.log(logHeaderConcludeInteractions+" Validate send with parameters: "+params);
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(params);
  /* Handle request results */
  http.onreadystatechange = function() {
    if (http.readyState === 4) {
        if (http.status === 200) {
          console.log(logHeaderConcludeInteractions+' Validate successfully with status code: '+this.status);
          alert("The article was successfully proccessed. Return to your tasks ");
          window.close();
        } else {
          console.log(logHeaderConcludeInteractions+' Validate failed with status code: '+this.status);
          alert("An error occured. Please retry. "+this.response);
        }
    }
  }
}