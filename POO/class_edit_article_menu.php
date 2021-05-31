<?php

/**
 * EditArticleMenu
 * 
 * Created on Tue Apr 22 2021
 * Latest update on Mon May 10 2021
 * Info - PHP Class for the article editing tools' menu
 * The functionning differ from mainMenu, here we include php but never do a href link. 
 * If you require a module in this section asking for parameters, use super variables to store and throw.
 * Example: the page parameters is ?ID=1234, hence it should already live in a super variable, if not add it in $_GET, $_POST or $_GLOBAL to use it in the module.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

class EditArticleMenu {

    protected $article;
    protected $Folder;
    protected $activesModules;

    
    /**
     * __construct
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $NumAccess
     *            The ID of the article in the database.
     * @return void
     */
    public function __construct($article, $activesModules) 
    {
        $this->article = $article;
        $this->Folder = "edit_article_menu";
        $this->activesModules = $activesModules;
    }

    /**
     * write function will echo the menu's html for each active sections
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return void
     */
    public function write() 
    {
        $html = '
        <script>
            function articleGet(value) 
            {
                return document.getElementById(\'ArticleMenu\').dataset[value];
            }
        </script>
        <div class="resizer bg-info" id="dragArticleMenu"></div>
                 <div class="flex bg-light overflow-auto" id="ArticleMenu" style="width: 25em; height: 100vh;" data-art-I-D="'.$this->article['id_article'].'" data-numaccess="'.$this->article['num_access'].'" data-origin="'.$this->article['origin'].'">
                    <div class="accordion accordion-flush bg-light" id="menu-article" >';

        foreach ($this->activesModules as $module)
        {
            $html = $this->writeOne($html, strval($module));
        }
        $html = $html . '</div></div></div>';
        //echo final html string
        echo $html;
    }

    /**
     * writeOne function will write in the given $html string a menu section.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $html
     *            the given $html string that contains the html code of the menu.
     * @param  mixed $value
     *            $value refers to the variable name of the section, or the section name with underscore instead of spaces.
     * @return void
     */
    private function writeOne($html, $value) 
    {
        $file = str_replace(' ', '', strtolower($value));
        $data = file_get_contents('../modules/'.$this->Folder.'/'.$file.'/'.$file.'.php');
        $html = $html . '<div class="accordion-item" >
                            <h2 class="accordion-header">
                            <button id="'.$file.'Btn" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#article-'.$file.'">'.ucfirst($value).'</button>
                            </h2>
                            <div id="article-'.$file.'" class="accordion-collapse collapse">
                                <div class="accordion-body p-0 m-0">'.$data.'</div></div></div>';
        return $html;
    }
}
?>