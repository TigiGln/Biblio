/*
script for the management of the insertion table in the result part. 
Change of the colours for the lines of articles already present
Management of the global checkbox.
Blocking of the insertion of articles to avoid duplication
*/
var body = document.getElementsByTagName('body')[0].setAttribute('onload', 'load_body()');
function load_body()
{
    var insert = document.getElementById('insert').setAttribute('disabled', true);
    changeColorLigne();
}


function changeColorLigne()
{
    var table = document.getElementsByTagName('table')[0];
    var tableRow = table.rows;
    var tableRowLength = tableRow.length;
    var checks = document.getElementsByClassName('check');
    var listNumAccess = [];
    var title = document.getElementsByClassName('survol_title');
    var authors = document.getElementsByClassName('survol_authors');
    for(var i=1; i<tableRowLength; i++)
    {

        tableCells = tableRow[i].cells;
        listNumAccess.push(tableCells[0].innerHTML);
        title[i-1].style.color = '#000';
        title[i-1].style.fontWeight = 'bold';
        title[i-1].style.textDecoration = 'none';
        authors[i-1].style.color = '#000';
        authors[i-1].style.fontWeight = 'bold';
        authors[i-1].style.textDecoration = 'none';

    }
    listAccessDb = Object.values(listNumAccessDb);
    for(numAccess of listAccessDb)
    {
        var indexNumAccess = listNumAccess.indexOf(numAccess) ;
        if (indexNumAccess !== -1)
        {
            tableRow[indexNumAccess + 1].style.background = "#A9A9A9";
            checks[indexNumAccess + 1].setAttribute('disabled', true);
            checks[indexNumAccess + 1].style.display = 'none';
        }
    }
}

function check(source) 
{
    checkboxes = document.querySelectorAll("input[name^='check']");
    for(var i=0, n=checkboxes.length;i<n;i++) 
    {
        if (checkboxes[i].disabled == false)
        {
            checkboxes[i].checked = source.checked;
        }
        
    }
}
function checked_check(coche)
{
    var arraycheck = [];
    var checkdisabled = [];
    var insert = document.getElementById('insert');
    var globalCheck = document.getElementById('global_check');
    var checkboxes = document.getElementsByName('check[]');
    for(element of checkboxes)
    {
        if (element.disabled == false)
        {
            if (element.checked == true)
            {
                arraycheck.push(element);
            }
        }
        else
        {
            checkdisabled.push(element);
        }
    }
    if (arraycheck.length >= 1)
    {
        insert.removeAttribute('disabled');
    }
    else
    {
        insert.setAttribute('disabled', true);
    }
    if (arraycheck.length != (checkboxes.length-checkdisabled.length))
    {
        globalCheck.checked = false;
    }
    else
    {
        globalCheck.checked = true;
    }
    
    
}