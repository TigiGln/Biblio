<?php
    class Article
    {
        protected $origin;
        protected $num_access;
        protected $title;
        protected $abstract;
        protected $year;
        protected $journal;
        protected $pmcid;
        protected $status;
        protected $authors;
        protected $user;
        
        
        //public function __construct($num_access, $title, $abstract, $year, $journal, $pmcid, $authors, $origin = "pubmed", $status = '1')
        public function __construct(...$elements)
        {
            $list_attributes = $this->iter();
            $list_attributes = array_keys($list_attributes);
            for($i=0; $i<count($elements); $i++)
            {
                $this->setter($list_attributes[$i], $elements[$i]);
            }
        }
        //La méthode get
        public function getter($get)
        {
            return $this->$get;
        }
        //La méthode set
        public function setter($attributs, $values)
        {
            if (is_string($values))
            {
                //$attributs = 'set' . $attributs;
                $this->$attributs = $values;
            } 
        }
        //Methodes permettant de créer un set et un get si méthode opar créé
        function __call($method,$param) 
        {
            $attribut = strtolower(substr($method,3));
            if (!strncasecmp($method,'get',3))
            {
                return $this->$attribut;
            }
            if (!strncasecmp($method,'set',3)) 
            {
                $this->$attribut = $param[0];
            }
        }
        //méthode pour l'echo de l'objet
        public function __toString()
        {
            $list_attribute = [];
            foreach($this as $key => $value)
            {
                $list_attribute[] = $value;
            }
            $list_attribute = implode(', ', $list_attribute);
            return $list_attribute;
        }
        //methode pour l'itération de l'objet
        public function iter()
        {
            $object_list = [];
            foreach ($this as $key => $value)
            {
                $object_list[$key] = $value;
            }
            //var_dump($object_list);
            return $object_list;
        }
        
  
}





?>