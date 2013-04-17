<?php 

class Core_Pay_History {

	protected $_currency;
	protected $_allowance;
	protected $_events;
	protected $_total;
	protected $_history = array();

	/**
	 *	Constructor
	 */
	public function __construct($allowance = null) {
		$this->_currency = new Zend_Currency('en_US');

		$this->setAllowance($allowance);

		$events = Events::getHistoryByAllowanceId($this->_allowance->id);

		$this->setEvents($events);

		$this->generate();

		//Core_P::p($this->_history, 1);
	}
	// END

	/**
 	 *	Get | Set
	 */
	public function setPayHistory($history = null) {
		$this->_history = $history;
		return $this;
	}
	public function getPayHistory() {
		return $this->_history;
	}
	public function setEvents($events = null) {
		if( is_array($events) ) $events = Core_Helper::to_obj($events, false);
		$this->_events = $events;
		return $this;
	}
	public function getEvents() {
		return $this->_events;
	}
	public function setAllowance($allowance = null) {
		$this->_allowance = $allowance;
		return $this;
	}
	public function getAllowance() {
		return $this->_allowance;
	}
	// END

	/**
 	 *	Generate
	 */
	public function generate() {

		/**
		 *	Build
		 */
		if( count((array) $this->_events) > 0 ) {
			foreach($this->_events as $key => $event) {
				$subtotal 	= 0;
				$history 	= array(
									'created' 		=> $event->created,
									'period_from' 	=> $event->period_from,
									'period_to' 	=> $event->period_to
								);
				if( ! empty($event->Earnings) ) {
					$history['Earnings'] = array();
					foreach($event->Earnings as $earning) {
						$amount 		= $earning->amount;
						$subtotal 	   += $amount;
						$this->_total  += $amount;
						$history['Earnings'][] = array(
														'id' 		=> $earning->Configurations->id,
														'action' 	=> $earning->Configurations->action,
														'label' 	=> $earning->Configurations->label, 
														'current' 	=> array('string' => $this->_currency->toCurrency($amount), 'amount' => $amount)
													);
					}
				}

				if( ! empty($event->Deductions) ) {
					$history['Deductions'] = array();
					foreach($event->Deductions as $deduction) {
						$amount 		= $deduction->amount;
						$subtotal 	   -= $amount;
						$this->_total  -= $amount;
						$history['Deductions'][] = array(
														'id' 		=> $deduction->Configurations->id,
														'action' 	=> $earning->Configurations->action,
														'label' 	=> $deduction->Configurations->label, 
														'current' 	=> array('string' => $this->_currency->toCurrency($amount), 'amount' => $amount)
													);
					}
				}

				$history['subtotal'] = array('string' => $this->_currency->toCurrency($subtotal), 'amount' => $subtotal);
				$this->_history['rows'][] = $history;
			}
		}

		/**
		 *	Set Net Current
		 */
		$this->_history['total']['string'] = $this->_currency->toCurrency($this->_total);
		$this->_history['total']['amount'] = $this->_total;
	}
	// END


}
// END CLASS