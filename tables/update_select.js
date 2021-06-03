//Asynchronous function calling the php script update_select.php to make the change according to the drop-down menu whose value is modified
function changeSelectAsync(numSelect, statusInitial)
{
    var lineTable = document.getElementById('line_' + numSelect.id); //retrieve the row from the table whose select has been modified
    var linesTable = document.getElementsByTagName('table')[0].rows;//retrieve the rows of the entire table to delete the row
    value_select = numSelect.name.split('_')[0];
    
    for (line of linesTable)//loop over the table row by row
    {
        if (line.id == lineTable.id)//if corresponds to the line whose status is changed then
        {
            xhttp = new XMLHttpRequest(); //creation of the request object to access the php scripts
            //opening the php script request
            xhttp.open("GET", "update_select.php?valueStatusInitial=" + statusInitial + "&" + value_select + "=" + numSelect.value + "&num_acces=" + numSelect.id + "&fields=" + value_select, true);
            xhttp.onreadystatechange = function() //HTML action for state change
            {
                if (this.readyState == 4)//Checking the document load
                {
                    if (this.status == 200) 
                    {
                        var info_change = document.getElementById('info_change');
                        info_change.innerHTML += this.response;
                    }
                    else
                    {
                        document.getElementById('info_change').innerHTML += '<div class="alert alert-danger" role="alert">a problem has arisen</div>';  
                    }
                }
            };
            
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
            xhttp.send(); //launch of the request
            
        }
    }
}
var queryString = window.location.search;
var urlParams = new URLSearchParams(queryString);
//function that runs the asynchronous changes and checks that there is a positive return to delete the list  
function changeSelect(numSelect)
{
    changeSelectAsync(numSelect, urlParams.get('status'));
    var info_change = document.getElementById('info_change');
    if (info_change != 'a problem has arisen')
    {
        var table = document.getElementsByTagName('table')[0];//retrieve the table
        var line = document.getElementById('line_' + numSelect.id);//retrieve the line
        table.deleteRow(line.rowIndex);//you delete the line according to its index in the table
    }
    
}