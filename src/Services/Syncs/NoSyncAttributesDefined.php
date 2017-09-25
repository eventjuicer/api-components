<?php

namespace Eventjuicer\Services\Syncs;


class NoSyncAttributesDefined extends \Exception {
	

		function __construct($className = "")
		{
			parent::__construct($className);
		}

}