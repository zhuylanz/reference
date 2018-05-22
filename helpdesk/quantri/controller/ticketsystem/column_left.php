<?php
class ControllerTicketSystemColumnLeft extends Controller {

	public function index() {
		if (isset($this->request->get['token']) && isset($this->session->data['token']) && ($this->request->get['token'] == $this->session->data['token'])) {

			$this->language->load('ticketsystem/menu');

			$data = array();

			$data['column_left'] = array(
						array(
							'text' => $this->language->get('text_emails'),
							'href' => $this->url->link('ticketsystem/emails','token='.$this->session->data['token'] , 'SSL'),
							'class' => $this->routeCheck('ticketsystem/emails')
							),
						array(
							'text' => $this->language->get('text_activity'),
							'href' => $this->url->link('ticketsystem/activity','token='.$this->session->data['token'] , 'SSL'),
							'class' => $this->routeCheck('ticketsystem/activity')
							),
						array(
							'text' => $this->language->get('text_sla'),
							'href' => $this->url->link('ticketsystem/sla','token='.$this->session->data['token'] , 'SSL'),
							'class' => $this->routeCheck('ticketsystem/sla')
							),
						array(
							'text' => $this->language->get('text_events'),
							'href' => $this->url->link('ticketsystem/events','token='.$this->session->data['token'] , 'SSL'),
							'class' => $this->routeCheck('ticketsystem/events')
							),
						array(
							'text' => $this->language->get('text_rules'),
							'href' => $this->url->link('ticketsystem/rules','token='.$this->session->data['token'] , 'SSL'),
							'class' => $this->routeCheck('ticketsystem/rules')
							),
						array(
							'text' => $this->language->get('text_emailtemplates'),
							'href' => $this->url->link('ticketsystem/emailtemplates','token='.$this->session->data['token'] , 'SSL'),
							'class' => $this->routeCheck('ticketsystem/emailtemplates')
							),
						);

			$this->event->trigger('post.admin.ts.list.add.menu',$data);

			return $this->load->view('ticketsystem/column_left.tpl', $data);
		}
	}

	protected function routeCheck($route){
		if(preg_match("#$route#", $this->request->get['route'])){
			return ' active';
		}
		else
			return false;
	}
}