<?php

namespace app;

use Config;

class MessageUtils {
	public static function getMessageFromCode($msgCode) {
		return Config::get('constants.MESSAGES.'.Config::get('constants.MSG_CODES.'.$msgCode));
	}
}