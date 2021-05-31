/*
 * Created on Tue Apr 21 2021
 * Latest update on Mon Apr 26 2021
 * Info - JS for notes module in edit article menu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

/*******************************************************************************/
/* WYSIWYG function */
/*******************************************************************************/

const notesEditor = document.getElementsByClassName('notesEditor')[0];
const notesToolbar = notesEditor.getElementsByClassName('notesToolbar')[0];
const notesButtons = notesToolbar.querySelectorAll('.notesAction');
const notesArea = document.getElementById('notesArea');
const notesVisualView = document.getElementById('notesVisualView');
const notesHtmlView = document.getElementById('notesHtmlView');
launchNotesWysiwyg();

/**
 * For each element in the action (button) line, add event listener on click event corresponding to its action.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function launchNotesWysiwyg() 
{
  for(let i = 0; i < notesButtons.length; i++) 
  {
    let button = notesButtons[i];
    button.addEventListener('click', function(e) 
    {
      let action = this.dataset.action;
      switch(action) 
      {
        case 'code':
          notesCodeAction(this);
          break;
        case 'createLink':
          notesLinkAction();
          break;
        default:
          notesDefaultAction(action);
      } 
    });
  }
}


/**
 * notesCodeAction is the specific function related to the (hidden) switch between visual editor and html editor.
 * Once called, will open the visual editor and close the html editor if the html editor was open, and the opposite.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} button 
 *            The (hidden) button that trigger this function.
 */
function notesCodeAction(button) 
{
  if(button.classList.contains('active')) 
  {
    notesVisualView.innerHTML = notesHtmlView.value;
    notesHtmlView.style.display = 'none';
    notesVisualView.style.display = 'block';
    button.classList.remove('active');   
  } 
  else 
  { 
    notesHtmlView.innerText = notesVisualView.innerHTML;
    notesVisualView.style.display = 'none';
    notesHtmlView.style.display = 'block';
    button.classList.add('active'); 
  }
}

/**
 * notesLinkAction is the specific function related to the write an url link button.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function notesLinkAction() 
{
  let linkValue = prompt('Please insert a link');
  document.execCommand('createLink', false, linkValue);
}

/**
 * notesDefaultAction is the specific function related to the execCommand buttons.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function notesDefaultAction(action) 
{
  document.execCommand(action, false);
}