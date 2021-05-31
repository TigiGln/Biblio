/*
 * Created on Mon Apr 19 2021
 * Latest update on Fri May 28 2021
 * Info - JS for annotate module in edit article menu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

/*******************************************************************************/
/* interactions function */
/*******************************************************************************/

const logHeaderAnnotateInteractions = "[edit article menu : annotate module]";
const maxLengthAnnotateInteractions = 300;

var isOpen = false;

/**
 * annotateClose is the specific function to close (lock) the annotate's WYSIWYG.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function annotateClose() {
  if(isOpen) {
    document.querySelector("#annotateEditor").style.pointerEvents = "none";
    document.querySelector("#annotateEditor").style.userSelect = "none";
    if(!!document.getElementById("temp")) {
      let article = document.getElementById("html").innerHTML;
      let data = document.getElementById("temp").innerHTML;
      document.getElementById("html").innerHTML = article.replace(/(<span id="temp">).*?(<\/span>)/s, data);
    }
    isOpen = false;
    document.querySelector("#annotateArea").style.backgroundColor = "white";
    document.getElementById("annotateRemove").innerHTML = "";
    //Uncomment next line to auto-hide annotation menu on close
    //if(document.querySelector('#article-Annotate').classList.contains("show")) { document.querySelector('#AnnotateBtn').click(); }
    refreshPopovers(); //Always refresh popOvers
  }
}

/**
 * annotateClose is the specific function called by the undo button to remove a specific annotation.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The annotation ID (the date) on this article.
 * @fires XMLHttpRequest
 */
function annotateUndo(id, date) {
  if (confirm('Are you sure you want to remove this annotation ?')) {
    if (!!document.getElementById("mark_"+date)) document.getElementById("mark_"+date).outerHTML = document.getElementById("link_"+date).innerHTML;
    if(!!document.getElementById("annotateUndo")) document.getElementById("annotateUndo").outerHTML = "";
    document.querySelector("#annotateArea").style.backgroundColor = "salmon";
    if(!!document.getElementById("annotates")) {
      document.getElementById("annotates").innerHTML = "";
    }
    simpleUpdateArticle(id);
    //Pairing with annotateThread module
    if(!!document.getElementById("selectedAnnotation")) {
      document.getElementById("selectedAnnotation").innerHTML = "";
    }
    if(!!document.getElementById("AnnotationRepliesThread")) {
      document.getElementById("AnnotationRepliesThread").innerHTML = "";
    }
    document.getElementById("annotateRemove").innerHTML = "";
  }
}

/**
 * annotateSend is a function called by the send button to save the annotation and send it to the annotaion xml database 
 * as well as saving the new html content of the article in the database.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The annotation ID on this article.
 * @fires XMLHttpRequest
 */
function annotateSend() {
  if(isOpen) {
    /* Prepare request */
    document.querySelector('#annotateCode').click();
    let url = "../modules/edit_article_menu/annotate/save-annotation.php";
    let color = document.getElementById("annotateColorPicker").value;
    let text = document.getElementById("temp").innerHTML;
    let comment = document.querySelector("#annotateHtmlView").textContent;
    let id = articleGet("numaccess");
    let origin = articleGet("origin");
    document.querySelector('#annotateCode').click();
    let params = "ORIGIN="+encodeURIComponent(origin)+"&ID="+encodeURIComponent(id)+"&color="+encodeURIComponent(color)+"&text="+encodeURIComponent(text)+"&comment="+encodeURIComponent(comment);
    console.log(logHeaderAnnotateInteractions+" annotate send request with parameters: "+params);
    /* Fires request */
    var http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send(params);
    /* Handle request results */
    http.onreadystatechange = function() {
      if (http.readyState === 4) {
          if (http.status === 200) {
            console.log(logHeaderAnnotateInteractions+" annotate sent successfully with status code: "+this.status);
            let res = this.response.toString().split(',');
            let result = updateArticle(res[0], res[1],  color, text, comment);
            if (result) {
              annotateClose();
              let commentID = "'"+res[0].toString()+"'";
              document.getElementById("annotateInteraction").innerHTML = '<button id="annotateUndo" type="button" class="btn btn-warning" style="pointer-events: all; user-select: all;" onclick="annotateUndo(\''+id+'\','+commentID+')" >R</button>'
                                                          + document.getElementById("annotateInteraction").innerHTML;
            }
          } else {
            console.log(logHeaderAnnotateInteractions+" annotate sent failed with status code: "+this.status);
             annotateClose();
          }
      }
    }
  }
}

/**
 * updateArticle is the specific function to update and save the html content of the article in the database.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The ID of the article
 * @param {*} date 
 *            The date of the latest annotation.
 * @param {*} author 
 *            The author of the latest annotation.
 * @param {*} color 
 *            The color of the latest annotation.
 * @param {*} text 
 *            The text of the latest annotation.
 * @param {*} comment 
 *            The content of the latest annotation.
 * @returns 
 *            Boolean to notify of the success of the save.
 * @fires XMLHttpRequest
 */
function updateArticle(date, author, color, text, comment) {
  let id = articleGet("numaccess");
  let origin = articleGet("origin");
  /* Update article's html */
  let article = document.getElementById("html").innerHTML;
  let highlight = '<mark id=mark_'+date+' style="background-color: '+color+'; cursor: pointer;"><a id=link_'+date+' class="note" data-bs-toggle="popover" data-bs-trigger="hover focus" data-placement="bottom" data-bs-html="true" title="'+
  '['+date+'] '+author+'"'+' data-bs-content="'+comment+'<hr class=\'sep d-none\'>" onClick="annotateShow(\''+date.toString()+'\')">'+text.toString()+'</a></mark>';
  document.getElementById("temp").outerHTML = highlight;
  article = document.getElementById("html").innerHTML;
  /* Prepare request */
  let url = "../modules/edit_article_menu/annotate/save-article.php";
  let params = "ORIGIN="+encodeURIComponent(origin)+"&ID="+encodeURIComponent(id)+"&ARTICLE="+encodeURIComponent(article);
  console.log(logHeaderAnnotateInteractions+" article send request with parameters: "+params);
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(params);
  /* Handle request results */
  http.onreadystatechange = function() {
    if (http.readyState === 4) {
        if (http.status === 200) {
          console.log(logHeaderAnnotateInteractions+" article sent successfully with status code: "+this.status);
          document.querySelector("#annotateArea").style.backgroundColor = "palegreen";
        } else {
          console.log(logHeaderAnnotateInteractions+" annotate sent failed with status code: "+this.status);
          return false;
        }
    }
  }
  return true;
}

/**
 * simpleUpdateArticle is the specific function to update and save the html content of the article in the database.
 * This version is used when we remove or just save the article div, without adding any annotations.
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
  console.log(logHeaderAnnotateInteractions+" article send request with parameters: "+params);
  /* Fires request */
  var http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  http.send(params);
  /* Handle request results */
  http.onreadystatechange = function() {
    if (http.readyState === 4) {
        if (http.status === 200) {
          console.log(logHeaderAnnotateInteractions+" article sent successfully with status code: "+this.status);
        } else {
          console.log(logHeaderAnnotateInteractions+" annotate sent failed with status code: "+this.status);
          return false;
        }
    }
  }
  return true;
}

/**
 * addTemptag will add a temporary span with id temp around the selection to allows us to find the place where we are annotating the article.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @returns
 *            The Inner html of the temp tag.
 */
function addTempTag() {
    var sel, range, node;
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
          range = sel.getRangeAt(0);
          var container = document.createElement("div");
          for (var i = 0, len = sel.rangeCount; i < len; ++i) {
            container.appendChild(sel.getRangeAt(i).cloneContents());
          }
          /* Known Issues: OuterHTML isn't used, hence if a link may cause issues */
          let text = container.innerHTML.replace(/(<a id="mark_).*?(<mark).*?(>)/, '').replace('(</mark></a>)', '');
          var html = '<span id="temp">' + text + '</span>';
          range.deleteContents();
          var el = document.createElement("div");
          el.innerHTML = html;
          var frag = document.createDocumentFragment(), node, lastNode;
          while ( (node = el.firstChild) ) { lastNode = frag.appendChild(node); }
          range.insertNode(frag);
          return container.innerHTML;
        }
    }
}

/**
 * Listener on mouseUp button, if the mouse is inside article div and length of the selection inferior from maxLengthAnnotateInteractions
 * will allows the selection of the selected text and go to annotating mode.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
document.getElementById("html").addEventListener("mouseup", function() {
  if(document.getSelection() && !isOpen && document.getSelection().toString().length > 0 && document.getSelection().toString().length < maxLengthAnnotateInteractions) {
    document.querySelector("#annotateEditor").style.pointerEvents = "all";
    document.querySelector("#annotateEditor").style.userSelect = "all";

    let text = addTempTag();

    document.querySelector("#selection").innerHTML = text;
    document.querySelector("#annotateVisualView").innerHTML = "";
    document.querySelector("#annotateVisualView").innerHTML = "";
    document.getElementById("annotateRemove").innerHTML = "";

    document.querySelector("#annotateArea").style.backgroundColor = "white";
    if(!!document.getElementById("annotateUndo")) {
      document.getElementById("annotateUndo").outerHTML = "";
    }
    if(!!document.getElementById("annotates")) {
      document.getElementById("annotates").innerHTML = "";
    }
    isOpen = true;
    if(!document.querySelector('#article-annotate').classList.contains("show")) { document.querySelector('#annotateBtn').click(); }
  } 
});

/**
 * Will be overwrite by whataver come after annotation and has to show the annotation + the replies thread. Just to avoid "missing function" log.
 * example: the module AnnotateThreads will use it and redefine it.
 * @param {*} id 
 */
function annotateShow(id) {
  annotateShowDelete(id);
} 


function annotateShowDelete(id) {
  let annotation = document.getElementById("link_"+id);
  let tag = annotation.dataset.bsOriginalTitle;
  let content  = annotation.dataset.bsContent.match(/.*?(<hr class='sep)/mg)[0].replace("<hr class='sep", "");
  document.getElementById("annotateRemove").innerHTML = `
  <hr>
  <div class="alert alert-danger" role="alert">
  [DANGER ZONE]<br>`+tag+`<br>-----<br>`+content+`<br>-----<br>at: `+annotation.innerHTML+`<br>
  <button id="annotateRemoveButton" type="button" class="btn btn-outline-danger w-100" style="pointer-events: all; user-select: all;" onclick="annotateUndo(\'null\','`+id+`')" >Remove Annotation</button>
  </div>
  `;
} 