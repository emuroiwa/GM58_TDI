<?php

// Global variable for table object
$Last_Payment_Made = NULL;

//
// Table class for Last Payment Made
//
class crLast_Payment_Made extends crTableBase {
	var $ShowGroupHeaderAsRow = FALSE;
	var $ShowCompactSummaryFooter = TRUE;
	var $Bar_Graph;
	var $Pie_Chart;
	var $NAME;
	var $surname;
	var $standnumber;
	var $balance;
	var $instalments;
	var $paymentnumbermonth;
	var $paymentmonth;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage, $gsLanguage;
		$this->TableVar = 'Last_Payment_Made';
		$this->TableName = 'Last Payment Made';
		$this->TableType = 'REPORT';
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0;

		// NAME
		$this->NAME = new crField('Last_Payment_Made', 'Last Payment Made', 'x_NAME', 'NAME', '`NAME`', 200, EWR_DATATYPE_STRING, -1);
		$this->NAME->Sortable = TRUE; // Allow sort
		$this->fields['NAME'] = &$this->NAME;
		$this->NAME->DateFilter = "";
		$this->NAME->SqlSelect = "SELECT DISTINCT `NAME`, `NAME` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->NAME->SqlOrderBy = "`NAME`";

		// surname
		$this->surname = new crField('Last_Payment_Made', 'Last Payment Made', 'x_surname', 'surname', '`surname`', 200, EWR_DATATYPE_STRING, -1);
		$this->surname->Sortable = TRUE; // Allow sort
		$this->fields['surname'] = &$this->surname;
		$this->surname->DateFilter = "";
		$this->surname->SqlSelect = "SELECT DISTINCT `surname`, `surname` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->surname->SqlOrderBy = "`surname`";

		// standnumber
		$this->standnumber = new crField('Last_Payment_Made', 'Last Payment Made', 'x_standnumber', 'standnumber', '`standnumber`', 200, EWR_DATATYPE_STRING, -1);
		$this->standnumber->Sortable = TRUE; // Allow sort
		$this->fields['standnumber'] = &$this->standnumber;
		$this->standnumber->DateFilter = "";
		$this->standnumber->SqlSelect = "SELECT DISTINCT `standnumber`, `standnumber` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->standnumber->SqlOrderBy = "`standnumber`";

		// balance
		$this->balance = new crField('Last_Payment_Made', 'Last Payment Made', 'x_balance', 'balance', '`balance`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->balance->Sortable = TRUE; // Allow sort
		$this->balance->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['balance'] = &$this->balance;
		$this->balance->DateFilter = "";
		$this->balance->SqlSelect = "SELECT DISTINCT `balance`, `balance` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->balance->SqlOrderBy = "`balance`";

		// instalments
		$this->instalments = new crField('Last_Payment_Made', 'Last Payment Made', 'x_instalments', 'instalments', '`instalments`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->instalments->Sortable = TRUE; // Allow sort
		$this->instalments->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['instalments'] = &$this->instalments;
		$this->instalments->DateFilter = "";
		$this->instalments->SqlSelect = "SELECT DISTINCT `instalments`, `instalments` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->instalments->SqlOrderBy = "`instalments`";

		// paymentnumbermonth
		$this->paymentnumbermonth = new crField('Last_Payment_Made', 'Last Payment Made', 'x_paymentnumbermonth', 'paymentnumbermonth', '`paymentnumbermonth`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->paymentnumbermonth->Sortable = TRUE; // Allow sort
		$this->paymentnumbermonth->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['paymentnumbermonth'] = &$this->paymentnumbermonth;
		$this->paymentnumbermonth->DateFilter = "";
		$this->paymentnumbermonth->SqlSelect = "SELECT DISTINCT `paymentnumbermonth`, `paymentnumbermonth` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->paymentnumbermonth->SqlOrderBy = "`paymentnumbermonth`";

		// paymentmonth
		$this->paymentmonth = new crField('Last_Payment_Made', 'Last Payment Made', 'x_paymentmonth', 'paymentmonth', '`paymentmonth`', 200, EWR_DATATYPE_STRING, -1);
		$this->paymentmonth->Sortable = TRUE; // Allow sort
		$this->fields['paymentmonth'] = &$this->paymentmonth;
		$this->paymentmonth->DateFilter = "";
		$this->paymentmonth->SqlSelect = "SELECT DISTINCT `paymentmonth`, `paymentmonth` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->paymentmonth->SqlOrderBy = "`paymentmonth`";

		// Bar Graph
		$this->Bar_Graph = new crChart($this->DBID, 'Last_Payment_Made', 'Last Payment Made', 'Bar_Graph', 'Bar Graph', 'paymentmonth', 'standnumber', '', 104, 'COUNT', 550, 440);
		$this->Bar_Graph->SqlSelect = "SELECT `paymentmonth`, '', COUNT(`standnumber`) FROM ";
		$this->Bar_Graph->SqlGroupBy = "`paymentmonth`";
		$this->Bar_Graph->SqlOrderBy = "";
		$this->Bar_Graph->SeriesDateType = "";

		// Pie Chart
		$this->Pie_Chart = new crChart($this->DBID, 'Last_Payment_Made', 'Last Payment Made', 'Pie_Chart', 'Pie Chart', 'paymentmonth', 'standnumber', '', 6, 'COUNT', 550, 440);
		$this->Pie_Chart->SqlSelect = "SELECT `paymentmonth`, '', COUNT(`standnumber`) FROM ";
		$this->Pie_Chart->SqlGroupBy = "`paymentmonth`";
		$this->Pie_Chart->SqlOrderBy = "";
		$this->Pie_Chart->SeriesDateType = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`lastpayments`";
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
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT SUM(`balance`) AS `sum_balance`, MIN(NULLIF(`balance`,0)) AS `min_balance`, MAX(NULLIF(`balance`,0)) AS `max_balance`, COUNT(NULLIF(`balance`,0)) AS `cnt_balance` FROM " . $this->getSqlFrom();
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
