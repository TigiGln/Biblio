/*
 * Created on Mon Apr 19 2021
 * Latest update on Mon Apr 26 2021
 * Info - JS for annotate module in edit article menu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */


/*******************************************************************************/
/* WYSIWYG function */
/*******************************************************************************/

const annotateEditor = document.getElementsByClassName('annotateEditor')[0];
const annotateToolbar = annotateEditor.getElementsByClassName('annotateToolbar')[0];
const annotateButtons = annotateToolbar.querySelectorAll('.annotateAction');
const annotateArea = document.getElementById('annotateArea');
const annotateVisualView = document.getElementById('annotateVisualView');
const annotateHtmlView = document.getElementById('annotateHtmlView');
launchAnnotateWysiwyg();

/**
 * For each element in the action (button) line, add event listener on click event corresponding to its action.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
 function launchAnnotateWysiwyg() {
  for(let i = 0; i < annotateButtons.length; i++) {
    let button = annotateButtons[i];
    button.addEventListener('click', function(e) {
      let action = this.dataset.action;
      switch(action) {
        case 'code':
          annotateCodeAction(this);
          break;
        case 'createLink':
          annotateLinkAction();
          break;
        default:
          annotateDefaultAction(action);
      } 
    });
  }
 }

/**
 * annotateCodeAction is the specific function related to the (hidden) switch between visual editor and html editor.
 * Once called, will open the visual editor and close the html editor if the html editor was open, and the opposite.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} button 
 *            The (hidden) button that trigger this function.
 */
function annotateCodeAction(button) {
  if(button.classList.contains('active')) {
    //Close Code
    annotateVisualView.innerHTML = annotateHtmlView.value;
    annotateHtmlView.style.display = 'none';
    annotateVisualView.style.display = 'block';
    button.classList.remove('active');   
  } else { 
    //Open Code
    annotateHtmlView.innerText = annotateVisualView.innerHTML;
    annotateVisualView.style.display = 'none';
    annotateHtmlView.style.display = 'block';
    button.classList.add('active'); 
  }
}

/**
 * annotateLinkAction is the specific function related to the write an url link button.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function annotateLinkAction() {
  let linkValue = prompt('Please insert a link');
  document.execCommand('createLink', false, linkValue);
}

/**
 * annotateDefaultAction is the specific function related to the execCommand buttons.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function annotateDefaultAction(action) {
  document.execCommand(action, false);
}