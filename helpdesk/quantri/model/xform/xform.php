<?php
class ModelXformXform extends Model
{
  var $formError=array(); 
	
   public function addForm($data, $id="",$quick=false) {
        
        if (!$id) {
            $sql="INSERT INTO " . DB_PREFIX . "tblform SET hideTitle= '" . (int)$data['hideTitle'] . "'";
                  isset($data['sendAdminEmail'])? $sql.= ", `sendAdminEmail` = '" . (int)$data['sendAdminEmail'] . "'":"";
                  isset($data['adminEmail'])? $sql.= ", `adminEmail` = '" . $this->db->escape($data['adminEmail']) . "'":"";
                  isset($data['formCreationDate'])? $sql.= ", `formCreationDate` = '".$data['formCreationDate']. "'":"";
                  isset($data['status'])? $sql.= ", `status` = '".(int)$data['status']."'":""; 
                  isset($data['sendUserEmail'])? $sql.= ", `sendUserEmail` = '".(int)$data['sendUserEmail']. "'":"";
                  isset($data['userEmail'])? $sql.= ", `userEmail` = '".$this->db->escape($data['userEmail']). "'":"";
                  isset($data['successType'])? $sql.= ", `successType` = '".$this->db->escape($data['successType']). "'":"";
                  isset($data['successURL'])? $sql.= ", `successURL` = '".$this->db->escape($data['successURL']). "'":"";
                  isset($data['formHeading'])? $sql.= ", `formHeading` = '".$this->db->escape($data['formHeading']). "'":"";
                  isset($data['keyword'])? $sql.= ", `keyword` = '".$this->db->escape($data['keyword']). "'":"";
                  isset($data['theme'])? $sql.= ", `theme` = '".$this->db->escape($data['theme']). "'":"";
                  isset($data['custom'])? $sql.= ", `custom` = '".$this->db->escape($data['custom']). "'":"";
                  isset($data['script'])? $sql.= ", `script` = '".$this->db->escape($data['script']). "'":"";
                  isset($data['style'])? $sql.= ", `style` = '".$this->db->escape($data['style']). "'":"";
                  isset($data['storeId'])? $sql.= ", `storeId` = '".$this->db->escape($data['storeId']). "'":"";
                  isset($data['sendEmailAttachment'])? $sql.= ", `sendEmailAttachment` = '".(int)$data['sendEmailAttachment']."'":""; 
                  isset($data['emailAttachmentType'])? $sql.= ", `emailAttachmentType` = '".$this->db->escape($data['emailAttachmentType']). "'":"";
                  isset($data['emailAttachmentUser'])? $sql.= ", `emailAttachmentUser` = '".$this->db->escape($data['emailAttachmentUser']). "'":"";
                  isset($data['emailAttachmentName'])? $sql.= ", `emailAttachmentName` = '".$this->db->escape($data['emailAttachmentName']). "'":"";
                  isset($data['formModule'])? $sql.= ", `formModule` = '".(int)$data['formModule']."'":"";
                  isset($data['dateFormat'])? $sql.= ", `dateFormat` = '".$this->db->escape($data['dateFormat']). "'":"";
                  isset($data['customerOnly'])? $sql.= ", `customerOnly` = '".(int)$data['customerOnly']."'":""; 
                  isset($data['jsvalid'])? $sql.= ", `jsvalid` = '".(int)$data['jsvalid']."'":"";
        } else {
            $sql="UPDATE " . DB_PREFIX . "tblform SET hideTitle= '" . (int)$data['hideTitle'] . "'";
                  isset($data['sendAdminEmail'])? $sql.= ", `sendAdminEmail` = '" . (int)$data['sendAdminEmail'] . "'":"";
                  isset($data['adminEmail'])? $sql.= ", `adminEmail` = '" . $this->db->escape($data['adminEmail']) . "'":"";
                  isset($data['formCreationDate'])? $sql.= ", `formCreationDate` = '".$data['formCreationDate']. "'":"";
                  isset($data['status'])? $sql.= ", `status` = '".(int)$data['status']."'":"";
                  isset($data['sendUserEmail'])? $sql.= ", `sendUserEmail` = '".(int)$data['sendUserEmail']. "'":"";
                  isset($data['userEmail'])? $sql.= ", `userEmail` = '".$this->db->escape($data['userEmail']). "'":"";
                  isset($data['successType'])? $sql.= ", `successType` = '".$this->db->escape($data['successType']). "'":"";
                  isset($data['successURL'])? $sql.= ", `successURL` = '".$this->db->escape($data['successURL']). "'":"";
                  isset($data['formHeading'])? $sql.= ", `formHeading` = '".$this->db->escape($data['formHeading']). "'":"";
                  isset($data['keyword'])? $sql.= ", `keyword` = '".$this->db->escape($data['keyword']). "'":"";
                  isset($data['theme'])? $sql.= ", `theme` = '".$this->db->escape($data['theme']). "'":"";
                  isset($data['custom'])? $sql.= ", `custom` = '".$this->db->escape($data['custom']). "'":"";
                  isset($data['script'])? $sql.= ", `script` = '".$this->db->escape($data['script']). "'":"";
                  isset($data['style'])? $sql.= ", `style` = '".$this->db->escape($data['style']). "'":"";
                  isset($data['storeId'])? $sql.= ", `storeId` = '".$this->db->escape($data['storeId']). "'":"";
                  isset($data['sendEmailAttachment'])? $sql.= ", `sendEmailAttachment` = '".(int)$data['sendEmailAttachment']."'":""; 
                  isset($data['emailAttachmentType'])? $sql.= ", `emailAttachmentType` = '".$this->db->escape($data['emailAttachmentType']). "'":"";
                  isset($data['emailAttachmentUser'])? $sql.= ", `emailAttachmentUser` = '".$this->db->escape($data['emailAttachmentUser']). "'":"";
                  isset($data['emailAttachmentName'])? $sql.= ", `emailAttachmentName` = '".$this->db->escape($data['emailAttachmentName']). "'":"";
                  isset($data['formModule'])? $sql.= ", `formModule` = '".(int)$data['formModule']."'":""; 
                  isset($data['dateFormat'])? $sql.= ", `dateFormat` = '".$this->db->escape($data['dateFormat']). "'":"";
                  isset($data['customerOnly'])? $sql.= ", `customerOnly` = '".(int)$data['customerOnly']."'":"";
                  isset($data['jsvalid'])? $sql.= ", `jsvalid` = '".(int)$data['jsvalid']."'":"";
                  
              $sql.=" WHERE formId = '" . (int)$id . "'";
        }
		
		$this->db->query($sql);
		
		$id = (!$id)? $this->db->getLastId(): $id;
		
		if($quick===false) {
		
				$this->db->query("DELETE FROM " . DB_PREFIX . "tblformdesc WHERE formId = '" . (int)$id . "'");
				foreach ($data['formDesc'] as $language_id => $value) {
		
		     		 $sql="INSERT INTO " . DB_PREFIX . "tblformdesc SET formId = " . (int)$id . ", languageId = " . (int)$language_id . "";
		          		   isset($value['formName'])? $sql.= ", `formName` = '" . $this->db->escape($value['formName']) . "'":"";
                  		   isset($value['formDescription'])? $sql.= ", `formDescription` = '" . $this->db->escape($value['formDescription']) . "'":"";
                  		   isset($value['adminEmailContent'])? $sql.= ", `adminEmailContent` = '" . $this->db->escape($value['adminEmailContent']) . "'":"";
                  		   isset($value['userEmailContent'])? $sql.= ", `userEmailContent` = '".$this->db->escape($value['userEmailContent']). "'":"";
                  		   isset($value['adminEmailSubject'])? $sql.= ", `adminEmailSubject` = '".$this->db->escape($value['adminEmailSubject'])."'":""; 
                  		   isset($value['userEmailSubject'])? $sql.= ", `userEmailSubject` = '".$this->db->escape($value['userEmailSubject']). "'":"";
                  		   isset($value['successMsg'])? $sql.= ", `successMsg` = '".$this->db->escape($value['successMsg']). "'":"";
                  
			         $this->db->query($sql);
		         }
		
	
		        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'form_id=" . (int)$id . "'");
		        if (isset($data['keyword'])) {
		  
		            $row_exist = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($data['keyword']) . "'")->row;
		            if($row_exist) {
		               $data['keyword'] .= rand(1111,9999);
		            } 
		  
		            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'form_id=" . (int)$id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		        }
		}
		return $id;
    }
    
    public function getFormDescriptions($formId) {
		$formDesc = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tblformdesc WHERE formId = '" . (int)$formId . "'");
		foreach ($query->rows as $result) {
			$formDesc[$result['languageId']] = array(
				'formName'             => $result['formName'],
				'formDescription'      => $result['formDescription'],
				'adminEmailContent'       => $result['adminEmailContent'],
				'userEmailContent' => $result['userEmailContent'],
				'adminEmailSubject'     => $result['adminEmailSubject'],
				'userEmailSubject'              => $result['userEmailSubject'],
				'successMsg'              => $result['successMsg']
			);
		}

		return $formDesc;
	}
	
	public function addFormRecord($data,$id="") {
		
		if (!$id) {
            $sql="INSERT INTO " . DB_PREFIX . "tblformrecord SET `formId` = '" . (int)$data['formId'] . "', `userIP` = '" . $this->db->escape($data['userIP']) . "', userAgent = '" . $this->db->escape($data['userAgent']) . "', submitDate = '" . $data['submitDate'] . "', storeId = '".(int)$data['storeId']."', userId = '".(int)$data['userId']."'";
        } else {
            $sql="UPDATE " . DB_PREFIX . "tblformrecord SET `formId` = '" . (int)$data['formId'] . "', `userIP` = '" . $this->db->escape($data['userIP']) . "', userAgent = '" . $this->db->escape($data['userAgent']) . "', submitDate = '" . $data['submitDate'] . "', storeId = '".(int)$data['storeId']."', userId = '".(int)$data['userId']."' WHERE recordId = '" . (int)$id . "'";
        }
		
		$this->db->query($sql);
		
		$id = (!$id)? $this->db->getLastId(): $id;
		return $id;
    }
	
	public function addFormRecordData($data,$id="") {
	
	   if (!$id) {
            $sql="INSERT INTO " . DB_PREFIX . "tblformrecorddata SET `recordId` = '" . (int)$data['recordId'] . "', `formId` = '" . (int)$data['formId'] . "', `fieldType` = '" . $this->db->escape($data['fieldType']) . "', fieldName = '" . $this->db->escape($data['fieldName']) . "', fieldLabel = '".$this->db->escape($data['fieldLabel'])."', fieldValue = '".$this->db->escape($data['fieldValue'])."', isSerialize = '".(int)$data['isSerialize']."'";
        } else {
            $sql="UPDATE " . DB_PREFIX . "tblformrecorddata SET `recordId` = '" . (int)$data['recordId'] . "', `formId` = '" . (int)$data['formId'] . "', `fieldType` = '" . $this->db->escape($data['fieldType']) . "', fieldName = '" . $this->db->escape($data['fieldName']) . "', fieldLabel = '".$this->db->escape($data['fieldLabel'])."', fieldValue = '".$this->db->escape($data['fieldValue'])."', isSerialize = '".(int)$data['isSerialize']."' WHERE recordDataId = '" . (int)$id . "'";
        }
		
		$this->db->query($sql);
		
		$id = (!$id)? $this->db->getLastId(): $id;
		return $id;
		
    }
	
   public function setFormHeading($formId,$formHeading=array()) {
	   
	    if(!is_array($formHeading))$formHeading=array();
		$formHeading=serialize($formHeading);
		
		$this->db->query("UPDATE " . DB_PREFIX . "tblform SET formHeading='$formHeading' WHERE formId='".(int)$formId."'");
    }
	
	public function getFormHeading($formId,$all=false) {
		/* If set to all, then return all heading regardless preset heading*/
		if($all){
			  $fields = $this->getFormFields($formId,false,true);
			  $formHeading=array();
			  foreach($fields as $field){
				 $formHeading[$field['cid']]= $field['label']; 
			  }	
			  return $formHeading;
	    }
		
		$formHeading= $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblform` WHERE formId = '" . (int)$formId . "'")->row['formHeading'];
		if($formHeading)$formHeading=unserialize($formHeading);
		else {
		  $fields      = $this->getFormFields($formId,false,true);	
		  $formHeading=array();
		  foreach($fields as $field){
			 $formHeading[$field['cid']]= $field['label']; 
		     if(count($formHeading)==3) break;
		  }
		  $this->setFormHeading($formId,$formHeading);
	   } 
	   
	   return $formHeading;
    }
	

	public function getRecords($formId,$data=array()){
	
	    $sql = "SELECT * FROM " . DB_PREFIX . "tblformrecord where formId='".(int)$formId."'";
	    
	    if (isset($data['filter_store']) && $data['filter_store']) {
	        $sql .= " and storeId = '".(int)$data['filter_store']."'"; 
	    }
	    
	    if (isset($data['filter_start_date']) && isset($data['filter_end_date']) ) {
	        $sql .= " and DATE_FORMAT(submitDate,'%Y-%m-%d') between '".$data['filter_start_date']."' and '".$data['filter_end_date']."'"; 
	    }
	    
	    if (isset($data['filter_keyword']) && $data['filter_keyword']) {
	        $sql .= " and searchKey like '%".$data['filter_keyword']."%'"; 
	    }
	    
	    $sort_data = array(
		   'submitDate'
		);

	    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		    $sql .= " ORDER BY " . $data['sort'];
	     } else {
		    $sql .= " ORDER BY submitDate";
		 }

	    if (isset($data['order']) && ($data['order'] == 'DESC')) {
		     $sql .= " DESC";
		  } else {
			 $sql .= " DESC";
		 }

		if (isset($data['start']) || isset($data['limit'])) {
		    if ($data['start'] < 0) {
			   $data['start'] = 0;
		    }

		    if ($data['limit'] < 1) {
			   $data['limit'] = 20;
		    }

		   $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
	    }
		
		 $filter='';
		 $result=$this->db->query($sql)->rows;
		 
	     if(!$result)$result=array();
		 foreach($result as $i=>$single) {
		 
		     $date_format = $this->getDateFormat($formId, true);
		 
			 $result[$i]['submitDate']=date($date_format,strtotime($single['submitDate']));
			 $field_rows=$this->db->query("SELECT * FROM " . DB_PREFIX . "tblformrecorddata WHERE formId='".(int)$formId."' and recordId='".$single['recordId']."' order by recordDataId asc")->rows;
	         
	         if(!$field_rows)$field_rows=array();
			 foreach($field_rows as $field_row) {
				  $result[$i][$field_row['fieldName']]= $this->formatViewData($field_row);
		     }
				
	    }
		
	    return $result;
	}
	
	public function getTotalRecords($formId, $data=array()){
	    
	    $sql = "SELECT count(`recordId`) as total FROM `" . DB_PREFIX . "tblformrecord` where formId='".(int)$formId."'";
	    if (isset($data['filter_store']) && $data['filter_store']) {
	        $sql .= " and storeId = '".(int)$data['filter_store']."'"; 
	    }
	    
	    if (isset($data['filter_start_date']) && isset($data['filter_end_date']) ) {
	        $sql .= " and DATE_FORMAT(submitDate,'%Y-%m-%d') between '".$data['filter_start_date']."' and '".$data['filter_end_date']."'"; 
	    }
	    
	    if (isset($data['filter_keyword']) && $data['filter_keyword']) {
	        $sql .= " and searchKey like '%".$data['filter_keyword']."%'"; 
	    }
	   
	   $row= $this->db->query($sql)->row;
	   return $row['total'];
	}
	
	/* We are fetching this way so that we can get in right order as per current form setting*/
	public function getRecordById($recordId,$ignore_empty=true, $ignore_meta=false){
		  
		  $this->load->language('module/xform');	
		  $return=array();
		  
		  $recordInfo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecord` WHERE recordId = '" . (int)$recordId . "'")->row;
		  $formId = $recordInfo['formId'];
		  $fields      = $this->getFormFields($formId,false,true);	
		  
		  $date_format = $this->getDateFormat($formId,true);
		  
		  if(!$ignore_meta) {
		     $return[$this->language->get('text_submission_date')]= date($date_format,strtotime($recordInfo['submitDate']));
		     $return[$this->language->get('text_IP')]= $recordInfo['userIP'];
		     $return[$this->language->get('text_browser')]= $recordInfo['userAgent'];
		  }
		 
		  
		  foreach($fields as $field){
	
	        $recordFieldData= $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecorddata` WHERE fieldName='".$field['cid']."' and recordId='".(int)$recordId."'")->row;
		    $fieldValue=$this->formatViewData($recordFieldData);
		    if($ignore_empty && !$fieldValue) continue;
		    $return[$field['label']]=$fieldValue;
		  }
		
		  
	    return $return;
	}
	
	public function getRecordByOrderId($orderId) {
	  
	  $recordInfo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecord` WHERE orderId = '" . (int)$orderId . "'")->row;
	  
	  $return = array();
	  
	  if($recordInfo) {
	    $return = $this->getRecordById($recordInfo['recordId'],true,true);  
	  } 
	  
	  return $return;
	}
	
   public function getRecordSearchKey($recordId){
      $recordData = $this->getRecordById($recordId,true);
      $return = '';
      foreach($recordData as $value) {
        $return .= ' '.$value;
      }
     return trim($return);
   }	

   public function processFormEmail($recordId) {
	   
	   $recordInfo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecord` WHERE recordId='".(int)$recordId."'")->row;    
	   $formInfo = $this->getForm((int)$recordInfo['formId']);
	   
	   $date_format = $this->getDateFormat($recordInfo['formId'], true);
	     
	   $placeholder=array('{formName}','{userIP}','{submitDate}','{submitDateTime}','{siteURL}');
	   $replacer=array($formInfo['formName'],$recordInfo['userIP'],date($date_format,strtotime($recordInfo['submitDate'])),date('M d, Y h:i A',strtotime($recordInfo['submitDate'])), HTTP_SERVER);

	   
	   $fields      = $this->getFormFields($recordInfo['formId']);	
	   foreach($fields as $field){
	
	       $recordFieldData = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecorddata` WHERE fieldName='".$field['cid']."' and recordId='".(int)$recordId."'")->row;
	       if($recordFieldData) {
	       
		   		$fieldValue=$this->formatViewData($recordFieldData);
		   
		   		$placeholder[]='{'.$field['cid'].'.label}';
		   		$replacer[]=$field['label'];
		   
		   		$placeholder[]='{'.$field['cid'].'.value}';
		   		$replacer[]=$fieldValue;
		   }
		   
		}
		
		
		
	   if($formInfo['sendAdminEmail'] && $formInfo['adminEmail'] && $formInfo['adminEmailSubject'] && $formInfo['adminEmailContent']){
		   
		   $adminEmail=$formInfo['adminEmail']; 
		   
		   $subject=$formInfo['adminEmailSubject']; 
		   $message=$formInfo['adminEmailContent'];
		   $subject=str_replace($placeholder,$replacer,$subject);
		   $message=str_replace($placeholder,$replacer,$message);
		   
		   
		   
		   if($adminEmail){
	
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
				
				$adminEmails=explode(';',$adminEmail);
				foreach($adminEmails as $adminEmail){
				  if($adminEmail) $mail->setTo(trim($adminEmail));
				}
				
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject(html_entity_decode($subject));
				$mail->setHtml(html_entity_decode($message));
				$mail->setText(strip_tags($message));
				$mail->send();		
		   }
		}
		
		
		if($formInfo['sendUserEmail'] && $formInfo['userEmailSubject'] && $formInfo['userEmailContent'] && $formInfo['userEmail']){
		   $emailFieldData = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecorddata` WHERE fieldName='".$formInfo['userEmail']."' and recordId='".(int)$recordId."'")->row; 
		   $userEmail=$emailFieldData['fieldValue'];
		   
		   $subject=$formInfo['userEmailSubject']; 
		   $message=$formInfo['userEmailContent'];
		   $subject=str_replace($placeholder,$replacer,$subject);
		   $message=str_replace($placeholder,$replacer,$message);
		  
		   if($userEmail){
		   
		        $mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
		        
				$mail->setTo($userEmail);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject(html_entity_decode($subject));
				$mail->setHtml(html_entity_decode($message));
				$mail->setText(strip_tags($message));
				$mail->send();
		   }
		}
		
   }
   
   public function processFormData($recordId, $data, $isAdmin=false) {
	   
	   $formId = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecord` WHERE recordId = '" . (int)$recordId . "'")->row['formId'];
	   $fields      = $this->getFormFields($formId);	
	   
	   $hasPriceField=0; 
	   $paymentStatus=0;   
	   $totalAmount=0;
	  
	   foreach($fields as $field){
		  
		  $fieldValue='';
		  $isSerialize=0;
		 
		  
		  if($field['field_type']=='copy' || $field['field_type']=='submit' || $field['field_type']=='captcha')
		   {
			  continue;
		   }
		 elseif($field['field_type']=='text' || $field['field_type']=='hidden' || $field['field_type']=='email' || $field['field_type']=='paragraph' || $field['field_type']=='dropdown')
		   {
			  $fieldValue=$data[$field['cid']];
		   }
		  elseif($field['field_type'] =='date' || $field['field_type'] =='time' || $field['field_type'] =='address')
		   {
			  if(!isset($data[$field['cid']]))$data[$field['cid']]=array();
			  if(!is_array($data[$field['cid']]))$data[$field['cid']]=array();
			  $fieldValue=serialize($data[$field['cid']]);
			  $isSerialize=1;
		   } 
		   elseif($field['field_type'] =='radio')
		    {  
			  $fieldValue=$data[$field['cid']];
			  if(isset($data[$field['cid']]) && $data[$field['cid']]=='Other'.$field['cid'] && isset($data['other_value'.$field['cid']]) && $data['other_value'.$field['cid']]){
				  $fieldValue='Other -'.$data['other_value'.$field['cid']];  
			  }
			   
		    }
		   elseif($field['field_type'] =='checkboxes')
		   {   
			  if(!isset($data[$field['cid']]))$data[$field['cid']]=array();
			  if(!is_array($data[$field['cid']]))$data[$field['cid']]=array();
			  if(isset($data[$field['cid']]['other']) && isset($data[$field['cid']]['other_value']) && $data[$field['cid']]['other_value']){
				 $data[$field['cid']][]='Other -'.$data[$field['cid']]['other_value'];  
			  }
			  $fieldValue=serialize($data[$field['cid']]);
			  $isSerialize=1;
		   } 
		   elseif($field['field_type'] =='file')
		   {
			  if(isset($_FILES['data']['name'][$field['cid']]) && $_FILES['data']['name'][$field['cid']]){
				  $ext = substr(strrchr(($_FILES['data']['name'][$field['cid']]), "."), 1);
				  $fieldValue=time()."_".$field['cid'].'_'.$formId.".$ext";
				  @copy($_FILES['data']['tmp_name'][$field['cid']],DIR_IMAGE.'xform/'.$fieldValue);
			   }
		   } 
		   else{
			  $fieldValue=$data[$field['cid']];   
		   }
		   
		   $recordDataId='';
		   
		   if($isAdmin) { 
		     $recordDataRow= $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecorddata` WHERE recordId = '" . (int)$recordId . "' and fieldName='".$field['cid']."'")->row;
		     if($recordDataRow) $recordDataId = $recordDataRow['recordDataId'];
		   }
		   

		   
		   $recorddata=array();
		   $recorddata['recordId']=$recordId;
		   $recorddata['formId']=$formId;
		   $recorddata['fieldType']=$field['field_type'];
		   $recorddata['fieldName']=$field['cid'];
		   $recorddata['fieldLabel']=$field['label'];
		   $recorddata['fieldValue']=$fieldValue;
		   $recorddata['isSerialize']=$isSerialize;
		   $this->addFormRecordData($recorddata,$recordDataId);

		}
		
		
		$searchKey= $this->getRecordSearchKey($recordId);
		$sql="UPDATE " . DB_PREFIX . "tblformrecord SET `searchKey` = '" . $this->db->escape($searchKey) . "' WHERE recordId = '" . (int)$recordId . "'";
	    $this->db->query($sql);
	   return true;
    } 

	
	
	public function getCommonHeadings($formId) {
	   
	   $this->load->language('module/xform');		
	   $headings=array();	
	   $headings[]=array('cid'=>'userIP','label'=>$this->language->get('text_IP'));
	   $headings[]=array('cid'=>'submitDate','label'=>$this->language->get('text_submission_date'));
	   return $headings;
    }
	
	public function getDateFormat($formId, $phpFormat=false) {
    	
       $format = 'mm/dd/yyyy';
       
       $php_formats = array(
        'mm/dd/yyyy' => 'm/d/Y',
        'dd/mm/yyyy' => 'd/m/Y',
        'yyyy/mm/dd' => 'Y/m/d'
       );
       	
       $result= $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblform` WHERE formId = '".(int)$formId."'")->row;
       
       if(isset($result['dateFormat']) && $result['dateFormat']) {
         $format =  $result['dateFormat'];
       }
       
       if($phpFormat) {
         $format = $php_formats[$format];
       }
       
       return $format;
    }
	
	/* Format data before viewing*/
	public function formatViewData($recordFieldData, &$rawData=array(), $langOptions = array()){
	  
	  if(!isset($recordFieldData['fieldType'])) return '';
	  
	  if($recordFieldData['fieldType']=='text' || $recordFieldData['fieldType']=='hidden' || $recordFieldData['fieldType']=='email')
	   {
		  $rawData[$recordFieldData['fieldName']]=$recordFieldData['fieldValue'];
		  return html_entity_decode($recordFieldData['fieldValue']); 
	   }
	   elseif($recordFieldData['fieldType']=='dropdown')
	   {
		  $valuePieces = explode("~~", $recordFieldData['fieldValue']);	

		  if (isset($valuePieces[1]) && $langOptions) {
		  	$valuePieces[0] = isset($langOptions[$recordFieldData['fieldName'].'_'.$valuePieces[1]]) ? $langOptions[$recordFieldData['fieldName'].'_'.$valuePieces[1]] : $valuePieces[0];
		  }	  
		  $rawData[$recordFieldData['fieldName']] = $recordFieldData['fieldValue'];
		  return html_entity_decode($valuePieces[0]); 
	   } 
	  elseif($recordFieldData['fieldType']=='paragraph')
	   {
		  $rawData[$recordFieldData['fieldName']]=$recordFieldData['fieldValue'];
		  return ($recordFieldData['fieldValue'])?nl2br($recordFieldData['fieldValue']):''; 
	   }
	  elseif($recordFieldData['fieldType'] =='radio')
	   {
		  $valuePieces = explode("~~", $recordFieldData['fieldValue']);	
		  if (isset($valuePieces[1]) && $langOptions) {
		  	$valuePieces[0] = isset($langOptions[$recordFieldData['fieldName'].'_'.$valuePieces[1]]) ? $langOptions[$recordFieldData['fieldName'].'_'.$valuePieces[1]] : $valuePieces[0];
		  }	  
		  $rawData[$recordFieldData['fieldName']] = $recordFieldData['fieldValue'];

		  if(strpos($recordFieldData['fieldValue'],'Other -')){
			 $rawData[$recordFieldData['fieldName']]=='Other'.$recordFieldData['fieldName'];
			 list($other,$other_value)=explode('Other -',$recordFieldData['fieldValue']);
			 $rawData['other_value'.$recordFieldData['fieldName']]= $other_value; 
		  }
		  return $valuePieces[0];
	   } 
	  elseif($recordFieldData['fieldType'] =='checkboxes')
	   {
		  $recordFieldData['fieldValue']=unserialize($recordFieldData['fieldValue']);
		  $checkboxValues = array();

		  if($recordFieldData['fieldValue'] && is_array($recordFieldData['fieldValue'])) {
			  
			  foreach($recordFieldData['fieldValue'] as $singleValue) {
				  
				  $valuePieces = explode("~~", $singleValue);
				  if (isset($valuePieces[1]) && $langOptions) {
				  	  $checkboxValues[] = isset($langOptions[$recordFieldData['fieldName'].'_'.$valuePieces[1]]) ? $langOptions[$recordFieldData['fieldName'].'_'.$valuePieces[1]] : $valuePieces[0];
				  }		

				  if(strpos($singleValue,'Other -')) {
					 list($other,$other_value)=explode('Other -',$singleValue); 
					 $rawData[$recordFieldData['fieldName']]['other']=1;
					 $rawData[$recordFieldData['fieldName']]['other_value']= $other_value;
				  }	    
			  }  
		   }

		  $rawData[$recordFieldData['fieldName']] = $recordFieldData['fieldValue'];

		  return implode(', ',$checkboxValues);
	   }  
	   elseif($recordFieldData['fieldType'] =='file')
	   {
		  $ext = strtolower(substr(strrchr(($recordFieldData['fieldValue']), "."), 1));
		  $file='';
		  if($ext){
			 if($ext=='jpg' || $ext=='png' || $ext=='gif' || $ext=='jpeg'){
			   $file='<a target="_blank" href="'.str_replace('/admin','',HTTP_SERVER.'image/xform/'.$recordFieldData['fieldValue']).'"><img src="'.str_replace('/admin','',HTTP_SERVER.'image/xform/'.$recordFieldData['fieldValue']).'" width="150" /></a>';	 
		     }else{
				$file='<a target="_blank" href="'.HTTP_CATALOG.'index.php?route=xform/xform/dl&f='.$recordFieldData['fieldValue'].'">Uploaded File</a>';	 
			  }  
		  }
		  $rawData[$recordFieldData['fieldName']]=$recordFieldData['fieldValue'];
		  return $file;
	   } 
	   elseif($recordFieldData['fieldType'] =='date')
	   {  
	      $date_format = $this->getDateFormat($recordFieldData['formId']);
	      
		  $recordFieldData['fieldValue']=unserialize($recordFieldData['fieldValue']);
		  
		  if($date_format == 'dd/mm/yyyy') {
		   $date=(isset($recordFieldData['fieldValue']['day']) && isset($recordFieldData['fieldValue']['month']))?$recordFieldData['fieldValue']['day'].'/'.$recordFieldData['fieldValue']['month'].'/'.$recordFieldData['fieldValue']['year']:'';
		  }
		  else if($date_format == 'yyyy/mm/dd') {
		    $date=(isset($recordFieldData['fieldValue']['day']) && isset($recordFieldData['fieldValue']['year']))?$recordFieldData['fieldValue']['year'].'/'.$recordFieldData['fieldValue']['month'].'/'.$recordFieldData['fieldValue']['day']:'';
		  }
		  else {
		    $date=(isset($recordFieldData['fieldValue']['day']) && isset($recordFieldData['fieldValue']['month']))?$recordFieldData['fieldValue']['month'].'/'.$recordFieldData['fieldValue']['day'].'/'.$recordFieldData['fieldValue']['year']:'';
		  }
		  
		  $rawData[$recordFieldData['fieldName']]=$recordFieldData['fieldValue'];
		  return $date;
		  
	   }
	   elseif($recordFieldData['fieldType'] =='time')
	   {
		  $recordFieldData['fieldValue']=unserialize($recordFieldData['fieldValue']);
		  $time=(isset($recordFieldData['fieldValue']['hour']) && isset($recordFieldData['fieldValue']['minute']))?$recordFieldData['fieldValue']['hour'].':'.$recordFieldData['fieldValue']['minute'].' '.$recordFieldData['fieldValue']['ampm']:'';
		  $rawData[$recordFieldData['fieldName']]=$recordFieldData['fieldValue'];
		  return $time;
	   }
	   elseif($recordFieldData['fieldType'] =='address')
	   {
		  $recordFieldData['fieldValue']=unserialize($recordFieldData['fieldValue']);
		  $address= $recordFieldData['fieldValue']['steet'];
		  $address.= isset($recordFieldData['fieldValue']['city'])?', '.$recordFieldData['fieldValue']['city']:'';
		  $address.= isset($recordFieldData['fieldValue']['state'])?', '.$recordFieldData['fieldValue']['state']:'';
		  $address.= isset($recordFieldData['fieldValue']['zip'])?' '.$recordFieldData['fieldValue']['zip']:'';
		  $address.= isset($recordFieldData['fieldValue']['country'])?', '.$recordFieldData['fieldValue']['country']:'';     $rawData[$recordFieldData['fieldName']]=$recordFieldData['fieldValue'];
		  return $address;
	   }  
	   return isset($recordFieldData['fieldValue'])?$recordFieldData['fieldValue']:'';
    }
	
	
	public function getForm($formId){
	   $result= $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblform` WHERE formId = '".(int)$formId."'")->row;
	   if(!$result)$result=array();
	   
	   $result['storeId']= isset($result['storeId']) && $result['storeId']  ? unserialize($result['storeId']): array();
	   
	   $formDesc  = $this->getFormDescriptions($formId);
	   $language_id=$this->config->get('config_language_id');
	   $result['formName']= isset($formDesc[$language_id]['formName'])?$formDesc[$language_id]['formName']:'untitled form';
	   $result['formDescription']= isset($formDesc[$language_id]['formDescription'])?$formDesc[$language_id]['formDescription']:'';
	   $result['adminEmailContent']= isset($formDesc[$language_id]['adminEmailContent'])?$formDesc[$language_id]['adminEmailContent']:'';
	   $result['userEmailContent']= isset($formDesc[$language_id]['userEmailContent'])?$formDesc[$language_id]['userEmailContent']:'';
	   $result['adminEmailSubject']= isset($formDesc[$language_id]['adminEmailSubject'])?$formDesc[$language_id]['adminEmailSubject']:'';
	   $result['userEmailSubject']= isset($formDesc[$language_id]['userEmailSubject'])?$formDesc[$language_id]['userEmailSubject']:'';
	   $result['successMsg']= isset($formDesc[$language_id]['successMsg'])?$formDesc[$language_id]['successMsg']:'';

	   return $result;
	}
	
	public function deleteForm($formId){
	   $this->db->query("DELETE FROM `" . DB_PREFIX . "tblform` WHERE formId = '".(int)$formId."'");
       $this->db->query("DELETE FROM `" . DB_PREFIX . "tblformdesc` WHERE formId = '".(int)$formId."'");
	   $this->db->query("DELETE FROM `" . DB_PREFIX . "tblformfield` WHERE formId = '".(int)$formId."'");
	   $this->db->query("DELETE FROM `" . DB_PREFIX . "tblformlang` WHERE formId = '".(int)$formId."'");
	   $this->db->query("DELETE FROM `" . DB_PREFIX . "tblformrecord` WHERE formId = '".(int)$formId."'");
	   $this->db->query("DELETE FROM `" . DB_PREFIX . "tblformrecorddata` WHERE formId = '".(int)$formId."'");
	}
	
	public function deleteFormRecord($recordId){
	   
	   $this->db->query("DELETE FROM `" . DB_PREFIX . "tblformrecord` WHERE recordId = '".(int)$recordId."'");
	   $this->db->query("DELETE FROM `" . DB_PREFIX . "tblformrecorddata` WHERE recordId = '".(int)$recordId."'");
	}
	
	
	public function getForms($data = array()) {
	    
	    $sql = "SELECT * FROM `" . DB_PREFIX . "tblform`";
	    
	    $sort_data = array(
				'formCreationDate'
		);

	    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		    $sql .= " ORDER BY " . $data['sort'];
	     } else {
		    $sql .= " ORDER BY formCreationDate";
		 }

	    if (isset($data['order']) && ($data['order'] == 'DESC')) {
		     $sql .= " DESC";
		  } else {
			 $sql .= " ASC";
		 }

		if (isset($data['start']) || isset($data['limit'])) {
		    if ($data['start'] < 0) {
			   $data['start'] = 0;
		    }

		    if ($data['limit'] < 1) {
			   $data['limit'] = 20;
		    }

		   $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
	    }
	
	   $result= $this->db->query($sql)->rows;
	   if(!$result)$result=array();
	   return $result;
	}
	
	public function getTotalForms(){
	   $row= $this->db->query("SELECT count(`formId`) as total FROM `" . DB_PREFIX . "tblform`")->row;
	   return $row['total'];
	}
	
	
	
	public function addFormFields($fields,$formId)
	{
	   if(!is_array($fields))$fields=array(); 
	   $insertedId=array();
	  
	   foreach($fields as $i=>$field)
	   {
	     if(!empty($field))
		 {

		    $label = $field['label'];
			$fieldType = $field['field_type'];
			$required = ($field['required'])?1:0;
			$hideLabel = ($field['hide_label'])?1:0;
			$fieldParam = is_array($field['field_options'])?$field['field_options']:array();
			$fieldParam = serialize($fieldParam);
			
			$name = $field['cid'];
			$field_row= $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformfield` WHERE name='".$name."' and formId = '" . (int)$formId . "'")->row;
			if($field_row){
			   $fieldId = $field_row['fieldId'];
			   $sql="UPDATE " . DB_PREFIX . "tblformfield SET `formId` = '" . (int)$formId . "', `label` = '" . $this->db->escape($label) . "', `name` = '" . $this->db->escape($name) . "', fieldType = '" . $this->db->escape($fieldType) . "', required = '".(int)$required."', hideLabel = '".(int)$hideLabel."', fieldParam = '".$this->db->escape($fieldParam)."', sortOrder = '".(int)$i."' WHERE fieldId = '" . (int)$fieldId . "'";
			   $this->db->query($sql);
			}
			else{
			   $sql="INSERT INTO " . DB_PREFIX . "tblformfield SET `formId` = '" . (int)$formId . "', `label` = '" . $this->db->escape($label) . "', `name` = '" . $this->db->escape($name) . "', fieldType = '" . $this->db->escape($fieldType) . "', required = '".(int)$required."', hideLabel = '".(int)$hideLabel."', fieldParam = '".$this->db->escape($fieldParam)."', sortOrder = '".(int)$i."'";
			   $this->db->query($sql);
			   $fieldId = $this->db->getLastId();
		    }
			
			$insertedId[]=$fieldId;
		 }
	   } 
	   
	   if($insertedId){
          $this->db->query("DELETE from " . DB_PREFIX . "tblformfield WHERE formId='".$formId."' and fieldId not in (".implode(',',$insertedId).")");
       }else{
		   /* $insertedId is blank means there is no field there */
		  $this->db->query("DELETE from " . DB_PREFIX . "tblformfield WHERE formId='".$formId."'");   
	   }
	   
	   return true;
	}
	
  
	
	public function getFormFields($formId,$onlyReq=false,$onlyInput=false){
		
		$filter='';
		$filter.=($onlyReq)?" and required=1":"";
		$filter.=($onlyInput)?" and fieldType not in ('submit','captcha','copy')":'';
		
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformfield` WHERE formId = '" . (int)$formId . "' $filter order by sortOrder asc")->rows;
		$fields = array();
		
		foreach($result as $single){
		
			$fieldParam=array();  
			if($single['fieldParam'])$fieldParam=unserialize($single['fieldParam']);  
			
			foreach($fieldParam as $paramIndex=>$paramValue){
			   if($paramIndex=='checked' || $paramIndex=='include_blank_option' || $paramIndex=='include_other_option'){
				   $fieldParam[$paramIndex]=($paramValue)?true:false;
				 }	
			}
			
			$fields[] = array(
				'label'           => $single['label'],
				'cid'            => $single['name'],
				'field_type'      => $single['fieldType'],
				'required'        => ($single['required'])?true:false,
				'hide_label'        => ($single['hideLabel'])?true:false,
				'field_options'   => $fieldParam
			);
		   }

		return $fields;
	}
	
	public function getFormKeywords($formId) {
	 
	  $filter = '';
	  $result = $this->db->query("SELECT name, label FROM `" . DB_PREFIX . "tblformfield` WHERE formId = '" . (int)$formId . "' $filter order by sortOrder asc")->rows;
	  if(!$result)$result=array();

	  $keyword=array();
	  foreach($result as $index=>$single){
	  
	    $keyword[$index]=array( 'label'=>'{'.$single['name'].'.label}',
	     				  'value'=>'{'.$single['name'].'.value}',
	     				  'element'=>'{'.$single['name'].'.element}',
	     				  'info'=>'{'.$single['name'].'.info}',
	     				  'error'=>'{'.$single['name'].'.error}',
	     				   'title' => $single['label']
	     				);  
	  }
	
	  return $keyword;
   }
   
   public function getFormEmails($formId, $ui=false,$sel=''){
	 
	  $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformfield` WHERE formId='".(int)$formId."' and fieldType='email' order by fieldId asc")->rows;
	  if(!$result)$result=array();
	  if($ui===false) return $result;
	  
	  $ui_options='<option value="">-Select-</option>';
	  foreach($result as $email_field){
		   $selected=($sel==$email_field['name'])?'selected':'';
		   $ui_options.='<option '.$selected.' value="'.$email_field['name'].'">'.$email_field['label'].'</option>'; 
		}
	  
	  return $ui_options;
   }	
	
 public function validateForm($formId,$data, $isAdmin=false){
    $fields      = $this->getFormFields($formId,true);	 
	$validate=true;
     
	
	foreach($fields as $field){
	  
	  $regex = (isset($field['field_options']['regex']) && $field['field_options']['regex'])?$field['field_options']['regex']:'';
	  
	  if($field['field_type']=='copy' || $field['field_type']=='hidden' || $field['field_type']=='submit')
	   {
		  continue;
	   }
	elseif($field['field_type']=='email')
	   {
		  if(!isset($data[$field['cid']])) $data[$field['cid']] = '';
		  
		  if(filter_var($data[$field['cid']], FILTER_VALIDATE_EMAIL)===false){
			  $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
		   
		   if($regex && !preg_match($regex,$data[$field['cid']])) {
		      $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
		   
	   }
	elseif($field['field_type']=='text' || $field['field_type']=='paragraph' || $field['field_type']=='price' || $field['field_type']=='dropdown')
	   {
	
		  if(!isset($data[$field['cid']])) $data[$field['cid']] = '';
		  
		  if(!$data[$field['cid']]){
			  $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
		  
		   if($regex && !preg_match($regex,$data[$field['cid']])) {
		      $validate=false;
		      $this->formError[]=$field['cid'];
		   }
	   }
	   elseif($field['field_type']=='checkboxes')
	   {
		  if(!isset($data[$field['cid']])) $data[$field['cid']] = array();
		
		  if(!$data[$field['cid']]){
		      if(!isset($data[$field['cid']]['other_value'])) $data[$field['cid']]['other_value'] = '';
		      if(!$data[$field['cid']]['other_value']) {
			    $validate=false;
		        $this->formError[]=$field['cid'];  
		      }
		   }
		   
	   }
	   elseif($field['field_type']=='radio')
	   {
		  if(!isset($data[$field['cid']])) $data[$field['cid']] = '';
          if(!isset($data['other_value'.$field['cid']])) $data['other_value'.$field['cid']] ='';
		  
		  if(!$data[$field['cid']] && !$data['other_value'.$field['cid']]){
			  $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
	   }
	   
	   
	  elseif($field['field_type'] =='captcha' && !$isAdmin)
	   { 
		   if(!isset($data[$field['cid']])) $data[$field['cid']] = '';
		   
		   if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $data[$field['cid']])) {
			  $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
		   
		   
	   } 
	   elseif($field['field_type'] =='file')
	   { 
		  if(!isset($_FILES['data']['name'][$field['cid']])) $_FILES['data']['name'][$field['cid']] = '';
		  
		  if(!isset($_FILES['data']['name'][$field['cid']]) || !$_FILES['data']['name'][$field['cid']]){
			  $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
	   } 
	   elseif($field['field_type'] =='date')
	   {
		  if(!isset($data[$field['cid']])) $data[$field['cid']] = '';
		  if(!isset($data[$field['cid']]['day'])) $data[$field['cid']]['day'] = '';
		  if(!isset($data[$field['cid']]['day'])) $data[$field['cid']]['month'] = '';
		  if(!isset($data[$field['cid']]['day'])) $data[$field['cid']]['year'] = '';
		  
		  if(!$data[$field['cid']]['day'] || !$data[$field['cid']]['month'] || !$data[$field['cid']]['year']){
			  $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
	   }
	   elseif($field['field_type'] =='time')
	   {
	      if(!isset($data[$field['cid']])) $data[$field['cid']] = '';
		  if(!isset($data[$field['cid']]['hour'])) $data[$field['cid']]['hour'] = '';
		  if(!isset($data[$field['cid']]['minute'])) $data[$field['cid']]['minute'] = '';
		  
		  if(!$data[$field['cid']]['hour'] || !$data[$field['cid']]['minute']){
			  $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
	   }
	   elseif($field['field_type'] =='address')
	   {
		  if(!isset($data[$field['cid']])) $data[$field['cid']] = '';
		  if(!isset($data[$field['cid']]['steet'])) $data[$field['cid']]['steet'] = '';
		  if(!isset($data[$field['cid']]['city'])) $data[$field['cid']]['city'] = '';
		  if(!isset($data[$field['cid']]['state'])) $data[$field['cid']]['state'] = '';
		  if(!isset($data[$field['cid']]['zip'])) $data[$field['cid']]['zip'] = '';
		  
		  if(!$data[$field['cid']]['steet'] || !$data[$field['cid']]['city'] || !$data[$field['cid']]['state']  || !$data[$field['cid']]['zip']){
			  $validate=false;
		      $this->formError[]=$field['cid'];  
		   }
	   }  
    }
	
	
	return $validate;
 }
 

	
 public function renderForm($formId,$data=array(), $isAdmin=false, $fromModule=false, $layout = false){
	  
	  $this->load->language('module/xform');
	  $language_id=$this->config->get('config_language_id');
	  
	  $formErrorMessage=array(
	 	'text'=>$this->language->get('error_text'),
	 	'email'=>$this->language->get('error_email'),
	 	'paragraph'=>$this->language->get('error_paragraph'),
	 	'dropdown'=>$this->language->get('error_dropdown'),
	 	'checkboxes'=>$this->language->get('error_check'),
	 	'radio'=>$this->language->get('error_radio'),
	 	'file'=>$this->language->get('error_file'),
	 	'address'=>$this->language->get('error_address'),
	 	'time'=>$this->language->get('error_time'),
	 	'date'=>$this->language->get('error_date'),
	 	'captcha'=>$this->language->get('error_captcha'),
	 );
	
	$language_id=$this->config->get('config_language_id'); 
	$formInfo = $this->getForm($formId);
	$fields      = $this->getFormFields($formId);
	$this->load->model('localisation/country');
	$countries = $this->model_localisation_country->getCountries();
	$countryInfo = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . $this->config->get('config_country_id'). "'")->row;
   
    $theme = isset($formInfo['theme'])?$formInfo['theme']:'classic';
    
    if($fromModule && $theme!='custom') $theme = 'module';
     
    $output = ""; 
    if(!$isAdmin && $theme!='custom') {
      $output .= '<link href="catalog/view/theme/default/stylesheet/xform/'.$theme.'.css" rel="stylesheet">';
    }
    
    if(!$isAdmin) {
    
       $output .= html_entity_decode($formInfo['script']);
       $output .= html_entity_decode($formInfo['style']);
    }
    
    if($isAdmin && $theme=='custom')  $theme = 'classic'; // custom theme is not available for admin
	
	if(!$layout) {
		$output .= '<div class="form-wrapper">';
		if(!$formInfo['hideTitle']) $output .= '<h1>'.$formInfo['formName'].'</h1>'; 
		if($formInfo['formDescription']) $output .= '<p class="form-desc">'.html_entity_decode($formInfo['formDescription']).'</p>';    
		$output   .= '<form method="post" class="form-class" enctype="multipart/form-data" action="">';
	}
	
	
	if($theme!='custom') {
      $output   .='<ul class="form-ul">';
    }
    
    if($theme=="custom") {
       $output .= html_entity_decode($formInfo['custom']);
    }
    
    $form_langs = $this->getFormLang($formId, $language_id);
    $lables = $form_langs['labels'];
    $lang_options = $form_langs['options'];
    $lang_guidelines = $form_langs['guidelines'];
    $lang_errors = $form_langs['errors'];
	
	 foreach ($fields as $field ) {
		  
		   $css_class='f-'.$field['cid'];
		   if($field['required'])$css_class.=' required';
		   
		   if($layout) {
		     
		      $output   .= '<li class="li-'.$field['field_type'].'">';
		      $output   .= '  <label for="field-'.$field['cid'].'">{'.$field['cid'].'.label}';
		      if($field['required']) $output   .= '<abbr>*</abbr>';
		      $output   .= '  </label>';
		      $output   .= '  {'.$field['cid'].'.element}';
		      $output   .= '  <span class="help-block">{'.$field['cid'].'.info}</span>'; 
		      $output   .= '  <span class="error-block">{'.$field['cid'].'.error}</span>';
		      $output   .= '</li>';
		      continue;
		   }
		     
		   if(in_array($field['cid'],$this->formError)) $css_class.=' error';
		   
		   if($field['field_type']=='captcha' && $isAdmin){
		     $field['hide_label'] = true;
		   }
		   
		    if(!isset($field['field_options']['placeholder'])) $field['field_options']['placeholder'] = false;
		
		   $placeholder = array('{'.$field['cid'].'.label}','{'.$field['cid'].'.element}','{'.$field['cid'].'.info}','{'.$field['cid'].'.error}');
		   $keyword = array();   
		   $keyword[]= $lables[$field['cid']];
		   
		   $element_html = '';
           
           if($theme!="custom") {
		      $output   .= '<li class="li-'.$field['field_type'].'">';
		   }   
		   
		   if(!$field['hide_label'] && $theme!="custom") {
			  $output   .= '<label for="field-'.$field['cid'].'">';
			  $output   .= $lables[$field['cid']]; 
			  if($field['required']) $output   .= '<abbr>*</abbr>';
			  $output   .= '</label>';
		   }
		   
		   if($field['field_type']=='text' || $field['field_type']=='email'){
			    $css_class.=isset($field['field_options']['size'])?' '.$field['field_options']['size']:'';
		        
		        $placeholder ='';
		        if(isset($lang_guidelines[$field['cid']]) && $lang_guidelines[$field['cid']] && $field['field_options']['placeholder']) {
		           $placeholder = 'placeholder="'.$lang_guidelines[$field['cid']].'"';
		        }
		       
		        $element_html   .= '<input '.$placeholder.' id="field-'.$field['cid'].'" class="'.$css_class.'" name="data['.$field['cid'].']" value="'.(isset($data[$field['cid']])?$data[$field['cid']]:'').'" type="text" />';  
		   }
		   
		   if($field['field_type']=='hidden'){
			    $css_class.=' hidden';
		        $element_html   .= '<input id="field-'.$field['cid'].'" class="'.$css_class.'" name="data['.$field['cid'].']" value="'.(isset($data[$field['cid']])?$data[$field['cid']]:'').'" type="hidden" />';  
		   }
		   
		   if($field['field_type']=='paragraph'){
			    $css_class.=isset($field['field_options']['size'])?' '.$field['field_options']['size']:'';
				
				$placeholder ='';
		        if(isset($lang_guidelines[$field['cid']]) && $lang_guidelines[$field['cid']] && $field['field_options']['placeholder']) {
		           $placeholder = 'placeholder="'.$lang_guidelines[$field['cid']].'"';
		        }
				
				$element_html   .= '<textarea '.$placeholder.' id="field-'.$field['cid'].'" class="'.$css_class.'" name="data['.$field['cid'].']">'.(isset($data[$field['cid']])?$data[$field['cid']]:'').'</textarea>';
		   }
		   
		   if($field['field_type']=='copy'){
				$element_html   .= '<p id="field-'.$field['cid'].'" class="'.$css_class.'" name="data['.$field['cid'].']">'.(isset($field['field_options']['html'])?$field['field_options']['html']:'').'</p>';
		   }
		   
		  if($field['field_type']=='dropdown'){
			
			   $element_html   .= '<select id="field-'.$field['cid'].'" class="'.$css_class.'" name="data['.$field['cid'].']">';
			   $element_html   .=isset($field['field_options']['include_blank_option'])?'<option value="">'.$this->language->get('text_xform_select').'</option>':'';
			    foreach ($field['field_options']['options'] as $inc=>$option){
			      $value=($option['value'])?$option['value']:$option['label'];
			      $value .= '~~'.$inc;

				  if(!isset($data[$field['cid']]) && $option['checked'])$data[$field['cid']]=	$value; // set initial value if data is not set
			      $selected=(isset($data[$field['cid']]) && $data[$field['cid']]==$value)?'selected':'';
			      $option['label'] = isset($lang_options[$field['cid'].'_'.$inc])?$lang_options[$field['cid'].'_'.$inc]:$option['label'];
			      $element_html   .= '<option '.$selected.' value="'.htmlentities($value).'">' .html_entity_decode($option['label']). '</option>';
			    }
			    $element_html    .= '</select>';
			  
		   }
		   
		   if($field['field_type']=='checkboxes'){
			 
			    $element_html   .= '<div class="checkbox-wrapper">';
				 $counter=1;
			    foreach ($field['field_options']['options'] as $inc=>$option){
			      $value=($option['value'])?$option['value']:$option['label'];	
			      $value .= '~~'.$inc; 
				  
				  if(!isset($data[$field['cid']]))$data[$field['cid']]=array();
				  if($option['checked'])$data[$field['cid']][]=	$value; // set initial selection if data is not set
			    
				  $checked=(in_array($value,$data[$field['cid']]))?'checked':'';
				  
			      $option['label'] = isset($lang_options[$field['cid'].'_'.$inc])?$lang_options[$field['cid'].'_'.$inc]:$option['label'];
			      $element_html   .= '<label for="field-'.$field['cid'].'-'.$counter.'"><input id="field-'.$field['cid'].'-'.$counter.'" class="'.$css_class.'" name="data['.$field['cid'].'][]" '.$checked.' value="'.htmlentities($value).'" type="checkbox" />&nbsp;'.html_entity_decode($option['label']).'</label>'; 
				  $counter++; 
			    }
				
				/* Option for other*/
				$checked=(isset($data[$field['cid']]['other']) && $data[$field['cid']]['other'])?'checked':'';
				$element_html   .=(isset($field['field_options']['include_other_option']) && $field['field_options']['include_other_option'])?'<label for="field-'.$field['cid'].'-'.$counter.'"><input id="field-'.$field['cid'].'-'.$counter.'" class="'.$css_class.'" name="data['.$field['cid'].'][other]" '.$checked.' value="1" type="checkbox" />&nbsp;'.$this->language->get('text_xform_other').'</label><input placeholder="'.$this->language->get('text_xform_other_placeholder').'" class="option-other" name="data['.$field['cid'].'][other_value]" value="'.(isset($data[$field['cid']]['other_value'])?$data[$field['cid']]['other_value']:'').'" type="text" />':'';
			    $element_html    .= '</div>';
			  
		   }
		   
		   if($field['field_type']=='radio'){
			 
			    $element_html   .= '<div class="radio-wrapper">';
				 $counter=1;
			    foreach ($field['field_options']['options'] as $inc=>$option){
			      $value=($option['value'])?$option['value']:$option['label'];	
			      $value .= '~~'.$inc; 
				  if(!isset($data[$field['cid']]) && $option['checked'])$data[$field['cid']]=	$value; // set initial selection if data is not set
			      $checked=(isset($data[$field['cid']]) && $data[$field['cid']]==$value)?'checked':'';
			      
			      $option['label'] = isset($lang_options[$field['cid'].'_'.$inc])?$lang_options[$field['cid'].'_'.$inc]:$option['label'];
			      $element_html   .= '<label for="field-'.$field['cid'].'-'.$counter.'"><input id="field-'.$field['cid'].'-'.$counter.'" class="'.$css_class.'" name="data['.$field['cid'].']" '.$checked.' value="'.htmlentities($value).'" type="radio" />&nbsp;'.html_entity_decode($option['label']).'</label>'; 
				  $counter++; 
			    }
				
				/* Option for other*/
				$checked=(isset($data[$field['cid']]) && $data[$field['cid']]=='Other'.$field['cid'])?'checked':'';
				$element_html   .=(isset($field['field_options']['include_other_option']) && $field['field_options']['include_other_option'])?'<label for="field-'.$field['cid'].'-'.$counter.'"><input id="field-'.$field['cid'].'-'.$counter.'" class="'.$css_class.'" name="data['.$field['cid'].']" '.$checked.' value="Other'.$field['cid'].'" type="radio" />&nbsp;'.$this->language->get('text_xform_other').'</label><input placeholder="'.$this->language->get('text_xform_other_placeholder').'" class="option-other" name="data[other_value'.$field['cid'].']" value="'.(isset($data['other_value'.$field['cid']])?$data['other_value'.$field['cid']]:'').'" type="text" />':'';
			    $element_html    .= '</div>';
			  
		   }
		   
		   if($field['field_type']=='file'){
			   $element_html   .= '<input id="field-'.$field['cid'].'" class="'.$css_class.'" name="data['.$field['cid'].']" type="file" />';     
		   }
		   
		   if($field['field_type']=='submit'){
			    $css_class.=' btn';
			   $element_html   .= '<input id="field-'.$field['cid'].'" class="'.$css_class.'" name="submit" value="'.(isset($field['label'])?$field['label']:'Submit').'" type="submit" />';  
			   
		   }
		   
		   
		    if($field['field_type']=='date'){
			 
			    $element_html   .= '<div class="date-wrapper">';
				
				if($formInfo['dateFormat'] == 'dd/mm/yyyy') {
				   $element_html   .= '<input placeholder="DD" id="field-'.$field['cid'].'" class="'.$css_class.' day" name="data['.$field['cid'].'][day]" value="'.(isset($data[$field['cid']]['day'])?$data[$field['cid']]['day']:'').'" type="text" /> / '; 
				   $element_html   .= '<input placeholder="MM" class="'.$css_class.' month" name="data['.$field['cid'].'][month]" value="'.(isset($data[$field['cid']]['month'])?$data[$field['cid']]['month']:'').'" type="text" /> / '; 
				   $element_html   .= '<input placeholder="YYYY" class="'.$css_class.' year" name="data['.$field['cid'].'][year]" value="'.(isset($data[$field['cid']]['year'])?$data[$field['cid']]['year']:'').'" type="text" />'; 
				}
				else if($formInfo['dateFormat'] == 'yyyy/mm/dd') {
				   $element_html   .= '<input placeholder="YYYY" class="'.$css_class.' year" name="data['.$field['cid'].'][year]" value="'.(isset($data[$field['cid']]['year'])?$data[$field['cid']]['year']:'').'" type="text" /> / ';  
				   $element_html   .= '<input placeholder="DD" id="field-'.$field['cid'].'" class="'.$css_class.' day" name="data['.$field['cid'].'][day]" value="'.(isset($data[$field['cid']]['day'])?$data[$field['cid']]['day']:'').'" type="text" /> / '; 
				   $element_html   .= '<input placeholder="MM" class="'.$css_class.' month" name="data['.$field['cid'].'][month]" value="'.(isset($data[$field['cid']]['month'])?$data[$field['cid']]['month']:'').'" type="text" />'; 
				}
				else {
				   $element_html   .= '<input placeholder="MM" class="'.$css_class.' month" name="data['.$field['cid'].'][month]" value="'.(isset($data[$field['cid']]['month'])?$data[$field['cid']]['month']:'').'" type="text" /> / '; 
				   $element_html   .= '<input placeholder="DD" id="field-'.$field['cid'].'" class="'.$css_class.' day" name="data['.$field['cid'].'][day]" value="'.(isset($data[$field['cid']]['day'])?$data[$field['cid']]['day']:'').'" type="text" /> / '; 
				   $element_html   .= '<input placeholder="YYYY" class="'.$css_class.' year" name="data['.$field['cid'].'][year]" value="'.(isset($data[$field['cid']]['year'])?$data[$field['cid']]['year']:'').'" type="text" />'; 
				}
				
				$element_html   .='</div>';
			}
			
			if($field['field_type']=='time'){
			 
			    $element_html   .= '<div class="time-wrapper">';
				$element_html   .= '<input placeholder="HH" id="field-'.$field['cid'].'" class="'.$css_class.' hour" name="data['.$field['cid'].'][hour]" value="'.(isset($data[$field['cid']]['hour'])?$data[$field['cid']]['hour']:'').'" type="text" />&nbsp;:&nbsp;'; 
				$element_html   .= '<input placeholder="MM" class="'.$css_class.' minute" name="data['.$field['cid'].'][minute]" value="'.(isset($data[$field['cid']]['minute'])?$data[$field['cid']]['minute']:'').'" type="text" /> '; 
				
				$data[$field['cid']]['ampm']=isset($data[$field['cid']]['ampm'])?$data[$field['cid']]['ampm']:'AM';
				$element_html   .= '<select class="'.$css_class.' ampm" name="data['.$field['cid'].'][ampm]"><option '.(($data[$field['cid']]['ampm']=='AM')?'selected':'').' value="AM">'.$this->language->get('text_xform_am').'</option><option '.(($data[$field['cid']]['ampm']=='PM')?'selected':'').' value="PM">'.$this->language->get('text_xform_pm').'</option></select>'; 
				$element_html   .='</div>';
			}
			
			if($field['field_type']=='captcha' && !$isAdmin){
		        $element_html   .= '<input id="field-'.$field['cid'].'" class="'.$css_class.'" name="data['.$field['cid'].']" value="" type="text" />';  
				$element_html   .='<span><img src="index.php?route=xform/xform/captcha" /></span>';
		   }
		   
		   
		   
		   if($field['field_type']=='address') {
			 
			    $element_html   .= '<div class="address-wrapper">';
				$element_html   .= '	<div class="address-line">
								    <input placeholder="'.$this->language->get('text_xform_street').'" id="field-'.$field['cid'].'" class="'.$css_class.' street" name="data['.$field['cid'].'][steet]" value="'.(isset($data[$field['cid']]['steet'])?$data[$field['cid']]['steet']:'').'" type="text" />
							   </div>';
				
			    $element_html   .= '	<div class="address-line">
								  <input placeholder="'.$this->language->get('text_xform_city').'" class="'.$css_class.' city" name="data['.$field['cid'].'][city]" value="'.(isset($data[$field['cid']]['city'])?$data[$field['cid']]['city']:'').'" type="text" />
								 <input placeholder="'.$this->language->get('text_xform_state').'" class="'.$css_class.' state" name="data['.$field['cid'].'][state]" value="'.(isset($data[$field['cid']]['state'])?$data[$field['cid']]['state']:'').'" type="text" />
						      </div>';
						   
		
		        
				
				$data[$field['cid']]['country']=isset($data[$field['cid']]['country'])?$data[$field['cid']]['country']:$countryInfo['name'];
				$element_html   .= '<div class="input-line">
									 <input placeholder="'.$this->language->get('text_xform_zip').'" class="'.$css_class.' zip" name="data['.$field['cid'].'][zip]" value="'.(isset($data[$field['cid']]['zip'])?$data[$field['cid']]['zip']:'').'" type="text" />
									<select class="'.$css_class.' country" name="data['.$field['cid'].'][country]">';
				foreach($countries as $country) {		
						$element_html   .= '<option value="'.$country['name'].'" '.(($data[$field['cid']]['country']==$country['name'])?'selected':'').'>'.$country['name'].'</option>';
			    }						               
		        $element_html   .=' </select>  </div>';
				$element_html   .='</div>';
			}
		   
		  /*append UI for theme wise*/ 
		  if($theme=='custom') {	
		    $keyword[]= $element_html;
		  } else {
		    $output .= $element_html;
		  }
		   
		   if(isset($lang_guidelines[$field['cid']]) && $lang_guidelines[$field['cid']] && !$field['field_options']['placeholder']) {
		      if($theme=='custom') {	
		         $keyword[] = $lang_guidelines[$field['cid']]; 
		      } else {
			     $output   .= '<span class="help-block">'.$lang_guidelines[$field['cid']].'</span>'; 
			  }    
		   }
		   else {
			 $keyword[] = "";
		   }
		   
		   
		   if(in_array($field['cid'],$this->formError)) {
		   
		     if($theme=='custom') {	
			    $keyword[] = (isset($lang_errors[$field['cid']]) && $lang_errors[$field['cid']])?$lang_errors[$field['cid']]:$formErrorMessage[$field['field_type']]; 
			  } else {
			    
			    $output   .= '<span class="error-block">';
			    $output   .= (isset($lang_errors[$field['cid']]) && $lang_errors[$field['cid']])?$lang_errors[$field['cid']]:$formErrorMessage[$field['field_type']]; 
				$output   .= '</span>'; 
			  
			  }	
			}
			else {
			  $keyword[] = "";
			}
			
		  if($theme!='custom') {	
		     $output   .= '</li>';  
		   }
		   
		   if($theme=='custom') {
		     
		     $output = str_replace($placeholder,$keyword, $output);
		   
		   }
		 	
		}
		
		if($theme!='custom') {
	   	  $output   .= '</ul>';
		}
		
		if(!$layout) {
		
			$output   .= '<input type="hidden" name="form_id" value="'.$formId.'">';
			$output   .= '</form>'; 
			$output   .= '</div>'; 
		}
		
		return $output;
	 }
	 
  public function getRecordData($formId,$recordId){
	
	  $fields      = $this->getFormFields($formId);
	  
	  $return =array();
	  
	  foreach($fields as $field){ 
		 $recordFieldData = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformrecorddata` WHERE fieldName='".$field['cid']."' and recordId='".(int)$recordId."'")->row;
		 if($recordFieldData) $this->formatViewData($recordFieldData,$return);
	  }
	 
	  return $return; 
   }

   
   public function getFormLang ($formId, $languageId='') {
   
      $data_labels = array();
      $data_options = array();
      $data_guidelines = array();
      $data_errors = array();
      
      $labels = array();
      $options = array();
      $guidelines = array();
      $errors = array();
      
      if( !$formId ) return array('labels'=>$labels,'options'=>$options,'guidelines'=>$guidelines, 'errors'=>$errors);
      
      $fields = $this->getFormFields($formId);
      $languageId= ($languageId)? $languageId : $this->config->get('config_language_id');
      $langInfo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformlang` WHERE formId = '" . (int)$formId . "' and languageId = '" . (int)$languageId . "'")->row;
      if($langInfo) {
      
        if( $langInfo['data'] ) {
           $data_labels= unserialize(base64_decode($langInfo['data']));
        }
        
        if( $langInfo['options'] ) {
           $data_options= unserialize(base64_decode($langInfo['options']));
        }
        
         if( $langInfo['guidelines'] ) {
           $data_guidelines= unserialize(base64_decode($langInfo['guidelines']));
          }
         if( $langInfo['errors'] ) {
           $data_errors= unserialize(base64_decode($langInfo['errors']));
          } 
        
      }
      
      foreach($fields as $field) {  
        
        //labels
        $labels[$field['cid']] = (isset($data_labels[$field['cid']]) && $data_labels[$field['cid']])? html_entity_decode($data_labels[$field['cid']]):$field['label'];
        
        //options
        if(isset($field['field_options']['options']) && is_array($field['field_options']['options'])) {
            foreach ($field['field_options']['options'] as $inc=>$option){
                if($option['label']) {
                   $options[$field['cid'].'_'.$inc] = isset($data_options[$field['cid'].'_'.$inc]) && $data_options[$field['cid'].'_'.$inc]?html_entity_decode($data_options[$field['cid'].'_'.$inc]):$option['label'];
                 }
            }
         } 
         
         //guideline
         if(isset($field['field_options']['description']) && $field['field_options']['description']) {
           $guidelines[$field['cid']] = (isset($data_guidelines[$field['cid']]) && $data_guidelines[$field['cid']])? html_entity_decode($data_guidelines[$field['cid']]):$field['field_options']['description'];
         }
         
          /* text/paragraph and showing as guideline*/
         if(isset($field['field_options']['html']) && $field['field_options']['html']) {
           $guidelines[$field['cid']] = (isset($data_guidelines[$field['cid']]) && $data_guidelines[$field['cid']])? html_entity_decode($data_guidelines[$field['cid']]):$field['field_options']['html'];
         }
         
         //error
         if(isset($field['field_options']['error']) && $field['field_options']['error']) {
           $errors[$field['cid']] = (isset($data_errors[$field['cid']]) && $data_errors[$field['cid']])? html_entity_decode($data_errors[$field['cid']]):$field['field_options']['error'];
         }
      }
      
      return array('labels'=>$labels,'options'=>$options,'guidelines'=>$guidelines, 'errors'=>$errors);
       
   }
   
   public function setFormLang($formId, $languageId, $data, $options, $guidelines, $errors) {
      
        $this->db->query("DELETE FROM " . DB_PREFIX . "tblformlang WHERE formId = '" . (int)$formId . "' and languageId = '" . (int)$languageId . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "tblformlang SET formId = '" . (int)$formId . "', languageId = '" . (int)$languageId . "', data = '" . $this->db->escape($data) . "', `options` = '" . $this->db->escape($options) . "', `guidelines` = '" . $this->db->escape($guidelines) . "', `errors` = '" . $this->db->escape($errors) . "'");   
   }
   
   public function getFormLangData ($formId, $languageId) {

      $dataInfo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblformlang` WHERE formId = '" . (int)$formId . "' and languageId = '" . (int)$languageId . "'")->row;
      if($dataInfo) return $dataInfo;
      return '';
       
   }
      
     public function install(){
        
        $this->log->write('xform Module --> Starting install');
	  
        $sql = " CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tblform` (
            `formId` int(11) NOT NULL AUTO_INCREMENT,
  			`hideTitle` int(11) NOT NULL,
 			`sendAdminEmail` tinyint(1) NOT NULL,
  			`adminEmail` varchar(240) NOT NULL,
  			`formCreationDate` datetime NOT NULL,
  			`status` int(11) NOT NULL,
  			`sendUserEmail` tinyint(1) NOT NULL,
  			`userEmail` varchar(100) NOT NULL,
  			`successType` varchar(50) NOT NULL,
  			`successURL` varchar(240) NOT NULL,
  			`formHeading` text NOT NULL,
  			`keyword` varchar(250) NOT NULL,
  			`theme` varchar(100) NOT NULL,
  			`formModule` tinyint(1) NOT NULL,
  			`sendEmailAttachment` tinyint(1) NOT NULL,
  			`emailAttachmentType` varchar(50) NOT NULL,
  			`emailAttachmentUser` varchar(50) NOT NULL,
  			`emailAttachmentName` varchar(200) NOT NULL,
  			`storeId` varchar(250) NOT NULL,
  			`custom` TEXT NOT NULL,
  			`script` TEXT NOT NULL,
  			`style` TEXT NOT NULL,
  			`customerOnly` tinyint(1) NOT NULL,
            `jsvalid` tinyint(1) NOT NULL,
  			`dateFormat` varchar(50) NOT NULL,
             PRIMARY KEY (`formId`)
           ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
        $query = $this->db->query($sql);
        
        $sql = "
            CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tblformdesc` (
            `formDescId` bigint(8) NOT NULL AUTO_INCREMENT,
            `formId` bigint(8) DEFAULT NULL,
            `languageId` int(5) NOT NULL,
            `formName` varchar(200) NOT NULL,
            `formDescription` text NOT NULL,
            `adminEmailContent` text NOT NULL,
            `userEmailContent` text NOT NULL,
            `adminEmailSubject` text NOT NULL,
            `userEmailSubject` text NOT NULL,
            `successMsg` text NOT NULL,
  			  PRIMARY KEY (`formDescId`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ";
        $query = $this->db->query($sql);  
        
       
		 $sql = "
            CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tblformfield` (
               `fieldId` bigint(8) NOT NULL AUTO_INCREMENT,
  			   `formId` int(8) NOT NULL,
  			  `label` varchar(100) NOT NULL,
  			  `hideLabel` tinyint(1) NOT NULL,
  			   `name` varchar(50) NOT NULL,
  		      `fieldType` varchar(100) DEFAULT NULL,
  			  `required` int(11) NOT NULL,
  			  `fieldParam` text NOT NULL,
  			  `sortOrder` int(8) DEFAULT NULL,
  			   PRIMARY KEY (`fieldId`)
             ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ";
        $query = $this->db->query($sql);
       	
		$sql = "
            CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tblformrecord` (
            `recordId` bigint(8) NOT NULL AUTO_INCREMENT,
            `formId` bigint(8) DEFAULT NULL,
            `userIP` varchar(100) NOT NULL,
            `userAgent` varchar(240) NOT NULL,
            `submitDate` datetime DEFAULT NULL,
            `storeId` int(10) NOT NULL,
            `userId` int(8) NOT NULL,
            `orderId` int(8) NOT NULL,
            `productId` int(8) NOT NULL,
            `searchKey` text NOT NULL,
  			  PRIMARY KEY (`recordId`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ";
        $query = $this->db->query($sql);  
        
        $sql = "
            CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tblformrecorddata` (
            `recordDataId` bigint(10) NOT NULL AUTO_INCREMENT,
 			`recordId` bigint(10) NOT NULL,
  			`formId` bigint(8) NOT NULL,
  			`fieldType` varchar(100) NOT NULL,
  			`fieldName` varchar(8) NOT NULL,
  			`fieldLabel` varchar(100) NOT NULL,
  			`fieldValue` text NOT NULL,
  			`isSerialize` int(11) NOT NULL,
             PRIMARY KEY (`recordDataId`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ";
        $query = $this->db->query($sql);
        
         $sql = "
            CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tblformlang` (
  				`formLangId` int(10) NOT NULL AUTO_INCREMENT,
  				`formId` int(8) NOT NULL,
 				`languageId` int(5) NOT NULL,
 				`data` longtext NOT NULL,
 				`options` longtext NOT NULL,
 				`guidelines` longtext NOT NULL,
 				`errors` longtext NOT NULL,
  				PRIMARY KEY (`formLangId`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
         ";
        $query = $this->db->query($sql);
        
      /*Insert demo data*/
      $sql ="INSERT INTO `".DB_PREFIX."tblform` (`formId`, `hideTitle`, `sendAdminEmail`, `adminEmail`, `formCreationDate`, `status`, `sendUserEmail`, `userEmail`, `successType`, `successURL`, `formHeading`, `formModule`, `keyword`, `theme`, `custom`, `script`, `style`, `customerOnly`, `storeId`) VALUES
(1, 0, 1, 'info@example.com', '2015-07-16 15:44:52', 1,1, 'c17', 'self', '', 'a:3:{s:2:\"c2\";s:9:\"Your Name\";s:3:\"c17\";s:5:\"Email\";s:3:\"c14\";s:7:\"Enquiry\";}', 0, 'Contact_Us', 'boxplus', '', '', '', 0, 'a:1:{i:0;s:1:\"0\";}');";
      
       $query = $this->db->query($sql);
       
      $sql = "INSERT INTO `".DB_PREFIX."tblformdesc` (`formDescId`, `formId`, `languageId`, `formName`, `formDescription`, `adminEmailContent`, `userEmailContent`, `adminEmailSubject`, `userEmailSubject`, `successMsg`) VALUES
(1, 1, 1, 'Contact Us', 'Your questions and comments are important to us.  Please fill up following form and send to us, We will back to you soon.', '&lt;p&gt;Hello&lt;/p&gt;&lt;p&gt;Someone has submitted an equerry. Here are info:&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;{c2.label} -&amp;nbsp;{c2.value}&lt;/p&gt;&lt;p&gt;{c6.label} -&amp;nbsp;{c6.value}&lt;br&gt;&lt;/p&gt;&lt;p&gt;{c14.label} -&amp;nbsp;{c14.value}&lt;br&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;', '&lt;p&gt;Hello&amp;nbsp;{c2.value},&lt;/p&gt;&lt;p&gt;Thank you for contacting.&lt;/p&gt;&lt;p&gt;{c2.label} -&amp;nbsp;{c2.value}&lt;/p&gt;&lt;p&gt;{c6.label} -&amp;nbsp;{c6.value}&lt;br&gt;&lt;/p&gt;&lt;p&gt;{c14.label} -&amp;nbsp;{c14.value}&lt;br&gt;&lt;/p&gt;&lt;div&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Regards&lt;/div&gt;&lt;div&gt;{formName}&lt;br&gt;&lt;/div&gt;', 'Contact US', 'Thank you for contacting', '&lt;p&gt;Thank you &amp;nbsp;for contacting to us. We have received your equerry. &amp;nbsp;We will back to you soon.&lt;br&gt;&lt;/p&gt;');";
		
		$query = $this->db->query($sql); 
       
      $sql = "INSERT INTO `".DB_PREFIX."tblformfield` (`fieldId`, `formId`, `label`, `hideLabel`, `name`, `fieldType`, `required`, `fieldParam`, `sortOrder`) VALUES
		(1, 1, 'Your Name', 0, 'c2', 'text', 1, 'a:1:{s:4:\"size\";s:6:\"medium\";}', 0),
		(2, 1, 'Enquiry', 0, 'c14', 'paragraph', 1, 'a:1:{s:4:\"size\";s:6:\"medium\";}', 2),
		(3, 1, 'Verification Code', 0, 'c18', 'captcha', 1, 'a:0:{}', 3),
		(4, 1, 'Submit', 1, 'c22', 'submit', 0, 'a:0:{}', 4),
		(5, 1, 'Email', 0, 'c17', 'email', 1, 'a:1:{s:4:\"size\";s:6:\"medium\";}', 1);";
		
		$query = $this->db->query($sql);
		
		$sql = "INSERT INTO `".DB_PREFIX."url_alias` (`url_alias_id`, `query`, `keyword`) VALUES('', 'form_id=1', 'Contact_Us');";
		$query = $this->db->query($sql);
        
    }
    
    public function uninstall(){
        
         $this->log->write('xform Module --> Starting uninstall');
	  
         $query = $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tblform`"); 
         $query = $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tblformdesc`");
		 $query = $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tblformfield`");
    	 $query = $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tblformrecord`");
         $query = $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tblformrecorddata`");
         $query = $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tblformlang`");
         $this->log->write('xform Module --> Completed uninstall');
    }
    
    
    public function upgrade() { 
		$this->safeColumnAdd('tblform', array (
		                                    array('column'=>'sendEmailAttachment','extra'=>'int(1) NULL'),
									        array('column'=>'emailAttachmentType','extra'=>'VARCHAR( 50 ) NULL'),
										    array('column'=>'emailAttachmentUser','extra'=>'VARCHAR( 50 ) NULL'),
										    array('column'=>'emailAttachmentName','extra'=>'VARCHAR( 200 ) NULL'),
										    array('column'=>'customerOnly','extra'=>'tinyint(1) NOT NULL'),
                                            array('column'=>'jsvalid','extra'=>'tinyint(1) NOT NULL'),
										    array('column'=>'storeId','extra'=>'VARCHAR( 250 ) NULL'),
										    array('column'=>'dateFormat','extra'=>'VARCHAR( 50 ) NULL')
				 )	
		);
		
		$this->safeColumnAdd('tblformlang', array (
										    array('column'=>'options','extra'=>'longtext NOT NULL'),
										    array('column'=>'guidelines','extra'=>'longtext NOT NULL'),
										    array('column'=>'errors','extra'=>'longtext NOT NULL')
				            )	
		);
		
		$this->safeColumnAdd('tblformrecord', array (
								              array('column'=>'searchKey','extra'=>'text NOT NULL'),
								              array('column'=>'orderId','extra'=>'int(8) NOT NULL'),
                                              array('column'=>'productId','extra'=>'int(8) NOT NULL')
				            )	
		);
		
		$sql = "
            CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tblformdesc` (
            `formDescId` bigint(8) NOT NULL AUTO_INCREMENT,
            `formId` bigint(8) DEFAULT NULL,
            `languageId` int(5) NOT NULL,
            `formName` varchar(200) NOT NULL,
            `formDescription` text NOT NULL,
            `adminEmailContent` text NOT NULL,
            `userEmailContent` text NOT NULL,
            `adminEmailSubject` text NOT NULL,
            `userEmailSubject` text NOT NULL,
            `successMsg` text NOT NULL,
  			  PRIMARY KEY (`formDescId`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ";
        $query = $this->db->query($sql);  
	
    }
    public function isDBBUPdateAvail(){
		  
	      $tables=array('tblformdesc');
		  $form_columns=array('sendEmailAttachment','emailAttachmentType', 'emailAttachmentUser','emailAttachmentName','customerOnly','storeId','dateFormat', 'jsvalid');
		  $form_lang_columns = array('options','guidelines','errors');
		  $record_columns = array('searchKey','orderId', 'productId');
		  
		  foreach($tables as $table){
			  if(!$this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = '".DB_DATABASE."' AND table_name = '".DB_PREFIX.$table."' LIMIT 1")->row){
				   return true;
			  }
		  }
		  
		  
		  foreach($form_columns as $column){
			 if(!$this->db->query("SELECT * FROM information_schema.columns WHERE table_schema = '".DB_DATABASE."' AND table_name = '".DB_PREFIX."tblform' and column_name='".$column."' LIMIT 1")->row){
				   return true;
			  }
		  }
		  
		  foreach($form_lang_columns as $column){
			 if(!$this->db->query("SELECT * FROM information_schema.columns WHERE table_schema = '".DB_DATABASE."' AND table_name = '".DB_PREFIX."tblformlang' and column_name='".$column."' LIMIT 1")->row){
				   return true;
			  }
		  }
		  
		  foreach($record_columns as $column){
			 if(!$this->db->query("SELECT * FROM information_schema.columns WHERE table_schema = '".DB_DATABASE."' AND table_name = '".DB_PREFIX."tblformrecord' and column_name='".$column."' LIMIT 1")->row){
				   return true;
			  }
		  }
		  
		  return false;
	}
    
    private function safeColumnAdd($table,$columns){
	   if(!is_array($columns))$columns=array();
	   
	   if($table){
	     foreach($columns as $columnInfo){
			 $column=$columnInfo['column'];
			 $extra=$columnInfo['extra'];
	     	if(!$this->db->query("SELECT * FROM information_schema.columns WHERE table_schema = '".DB_DATABASE."' AND table_name = '".DB_PREFIX.$table."' and column_name='".$column."' LIMIT 1")->row){
	          $query = $this->db->query("ALTER TABLE `".DB_PREFIX.$table."` ADD `".$column."` ".$extra); 
	     	}
	     }
	   }
	}
    
    
    /* site*/
    public function getFormStatus($formId) {
      
       $result= $this->db->query("SELECT * FROM `" . DB_PREFIX . "tblform` WHERE status=1 and formId = '".(int)$formId."'")->row;
	   if(!$result) return false;
	   return true;
    
    }
	
}

?>