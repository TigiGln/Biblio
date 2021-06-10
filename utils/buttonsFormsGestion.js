/*
 * Created on Tue May 25 2021
 * Latest update on Tue May 25 2021
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

/**
 * updateButtons will change the current section's button to have a full background and change the other to have an otuline background
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The DOM id of the button that was clicked/selected
 * @param {*} type
 *            The type of buttons (danger, warning, info etc.)
 */
 function updateButtons(id, type = "info") {
    let buttons = document.getElementsByClassName('formButton');
    for(let i = 0; i<buttons.length; i++) {
        button = buttons[i];
        if(button.id == id) {
            button.classList.remove("btn-outline-"+type);
            button.classList.add("btn-"+type);
        } else {
            button.classList.remove("btn-"+type);
            button.classList.add("btn-outline-"+type);
        }
    }
}

/**
 * activate will disable or not the validate buttons depending of the datas given
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} data 
 *            A string separated with space for the different ID that has to not be empty to allows the button to perform any action on click.
 */
 function activate(data) {
    let buttons = document.getElementsByClassName("validate");
    for(let i = 0; i<buttons.length; i++) {
        buttons[i].disabled = false;
    }
    let ids = data.split(" ");
    ids.forEach(element => {
        if(document.getElementById(element).value.length == 0) {
            for(let i = 0; i<buttons.length; i++) {
                buttons[i].disabled = true;
            }
        }
    });
}