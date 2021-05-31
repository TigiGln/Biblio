//script to sort the tables alphabetically and according to the desired columns
var compare = function(ids, asc)
{
    return function(row1, row2)
    {
        var tdValue = function(row, ids)
        {
            return row.children[ids].textContent;  
        }
        var tri = function(v1, v2)
        {
            if (v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2))
            {
                return v1 - v2;
            }
            else 
            {
                return v1.toString().localeCompare(v2);
            }
        };
        return tri(tdValue(asc ? row1 : row2, ids), tdValue(asc ? row2 : row1, ids));
    }
}

var tbody = document.querySelector('tbody');
var headsTable = document.querySelectorAll('th');
var headsSortTable = document.querySelectorAll('th.sort_column');
var linesTable = tbody.querySelectorAll('tr');
headsSortTable.forEach(function(headSort)
{
    
    headSort.addEventListener('click', function()
    {
        //The this.asc = !this.asc part allows you to define a boolean whose logical value will be inverted each time a header element is clicked. 
        //This will then allow us to choose the sort order.
        var linesSort = Array.from(linesTable).sort(compare(Array.from(headsTable).indexOf(headSort), this.asc = !this.asc));
        linesSort.forEach(function(line)
        {
            tbody.appendChild(line)
        });
    })
});