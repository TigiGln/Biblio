function delete_article_async(num_access)
{
    var url = "./delete_article/delete_article.php";
    let params = "num_access=" + num_access;

    var http = new XMLHttpRequest();
    http.open("GET", url + "?" + params, true);
    http.onreadystatechange = function()
    {
        if (http.readyState === 4)
        {
            var info_change = document.getElementById('info_change');
            if (http.status === 200)
            {
                var data = this.response;
                if(data.trim() == 'article delete')
                {
                    info_change.innerHTML = "<div class='alert alert-info' role='alert'>L'article a bien été  supprimé</div>";
                }
            }
            else 
            { //Si le serveur a eu une erreur
                info_change.innerHTML = '<div class="alert alert-danger" role="alert">A problem has arisen</div>';
            }
        }

    }
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.send();
}

function delete_article(num_access)
{
    var table = document.getElementsByTagName('table')[0];
    var line = document.getElementById('line_' + num_access);
    delete_article_async(num_access);
    table.deleteRow(line.rowIndex);
}