<?php

// Global variable for table object
$stand = NULL;

//
// Table class for stand
//
class crstand extends crTableBase {
	var $location;
	var $area;
	var $number;
	var $status;
	var $id_stand;
	var $price;
	var $date;
	var $deposit;
	var $instalments;
	var $datestatus;
	var $months_paid;
	var $start_instalment;
	var $vatdate;
	var $vat;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'stand';
		$this->TableName = 'stand';
		$this->TableType = 'TABLE';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// location
		$this->location = new crField('stand', 'stand', 'x_location', 'location', '`location`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['location'] = &$this->location;
		$this->location->DateFilter = "";
		$this->location->SqlSelect = "";
		$this->location->SqlOrderBy = "";

		// area
		$this->area = new crField('stand', 'stand', 'x_area', 'area', '`area`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->fields['area'] = &$this->area;
		$this->area->DateFilter = "";
		$this->area->SqlSelect = "";
		$this->area->SqlOrderBy = "";

		// number
		$this->number = new crField('stand', 'stand', 'x_number', 'number', '`number`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['number'] = &$this->number;
		$this->number->DateFilter = "";
		$this->number->SqlSelect = "";
		$this->number->SqlOrderBy = "";

		// status
		$this->status = new crField('stand', 'stand', 'x_status', 'status', '`status`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['status'] = &$this->status;
		$this->status->DateFilter = "";
		$this->status->SqlSelect = "";
		$this->status->SqlOrderBy = "";

		// id_stand
		$this->id_stand = new crField('stand', 'stand', 'x_id_stand', 'id_stand', '`id_stand`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->id_stand->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['id_stand'] = &$this->id_stand;
		$this->id_stand->DateFilter = "";
		$this->id_stand->SqlSelect = "";
		$this->id_stand->SqlOrderBy = "";

		// price
		$this->price = new crField('stand', 'stand', 'x_price', 'price', '`price`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->fields['price'] = &$this->price;
		$this->price->DateFilter = "";
		$this->price->SqlSelect = "";
		$this->price->SqlOrderBy = "";

		// date
		$this->date = new crField('stand', 'stand', 'x_date', 'date', '`date`', 135, EWR_DATATYPE_DATE, -1);
		$this->fields['date'] = &$this->date;
		$this->date->DateFilter = "";
		$this->date->SqlSelect = "";
		$this->date->SqlOrderBy = "";

		// deposit
		$this->deposit = new crField('stand', 'stand', 'x_deposit', 'deposit', '`deposit`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->fields['deposit'] = &$this->deposit;
		$this->deposit->DateFilter = "";
		$this->deposit->SqlSelect = "";
		$this->deposit->SqlOrderBy = "";

		// instalments
		$this->instalments = new crField('stand', 'stand', 'x_instalments', 'instalments', '`instalments`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['instalments'] = &$this->instalments;
		$this->instalments->DateFilter = "";
		$this->instalments->SqlSelect = "";
		$this->instalments->SqlOrderBy = "";

		// datestatus
		$this->datestatus = new crField('stand', 'stand', 'x_datestatus', 'datestatus', '`datestatus`', 135, EWR_DATATYPE_DATE, -1);
		$this->fields['datestatus'] = &$this->datestatus;
		$this->datestatus->DateFilter = "";
		$this->datestatus->SqlSelect = "";
		$this->datestatus->SqlOrderBy = "";

		// months_paid
		$this->months_paid = new crField('stand', 'stand', 'x_months_paid', 'months_paid', '`months_paid`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['months_paid'] = &$this->months_paid;
		$this->months_paid->DateFilter = "";
		$this->months_paid->SqlSelect = "";
		$this->months_paid->SqlOrderBy = "";

		// start_instalment
		$this->start_instalment = new crField('stand', 'stand', 'x_start_instalment', 'start_instalment', '`start_instalment`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['start_instalment'] = &$this->start_instalment;
		$this->start_instalment->DateFilter = "";
		$this->start_instalment->SqlSelect = "";
		$this->start_instalment->SqlOrderBy = "";

		// vatdate
		$this->vatdate = new crField('stand', 'stand', 'x_vatdate', 'vatdate', '`vatdate`', 135, EWR_DATATYPE_DATE, 5);
		$this->vatdate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['vatdate'] = &$this->vatdate;
		$this->vatdate->DateFilter = "";
		$this->vatdate->SqlSelect = "";
		$this->vatdate->SqlOrderBy = "";

		// vat
		$this->vat = new crField('stand', 'stand', 'x_vat', 'vat', '`vat`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['vat'] = &$this->vat;
		$this->vat->DateFilter = "";
		$this->vat->SqlSelect = "";
		$this->vat->SqlOrderBy = "";
	}

	// Multiple column sort
	function UpdateSort(&$ofld, $ctrl) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
		} else {
			if ($ofld->GroupingFieldId == 0 && !$ctrl) $ofld->setSort("");
		}
	}

	// Get Sort SQL
	function SortSql() {
		$sDtlSortSql = "";
		$argrps = array();
		foreach ($this->fields as $fld) {
			if ($fld->getSort() <> "") {
				if ($fld->GroupingFieldId > 0) {
					if ($fld->FldGroupSql <> "")
						$argrps[$fld->GroupingFieldId] = str_replace("%s", $fld->FldExpression, $fld->FldGroupSql) . " " . $fld->getSort();
					else
						$argrps[$fld->GroupingFieldId] = $fld->FldExpression . " " . $fld->getSort();
				} else {
					if ($sDtlSortSql <> "") $sDtlSortSql .= ", ";
					$sDtlSortSql .= $fld->FldExpression . " " . $fld->getSort();
				}
			}
		}
		$sSortSql = "";
		foreach ($argrps as $grp) {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $grp;
		}
		if ($sDtlSortSql <> "") {
			if ($sSortSql <> "") $sSortSql .= ",";
			$sSortSql .= $sDtlSortSql;
		}
		return $sSortSql;
	}

	// Table level SQL
	// From

	var $_SqlFrom = "";

	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`stand`";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}

	// Select
	var $_SqlSelect = "";

	function getSqlSelect() {
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}

	// Where
	var $_SqlWhere = "";

	function getSqlWhere() {
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}

	// Group By
	var $_SqlGroupBy = "";

	function getSqlGroupBy() {
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}

	// Having
	var $_SqlHaving = "";

	function getSqlHaving() {
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}

	// Order By
	var $_SqlOrderBy = "";

	function getSqlOrderBy() {
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Select Aggregate
	var $_SqlSelectAgg = "";

	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelectAgg() { // For backward compatibility
		return $this->getSqlSelectAgg();
	}

	function setSqlSelectAgg($v) {
		$this->_SqlSelectAgg = $v;
	}

	// Aggregate Prefix
	var $_SqlAggPfx = "";

	function getSqlAggPfx() {
		return ($this->_SqlAggPfx <> "") ? $this->_SqlAggPfx : "";
	}

	function SqlAggPfx() { // For backward compatibility
		return $this->getSqlAggPfx();
	}

	function setSqlAggPfx($v) {
		$this->_SqlAggPfx = $v;
	}

	// Aggregate Suffix
	var $_SqlAggSfx = "";

	function getSqlAggSfx() {
		return ($this->_SqlAggSfx <> "") ? $this->_SqlAggSfx : "";
	}

	function SqlAggSfx() { // For backward compatibility
		return $this->getSqlAggSfx();
	}

	function setSqlAggSfx($v) {
		$this->_SqlAggSfx = $v;
	}

	// Select Count
	var $_SqlSelectCount = "";

	function getSqlSelectCount() {
		return ($this->_SqlSelectCount <> "") ? $this->_SqlSelectCount : "SELECT COUNT(*) FROM " . $this->getSqlFrom();
	}

	function SqlSelectCount() { // For backward compatibility
		return $this->getSqlSelectCount();
	}

	function setSqlSelectCount($v) {
		$this->_SqlSelectCount = $v;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {

			//$sUrlParm = "order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort();
			$sUrlParm = "order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort();
			return ewr_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Table level events
	// Page Selecting event
	function Page_Selecting(&$filter) {

		// Enter your code here	
	}

	// Page Breaking event
	function Page_Breaking(&$break, &$content) {

		// Example:
		//$break = FALSE; // Skip page break, or
		//$content = "<div style=\"page-break-after:always;\">&nbsp;</div>"; // Modify page break content

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Cell Rendered event
	function Cell_Rendered(&$Field, $CurrentValue, &$ViewValue, &$ViewAttrs, &$CellAttrs, &$HrefValue, &$LinkAttrs) {

		//$ViewValue = "xxx";
		//$ViewAttrs["style"] = "xxx";

	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}

	// Load Filters event
	function Page_FilterLoad() {

		// Enter your code here
		// Example: Register/Unregister Custom Extended Filter
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A', 'GetStartsWithAFilter'); // With function, or
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A'); // No function, use Page_Filtering event
		//ewr_UnregisterFilter($this-><Field>, 'StartsWithA');

	}

	// Page Filter Validated event
	function Page_FilterValidated() {

		// Example:
		//$this->MyField1->SearchValue = "your search criteria"; // Search value

	}

	// Page Filtering event
	function Page_Filtering(&$fld, &$filter, $typ, $opr = "", $val = "", $cond = "", $opr2 = "", $val2 = "") {

		// Note: ALWAYS CHECK THE FILTER TYPE ($typ)! Example:
		// if ($typ == "dropdown" && $fld->FldName == "MyField") // Dropdown filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "extended" && $fld->FldName == "MyField") // Extended filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "popup" && $fld->FldName == "MyField") // Popup filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "custom" && $opr == "..." && $fld->FldName == "MyField") // Custom filter, $opr is the custom filter ID
		//     $filter = "..."; // Modify the filter

	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}
}
?>
