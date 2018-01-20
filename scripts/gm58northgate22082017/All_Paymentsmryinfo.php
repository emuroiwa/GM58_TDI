<?php

// Global variable for table object
$All_Payment = NULL;

//
// Table class for All Payment
//
class crAll_Payment extends crTableBase {
	var $ShowGroupHeaderAsRow = FALSE;
	var $ShowCompactSummaryFooter = TRUE;
	var $All_Payments_Bar;
	var $All_Payment_Pie;
	var $surname;
	var $name;
	var $standnumber;
	var $paymentdate;
	var $paymentmonth;
	var $paymentmonthdate;
	var $paymentnumbermonth;
	var $paymentdetails;
	var $runningbalance;
	var $balance;
	var $paymentamount;
	var $paymentowner;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage, $gsLanguage;
		$this->TableVar = 'All_Payment';
		$this->TableName = 'All Payment';
		$this->TableType = 'REPORT';
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0;

		// surname
		$this->surname = new crField('All_Payment', 'All Payment', 'x_surname', 'surname', '`surname`', 200, EWR_DATATYPE_STRING, -1);
		$this->surname->Sortable = TRUE; // Allow sort
		$this->fields['surname'] = &$this->surname;
		$this->surname->DateFilter = "";
		$this->surname->SqlSelect = "SELECT DISTINCT `surname`, `surname` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->surname->SqlOrderBy = "`surname`";

		// name
		$this->name = new crField('All_Payment', 'All Payment', 'x_name', 'name', '`name`', 200, EWR_DATATYPE_STRING, -1);
		$this->name->Sortable = TRUE; // Allow sort
		$this->fields['name'] = &$this->name;
		$this->name->DateFilter = "";
		$this->name->SqlSelect = "SELECT DISTINCT `name`, `name` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->name->SqlOrderBy = "`name`";

		// standnumber
		$this->standnumber = new crField('All_Payment', 'All Payment', 'x_standnumber', 'standnumber', '`standnumber`', 200, EWR_DATATYPE_STRING, -1);
		$this->standnumber->Sortable = TRUE; // Allow sort
		$this->fields['standnumber'] = &$this->standnumber;
		$this->standnumber->DateFilter = "";
		$this->standnumber->SqlSelect = "SELECT DISTINCT `standnumber`, `standnumber` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->standnumber->SqlOrderBy = "`standnumber`";

		// paymentdate
		$this->paymentdate = new crField('All_Payment', 'All Payment', 'x_paymentdate', 'paymentdate', '`paymentdate`', 135, EWR_DATATYPE_DATE, 5);
		$this->paymentdate->Sortable = TRUE; // Allow sort
		$this->paymentdate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EWR_DATE_SEPARATOR"], $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['paymentdate'] = &$this->paymentdate;
		$this->paymentdate->DateFilter = "";
		$this->paymentdate->SqlSelect = "SELECT DISTINCT `paymentdate`, `paymentdate` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->paymentdate->SqlOrderBy = "`paymentdate`";

		// paymentmonth
		$this->paymentmonth = new crField('All_Payment', 'All Payment', 'x_paymentmonth', 'paymentmonth', '`paymentmonth`', 200, EWR_DATATYPE_STRING, -1);
		$this->paymentmonth->Sortable = TRUE; // Allow sort
		$this->fields['paymentmonth'] = &$this->paymentmonth;
		$this->paymentmonth->DateFilter = "";
		$this->paymentmonth->SqlSelect = "SELECT DISTINCT `paymentmonth`, `paymentmonth` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->paymentmonth->SqlOrderBy = "`paymentmonth`";

		// paymentmonthdate
		$this->paymentmonthdate = new crField('All_Payment', 'All Payment', 'x_paymentmonthdate', 'paymentmonthdate', '`paymentmonthdate`', 135, EWR_DATATYPE_DATE, 5);
		$this->paymentmonthdate->Sortable = TRUE; // Allow sort
		$this->paymentmonthdate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EWR_DATE_SEPARATOR"], $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['paymentmonthdate'] = &$this->paymentmonthdate;
		$this->paymentmonthdate->DateFilter = "";
		$this->paymentmonthdate->SqlSelect = "SELECT DISTINCT `paymentmonthdate`, `paymentmonthdate` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->paymentmonthdate->SqlOrderBy = "`paymentmonthdate`";

		// paymentnumbermonth
		$this->paymentnumbermonth = new crField('All_Payment', 'All Payment', 'x_paymentnumbermonth', 'paymentnumbermonth', '`paymentnumbermonth`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->paymentnumbermonth->Sortable = TRUE; // Allow sort
		$this->paymentnumbermonth->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['paymentnumbermonth'] = &$this->paymentnumbermonth;
		$this->paymentnumbermonth->DateFilter = "";
		$this->paymentnumbermonth->SqlSelect = "SELECT DISTINCT `paymentnumbermonth`, `paymentnumbermonth` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->paymentnumbermonth->SqlOrderBy = "`paymentnumbermonth`";

		// paymentdetails
		$this->paymentdetails = new crField('All_Payment', 'All Payment', 'x_paymentdetails', 'paymentdetails', '`paymentdetails`', 200, EWR_DATATYPE_STRING, -1);
		$this->paymentdetails->Sortable = TRUE; // Allow sort
		$this->fields['paymentdetails'] = &$this->paymentdetails;
		$this->paymentdetails->DateFilter = "";
		$this->paymentdetails->SqlSelect = "SELECT DISTINCT `paymentdetails`, `paymentdetails` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->paymentdetails->SqlOrderBy = "`paymentdetails`";

		// runningbalance
		$this->runningbalance = new crField('All_Payment', 'All Payment', 'x_runningbalance', 'runningbalance', '`runningbalance`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->runningbalance->Sortable = TRUE; // Allow sort
		$this->runningbalance->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['runningbalance'] = &$this->runningbalance;
		$this->runningbalance->DateFilter = "";
		$this->runningbalance->SqlSelect = "SELECT DISTINCT `runningbalance`, `runningbalance` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->runningbalance->SqlOrderBy = "`runningbalance`";

		// balance
		$this->balance = new crField('All_Payment', 'All Payment', 'x_balance', 'balance', '`balance`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->balance->Sortable = TRUE; // Allow sort
		$this->balance->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['balance'] = &$this->balance;
		$this->balance->DateFilter = "";
		$this->balance->SqlSelect = "SELECT DISTINCT `balance`, `balance` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->balance->SqlOrderBy = "`balance`";

		// paymentamount
		$this->paymentamount = new crField('All_Payment', 'All Payment', 'x_paymentamount', 'paymentamount', '`paymentamount`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->paymentamount->Sortable = TRUE; // Allow sort
		$this->paymentamount->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['paymentamount'] = &$this->paymentamount;
		$this->paymentamount->DateFilter = "";
		$this->paymentamount->SqlSelect = "SELECT DISTINCT `paymentamount`, `paymentamount` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->paymentamount->SqlOrderBy = "`paymentamount`";

		// paymentowner
		$this->paymentowner = new crField('All_Payment', 'All Payment', 'x_paymentowner', 'paymentowner', '`paymentowner`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->paymentowner->Sortable = FALSE; // Allow sort
		$this->paymentowner->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['paymentowner'] = &$this->paymentowner;
		$this->paymentowner->DateFilter = "";
		$this->paymentowner->SqlSelect = "";
		$this->paymentowner->SqlOrderBy = "";

		// All Payments Bar
		$this->All_Payments_Bar = new crChart($this->DBID, 'All_Payment', 'All Payment', 'All_Payments_Bar', 'All Payments Bar', 'paymentmonth', 'standnumber', '', 104, 'COUNT', 550, 440);
		$this->All_Payments_Bar->SqlSelect = "SELECT `paymentmonth`, '', COUNT(`standnumber`) FROM ";
		$this->All_Payments_Bar->SqlGroupBy = "`paymentmonth`";
		$this->All_Payments_Bar->SqlOrderBy = "";
		$this->All_Payments_Bar->SeriesDateType = "";

		// All Payment Pie
		$this->All_Payment_Pie = new crChart($this->DBID, 'All_Payment', 'All Payment', 'All_Payment_Pie', 'All Payment Pie', 'paymentmonth', 'standnumber', '', 6, 'COUNT', 550, 440);
		$this->All_Payment_Pie->SqlSelect = "SELECT `paymentmonth`, '', COUNT(`standnumber`) FROM ";
		$this->All_Payment_Pie->SqlGroupBy = "`paymentmonth`";
		$this->All_Payment_Pie->SqlOrderBy = "";
		$this->All_Payment_Pie->SeriesDateType = "";
	}

	// Set Field Visibility
	function SetFieldVisibility($fldparm) {
		global $Security;
		return $this->$fldparm->Visible; // Returns original value
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			if ($ofld->GroupingFieldId == 0)
				$this->setDetailOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			if ($ofld->GroupingFieldId == 0) $ofld->setSort("");
		}
	}

	// Get Sort SQL
	function SortSql() {
		$sDtlSortSql = $this->getDetailOrderBy(); // Get ORDER BY for detail fields from session
		$argrps = array();
		foreach ($this->fields as $fld) {
			if ($fld->getSort() <> "") {
				$fldsql = $fld->FldExpression;
				if ($fld->GroupingFieldId > 0) {
					if ($fld->FldGroupSql <> "")
						$argrps[$fld->GroupingFieldId] = str_replace("%s", $fldsql, $fld->FldGroupSql) . " " . $fld->getSort();
					else
						$argrps[$fld->GroupingFieldId] = $fldsql . " " . $fld->getSort();
				}
			}
		}
		$sSortSql = "";
		foreach ($argrps as $grp) {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $grp;
		}
		if ($sDtlSortSql <> "") {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $sDtlSortSql;
		}
		return $sSortSql;
	}

	// Table level SQL
	// From

	var $_SqlFrom = "";

	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`allpayments`";
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
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT SUM(`paymentamount`) AS `sum_paymentamount`, MIN(`paymentamount`) AS `min_paymentamount`, MAX(`paymentamount`) AS `max_paymentamount`, COUNT(*) AS `cnt_paymentamount` FROM " . $this->getSqlFrom();
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
		return "";
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld) {
		global $gsLanguage;
		switch ($fld->FldVar) {
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld) {
		global $gsLanguage;
		switch ($fld->FldVar) {
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
		//if ($typ == "dropdown" && $fld->FldName == "MyField") // Dropdown filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "extended" && $fld->FldName == "MyField") // Extended filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "popup" && $fld->FldName == "MyField") // Popup filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "custom" && $opr == "..." && $fld->FldName == "MyField") // Custom filter, $opr is the custom filter ID
		//	$filter = "..."; // Modify the filter

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
