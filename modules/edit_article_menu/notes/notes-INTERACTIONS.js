/*
 * Created on Tue Apr 21 2021
	* Latest update on Thu May 20 2021
 * Info - JS for notes module in edit article menu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

/*******************************************************************************/
/* interactions function */
/*******************************************************************************/

const logHeaderNotesInteractions = "[edit article menu : notes module]";
notesInteractionsLoadNotes();

/**
 * notesInteractionsLoadNotes is a method calling a function to get the article's ID.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function notesInteractionsLoadNotes() 
{
  notesLoad();
}

/**
 * notesSave is the specific function to save user's notes.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The article ID in the database.
 * @fires XMLHttpRequest
 */
 function notesSave() 
 {
  /* Prepare request */
  document.querySelector('#notesCode').click();
  let url = "../modules/edit_article_menu/notes/save-notes.php";
  let notes = document.querySelector("#notesHtmlView").textContent;
  let date = (new Date()).getTime(); //Until I find a way to get date from the php
  document.querySelector('#notesCode').click();
  let params = "ORIGIN="+encodeURIComponent(articleGet("origin"))+"&ID="+encodeURIComponent(articleGet("numaccess"))+"&date="+encodeURIComponent(date)+"&notes="+encodeURIComponent(notes);
  //console.log(logHeaderNotesInteractions+" Notes save send request with parameters: "+params);
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(params);
  /* Handle request results */
  http.onreadystatechange = function() 
  {
    if (http.readyState === 4) 
    {
        //if success, call the update article function
        if (http.status === 200) 
        {
          console.log(logHeaderNotesInteractions+' Notes saved successfuly with status code: '+this.status);
          document.querySelector("#notesArea").style.backgroundColor = "white";
        } 
        else 
        {
           console.log(logHeaderNotesInteractions+' Notes save failed with status code: '+this.status);
           document.querySelector("#notesArea").style.backgroundColor = "salmon";
        }
    }
  }
}

/**
 * notesLoad allows to load the user's saved notes, as well as others' notes.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 * @fires XMLHttpRequest
 */
function notesLoad() 
{
  /* Prepare request */
  let url = "../modules/edit_article_menu/notes/load-notes.php";
  let params = "ORIGIN="+encodeURIComponent(articleGet("origin"))+"&ID="+encodeURIComponent(articleGet("numaccess"));
  //console.log(logHeaderNotesInteractions+" Notes Load with parameters: "+params);
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("GET",url+"?"+params,true);
  http.send(null);
  /* Handle request results */
  http.onreadystatechange = function() 
  {
    if (this.readyState == 4) 
    {
      if (this.status == 200) 
      {
        //console.log(this.response);
        //console.log(logHeaderNotesInteractions+' Notes received successfuly with status code: '+this.status);
        notesFill(JSON.parse(this.response));
      } 
      else if (this.status == 404) 
      { 
        document.querySelector("#notes").innerHTML = '<div class="alert alert-danger" role="alert">An error occured. Please reload to use this module</div>';
        //console.log(logHeaderNotesInteractions+' Notes received failed with status code: '+this.status); 
      }
    } 
  }
}

/**
 * notesFill will parse the JSON of notes and fill the notes categories.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} notes
 *            JSON content containing the notes fetched by notesLoad.
 */
function notesFill(notes) 
{
  if(!!notes[0]["@attributes"]) 
  {
    notesLoadUser(notes[0]["content"]);
  }
  for (let i = 1; i<notes.length; i++) 
  {
    notesLoadOther(notes[i]);
  }
}

/**
 * notesLoadUser is the specific function to load the user's saved notes.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} content 
 *            The text that will be inside the user's notes part.
 */
function notesLoadUser(content) 
{
  document.querySelector("#notesCode").click();
  document.querySelector("#notesHtmlView").textContent = decodeURIComponent(content);
  document.querySelector("#notesCode").click();
}

/**
 * notesLoadOthers is the specific function to load the others' saved notes.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} content 
 *            A to-be-parsed string separated with coma and semicolon.
 */
function notesLoadOther(note) 
{
  document.getElementById("notesThread").innerHTML += 
    '<div class="card m-0 p-0"><div class="card-header m-0 p-1">['+decodeURIComponent(note["date"])+'] '+decodeURIComponent(note["@attributes"]["name"])
    +'</div><div class="card-body m-0 p-1">'+decodeURIComponent(note["content"])+'</div></div>';
}

/**
 * Listener on visual view area that will change the background color of the notes area to salmon on input event.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
document.getElementById("notesVisualView").addEventListener("input", function() 
{
  document.querySelector("#notesArea").style.backgroundColor = "salmon";
});

/**
 * Listener on visual html area that will change the background color of the notes area to salmon on input event.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
document.getElementById("notesHtmlView").addEventListener("input", function() 
{
  document.querySelector("#notesArea").style.backgroundColor = "salmon";
});