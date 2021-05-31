/*
 * Created on Mon May 3 2021
 * Latest update on Thu May 27 2021
 * Info - JS for annotate threads module in edit article menu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

/*******************************************************************************/
/* interactions function */
/*******************************************************************************/

const logHeaderAnnotateThreadsInteractions = "[edit article menu : annotate Threads module]";

/**
 * annotateShow is a function that will show the selected annotation, allowing to reply to this.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 */
function annotateShow(id) {
    let annotation = document.getElementById("link_"+id);
    let tag = annotation.dataset.bsOriginalTitle;
    let content  = annotation.dataset.bsContent.match(/.*?(<hr class='sep)/mg)[0].replace("<hr class='sep", "");
    let selection = document.getElementById("selectedAnnotation");
    selection.innerHTML = tag+"<br>-----<br>"+content+"<br>-----<br>at: "+annotation.innerHTML;
    selection.style.pointerEvents = "all";
    selection.style.userSelect = "all";
    let numaccess = articleGet("numaccess");
    let origin = articleGet("origin");
    annotateRepliesLoad(origin, numaccess, id);
    if(!document.querySelector('#article-annotatethreads').classList.contains("show")) { document.querySelector('#annotatethreadsBtn').click(); }
    //Link with annotate to remove annotations when we work on the article
    if (typeof annotateShowDelete === "function") { annotateShowDelete(id); }
}

/**
 * annotateReplySend is a function that will send the user's reply to an annotation.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} artID 
 * @param {*} commentId 
 * @fires XMLHttpRequest
 */
function annotateReplySend(commentId) {
  let numaccess = articleGet("numaccess").replace('/', '_');
  let origin = articleGet("origin");
  let id = numaccess+"_"+commentId;
  let text = document.getElementById("annotatesReply").value;
  let url = "../modules/edit_article_menu/annotatethreads/save-reply.php";
  let params = "ORIGIN="+origin+"&ID="+id+"&text="+text;
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(params);
  /* Handle request results */
  http.onreadystatechange = function() {
    if (http.readyState === 4) {
        if (http.status === 200) {
          console.log(logHeaderAnnotateThreadsInteractions+" annotate reply sent successfully with status code: "+this.status);
          text = "";
          annotateRepliesLoad(origin, numaccess, commentId);
        } else {
          console.log(logHeaderAnnotateThreadsInteractions+" annotate reply failed with status code: "+this.status);
          return false;
        }
    }
  }
}

/**
 * annotateReplyLoad is a function that will load the correct replies to an annotation.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} artID 
 * @param {*} commentId 
 * @fires XMLHttpRequest
 */
 function annotateRepliesLoad(origin, numaccess, commentId) {
  let id = numaccess.replace('/', '_')+"_"+commentId;
  let url = "../modules/edit_article_menu/annotatethreads/load-replies.php";
  let params = "ORIGIN="+origin+"&ID="+id;
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("GET", url+"?"+params, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(null);
  /* Handle request results */
  http.onreadystatechange = function() {
    if (http.readyState === 4) {
        if (http.status === 200) {
          console.log(http.response);
          let replies = JSON.parse(http.response);
          annotateFillReplies(commentId, replies);
          updateAnnotatePopOver(replies.length-1, commentId);
          console.log(logHeaderAnnotateThreadsInteractions+" annotate replies receive successfully with status code: "+this.status);
        } else {
          annotateFillReplies(commentId, []);
          console.log(logHeaderAnnotateThreadsInteractions+" annotate replies receive failed with status code: "+this.status);
        }
    }
  }
}

/**
 * will update the article in the database with the reply count update.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} size 
 * @param {*} commentId 
 */
function updateAnnotatePopOver(size, commentId) {
  if(size > 0) {
    let annotation = document.getElementById("link_"+commentId).dataset.bsContent.match(/.*?(<hr class='sep)/mg)[0]+"'>"+size+" Replies";
    document.getElementById("link_"+commentId).setAttribute('data-bs-content',annotation);
  }
  simpleUpdateArticle();
}

/**
 * annotateFillReplies will write replies and write reply zone.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 * @param {*} replies 
 */
function annotateFillReplies(id, replies) {
  let thread = "";
  for (let i = 1; i<replies.length; i++) {
    let reply = replies[i];
    thread = '<div class="card m-0 p-0"><div class="card-header m-0 p-1">['+decodeURIComponent(reply["date"])+'] '+decodeURIComponent(reply["@attributes"]["name"])
              +'</div><div class="card-body m-0 p-1">'+decodeURIComponent(reply["content"])+'</div></div>' + thread;
  }
  document.getElementById("AnnotationRepliesThread").innerHTML = '<div class="card"><div class="card-body"><textarea id="annotatesReply" rows="1"></textarea>'
  +'<button id="annotateReplySend" type="button" class="btn btn-outline-success btn-sm w-100" style="pointer-events: all; user-select: all;" onclick="annotateReplySend(\''+id+'\')" >Send reply</button></div></div>'+thread;
}

/**
 * simpleUpdateArticle is the specific function to update and save the html content of the article in the database.
 * This version is used when we remove or just save the article div, without adding any annotations. This is a copy of annotate module to allows them to work alone
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The ID of the article
 * @returns 
 *            Boolean to notify of the success of the save.
 * @fires XMLHttpRequest
 */
 function simpleUpdateArticle() {
  /* Prepare request */
  let id = articleGet("numaccess");
  let origin = articleGet("origin");
  let article = document.getElementById("html").innerHTML;
  let url = "../modules/edit_article_menu/annotate/save-article.php";
  let params = "ARTICLE="+encodeURIComponent(article)+"&ID="+encodeURIComponent(id)+"&ORIGIN="+encodeURIComponent(origin);
  console.log(logHeaderAnnotateThreadsInteractions+" article send request with parameters: "+params);
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(params);
  /* Handle request results */
  http.onreadystatechange = function() {
    if (http.readyState === 4) {
        if (http.status === 200) {
          console.log(logHeaderAnnotateThreadsInteractions+" article sent successfully with status code: "+this.status);
        } else {
          console.log(logHeaderAnnotateThreadsInteractions+" annotate sent failed with status code: "+this.status);
          return false;
        }
    }
  }
  return true;
}