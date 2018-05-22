<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * SLA :)
 * This class calculate SLA for tickets.
 */
class TsSLA extends TsRegistry{
	
	//Y-m-d H:i:s is used because it's mysql format
	
	/**
	 * $ticketData Passed ticket_id Data will store here
	 * @var array
	 */
	protected $ticketData;

	/**
	 * $agent Agent Details (it can we customers, we used agent for common name)
	 * @var array
	 */
	protected $agent;

	/**
	 * $date SLA Date Array
	 * @var array
	 */
	protected $date = array();

	/**
	 * $operateDate Date Object
	 * @var date
	 */
	protected $operateDate;

	/**
	 * $plusDays Variable used to increase days from today to SLA completed time
	 * @var integer
	 */
	protected $plusDays = 0;

	/**
	 * $today Stored today Day
	 * @var string
	 */
	protected $today;

	/**
	 * $weekDays Weekdays
	 * @var array
	 */
	protected $weekDays = array(
						'monday',
						'tuesday',
						'wednesday',
						'thursday',
						'friday',
						'saturday',
						'sunday',
						);

	/**
	 * initilize Function init all requirement required for SLA Class
	 */
	protected function initilize(){
		$TsService = new TsService($this->registry);

		$this->language->load('ticketsystem/sla');
		
		/**
		 * This can loads model from Admin and Calatog both 
		 * @default admin
		 */
		$TsService->model(array('model'=>'ticketsystem/sla'));
		$TsService->model(array('model'=>'ticketsystem/groups'));
		$TsService->model(array('model'=>'ticketsystem/businesshours'));
		$TsService->model(array('model'=>'ticketsystem/agents'));

		//it will be already loaded from sla calling file
		$this->model_ticketsystem_tickets = $this->registry->get('model_ticketsystem_tickets');
		$this->model_ticketsystem_sla = $this->registry->get('model_ticketsystem_sla');
		$this->model_ticketsystem_groups = $this->registry->get('model_ticketsystem_groups');
		$this->model_ticketsystem_businesshours = $this->registry->get('model_ticketsystem_businesshours');
		$this->model_ticketsystem_agents = $this->registry->get('model_ticketsystem_agents');

		$this->TsCondtions = new TsConditions($this->registry);
		$this->TsHelper = new TsHelper($this->registry);

		$this->operateDate = time();
		//date is not working here so used time()
		// $this->operateDate = date('Y-m-d H:i:s');
	}

	/**
	 * manageSLA This function is called from out with Ticket id on which we want to operate
	 * @param  array  $data which Ticket details
	 */
	public function manageSLA($data = array()){
		//if ticket is not reply then return, sla will not be affected
		if(!isset($data['messagetype']) || $data['messagetype']!='reply')
			return;

		$this->initilize();

		$data['ticket_id'] = isset($data['id']) ? $data['id'] : (isset($data['ticket_id']) ? $data['ticket_id'] : 0);

		if(!$this->TsCondtions->init($data))
			return;

		$this->ticketData = $this->TsCondtions->getTicketData();

		//check if ticket is resolved
		$configResolved = is_array($this->config->get('ts_ticket_status')) ? $this->config->get('ts_ticket_status')['solved'] : 0;
		if($this->ticketData['status']==$configResolved)
			return;

		$SLAs = $this->model_ticketsystem_sla->getSLAs(false, array('sort' => 'sort_order', 'order' => 'ASC'));

		$applySla = false;
		foreach ($SLAs as $sla) {
			if($sla['status']){
				$slaComplete = $this->model_ticketsystem_sla->getSLA($sla['id']);
				if(isset($slaComplete['priority'][$this->ticketData['priority']]) AND $slaComplete['priority'][$this->ticketData['priority']]['status']){
					if(!$slaComplete['conditions_one'] && !$slaComplete['conditions_all']){
						$applySla = $slaComplete['priority'][$this->ticketData['priority']];
						break;
					}elseif($slaComplete['conditions_one']){
						if($this->TsCondtions->checkConditionOne($slaComplete['conditions_one'])){
							$applySla = $slaComplete['priority'][$this->ticketData['priority']];
							break;
						}
					}elseif($slaComplete['conditions_all']){
						if($this->TsCondtions->checkConditionAll($slaComplete['conditions_all'])){
							$applySla = $slaComplete['priority'][$this->ticketData['priority']];
							break;
						}
					}
				}
			}
		}

		$this->setResolveRespondTime($applySla);

		$dataAdd = array(
					'ticket_id' => $data['ticket_id'],
					'resolve_time' => $this->date['resolveDate'],
					'response_time' => $this->date['resposeDate'],
					);

		//clear sla response time
		if($data['sender_type']=='agent')
			$dataAdd['response_time'] = 0;
		//add sla response time if sender is customer

		if($this->model_ticketsystem_tickets->getTicketSLA(array('t.id' => $dataAdd['ticket_id']))){
			unset($dataAdd['resolve_time']);
			$this->model_ticketsystem_tickets->updateTicketSLA($dataAdd);
		}else{
			$this->model_ticketsystem_tickets->addTicketSLA($dataAdd);
		}
	}

	/**
	 * setResolveRespondTime Function calculate SLA Response and Resolve Time based on Calender/ Business Hour
	 * @param array $sla SLA time details
	 */
	protected function setResolveRespondTime($sla){
		$alreadyWorked = false;

		if($sla['hours_type'] AND $this->ticketData['group'] AND ($groupDetails = $this->model_ticketsystem_groups->getGroup($this->ticketData['group']))){
			if($groupDetails['businesshour_id'] AND ($businesshourDetails = $this->model_ticketsystem_businesshours->getBusinessHour($groupDetails['businesshour_id']))){

				$alreadyWorked = true;
				$this->timings = unserialize($businesshourDetails['timings']);
				$this->holidays = $businesshourDetails['holiday'];

				$offset = 0;
				//add this to response and resolve to manage timezone
				// $offset = $this->getTimeZoneOffset($businesshourDetails['timezone']);
				$this->operateDate = date('Y-m-d H:i:s', ($this->operateDate + $offset));
				$this->today = $today = strtolower(date('l', strtotime($this->operateDate)));

				$slaResolveHourSeconds = $this->convertSLAResponseResolveTimeToSeconds($sla['resolve']);
				$slaResponseHourSeconds = $this->convertSLAResponseResolveTimeToSeconds($sla['respond']);

				//for sla response time
				$this->today = $today;
				$this->plusDays = 0;
				$this->date['resposeDate'] = $this->setSlaResponseResolveCalulation($slaResponseHourSeconds);
				//for sla resolve time
				$this->today = $today;
				$this->plusDays = 0;
				$this->date['resolveDate'] = $this->setSlaResponseResolveCalulation($slaResolveHourSeconds);
			}
		}elseif(!$alreadyWorked AND $this->ticketData['assign_agent'] AND ($agentDetails = $this->agent = $this->model_ticketsystem_agents->getAgent(array('a.id' => $this->ticketData['assign_agent'])))){
			//then work like calender hour
			$this->date = array('resolveDate' => "DATE_ADD(NOW(),INTERVAL ".strtoupper(rtrim($sla['resolve']['time'].' '.$sla['resolve']['type'],'s')).")",
								'resposeDate' => "DATE_ADD(NOW(),INTERVAL ".strtoupper(rtrim($sla['respond']['time'].' '.$sla['respond']['type'],'s')).")"
								);
		}
	}

	/**
	 * setSlaResponseResolveCalulation Calculate SLA times and Days times and generate result after converting those to seconds
	 * @param integer $slaXHourSeconds Passed SLA time in seconds
	 */
	protected function setSlaResponseResolveCalulation($slaXHourSeconds){
		$timeResult = "NOW()";
		$slaDay = $this->getSLADay($this->today);
		$slaDayHoursSeconds = $this->getBusinessHourWorkingHours($slaDay);

		if($slaXHourSeconds > $slaDayHoursSeconds){
			$this->plusDays++;
			$this->operateDate = date("Y-m-d H:i:s", strtotime($this->operateDate. '+1 day'));
			$this->today = $this->weekDays[(array_search($this->today, $this->weekDays)+1 <= 6) ? (array_search($this->today, $this->weekDays)+1) : 0];
			return $this->setSlaResponseResolveCalulation($slaXHourSeconds-$slaDayHoursSeconds);
		}else{
			$this->getBusinessHourWorkingHoursAdavance($slaDay, $slaXHourSeconds);
			$this->convertSecondsToHoursMin($slaXHourSeconds);
			//days
			if($this->plusDays || $this->convertedDays)
				$timeResult = "DATE_ADD(NOW(),INTERVAL ".($this->plusDays + $this->convertedDays)." DAY )";
			//hours
			if(isset($timeResult) AND $this->convertedHours)
				$timeResult = "DATE_ADD($timeResult,INTERVAL $this->convertedHours HOUR )";
			elseif($this->convertedHours)
				$timeResult = "DATE_ADD(NOW(),INTERVAL $this->convertedHours HOUR )";
			//min
			if(isset($timeResult) AND $this->convertedMinutes)
				$timeResult = "DATE_ADD($timeResult,INTERVAL $this->convertedMinutes MINUTE )";
			elseif($this->convertedMinutes)			
				$timeResult = "DATE_ADD(NOW(),INTERVAL $this->convertedMinutes MINUTE )";
			//seconds
			if(isset($timeResult) AND $this->convertedSeconds)
				$timeResult = "DATE_ADD($timeResult,INTERVAL $this->convertedSeconds MINUTE )";
			elseif($this->convertedSeconds)			
				$timeResult = "DATE_ADD(NOW(),INTERVAL $this->convertedSeconds SECOND )";
		}
				
		return $timeResult;
	}

	/**
	 * getSLADay get Day if exists in Business hour and return it's timings else search for another day and add plusDay value
	 * @param  string $day weekdays
	 * @return array  timings of day
	 */
	protected function getSLADay($day){
		if(isset($this->timings[$day]) AND $this->isNotHoliday()){
			return $this->timings[$day];
		}else{
			$this->plusDays++;
			$this->operateDate = date("Y-m-d H:i:s", strtotime($this->operateDate. '+1 day'));
			return $this->getSLADay($this->weekDays[( (array_search($day, $this->weekDays)+1 <= 6) ? (array_search($day, $this->weekDays)+1) : 0) ]);
		}
	}

	/**
	 * isNotHoliday Check if current date is not holiday or not
	 * @return boolean Return false if it's holiday
	 */
	protected function isNotHoliday(){
		$itsNotHoliday = true;
		foreach($this->holidays as $holidays){
			// or use this ---> strtotime(date("Y-m-d", strtotime($this->operateDate)));
			if($holidays['to_date'] <= ($compareDate = date("Y-m-d", strtotime($this->operateDate))) AND $holidays['from_date'] >= $compareDate){
				$itsNotHoliday = false;
				break;
			}
		}
		return $itsNotHoliday;
	}

	/**
	 * getBusinessHourWorkingHours Calculate Business hours of passed day From Business hour array
	 * @param  array $day Day Timing
	 * @return integer Seconds of day time
	 */
	protected function getBusinessHourWorkingHours($day){
		$dayMin = $dayHours = 0;
		foreach($day as $hours){
			list($startTime, $endTime) = explode(' - ', $hours);
			list($startTimeHour, $startTimeMin) = explode(':', $startTime);
			list($endTimeHour, $endTimeMin) = explode(':', $endTime);
			$endTimeHour = ($endTimeHour==0 ? 24 : $endTimeHour);
			if((int)$endTimeMin >= (int)$startTimeMin)
				$dayMin = (int)$endTimeMin - (int)$startTimeMin;
			else{
				$dayMin = (int)$endTimeMin - (int)$startTimeMin + 60;
				(int)$endTimeHour--;
			}
			$dayHours = (int)$endTimeHour - (int)$startTimeHour;
		}
		return ($dayHours*60*60) + ($dayMin*60);
		// return number_format($dayHours.'.'.$dayMin, 2, ':', '');
	}

	/**
	 * getBusinessHourWorkingHoursAdavance Advance calculation also includes day start time and gap time between slots
	 * @param  array $day Day Timing
	 * @param  seconds &$slaXHourSeconds Add extra seconds to var
	 * @return integer Seconds of day time
	 */
	protected function getBusinessHourWorkingHoursAdavance($day, &$slaXHourSeconds){
		$diffSeconds = $dayMin = $dayHours = 0;
		foreach($day as $slotKey => $hours){
			list($startTime, $endTime) = explode(' - ', $hours);
			list($startTimeHour, $startTimeMin) = explode(':', $startTime);
			list($endTimeHour, $endTimeMin) = explode(':', $endTime);
			$endTimeHour = ($endTimeHour==0 ? 24 : $endTimeHour);
			if((int)$endTimeMin >= (int)$startTimeMin)
				$dayMin = (int)$endTimeMin - (int)$startTimeMin;
			else{
				$dayMin = (int)$endTimeMin - (int)$startTimeMin + 60;
				(int)$endTimeHour--;
			}
			$dayHours = (int)$endTimeHour - (int)$startTimeHour;
			$thisSlotSeconds = ($dayHours*60*60) + ($dayMin*60);
			if($thisSlotSeconds >= $slaXHourSeconds)
				break;
			else{
				if(isset($day[$slotKey+1])){
					$nextSlot = current(explode(' - ', $day[$slotKey+1]));
					$diffSeconds += $this->getDifferenceFromDayStart($nextSlot, $endTime);
				}
			}
		}

		$hours = $day[0];
		if($this->plusDays)
			$diffSeconds += $this->getDifferenceFromDayStart(current(explode(' - ', $hours)));
		$slaXHourSeconds += $diffSeconds;
	}

	/**
	 * getDifferenceFromDayStart Function calculates differences between passes dates
	 * @param  string $startTime Structure - 00:00
	 * @param  string $dayStart  Structure - 00:00
	 * @return integer seconds based on difference
	 */
	protected function getDifferenceFromDayStart($startTime, $dayStart = '00:00'){
		list($startTimeHour, $startTimeMin) = explode(':', $startTime);
		list($dayStartTimeHour, $dayStartTimeMin) = explode(':', $dayStart);

		$diffSeconds = 0;

		if((int)$dayStartTimeMin!=(int)$startTimeMin){
			if((int)$startTimeMin < (int)$dayStartTimeMin){
				$diffSeconds += ((int)$startTimeMin - (int)$dayStartTimeMin)*(60);
				$startTimeHour--;
			}else
				$diffSeconds += ((int)$startTimeMin - (int)$dayStartTimeMin)*(60);
		}
		if((int)$dayStartTimeHour!=(int)$startTimeHour){
			$diffSeconds += ((int)$startTimeHour - (int)$dayStartTimeHour)*(60*60);
		}

		return $diffSeconds;
	}

	/**
	 * convertSecondsToHoursMin Convert passed seconds to days, hours, mins
	 * @param  integer $seconds seconds
	 * @return string days, hours, mins
	 */
	protected function convertSecondsToHoursMin($seconds){
		$this->convertedDays = floor(($seconds / (3600*24)));
		$this->convertedHours = floor(($seconds / 3600) % 24);
		$this->convertedMinutes = floor(($seconds / 60) % 60);
		$this->convertedSeconds = $seconds % 60;
	}

	/**
	 * convertSLAResponseResolveTimeToSeconds Function convert admin added Priority time for SLA to seconds
	 * @param  array $sla Array with type and time
	 * @return integer seconds
	 */
	protected function convertSLAResponseResolveTimeToSeconds($sla){
		$slaHour = 0;
		if($sla['type']=='minute'){
			$slaHour = (int)$sla['time']*60;
		}elseif($sla['type']=='hours'){
			$slaHour = (int)$sla['time']*60*60;
		}elseif($sla['type']=='days'){
			$slaHour = (int)$sla['time']*60*60*24;
		}elseif($sla['type']=='months'){
			$slaHour = (int)$sla['time']*60*60*24*12;
		}
		return $slaHour;
	}

	/**
	 * getTimeZoneOffset Calculate offset based on passed timezone and current timezone
	 * @param  string $timezone 
	 * @return integer offset -> seconds
	 */
	public function getTimeZoneOffset($timezone){
		$offset = timezone_offset_get(timezone_open($timezone), new \DateTime());
		return $offset;
	}
}