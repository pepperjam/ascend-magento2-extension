<?php
namespace Pepperjam\Network\Cron\Feed\OrderCorrection;

use \Pepperjam\Network\Cron\Feed\OrderCorrection\Itemized;
use \Pepperjam\Network\Helper\Map\OrderCorrection as Map;

class Dynamic extends Itemized {
	protected function _getFeedFields() {
		return [
			'PROGRAM_ID' => Map::FIELD_PROGRAM_ID,
			'ORDER_ID' => Map::FIELD_ORDER_ID,
			'ITEM_ID' => Map::FIELD_ITEM_ID,
			'ITEM_PRICE' => Map::FIELD_ITEM_PRICE,
			'QUANTITY' => Map::FIELD_ITEM_QUANTITY,
			'CATEGORY' => Map::FIELD_CATEGORY,
			'NEW_TO_FILE' => Map::FIELD_NEW_TO_FILE,
		];
	}
}
