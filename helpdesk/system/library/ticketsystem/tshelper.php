<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * TsHelper Ka-ching !
 * This class set Helper for Helpdesk module and do max repetitive tasks
 */
class TsHelper extends TsDefault{

	/**
	 * $modFolder Store mod folder value, used in create urls and other
	 * @var string
	 */
	private $modFolder = 'ticketsystem';

	/**
	 * $baseData Used to store data
	 * @var array
	 */
	private $baseData;

	/**
	 * $defaultSort Sorting url if not set then use this
	 * @var string
	 */
	private $defaultSort;

	/**
	 * $allowedFields Allowed fields which will used to create query
	 * @var array
	 */
	private $allowedFields = array();

	/**
	 * $controllerFile Current controller name
	 * @var string
	 */
	private $controllerFile;

	/**
	 * $tplFile Tpl file name which will use to load
	 * @var string
	 */
	private $tplFile;

	/**
	 * $templateHtml Template data after building everything
	 * @var string html
	 */
	private $templateHtml;

	/**
	 * $addTsColumnLeft Add column left to current controller 
	 * @var boolean
	 * @ignore not used current version
	 */
	private $addTsColumnLeft;

	/**
	 * $sortData This stores all sorting related data
	 * @var array
	 */
	private $sortData = array();

	/**
	 * $sortOrder Sorting Order
	 * @var string
	 */
	private $sortOrder = self::TS_DEFAULT_SORT_ORDER;

	/**
	 * $sortDataSql Sql created based on $sortData
	 * @var string
	 */
	private $sortDataSql;

	/**
	 * $preLikeInfilterSql Add % to query in starting
	 * @var string %
	 */
	private $preLikeInfilterSql;

	/**
	 * $sqlStringEqual If want to compare string than class default used like but this will force class to use equal
	 * @var boolean
	 */
	private $sqlStringEqual;

	/**
	 * $filterSql Filter Sql without Sort Sql, which will use from model class at time of query
	 * @var string
	 */
	private $filterSql;

	/**
	 * $completeSql Complete Sql
	 * @var string
	 */
	private $completeSql;

	/**
	 * $requestData Request data on which class will work, will store here
	 * @var array
	 */
	private $requestData;

	/**
	 * $filterUrl Filter Url - combination of all
	 * @var string
	 */
	private $filterUrl;

	/**
	 * $filterUrlDefault Filter Url Default
	 * @var string
	 */
	private $filterUrlDefault;

	/**
	 * $filterUrlSort Filter Sort Url
	 * @var string
	 */
	private $filterUrlSort;

	/**
	 * $filterUrlPagination Filter Pagination Default
	 * @var string
	 */
	private $filterUrlPagination;

	/**
	 * $filtetLimit Filter Limit
	 * @var integer
	 */
	private $filtetLimit;

	public function __construct($registry) {
		parent::__construct($registry);
		if(!$this->requestData)
			$this->requestData = array_diff_key($this->request->get, array_flip($this->safeFields));
		
		$this->filtetLimit = $this->config->get('config_limit_admin');
	}

	public function setData($data) {
		$this->baseData = $data;
	}

	/**
	 * overrideRequestData Override $this->requestData, if we want so that Helper can work on added data
	 * @param  array  $data  New data
	 * @param  boolean $merge Want to merge with old data or not
	 */
	public function overrideRequestData($data, $merge = false) {
		if($merge){
			$this->request->get = array_merge($this->request->get, $data);
			$this->requestData = array_merge($this->requestData, array_diff_key($data, array_flip($this->safeFields)));
		}
		else{
			$this->request->get = $data;
			$this->requestData = array_diff_key($data, array_flip($this->safeFields));
		}
	}

	/**
	 * __get Return Values 
	 * @param  string $key
	 * @return string or null
	 */
	public function __get($key) {
		if(isset($this->$key))
			return $this->$key;
		return null;
	}

	/**
	 * setDefaultValues Set Default values to Helper class
	 * @param array $data class properties
	 */
	public function setDefaultValues($data) {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * kind of hack, which will use to clear get value in few times when we want to use over data in query
	 * @param  array $data clear these from get
	 * @return none
	 */
	public function clearGetValues($data) {
		foreach ($data as $value) {
			if(isset($this->request->get[$value]))
				unset($this->request->get[$value]);
		}
	}

	/**
	 * getAdminBreadcrumbs Create admin breadcrumbs
	 * @param  array $breadcrumbsData 
	 * @return array Breadcrumbs
	 */
	public function getAdminBreadcrumbs($breadcrumbsData) {
		$breadcrumbs = array();

		$breadcrumbs[] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		foreach ($breadcrumbsData as $language => $href) {
			$breadcrumbs[] = array(
				'text' => $language,
				'href' => $this->url->link($this->modFolder.'/'.$href, 'token=' . $this->session->data['token'] . $this->getUrlData('default'), 'SSL')
			);
		}

		return $breadcrumbs;
	}

	/**
	 * getCatalogBreadcrumbs Create catalog breadcrumbs
	 * @param  array $breadcrumbsData
	 * @return array Breadcrumbs
	 */
	public function getCatalogBreadcrumbs($breadcrumbsData) {
		$breadcrumbs = array();

		$breadcrumbs[] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', 'SSL')
		);

		foreach ($breadcrumbsData as $language => $href) {
			$breadcrumbs[] = array(
				'text' => $language,
				'href' => $this->url->link($this->modFolder.'/'.$href, '' . $this->getUrlData('default'), 'SSL')
			);
		}

		return $breadcrumbs;
	}

	/**
	 * getSortData Get sort order based on values set to class or default 
	 * @param  boolean $force Force it to generate again
	 * @return array sorting array
	 */
	public function getSortData($force = false) {
		if($this->sortData AND !$force)
			return $this->sortData;

		$this->sortData['sort'] = isset($this->request->get['sort']) ? $this->request->get['sort'] : $this->defaultSort;
		$this->sortData['order'] = isset($this->request->get['order']) ? $this->request->get['order'] : self::TS_DEFAULT_SORT_ORDER;
		$this->sortData['page'] = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		return $this->sortData;
	}
	
	/**
	 * getSortDataSql Create Sql based on sortData
	 * @param  boolean $force Force it to generate again
	 * @return string  Sql
	 */
	public function getSortDataSql($force = false) {
		if($this->sortDataSql AND !$force)
			return $this->sortDataSql;
		
		if(!$this->sortData || $force)
			$this->getSortData($force);

		$this->sortDataSql = " ORDER BY ".$this->sortData['sort'];
		$this->sortDataSql .= ' '.$this->sortData['order'];
		$this->sortDataSql .= " LIMIT ".($this->sortData['page'] - 1) * $this->filtetLimit." , ".$this->filtetLimit;

		return $this->sortDataSql;
	}

	/**
	 * getFilterDataSql Create Filter Sql based on set data
	 * @param  boolean $force Force it to generate again
	 * @return string  Sql
	 */
	public function getFilterDataSql($force = false) {
		if($this->filterSql AND !$force)
			return;

		$data = array_intersect_key($this->requestData, array_flip($this->allowedFields));

		if($data){
			$sql = ' AND ';
			
			$this->filterSql = $sql.$this->createQueryUsingFields($data);
		}

		$this->completeSql = $this->filterSql.$this->getSortDataSql($force);
	}

	/**
	 * createQueryUsingFields This is the function who really creates query
	 * @param  array  $data you can say table column name and values
	 * @return string  Sql
	 */
	public function createQueryUsingFields(array $data) {
		$implode = array();
		foreach($data as $column => $value){
			/**
			 * remove filter_ keyword from keys
			 * convert __ to . to build query for join table like t.id is coming from filter_t__id
			 */
			$column = preg_replace('/__/', '.', preg_replace('/filter_/', '', $column));
			$columnChk = explode('.', $column);

			/**
			 * If query has string than it will use like else use equal
			 * we can change this after set true to $this->sqlStringEqual 
			 */
			if((int)$value AND !in_array(end($columnChk), $this->safeIntegerColums)){
				if(is_array($value)){
					$implode[] = $column .' IN (\''.join('\',\'',$value).'\')';
				}
				else
					$implode[] = $column .' = "'.(int)$value.'"';
			}elseif($this->sqlStringEqual){
				$implode[] = $column .' ="'.$this->db->escape($value).'"';
			}else
				$implode[] = $column .' LIKE "'.$this->preLikeInfilterSql.$this->db->escape($value).'%"';
		}

		return implode(' AND ', $implode);
	}

	/**
	 * getFilterData Return Filter data, called from models
	 * @param  boolean $addSortData Want sorting data
	 * @param  boolean $force       Wan to generate data again
	 * @return string Sql
	 */
	public function getFilterData($addSortData = true, $force = false) {
		if($this->completeSql AND $addSortData AND !$force)
			return $this->completeSql;
		elseif($this->filterSql AND !$addSortData AND !$force)
			return $this->filterSql;

		$this->getFilterDataSql($force);

		if($addSortData)
			return $this->completeSql;
		elseif(!$addSortData)
			return $this->filterSql;
	}

	/**
	 * Framework creates 3 urls string, this function will do that for us
	 * @param  array    $data        [filter data]
	 * @param  boolean  $type 		 [this will show what type of url we need]
	 * @return string                [url]
	 */
	public function getUrlData($type = 'default', $force = false) {
		if(!$this->filterUrl AND !$force)
			$this->setUrls();

		switch($type){
			case(self::TS_URL_SORT):
				$returnUrl = $this->filterUrl.$this->filterUrlSort;
				break;
			case(self::TS_URL_PAGINATION):
				$returnUrl = $this->filterUrl.$this->filterUrlPagination;
				break;
			case(self::TS_URL_DEFAULT):
			default:
				$returnUrl = $this->filterUrl.$this->filterUrlDefault;
				break;
		}

		return $returnUrl;
	}

	/**
	 * setUrls Function set url data fro controller
	 * @param  boolean $force Force it to generate again
	 */
	public function setUrls($force = false) {
		if(($this->filterUrl || $this->filterUrlDefault || $this->filterUrlSort || $this->filterUrlPagination) AND !$force)
			return;

		$this->requestData = array_intersect_key($this->requestData, array_merge(array_flip($this->allowedFields), array_flip($this->sortFields)));
		$data = $this->requestData;

		$implode = $sortDefaultImplode = $sortPaginationImplode = $sortSortImplode = array();

		if(isset($data['sort']) AND !isset($data['order']))
			$data['order'] = 'ASC';

		foreach($data as $key => $value){
			if(!in_array($key,$this->sortFields)){ //for default filtering
				if((int)$value AND !in_array(preg_replace('/filter_/', '', $key), $this->safeIntegerColums))
					$implode[] = $key .'='.(int)$value;
				else
					$implode[] = $key .'='.urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}else{ // create sort filtering
				$sortDefaultImplode[] = $key .'='.$value;

				if($key!='page')
					$sortPaginationImplode[] = $key .'='.$value;

				if($key!='sort'){
					if($key=='order'){
						$sortSortImplode[] = $key .'='.($value=='ASC' ? 'DESC' : 'ASC');
					}
					else
						$sortSortImplode[] = $key .'='.(int)$value;
				}
			}
		}

		if($implode)
			$this->filterUrl = '&'.implode('&', $implode);
		if($sortDefaultImplode)
			$this->filterUrlDefault = '&'.implode('&', $sortDefaultImplode);
		if($sortSortImplode)
			$this->filterUrlSort = '&'.implode('&', $sortSortImplode);
		if($sortPaginationImplode)
			$this->filterUrlPagination = '&'.implode('&', $sortPaginationImplode);
	}


	/**
	 * loadHtml Load Admin Tpl and generate response
	 * @param  array  $data 
	 * @param  boolean $force Want to build again
	 * @return string html
	 */
	public function loadHtml($data, $force = false) {		
		if($this->templateHtml AND !$force)
			return $this->templateHtml;

		foreach ($this->allowedFields as $key) {
			if(isset($this->requestData[$key]))
				$data[$key] = $this->requestData[$key];
			else
				$data[$key] = '';
		}

		if(isset($data['addPagination']) AND $data['addPagination'])
			$this->addPagination($data);

		if($this->addTsColumnLeft)
			$data['ts_column_left'] = $this->load->controller('ticketsystem/column_left');
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		if(version_compare(VERSION, '2.2.0.0', '<')) {
			$this->templateHtml = $this->load->view($this->modFolder.'/'.$this->tplFile.'.tpl', $data);
		}else{
			$this->templateHtml = $this->load->view($this->modFolder.'/'.$this->tplFile.'.tpl', $data);
		}

		return $this->templateHtml;
	}

	/**
	 * loadCatalogHtml Load catalog Tpl and generate response
	 * @param  array  $data 
	 * @param  boolean $force Want to build again
	 * @return string html
	 */
	public function loadCatalogHtml($data, $force = false) {
		if($this->templateHtml AND !$force)
			return $this->templateHtml;

		foreach ($this->allowedFields as $key) {
			if(isset($this->requestData[$key]))
				$data[$key] = $this->requestData[$key];
			else
				$data[$key] = '';
		}

		if(isset($data['addPagination']) AND $data['addPagination'])
			$this->addPagination($data);


		if($this->addTsHeader)
			$data['ts_column_top'] = $this->load->controller('ticketsystem/header', $data['categories']);
		else
			$data['ts_column_top'] = '';

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if(version_compare(VERSION, '2.2.0.0', '<')) {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/'.$this->modFolder.'/'.$this->tplFile.'.tpl')) {
				$this->templateHtml = $this->load->view($this->config->get('config_template') . '/template/'.$this->modFolder.'/'.$this->tplFile.'.tpl', $data);
			} else {
				$this->templateHtml = $this->load->view('default/template/'.$this->modFolder.'/'.$this->tplFile.'.tpl', $data);
			}
		}else{
			$this->templateHtml = $this->load->view($this->modFolder.'/'.$this->tplFile, $data);
		}		

		return $this->templateHtml;
	}

	/**
	 * addPagination Add Pagination to current controller if enabled from calling controller
	 * @param array &$data
	 */
	public function addPagination(&$data){
		$total = $data['resultTotal'];
		$page = $data['page'];

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $this->filtetLimit;
		$pagination->url = $this->url->link($this->modFolder.'/'.$this->controllerFile, (isset($this->session->data['token']) ? 'token=' . $this->session->data['token'] : false) . $this->getUrlData('pagination') . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->filtetLimit) + 1 : 0, ((($page - 1) * $this->filtetLimit) > ($total - $this->filtetLimit)) ? $total : ((($page - 1) * $this->filtetLimit) + $this->filtetLimit), $total, ceil($total / $this->filtetLimit));

	}
}