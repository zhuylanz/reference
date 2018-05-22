<?php
class ControllerModuleXform extends Controller {
	private $error = array(); 
	
	public function index() {   
	    
		@ini_set( "max_input_vars", 10000);
		
		$this->load->language('module/xform');
		$this->load->model('xform/xform');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$data['language_id']=$this->config->get('config_language_id');
		$language_id=$data['language_id'];
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
		
		     $formId = $this->request->post['formId'];
			 $this->request->post['sendAdminEmail']=isset($this->request->post['sendAdminEmail'])?1:0;
			 $this->request->post['sendUserEmail']=isset($this->request->post['sendUserEmail'])?1:0;
			 $this->request->post['formCreationDate'] = date('Y-m-d H:i:s');
			 $this->request->post['hideTitle']=isset($this->request->post['hideTitle'])?1:0;
			 $this->request->post['formCreationDate'] = date('Y-m-d H:i:s');
			 $this->request->post['formModule']=isset($this->request->post['formModule'])?1:0;
			 $this->request->post['customerOnly']=isset($this->request->post['customerOnly'])?1:0;
			 $this->request->post['jsvalid']=isset($this->request->post['jsvalid'])?1:0;
			 $this->request->post['sendEmailAttachment']=isset($this->request->post['sendEmailAttachment'])?1:0;
			 
			 $this->request->post['storeId']=isset($this->request->post['storeId']) && is_array($this->request->post['storeId'])? serialize($this->request->post['storeId']) : serialize(array());
			 
			 $formName = isset($this->request->post['formDesc'][$language_id]['formName'])?$this->request->post['formDesc'][$language_id]['formName']:'untitled form';
			 if(!$this->request->post['keyword']) {
			   $this->request->post['keyword'] = isset($this->request->post['formDesc'][$language_id]['formName'])?$this->request->post['formDesc'][$language_id]['formName']:'';
			 }
			 
			 $this->request->post['keyword'] = str_replace(array('#',' ',"'",'"','!','@','#','$','%','^','&','*','(',')','~','`'),'_',$this->request->post['keyword']);
			 
			 $formId = $this->model_xform_xform->addForm($this->request->post, $formId);
			 
			 if (get_magic_quotes_gpc()) {
				  $this->request->post['formdata']=stripslashes($this->request->post['formdata']);  
			 }
			
			 $formdata = $this->request->post['formdata'];
			 
			 $decode_data = json_decode(htmlspecialchars_decode($formdata),true);
			 $formdatas = $decode_data['fields'];
             $this->model_xform_xform->addFormFields($formdatas, $formId);
             
             /*Save Lang files*/
             if(isset($this->request->post['labels']) && is_array($this->request->post['labels'])) {
                 
                 foreach($this->request->post['labels'] as $languageId=>$lang_data) {
                   $lang_options = (isset($this->request->post['options'][$languageId]) && is_array($this->request->post['options'][$languageId]))? $this->request->post['options'][$languageId] : array();
                   $lang_guidelines = (isset($this->request->post['guidelines'][$languageId]) && is_array($this->request->post['guidelines'][$languageId]))? $this->request->post['guidelines'][$languageId] : array();
                   $lang_errors = (isset($this->request->post['errors'][$languageId]) && is_array($this->request->post['errors'][$languageId]))? $this->request->post['errors'][$languageId] : array();
                   $lang_options = base64_encode(serialize($lang_options));
                   $lang_guidelines = base64_encode(serialize($lang_guidelines));
                   $lang_data = base64_encode(serialize($lang_data));
                   $lang_errors = base64_encode(serialize($lang_errors));
                   $this->model_xform_xform->setFormLang($formId, $languageId, $lang_data, $lang_options, $lang_guidelines, $lang_errors);
                 }
             }
             
             
             $this->load->model('extension/module');
             
             $module_id = $this->getModuleByFormId($formId);
            
             
             if($this->request->post['formModule']==1) {
               
               if(!$module_id) {
               		$module_data = array();
               		$module_data['name'] = $formName;
               		$module_data['formId'] = $formId;
               		$module_data['status'] = 1;
               		$this->model_extension_module->addModule('xform', $module_data);
                } else {
                    $module_data = array();
               		$module_data['name'] = $formName;
               		$module_data['formId'] = $formId;
               		$module_data['status'] = 1;
               		$this->model_extension_module->editModule($module_id, $module_data);
                }
               
             } else {
                
                $this->model_extension_module->deleteModule($module_id);
             }
             
             $this->session->data['success'] = $this->language->get('text_success');
             
             if($this->request->post['save']=='continue') {	
			   $this->response->redirect($this->url->link('module/xform/edit', 'token=' . $this->session->data['token'].'&formId='.$formId, 'SSL'));
			 } else {
			   $this->response->redirect($this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL'));
			 }
		}
			
				
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_list'] = $this->language->get('text_list');
		$data['button_record'] = $this->language->get('button_record');
		$data['button_duplicate'] = $this->language->get('button_duplicate');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
        $data['text_form_name'] = $this->language->get('text_form_name');
		$data['text_status'] = $this->language->get('text_status');
		$data['text_view_record'] = $this->language->get('text_view_record');
		$data['text_action'] = $this->language->get('text_action');
		$data['text_create_on'] = $this->language->get('text_create_on');
		$data['text_duplicate'] = $this->language->get('text_duplicate');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_view_form'] = $this->language->get('text_view_form');
		
		
		
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
			
 	   if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'formCreationDate';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['sort_date'] = $this->url->link('module/xform', 'token=' . $this->session->data['token'] . '&sort=formCreationDate' . $url, 'SSL');

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
		$data['action'] = $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$data['add'] = $this->url->link('module/xform/edit', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('module/xform/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		$data['token']=$this->session->data['token'];
		
		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
	      
	   
	   if($this->model_xform_xform->isDBBUPdateAvail()) {
	     $data['error_warning'] = sprintf($this->language->get('text_db_upgrade'), $this->url->link('module/xform/upgrade', 'token=' . $this->session->data['token'], 'SSL'));
	   }  
	                   
		$results=$this->model_xform_xform->getForms($filter_data);
		$data['forms'] = array();
		
		foreach ($results as $result) {
		    
		    $formInfo  = $this->model_xform_xform->getForm($result['formId']);
		    $date_format = $this->model_xform_xform->getDateFormat($result['formId'], true);
		    
			$data['forms'][] = array(
				'formId' => $result['formId'],
				'formName'          => $formInfo['formName'],
				'status'     => ($result['status']==1)? 'Active':'Inactive',
				'formCreationDate'     => date($date_format.' H:i:s', strtotime($result['formCreationDate'])),
				'record'           => $this->url->link('module/xform/records', 'token=' . $this->session->data['token'] . '&formId=' . $result['formId'] . $url, 'SSL'),
				'duplicate'           => $this->url->link('module/xform/duplicate', 'token=' . $this->session->data['token'] . '&formId=' . $result['formId'] . $url, 'SSL'),
				'form_url'           => HTTP_CATALOG.'index.php?route=xform/xform&formId='.$result['formId'],
				'edit'           => $this->url->link('module/xform/edit', 'token=' . $this->session->data['token'] . '&formId=' . $result['formId'] . $url, 'SSL')
			);
		}
	
		$forms_total = $this->model_xform_xform->getTotalForms();
		
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $forms_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('module/xform', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($forms_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($forms_total - $this->config->get('config_limit_admin'))) ? $forms_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $forms_total, ceil($forms_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		  
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		 
		$this->response->setOutput($this->load->view('module/xform_listing.tpl', $data));
	}
      
   private function validate() {
		if (!$this->user->hasPermission('modify', 'module/xform')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	public function copyMehthod()
	{
	  $tabId=$this->request->get['tabId'];
	}
	
	public function generate()
	{
	   $formId=$this->request->get['formId'];
	   $this->load->model('xform/xform');
	   $layout = $this->model_xform_xform->renderForm($formId,array(),true,false,true);
	   $this->response->setOutput($layout);
	}
	
	public function edit()
	{
	   
	   $this->load->language('module/xform');
	   $this->load->model('xform/xform');
	   
	   $this->document->setTitle($this->language->get('heading_title'));
	   
	   if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
	
	   if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL')
   		);
   		
   		$formId = (isset($this->request->get['formId']) && $this->request->get['formId'])? $this->request->get['formId']: 0;
		
		$data['action'] = $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL');
		$data['form_url'] = '';
		
		if($formId) {
		  $data['form_url'] = HTTP_CATALOG.'index.php?route=xform/xform&formId='.$formId;
		}
		
		
		$data['heading_title'] = $this->language->get('heading_title');

        $data['text_form_url']    = $this->language->get('text_form_url');
		$data['text_form_seo_url']    = $this->language->get('text_form_seo_url');
		$data['text_seo_keyword']    = $this->language->get('text_seo_keyword');
		$data['text_enable_mod']    = $this->language->get('text_enable_mod');
		$data['text_enable_mod_tip']    = sprintf($this->language->get('text_enable_mod_tip'),$this->url->link('design/layout', 'token=' . $this->session->data['token'], 'SSL'));
		$data['text_success_page']    = $this->language->get('text_success_page');
		$data['text_show_success_msg']    = $this->language->get('text_show_success_msg');
		$data['text_success_url']    = $this->language->get('text_success_url');
		$data['text_redirect_url']    = $this->language->get('text_redirect_url');
		$data['text_success_msg']    = $this->language->get('text_success_msg');
		$data['text_send_admin_email']    = $this->language->get('text_send_admin_email');
		$data['text_enter_admin_email']    = $this->language->get('text_enter_admin_email');
		$data['text_admin_email_sub']    = $this->language->get('text_admin_email_sub');
		$data['text_admin_email_content']    = $this->language->get('text_admin_email_content');
		$data['text_send_user_email']    = $this->language->get('text_send_user_email');
		$data['text_enter_user_email']    = $this->language->get('text_enter_user_email');
		$data['text_user_email_sub']    = $this->language->get('text_user_email_sub');
		$data['text_user_email_content']    = $this->language->get('text_user_email_content');
		$data['text_user_email_tip']    = $this->language->get('text_user_email_tip');
		$data['text_email_keywords']    = $this->language->get('text_email_keywords');
		$data['text_keyword_ip']    = $this->language->get('text_keyword_ip');
		$data['text_keyword_date']    = $this->language->get('text_keyword_date');
		$data['text_keyword_date_time']    = $this->language->get('text_keyword_date_time');
		$data['text_keyword_url']    = $this->language->get('text_keyword_url');
		$data['text_form_url']    = $this->language->get('text_form_url');
		$data['text_edit']    = $this->language->get('text_edit');
		$data['text_form_info']    = $this->language->get('text_form_info');
		$data['text_form_option']    = $this->language->get('text_form_option');
		$data['text_form_integration']    = $this->language->get('text_form_integration');
		$data['text_form_desc']    = $this->language->get('text_form_desc');
		$data['text_status_active']    = $this->language->get('text_status_active');
		$data['text_status_inactive']    = $this->language->get('text_status_inactive');
		$data['text_status']    = $this->language->get('text_status');
		$data['text_form_name']    = $this->language->get('text_form_name');
		$data['text_hide_form_name']    = $this->language->get('text_hide_form_name');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['text_theme'] = $this->language->get('text_theme');
	    $data['btn_save_continue'] = $this->language->get('btn_save_continue');     
		$data['tip_keyword'] = $this->language->get('tip_keyword'); 
		$data['text_translation'] = $this->language->get('text_translation'); 
		$data['text_translation_tip'] = $this->language->get('text_translation_tip');
		
		$data['text_keyword_name'] = $this->language->get('text_keyword_name');
		$data['text_keyword_field'] = $this->language->get('text_keyword_field');
		$data['text_keyword_label'] = $this->language->get('text_keyword_label');
		$data['text_keyword_value'] = $this->language->get('text_keyword_value');
		$data['text_keyword_info'] = $this->language->get('text_keyword_info');
		$data['text_keyword_error'] = $this->language->get('text_keyword_error');
		$data['text_custom'] = $this->language->get('text_custom');
		$data['text_custom_tip'] = $this->language->get('text_custom_tip');
		$data['text_custom_html'] = $this->language->get('text_custom_html');
		$data['text_store_name'] = $this->language->get('text_store_name');
		
		$data['text_custom_script'] = $this->language->get('text_custom_script');
		$data['text_custom_style'] = $this->language->get('text_custom_style');
		$data['text_custom_script_tip'] = htmlentities($this->language->get('text_custom_script_tip'));
		$data['text_custom_style_tip'] = htmlentities($this->language->get('text_custom_style_tip'));
		$data['tab_other'] = $this->language->get('tab_other');
		$data['text_product_id'] = $this->language->get('text_product_id');
		$data['text_product_name'] = $this->language->get('text_product_name');
		$data['text_product_tip'] = $this->language->get('text_product_tip');
		$data['text_product_model'] = $this->language->get('text_product_model');
		$data['text_product_image'] = $this->language->get('text_product_image');
		$data['text_product_url'] = $this->language->get('text_product_url');
		
		$data['text_email_attached'] = $this->language->get('text_email_attached');
		$data['text_email_attached_type'] = $this->language->get('text_email_attached_type');
		$data['text_email_attached_csv'] = $this->language->get('text_email_attached_csv');
		$data['text_email_attached_pdf'] = $this->language->get('text_email_attached_pdf');
		$data['text_email_send_type'] = $this->language->get('text_email_send_type');
		$data['text_email_send_type_user'] = $this->language->get('text_email_send_type_user');
		$data['text_email_send_type_admin'] = $this->language->get('text_email_send_type_admin');
		$data['text_email_send_type_both'] = $this->language->get('text_email_send_type_both');
		$data['text_record_id'] = $this->language->get('text_record_id');
		$data['text_lang_label'] = $this->language->get('text_lang_label');
		$data['text_lang_other'] = $this->language->get('text_lang_other');
		$data['text_lang_guideline'] = $this->language->get('text_lang_guideline');
		$data['text_lang_error'] = $this->language->get('text_lang_error');
		$data['text_available_logged_only'] = $this->language->get('text_available_logged_only');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_date_format'] = $this->language->get('text_date_format');
		$data['text_generate_layout'] = $this->language->get('text_generate_layout');
		$data['text_email_file_name'] = $this->language->get('text_email_file_name');
		$data['text_email_name_tip'] = $this->language->get('text_email_name_tip');
		$data['text_client_validation'] = $this->language->get('text_client_validation');
		$data['clear_translation'] = $this->language->get('clear_translation');
		$data['clear_translation_tip'] = $this->language->get('clear_translation_tip');
		$data['text_record_data'] = $this->language->get('text_record_data');
		$data['text_record_data_tip'] = $this->language->get('text_record_data_tip');
		$data['text_record_non_empty_data'] = $this->language->get('text_record_non_empty_data');
		$data['text_record_non_empty_data_tip'] = $this->language->get('text_record_non_empty_data_tip');
		
		$email_fields='';
		$email_kewords=array();
		$formfields=array();
		$lang_labels = array();
		$lang_options = array();
		$lang_guidelines = array();
		$lang_errors = array();
		$data['formId'] = '';
		$data['formDesc'] = array();
		$data['formdata'] = array(
		   					 'status' => '1',
		   					 'formModule' => '',
		   					 'customerOnly' => '',
		   					 'jsvalid' => '',
		   					 'successType' => '',
		   					 'successURL' => '',
		   					 'dateFormat' => 'dd/mm/yyyy',
		   					 'sendAdminEmail' => '',
		   					 'adminEmail' => '',
		   					 'sendUserEmail' => '',
		   					 'sendEmailAttachment' => '',
		   					 'emailAttachmentType' => '',
		   					 'emailAttachmentUser' => '',
		   					 'emailAttachmentName' => '',
		   					 'keyword' => '',
		   					 'custom' => '',
		   					 'style' => '',
		   					 'script' => '',
		   					 'hideTitle' => '',
		   					 'storeId' => array(0),
		   					 'theme' => 'classic'
		 				);

		 if(!empty($formId)) 
			{
				$edit_data=$this->model_xform_xform->getForm($formId);
				$formDesc=$this->model_xform_xform->getFormDescriptions($formId);
				$data['formId']=$formId;
				$data['formdata'] = $edit_data;
				$data['formDesc'] = $formDesc;
				
				$email_fields=$this->model_xform_xform->getFormEmails($edit_data['formId'],true,$edit_data['userEmail']);
				$email_kewords=$this->model_xform_xform->getFormKeywords($edit_data['formId']);
				$formfields=$this->model_xform_xform->getFormFields($edit_data['formId']);
			}
	   
	   	 $this->load->model('localisation/language');
		 $data['languages'] = $this->model_localisation_language->getLanguages();
		 $data['language_id']=$this->config->get('config_language_id');
		 
		 $this->load->model('setting/store');
         $data['stores'] = $this->model_setting_store->getStores();
		 
		 /* fetching lang data */
		 foreach($data['languages'] as $i=>$language) {
		   $form_lang_data = $this->model_xform_xform->getFormLang($formId,$language['language_id']);
		   $lang_labels[$language['language_id']] = $form_lang_data['labels'];
		   $lang_options[$language['language_id']] = $form_lang_data['options'];
		   $lang_guidelines[$language['language_id']] = $form_lang_data['guidelines'];
		   $lang_errors[$language['language_id']] = $form_lang_data['errors'];
		 }
		 
		 $data['text_shortcode'] = ($formId)? sprintf($this->language->get('text_shortcode'),$formId):'';
		 $data['text_shortcode_data'] = ($formId)? sprintf($this->language->get('text_shortcode_data'),$formId):'';
			
		 $data['email_fields'] = $email_fields;  
		 $data['email_kewords'] = $email_kewords; 
		 $data['formfields'] = $formfields;  
		 $data['lang_labels'] = $lang_labels;  
		 $data['lang_options'] = $lang_options;  
		 $data['lang_guidelines'] = $lang_guidelines; 
		 $data['lang_errors'] = $lang_errors;  
		 $data['token']=$this->session->data['token'];
		 
		 $data['themes']= array('classic','square','box','boxplus','box-green','box-red','box-blue','custom');
	
		 $data['header'] = $this->load->controller('common/header');
		 $data['column_left'] = $this->load->controller('common/column_left');
		 $data['footer'] = $this->load->controller('common/footer');
		
		 $this->response->setOutput($this->load->view('module/xform_form.tpl', $data));
	  	
	}
	
	public function records()
	{
	
        if (isset($this->request->get['filter_store'])) {
			$data['filter_store'] = $this->request->get['filter_store'];
		} else {
			$data['filter_store'] = null;
		}

		if (isset($this->request->get['filter_start_date'])) {
			$data['filter_start_date'] = $this->request->get['filter_start_date'];
		} else {
			$data['filter_start_date'] = null;
		}

		if (isset($this->request->get['filter_end_date'])) {
			$data['filter_end_date'] = $this->request->get['filter_end_date'];
		} else {
			$data['filter_end_date'] = null;
		}
		
		if (isset($this->request->get['filter_keyword'])) {
			$data['filter_keyword'] = $this->request->get['filter_keyword'];
		} else {
			$data['filter_keyword'] = null;
		}
	
			   
	    $this->load->model('xform/xform');
	    $this->load->language('module/xform');
	    
	    $language_id=$this->config->get('config_language_id');
	    
	
	    $this->document->setTitle($this->language->get('heading_title'));
	    
	    if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
		
		     $formId = $this->request->get['formId'];
			 if(!isset($this->request->post['formHeading'])) $this->request->post['formHeading'] = array();
			 $this->model_xform_xform->setFormHeading($formId, $this->request->post['formHeading']);
             $this->session->data['success'] = $this->language->get('text_success');	
			 $this->response->redirect($this->url->link('module/xform/records', 'token=' . $this->session->data['token'].'&formId='.$formId, 'SSL'));
		}
	    
	    $data['heading_title'] = $this->language->get('heading_title');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['button_saving'] = $this->language->get('button_saving');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_csv'] = $this->language->get('button_csv');
		$data['button_pdf'] = $this->language->get('button_pdf');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_start_date'] = $this->language->get('entry_start_date');
		$data['entry_end_date'] = $this->language->get('entry_end_date');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['text_action'] = $this->language->get('text_action');
		$data['button_setting'] = $this->language->get('button_setting');
		$data['text_save_heading'] = $this->language->get('text_save_heading');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_view'] = $this->language->get('button_view');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_export_all'] = $this->language->get('text_export_all');
		$data['text_export_current'] = $this->language->get('text_export_current');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
	
	    if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
			
 	   if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'submitDate';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

        if (isset($this->request->get['formId'])) {
			$url .= '&formId=' . $this->request->get['formId'];
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}
		
		if (isset($this->request->get['filter_keyword'])) {
			$url .= '&filter_keyword=' . $this->request->get['filter_keyword'];
		}
		
	
		$data['sort_date'] = $this->url->link('module/xform', 'token=' . $this->session->data['token'] . '&sort=submitDate' . $url, 'SSL');

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
		$formId = $this->request->get['formId'];
		
		$data['action'] = $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('module/xform', 'token=' . $this->session->data['token']. $url, 'SSL');
		$data['delete'] = $this->url->link('module/xform/record_delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['save_heading'] = $this->url->link('module/xform/records', 'token=' . $this->session->data['token']. $url, 'SSL');
		$data['export_csv'] = $this->url->link('module/xform/export', 'token=' . $this->session->data['token'].'&format=csv' .$url, 'SSL');
		$data['export_pdf'] = $this->url->link('module/xform/export', 'token=' . $this->session->data['token'].'&format=pdf' .$url, 'SSL');
		
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
        $data['stores']=  array_merge(array(array('store_id'=>0,'name'=>$this->language->get('store_default'))),$data['stores']);
		
		$data['token']=$this->session->data['token'];
		
		$filter_data = array(
		    'filter_store' => $data['filter_store'],
		    'filter_start_date' => $data['filter_start_date'],
		    'filter_end_date' => $data['filter_end_date'],
		    'filter_keyword' => $data['filter_keyword'],
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		 
		
			 
	    if(isset($this->request->post['save_setting']) && isset($this->request->post['formHeading'])){
		   $this->model_xform_xform->setFormHeading($formId,$this->request->post['formHeading']);
		}
			 
		 $formHeading=$this->model_xform_xform->getFormHeading($formId);
			 
		 $fields      = $this->model_xform_xform->getFormFields($formId,false,true);	
		 $common_fields      = $this->model_xform_xform->getCommonHeadings($formId);	
		 $fields=array_merge($fields,$common_fields);
			 
	     
			 	
		 $formInfo=$this->model_xform_xform->getForm($formId);
		 $rows      = $this->model_xform_xform->getRecords($formId, $filter_data);
		 
		
		 foreach ($rows as $index=>$row) {
		 
			$rows[$index]['view'] = $this->url->link('module/xform/viewRecord', 'token=' . $this->session->data['token'] . '&formId=' . $row['formId'].'&recordId=' .$row['recordId']. $url, 'SSL');
			$rows[$index]['edit'] = $this->url->link('module/xform/editRecord', 'token=' . $this->session->data['token'] . '&formId=' . $row['formId'].'&recordId=' .$row['recordId']. $url, 'SSL');
		 }
			 
		 $data['rows'] = $rows;
		 $data['formHeading'] = $formHeading;
		 $data['fields'] = $fields;
		 $data['formInfo'] = $formInfo;
		 $data['formId'] = $formId;
		 
		$data['record_list'] = sprintf($this->language->get('record_list'),$formInfo['formName']); 
		 
		$row_total = $this->model_xform_xform->getTotalRecords($formId, $filter_data);
		
		$url = '';

		if (isset($this->request->get['formId'])) {
			$url .= '&formId=' . $this->request->get['formId'];
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}
		if (isset($this->request->get['filter_keyword'])) {
			$url .= '&filter_keyword=' . $this->request->get['filter_keyword'];
		}
		
		
		$pagination = new Pagination();
		$pagination->total = $row_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('module/xform/records', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($row_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($row_total - $this->config->get('config_limit_admin'))) ? $row_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $row_total, ceil($row_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		 
		 
	
		 $data['header'] = $this->load->controller('common/header');
		 $data['column_left'] = $this->load->controller('common/column_left');
		 $data['footer'] = $this->load->controller('common/footer');
		
		 $this->response->setOutput($this->load->view('module/xform_records.tpl', $data));
	  	
	}
	
	public function viewRecord()
	{
	   
	   $this->load->model('xform/xform');
	   $this->load->language('module/xform');
	   
	   $this->document->setTitle($this->language->get('heading_title'));
	   $language_id=$this->config->get('config_language_id');
	   
	   $data['heading_title'] = $this->language->get('heading_title');
	   $data['button_cancel'] = $this->language->get('button_cancel');
	   $data['text_view'] = $this->language->get('text_view');

	   $data['text_from_name'] = $this->language->get('text_from_name');
	   $data['text_from_email'] = $this->language->get('text_from_email');
	   $data['text_to_email'] = $this->language->get('text_to_email');
	   $data['entry_subject'] = $this->language->get('entry_subject');
	   $data['entry_message'] = $this->language->get('entry_message');
	   $data['text_send_email'] = $this->language->get('text_send_email');
	   $data['text_cancel'] = $this->language->get('text_cancel');
	   $data['text_send'] = $this->language->get('text_send');

	   $data['from_name'] = $this->config->get('config_name');
	   $data['from_email'] = $this->config->get('config_email');
	   $data['to_email'] = '';
	   $data['subject'] = '';
	   $data['message'] = '';

	   $isError = false;

	   if ($this->request->post && isset($this->request->post['send_email'])) {
	   	   $to_email = isset($this->request->post['to_email']) && $this->request->post['to_email'] ?
	   	   			$this->request->post['to_email'] : '';

	   	   $from_name = isset($this->request->post['from_name']) && $this->request->post['from_name'] ?
	   	   			$this->request->post['from_name'] : $this->config->get('config_name');
	   	   $from_email = isset($this->request->post['from_email']) && $this->request->post['from_email'] ?
	   	   			$this->request->post['from_email'] : $this->config->get('config_email');

	   	   $subject = isset($this->request->post['subject']) && $this->request->post['subject'] ?
	   	   			$this->request->post['subject'] : '';

	   	   $message = isset($this->request->post['message']) && $this->request->post['message'] ?
	   	   			$this->request->post['message'] : '';	

	   	   if (!$to_email)	{
	   	   	 $this->error['warning'] = $this->language->get('error_empty_email');
	   	   	 $isError = true;
	   	   } else if(!$subject)	{
	   	   	 $this->error['warning'] = $this->language->get('error_empty_subject');
	   	   	 $isError = true;
	   	   } else if(!$message)	{
	   	   	 $this->error['warning'] = $this->language->get('error_empty_message');
	   	   	 $isError = true;
	   	   } else {

	   	   	   if(intval(str_replace('.','',VERSION)) > 2011) {   
		        
		        	$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
				
				} else {
				   $mail = new Mail($from_email);
				}
								
				$mail->setFrom($from_email);
				$mail->setSender($from_name);
				$mail->setSubject(html_entity_decode($subject));
				$mail->setHtml(html_entity_decode($message));
				$mail->setText(strip_tags($message));
				$mail->setTo(trim($to_email));
				$mail->send();
				$this->session->data['success'] = $this->language->get('success_message');
	   	   }

	   	   if ($isError) {
	   	   	  $data['from_name'] = $from_name;
	   	   	  $data['from_email'] = $from_email;
	   	   	  $data['to_email'] = $to_email;
	   	   	  $data['subject'] = $subject;
	   	   	  $data['message'] = $message;
	   	   }												

	   }

	   $data['isError'] = $isError;
	
	   if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
			
 	   if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
		$url = '';
		
		if (isset($this->request->get['formId'])) {
			$url .= '&formId=' . $this->request->get['formId'];
		}
		
		if (isset($this->request->get['recordId'])) {
			$url .= '&recordId=' . $this->request->get['recordId'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}
	
		 $data['cancel'] = $this->url->link('module/xform/records', 'token=' . $this->session->data['token']. $url, 'SSL');
		 $data['action'] = $this->url->link('module/xform/viewRecord', 'token=' . $this->session->data['token']. $url, 'SSL');
		 
		 $formId = $this->request->get['formId'];
		 $recordId = $this->request->get['recordId'];
			 
	     $record     = $this->model_xform_xform->getRecordById($recordId);
		 $data['record'] = $record;
		 $data['formId'] = $formId;
		 
		 
	
		 $data['header'] = $this->load->controller('common/header');
		 $data['column_left'] = $this->load->controller('common/column_left');
		 $data['footer'] = $this->load->controller('common/footer');
		
		 $this->response->setOutput($this->load->view('module/xform_view.tpl', $data));
	  	
	}
	
	
	public function editRecord()
	{
	   
	   $this->load->model('xform/xform');
	   $this->load->language('module/xform');
	   $language_id=$this->config->get('config_language_id');
	   
	    $url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}
		
		if (isset($this->request->get['formId'])) {
			$url .= '&formId=' . $this->request->get['formId'];
		}
		
		if (isset($this->request->get['recordId'])) {
			$url .= '&recordId=' . $this->request->get['recordId'];
		}
	   
	   if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
		
		     $formId = $this->request->get['formId'];
		     $recordId = $this->request->get['recordId'];
	
			 $this->model_xform_xform->processFormData($recordId, $this->request->post['data'],true);
             $this->session->data['success'] = $this->language->get('text_success');	
			 $this->response->redirect($this->url->link('module/xform/records', 'token=' . $this->session->data['token']. $url, 'SSL'));
		}
	   $this->document->setTitle($this->language->get('heading_title'));
	   
	   $data['heading_title'] = $this->language->get('heading_title');
	   $data['text_edit_list'] = $this->language->get('text_edit_list');
	   $data['button_cancel'] = $this->language->get('button_cancel');
	
	   if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
			
 	   if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
	
		$data['cancel'] = $this->url->link('module/xform/records', 'token=' . $this->session->data['token']. $url, 'SSL');
		$data['action'] = $this->url->link('module/xform/editRecord', 'token=' . $this->session->data['token']. $url, 'SSL');
		
		 
		 $formId = $this->request->get['formId'];
		 $recordId = $this->request->get['recordId'];
			 
	     $record_data = $this->model_xform_xform->getRecordData($formId, $recordId);
		 $data['record_form'] = $this->model_xform_xform->renderForm($formId,$record_data,true);
		 
		 
	
		 $data['header'] = $this->load->controller('common/header');
		 $data['column_left'] = $this->load->controller('common/column_left');
		 $data['footer'] = $this->load->controller('common/footer');
		
		 $this->response->setOutput($this->load->view('module/xform_edit.tpl', $data));
	  	
	}
	
	public function duplicate() {
         
            $this->load->model('xform/xform');
            $this->load->language('module/xform');
            
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();
            
            if (!$this->validate()) {
               $this->session->data['warning'] = $this->error['warning'];
               $this->response->redirect($this->url->link('module/xform', 'token=' . $this->session->data['token'], true));
            }
            
            $formId = $this->request->get['formId'];	
			$formInfo= $this->model_xform_xform->getForm($formId);
			$formDesc  = $this->model_xform_xform->getFormDescriptions($formId);
			$fields = $this->model_xform_xform->getFormFields($formId); 
		 
		    /* fetching lang data */
		    $xform_langs = array();
		    foreach($languages as $i=>$language) {
		      $xform_langs[$language['language_id']] = $this->model_xform_xform->getFormLangData($formId,$language['language_id']);
		    }
			
			
			if($formInfo){
			     
			     if($formDesc && is_array($formDesc)) {
			       foreach($formDesc as $language_id=>$value) {
			          $formDesc[$language_id]['formName'] =  $value['formName'].' Copy'; 
			        }
			      }
			      
			     $formInfo['storeId']=isset($formInfo['storeId']) && is_array($formInfo['storeId'])? serialize($formInfo['storeId']) : serialize(array()); 
			    
			     $formInfo['formDesc']= $formDesc;
			     $formInfo['keyword']=''; 		
			     	 
				 $formId = $this->model_xform_xform->addForm($formInfo);
				 $this->model_xform_xform->addFormFields($fields,$formId);
				 
				 if(isset($xform_langs) && is_array($xform_langs)) {
                    foreach($xform_langs as $languageId=>$lang_data) {
                       if($lang_data) {
                         $this->model_xform_xform->setFormLang($formId, $languageId, $lang_data['data'], $lang_data['options'], $lang_data['guidelines'], $lang_data['errors']);
                       }
                    }
                }
				 
		    }
			
			 $this->session->data['success'] = $this->language->get('text_success');	
			 $this->response->redirect($this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL'));
         
      } 
      
    public function export() { 
         
           $this->load->model('xform/xform');
           $this->load->language('module/xform');
           $language_id=$this->config->get('config_language_id');
           
            if (isset($this->request->get['filter_store'])) {
				$data['filter_store'] = $this->request->get['filter_store'];
			} else {
				$data['filter_store'] = null;
			}

			if (isset($this->request->get['filter_start_date'])) {
				$data['filter_start_date'] = $this->request->get['filter_start_date'];
			} else {
				$data['filter_start_date'] = null;
			}

			if (isset($this->request->get['filter_end_date'])) {
				$data['filter_end_date'] = $this->request->get['filter_end_date'];
			} else {
				$data['filter_end_date'] = null;
			}
			
			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
			} else {
				$sort = 'submitDate';
			}

			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
			} else {
				$order = 'DESC';
			}

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}
			
			
			$filter_data = array(
		    'filter_store' => $data['filter_store'],
		    'filter_start_date' => $data['filter_start_date'],
		    'filter_end_date' => $data['filter_end_date'],
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		   );
           	
           	if(isset($this->request->get['all']) && $this->request->get['all']==1) {
           	  $filter_data= array();
           	}			
            
            $formId = $this->request->get['formId'];	
            $format = $this->request->get['format'];
            
            $formInfo= $this->model_xform_xform->getForm($formId);
            
            $formHeading= $this->model_xform_xform->getFormHeading($formId);
			$records      = $this->model_xform_xform->getRecords($formId,$filter_data);
			
			foreach($records as $i=>$single) {
					 $resultant=array_intersect_key($single,$formHeading); 
					 $resultant=$this->sortArrayByArray($resultant,$formHeading,'key');
					 $records[$i]=$resultant;
		    }
			
			$filename = str_replace(array('#',' ',"'",'"','!','@','#','$','%','^','&','*','(',')','~','`'),'_',$formInfo['formName']);
			
			if($format == 'csv') {
			
				$this->arrayToCsv($records,$formHeading,$filename.'.csv');
		    }
		    
		    if($format == 'pdf') {
			    
                require(DIR_SYSTEM.'library/tcpdf/tcpdf.php');
                
				$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
				
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                $pdf->SetFont('helvetica', '', 12);
                $pdf->AddPage();
                
                $html = '<style>
    				h3 {
        				color: #000000;
        				font-family: helvetica;
        				font-size: 22pt;
   						}
    				table {
        				font-family: helvetica;
        				font-size: 8pt;
        				border: 1px solid #EAEAEA;
        				border-collapse: collapse;
   					 }
    				td {
        				border: 1px solid #EAEAEA;
        				background-color: #ffffff;
   					 }
   					 td.label {
        				background-color: #F5F5F5;
        				color: #333333;
   					 } 
				</style>';
				
                $html .= '<h3>'.$formInfo['formName'].'</h3> <br />';
                
                
	            foreach($records as $row) {
				   $html .= '<table  cellpadding="6" cellspacing="0">';	
				    foreach($formHeading as $cid=>$label){
				       $html .= '<tr>'; 
				       $html .= '<td class="label" width="30%" align="left"><b>'.$label.'</b></td>';
				       $html .= '<td width="70%" align="left">'.(isset($row[$cid])?$row[$cid]:'').'</td>';
				       $html .= '</tr>';  
					 }
				   $html .= '</table>'; 
				   
				   $html .='<br><br>';	
				}
				
				
				$html = str_replace('src="'.HTTP_CATALOG, 'src="../', $html);
				$pdf->writeHTML($html, true, false, true, false, '');
				
			    // Closing line
			    $pdf->Output($filename.'.pdf','D');
		   }		

         
      }   
	
	
	public function quick_save(){
         
          $this->load->model('xform/xform');
          $this->load->language('module/xform');
         
           $json=array();
           
          if($this->request->post['data']){
          
               if (get_magic_quotes_gpc()) {
				  $this->request->post['data']=stripslashes($this->request->post['data']);  
			    }
          
			   $post_data=json_decode(htmlspecialchars_decode($this->request->post['data']),true);
			   $fields=$post_data['fields'];	
			   $form=$post_data['form'];
			   
			   if(!$form['formId']) { 
				   
				   $data=array();
				   $data['formCreationDate'] = date('Y-m-d H:i:s');
				   $data['hideTitle']=(isset($form['hideTitle']) && $form['hideTitle'])?$form['hideTitle']:0;
			       $formId = $this->model_xform_xform->addForm($data,'',true);
			        
				} else {
				   
				   $formId	=$form['formId'];
				   $data=array();
				   $data['hideTitle']=(isset($form['hideTitle']) && $form['hideTitle'])?$form['hideTitle']:0;
				   $this->model_xform_xform->addForm($data,$formId,true); 
			    }
			    
			    $this->model_xform_xform->addFormFields($fields,$formId);
			    $email_fields= $this->model_xform_xform->getFormEmails($formId,true,$form['userEmail']);
			    $json = array("success" =>1, "formId"=>$formId,"emails"=>$email_fields);
		    } 
		    
		    $this->response->addHeader('Content-Type: application/json');
		    $this->response->setOutput(json_encode($json)); 
         
      } 
      
      public function record_delete() {
           
           $this->load->model('xform/xform');
           $this->load->language('module/xform');
           
           $formId = $this->request->get['formId'];
           
           if($this->request->post && $this->request->post['selected'] && $this->validate()) {
           	 
           	  $selected = $this->request->post['selected'];
          
              if($selected && is_array($selected)) {
           
                foreach($selected as $recordId) {
                   $this->model_xform_xform->deleteFormRecord($recordId);
                }
              }
           }
           
         $this->session->data['success'] = $this->language->get('text_success');	
		 $this->response->redirect($this->url->link('module/xform/records', 'token=' . $this->session->data['token'].'&formId='.$formId, 'SSL'));
         
      } 
      
      public function delete(){
          
           $this->load->model('xform/xform');
           $this->load->language('module/xform');
           
           $this->load->model('extension/module');
           
           if($this->request->post && $this->request->post['selected'] && $this->validate()) {
           	 
           	  $selected = $this->request->post['selected'];
          
              if($selected && is_array($selected)) {
            
                  foreach($selected as $formId) {
               
                     $this->model_xform_xform->deleteForm($formId);
                     $module_id = $this->getModuleByFormId($formId);
                     
                     if($module_id) {
                       $this->model_extension_module->deleteModule($module_id);
                     }
                  }
              }
          } 
         
         if (!$this->validate()) {
            $this->session->data['warning'] = $this->error['warning'];
          } else {
            $this->session->data['success'] = $this->language->get('text_success');	
          }     
         
		 $this->response->redirect($this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL')); 
         
      } 
      
	
	public function upgrade(){
        $this->load->model('xform/xform');
        $this->model_xform_xform->upgrade();
        $this->response->redirect($this->url->link('module/xform', 'token=' . $this->session->data['token'], 'SSL')); 
    }
	
	public function install(){
        $this->load->model('xform/xform');
        
        $this->model_xform_xform->install();
    }
    
    public function uninstall(){        
        $this->load->model('xform/xform');
        
        $this->model_xform_xform->uninstall();
    }
    
    private function arrayToCsv($data=array(),$heading=array(), $filename = 'data.csv')
	 {

		$csv_terminated = "\n";
		$csv_separator = ",";
		$csv_enclosed = '"';
		$csv_escaped = "\\";
		$out="";
		foreach($heading as $head)
		{		
			$out .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,			
			stripslashes($head)) . $csv_enclosed;			
			$out .= $csv_separator;
		
		} // end for   

		$out= rtrim($out,$csv_separator);		
		$out .= $csv_terminated;
		
		

		// Format the data
		foreach($data as $row)
		{
        	foreach($row as $cell)
        	{
				$out .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,			
			           stripslashes($cell)) . $csv_enclosed;			
			    $out .= $csv_separator;
            } 
			
			$out = rtrim($out,$csv_separator);		
		    $out .= $csv_terminated;
			
        } 
  
       header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
       header("Content-Length: " . strlen($out));
       // Output to browser with appropriate mime type, you choose ;)
       header("Content-type: text/x-csv");
       //header("Content-type: text/csv");
       //header("Content-type: application/csv");
       header("Content-Disposition: attachment; filename=$filename");
       echo $out;
       exit;
     }
     
    private function sortArrayByArray($array=array(),$orderArray=array(),$flag='value') {
		
		$ordered = array();
		foreach($orderArray as $key=>$value) {
			if($flag=='value')$key=$value;
			if(array_key_exists($key,$array)) {
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		return $ordered + $array;
    }
    
    
    private function getModuleByFormId ($formId) {
              
               $this->load->model('extension/module');
               $xform_modules = $this->model_extension_module->getModulesByCode('xform');
               $module_id = 0;
               
               if($xform_modules) {
                   foreach($xform_modules as $xform_module) {
                      if ($xform_module) {
		 	           
		 	             $module_stting = (intval(str_replace('.','',VERSION)) > 2033)? json_decode($xform_module['setting'],true):unserialize($xform_module['setting']);
		 
		 	             if($module_stting['formId']==$formId) {
		 	               $module_id = $xform_module['module_id'];
		 	               break;
		 	             }
		              }
                   }
                }
                
          return  $module_id;     
    }
    
}
?>