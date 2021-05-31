/**
 * Created on Fri May 7 2021
 * Latest update on Fri May 7 2021
 * Info - allow to change "free pmc article" links
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

function switchDisplay(value) 
{
    let displays = document.getElementsByClassName("switchDisplay");
    for(let i = 0; i<displays.length; i++) 
    {
        displays[i].hidden = !(displays[i].id == value);
    }
}