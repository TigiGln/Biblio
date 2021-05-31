function launch_table_cazy(num_access)
{
    var url = "../modules/edit_article_menu/cazy/cazy_table.php";
    let params = "NUMACCESS=" + num_access;

    var http = new XMLHttpRequest();
    http.open("GET", url + "?" + params, true);
    http.onreadystatechange = function()
    {
        if (http.readyState === 4)
        {
            if (http.status === 200)
            {
                //var data = JSON.parse(this.response);
                document.getElementById("cazy").innerHTML += this.response;
                load_lien()
                
                
            }
            else 
            { //Si le serveur a eu une erreur
                //document.querySelector("#info").innerHTML = '<div class="alert alert-danger" role="alert">An error occured. Please reload.</div>';
            }
        }

    }
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send();
}
//This is to use parameters of the page, let say we only have a parameter called id
var queryString = window.location.search;
var urlParams = new URLSearchParams(queryString);
launch_table_cazy(urlParams.get('NUMACCESS'));