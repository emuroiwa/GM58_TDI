<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "Amendments_Reportsmryinfo.php" ?>
<?php

//
// Page class
//

$Amendments_Report_summary = NULL; // Initialize page object first

class crAmendments_Report_summary extends crAmendments_Report {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{3080AF49-5443-4264-8421-3510B6183D7C}";

	// Page object name
	var $PageObjName = 'Amendments_Report_summary';

	// Page name
	function PageName() {
		return ewr_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ewr_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Export URLs
	var $ExportPrintUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportPdfUrl;
	var $ReportTableClass;
	var $ReportTableStyle = "";

	// Custom export
	var $ExportPrintCustom = FALSE;
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EWR_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EWR_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EWR_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EWR_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_WARNING_MESSAGE], $v);
	}

		// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EWR_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EWR_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EWR_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EWR_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog ewDisplayTable\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") // Header exists, display
			echo $sHeader;
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") // Fotoer exists, display
			echo $sFooter;
	}

	// Validate page request
	function IsPageRequest() {
		if ($this->UseTokenInUrl) {
			if (ewr_IsHttpPost())
				return ($this->TableVar == @$_POST("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == @$_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EWR_CHECK_TOKEN;
	var $CheckTokenFn = "ewr_CheckToken";
	var $CreateTokenFn = "ewr_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ewr_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EWR_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EWR_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $ReportLanguage;

		// Language object
		$ReportLanguage = new crLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (Amendments_Report)
		if (!isset($GLOBALS["Amendments_Report"])) {
			$GLOBALS["Amendments_Report"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Amendments_Report"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", 'summary', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", 'Amendments Report', TRUE);

		// Start timer
		$GLOBALS["gsTimer"] = new crTimer();

		// Open connection
		if (!isset($conn)) $conn = ewr_Connect($this->DBID);

		// Export options
		$this->ExportOptions = new crListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Search options
		$this->SearchOptions = new crListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Filter options
		$this->FilterOptions = new crListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fAmendments_Reportsummary";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $gsEmailContentType, $ReportLanguage, $Security;
		global $gsCustomExport;

		// Get export parameters
		if (@$_GET["export"] <> "")
			$this->Export = strtolower($_GET["export"]);
		elseif (@$_POST["export"] <> "")
			$this->Export = strtolower($_POST["export"]);
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$gsEmailContentType = @$_POST["contenttype"]; // Get email content type

		// Setup placeholder
		$this->number->PlaceHolder = $this->number->FldCaption();
		$this->cash->PlaceHolder = $this->cash->FldCaption();
		$this->date->PlaceHolder = $this->date->FldCaption();
		$this->capturer->PlaceHolder = $this->capturer->FldCaption();
		$this->authority->PlaceHolder = $this->authority->FldCaption();
		$this->a_type->PlaceHolder = $this->a_type->FldCaption();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $ReportLanguage->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	// Set up export options
	function SetupExportOptions() {
		global $ReportLanguage;
		$exportid = session_id();

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = TRUE;
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = TRUE;
		// Export to Email

		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_Amendments_Report\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_Amendments_Report',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fAmendments_Reportsummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = TRUE;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fAmendments_Reportsummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fAmendments_Reportsummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton; // v8
		$this->FilterOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up options (extended)
		$this->SetupExportOptionsExt();

		// Hide options for export
		if ($this->Export <> "") {
			$this->ExportOptions->HideAllOptions();
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $ReportLanguage, $EWR_EXPORT, $gsExportFile;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EWR_EXPORT)) {
			$sContent = ob_get_contents();

			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				ob_end_clean();
				echo $this->$fn($sContent);
				ewr_CloseConn(); // Close connection
				exit();
			} else {
				$this->$fn($sContent);
			}
		}

		 // Close connection
		ewr_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EWR_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Initialize common variables
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Paging variables
	var $RecIndex = 0; // Record index
	var $RecCount = 0; // Record count
	var $StartGrp = 0; // Start group
	var $StopGrp = 0; // Stop group
	var $TotalGrps = 0; // Total groups
	var $GrpCount = 0; // Group count
	var $GrpCounter = array(); // Group counter
	var $DisplayGrps = 10; // Groups per page
	var $GrpRange = 10;
	var $Sort = "";
	var $Filter = "";
	var $PageFirstGroupFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;
	var $DrillDownList = "";

	// Clear field for ext filter
	var $ClearExtFilter = "";
	var $PopupName = "";
	var $PopupValue = "";
	var $FilterApplied;
	var $SearchCommand = FALSE;
	var $ShowHeader;
	var $GrpFldCount = 0;
	var $SubGrpFldCount = 0;
	var $DtlFldCount = 0;
	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;
	var $GrpIdx;

	//
	// Page main
	//
	function Page_Main() {
		global $rs;
		global $rsgrp;
		global $Security;
		global $gsFormError;
		global $gbDrillDownInPanel;
		global $ReportBreadcrumb;
		global $ReportLanguage;

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields

		$nDtls = 8;
		$nGrps = 1;
		$this->Val = &ewr_InitArray($nDtls, 0);
		$this->Cnt = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Smry = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Mn = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->Mx = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->GrandCnt = &ewr_InitArray($nDtls, 0);
		$this->GrandSmry = &ewr_InitArray($nDtls, 0);
		$this->GrandMn = &ewr_InitArray($nDtls, NULL);
		$this->GrandMx = &ewr_InitArray($nDtls, NULL);

		// Set up array if accumulation required: array(Accum, SkipNullOrZero)
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->number->SelectionList = "";
		$this->number->DefaultSelectionList = "";
		$this->number->ValueList = "";
		$this->cash->SelectionList = "";
		$this->cash->DefaultSelectionList = "";
		$this->cash->ValueList = "";
		$this->date->SelectionList = "";
		$this->date->DefaultSelectionList = "";
		$this->date->ValueList = "";
		$this->capturer->SelectionList = "";
		$this->capturer->DefaultSelectionList = "";
		$this->capturer->ValueList = "";
		$this->authority->SelectionList = "";
		$this->authority->DefaultSelectionList = "";
		$this->authority->ValueList = "";
		$this->a_type->SelectionList = "";
		$this->a_type->DefaultSelectionList = "";
		$this->a_type->ValueList = "";

		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();

		// Load custom filters
		$this->Page_FilterLoad();

		// Set up popup filter
		$this->SetupPopup();

		// Load group db values if necessary
		$this->LoadGroupDbValues();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Extended filter
		$sExtendedFilter = "";

		// Restore filter list
		$this->RestoreFilterList();

		// Build extended filter
		$sExtendedFilter = $this->GetExtendedFilter();
		ewr_AddFilter($this->Filter, $sExtendedFilter);

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();

		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);
		$this->SearchOptions->GetItem("resetfilter")->Visible = $this->FilterApplied;

		// Get sort
		$this->Sort = $this->GetSort();

		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);
		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowHeader = TRUE;

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
		    $this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup(); 

		// Set no record found message
		if ($this->TotalGrps == 0) {
				if ($this->Filter == "0=101") {
					$this->setWarningMessage($ReportLanguage->Phrase("EnterSearchCriteria"));
				} else {
					$this->setWarningMessage($ReportLanguage->Phrase("NoRecord"));
				}
		}

		// Hide export options if export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();

		// Hide search/filter options if export/drilldown
		if ($this->Export <> "" || $this->DrillDown) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Get current page records
		$rs = $this->GetRs($sSql, $this->StartGrp, $this->DisplayGrps);
		$this->SetupFieldCount();
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				if ($this->Col[$iy][0]) { // Accumulate required
					$valwrk = $this->Val[$iy];
					if (is_null($valwrk)) {
						if (!$this->Col[$iy][1])
							$this->Cnt[$ix][$iy]++;
					} else {
						$accum = (!$this->Col[$iy][1] || !is_numeric($valwrk) || $valwrk <> 0);
						if ($accum) {
							$this->Cnt[$ix][$iy]++;
							if (is_numeric($valwrk)) {
								$this->Smry[$ix][$iy] += $valwrk;
								if (is_null($this->Mn[$ix][$iy])) {
									$this->Mn[$ix][$iy] = $valwrk;
									$this->Mx[$ix][$iy] = $valwrk;
								} else {
									if ($this->Mn[$ix][$iy] > $valwrk) $this->Mn[$ix][$iy] = $valwrk;
									if ($this->Mx[$ix][$iy] < $valwrk) $this->Mx[$ix][$iy] = $valwrk;
								}
							}
						}
					}
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0]++;
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {

		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				if ($this->Col[$iy][0]) {
					$this->Smry[$ix][$iy] = 0;
					$this->Mn[$ix][$iy] = NULL;
					$this->Mx[$ix][$iy] = NULL;
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0] = 0;
		}

		// Reset record count
		$this->RecCount = 0;
	}

	// Accummulate grand summary
	function AccumulateGrandSummary() {
		$this->TotCount++;
		$cntgs = count($this->GrandSmry);
		for ($iy = 1; $iy < $cntgs; $iy++) {
			if ($this->Col[$iy][0]) {
				$valwrk = $this->Val[$iy];
				if (is_null($valwrk) || !is_numeric($valwrk)) {
					if (!$this->Col[$iy][1])
						$this->GrandCnt[$iy]++;
				} else {
					if (!$this->Col[$iy][1] || $valwrk <> 0) {
						$this->GrandCnt[$iy]++;
						$this->GrandSmry[$iy] += $valwrk;
						if (is_null($this->GrandMn[$iy])) {
							$this->GrandMn[$iy] = $valwrk;
							$this->GrandMx[$iy] = $valwrk;
						} else {
							if ($this->GrandMn[$iy] > $valwrk) $this->GrandMn[$iy] = $valwrk;
							if ($this->GrandMx[$iy] < $valwrk) $this->GrandMx[$iy] = $valwrk;
						}
					}
				}
			}
		}
	}

	// Get count
	function GetCnt($sql) {
		$conn = &$this->Connection();
		$rscnt = $conn->Execute($sql);
		$cnt = ($rscnt) ? $rscnt->RecordCount() : 0;
		if ($rscnt) $rscnt->Close();
		return $cnt;
	}

	// Get recordset
	function GetRs($wrksql, $start, $grps) {
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row

	//		$rs->MoveFirst(); // NOTE: no need to move position
				$this->FirstRowData = array();
				$this->FirstRowData['id_stand'] = ewr_Conv($rs->fields('id_stand'),3);
				$this->FirstRowData['number'] = ewr_Conv($rs->fields('number'),200);
				$this->FirstRowData['cash'] = ewr_Conv($rs->fields('cash'),200);
				$this->FirstRowData['date'] = ewr_Conv($rs->fields('date'),135);
				$this->FirstRowData['capturer'] = ewr_Conv($rs->fields('capturer'),200);
				$this->FirstRowData['authority'] = ewr_Conv($rs->fields('authority'),200);
				$this->FirstRowData['a_type'] = ewr_Conv($rs->fields('a_type'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->id_stand->setDbValue($rs->fields('id_stand'));
			$this->number->setDbValue($rs->fields('number'));
			$this->cash->setDbValue($rs->fields('cash'));
			$this->date->setDbValue($rs->fields('date'));
			$this->capturer->setDbValue($rs->fields('capturer'));
			$this->authority->setDbValue($rs->fields('authority'));
			$this->a_type->setDbValue($rs->fields('a_type'));
			$this->Val[1] = $this->id_stand->CurrentValue;
			$this->Val[2] = $this->number->CurrentValue;
			$this->Val[3] = $this->cash->CurrentValue;
			$this->Val[4] = $this->date->CurrentValue;
			$this->Val[5] = $this->capturer->CurrentValue;
			$this->Val[6] = $this->authority->CurrentValue;
			$this->Val[7] = $this->a_type->CurrentValue;
		} else {
			$this->id_stand->setDbValue("");
			$this->number->setDbValue("");
			$this->cash->setDbValue("");
			$this->date->setDbValue("");
			$this->capturer->setDbValue("");
			$this->authority->setDbValue("");
			$this->a_type->setDbValue("");
		}
	}

	//  Set up starting group
	function SetUpStartGroup() {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;

		// Check for a 'start' parameter
		if (@$_GET[EWR_TABLE_START_GROUP] != "") {
			$this->StartGrp = $_GET[EWR_TABLE_START_GROUP];
			$this->setStartGroup($this->StartGrp);
		} elseif (@$_GET["pageno"] != "") {
			$nPageNo = $_GET["pageno"];
			if (is_numeric($nPageNo)) {
				$this->StartGrp = ($nPageNo-1)*$this->DisplayGrps+1;
				if ($this->StartGrp <= 0) {
					$this->StartGrp = 1;
				} elseif ($this->StartGrp >= intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1) {
					$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1;
				}
				$this->setStartGroup($this->StartGrp);
			} else {
				$this->StartGrp = $this->getStartGroup();
			}
		} else {
			$this->StartGrp = $this->getStartGroup();
		}

		// Check if correct start group counter
		if (!is_numeric($this->StartGrp) || $this->StartGrp == "") { // Avoid invalid start group counter
			$this->StartGrp = 1; // Reset start group counter
			$this->setStartGroup($this->StartGrp);
		} elseif (intval($this->StartGrp) > intval($this->TotalGrps)) { // Avoid starting group > total groups
			$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to last page first group
			$this->setStartGroup($this->StartGrp);
		} elseif (($this->StartGrp-1) % $this->DisplayGrps <> 0) {
			$this->StartGrp = intval(($this->StartGrp-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to page boundary
			$this->setStartGroup($this->StartGrp);
		}
	}

	// Load group db values if necessary
	function LoadGroupDbValues() {
		$conn = &$this->Connection();
	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		$fld = NULL;
		if (@$_GET["popup"] <> "") {
			$popupname = $_GET["popup"];

			// Check popup name
			// Build distinct values for number

			if ($popupname == 'Amendments_Report_number') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->number, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->number->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->number->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->number->setDbValue($rswrk->fields[0]);
					if (is_null($this->number->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->number->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->number->ViewValue = $this->number->CurrentValue;
						ewr_SetupDistinctValues($this->number->ValueList, $this->number->CurrentValue, $this->number->ViewValue, FALSE, $this->number->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->number->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->number->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->number;
			}

			// Build distinct values for cash
			if ($popupname == 'Amendments_Report_cash') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->cash, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->cash->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->cash->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->cash->setDbValue($rswrk->fields[0]);
					if (is_null($this->cash->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->cash->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->cash->ViewValue = $this->cash->CurrentValue;
						ewr_SetupDistinctValues($this->cash->ValueList, $this->cash->CurrentValue, $this->cash->ViewValue, FALSE, $this->cash->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->cash->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->cash->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->cash;
			}

			// Build distinct values for date
			if ($popupname == 'Amendments_Report_date') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->date, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->date->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->date->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->date->setDbValue($rswrk->fields[0]);
					if (is_null($this->date->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->date->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->date->ViewValue = $this->date->CurrentValue;
						ewr_SetupDistinctValues($this->date->ValueList, $this->date->CurrentValue, $this->date->ViewValue, FALSE, $this->date->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->date->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->date->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->date;
			}

			// Build distinct values for capturer
			if ($popupname == 'Amendments_Report_capturer') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->capturer, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->capturer->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->capturer->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->capturer->setDbValue($rswrk->fields[0]);
					if (is_null($this->capturer->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->capturer->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->capturer->ViewValue = $this->capturer->CurrentValue;
						ewr_SetupDistinctValues($this->capturer->ValueList, $this->capturer->CurrentValue, $this->capturer->ViewValue, FALSE, $this->capturer->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->capturer->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->capturer->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->capturer;
			}

			// Build distinct values for authority
			if ($popupname == 'Amendments_Report_authority') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->authority, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->authority->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->authority->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->authority->setDbValue($rswrk->fields[0]);
					if (is_null($this->authority->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->authority->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->authority->ViewValue = $this->authority->CurrentValue;
						ewr_SetupDistinctValues($this->authority->ValueList, $this->authority->CurrentValue, $this->authority->ViewValue, FALSE, $this->authority->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->authority->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->authority->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->authority;
			}

			// Build distinct values for a_type
			if ($popupname == 'Amendments_Report_a_type') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->a_type, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->a_type->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->a_type->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->a_type->setDbValue($rswrk->fields[0]);
					if (is_null($this->a_type->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->a_type->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->a_type->ViewValue = $this->a_type->CurrentValue;
						ewr_SetupDistinctValues($this->a_type->ValueList, $this->a_type->CurrentValue, $this->a_type->ViewValue, FALSE, $this->a_type->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->a_type->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->a_type->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->a_type;
			}

			// Output data as Json
			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				ob_end_clean();
				echo $jsdb;
				exit();
			}
		}
	}

	// Set up popup
	function SetupPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->DrillDown)
			return;

		// Process post back form
		if (ewr_IsHttpPost()) {
			$sName = @$_POST["popup"]; // Get popup form name
			if ($sName <> "") {
				$cntValues = (is_array(@$_POST["sel_$sName"])) ? count($_POST["sel_$sName"]) : 0;
				if ($cntValues > 0) {
					$arValues = ewr_StripSlashes($_POST["sel_$sName"]);
					if (trim($arValues[0]) == "") // Select all
						$arValues = EWR_INIT_VALUE;
					$this->PopupName = $sName;
					if (ewr_IsAdvancedFilterValue($arValues) || $arValues == EWR_INIT_VALUE)
						$this->PopupValue = $arValues;
					if (!ewr_MatchedArray($arValues, $_SESSION["sel_$sName"])) {
						if ($this->HasSessionFilterValues($sName))
							$this->ClearExtFilter = $sName; // Clear extended filter for this field
					}
					$_SESSION["sel_$sName"] = $arValues;
					$_SESSION["rf_$sName"] = ewr_StripSlashes(@$_POST["rf_$sName"]);
					$_SESSION["rt_$sName"] = ewr_StripSlashes(@$_POST["rt_$sName"]);
					$this->ResetPager();
				}
			}

		// Get 'reset' command
		} elseif (@$_GET["cmd"] <> "") {
			$sCmd = $_GET["cmd"];
			if (strtolower($sCmd) == "reset") {
				$this->ClearSessionSelection('number');
				$this->ClearSessionSelection('cash');
				$this->ClearSessionSelection('date');
				$this->ClearSessionSelection('capturer');
				$this->ClearSessionSelection('authority');
				$this->ClearSessionSelection('a_type');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get number selected values

		if (is_array(@$_SESSION["sel_Amendments_Report_number"])) {
			$this->LoadSelectionFromSession('number');
		} elseif (@$_SESSION["sel_Amendments_Report_number"] == EWR_INIT_VALUE) { // Select all
			$this->number->SelectionList = "";
		}

		// Get cash selected values
		if (is_array(@$_SESSION["sel_Amendments_Report_cash"])) {
			$this->LoadSelectionFromSession('cash');
		} elseif (@$_SESSION["sel_Amendments_Report_cash"] == EWR_INIT_VALUE) { // Select all
			$this->cash->SelectionList = "";
		}

		// Get date selected values
		if (is_array(@$_SESSION["sel_Amendments_Report_date"])) {
			$this->LoadSelectionFromSession('date');
		} elseif (@$_SESSION["sel_Amendments_Report_date"] == EWR_INIT_VALUE) { // Select all
			$this->date->SelectionList = "";
		}

		// Get capturer selected values
		if (is_array(@$_SESSION["sel_Amendments_Report_capturer"])) {
			$this->LoadSelectionFromSession('capturer');
		} elseif (@$_SESSION["sel_Amendments_Report_capturer"] == EWR_INIT_VALUE) { // Select all
			$this->capturer->SelectionList = "";
		}

		// Get authority selected values
		if (is_array(@$_SESSION["sel_Amendments_Report_authority"])) {
			$this->LoadSelectionFromSession('authority');
		} elseif (@$_SESSION["sel_Amendments_Report_authority"] == EWR_INIT_VALUE) { // Select all
			$this->authority->SelectionList = "";
		}

		// Get a_type selected values
		if (is_array(@$_SESSION["sel_Amendments_Report_a_type"])) {
			$this->LoadSelectionFromSession('a_type');
		} elseif (@$_SESSION["sel_Amendments_Report_a_type"] == EWR_INIT_VALUE) { // Select all
			$this->a_type->SelectionList = "";
		}
	}

	// Reset pager
	function ResetPager() {

		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	// Set up number of groups displayed per page
	function SetUpDisplayGrps() {
		$sWrk = @$_GET[EWR_TABLE_GROUP_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayGrps = intval($sWrk);
			} else {
				if (strtoupper($sWrk) == "ALL") { // Display all groups
					$this->DisplayGrps = -1;
				} else {
					$this->DisplayGrps = 10; // Non-numeric, load default
				}
			}
			$this->setGroupPerPage($this->DisplayGrps); // Save to session

			// Reset start position (reset command)
			$this->StartGrp = 1;
			$this->setStartGroup($this->StartGrp);
		} else {
			if ($this->getGroupPerPage() <> "") {
				$this->DisplayGrps = $this->getGroupPerPage(); // Restore from session
			} else {
				$this->DisplayGrps = 10; // Load default
			}
		}
	}

	// Render row
	function RenderRow() {
		global $rs, $Security, $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND && !$this->GrandSummarySetup) { // Grand total
			$bGotCount = FALSE;
			$bGotSummary = FALSE;

			// Get total count from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectCount(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$rstot = $conn->Execute($sSql);
			if ($rstot) {
				$this->TotCount = ($rstot->RecordCount()>1) ? $rstot->RecordCount() : $rstot->fields[0];
				$rstot->Close();
				$bGotCount = TRUE;
			} else {
				$this->TotCount = 0;
			}
		$bGotSummary = TRUE;

			// Accumulate grand summary from detail records
			if (!$bGotCount || !$bGotSummary) {
				$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
				$rs = $conn->Execute($sSql);
				if ($rs) {
					$this->GetRow(1);
					while (!$rs->EOF) {
						$this->AccumulateGrandSummary();
						$this->GetRow(2);
					}
					$rs->Close();
				}
			}
			$this->GrandSummarySetup = TRUE; // No need to set up again
		}

		// Call Row_Rendering event
		$this->Row_Rendering();

		//
		// Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
			$this->RowAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel; // Set up row class

			// id_stand
			$this->id_stand->HrefValue = "";

			// number
			$this->number->HrefValue = "";

			// cash
			$this->cash->HrefValue = "";

			// date
			$this->date->HrefValue = "";

			// capturer
			$this->capturer->HrefValue = "";

			// authority
			$this->authority->HrefValue = "";

			// a_type
			$this->a_type->HrefValue = "";
		} else {

			// id_stand
			$this->id_stand->ViewValue = $this->id_stand->CurrentValue;
			$this->id_stand->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// number
			$this->number->ViewValue = $this->number->CurrentValue;
			$this->number->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// cash
			$this->cash->ViewValue = $this->cash->CurrentValue;
			$this->cash->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// date
			$this->date->ViewValue = $this->date->CurrentValue;
			$this->date->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// capturer
			$this->capturer->ViewValue = $this->capturer->CurrentValue;
			$this->capturer->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// authority
			$this->authority->ViewValue = $this->authority->CurrentValue;
			$this->authority->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// a_type
			$this->a_type->ViewValue = $this->a_type->CurrentValue;
			$this->a_type->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// id_stand
			$this->id_stand->HrefValue = "";

			// number
			$this->number->HrefValue = "";

			// cash
			$this->cash->HrefValue = "";

			// date
			$this->date->HrefValue = "";

			// capturer
			$this->capturer->HrefValue = "";

			// authority
			$this->authority->HrefValue = "";

			// a_type
			$this->a_type->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// id_stand
			$CurrentValue = $this->id_stand->CurrentValue;
			$ViewValue = &$this->id_stand->ViewValue;
			$ViewAttrs = &$this->id_stand->ViewAttrs;
			$CellAttrs = &$this->id_stand->CellAttrs;
			$HrefValue = &$this->id_stand->HrefValue;
			$LinkAttrs = &$this->id_stand->LinkAttrs;
			$this->Cell_Rendered($this->id_stand, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// number
			$CurrentValue = $this->number->CurrentValue;
			$ViewValue = &$this->number->ViewValue;
			$ViewAttrs = &$this->number->ViewAttrs;
			$CellAttrs = &$this->number->CellAttrs;
			$HrefValue = &$this->number->HrefValue;
			$LinkAttrs = &$this->number->LinkAttrs;
			$this->Cell_Rendered($this->number, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// cash
			$CurrentValue = $this->cash->CurrentValue;
			$ViewValue = &$this->cash->ViewValue;
			$ViewAttrs = &$this->cash->ViewAttrs;
			$CellAttrs = &$this->cash->CellAttrs;
			$HrefValue = &$this->cash->HrefValue;
			$LinkAttrs = &$this->cash->LinkAttrs;
			$this->Cell_Rendered($this->cash, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// date
			$CurrentValue = $this->date->CurrentValue;
			$ViewValue = &$this->date->ViewValue;
			$ViewAttrs = &$this->date->ViewAttrs;
			$CellAttrs = &$this->date->CellAttrs;
			$HrefValue = &$this->date->HrefValue;
			$LinkAttrs = &$this->date->LinkAttrs;
			$this->Cell_Rendered($this->date, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// capturer
			$CurrentValue = $this->capturer->CurrentValue;
			$ViewValue = &$this->capturer->ViewValue;
			$ViewAttrs = &$this->capturer->ViewAttrs;
			$CellAttrs = &$this->capturer->CellAttrs;
			$HrefValue = &$this->capturer->HrefValue;
			$LinkAttrs = &$this->capturer->LinkAttrs;
			$this->Cell_Rendered($this->capturer, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// authority
			$CurrentValue = $this->authority->CurrentValue;
			$ViewValue = &$this->authority->ViewValue;
			$ViewAttrs = &$this->authority->ViewAttrs;
			$CellAttrs = &$this->authority->CellAttrs;
			$HrefValue = &$this->authority->HrefValue;
			$LinkAttrs = &$this->authority->LinkAttrs;
			$this->Cell_Rendered($this->authority, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// a_type
			$CurrentValue = $this->a_type->CurrentValue;
			$ViewValue = &$this->a_type->ViewValue;
			$ViewAttrs = &$this->a_type->ViewAttrs;
			$CellAttrs = &$this->a_type->CellAttrs;
			$HrefValue = &$this->a_type->HrefValue;
			$LinkAttrs = &$this->a_type->LinkAttrs;
			$this->Cell_Rendered($this->a_type, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpFldCount = 0;
		$this->SubGrpFldCount = 0;
		$this->DtlFldCount = 0;
		if ($this->id_stand->Visible) $this->DtlFldCount += 1;
		if ($this->number->Visible) $this->DtlFldCount += 1;
		if ($this->cash->Visible) $this->DtlFldCount += 1;
		if ($this->date->Visible) $this->DtlFldCount += 1;
		if ($this->capturer->Visible) $this->DtlFldCount += 1;
		if ($this->authority->Visible) $this->DtlFldCount += 1;
		if ($this->a_type->Visible) $this->DtlFldCount += 1;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("summary", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	function SetupExportOptionsExt() {
		global $ReportLanguage;
		$item =& $this->ExportOptions->GetItem("pdf");
		$item->Visible = TRUE;
		$exportid = session_id();
		$url = $this->ExportPdfUrl;
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $url . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
	}

	// Return extended filter
	function GetExtendedFilter() {
		global $gsFormError;
		$sFilter = "";
		if ($this->DrillDown)
			return "";
		$bPostBack = ewr_IsHttpPost();
		$bRestoreSession = TRUE;
		$bSetupFilter = FALSE;

		// Reset extended filter if filter changed
		if ($bPostBack) {

			// Clear extended filter for field number
			if ($this->ClearExtFilter == 'Amendments_Report_number')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'number');

			// Clear extended filter for field cash
			if ($this->ClearExtFilter == 'Amendments_Report_cash')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'cash');

			// Clear extended filter for field date
			if ($this->ClearExtFilter == 'Amendments_Report_date')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'date');

			// Clear extended filter for field capturer
			if ($this->ClearExtFilter == 'Amendments_Report_capturer')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'capturer');

			// Clear extended filter for field authority
			if ($this->ClearExtFilter == 'Amendments_Report_authority')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'authority');

			// Clear extended filter for field a_type
			if ($this->ClearExtFilter == 'Amendments_Report_a_type')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'a_type');

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->number->SearchValue, $this->number->SearchOperator, $this->number->SearchCondition, $this->number->SearchValue2, $this->number->SearchOperator2, 'number'); // Field number
			$this->SetSessionFilterValues($this->cash->SearchValue, $this->cash->SearchOperator, $this->cash->SearchCondition, $this->cash->SearchValue2, $this->cash->SearchOperator2, 'cash'); // Field cash
			$this->SetSessionFilterValues($this->date->SearchValue, $this->date->SearchOperator, $this->date->SearchCondition, $this->date->SearchValue2, $this->date->SearchOperator2, 'date'); // Field date
			$this->SetSessionFilterValues($this->capturer->SearchValue, $this->capturer->SearchOperator, $this->capturer->SearchCondition, $this->capturer->SearchValue2, $this->capturer->SearchOperator2, 'capturer'); // Field capturer
			$this->SetSessionFilterValues($this->authority->SearchValue, $this->authority->SearchOperator, $this->authority->SearchCondition, $this->authority->SearchValue2, $this->authority->SearchOperator2, 'authority'); // Field authority
			$this->SetSessionFilterValues($this->a_type->SearchValue, $this->a_type->SearchOperator, $this->a_type->SearchCondition, $this->a_type->SearchValue2, $this->a_type->SearchOperator2, 'a_type'); // Field a_type

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field number
			if ($this->GetFilterValues($this->number)) {
				$bSetupFilter = TRUE;
			}

			// Field cash
			if ($this->GetFilterValues($this->cash)) {
				$bSetupFilter = TRUE;
			}

			// Field date
			if ($this->GetFilterValues($this->date)) {
				$bSetupFilter = TRUE;
			}

			// Field capturer
			if ($this->GetFilterValues($this->capturer)) {
				$bSetupFilter = TRUE;
			}

			// Field authority
			if ($this->GetFilterValues($this->authority)) {
				$bSetupFilter = TRUE;
			}

			// Field a_type
			if ($this->GetFilterValues($this->a_type)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionFilterValues($this->number); // Field number
			$this->GetSessionFilterValues($this->cash); // Field cash
			$this->GetSessionFilterValues($this->date); // Field date
			$this->GetSessionFilterValues($this->capturer); // Field capturer
			$this->GetSessionFilterValues($this->authority); // Field authority
			$this->GetSessionFilterValues($this->a_type); // Field a_type
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->number, $sFilter, FALSE, TRUE); // Field number
		$this->BuildExtendedFilter($this->cash, $sFilter, FALSE, TRUE); // Field cash
		$this->BuildExtendedFilter($this->date, $sFilter, FALSE, TRUE); // Field date
		$this->BuildExtendedFilter($this->capturer, $sFilter, FALSE, TRUE); // Field capturer
		$this->BuildExtendedFilter($this->authority, $sFilter, FALSE, TRUE); // Field authority
		$this->BuildExtendedFilter($this->a_type, $sFilter, FALSE, TRUE); // Field a_type

		// Save parms to session
		$this->SetSessionFilterValues($this->number->SearchValue, $this->number->SearchOperator, $this->number->SearchCondition, $this->number->SearchValue2, $this->number->SearchOperator2, 'number'); // Field number
		$this->SetSessionFilterValues($this->cash->SearchValue, $this->cash->SearchOperator, $this->cash->SearchCondition, $this->cash->SearchValue2, $this->cash->SearchOperator2, 'cash'); // Field cash
		$this->SetSessionFilterValues($this->date->SearchValue, $this->date->SearchOperator, $this->date->SearchCondition, $this->date->SearchValue2, $this->date->SearchOperator2, 'date'); // Field date
		$this->SetSessionFilterValues($this->capturer->SearchValue, $this->capturer->SearchOperator, $this->capturer->SearchCondition, $this->capturer->SearchValue2, $this->capturer->SearchOperator2, 'capturer'); // Field capturer
		$this->SetSessionFilterValues($this->authority->SearchValue, $this->authority->SearchOperator, $this->authority->SearchCondition, $this->authority->SearchValue2, $this->authority->SearchOperator2, 'authority'); // Field authority
		$this->SetSessionFilterValues($this->a_type->SearchValue, $this->a_type->SearchOperator, $this->a_type->SearchCondition, $this->a_type->SearchValue2, $this->a_type->SearchOperator2, 'a_type'); // Field a_type

		// Setup filter
		if ($bSetupFilter) {

			// Field number
			$sWrk = "";
			$this->BuildExtendedFilter($this->number, $sWrk);
			ewr_LoadSelectionFromFilter($this->number, $sWrk, $this->number->SelectionList);
			$_SESSION['sel_Amendments_Report_number'] = ($this->number->SelectionList == "") ? EWR_INIT_VALUE : $this->number->SelectionList;

			// Field cash
			$sWrk = "";
			$this->BuildExtendedFilter($this->cash, $sWrk);
			ewr_LoadSelectionFromFilter($this->cash, $sWrk, $this->cash->SelectionList);
			$_SESSION['sel_Amendments_Report_cash'] = ($this->cash->SelectionList == "") ? EWR_INIT_VALUE : $this->cash->SelectionList;

			// Field date
			$sWrk = "";
			$this->BuildExtendedFilter($this->date, $sWrk);
			ewr_LoadSelectionFromFilter($this->date, $sWrk, $this->date->SelectionList);
			$_SESSION['sel_Amendments_Report_date'] = ($this->date->SelectionList == "") ? EWR_INIT_VALUE : $this->date->SelectionList;

			// Field capturer
			$sWrk = "";
			$this->BuildExtendedFilter($this->capturer, $sWrk);
			ewr_LoadSelectionFromFilter($this->capturer, $sWrk, $this->capturer->SelectionList);
			$_SESSION['sel_Amendments_Report_capturer'] = ($this->capturer->SelectionList == "") ? EWR_INIT_VALUE : $this->capturer->SelectionList;

			// Field authority
			$sWrk = "";
			$this->BuildExtendedFilter($this->authority, $sWrk);
			ewr_LoadSelectionFromFilter($this->authority, $sWrk, $this->authority->SelectionList);
			$_SESSION['sel_Amendments_Report_authority'] = ($this->authority->SelectionList == "") ? EWR_INIT_VALUE : $this->authority->SelectionList;

			// Field a_type
			$sWrk = "";
			$this->BuildExtendedFilter($this->a_type, $sWrk);
			ewr_LoadSelectionFromFilter($this->a_type, $sWrk, $this->a_type->SelectionList);
			$_SESSION['sel_Amendments_Report_a_type'] = ($this->a_type->SelectionList == "") ? EWR_INIT_VALUE : $this->a_type->SelectionList;
		}
		return $sFilter;
	}

	// Build dropdown filter
	function BuildDropDownFilter(&$fld, &$FilterClause, $FldOpr, $Default = FALSE, $SaveFilter = FALSE) {
		$FldVal = ($Default) ? $fld->DefaultDropDownValue : $fld->DropDownValue;
		$sSql = "";
		if (is_array($FldVal)) {
			foreach ($FldVal as $val) {
				$sWrk = $this->GetDropDownFilter($fld, $val, $FldOpr);

				// Call Page Filtering event
				if (substr($val, 0, 2) <> "@@") $this->Page_Filtering($fld, $sWrk, "dropdown", $FldOpr, $val);
				if ($sWrk <> "") {
					if ($sSql <> "")
						$sSql .= " OR " . $sWrk;
					else
						$sSql = $sWrk;
				}
			}
		} else {
			$sSql = $this->GetDropDownFilter($fld, $FldVal, $FldOpr);

			// Call Page Filtering event
			if (substr($FldVal, 0, 2) <> "@@") $this->Page_Filtering($fld, $sSql, "dropdown", $FldOpr, $FldVal);
		}
		if ($sSql <> "") {
			ewr_AddFilter($FilterClause, $sSql);
			if ($SaveFilter) $fld->CurrentFilter = $sSql;
		}
	}

	function GetDropDownFilter(&$fld, $FldVal, $FldOpr) {
		$FldName = $fld->FldName;
		$FldExpression = $fld->FldExpression;
		$FldDataType = $fld->FldDataType;
		$FldDelimiter = $fld->FldDelimiter;
		$FldVal = strval($FldVal);
		if ($FldOpr == "") $FldOpr = "=";
		$sWrk = "";
		if ($FldVal == EWR_NULL_VALUE) {
			$sWrk = $FldExpression . " IS NULL";
		} elseif ($FldVal == EWR_NOT_NULL_VALUE) {
			$sWrk = $FldExpression . " IS NOT NULL";
		} elseif ($FldVal == EWR_EMPTY_VALUE) {
			$sWrk = $FldExpression . " = ''";
		} elseif ($FldVal == EWR_ALL_VALUE) {
			$sWrk = "1 = 1";
		} else {
			if (substr($FldVal, 0, 2) == "@@") {
				$sWrk = $this->GetCustomFilter($fld, $FldVal);
			} elseif ($FldDelimiter <> "" && trim($FldVal) <> "") {
				$sWrk = ewr_GetMultiSearchSql($FldExpression, trim($FldVal), $this->DBID);
			} else {
				if ($FldVal <> "" && $FldVal <> EWR_INIT_VALUE) {
					if ($FldDataType == EWR_DATATYPE_DATE && $FldOpr <> "") {
						$sWrk = ewr_DateFilterString($FldExpression, $FldOpr, $FldVal, $FldDataType, $this->DBID);
					} else {
						$sWrk = ewr_FilterString($FldOpr, $FldVal, $FldDataType, $this->DBID);
						if ($sWrk <> "") $sWrk = $FldExpression . $sWrk;
					}
				}
			}
		}
		return $sWrk;
	}

	// Get custom filter
	function GetCustomFilter(&$fld, $FldVal) {
		$sWrk = "";
		if (is_array($fld->AdvancedFilters)) {
			foreach ($fld->AdvancedFilters as $filter) {
				if ($filter->ID == $FldVal && $filter->Enabled) {
					$sFld = $fld->FldExpression;
					$sFn = $filter->FunctionName;
					$wrkid = (substr($filter->ID,0,2) == "@@") ? substr($filter->ID,2) : $filter->ID;
					if ($sFn <> "")
						$sWrk = $sFn($sFld);
					else
						$sWrk = "";
					$this->Page_Filtering($fld, $sWrk, "custom", $wrkid);
					break;
				}
			}
		}
		return $sWrk;
	}

	// Build extended filter
	function BuildExtendedFilter(&$fld, &$FilterClause, $Default = FALSE, $SaveFilter = FALSE) {
		$sWrk = ewr_GetExtendedFilter($fld, $Default, $this->DBID);
		if (!$Default)
			$this->Page_Filtering($fld, $sWrk, "extended", $fld->SearchOperator, $fld->SearchValue, $fld->SearchCondition, $fld->SearchOperator2, $fld->SearchValue2);
		if ($sWrk <> "") {
			ewr_AddFilter($FilterClause, $sWrk);
			if ($SaveFilter) $fld->CurrentFilter = $sWrk;
		}
	}

	// Get drop down value from querystring
	function GetDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return FALSE; // Skip post back
		if (isset($_GET["so_$parm"]))
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
		if (isset($_GET["sv_$parm"])) {
			$fld->DropDownValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			return TRUE;
		}
		return FALSE;
	}

	// Get filter values from querystring
	function GetFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return; // Skip post back
		$got = FALSE;
		if (isset($_GET["sv_$parm"])) {
			$fld->SearchValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so_$parm"])) {
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sc_$parm"])) {
			$fld->SearchCondition = ewr_StripSlashes(@$_GET["sc_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sv2_$parm"])) {
			$fld->SearchValue2 = ewr_StripSlashes(@$_GET["sv2_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so2_$parm"])) {
			$fld->SearchOperator2 = ewr_StripSlashes($_GET["so2_$parm"]);
			$got = TRUE;
		}
		return $got;
	}

	// Set default ext filter
	function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2) {
		$fld->DefaultSearchValue = $sv1; // Default ext filter value 1
		$fld->DefaultSearchValue2 = $sv2; // Default ext filter value 2 (if operator 2 is enabled)
		$fld->DefaultSearchOperator = $so1; // Default search operator 1
		$fld->DefaultSearchOperator2 = $so2; // Default search operator 2 (if operator 2 is enabled)
		$fld->DefaultSearchCondition = $sc; // Default search condition (if operator 2 is enabled)
	}

	// Apply default ext filter
	function ApplyDefaultExtFilter(&$fld) {
		$fld->SearchValue = $fld->DefaultSearchValue;
		$fld->SearchValue2 = $fld->DefaultSearchValue2;
		$fld->SearchOperator = $fld->DefaultSearchOperator;
		$fld->SearchOperator2 = $fld->DefaultSearchOperator2;
		$fld->SearchCondition = $fld->DefaultSearchCondition;
	}

	// Check if Text Filter applied
	function TextFilterApplied(&$fld) {
		return (strval($fld->SearchValue) <> strval($fld->DefaultSearchValue) ||
			strval($fld->SearchValue2) <> strval($fld->DefaultSearchValue2) ||
			(strval($fld->SearchValue) <> "" &&
				strval($fld->SearchOperator) <> strval($fld->DefaultSearchOperator)) ||
			(strval($fld->SearchValue2) <> "" &&
				strval($fld->SearchOperator2) <> strval($fld->DefaultSearchOperator2)) ||
			strval($fld->SearchCondition) <> strval($fld->DefaultSearchCondition));
	}

	// Check if Non-Text Filter applied
	function NonTextFilterApplied(&$fld) {
		if (is_array($fld->DropDownValue)) {
			if (is_array($fld->DefaultDropDownValue)) {
				if (count($fld->DefaultDropDownValue) <> count($fld->DropDownValue))
					return TRUE;
				else
					return (count(array_diff($fld->DefaultDropDownValue, $fld->DropDownValue)) <> 0);
			} else {
				return TRUE;
			}
		} else {
			if (is_array($fld->DefaultDropDownValue))
				return TRUE;
			else
				$v1 = strval($fld->DefaultDropDownValue);
			if ($v1 == EWR_INIT_VALUE)
				$v1 = "";
			$v2 = strval($fld->DropDownValue);
			if ($v2 == EWR_INIT_VALUE || $v2 == EWR_ALL_VALUE)
				$v2 = "";
			return ($v1 <> $v2);
		}
	}

	// Get dropdown value from session
	function GetSessionDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->DropDownValue, 'sv_Amendments_Report_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Amendments_Report_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_Amendments_Report_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Amendments_Report_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_Amendments_Report_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_Amendments_Report_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_Amendments_Report_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_Amendments_Report_' . $parm] = $sv;
		$_SESSION['so_Amendments_Report_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_Amendments_Report_' . $parm] = $sv1;
		$_SESSION['so_Amendments_Report_' . $parm] = $so1;
		$_SESSION['sc_Amendments_Report_' . $parm] = $sc;
		$_SESSION['sv2_Amendments_Report_' . $parm] = $sv2;
		$_SESSION['so2_Amendments_Report_' . $parm] = $so2;
	}

	// Check if has Session filter values
	function HasSessionFilterValues($parm) {
		return ((@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv2_' . $parm] <> "" && @$_SESSION['sv2_' . $parm] <> EWR_INIT_VALUE));
	}

	// Dropdown filter exist
	function DropDownFilterExist(&$fld, $FldOpr) {
		$sWrk = "";
		$this->BuildDropDownFilter($fld, $sWrk, $FldOpr);
		return ($sWrk <> "");
	}

	// Extended filter exist
	function ExtendedFilterExist(&$fld) {
		$sExtWrk = "";
		$this->BuildExtendedFilter($fld, $sExtWrk);
		return ($sExtWrk <> "");
	}

	// Validate form
	function ValidateForm() {
		global $ReportLanguage, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EWR_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!ewr_CheckNumber($this->cash->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->cash->FldErrMsg();
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			$gsFormError .= ($gsFormError <> "") ? "<p>&nbsp;</p>" : "";
			$gsFormError .= $sFormCustomError;
		}
		return $ValidateForm;
	}

	// Clear selection stored in session
	function ClearSessionSelection($parm) {
		$_SESSION["sel_Amendments_Report_$parm"] = "";
		$_SESSION["rf_Amendments_Report_$parm"] = "";
		$_SESSION["rt_Amendments_Report_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_Amendments_Report_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_Amendments_Report_$parm"];
		$fld->RangeTo = @$_SESSION["rt_Amendments_Report_$parm"];
	}

	// Load default value for filters
	function LoadDefaultFilters() {

		/**
		* Set up default values for non Text filters
		*/

		/**
		* Set up default values for extended filters
		* function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2)
		* Parameters:
		* $fld - Field object
		* $so1 - Default search operator 1
		* $sv1 - Default ext filter value 1
		* $sc - Default search condition (if operator 2 is enabled)
		* $so2 - Default search operator 2 (if operator 2 is enabled)
		* $sv2 - Default ext filter value 2 (if operator 2 is enabled)
		*/

		// Field number
		$this->SetDefaultExtFilter($this->number, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->number);
		$sWrk = "";
		$this->BuildExtendedFilter($this->number, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->number, $sWrk, $this->number->DefaultSelectionList);
		if (!$this->SearchCommand) $this->number->SelectionList = $this->number->DefaultSelectionList;

		// Field cash
		$this->SetDefaultExtFilter($this->cash, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->cash);
		$sWrk = "";
		$this->BuildExtendedFilter($this->cash, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->cash, $sWrk, $this->cash->DefaultSelectionList);
		if (!$this->SearchCommand) $this->cash->SelectionList = $this->cash->DefaultSelectionList;

		// Field date
		$this->SetDefaultExtFilter($this->date, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->date);
		$sWrk = "";
		$this->BuildExtendedFilter($this->date, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->date, $sWrk, $this->date->DefaultSelectionList);
		if (!$this->SearchCommand) $this->date->SelectionList = $this->date->DefaultSelectionList;

		// Field capturer
		$this->SetDefaultExtFilter($this->capturer, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->capturer);
		$sWrk = "";
		$this->BuildExtendedFilter($this->capturer, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->capturer, $sWrk, $this->capturer->DefaultSelectionList);
		if (!$this->SearchCommand) $this->capturer->SelectionList = $this->capturer->DefaultSelectionList;

		// Field authority
		$this->SetDefaultExtFilter($this->authority, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->authority);
		$sWrk = "";
		$this->BuildExtendedFilter($this->authority, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->authority, $sWrk, $this->authority->DefaultSelectionList);
		if (!$this->SearchCommand) $this->authority->SelectionList = $this->authority->DefaultSelectionList;

		// Field a_type
		$this->SetDefaultExtFilter($this->a_type, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->a_type);
		$sWrk = "";
		$this->BuildExtendedFilter($this->a_type, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->a_type, $sWrk, $this->a_type->DefaultSelectionList);
		if (!$this->SearchCommand) $this->a_type->SelectionList = $this->a_type->DefaultSelectionList;

		/**
		* Set up default values for popup filters
		*/

		// Field number
		// $this->number->DefaultSelectionList = array("val1", "val2");
		// Field cash
		// $this->cash->DefaultSelectionList = array("val1", "val2");
		// Field date
		// $this->date->DefaultSelectionList = array("val1", "val2");
		// Field capturer
		// $this->capturer->DefaultSelectionList = array("val1", "val2");
		// Field authority
		// $this->authority->DefaultSelectionList = array("val1", "val2");
		// Field a_type
		// $this->a_type->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check number text filter
		if ($this->TextFilterApplied($this->number))
			return TRUE;

		// Check number popup filter
		if (!ewr_MatchedArray($this->number->DefaultSelectionList, $this->number->SelectionList))
			return TRUE;

		// Check cash text filter
		if ($this->TextFilterApplied($this->cash))
			return TRUE;

		// Check cash popup filter
		if (!ewr_MatchedArray($this->cash->DefaultSelectionList, $this->cash->SelectionList))
			return TRUE;

		// Check date text filter
		if ($this->TextFilterApplied($this->date))
			return TRUE;

		// Check date popup filter
		if (!ewr_MatchedArray($this->date->DefaultSelectionList, $this->date->SelectionList))
			return TRUE;

		// Check capturer text filter
		if ($this->TextFilterApplied($this->capturer))
			return TRUE;

		// Check capturer popup filter
		if (!ewr_MatchedArray($this->capturer->DefaultSelectionList, $this->capturer->SelectionList))
			return TRUE;

		// Check authority text filter
		if ($this->TextFilterApplied($this->authority))
			return TRUE;

		// Check authority popup filter
		if (!ewr_MatchedArray($this->authority->DefaultSelectionList, $this->authority->SelectionList))
			return TRUE;

		// Check a_type text filter
		if ($this->TextFilterApplied($this->a_type))
			return TRUE;

		// Check a_type popup filter
		if (!ewr_MatchedArray($this->a_type->DefaultSelectionList, $this->a_type->SelectionList))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList() {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field number
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->number, $sExtWrk);
		if (is_array($this->number->SelectionList))
			$sWrk = ewr_JoinArray($this->number->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->number->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field cash
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->cash, $sExtWrk);
		if (is_array($this->cash->SelectionList))
			$sWrk = ewr_JoinArray($this->cash->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->cash->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field date
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->date, $sExtWrk);
		if (is_array($this->date->SelectionList))
			$sWrk = ewr_JoinArray($this->date->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->date->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field capturer
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->capturer, $sExtWrk);
		if (is_array($this->capturer->SelectionList))
			$sWrk = ewr_JoinArray($this->capturer->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->capturer->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field authority
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->authority, $sExtWrk);
		if (is_array($this->authority->SelectionList))
			$sWrk = ewr_JoinArray($this->authority->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->authority->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field a_type
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->a_type, $sExtWrk);
		if (is_array($this->a_type->SelectionList))
			$sWrk = ewr_JoinArray($this->a_type->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->a_type->FldCaption() . "</span>" . $sFilter . "</div>";
		$divstyle = "";
		$divdataclass = "";

		// Show Filters
		if ($sFilterList <> "") {
			$sMessage = "<div class=\"ewDisplayTable\"" . $divstyle . "><div id=\"ewrFilterList\" class=\"alert alert-info\"" . $divdataclass . "><div id=\"ewrCurrentFilters\">" . $ReportLanguage->Phrase("CurrentFilters") . "</div>" . $sFilterList . "</div></div>";
			$this->Message_Showing($sMessage, "");
			echo $sMessage;
		}
	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";

		// Field number
		$sWrk = "";
		if ($this->number->SearchValue <> "" || $this->number->SearchValue2 <> "") {
			$sWrk = "\"sv_number\":\"" . ewr_JsEncode2($this->number->SearchValue) . "\"," .
				"\"so_number\":\"" . ewr_JsEncode2($this->number->SearchOperator) . "\"," .
				"\"sc_number\":\"" . ewr_JsEncode2($this->number->SearchCondition) . "\"," .
				"\"sv2_number\":\"" . ewr_JsEncode2($this->number->SearchValue2) . "\"," .
				"\"so2_number\":\"" . ewr_JsEncode2($this->number->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->number->SelectionList <> EWR_INIT_VALUE) ? $this->number->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_number\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field cash
		$sWrk = "";
		if ($this->cash->SearchValue <> "" || $this->cash->SearchValue2 <> "") {
			$sWrk = "\"sv_cash\":\"" . ewr_JsEncode2($this->cash->SearchValue) . "\"," .
				"\"so_cash\":\"" . ewr_JsEncode2($this->cash->SearchOperator) . "\"," .
				"\"sc_cash\":\"" . ewr_JsEncode2($this->cash->SearchCondition) . "\"," .
				"\"sv2_cash\":\"" . ewr_JsEncode2($this->cash->SearchValue2) . "\"," .
				"\"so2_cash\":\"" . ewr_JsEncode2($this->cash->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->cash->SelectionList <> EWR_INIT_VALUE) ? $this->cash->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_cash\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field date
		$sWrk = "";
		if ($this->date->SearchValue <> "" || $this->date->SearchValue2 <> "") {
			$sWrk = "\"sv_date\":\"" . ewr_JsEncode2($this->date->SearchValue) . "\"," .
				"\"so_date\":\"" . ewr_JsEncode2($this->date->SearchOperator) . "\"," .
				"\"sc_date\":\"" . ewr_JsEncode2($this->date->SearchCondition) . "\"," .
				"\"sv2_date\":\"" . ewr_JsEncode2($this->date->SearchValue2) . "\"," .
				"\"so2_date\":\"" . ewr_JsEncode2($this->date->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->date->SelectionList <> EWR_INIT_VALUE) ? $this->date->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_date\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field capturer
		$sWrk = "";
		if ($this->capturer->SearchValue <> "" || $this->capturer->SearchValue2 <> "") {
			$sWrk = "\"sv_capturer\":\"" . ewr_JsEncode2($this->capturer->SearchValue) . "\"," .
				"\"so_capturer\":\"" . ewr_JsEncode2($this->capturer->SearchOperator) . "\"," .
				"\"sc_capturer\":\"" . ewr_JsEncode2($this->capturer->SearchCondition) . "\"," .
				"\"sv2_capturer\":\"" . ewr_JsEncode2($this->capturer->SearchValue2) . "\"," .
				"\"so2_capturer\":\"" . ewr_JsEncode2($this->capturer->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->capturer->SelectionList <> EWR_INIT_VALUE) ? $this->capturer->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_capturer\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field authority
		$sWrk = "";
		if ($this->authority->SearchValue <> "" || $this->authority->SearchValue2 <> "") {
			$sWrk = "\"sv_authority\":\"" . ewr_JsEncode2($this->authority->SearchValue) . "\"," .
				"\"so_authority\":\"" . ewr_JsEncode2($this->authority->SearchOperator) . "\"," .
				"\"sc_authority\":\"" . ewr_JsEncode2($this->authority->SearchCondition) . "\"," .
				"\"sv2_authority\":\"" . ewr_JsEncode2($this->authority->SearchValue2) . "\"," .
				"\"so2_authority\":\"" . ewr_JsEncode2($this->authority->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->authority->SelectionList <> EWR_INIT_VALUE) ? $this->authority->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_authority\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field a_type
		$sWrk = "";
		if ($this->a_type->SearchValue <> "" || $this->a_type->SearchValue2 <> "") {
			$sWrk = "\"sv_a_type\":\"" . ewr_JsEncode2($this->a_type->SearchValue) . "\"," .
				"\"so_a_type\":\"" . ewr_JsEncode2($this->a_type->SearchOperator) . "\"," .
				"\"sc_a_type\":\"" . ewr_JsEncode2($this->a_type->SearchCondition) . "\"," .
				"\"sv2_a_type\":\"" . ewr_JsEncode2($this->a_type->SearchValue2) . "\"," .
				"\"so2_a_type\":\"" . ewr_JsEncode2($this->a_type->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->a_type->SelectionList <> EWR_INIT_VALUE) ? $this->a_type->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_a_type\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Return filter list in json
		if ($sFilterList <> "")
			return "{" . $sFilterList . "}";
		else
			return "null";
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ewr_StripSlashes(@$_POST["filter"]), TRUE);

		// Field number
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_number", $filter) || array_key_exists("so_number", $filter) ||
			array_key_exists("sc_number", $filter) ||
			array_key_exists("sv2_number", $filter) || array_key_exists("so2_number", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_number"], @$filter["so_number"], @$filter["sc_number"], @$filter["sv2_number"], @$filter["so2_number"], "number");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_number", $filter)) {
			$sWrk = $filter["sel_number"];
			$sWrk = explode("||", $sWrk);
			$this->number->SelectionList = $sWrk;
			$_SESSION["sel_Amendments_Report_number"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "number"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "number");
			$this->number->SelectionList = "";
			$_SESSION["sel_Amendments_Report_number"] = "";
		}

		// Field cash
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_cash", $filter) || array_key_exists("so_cash", $filter) ||
			array_key_exists("sc_cash", $filter) ||
			array_key_exists("sv2_cash", $filter) || array_key_exists("so2_cash", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_cash"], @$filter["so_cash"], @$filter["sc_cash"], @$filter["sv2_cash"], @$filter["so2_cash"], "cash");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_cash", $filter)) {
			$sWrk = $filter["sel_cash"];
			$sWrk = explode("||", $sWrk);
			$this->cash->SelectionList = $sWrk;
			$_SESSION["sel_Amendments_Report_cash"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "cash"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "cash");
			$this->cash->SelectionList = "";
			$_SESSION["sel_Amendments_Report_cash"] = "";
		}

		// Field date
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_date", $filter) || array_key_exists("so_date", $filter) ||
			array_key_exists("sc_date", $filter) ||
			array_key_exists("sv2_date", $filter) || array_key_exists("so2_date", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_date"], @$filter["so_date"], @$filter["sc_date"], @$filter["sv2_date"], @$filter["so2_date"], "date");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_date", $filter)) {
			$sWrk = $filter["sel_date"];
			$sWrk = explode("||", $sWrk);
			$this->date->SelectionList = $sWrk;
			$_SESSION["sel_Amendments_Report_date"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "date"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "date");
			$this->date->SelectionList = "";
			$_SESSION["sel_Amendments_Report_date"] = "";
		}

		// Field capturer
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_capturer", $filter) || array_key_exists("so_capturer", $filter) ||
			array_key_exists("sc_capturer", $filter) ||
			array_key_exists("sv2_capturer", $filter) || array_key_exists("so2_capturer", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_capturer"], @$filter["so_capturer"], @$filter["sc_capturer"], @$filter["sv2_capturer"], @$filter["so2_capturer"], "capturer");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_capturer", $filter)) {
			$sWrk = $filter["sel_capturer"];
			$sWrk = explode("||", $sWrk);
			$this->capturer->SelectionList = $sWrk;
			$_SESSION["sel_Amendments_Report_capturer"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "capturer"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "capturer");
			$this->capturer->SelectionList = "";
			$_SESSION["sel_Amendments_Report_capturer"] = "";
		}

		// Field authority
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_authority", $filter) || array_key_exists("so_authority", $filter) ||
			array_key_exists("sc_authority", $filter) ||
			array_key_exists("sv2_authority", $filter) || array_key_exists("so2_authority", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_authority"], @$filter["so_authority"], @$filter["sc_authority"], @$filter["sv2_authority"], @$filter["so2_authority"], "authority");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_authority", $filter)) {
			$sWrk = $filter["sel_authority"];
			$sWrk = explode("||", $sWrk);
			$this->authority->SelectionList = $sWrk;
			$_SESSION["sel_Amendments_Report_authority"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "authority"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "authority");
			$this->authority->SelectionList = "";
			$_SESSION["sel_Amendments_Report_authority"] = "";
		}

		// Field a_type
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_a_type", $filter) || array_key_exists("so_a_type", $filter) ||
			array_key_exists("sc_a_type", $filter) ||
			array_key_exists("sv2_a_type", $filter) || array_key_exists("so2_a_type", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_a_type"], @$filter["so_a_type"], @$filter["sc_a_type"], @$filter["sv2_a_type"], @$filter["so2_a_type"], "a_type");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_a_type", $filter)) {
			$sWrk = $filter["sel_a_type"];
			$sWrk = explode("||", $sWrk);
			$this->a_type->SelectionList = $sWrk;
			$_SESSION["sel_Amendments_Report_a_type"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "a_type"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "a_type");
			$this->a_type->SelectionList = "";
			$_SESSION["sel_Amendments_Report_a_type"] = "";
		}
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		if (!$this->ExtendedFilterExist($this->number)) {
			if (is_array($this->number->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->number, "`number`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->number, $sFilter, "popup");
				$this->number->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->cash)) {
			if (is_array($this->cash->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->cash, "`cash`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->cash, $sFilter, "popup");
				$this->cash->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->date)) {
			if (is_array($this->date->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->date, "`date`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->date, $sFilter, "popup");
				$this->date->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->capturer)) {
			if (is_array($this->capturer->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->capturer, "`capturer`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->capturer, $sFilter, "popup");
				$this->capturer->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->authority)) {
			if (is_array($this->authority->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->authority, "`authority`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->authority, $sFilter, "popup");
				$this->authority->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->a_type)) {
			if (is_array($this->a_type->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->a_type, "`a_type`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->a_type, $sFilter, "popup");
				$this->a_type->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		return $sWrk;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort() {
		if ($this->DrillDown)
			return "";

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for a resetsort command
		if (strlen(@$_GET["cmd"]) > 0) {
			$sCmd = @$_GET["cmd"];
			if ($sCmd == "resetsort") {
				$this->setOrderBy("");
				$this->setStartGroup(1);
				$this->id_stand->setSort("");
				$this->number->setSort("");
				$this->cash->setSort("");
				$this->date->setSort("");
				$this->capturer->setSort("");
				$this->authority->setSort("");
				$this->a_type->setSort("");
			}

		// Check for an Order parameter
		} elseif (@$_GET["order"] <> "") {
			$this->CurrentOrder = ewr_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->id_stand, $bCtrl); // id_stand
			$this->UpdateSort($this->number, $bCtrl); // number
			$this->UpdateSort($this->cash, $bCtrl); // cash
			$this->UpdateSort($this->date, $bCtrl); // date
			$this->UpdateSort($this->capturer, $bCtrl); // capturer
			$this->UpdateSort($this->authority, $bCtrl); // authority
			$this->UpdateSort($this->a_type, $bCtrl); // a_type
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}
		return $this->getOrderBy();
	}

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $ReportLanguage;
		$sContentType = @$_POST["contenttype"];
		$sSender = @$_POST["sender"];
		$sRecipient = @$_POST["recipient"];
		$sCc = @$_POST["cc"];
		$sBcc = @$_POST["bcc"];

		// Subject
		$sSubject = ewr_StripSlashes(@$_POST["subject"]);
		$sEmailSubject = $sSubject;

		// Message
		$sContent = ewr_StripSlashes(@$_POST["message"]);
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "")
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterSenderEmail") . "</p>";
		if (!ewr_CheckEmail($sSender))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperSenderEmail") . "</p>";

		// Check recipient
		if (!ewr_CheckEmailList($sRecipient, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperRecipientEmail") . "</p>";

		// Check cc
		if (!ewr_CheckEmailList($sCc, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperCcEmail") . "</p>";

		// Check bcc
		if (!ewr_CheckEmailList($sBcc, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperBccEmail") . "</p>";

		// Check email sent count
		$emailcount = ewr_LoadEmailCount();
		if (intval($emailcount) >= EWR_MAX_EMAIL_SENT_COUNT)
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("ExceedMaxEmailExport") . "</p>";
		if ($sEmailMessage <> "") {
			if (EWR_REMOVE_XSS) $sEmailMessage = ewr_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		$sAttachmentContent = ewr_CleanEmailContent($EmailContent);
		$sAppPath = ewr_FullUrl();
		$sAppPath = substr($sAppPath, 0, strrpos($sAppPath, "/")+1);
		if (strpos($sAttachmentContent, "<head>") !== FALSE)
			$sAttachmentContent = str_replace("<head>", "<head><base href=\"" . $sAppPath . "\">", $sAttachmentContent); // Add <base href> statement inside the header
		else
			$sAttachmentContent = "<base href=\"" . $sAppPath . "\">" . $sAttachmentContent; // Add <base href> statement as the first statement

		//$sAttachmentFile = $this->TableVar . "_" . Date("YmdHis") . ".html";
		$sAttachmentFile = $this->TableVar . "_" . Date("YmdHis") . "_" . ewr_Random() . ".html";
		if ($sContentType == "url") {
			ewr_SaveFile(EWR_UPLOAD_DEST_PATH, $sAttachmentFile, $sAttachmentContent);
			$sAttachmentFile = EWR_UPLOAD_DEST_PATH . $sAttachmentFile;
			$sUrl = $sAppPath . $sAttachmentFile;
			$sEmailMessage .= $sUrl; // Send URL only
			$sAttachmentFile = "";
			$sAttachmentContent = "";
		} else {
			$sEmailMessage .= $sAttachmentContent;
			$sAttachmentFile = "";
			$sAttachmentContent = "";
		}

		// Send email
		$Email = new crEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Content = $sEmailMessage; // Content
		if ($sAttachmentFile <> "")
			$Email->AddAttachment($sAttachmentFile, $sAttachmentContent);
		if ($sContentType <> "url") {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
		}
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		$Email->Charset = EWR_EMAIL_CHARSET;
		$EventArgs = array();
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();
		ewr_DeleteTmpImages($EmailContent);

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count and write log
			ewr_AddEmailLog($sSender, $sRecipient, $sEmailSubject, $sEmailMessage);

			// Sent email success
			return "<p class=\"text-success\">" . $ReportLanguage->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {

			// Sent email failure
			return "<p class=\"text-error\">" . $Email->SendErrDescription . "</p>";
		}
	}

	// Export to HTML
	function ExportHtml($html) {

		//global $gsExportFile;
		//header('Content-Type: text/html' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		//header('Content-Disposition: attachment; filename=' . $gsExportFile . '.html');
		//echo $html;

	} 

	// Export to WORD
	function ExportWord($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-word' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.doc');
		echo $html;
	}

	// Export to EXCEL
	function ExportExcel($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-excel' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
		echo $html;
	}

	// Export PDF
	function ExportPDF($html) {
		global $gsExportFile;
		include_once "dompdf061/dompdf_config.inc.php";
		@ini_set("memory_limit", EWR_PDF_MEMORY_LIMIT);
		set_time_limit(EWR_PDF_TIME_LIMIT);
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		ob_end_clean();
		$dompdf->set_paper("a4", "portrait");
		$dompdf->render();
		ewr_DeleteTmpImages($html);
		$dompdf->stream($gsExportFile . ".pdf", array("Attachment" => 1)); // 0 to open in browser, 1 to download

//		exit();
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ewr_Header(FALSE) ?>
<?php

// Create page object
if (!isset($Amendments_Report_summary)) $Amendments_Report_summary = new crAmendments_Report_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$Amendments_Report_summary;

// Page init
$Page->Page_Init();

// Page main
$Page->Page_Main();

// Global Page Rendering event (in ewrusrfn*.php)
Page_Rendering();

// Page Rendering event
$Page->Page_Render();
?>
<?php include_once "phprptinc/header.php" ?>
<?php if ($Page->Export == "" || $Page->Export == "print" || $Page->Export == "email" && @$gsEmailContentType == "url") { ?>
<script type="text/javascript">

// Create page object
var Amendments_Report_summary = new ewr_Page("Amendments_Report_summary");

// Page properties
Amendments_Report_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = Amendments_Report_summary.PageID;

// Extend page with Chart_Rendering function
Amendments_Report_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
Amendments_Report_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fAmendments_Reportsummary = new ewr_Form("fAmendments_Reportsummary");

// Validate method
fAmendments_Reportsummary.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	var elm = fobj.sv_cash;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->cash->FldErrMsg()) ?>"))
			return false;
	}

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fAmendments_Reportsummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fAmendments_Reportsummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fAmendments_Reportsummary.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Use Ajax
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Page->Export == "") { ?>
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">
<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<?php } ?>
<!-- top slot -->
<div class="ewToolbar">
<?php if ($Page->Export == "" && (!$Page->DrillDown || !$Page->DrillDownInPanel)) { ?>
<?php if ($ReportBreadcrumb) $ReportBreadcrumb->Render(); ?>
<?php } ?>
<?php
if (!$Page->DrillDownInPanel) {
	$Page->ExportOptions->Render("body");
	$Page->SearchOptions->Render("body");
	$Page->FilterOptions->Render("body");
}
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php echo $ReportLanguage->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $Page->ShowPageHeader(); ?>
<?php $Page->ShowMessage(); ?>
<?php if ($Page->Export == "") { ?>
</div>
<!-- top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<?php } ?>
	<!-- Left slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- left container (end) -->
	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
<?php } ?>
	<!-- center slot -->
<!-- summary report starts -->
<?php if ($Page->Export <> "pdf") { ?>
<div id="report_summary">
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<!-- Search form (begin) -->
<form name="fAmendments_Reportsummary" id="fAmendments_Reportsummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fAmendments_Reportsummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_number" class="ewCell form-group">
	<label for="sv_number" class="ewSearchCaption ewLabel"><?php echo $Page->number->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_number" id="so_number" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->number->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->number->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->number->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->number->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->number->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->number->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->number->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->number->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->number->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->number->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->number->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->number->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->number->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->number->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_number" id="sv_number" name="sv_number" size="30" maxlength="111" placeholder="<?php echo $Page->number->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->number->SearchValue) ?>"<?php echo $Page->number->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_number" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_number" style="display: none">
<?php ewr_PrependClass($Page->number->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_number" id="sv2_number" name="sv2_number" size="30" maxlength="111" placeholder="<?php echo $Page->number->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->number->SearchValue2) ?>"<?php echo $Page->number->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_cash" class="ewCell form-group">
	<label for="sv_cash" class="ewSearchCaption ewLabel"><?php echo $Page->cash->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_cash" id="so_cash" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->cash->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->cash->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->cash->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->cash->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->cash->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->cash->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->cash->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->cash->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->cash->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->cash->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->cash->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->cash->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->cash->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->cash->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_cash" id="sv_cash" name="sv_cash" size="30" placeholder="<?php echo $Page->cash->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->cash->SearchValue) ?>"<?php echo $Page->cash->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_cash" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_cash" style="display: none">
<?php ewr_PrependClass($Page->cash->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_cash" id="sv2_cash" name="sv2_cash" size="30" placeholder="<?php echo $Page->cash->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->cash->SearchValue2) ?>"<?php echo $Page->cash->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_3" class="ewRow">
<div id="c_date" class="ewCell form-group">
	<label for="sv_date" class="ewSearchCaption ewLabel"><?php echo $Page->date->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_date" id="so_date" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->date->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->date->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->date->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->date->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->date->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->date->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->date->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->date->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->date->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->date->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_date" id="sv_date" name="sv_date" size="30" maxlength="11" placeholder="<?php echo $Page->date->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->date->SearchValue) ?>"<?php echo $Page->date->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_date" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_date" style="display: none">
<?php ewr_PrependClass($Page->date->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_date" id="sv2_date" name="sv2_date" size="30" maxlength="11" placeholder="<?php echo $Page->date->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->date->SearchValue2) ?>"<?php echo $Page->date->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_4" class="ewRow">
<div id="c_capturer" class="ewCell form-group">
	<label for="sv_capturer" class="ewSearchCaption ewLabel"><?php echo $Page->capturer->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_capturer" id="so_capturer" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->capturer->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->capturer->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->capturer->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->capturer->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->capturer->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->capturer->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->capturer->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->capturer->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->capturer->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->capturer->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->capturer->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->capturer->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->capturer->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->capturer->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_capturer" id="sv_capturer" name="sv_capturer" size="30" maxlength="255" placeholder="<?php echo $Page->capturer->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->capturer->SearchValue) ?>"<?php echo $Page->capturer->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_capturer" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_capturer" style="display: none">
<?php ewr_PrependClass($Page->capturer->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_capturer" id="sv2_capturer" name="sv2_capturer" size="30" maxlength="255" placeholder="<?php echo $Page->capturer->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->capturer->SearchValue2) ?>"<?php echo $Page->capturer->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_5" class="ewRow">
<div id="c_authority" class="ewCell form-group">
	<label for="sv_authority" class="ewSearchCaption ewLabel"><?php echo $Page->authority->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_authority" id="so_authority" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->authority->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->authority->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->authority->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->authority->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->authority->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->authority->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->authority->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->authority->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->authority->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->authority->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->authority->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->authority->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->authority->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->authority->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_authority" id="sv_authority" name="sv_authority" size="30" maxlength="111" placeholder="<?php echo $Page->authority->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->authority->SearchValue) ?>"<?php echo $Page->authority->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_authority" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_authority" style="display: none">
<?php ewr_PrependClass($Page->authority->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_authority" id="sv2_authority" name="sv2_authority" size="30" maxlength="111" placeholder="<?php echo $Page->authority->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->authority->SearchValue2) ?>"<?php echo $Page->authority->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_6" class="ewRow">
<div id="c_a_type" class="ewCell form-group">
	<label for="sv_a_type" class="ewSearchCaption ewLabel"><?php echo $Page->a_type->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_a_type" id="so_a_type" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->a_type->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->a_type->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->a_type->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->a_type->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->a_type->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->a_type->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->a_type->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->a_type->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->a_type->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->a_type->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->a_type->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->a_type->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->a_type->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->a_type->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_a_type" id="sv_a_type" name="sv_a_type" size="30" maxlength="111" placeholder="<?php echo $Page->a_type->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->a_type->SearchValue) ?>"<?php echo $Page->a_type->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_a_type" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_a_type" style="display: none">
<?php ewr_PrependClass($Page->a_type->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Amendments_Report" data-field="x_a_type" id="sv2_a_type" name="sv2_a_type" size="30" maxlength="111" placeholder="<?php echo $Page->a_type->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->a_type->SearchValue2) ?>"<?php echo $Page->a_type->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fAmendments_Reportsummary.Init();
fAmendments_Reportsummary.FilterList = <?php echo $Page->GetFilterList() ?>;
</script>
<!-- Search form (end) -->
<?php } ?>
<?php if ($Page->ShowCurrentFilter) { ?>
<?php $Page->ShowFilterList() ?>
<?php } ?>
<?php } ?>
<?php

// Set the last group to display if not export all
if ($Page->ExportAll && $Page->Export <> "") {
	$Page->StopGrp = $Page->TotalGrps;
} else {
	$Page->StopGrp = $Page->StartGrp + $Page->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($Page->StopGrp) > intval($Page->TotalGrps))
	$Page->StopGrp = $Page->TotalGrps;
$Page->RecCount = 0;
$Page->RecIndex = 0;

// Get first row
if ($Page->TotalGrps > 0) {
	$Page->GetRow(1);
	$Page->GrpCount = 1;
}
$Page->GrpIdx = ewr_InitArray(2, -1);
$Page->GrpIdx[0] = -1;
$Page->GrpIdx[1] = $Page->StopGrp - $Page->StartGrp + 1;
while ($rs && !$rs->EOF && $Page->GrpCount <= $Page->DisplayGrps || $Page->ShowHeader) {

	// Show dummy header for custom template
	// Show header

	if ($Page->ShowHeader) {
?>
<?php if ($Page->Export <> "pdf") { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "Amendments_Reportsmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->id_stand->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="id_stand"><div class="Amendments_Report_id_stand"><span class="ewTableHeaderCaption"><?php echo $Page->id_stand->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="id_stand">
<?php if ($Page->SortUrl($Page->id_stand) == "") { ?>
		<div class="ewTableHeaderBtn Amendments_Report_id_stand">
			<span class="ewTableHeaderCaption"><?php echo $Page->id_stand->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Amendments_Report_id_stand" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->id_stand) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->id_stand->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->id_stand->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->id_stand->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->number->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="number"><div class="Amendments_Report_number"><span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="number">
<?php if ($Page->SortUrl($Page->number) == "") { ?>
		<div class="ewTableHeaderBtn Amendments_Report_number">
			<span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_number', false, '<?php echo $Page->number->RangeFrom; ?>', '<?php echo $Page->number->RangeTo; ?>');" id="x_number<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Amendments_Report_number" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->number) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->number->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->number->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_number', false, '<?php echo $Page->number->RangeFrom; ?>', '<?php echo $Page->number->RangeTo; ?>');" id="x_number<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->cash->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="cash"><div class="Amendments_Report_cash"><span class="ewTableHeaderCaption"><?php echo $Page->cash->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="cash">
<?php if ($Page->SortUrl($Page->cash) == "") { ?>
		<div class="ewTableHeaderBtn Amendments_Report_cash">
			<span class="ewTableHeaderCaption"><?php echo $Page->cash->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_cash', false, '<?php echo $Page->cash->RangeFrom; ?>', '<?php echo $Page->cash->RangeTo; ?>');" id="x_cash<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Amendments_Report_cash" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->cash) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->cash->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->cash->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->cash->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_cash', false, '<?php echo $Page->cash->RangeFrom; ?>', '<?php echo $Page->cash->RangeTo; ?>');" id="x_cash<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->date->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="date"><div class="Amendments_Report_date"><span class="ewTableHeaderCaption"><?php echo $Page->date->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="date">
<?php if ($Page->SortUrl($Page->date) == "") { ?>
		<div class="ewTableHeaderBtn Amendments_Report_date">
			<span class="ewTableHeaderCaption"><?php echo $Page->date->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_date', false, '<?php echo $Page->date->RangeFrom; ?>', '<?php echo $Page->date->RangeTo; ?>');" id="x_date<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Amendments_Report_date" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->date) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->date->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_date', false, '<?php echo $Page->date->RangeFrom; ?>', '<?php echo $Page->date->RangeTo; ?>');" id="x_date<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->capturer->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="capturer"><div class="Amendments_Report_capturer"><span class="ewTableHeaderCaption"><?php echo $Page->capturer->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="capturer">
<?php if ($Page->SortUrl($Page->capturer) == "") { ?>
		<div class="ewTableHeaderBtn Amendments_Report_capturer">
			<span class="ewTableHeaderCaption"><?php echo $Page->capturer->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_capturer', false, '<?php echo $Page->capturer->RangeFrom; ?>', '<?php echo $Page->capturer->RangeTo; ?>');" id="x_capturer<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Amendments_Report_capturer" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->capturer) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->capturer->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->capturer->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->capturer->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_capturer', false, '<?php echo $Page->capturer->RangeFrom; ?>', '<?php echo $Page->capturer->RangeTo; ?>');" id="x_capturer<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->authority->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="authority"><div class="Amendments_Report_authority"><span class="ewTableHeaderCaption"><?php echo $Page->authority->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="authority">
<?php if ($Page->SortUrl($Page->authority) == "") { ?>
		<div class="ewTableHeaderBtn Amendments_Report_authority">
			<span class="ewTableHeaderCaption"><?php echo $Page->authority->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_authority', false, '<?php echo $Page->authority->RangeFrom; ?>', '<?php echo $Page->authority->RangeTo; ?>');" id="x_authority<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Amendments_Report_authority" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->authority) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->authority->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->authority->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->authority->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_authority', false, '<?php echo $Page->authority->RangeFrom; ?>', '<?php echo $Page->authority->RangeTo; ?>');" id="x_authority<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->a_type->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="a_type"><div class="Amendments_Report_a_type"><span class="ewTableHeaderCaption"><?php echo $Page->a_type->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="a_type">
<?php if ($Page->SortUrl($Page->a_type) == "") { ?>
		<div class="ewTableHeaderBtn Amendments_Report_a_type">
			<span class="ewTableHeaderCaption"><?php echo $Page->a_type->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_a_type', false, '<?php echo $Page->a_type->RangeFrom; ?>', '<?php echo $Page->a_type->RangeTo; ?>');" id="x_a_type<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Amendments_Report_a_type" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->a_type) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->a_type->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->a_type->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->a_type->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Amendments_Report_a_type', false, '<?php echo $Page->a_type->RangeFrom; ?>', '<?php echo $Page->a_type->RangeTo; ?>');" id="x_a_type<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
	</tr>
</thead>
<tbody>
<?php
		if ($Page->TotalGrps == 0) break; // Show header only
		$Page->ShowHeader = FALSE;
	}
	$Page->RecCount++;
	$Page->RecIndex++;

		// Render detail row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->id_stand->Visible) { ?>
		<td data-field="id_stand"<?php echo $Page->id_stand->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Amendments_Report_id_stand"<?php echo $Page->id_stand->ViewAttributes() ?>><?php echo $Page->id_stand->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Amendments_Report_number"<?php echo $Page->number->ViewAttributes() ?>><?php echo $Page->number->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->cash->Visible) { ?>
		<td data-field="cash"<?php echo $Page->cash->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Amendments_Report_cash"<?php echo $Page->cash->ViewAttributes() ?>><?php echo $Page->cash->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->date->Visible) { ?>
		<td data-field="date"<?php echo $Page->date->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Amendments_Report_date"<?php echo $Page->date->ViewAttributes() ?>><?php echo $Page->date->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->capturer->Visible) { ?>
		<td data-field="capturer"<?php echo $Page->capturer->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Amendments_Report_capturer"<?php echo $Page->capturer->ViewAttributes() ?>><?php echo $Page->capturer->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->authority->Visible) { ?>
		<td data-field="authority"<?php echo $Page->authority->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Amendments_Report_authority"<?php echo $Page->authority->ViewAttributes() ?>><?php echo $Page->authority->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->a_type->Visible) { ?>
		<td data-field="a_type"<?php echo $Page->a_type->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Amendments_Report_a_type"<?php echo $Page->a_type->ViewAttributes() ?>><?php echo $Page->a_type->ListViewValue() ?></span></td>
<?php } ?>
	</tr>
<?php

		// Accumulate page summary
		$Page->AccumulateSummary();

		// Get next record
		$Page->GetRow(2);
	$Page->GrpCount++;
} // End while
?>
<?php if ($Page->TotalGrps > 0) { ?>
</tbody>
<tfoot>
<?php
	$Page->ResetAttrs();
	$Page->RowType = EWR_ROWTYPE_TOTAL;
	$Page->RowTotalType = EWR_ROWTOTAL_GRAND;
	$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>><td colspan="<?php echo ($Page->GrpFldCount + $Page->DtlFldCount) ?>"><?php echo $ReportLanguage->Phrase("RptGrandSummary") ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->TotCount,0,-2,-2,-2); ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td></tr>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && TRUE) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "Amendments_Reportsmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || TRUE) { // Show footer ?>
</table>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->TotalGrps > 0) { ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "Amendments_Reportsmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<!-- Summary Report Ends -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- center container - report (end) -->
	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
<?php } ?>
	<!-- Right slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- right container (end) -->
<div class="clearfix"></div>
<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
<?php } ?>
	<!-- Bottom slot -->
<a id="cht_Amendments_Report_Amendments_Bar"></a>
<div class="">
<div id="div_ctl_Amendments_Report_Amendments_Bar" class="ewChart">
<div id="div_Amendments_Report_Amendments_Bar" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_Amendments_Report_Amendments_Bar_grid" class="ewChartGrid"></div>
</div>
</div>
<?php

// Set up chart object
$Chart = &$Table->Amendments_Bar;

// Set up chart SQL
$SqlSelect = $Table->getSqlSelect();
$SqlChartSelect = $Chart->SqlSelect;
$sSqlChartBase = $Table->getSqlFrom();

// Load chart data from sql directly
$sSql = $SqlChartSelect . $sSqlChartBase;
$sChartFilter = $Chart->SqlWhere;
ewr_AddFilter($sChartFilter, $Table->getSqlWhere());
$sSql = ewr_BuildReportSql($sSql, $sChartFilter, $Chart->SqlGroupBy, "", $Chart->SqlOrderBy, $Page->Filter, "");
$Chart->ChartSql = $sSql;
$Chart->DrillDownInPanel = $Page->DrillDownInPanel;

// Set up page break
if (($Page->Export == "print" || $Page->Export == "pdf" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD")) && $Page->ExportChartPageBreak) {

	// Page_Breaking server event
	$Page->Page_Breaking($Page->ExportChartPageBreak, $Page->PageBreakContent);
	$Chart->PageBreakType = "before";
	$Chart->PageBreak = $Table->ExportChartPageBreak;
	$Chart->PageBreakContent = $Table->PageBreakContent;
}

// Set up show temp image
$Chart->ShowChart = ($Page->Export == "" || ($Page->Export == "print" && $Page->CustomExport == "") || ($Page->Export == "email" && @$_POST["contenttype"] == "url"));
$Chart->ShowTempImage = ($Page->Export == "pdf" || $Page->CustomExport <> "" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD"));
?>
<?php include_once "Amendments_Report_Amendments_Barchart.php" ?>
<?php if ($Page->Export <> "email" && !$Page->DrillDown) { ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<a href="javascript:void(0);" class="ewTopLink" onclick="$(document).scrollTop($('#top').offset().top);"><?php echo $ReportLanguage->Phrase("Top") ?></a>
<?php } ?>
<?php } ?>
<a id="cht_Amendments_Report_Amendments_Pie"></a>
<div class="">
<div id="div_ctl_Amendments_Report_Amendments_Pie" class="ewChart">
<div id="div_Amendments_Report_Amendments_Pie" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_Amendments_Report_Amendments_Pie_grid" class="ewChartGrid"></div>
</div>
</div>
<?php

// Set up chart object
$Chart = &$Table->Amendments_Pie;

// Set up chart SQL
$SqlSelect = $Table->getSqlSelect();
$SqlChartSelect = $Chart->SqlSelect;
$sSqlChartBase = $Table->getSqlFrom();

// Load chart data from sql directly
$sSql = $SqlChartSelect . $sSqlChartBase;
$sChartFilter = $Chart->SqlWhere;
ewr_AddFilter($sChartFilter, $Table->getSqlWhere());
$sSql = ewr_BuildReportSql($sSql, $sChartFilter, $Chart->SqlGroupBy, "", $Chart->SqlOrderBy, $Page->Filter, "");
$Chart->ChartSql = $sSql;
$Chart->DrillDownInPanel = $Page->DrillDownInPanel;

// Set up page break
if (($Page->Export == "print" || $Page->Export == "pdf" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD")) && $Page->ExportChartPageBreak) {

	// Page_Breaking server event
	$Page->Page_Breaking($Page->ExportChartPageBreak, $Page->PageBreakContent);
	$Chart->PageBreakType = "before";
	$Chart->PageBreak = $Table->ExportChartPageBreak;
	$Chart->PageBreakContent = $Table->PageBreakContent;
}

// Set up show temp image
$Chart->ShowChart = ($Page->Export == "" || ($Page->Export == "print" && $Page->CustomExport == "") || ($Page->Export == "email" && @$_POST["contenttype"] == "url"));
$Chart->ShowTempImage = ($Page->Export == "pdf" || $Page->CustomExport <> "" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD"));
?>
<?php include_once "Amendments_Report_Amendments_Piechart.php" ?>
<?php if ($Page->Export <> "email" && !$Page->DrillDown) { ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<a href="javascript:void(0);" class="ewTopLink" onclick="$(document).scrollTop($('#top').offset().top);"><?php echo $ReportLanguage->Phrase("Top") ?></a>
<?php } ?>
<?php } ?>
<?php if ($Page->Export == "") { ?>
	</div>
<!-- Bottom Container (End) -->
</div>
<!-- Table Container (End) -->
<?php } ?>
<?php $Page->ShowPageFooter(); ?>
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<?php

// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "phprptinc/footer.php" ?>
<?php
$Page->Page_Terminate();
if (isset($OldPage)) $Page = $OldPage;
?>
