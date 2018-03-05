<?php

class ModelExtensionPaymentNetpluspayment extends Model {




	function getStatesData() {

		$query = "SELECT * FROM " . DB_PREFIX . "zone WHERE country_id=(SELECT country_id FROM " . DB_PREFIX . "country WHERE iso_code_2='NG' )" ;
		$result = $this->db->query($query);
		return $result->rows;
	}

}

?>