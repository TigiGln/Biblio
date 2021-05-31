/*
 * Created on Mon May 17 2021
 * Latest update on Thu May 20 2021
 * Info - JS for members management
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

//Which form to show first, comment or remove if you don't want to open a section at start
//showAddForm();
var usersJSON;

/*******************************************************************************/
/* management function */
/*******************************************************************************/

/**
 * updateUser will send data from the form to server side and handle its answer.
 * If success, has to change the menu name in DOM.
 * Successfull if we are the user, and if we gave a password and current password, if current password match the one in the database.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @fires XMLHttpRequest
 */
function updateUser() 
{
    let username = document.getElementById('name_user').value;
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let currentPassword = document.getElementById('currentPassword').value;
    let params = "username=" + encodeURIComponent(username) + "&email=" + encodeURIComponent(email) + "&currentPassword=" + encodeURIComponent(currentPassword) + "&password=" + encodeURIComponent(password);
    /* Prepare request */
    let url = "./update.php";
    /* Fires request */
    var http = new XMLHttpRequest();
    http.open("GET", url+"?"+params, true);
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send(null);
    /* Handle request results */
    http.onreadystatechange = function() 
    {
        if (http.readyState === 4) 
        {
            if (http.status === 200) 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-info" role="alert">Successfully Updated your information.</div>';
                document.getElementById("menuUsername").innerHTML = username;
            } 
            else if (http.status === 403) 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">This username already exists.</div>';
            } 
            else if (http.status === 405)
            {
                 document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">Current password missmatch.</div>';
            } 
            else 
            {
            document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">Failed to Update.</div>';
            }
        }
    }
}

/**
 * addMember will send data from the form to server side and handle its answer.
 * Successfull if we have expert profile, then if the user don't already exist and if we could add it to the database.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @fires XMLHttpRequest
 */
function addMember() 
{
    let username = document.getElementById('name_user').value;
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let profile = document.getElementById('profile').value;
    let params = "username="+encodeURIComponent(username)+"&email="+encodeURIComponent(email)+"&password="+encodeURIComponent(password)+"&profile="+encodeURIComponent(profile);
    /* Prepare request */
    let url = "./register.php";
    /* Fires request */
    var http = new XMLHttpRequest();
    http.open("GET", url+"?"+params, true);
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send(null);
    /* Handle request results */
    http.onreadystatechange = function() 
    {
        if (http.readyState === 4) 
        {
            if (http.status === 200) 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-info" role="alert">Successfully Added '+username+' as '+profile+'.</div>';
                cleanForm();
            } 
            else if (http.status === 403) 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">'+username+' already exists.</div>';
            } 
            else if (http.status === 401) 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">You don\'t have the rights to perfom this action.</div>';
            } 
            else 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">Failed to Add '+username+' as '+profile+'.</div>';
            }
        }
    }
}

/**
 * updateMember will send data from the form to server side and handle its answer.
 * Successfull if we have expert profile, then if the user exist and if we could update it in the database.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @fires XMLHttpRequest
 */
function updateMember() 
{
    let oldUsername = document.getElementById('selectedUser').value;
    let username = document.getElementById('name_user').value;
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let profile = document.getElementById('profile').value;
    let users = document.getElementsByClassName('users');
    let id = "";
    for(let i = 0; i<users.length; i++) 
    {
        if(users[i].value == oldUsername) 
        {
            id = users[i].dataset.id;
            break;
        }
    }
    let params = "username="+encodeURIComponent(username)+"&email="+encodeURIComponent(email)+"&password="+encodeURIComponent(password)+"&profile="+encodeURIComponent(profile)+"&id="+encodeURIComponent(id);
    /* Prepare request */
    let url = "./update.php";
    /* Fires request */
    var http = new XMLHttpRequest();
    http.open("GET", url+"?"+params, true);
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send(null);
    /* Handle request results */
    http.onreadystatechange = function() 
    {
      if (http.readyState === 4) 
      {
        if (http.status === 200) 
        {
            document.getElementById("info").innerHTML = '<div class="alert alert-info" role="alert">Successfully Updated '+oldUsername+' as '+username+", "+profile+'.</div>';
            cleanForm();
            loadUsersList();
        } 
        else if (http.status === 403) 
        {
            document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">'+username+' don\'t exists.</div>';
        } 
        else if (http.status === 401) 
        {
                document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">You don\'t have the rights to perfom this action.</div>';
        } 
        else 
        {
            document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">Failed to Update '+username+' as '+profile+'.</div>';
        }
      }
    }
}

/**
 * updateMember will send data from the form to server side and handle its answer.
 * Successfull if we have expert profile, then if the user exist and if we could delete it from the database.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @fires XMLHttpRequest
 */
function deleteMember() 
{
    let username = document.getElementById('selectedUser').value;
    let users = document.getElementsByClassName('users');
    let id = "";
    for(let i = 0; i<users.length; i++) 
    {
        if(users[i].value == username) 
        {
            id = users[i].dataset.id;
            break;
        }
    }
    let params = "username="+encodeURIComponent(username)+"&id="+encodeURIComponent(id);
    /* Prepare request */
    let url = "./delete.php";
    /* Fires request */
    var http = new XMLHttpRequest();
    http.open("GET", url+"?"+params, true);
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send(null);
    /* Handle request results */
    http.onreadystatechange = function() 
    {
        if (http.readyState === 4) 
        {
            if (http.status === 200) 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-info" role="alert">Successfully Deleted '+username+'.</div>';
                cleanForm();
                loadUsersList();
            } 
            else if (http.status === 403) 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">'+username+' Don\'t exists.</div>';
            } 
            else if (http.status === 401) 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">You don\'t have the rights to perfom this action.</div>';
            } 
            else 
            {
                document.getElementById("info").innerHTML = '<div class="alert alert-danger" role="alert">Failed to Delete '+username+'.</div>';
            }
        }
    }
}

/*******************************************************************************/
/* activate function */
/*******************************************************************************/

/**
 * activate will disable or not the validate buttons depending of the datas given
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} data 
 *            A string separated with space for the different ID that has to not be empty to allows the button to perform any action on click.
 */
function activate(data) 
{
    let buttons = document.getElementsByClassName("validate");
    for(let i = 0; i<buttons.length; i++) 
    {
        buttons[i].disabled = false;
    }
    let ids = data.split(" ");
    ids.forEach(element => {
        if(document.getElementById(element).value.length == 0) 
        {
            for(let i = 0; i<buttons.length; i++) 
            {
                buttons[i].disabled = true;
            }
        }
    });
}

/*******************************************************************************/
/* show function */
/*******************************************************************************/

/**
 * showUserForm will show the user form in the form div (using DOM)
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function showUserForm()
{
    cleanDivs();
    getUserDataAndForm();
    updateButtons("userForm");
}

/**
 * getUserDataAndForm is called by showUserForm, to perform a query to the server to get user info and then show the form
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @fires XMLHttpRequest
 */
function getUserDataAndForm() 
{
    /* Prepare request */
    //If you deleted this module, please change path to use the correct file
    let url = "./getUserData.php";
    /* Fires request */
    var http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send(null);
    /* Handle request results */
    http.onreadystatechange = function() 
    {
        if (http.readyState === 4) 
        {
            if (http.status === 200) 
            {
                let user = JSON.parse(this.response);
                document.getElementById('form').innerHTML = `
                <div class="form-group pt-4">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Username</span>
                        </div>
                        <input class="form-control" type="text" name="name_user" id="name_user" required oninput="activate('name_user email')" value="`+user[0]['name_user']+`">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Email</span>
                        </div>
                        <input class="form-control" type="email" name="email" id="email" required oninput="activate('name_user email')" value="`+user[0]['email']+`">
                    </div>
                    <div class="input-group">
                    <span class="input-group-text">Current Password</span>
                    <input class="form-control" type="password" name="currentPassword" id="currentPassword" required oninput="activate('name_user email')">
                    <span class="input-group-text">New Password</span>
                    <input class="form-control" type="password" name="password" id="password" required oninput="activate('name_user email')">
                    </div>
                    <p class="text-muted mb-3">Leave it empty if you don't want to change your password</p>
                    <div class="form-group pb-4">
                        <button class="validate btn btn-outline-success" onclick="updateUser()" disabled>Change Information</button>
                    </div>
                </div>
                `;
                activate('name_user email');
            } 
            else 
            {
            document.querySelector("#info").innerHTML = '<div class="alert alert-danger" role="alert">An error occured. Please reload.</div>';
            }
        }
    }
}

/**
 * showAddForm will show the add member form in the form div (using DOM)
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function showAddForm() 
{
    cleanDivs();
    document.getElementById('form').innerHTML = `
    <div class="form-group pt-4">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Username</span>
            </div>
            <input class="form-control" type="text" name="name_user" id="name_user" required oninput="activate('name_user email profile password')">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Email</span>
            </div>
            <input class="form-control" type="email" name="email" id="email" required oninput="activate('name_user email profile password')">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Password</span>
            </div>
            <input class="form-control" type="password" name="password" id="password" required oninput="activate('name_user email profile password')">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Profile</span>
            </div>
            <select class="form-select" name="profile" id="profile" required oninput="activate('name_user email profile password')">
                <option value="expert">Expert</option>
                <option value="assistant">Assistant</option>
            </select>
        </div>
        <div class="form-group pb-4">
            <button class="validate btn btn-outline-success" onclick="addMember()" disabled>Add Member</button>
        </div>
    </div>
    `;
    updateButtons("addForm");
}

/**
 * showManageForm will show the manage form in the form div (using DOM)
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function showManageForm() 
{
    cleanDivs();
    document.getElementById('form').innerHTML = `
    <div class="row justify-content-start">
        <div class="col-4">
            <input type="text" list="usersList" id="selectedUser" name="selectedUser" class="form-control" placeholder="Member Name" oninput="showManageUser()" />
            <datalist id="usersList"></datalist>
        </div>
        <div class="col" id="formBis">
        </div>
    </div>
    `;
    loadUsersList();
    updateButtons("manageForm");
}

/**
 * showManageUser will be called by the form created by showManageForm. It will show the complete manage form in formBis.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function showManageUser() 
{
    //Check if value exist
    let selected = document.getElementById("selectedUser").value;
    let all = document.getElementsByClassName("users");
    document.getElementById('formBis').innerHTML = "";
    for(let i = 0; i<all.length; i++) {
        if(selected === all[i].value) {
            document.getElementById('formBis').innerHTML = `
            <div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Username</span>
                    </div>
                    <input class="form-control" type="text" name="name_user" id="name_user" value="`+selected+`" required oninput="activate('name_user email profile')">
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Email</span>
                    </div>
                    <input class="form-control" type="email" name="email" id="email" value="`+all[i].dataset.email+`" required oninput="activate('name_user email profile')">
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Password</span>
                    </div>
                    <input class="form-control" type="password" name="password" id="password" oninput="activate('name_user email profile')">
                </div>
                <p class="text-muted mb-3">Leave it empty if you don't want to change this member's password</p>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Profile</span>
                    </div>
                    <select class="form-select" name="profile" id="profile" required oninput="activate('name_user email profile')">
                        <option value="expert">Expert</option>
                        <option value="assistant">Assistant</option>
                    </select>
                </div>
                <div class="form-group pb-4">
                    <div class="row">
                    <div class="col-md-auto"><button class="validate btn btn-outline-success" onClick="updateMember()">Update</button></div>
                    <div class="col-md-auto"><button class="validate btn btn-outline-danger" onClick="deleteMember()">Delete</button></div>
                </div>
            </div>
            `;
            break;
        }
    }
}

/**
 * updateButtons will change the current section's button to have a full background and change the other to have an otuline background
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @param {*} id 
 *            The DOM id of the button that was clicked/selected
 */
function updateButtons(id) 
{
    let buttons = document.getElementsByClassName('formButton');
    for(let i = 0; i<buttons.length; i++) 
    {
        button = buttons[i];
        if(button.id == id) 
        {
            button.classList.remove("btn-outline-info");
            button.classList.add("btn-info");
        } 
        else 
        {
            button.classList.remove("btn-info");
            button.classList.add("btn-outline-info");
        }
    }
}

/**
 * loadUsersList will load the user list in the corresponding input form (in its dataset)
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function loadUsersList() 
{
    let usersList = document.getElementById('usersList');
    usersList.innerHTML = "";
    /* Prepare request */
    //If you deleted this module, please change path to use the correct file
    let url = "../modules/edit_article_menu/send/getUsersList.php";
    /* Fires request */
    var http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send(null);
    /* Handle request results */
    http.onreadystatechange = function() 
    {
        if (http.readyState === 4) 
        {
            if (http.status === 200) 
            {
                usersJSON = JSON.parse(this.response);
                for (let i = 0; i < usersJSON.length; i++) 
                {
                    let user = usersJSON[i];
                    //usersList.innerHTML += '<option class="users" value="'+user['name_user']+'"data-profile="'+user['profile']+'" data-id="'+user['id_user']+'">'+user['email']+'</option>';
                    usersList.innerHTML += '<option class="users" value="'+user['name_user']+'"data-profile="'+user['profile']+'"data-email="'+user['email']+'" data-id="'+user['id_user']+'">';
                }
            } 
            else 
            {
                document.querySelector("#send").innerHTML = '<div class="alert alert-danger" role="alert">An error occured. Please reload.</div>';
            }
        }
    }
}

/**
 * cleanDivs will remove the content from the div that contains the forms, we delete the form from DOM
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function cleanDivs() 
{
    if(document.getElementById('info') != null) document.getElementById('info').innerHTML = "";
    if(document.getElementById('form') != null) document.getElementById('form').innerHTML = "";
    if(document.getElementById('formBis') != null) document.getElementById('formBis').innerHTML = "";
}

/**
 * cleanForm will empty the current form values
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
function cleanForm() 
{
    if(document.getElementById('name_user') != null) document.getElementById('name_user').value = "";
    if(document.getElementById('email') != null) document.getElementById('email').value = "";
    if(document.getElementById('password') != null) document.getElementById('password').value = "";
    if(document.getElementById('selectedUser') != null) document.getElementById('selectedUser').value = "";
}