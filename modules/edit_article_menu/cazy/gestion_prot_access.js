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
                if(data.trim() == 'prot_access ajouté')
                {
                    info_add.innerHTML = 'Your accession has been added';
                }
                else if (data.trim() == "Le numéro d'accession existe déjà")
                {
                    info_add.innerHTML = 'This number already exists';
                }
            }
            else 
            { //Si le serveur a eu une erreur
                info_add.innerHTML = 'A problem has arisen';
            }
        }

    }
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send();
}
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
                    info_add.innerHTML = 'Your accession has been deleted';
                    console.log(info_add);
                }
            }
            else 
            { //Si le serveur a eu une erreur
                info_add.innerHTML = 'A problem has arisen';
            }
        }

    }
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send();
}

var queryString = window.location.search;
var urlParams = new URLSearchParams(queryString);
function click_add()
{
    var info_add = document.getElementById('add_prot_access');
    var table = document.getElementById('table_cazy');
    prot_access = document.getElementById('input_prot_access').value
    add_prot_access(urlParams.get('NUMACCESS'), prot_access);
    reload_table_cazy();
    info_add.innerHTML = 'Your accession has been added';
}
function click_delete(protAccess)
{
    
    var table = document.getElementById('table_cazy');
    protAccess = protAccess.id.split('input_');
    delete_prot_access(protAccess[1]);
    var line = document.getElementById('line_' + protAccess[1]);
    table.deleteRow(line.rowIndex);
}

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

