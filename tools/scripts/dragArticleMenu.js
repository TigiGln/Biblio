/**
 * Created on Thu May 6 2021
 * Latest update on Fri May 7 2021
 * Info - Allows drag menu on articleMenu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

const resizer = document.getElementById('dragArticleMenu');
const articleMenuDiv = document.getElementById('ArticleMenu');
const articleDiv = document.getElementById('display');
//MINSIZE is effective on the articleDiv
const MINSIZE = 300;

//Mouse position (initial)
let x = 0;
let leftWidth = 0;

/**
 * Event Listener on mousedown event on the resizer objet to start the resize mode.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} e 
 */
 resizer.addEventListener('mousedown', function(e) 
 {
    x = e.clientX;
    leftWidth = articleMenuDiv.getBoundingClientRect().width;
    document.addEventListener('mousemove', mouseMoveHandler);
    document.addEventListener('mouseup', mouseUpHandler);
    //Remove selection to dissalow article's text selection when resizing
    articleDiv.style.userSelect = "none";
    articleMenuDiv.style.userSelect = "none";
});

/**
 * Function to give to an event listener on mousemove event to get the width and resize correctly.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} e 
 */
const mouseMoveHandler = function(e) 
{
    if(e.clientX < MINSIZE) return;
    const dx = e.clientX - x;
    const newLeftWidth = (leftWidth - dx)*100/articleDiv.getBoundingClientRect().width;
    articleMenuDiv.style.width = `${newLeftWidth}%`;

};

/**
 * Function to give to an event listener on mouseup event to stop resize mode.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} e 
 */
const mouseUpHandler = function() 
{
    document.removeEventListener('mousemove', mouseMoveHandler);
    document.removeEventListener('mouseup', mouseUpHandler);
    //Turn back selection
    articleDiv.style.userSelect = "auto";
    articleMenuDiv.style.userSelect = "auto";
};
