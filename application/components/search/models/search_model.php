<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
		
  }
  
  ### Поиск по сайту ###
  function search($query_string, $search_types) {
    $types = array(
      'page'    => 'SELECT DISTINCT pr_pages.id as id, "page" as type FROM pr_pages
                    LEFT JOIN pr_params ON pr_params.owner_id = pr_pages.id
                    WHERE pr_pages.is_searchable = 1 AND pr_params.category = "pages" AND pr_params.value LIKE "%'. $query_string .'%"',
      'news'    => 'SELECT DISTINCT pr_publication.id as id, "publication" as type FROM pr_publication
                    LEFT JOIN pr_params ON pr_params.owner_id = pr_publication.id
                    WHERE pr_params.category = "publication" AND pr_params.value LIKE "%'. $query_string .'%"',
    );
    
    $queries = array();
    foreach ($search_types as $num => $type_name) {
      if (isset($types[$type_name])) {
        $queries[] = $types[$type_name];
      }
    }
    
    return $this->db->query(implode(' UNION ', $queries))->result_array();
  }
  ######
  
  function search_prepare($str) {
    return str_replace(' ', '%', htmlspecialchars_decode(trim($str)));
  }
}