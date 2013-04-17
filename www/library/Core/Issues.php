<?php 

class Core_Issues {

	const TOPIC_BUGS 				= 0;
	const TOPIC_FEATURES 			= 1;
	const TOPIC_INTERFACE 			= 2;
	const TOPIC_EXPERIENCE 			= 3;

	const TOPIC_BUGS_STRING 		= 'Bugs';
	const TOPIC_FEATURES_STRING 	= 'Features';
	const TOPIC_INTERFACE_STRING 	= 'Interface';
	const TOPIC_EXPERIENCE_STRING 	= 'Experience';

	var $topic 	= array(
									self::TOPIC_BUGS 		=> self::TOPIC_BUGS_STRING,
									self::TOPIC_FEATURES 	=> self::TOPIC_FEATURES_STRING,
									self::TOPIC_INTERFACE 	=> self::TOPIC_INTERFACE_STRING,
									self::TOPIC_EXPERIENCE 	=> self::TOPIC_EXPERIENCE_STRING
								);

	const STATUS_NEW 				= 0;
	const STATUS_ACTIVE 			= 1;
	const STATUS_RESOLVED 			= 2;
	const STATUS_UN_RESOLVED 		= 3;

	const STATUS_NEW_STRING 		= 'New';
	const STATUS_ACTIVE_STRING 		= 'Active';
	const STATUS_RESOLVED_STRING 	= 'Resolved';
	const STATUS_UN_RESOLVED_STRING = 'Un-Resolved';

	public $status = array(
									self::STATUS_NEW 			=> self::STATUS_NEW_STRING,
									self::STATUS_ACTIVE 		=> self::STATUS_ACTIVE_STRING,
									self::STATUS_RESOLVED 		=> self::STATUS_RESOLVED_STRING,
									self::STATUS_UN_RESOLVED 	=> self::STATUS_UN_RESOLVED_STRING
								);
	/**
	 *	Get Status To String
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function getStatusToString($status) {
		$args = array(
						self::STATUS_NEW 			=> self::STATUS_NEW_STRING,
						self::STATUS_ACTIVE 		=> self::STATUS_ACTIVE_STRING,
						self::STATUS_RESOLVED 		=> self::STATUS_RESOLVED_STRING,
						self::STATUS_UN_RESOLVED 	=> self::STATUS_UN_RESOLVED_STRING
					);
		return $args[$status];
	}
	// END

	/**
	 *	Get Status Options
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function getStatusOptions() {
		return array(
						self::STATUS_NEW 			=> self::STATUS_NEW_STRING,
						self::STATUS_ACTIVE 		=> self::STATUS_ACTIVE_STRING,
						self::STATUS_RESOLVED 		=> self::STATUS_RESOLVED_STRING,
						self::STATUS_UN_RESOLVED 	=> self::STATUS_UN_RESOLVED_STRING
					);
	}
	// END

	/**
	 *	Get Topics To String
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function getTopicToString($topic) {
		$args = array(
						self::TOPIC_BUGS 		=> self::TOPIC_BUGS_STRING,
						self::TOPIC_FEATURES 	=> self::TOPIC_FEATURES_STRING,
						self::TOPIC_INTERFACE 	=> self::TOPIC_INTERFACE_STRING,
						self::TOPIC_EXPERIENCE 	=> self::TOPIC_EXPERIENCE_STRING
					);
		return $args[$topic];
	}
	// END

	/**
	 *	Get Status Options
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function getTopicOptions() {
		return array(
						self::TOPIC_BUGS 		=> self::TOPIC_BUGS_STRING,
						self::TOPIC_FEATURES 	=> self::TOPIC_FEATURES_STRING,
						self::TOPIC_INTERFACE 	=> self::TOPIC_INTERFACE_STRING,
						self::TOPIC_EXPERIENCE 	=> self::TOPIC_EXPERIENCE_STRING
					);
	}
	// END

}
// END CLASS

