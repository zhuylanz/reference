<?php
class ControllerModuleXform extends Controller {
	public function index($setting) {
	
		$this->load->model('xform/xform');
		$this->load->language('module/xform');
		
		
		$formId = isset($setting['formId'])? $setting['formId'] : 0; 
		
		
		if(!$this->model_xform_xform->getFormStatus($formId)) {
		  return '';
		}
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['form_id']) && isset($this->request->post['data']) && $this->request->post['form_id']==$formId && $this->model_xform_xform->validateForm($formId,$this->request->post['data'])) {
		
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
			
			 $redirect=($formInfo['successType']=='redirect' && $formInfo['successURL'])?$formInfo['successURL']: $this->url->link('xform/xform/success', 'formId='.$formId.'&recordId='.$recordId, 'SSL');
			 
			 $this->response->redirect($redirect);
		}
			
		$formInfo= $this->model_xform_xform->getForm($formId);
				   
		$formdata = (isset($this->request->post['data']) && is_array($this->request->post['data']))?$this->request->post['data']:array();
		$data['form']=$this->model_xform_xform->renderForm($formId,$formdata, false, true);
	
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/xform.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/xform.tpl', $data);
		} else {
			return $this->load->view('default/template/module/xform.tpl', $data);
		}
	}
}