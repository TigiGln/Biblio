<?php
/**
 * MainMenu
 * 
 * Created on Tue Apr 22 2021
 * Latest update on Tue May 18 2021
 * Info - PHP Class for the main menu
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

class MainMenu {
 
    protected $title;
    protected $position; 
    //boolean to activate some menu parts
    protected $Tasks;
    protected $Undefined;
    protected $Members_Tasks;
    protected $Members_Undefined;
    protected $Processed_Tasks;
    protected $Rejected_Tasks;
    protected $Insertion;
    protected $MembersManagement;
  
    /**
     * __construct
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $position
     *            position refers to which part of the menu is active, in which part of the menu we are actually.
     * @return void
     */
    public function __construct($position, $manager) {
        $this->title = "Outil Biblio";
        $this->position = $position;
        $this->setTasks(true);
        $this->setUndefined(true);
        $this->setMembersTasks(true);
        $this->setMembersUndefined(true);
        $this->setProcessedTasks(true);
        $this->setRejectedTasks(true);
        $this->setInsertion(true);
        $this->setMembersManagement(true); 
    }

    /**
     * writeSubMenus function will add the menu's subMenus that are activated in the given variable and return it.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return $html
     */
    public function writeSubMenus($html) {
      /* section user tasks */
      if($this->Tasks) {
          $html = $this->writeOne($html, 'Tasks', '../tables/articles.php', "?status=tasks");
      }
      if($this->Undefined) {
          $html = $this->writeOne($html, 'Undefined', '../tables/articles.php', "?status=undefined");
      }
      /* section members tasks */
      if($this->Members_Tasks && $this->Members_Undefined) { $html = $this->writeSep($html); }
      if($this->Members_Tasks) {
        if(!$this->Members_Undefined) { $html = $this->writeSep($html); }
        $html = $this->writeOne($html, 'Members_tasks', '../tables/articles.php', "?status=members_tasks");
      }
      if($this->Members_Undefined) {
        if(!$this->Members_Tasks) { $html = $this->writeSep($html); }
        $html = $this->writeOne($html, 'Members_undefined', '../tables/articles.php', "?status=members_undefined");
      }
      /* section conclued tasks */
      if($this->Processed_Tasks && $this->Rejected_Tasks) { $html = $this->writeSep($html); }
      if($this->Processed_Tasks) {
        if(!$this->Rejected_Tasks) { $html = $this->writeSep($html); }
          $html = $this->writeOne($html, 'Processed', '../tables/articles.php', "?status=processed");
      }
      if($this->Rejected_Tasks) {
        if(!$this->Processed_Tasks) { $html = $this->writeSep($html); }
          $html = $this->writeOne($html, 'Rejected', '../tables/articles.php', "?status=rejected");
      }
      /* section insertion*/
      if($this->Insertion) {
          $html = $this->writeSep($html);
          $html = $this->writeOne($html, 'Insertion', '../insertion/form.php', "");
      }
      /* section expert */
      if($this->MembersManagement) {
        $html = $this->writeSep($html);
        $html = $this->writeOne($html, 'Members_Management', '../connection/members_management.php', "");
    }

      return $html;
    }

    
    /**
     * write function will echo the menu's html for each active sections
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return void
     */
    public function write() {
        $html = '<div>
                <button type="button" class="btn btn-info align-middle text-white" data-bs-toggle="offcanvas" data-bs-target="#mainMenu" role="button" style="height: 100vh;">
                    <p data-bs-toggle="offcanvas" data-bs-target="#mainMenu">&#9776;</p>
                </button>
                <div class="menu offcanvas offcanvas-start d-flex flex-column" tabindex="-1" id="mainMenu" data-bs-keyboard="false" data-bs-backdrop="false" width: 15em;>
                    <div class="offcanvas-header">
                        <div class="offcanvas-title col-md-auto">
                            <img src="../pictures/logo_small-top.png" width="30">
                            <span class="fs-5">'.$this->title.'</span>
                        </div>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" data-bs-target="#mainMenu"></button>
                    </div>
                    <hr>
                    <div class="offcanvas-body">
                        <ul id="subMenu" class="nav nav-pills flex-column mb-auto">';
        $html = $this->writeSubMenus($html) . '</ul></div>
                          <div class="row justify-content-center">
                            <div class="col col-md-auto">
                              <a href="http://www.afmb.univ-mrs.fr" target="_blank">
                                <img src="../pictures/logo_afmb.png" width="50" height="50">
                              </a>
                            </div>
                            <div class="col col-md-auto">
                              <a href="https://www.cea.fr/Pages/le-cea/les-centres-cea/cadarache.aspx" target="_blank">
                                <img src="../pictures/logo_cea.png" width="50" height="50">
                              </a>
                            </div>
                          </div>
                          <hr>
                          <div>
                            <div class="row justify-content-start m-1 p-1">
                              <div class="col-md-auto">
                                <!-- Disconenct Button -->
                                <form action="../connection/disconnect.php" method="post">
                                  <button class="btn btn-outline-danger" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
                                      <path d="M7.5 1v7h1V1h-1z"></path>
                                      <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"></path>
                                    </svg>
                                  </button>
                                </form>
                              </div>
                              <div class="col-md-auto mt-1">
                                <strong id="menuUsername">' . ucfirst($_SESSION['username']) . '</strong>
                              </div>
                            </div>
                            <br>
                          </div>
                        </div>
                    </div></div>';

        //echo final html string
        echo $html;
    }

    /**
     * writeLegacy function will echo the menu's html for each active sections
     * This is an older menu, that is always showed.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return void
     */
    public function writeLegacy() {
        $html = '<div class="menu d-flex flex-column bg-light p-3 sticky-top" style="width: 16em; height: 100vh;">
                    <div class="col-md-auto">
                        <img src="../pictures/logo_small-top.png" width="30">
                        <span class="fs-5">'.$this->title.'</span></div><hr>
                        <ul id="subMenu" class="nav nav-pills flex-column mb-auto">';
        $html = $this->writeSubMenus($html) . '</ul>
                          <div class="row justify-content-center">
                            <div class="col col-md-auto">
                              <a href="http://www.afmb.univ-mrs.fr" target="_blank">
                                <img src="../pictures/logo_afmb.png" width="50" height="50">
                              </a>
                            </div>
                            <div class="col col-md-auto">
                              <a href="https://www.cea.fr/Pages/le-cea/les-centres-cea/cadarache.aspx" target="_blank">
                                <img src="../pictures/logo_cea.png" width="50" height="50">
                              </a>
                            </div>
                          </div>
                          <hr>
                          <div>
                            <div class="row justify-content-start">
                              <div class="col-md-auto">
                                <!-- Disconenct Button -->
                                <form action="../connection/disconnect.php" method="post">
                                  <button class="btn btn-outline-danger" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
                                      <path d="M7.5 1v7h1V1h-1z"></path>
                                      <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"></path>
                                    </svg>
                                  </button>
                                </form>
                              </div>
                              <div class="col-md-auto mt-1">
                                <strong id="menuUsername">'.$_SESSION['username'].'</strong>
                              </div>
                            </div>
                          </div>
                        </div>';
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
     * @param  mixed $file
     *            $file refers to the file or the html/php/etc item to go as href when we click on this menu section.
     * @param  mixed $parameters
     *            $parameters refers to the possibly given parameters of $file (can be blank).
     * @return void
     */
    private function writeOne($html, $value, $file, $parameters) {
        $valueSpace = str_replace('_', ' ', $value);
        $html = $html . '<li class="nav-item">
                            <a href="'.$file.$parameters.'" class="nav-link link-dark ';
        if(strtolower($this->position) == strtolower($value)) { $html = $html . 'active text-dark'; }
        $html = $html . '">'.$valueSpace.'</a></li>';
        return $html;
    }

    private function writeSep($html) {
      return $html . "<hr>";
  }

        
    /**
     * setTasks is the setter to activate or not the section of the same name.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $value
     *            boolean value.
     * @return void
     */
    public function setTasks($value) {
        if (is_bool($value)) { $this->Tasks = $value; }
    }

    /**
     * setUndefined is the setter to activate or not the section of the same name.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $value
     *            boolean value.
     * @return void
     */
    public function setUndefined($value) {
        if (is_bool($value)) { $this->Undefined = $value; }
    }

    /**
     * setMembersTasks is the setter to activate or not the section of the same name.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $value
     *            boolean value.
     * @return void
     */
    public function setMembersTasks($value) {
      if (is_bool($value)) { $this->Members_Tasks = $value; }
    }

    /**
     * setMembersUndefined is the setter to activate or not the section of the same name.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $value
     *            boolean value.
     * @return void
     */
    public function setMembersUndefined($value) {
      if (is_bool($value)) { $this->Members_Undefined = $value; }
    }

    /**
     * setProcessedTasks is the setter to activate or not the section of the same name.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $value
     *            boolean value.
     * @return void
     */
    public function setProcessedTasks($value) {
        if (is_bool($value)) { $this->Processed_Tasks = $value; }
    }

    /**
     * setRejectedTasks is the setter to activate or not the section of the same name.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $value
     *            boolean value.
     * @return void
     */
    public function setRejectedtasks($value) {
        if (is_bool($value)) { $this->Rejected_Tasks = $value; }
    }
    /**
     * setInsertion is the setter to activate or not the section of the same name.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $value
     *            boolean value.
     * @return void
     */
    public function setInsertion($value) {
        if (is_bool($value)) { $this->Insertion = $value; }
    }
    /**
     * setMembersManagement is the setter to activate or not the section of the same name.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $value
     *            boolean value.
     * @return void
     */
    public function setMembersManagement($value) {
      if (is_bool($value)) { $this->MembersManagement = $value; }
  }
}?>