<?php
class ControllerXformXform extends Controller {
	private $error = array(); 
	
	public function index() {   
			
		$this->load->language('module/xform');
		$this->load->model('xform/xform');
        $language_id=$this->config->get('config_language_id');
		
		$formId = isset($this->request->get['formId'])? $this->request->get['formId'] : 0; 
		
		
		if(!$this->model_xform_xform->getFormStatus($formId)) {
		  $this->response->redirect(HTTP_SERVER);
		}
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->model_xform_xform->validateForm($formId,$this->request->post['data'])) {
		
             $formdata=array();
			 $formdata['formId']     = $formId;
			 $formdata['userIP'] = $_SERVER['REMOTE_ADDR'];
			 $formdata['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
			 $formdata['submitDate'] = date('Y-m-d H:i:s');
			 $formdata['userId']= ($this->customer->isLogged())? $this->customer->getId(): 0;
			 $formdata['storeId']= $this->config->get('config_store_id');

			 $orderId = isset($this->session->data['orderId'])?$this->session->data['orderId']:0;
			 $orderId = isset($this->request->post['orderId'])?$this->request->post['orderId']:$orderId;
			 $orderId = isset($this->request->get['orderId'])?$this->request->get['orderId']:$orderId;
			 $orderId = isset($this->session->data['order_id']) ? $this->session->data['order_id'] : $orderId ;
			 $formdata['orderId']     = $orderId;

			 $product_id = isset($this->session->data['xproduct_id'])?$this->session->data['xproduct_id']:'';
    		 $product_id = isset($this->request->post['product_id'])?$this->request->post['product_id']:$product_id;
             $product_id = isset($this->request->get['product_id'])?$this->request->get['product_id']:$product_id;

             $formdata['productId']     = $product_id;
		
			 
			 $recordId=$this->model_xform_xform->addFormRecord($formdata);
			 $this->model_xform_xform->processFormData($recordId,$this->request->post['data']);
			 
			 $this->model_xform_xform->processFormEmail($recordId);
			  
			 $formInfo= $this->model_xform_xform->getForm($formId);
			
			 $redirect=($formInfo['successType']=='redirect' && $formInfo['successURL'])?$formInfo['successURL']: $this->url->link('xform/xform/success', 'formId='.$formId.'&recordId='.$recordId, 'SSL');
			 
			 $this->response->redirect($redirect);
		}
			
		$formInfo= $this->model_xform_xform->getForm($formId);
			
		if($formInfo['formName']) {
		
		 $this->document->setTitle($formInfo['formName']);
		 
		} else {
		 
		  $this->document->setTitle($this->language->get('heading_title'));
		}
				
		$data['heading_title'] = $formInfo['formName'];
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', '', 'SSL')
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $formInfo['formName'],
			'href'      => $this->url->link('xform/xform', 'formId='.$formId, 'SSL')
   		);
	
			               
		$formdata = (isset($this->request->post['data']) && is_array($this->request->post['data']))?$this->request->post['data']:array();
		$data['form']=$this->model_xform_xform->renderForm($formId,$formdata);
		
	
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		 if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/xform/xform.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/xform/xform.tpl', $data));
		  } else {
				$this->response->setOutput($this->load->view('default/template/xform/xform.tpl', $data));
		 }
		
	}
      
  public function captcha() {
      
        $this->session->data['captcha'] = substr(sha1(mt_rand()), 17, 6);

		$image = imagecreatetruecolor(150, 35);

		$width = imagesx($image);
		$height = imagesy($image);

		$black = imagecolorallocate($image, 0, 0, 0);
		$white = imagecolorallocate($image, 255, 255, 255);
		$red = imagecolorallocatealpha($image, 255, 0, 0, 75);
		$green = imagecolorallocatealpha($image, 0, 255, 0, 75);
		$blue = imagecolorallocatealpha($image, 0, 0, 255, 75);

		imagefilledrectangle($image, 0, 0, $width, $height, $white);

		imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $red);
		imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $green);
		imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $blue);

		imagefilledrectangle($image, 0, 0, $width, 0, $black);
		imagefilledrectangle($image, $width - 1, 0, $width - 1, $height - 1, $black);
		imagefilledrectangle($image, 0, 0, 0, $height - 1, $black);
		imagefilledrectangle($image, 0, $height - 1, $width, $height - 1, $black);

		imagestring($image, 10, intval(($width - (strlen($this->session->data['captcha']) * 9)) / 2), intval(($height - 15) / 2), $this->session->data['captcha'], $black);

		header('Content-type: image/jpeg');

		imagejpeg($image);

		imagedestroy($image);
   }
	
	
	public function success() {
         
            $this->load->language('module/xform');
            $this->load->model('xform/xform');
            $language_id=$this->config->get('config_language_id');
            $formId = $this->request->get['formId'];	
            $recordId = $this->request->get['recordId'];	
			$formInfo= $this->model_xform_xform->getForm($formId);
			
			$recordPlaceholders = $this->model_xform_xform->getRecordPlaceholder($recordId);
	   		$placeholder = $recordPlaceholders['placeholder'];
	  		$replacer = $recordPlaceholders['replacer'];
			
			if(!$formInfo['successMsg']) {
			  $data['form']='Thank you for your submission.';
			} else {  
			  $data['form'] =  $message=str_replace($placeholder,$replacer, html_entity_decode($formInfo['successMsg']));
			}
			
			if($formInfo['formName']) {
		
		 		$this->document->setTitle($formInfo['formName']);
		 
			} else {
		 
		  		$this->document->setTitle($this->language->get('heading_title'));
			}
		   
		   $data['heading_title'] = $formInfo['formName'];
		   
		   $data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', '', 'SSL')
   		   );
		
   		   $data['breadcrumbs'][] = array(
       		'text'      => $formInfo['formName'],
			'href'      => $this->url->link('xform/xform', 'formId='.$formId, 'SSL')
   		   );
			
		    $data['column_left'] = $this->load->controller('common/column_left');
		    $data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

		 	if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/xform/xform.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/xform/xform.tpl', $data));
		 	 } else {
				$this->response->setOutput($this->load->view('default/template/xform/xform.tpl', $data));
		 	}
         
      } 
      
      public function dl() {
     
         if(!isset($this->request->get['f'])) return;
        
         $file = $this->request->get['f'];
         if($file && file_exists(DIR_IMAGE.'xform/'.$file)) {
		    $file_name = DIR_IMAGE.'xform/'.str_replace(array('#',' ',"'",'"','!','@','#','$','%','^','&','*','(',')','~','`'),'_',$file);
		    header("Content-disposition: attachment; filename={$file_name}");
            header('Content-type: application/octet-stream'); 
            readfile(DIR_IMAGE.'xform/'.$file);
		 }
		 
	   exit;	 
     }
     
     public function ajaxSubmit() {
       		 $this->load->language('module/xform');
		     $this->load->model('xform/xform');
		     
		     $formId = isset($this->request->get['formId'])? $this->request->get['formId'] : 0; 
		     $json = array();
		      
		     if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['data']) && $this->model_xform_xform->validateForm($formId,$this->request->post['data'])) {
		
            		$formdata=array();
					$formdata['formId']     = $formId;
			 		$formdata['userIP'] = $_SERVER['REMOTE_ADDR'];
			 		$formdata['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
			 		$formdata['submitDate'] = date('Y-m-d H:i:s');
			 		$formdata['userId']= ($this->customer->isLogged())? $this->customer->getId(): 0;
					$formdata['storeId']= $this->config->get('config_store_id');

					$orderId = isset($this->session->data['orderId'])?$this->session->data['orderId']:0;
					$orderId = isset($this->request->post['orderId'])?$this->request->post['orderId']:$orderId;
					$orderId = isset($this->request->get['orderId'])?$this->request->get['orderId']:$orderId;
					$orderId = isset($this->session->data['order_id']) ? $this->session->data['order_id'] : $orderId ;
					$formdata['orderId']     = $orderId;

					
			 		$recordId=$this->model_xform_xform->addFormRecord($formdata);
			 		$this->model_xform_xform->processFormData($recordId,$this->request->post['data']);
			 		$this->model_xform_xform->processFormEmail($recordId);
			 		
			 		$formInfo= $this->model_xform_xform->getForm($formId);
			 		$redirect=($formInfo['successType']=='redirect' && $formInfo['successURL'])?$formInfo['successURL']: '';
			 		$message = $formInfo['successMsg'] ? $formInfo['successMsg'] : 'Thank you for your submission.';
		     }
		     
		     $formError = array();
		     if(isset($this->request->post['data'])) {
		        $formError = $this->model_xform_xform->getErrorDetails($formId,$this->request->post['data']);
		     }
		     
		     $json['error'] = $formError;
		     $json['redirect'] = $redirect;
		     $json['message'] = $message;
		     
		     echo json_encode($json);
		     exit;
		     
     }
}
?>