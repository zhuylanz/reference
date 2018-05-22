<?php
use PHPMailer\PHPMailer;

if(!function_exists('helpDeskErrorHandler')){
	function helpDeskErrorHandler() {}
}

/**
 * Set Fake error handler to skip errors
 */
set_error_handler("helpDeskErrorHandler");

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This class is used to send mail with PHPMailer Class
 */
class TsEmail extends TsRegistry{

	/**
	 * sendMail Send Mail based on Passed data
	 * @param  array $data Mail data with various mail details
	 * @return string error/ null
	 */
	public function sendMail($data) {
		if(!current($data['address']))
			return;
		
		$mail = new PHPMailer(true);

		switch($this->config->get('config_mail_protocol')){
			case 'smtp':
				$mail->isSMTP();
				$mail->SMTPAuth = true;
				$mail->Host = $this->config->get('config_mail_smtp_hostname');
				$mail->Port = $this->config->get('config_mail_smtp_port');
				$mail->Timeout = (60*(int)$this->config->get('config_mail_smtp_timeout'));
				$mail->Username = $this->config->get('config_mail_smtp_username');
				$mail->Password = $this->config->get('config_mail_smtp_password');
				break;
			case 'mail':
			default:
				$mail->isMail();
				break;
		}

		if(isset($data['customHeaders']))
			foreach ($data['customHeaders'] as $customHeader) {
				$mail->addCustomHeader($customHeader['name'], $customHeader['value']);
			}

		foreach ($data['address'] as $address) {
			$mail->addAddress($address);
		}

		if(isset($data['cc']))
			foreach ($data['cc'] as $cc) {
				$mail->addCC($cc);
			}

		if(isset($data['bcc']))
			foreach ($data['bcc'] as $bcc) {
				$mail->addBCC($bcc);
			}

		if(isset($data['setFrom']) AND ($setFrom = $data['setFrom']))
			$mail->setFrom($setFrom['email'], (isset($setFrom['name']) ? $setFrom['name'] : ''));
		
		if(isset($data['replyTo']))
			foreach ($data['replyTo'] as $replyTo) {
				$mail->addReplyTo($replyTo['email'], (isset($replyTo['name']) ? $replyTo['name'] : ''));
			}

		$mail->Subject = $data['subject'];
		$mail->isHTML(true);
        $mail->WordWrap = 78; // set word wrap to the RFC2822 limit
        $mail->msgHTML($this->emailTemplate($data['subject'], html_entity_decode($data['message'], ENT_QUOTES, 'UTF-8')));

		if(isset($data['attachments']))
			foreach($data['attachments'] as $attachment){
        		$mail->addAttachment($attachment['attachment'], $attachment['name']); // optional name
			}

        try{
        	$mail->send();
        }catch(Exception $e){
        	exit($e->getMessage());
        }
	}

	/**
	 * emailTemplate Create email template
	 * @param  string $subject
	 * @param  string $message
	 * @return string html
	 */
	protected function emailTemplate($subject, $message){
		$html = <<<HTML
		<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>{$subject}</title>
		</head>
		<body> 
		<div class="content">
			<table border="0" cellspacing="0" cellpadding="0" align="center" style="width:100%;font-family: 'Open Sans', sans-serif; font-size: 12px;color: #666;">
				<tbody>
					<tr>
						<td style="width:100%;background-color:#ECECEC;padding:20px;">$message</td>
					</tr>
				</tbody>
			</table>
		</div>
		</body>
		</html>
HTML;
		return $html;
	}
}