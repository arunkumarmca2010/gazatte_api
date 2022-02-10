<?php
namespace Drupal\gazatte_api\Controller;
class GazatteController {
  public function gazatte() {
  
  drupal_flush_all_caches();
  
  //Code for Pagination
  $page = \Drupal::request()->query->get('page');
  $page = empty($page) ? 1 : $page;
  $pager = $this->pager($page);
  
  //Code for consuming gazette api
  $ch = curl_init('https://www.thegazette.co.uk/all-notices/notice/data.json?&results-page='.$page);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($data, true);
  
  $content = '';
  //code for display data
    if(is_array($data["entry"]))
    {
        //$content = array();
        foreach($data["entry"] as $i=>$item)
        {
            $content .= '<section>
                            <h1><a href="'.$item['id'].'" target="_blank">'.$item['title'].'</a></h1>
                            <p>'.$item['content'].'</p>
                            <h3>Published : '. date('d F Y', strtotime($item['published'])).'</h3>
                     </section><hr>';
        }
    }
        
    return array(
      '#markup' => $content.$pager
    );
  }

  public function pager($page)
  {
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    $pager = '<nav class="pager" role="navigation" aria-labelledby="pagination-heading">
    <h4 id="pagination-heading" class="pager__heading visually-hidden">Pagination</h4>
    <ul class="pager__items js-pager__items">';
        if($page!=1)
        {
          $pager .= '<li class="pager__item pager__item--previous">
            <a href="'.$url.'?page='.($page-1).'" title="Go to previous page" rel="prev">
              <span class="visually-hidden">Previous page</span>
              <span aria-hidden="true">‹‹</span>
            </a>
          </li>';
        }
        $pager.='<li class="pager__item is-active">Page '.$page.'</li>
                    <li class="pager__item pager__item--next">
          <a href="'.$url.'?page='.($page+1).'" title="Go to next page" rel="next">
            <span class="visually-hidden">Next page</span>
            <span aria-hidden="true">››</span>
          </a>
        </li>
          </ul>
      </nav>';
    return $pager;
  }
  

  
}
?>