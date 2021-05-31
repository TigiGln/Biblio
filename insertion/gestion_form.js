//script for managing the insertion form to avoid the passage of erroneous data for the queries to be made
var submit = document.getElementById("submit");
let textarea = document.getElementById("textarea");
let select = document.getElementById("list_query");
var retmax = document.getElementById('retmax');
var myFile = document.getElementById('myfile');
var changeSelect;

retmax.addEventListener('keyup', function(e)
{
    if(/^[^0-9]+$/.test(parseInt(e.target.value)))
    {
        e.target.style.border = '1px solid red';
        //e.target.value = 1;
        textarea.setAttribute('disabled', true);
    }
    else
    {
        e.target.style.border = '';
        textarea.removeAttribute('disabled');
    }
    if (parseInt(e.target.value) > parseInt(e.target.max))
    {
        e.target.value = e.target.max; 
    }
    else if (parseInt(e.target.value) < parseInt(e.target.min))
    {
        e.target.value = e.target.min; 
    }

});
if (textarea.value == "")
{
    submit.setAttribute('disabled', true);
    textarea.setAttribute('disabled', true);
    retmax.setAttribute('disabled', true);
}

function disabledSubmit(booleen)
{
if(booleen)
    {
    submit.setAttribute("disabled", true);
    }
else
    {
    submit.removeAttribute('disabled');
    }
};


select.addEventListener('input', function(e)
{
    changeSelect = e.target.value;
    textarea.removeAttribute('disabled');
    if (changeSelect == "")
    {
        textarea.setAttribute('disabled', true);
        submit.setAttribute('disabled', true);
    }
    if (changeSelect == "Author")
    {
        retmax.removeAttribute('disabled');
    }
    else
    {
        retmax.setAttribute('disabled', true);
    }
    
});
textarea.addEventListener('input', function(e)
{
    if (changeSelect == 'PMID')
    {
        if(/^[0-9\n\r]+$/.test(e.target.value))
        {
            disabledSubmit(false);
        }
        else
        {
            disabledSubmit(true);
            textarea.setAttribute("oninvalid=\"alert('Please only write numbers');\"");
        }
    }
    else if (changeSelect == 'ELocationID')
    {
        if(/^10\.[0-9]{4}\//.test(e.target.value))
        {
            disabledSubmit(false);
        }
        else
        {
            disabledSubmit(true);
        }
    }
    else if (changeSelect == 'Author')
    {
        
        if(/^[A-Za-z -]+$/.test(e.target.value))
        {
            disabledSubmit(false);   
        }
        else
        {
            disabledSubmit(true);
        }
    }
    else if (changeSelect == 'Title')
    {
        if(/^.+$/.test(e.target.value))
        {
            disabledSubmit(false);
        }
        else
        {
            disabledSubmit(true);
        }
    }
    else if (changeSelect == 'dp')
    {
        if(/^[1-2][09][0-9]{2}$/.test(e.target.value))
        {
            disabledSubmit(false);
        }
        else
        {
            disabledSubmit(true);
        }
    }
    
});
myFile.addEventListener('change', function(e)
{
    if (e.target.value !== "")
    {
        disabledSubmit(false);
        textarea.setAttribute('disabled', true);
        select.setAttribute('disabled', true); 
    }

});



