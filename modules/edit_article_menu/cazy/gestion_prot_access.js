//asynchronous function to add a number of protein accessions
function add_prot_access(num_access, prot_access)
{
    if (urlParams.get('ORIGIN'))
    {
        var url = "../modules/edit_article_menu/cazy/gestion_cazy.php";
    }
    else
    {
        var url = "./gestion_cazy.php";
    }
    let params = "num_access=" + num_access + "&prot_access=" + prot_access + "&function=add";

    var http = new XMLHttpRequest();
    http.open("GET", url + "?" + params, true);
    http.onreadystatechange = function()
    {
        info_add = document.getElementById('add_prot_access');
        if (http.readyState === 4)
        {
            if (http.status === 200)
            {
                var data = this.response;
                if(data.trim() == 'add prot_access')
                {
                    info_add.innerHTML = "<div class='alert alert-info' role='alert'>Your accession has been added</div>";
                }
                else if (data.trim() == "The accession number already exists")
                {
                    info_add.innerHTML = "<div class='alert alert-info' role='alert'>This number already exists</div>";
                }
            }
            else if( http.status === 404)
			{
				info_add.innerHTML = "<div class='alert alert-danger' role='alert'>Please enter an accession number</div>";
			}
			else
            { //Si le serveur a eu une erreur
                info_add.innerHTML = "<div class='alert alert-danger' role='alert'>A problem has arisen</div>";
            }
        }

    }
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send();
}
//asynchronous function to delete a number of protein accessions
function delete_prot_access(prot_access)
{
    if (urlParams.get('ORIGIN'))
    {
        var url = "../modules/edit_article_menu/cazy/gestion_cazy.php";
    }
    else
    {
        var url = "./gestion_cazy.php";
    }
    let params = "prot_access=" + prot_access + "&function=delete";

    var http = new XMLHttpRequest();
    http.open("GET", url + "?" + params, true);
    http.onreadystatechange = function()
    {
        info_add = document.getElementById('add_prot_access');
        if (http.readyState === 4)
        {
            if (http.status === 200)
            {
                var data = this.response;
                if(data.trim() == 'prot_access delete')
                {
                    info_add.innerHTML = "<div class='alert alert-info' role='alert'>Your accession has been deleted</div>";
                    console.log(info_add);
                }
            }
            else 
            { //Si le serveur a eu une erreur
                info_add.innerHTML = '<div class="alert alert-danger" role="alert">A problem has arisen</div>';
            }
        }

    }
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send();
}

var queryString = window.location.search;
var urlParams = new URLSearchParams(queryString);
//click button  add function to call the asynchronous function and to perform table update tasks
function click_add()
{
    var info_add = document.getElementById('add_prot_access');
    var table = document.getElementById('table_cazy');
    prot_access = document.getElementById('input_prot_access').value
    add_prot_access(urlParams.get('NUMACCESS'), prot_access);
    reload_table_cazy();
    info_add.innerHTML = 'Your accession has been added';
}
//click button  del function to call the asynchronous function and to perform table update tasks
function click_delete(protAccess)
{
    
    var table = document.getElementById('table_cazy');
    protAccess = protAccess.id.split('input_');
    delete_prot_access(protAccess[1]);
    var line = document.getElementById('line_' + protAccess[1]);
    table.deleteRow(line.rowIndex);
}
//function to reload the table only (div)
function reload_table_cazy()
{
    if (urlParams.get('ORIGIN'))
    {
        $("#cazy").load('../modules/edit_article_menu/cazy/cazy_table.php?NUMACCESS=' + urlParams.get('NUMACCESS'));  
    }
    else
    {
        $('#cazy').load('./cazy_table.php?body=1&NUMACCESS=' + urlParams.get('NUMACCESS'));
    }
}

