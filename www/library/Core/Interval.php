<?php 

class Core_Interval {

	protected $_config;
	protected $_calendar;

	protected $_week 		= 604800;

	const WEEKLY 			= 1;
	const BIMONTHLY 		= 2;
	const MONTHLY 			= 3;
	const QUARTERLY 		= 4;
	const ANNUALLY 			= 5;

	const Label_WEEKLY 		= 'Weekly';
	const Label_BIMONTHLY 	= 'Bi-Monthly';
	const Label_MONTHLY 	= 'Monthly';
	const Label_QUARTERLY 	= 'Quarterly';
	const Label_ANNUALLY 	= 'Annually';


	const MW_1 				= 1;
	const MW_2 				= 2;
	const MW_3 				= 3;
	const MW_4 				= 4;

	const Label_MW_1 		= 'First Week';
	const Label_MW_2 		= 'Second Week';
	const Label_MW_3 		= 'Third Week';
	const Label_MW_4 		= 'Last Week';


	const MP_1 				= 1;
	const MP_2 				= 2;

	const Label_MP_1 		= 'First Half';
	const Label_MP_2 		= 'Second Half';


	const Q_1 				= 1;
	const Q_2 				= 2;
	const Q_3 				= 3;
	const Q_4 				= 4;

	const Label_Q_1 		= 'Q1';
	const Label_Q_2 		= 'Q2';
	const Label_Q_3 		= 'Q3';
	const Label_Q_4 		= 'Q4';

	/**
 	 *	GET | SET Calendar Params
	 */
	public function getCalendarParams() {
		return $this->_calendar;
	}
	public function setCalendarParams($calendar = array()) {
		$this->_calendar = $calendar;
	}
	// END

	/**
 	 *	Generate Interval
	 */
	public function generateInterval($allowance = null) {

		/**
		 *	Switch by Period
		 */
		$period = (int) $allowance['period'];
		switch($period) {
			case self::WEEKLY:
				$period_from 	= $this->_getDateFromWeek(($this->_calendar['week']['current']['value']-1));
				$period_to 		= $this->_getDateFromWeek($this->_calendar['week']['current']['value']);
			break;
			case self::BIMONTHLY:
				$dates 			= $this->_getDatesFromBiMonth($this->_calendar['month']['current']['value'], $this->_calendar['month']['monthpart']['value']);
				$period_from 	= $dates['from'];
				$period_to 		= $dates['to'];
			break;
			case self::MONTHLY:
				$period_from 	= $this->_getDateFromMonth(($this->_calendar['month']['current']['value']-1));
				$period_to 		= $this->_getDateFromMonth($this->_calendar['month']['current']['value']);
			break;
			case self::QUARTERLY:
				$dates 			= $this->_getDatesFromQuarter($this->_calendar['quarter']['current']['value']);
				$period_from 	= $dates['from'];
				$period_to 		= $dates['to'];
			break;
			case self::ANNUALLY:
				$dates 			= $this->_getDatesFromYear($this->_calendar['year']);
				$period_from 	= $dates['from'];
				$period_to 		= $dates['to'];
			break;
			default: 
				throw new Zend_Exception(__METHOD__." :: Period Misconfigured");
			break;
		}

		return array('interval' => $this->getLabelByPeriod($period), 'period_from' => $period_from, 'period_to' => $period_to);
	}
	// END

	/**
 	 *	Get Interval Label By Period
	 */
	public function getLabelByPeriod($period = 1) {
		$options = $this->getIntervalOptions();
		return $options[$period];
	}
	// END

	/**
 	 *	Get Quarter Args
	 *
	 *	@access	public
	 *	@param	int
	 *	@return	array
	 */
	public function getQuarterArgs($qtr = 1) {
		$options = $this->getQuarterOptions();
		return array('label' => $options[$qtr], 'value' => $qtr);
	}
	// END

	/**
 	 *	Quarter Options
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function getQuarterOptions() {
		return array(
						self::Q_1 => self::Label_Q_1,
						self::Q_2 => self::Label_Q_2,
						self::Q_3 => self::Label_Q_3,
						self::Q_4 => self::Label_Q_4
					);
	}
	// END

	/**
 	 *	Get Month Args
	 *
	 *	@access	public
	 *	@param	int
	 *	@return	array
	 */
	public function getMonthArgs($month = 1) {
		return array('label' => date("F"), 'value' => $month);
	}
	// END

	/**
 	 *	Get Month Part
	 *
	 *	@access	public
	 *	@param	int
	 *	@param	int
	 *	@param	int
	 *	@return	void
	 */
	public function getMonthPartArgs($mdays, $day, $d = 4) {

		$r = round($mdays/$d, 0);
		switch($d) {
			case 2:
				$options 	= $this->getMpOptions();
				$value 		= ($day > $r) ? 2:1;
				$label 		= $options[$value];
			break;
			default:
				$options 	= $this->getMwOptions();
				$value 		= ($day <= $r) ? 1:(($day <= ($r*2)) ? 2:(($day <= ($r*3)) ? 3:4));
				$label 		= $options[$value];
			break;
		}
		return array('label' => $label, 'value' => $value); 
	}
	// END

	/**
 	 *	Month Week Options
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function getMwOptions() {
		return array(
						self::MW_1 => self::Label_MW_1,
						self::MW_2 => self::Label_MW_2,
						self::MW_3 => self::Label_MW_3,
						self::MW_4 => self::Label_MW_4
					);
	}
	// END

	/**
 	 *	Month Part Options
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function getMpOptions() {
		return array(self::MP_1 => self::Label_MP_1, self::MP_2 => self::Label_MP_2);
	}
	// END

	/**
 	 *	Get Week Args
	 *
	 *	@access	public
	 *	@param	string
	 *	@return	array
	 */
	public function getWeekArgs($yw = 1) {
		return array('label' => "Week", 'value' => $yw);
	}
	// END

	/**
 	 *	Get Day Args
	 *
	 *	@access	public
	 *	@param	string
	 *	@return	array
	 */
	public function getDayArgs($yd = 1) {
		return array('label' => "Day", 'value' => $yd);
	}
	// END

	/**
 	 *	Interval Options
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	array
	 */
	public function getIntervalOptions() {
		return array(
						self::WEEKLY 	=> self::Label_WEEKLY,
						self::BIMONTHLY => self::Label_BIMONTHLY,
						self::MONTHLY 	=> self::Label_MONTHLY,
						self::QUARTERLY => self::Label_QUARTERLY,
						self::ANNUALLY 	=> self::Label_ANNUALLY
					);
	}
	// END

	/**
 	 *	Get Quarter
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _getQuarter($date = null) { 
		$date = (is_null($date)) ? date("Y-m-d"):$date;
		return (int) floor(date('m', strtotime($date))/3.1)+1;
	}
	// END

	/**
 	 *	Get Date From Week
	 *
	 *	@access	public
	 *	@param	int
	 *	@return	string
	 */
	private function _getDateFromWeek($w) {
		return date("Y-m-d",(mktime(0, 0, 0, 1, 1, $this->_calendar['year'])+($this->_week * $w)));
	}
	// END

	/**
 	 *	Get Date From Bi Month
	 *
	 *	@access	public
	 *	@param	int
	 *	@return	string
	 */
	private function _getDatesFromBiMonth($m, $part = 1) {

		$days = date('t', mktime(0, 0, 0, $m, 1, $this->_calendar['year'])); 

		switch($part) {
			case 2:
				$dates 	= array(
									'from' 	=> date("Y-m-d", mktime(0, 0, 0, $m, (round(($days/2), 0)+1), $this->_calendar['year'])),
									'to' 	=> date("Y-m-d", mktime(0, 0, 0, $m, $days, $this->_calendar['year']))
								);
			break;
			default:
				$dates 	= array(
									'from' 	=> date("Y-m-d", mktime(0, 0, 0, $m, 1, $this->_calendar['year'])),
									'to' 	=> date("Y-m-d", mktime(0, 0, 0, $m, (round(($days/2), 0)), $this->_calendar['year']))
								);
			break;
		}

		return $dates;
	}
	// END

	/**
 	 *	Get Date From Month
	 *
	 *	@access	public
	 *	@param	int
	 *	@return	string
	 */
	private function _getDateFromMonth($m) {
		return date("Y-m-d", mktime(0, 0, 0, $m, 1, $this->_calendar['year']));
	}
	// END

	/**
 	 *	Get Date From Quarter
	 *
	 *	@access	public
	 *	@param	int
	 *	@return	string
	 */
	private function _getDatesFromQuarter($q = 1) {

		switch($q) {
			case 4:
				$dates 	= array(
									'from' 	=> date("Y-m-d", mktime(0, 0, 0, 10, 1, $this->_calendar['year'])),
									'to' 	=> date("Y-m-d", mktime(0, 0, 0, 12, 31, $this->_calendar['year']))
								);
			break;
			case 3:
				$dates 	= array(
									'from' 	=> date("Y-m-d", mktime(0, 0, 0, 7, 1, $this->_calendar['year'])),
									'to' 	=> date("Y-m-d", mktime(0, 0, 0, 9, 30, $this->_calendar['year']))
								);
			break;
			case 2:
				$dates 	= array(
									'from' 	=> date("Y-m-d", mktime(0, 0, 0, 4, 1, $this->_calendar['year'])),
									'to' 	=> date("Y-m-d", mktime(0, 0, 0, 6, 30, $this->_calendar['year']))
								);
			break;
			default:
				$dates 	= array(
									'from' 	=> date("Y-m-d", mktime(0, 0, 0, 1, 1, $this->_calendar['year'])),
									'to' 	=> date("Y-m-d", mktime(0, 0, 0, 3, 31, $this->_calendar['year']))
								);
			break;
		}

		return $dates;
	}
	// END

	/**
 	 *	Get Date From Year
	 *
	 *	@access	public
	 *	@param	int
	 *	@return	string
	 */
	private function _getDatesFromYear($y) {
		return array('from' => date("Y-m-d", mktime(0, 0, 0, 1, 1, $y)), 'to' => date("Y-m-d", mktime(0, 0, 0, 12, 31, $y)));
	}
	// END

	/**
 	 *	Constructor
	 */
	public function __construct($_init = false) {

		if($_init) {
			$this->_config = Zend_Registry::get('config');
			$year 	= date("Y");
			$month 	= date("n");
			$day 	= date("j");
			$mdays 	= date("t");
			$yw 	= date("W");
			$yd  	= date("z");
			$qtr 	= $this->_getQuarter();
			$args 	= array(
							'year' 		=> $year,
							'quarter' 	=> array( 
												'current' 	=> $this->getQuarterArgs($qtr)
												),
							'month' 	=> array( 
												'current' 	=> $this->getMonthArgs($month),
												'monthpart' => $this->getMonthPartArgs($mdays, $day, 2)
												),
							'week' 		=> array( 
												'current' 	=> $this->getWeekArgs($yw),
												'monthweek' => $this->getMonthPartArgs($mdays, $day, 4)
												),
							'day' 		=> array( 
												'current' 	=> $this->getDayArgs($yd),
												'daymonth' 	=> $day,
												'dayweek' 	=> ($yd%$yw)
												)
							);

			$this->setCalendarParams($args);
		}
	}
	// END

}
// END CLASS
