<?php 

class Core_Pay {

	const BASE_TYPE_ID 			= 1;
	const BONUS_TYPE_ID 		= 2;
	const ADVANCE_TYPE_ID 		= 3;

	const DEDUCTION_TYPE_ID 	= 4;
	const EXPENSE_TYPE_ID 		= 5;
	const DOCKING_TYPE_ID 		= 6;

	const ACTION_EARNINGS 		= 1;
	const ACTION_DEDUCTIONS 	= 0;

	const Label_EARNINGS 		= 'Earning';
	const Label_DEDUCTIONS 		= 'Deduction';


	protected $_allowance;
	protected $_currency;
	protected $_interval;
	protected $_pay;
	protected $_rate;

	protected $_base 	= array(
								'base' 			=> array('rate' => 0, 'interval' => null, 'period_from' => null, 'period_to' => null), 
								'earnings' 		=> array(), 
								'deductions' 	=> array(), 
								'net' 			=> array('current' => array('string' => '', 'amount' => 0), 'ytd' => array('string' => '', 'amount' => 0))
							);

	public $types 		= array(
								self::BASE_TYPE_ID 		=> self::ACTION_EARNINGS,
								self::BONUS_TYPE_ID 	=> self::ACTION_EARNINGS,
								self::ADVANCE_TYPE_ID 	=> self::ACTION_EARNINGS,
								self::DEDUCTION_TYPE_ID => self::ACTION_DEDUCTIONS,
								self::EXPENSE_TYPE_ID 	=> self::ACTION_DEDUCTIONS,
								self::DOCKING_TYPE_ID 	=> self::ACTION_DEDUCTIONS
							);

	/**
	 *	Constructor
	 */
	public function __construct($options = null) {
		$this->_currency = new Zend_Currency('en_US');

		if( is_array($options) && is_object($options['allowance']) && is_array($options['interval']) ) {
			$this->_pay = $this->_base;

			$this->setAllowance($options['allowance'])
				->setRate()
				->setInterval($options['interval'])
				->generate();

			$event 		= Events::getOrCreateEventsByAllowanceIdInPeriod($this->_allowance->id, $this->_interval['period_from'], $this->_interval['period_to']);
			$earnings 	= Earnings::getOrCreateEarningsByEventId($event['id'], $this->_allowance->id, $this->_pay['earnings']);
			$deductions = Deductions::getOrCreateDeductionsByEventId($event['id'], $this->_allowance->id, $this->_pay['deductions']);

			$this->setEarningsYtd($earnings)
				->setDeductionsYtd($deductions)
				->setNetYtd();
		}
	}
	// END

	/**
 	 *	Get Action Options
	 */
	public function getActionOptions() {
		return array(
						self::ACTION_EARNINGS => self::Label_EARNINGS,
						self::ACTION_DEDUCTIONS => self::Label_DEDUCTIONS
					);
	}
	// END

	/**
 	 *	Get | Set Allowance
	 */
	public function getAllowance() {
		return $this->_allowance;
	}
	public function setAllowance($allowance = null) {
		$this->_allowance = $allowance;
		return $this;
	}
	// END

	/**
 	 *	Get | Set Rate
	 */
	public function getRate() {
		return $this->_rate;
	}
	public function setRate() {

		if( empty($this->_allowance) ) {
			throw new Zend_Exception(__METHOD__." :: Allowance Not Set");
		}

		$rate = 0;

		foreach($this->_allowance->AllowanceConfigs as $key => $val) {
			if($val->Configurations->action == self::ACTION_EARNINGS && $val->Configurations->type_id == self::BASE_TYPE_ID) {
				$rate += (float) $val->amount;
			}
		}

		$this->_rate = $rate;

		$this->_pay['base']['rate'] = $this->_currency->toCurrency($rate);

		return $this;
	}
	// END

	/**
 	 *	Get | Set Interval
	 */
	public function getInterval() {
		return $this->_interval;
	}
	public function setInterval($interval = null) {
		$this->_interval = $interval;

		if( empty($this->_interval) ) {
			throw new Zend_Exception(__METHOD__." :: Interval Not Set");
		}

		$this->_pay['base']['interval'] 	= $this->_interval['interval'];
		$this->_pay['base']['period_from'] 	= $this->_interval['period_from'];
		$this->_pay['base']['period_to'] 	= $this->_interval['period_to'];

		return $this;
	}
	// END

	/**
 	 *	Get | Set Pay
	 */
	public function getPay() {
		return $this->_pay;
	}
	public function setPay($pay = null) {
		$this->_pay = $this->_pay;
	}
	// END

	/**
 	 *	Set Earnings Year to Date
	 */
	public function setEarningsYtd($earnings = array()) {
		if( count($earnings) > 0 ) {
			foreach($earnings as $row) {
				$cid = $row['config_id'];
				if( isset($this->_pay['earnings'][$cid]) ) {
					$amount = $this->_pay['earnings'][$cid]['ytd']['amount']+$row['amount'];
					$this->_pay['earnings'][$cid]['ytd']['amount'] = $amount;
					$this->_pay['earnings'][$cid]['ytd']['string'] = $this->_currency->toCurrency($amount);
				}
			}
		}
		return $this;
	}
	// END

	/**
 	 *	Set Deductions Year to Date
	 */
	public function setDeductionsYtd($deductions = array()) {
		if( count($deductions) > 0 ) {
			foreach($deductions as $row) {
				$cid = $row['config_id'];
				if( isset($this->_pay['deductions'][$cid]) ) {
					$amount = $this->_pay['deductions'][$cid]['ytd']['amount']+$row['amount'];
					$this->_pay['deductions'][$cid]['ytd']['amount'] = $amount;
					$this->_pay['deductions'][$cid]['ytd']['string'] = $this->_currency->toCurrency($amount);
				}
			}
		}
		return $this;
	}
	// END

	/**
 	 *	Set Net Year to Date
	 */
	public function setNetYtd() {

		$ytd = 0;
		foreach($this->_pay['earnings'] as $row) {
			$ytd += $row['ytd']['amount'];
		}

		foreach($this->_pay['deductions'] as $row) {
			$ytd -= $row['ytd']['amount'];
		}

		$this->_pay['net']['ytd']['amount'] = $ytd;
		$this->_pay['net']['ytd']['string'] = $this->_currency->toCurrency($ytd);
		return $this;
	}
	// END

	/**
 	 *	Generate
	 */
	public function generate() {

		/**
		 *	Build
		 */
		foreach($this->_allowance->AllowanceConfigs as $key => $val) {

			$action 	= $val->Configurations->action;
			$amount 	= $val->amount;
			$percent 	= $val->percent;

			switch($action) {

				// Earnings
				case self::ACTION_EARNINGS:
					$this->_pay['earnings'][$val->Configurations->id] = array(
																			'id' 		=> $val->Configurations->id,
																			'label' 	=> $val->Configurations->label, 
																			'current' 	=> array('string' => $this->_currency->toCurrency($amount), 'amount' => $amount), 
																			'ytd' 		=> array('string' => null, 'amount' => 0)
																		);

					$this->_pay['net']['current']['amount'] += $amount;
				break;

				// Deductions
				case self::ACTION_DEDUCTIONS:

					$_amount 	= (int) floor($amount);
					$_percent 	= (int) ceil($percent);

					if( empty($_amount) && ! empty($_percent) ) {

						$ma 	= ( $this->_rate * $percent );
						$cp 	= ( $percent * 100 );
						$string = $this->_currency->toCurrency($ma)." ($cp%)";
						$this->_pay['net']['current']['amount'] -= $ma;
						$amount = $ma;
					} 
					else {
						$string = $this->_currency->toCurrency($amount);
						$this->_pay['net']['current']['amount'] -= $amount;
					}

					$this->_pay['deductions'][$val->Configurations->id] = array(
																			'id' 		=> $val->Configurations->id,
																			'label' 	=> $val->Configurations->label, 
																			'current' 	=> array('string' => $string, 'amount' => $amount), 
																			'ytd' 		=> array('string' => null, 'amount' => 0)
																		);
				break;
				default:
					throw new Zend_Exception(__METHOD__." :: Action Misconfigured");
				break;
			}
		}

		/**
		 *	Set Net Current
		 */
		$this->_pay['net']['current']['string'] = $this->_currency->toCurrency($this->_pay['net']['current']['amount']);
	}
	// END

}
// END CLASS