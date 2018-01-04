<?php

namespace app;

use Config;

class PaginationUtils {
	public static function getDefaultPageSize() {
		return Config::get('constants.PAGINATION.DEFAULT_PAGE_SIZE');
	}
}