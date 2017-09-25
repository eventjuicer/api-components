<?php

namespace Eventjuicer\Services\Syncs;


class NoSyncTableException extends \Exception {
	

		function __construct($className = "")
		{
			parent::__construct($className);
		}

}