//Layout when loading the table
function load_lien()
{
    newEltHead = document.createElement('script');
    newEltHead.src = "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js";
    newEltHead.setAttribute('async', true);
    document.head.appendChild(newEltHead);
    lien_add_func = document.getElementsByClassName('lien_add_func');
    add_button = document.getElementById('add_button');
    add_button.setAttribute('disabled', true);
    if(lien_add_func.length > 0)
    {
        lien = lien_add_func[0].getAttribute('href');
        for(var i=0; i<lien_add_func.length; i++)
        {
            lien_add_func[i].style.pointerEvents = 'none';
            lien_add_func[i].style.color = 'black';
            lien_add_func[i].style.textDecoration = "none";
        }
        return lien;
    }
    
}
//link management depending on what is inserted in the ec_num input
function add_ec_num(input)
{
    //console.log(input.previousElementSibling);
    if (input.value != '' && /^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/.test(input.value))
    {
        input.previousElementSibling.setAttribute('href', lien + input.value)
        input.previousElementSibling.style.color = '#0044DD';
        input.previousElementSibling.style.textDecoration = "underline";
        input.previousElementSibling.style.pointerEvents = 'auto';
    }
    else
    {
        input.previousElementSibling.style.pointerEvents = 'none';
        input.previousElementSibling.style.color = 'black';
        input.previousElementSibling.style.textDecoration = "none";

    }  
    
}
//management of the add button according to the data entered in the insertion input
function listen_input(input_prot_access)
{
    add_button = document.getElementById('add_button');
    if(/^[A-Z]{2}_[0-9]+\.[0-9]$/.test(input_prot_access.value) || /^[A-Z]{2,3}[0-9]+\.[0-9]$/.test(input_prot_access.value) || /^[0-9][A-Z]{3}_[A-Z]$/.test(input_prot_access.value))
    {
        add_button.removeAttribute('disabled');
    }
    else
    {
        add_button.setAttribute('disabled', true);
    }
}
