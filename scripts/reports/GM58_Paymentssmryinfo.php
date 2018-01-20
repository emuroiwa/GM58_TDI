<?php

// Global variable for table object
$GM58_Payments = NULL;

//
// Table class for GM58 Payments
//
class crGM58_Payments extends crTableBase {
	var $Payments_28Entered29_Bar_Graph;
	var $Payments_28entered29_Pie_Graph;
	var $number;
	var $cash;
	var $payment_type;
	var $month;
	var $payment_date;
	var $months_paid;
	var $capturer;
	var $datestatus;
	var $value_date;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'GM58_Payments';
		$this->TableName = 'GM58 Payments';
		$this->TableType = 'REPORT';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// number
		$this->number = new crField('GM58_Payments', 'GM58 Payments', 'x_number', 'number', '`number`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['number'] = &$this->number;
		$this->number->DateFilter = "";
		$this->number->SqlSelect = "SELECT DISTINCT `number` FROM " . $this->getSqlFrom();
		$this->number->SqlOrderBy = "`number`";

		// cash
		$this->cash = new crField('GM58_Payments', 'GM58 Payments', 'x_cash', 'cash', '`cash`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->cash->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['cash'] = &$this->cash;
		$this->cash->DateFilter = "";
		$this->cash->SqlSelect = "SELECT DISTINCT `cash` FROM " . $this->getSqlFrom();
		$this->cash->SqlOrderBy = "`cash`";
		$this->cash->FldDelimiter = $GLOBALS["EWR_CSV_DELIMITER"];

		// payment_type
		$this->payment_type = new crField('GM58_Payments', 'GM58 Payments', 'x_payment_type', 'payment_type', '`payment_type`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['payment_type'] = &$this->payment_type;
		$this->payment_type->DateFilter = "";
		$this->payment_type->SqlSelect = "SELECT DISTINCT `payment_type` FROM " . $this->getSqlFrom();
		$this->payment_type->SqlOrderBy = "`payment_type`";

		// month
		$this->month = new crField('GM58_Payments', 'GM58 Payments', 'x_month', 'month', '`month`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['month'] = &$this->month;
		$this->month->DateFilter = "";
		$this->month->SqlSelect = "SELECT DISTINCT `month` FROM " . $this->getSqlFrom();
		$this->month->SqlOrderBy = "`month`";

		// payment_date
		$this->payment_date = new crField('GM58_Payments', 'GM58 Payments', 'x_payment_date', 'payment_date', '`payment_date`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['payment_date'] = &$this->payment_date;
		$this->payment_date->DateFilter = "";
		$this->payment_date->SqlSelect = "SELECT DISTINCT `payment_date` FROM " . $this->getSqlFrom();
		$this->payment_date->SqlOrderBy = "`payment_date`";

		// months_paid
		$this->months_paid = new crField('GM58_Payments', 'GM58 Payments', 'x_months_paid', 'months_paid', '`months_paid`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['months_paid'] = &$this->months_paid;
		$this->months_paid->DateFilter = "";
		$this->months_paid->SqlSelect = "SELECT DISTINCT `months_paid` FROM " . $this->getSqlFrom();
		$this->months_paid->SqlOrderBy = "`months_paid`";

		// capturer
		$this->capturer = new crField('GM58_Payments', 'GM58 Payments', 'x_capturer', 'capturer', '`capturer`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['capturer'] = &$this->capturer;
		$this->capturer->DateFilter = "";
		$this->capturer->SqlSelect = "SELECT DISTINCT `capturer` FROM " . $this->getSqlFrom();
		$this->capturer->SqlOrderBy = "`capturer`";

		// datestatus
		$this->datestatus = new crField('GM58_Payments', 'GM58 Payments', 'x_datestatus', 'datestatus', '`datestatus`', 135, EWR_DATATYPE_DATE, 1);
		$this->fields['datestatus'] = &$this->datestatus;
		$this->datestatus->DateFilter = "";
		$this->datestatus->SqlSelect = "SELECT DISTINCT `datestatus` FROM " . $this->getSqlFrom();
		$this->datestatus->SqlOrderBy = "`datestatus`";
		ewr_RegisterFilter($this->datestatus, "@@LastMonth", $ReportLanguage->Phrase("LastMonth"), "ewr_IsLastMonth");
		ewr_RegisterFilter($this->datestatus, "@@ThisMonth", $ReportLanguage->Phrase("ThisMonth"), "ewr_IsThisMonth");
		ewr_RegisterFilter($this->datestatus, "@@NextMonth", $ReportLanguage->Phrase("NextMonth"), "ewr_IsNextMonth");

		// value_date
		$this->value_date = new crField('GM58_Payments', 'GM58 Payments', 'x_value_date', 'value_date', '`value_date`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['value_date'] = &$this->value_date;
		$this->value_date->DateFilter = "";
		$this->value_date->SqlSelect = "";
		$this->value_date->SqlOrderBy = "";

		// Payments (Entered) Bar Graph
		$this->Payments_28Entered29_Bar_Graph = new crChart($this->DBID, 'GM58_Payments', 'GM58 Payments', 'Payments_28Entered29_Bar_Graph', 'Payments (Entered) Bar Graph', 'month', 'payment_date', '', 104, 'COUNT', 550, 440);
		$this->Payments_28Entered29_Bar_Graph->SqlSelect = "SELECT `month`, '', COUNT(`payment_date`) FROM ";
		$this->Payments_28Entered29_Bar_Graph->SqlGroupBy = "`month`";
		$this->Payments_28Entered29_Bar_Graph->SqlOrderBy = "";
		$this->Payments_28Entered29_Bar_Graph->SeriesDateType = "";

		// Payments (entered) Pie Graph
		$this->Payments_28entered29_Pie_Graph = new crChart($this->DBID, 'GM58_Payments', 'GM58 Payments', 'Payments_28entered29_Pie_Graph', 'Payments (entered) Pie Graph', 'month', 'payment_date', '', 6, 'COUNT', 550, 440);
		$this->Payments_28entered29_Pie_Graph->SqlSelect = "SELECT `month`, '', COUNT(`payment_date`) FROM ";
		$this->Payments_28entered29_Pie_Graph->SqlGroupBy = "`month`";
		$this->Payments_28entered29_Pie_Graph->SqlOrderBy = "";
		$this->Payments_28entered29_Pie_Graph->SeriesDateType = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`payments`";
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

	// Table Level Group SQL
	// First Group Field

	var $_SqlFirstGroupField = "";

	function getSqlFirstGroupField() {
		return ($this->_SqlFirstGroupField <> "") ? $this->_SqlFirstGroupField : "";
	}

	function SqlFirstGroupField() { // For backward compatibility
		return $this->getSqlFirstGroupField();
	}

	function setSqlFirstGroupField($v) {
		$this->_SqlFirstGroupField = $v;
	}

	// Select Group
	var $_SqlSelectGroup = "";

	function getSqlSelectGroup() {
		return ($this->_SqlSelectGroup <> "") ? $this->_SqlSelectGroup : "SELECT DISTINCT " . $this->getSqlFirstGroupField() . " FROM " . $this->getSqlFrom();
	}

	function SqlSelectGroup() { // For backward compatibility
		return $this->getSqlSelectGroup();
	}

	function setSqlSelectGroup($v) {
		$this->_SqlSelectGroup = $v;
	}

	// Order By Group
	var $_SqlOrderByGroup = "";

	function getSqlOrderByGroup() {
		return ($this->_SqlOrderByGroup <> "") ? $this->_SqlOrderByGroup : "";
	}

	function SqlOrderByGroup() { // For backward compatibility
		return $this->getSqlOrderByGroup();
	}

	function setSqlOrderByGroup($v) {
		$this->_SqlOrderByGroup = $v;
	}

	// Select Aggregate
	var $_SqlSelectAgg = "";

	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT SUM(`cash`) AS `sum_cash` FROM " . $this->getSqlFrom();
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
