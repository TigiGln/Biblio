/**
 * Created on Fri May 7 2021
 * Latest update on Mon May 10 2021
 * Info - allow to change "free pmc article" links
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

 function upgradePMCLinks() {
    let references = document.getElementById('reference-list').childNodes;
    for (let i = 0; i<references.length; i++) {
        let links = references[i].getElementsByTagName("a");
        for (let j = 0; j<links.length; j++) {
            let link = links[j].outerHTML;
            let PMC = link.match(/(PMC).*?(\/">)/g);
            if (PMC !== null) {
                PMC = PMC[0].substring(3, PMC[0].length-3);
                let upgrade = 'data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-content="'+
                '<div class=\'row justify-content-center text-center\'><div class=\'col\'>'+
                '<a target=\'_BLANK\' href=\'https://www.ncbi.nlm.nih.gov/pmc/articles/PMC'+PMC+'\'> Read From<br>PubMedCentral </a>'+
                '</div><div class=\'col\'>'+
                '<a target=\'_BLANK\' href=\'../utils/insertAndGo.php?ORIGIN=pubmed&ID='+PMC+'\'> Read From<br>BiblioTool </a>'+
                '</div></div>"';
                link = '<a tabindex="0" class="int-reflink" '+upgrade+'>PMC free article</a>';
                links[j].outerHTML = link;
            }
        }
    }
} 
if((String(new URLSearchParams(window.location.search).get("ORIGIN")) === "pubmed")) {
    //Todo since pubmed central can sometimes refers many elements with the same ids for reference-list, find a handy way to parse its elements (using the class used before take too much time)
    upgradePMCLinks();
}