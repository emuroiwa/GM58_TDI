<?php

// Global variable for table object
$System_Logs = NULL;

//
// Table class for System Logs
//
class crSystem_Logs extends crTableBase {
	var $details;
	var $detailsdate;
	var $user;
	var $id_logs;
	var $id;
	var $name;
	var $surname;
	var $sex;
	var $_email;
	var $account;
	var $address;
	var $department;
	var $username;
	var $password;
	var $idnumber;
	var $status;
	var $date;
	var $access;
	var $suspend;
	var $logtype;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'System_Logs';
		$this->TableName = 'System Logs';
		$this->TableType = 'REPORT';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// details
		$this->details = new crField('System_Logs', 'System Logs', 'x_details', 'details', '`details`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['details'] = &$this->details;
		$this->details->DateFilter = "";
		$this->details->SqlSelect = "SELECT DISTINCT `details` FROM " . $this->getSqlFrom();
		$this->details->SqlOrderBy = "`details`";

		// detailsdate
		$this->detailsdate = new crField('System_Logs', 'System Logs', 'x_detailsdate', 'detailsdate', '`detailsdate`', 135, EWR_DATATYPE_DATE, 5);
		$this->fields['detailsdate'] = &$this->detailsdate;
		$this->detailsdate->DateFilter = "";
		$this->detailsdate->SqlSelect = "";
		$this->detailsdate->SqlOrderBy = "";
		ewr_RegisterFilter($this->detailsdate, "@@LastMonth", $ReportLanguage->Phrase("LastMonth"), "ewr_IsLastMonth");
		ewr_RegisterFilter($this->detailsdate, "@@ThisMonth", $ReportLanguage->Phrase("ThisMonth"), "ewr_IsThisMonth");
		ewr_RegisterFilter($this->detailsdate, "@@NextMonth", $ReportLanguage->Phrase("NextMonth"), "ewr_IsNextMonth");

		// user
		$this->user = new crField('System_Logs', 'System Logs', 'x_user', 'user', '`user`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['user'] = &$this->user;
		$this->user->DateFilter = "";
		$this->user->SqlSelect = "SELECT DISTINCT `user` FROM " . $this->getSqlFrom();
		$this->user->SqlOrderBy = "`user`";

		// id_logs
		$this->id_logs = new crField('System_Logs', 'System Logs', 'x_id_logs', 'id_logs', '`id_logs`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->id_logs->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['id_logs'] = &$this->id_logs;
		$this->id_logs->DateFilter = "";
		$this->id_logs->SqlSelect = "";
		$this->id_logs->SqlOrderBy = "";

		// id
		$this->id = new crField('System_Logs', 'System Logs', 'x_id', 'id', '`id`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['id'] = &$this->id;
		$this->id->DateFilter = "";
		$this->id->SqlSelect = "";
		$this->id->SqlOrderBy = "";

		// name
		$this->name = new crField('System_Logs', 'System Logs', 'x_name', 'name', '`name`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['name'] = &$this->name;
		$this->name->DateFilter = "";
		$this->name->SqlSelect = "SELECT DISTINCT `name` FROM " . $this->getSqlFrom();
		$this->name->SqlOrderBy = "`name`";

		// surname
		$this->surname = new crField('System_Logs', 'System Logs', 'x_surname', 'surname', '`surname`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['surname'] = &$this->surname;
		$this->surname->DateFilter = "";
		$this->surname->SqlSelect = "SELECT DISTINCT `surname` FROM " . $this->getSqlFrom();
		$this->surname->SqlOrderBy = "`surname`";

		// sex
		$this->sex = new crField('System_Logs', 'System Logs', 'x_sex', 'sex', '`sex`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['sex'] = &$this->sex;
		$this->sex->DateFilter = "";
		$this->sex->SqlSelect = "";
		$this->sex->SqlOrderBy = "";

		// email
		$this->_email = new crField('System_Logs', 'System Logs', 'x__email', 'email', '`email`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['_email'] = &$this->_email;
		$this->_email->DateFilter = "";
		$this->_email->SqlSelect = "";
		$this->_email->SqlOrderBy = "";

		// account
		$this->account = new crField('System_Logs', 'System Logs', 'x_account', 'account', '`account`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['account'] = &$this->account;
		$this->account->DateFilter = "";
		$this->account->SqlSelect = "";
		$this->account->SqlOrderBy = "";

		// address
		$this->address = new crField('System_Logs', 'System Logs', 'x_address', 'address', '`address`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['address'] = &$this->address;
		$this->address->DateFilter = "";
		$this->address->SqlSelect = "";
		$this->address->SqlOrderBy = "";

		// department
		$this->department = new crField('System_Logs', 'System Logs', 'x_department', 'department', '`department`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['department'] = &$this->department;
		$this->department->DateFilter = "";
		$this->department->SqlSelect = "";
		$this->department->SqlOrderBy = "";

		// username
		$this->username = new crField('System_Logs', 'System Logs', 'x_username', 'username', '`username`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['username'] = &$this->username;
		$this->username->DateFilter = "";
		$this->username->SqlSelect = "";
		$this->username->SqlOrderBy = "";

		// password
		$this->password = new crField('System_Logs', 'System Logs', 'x_password', 'password', '`password`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['password'] = &$this->password;
		$this->password->DateFilter = "";
		$this->password->SqlSelect = "";
		$this->password->SqlOrderBy = "";

		// idnumber
		$this->idnumber = new crField('System_Logs', 'System Logs', 'x_idnumber', 'idnumber', '`idnumber`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['idnumber'] = &$this->idnumber;
		$this->idnumber->DateFilter = "";
		$this->idnumber->SqlSelect = "";
		$this->idnumber->SqlOrderBy = "";

		// status
		$this->status = new crField('System_Logs', 'System Logs', 'x_status', 'status', '`status`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['status'] = &$this->status;
		$this->status->DateFilter = "";
		$this->status->SqlSelect = "";
		$this->status->SqlOrderBy = "";

		// date
		$this->date = new crField('System_Logs', 'System Logs', 'x_date', 'date', '`date`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['date'] = &$this->date;
		$this->date->DateFilter = "";
		$this->date->SqlSelect = "";
		$this->date->SqlOrderBy = "";

		// access
		$this->access = new crField('System_Logs', 'System Logs', 'x_access', 'access', '`access`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['access'] = &$this->access;
		$this->access->DateFilter = "";
		$this->access->SqlSelect = "";
		$this->access->SqlOrderBy = "";

		// suspend
		$this->suspend = new crField('System_Logs', 'System Logs', 'x_suspend', 'suspend', '`suspend`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['suspend'] = &$this->suspend;
		$this->suspend->DateFilter = "";
		$this->suspend->SqlSelect = "";
		$this->suspend->SqlOrderBy = "";

		// logtype
		$this->logtype = new crField('System_Logs', 'System Logs', 'x_logtype', 'logtype', '`logtype`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['logtype'] = &$this->logtype;
		$this->logtype->DateFilter = "";
		$this->logtype->SqlSelect = "";
		$this->logtype->SqlOrderBy = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`logsview`";
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
