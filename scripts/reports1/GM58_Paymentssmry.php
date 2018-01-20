<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "GM58_Paymentssmryinfo.php" ?>
<?php

//
// Page class
//

$GM58_Payments_summary = NULL; // Initialize page object first

class crGM58_Payments_summary extends crGM58_Payments {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{3080AF49-5443-4264-8421-3510B6183D7C}";

	// Page object name
	var $PageObjName = 'GM58_Payments_summary';

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

		// Table object (GM58_Payments)
		if (!isset($GLOBALS["GM58_Payments"])) {
			$GLOBALS["GM58_Payments"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["GM58_Payments"];
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
			define("EWR_TABLE_NAME", 'GM58 Payments', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fGM58_Paymentssummary";
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
		$this->payment_type->PlaceHolder = $this->payment_type->FldCaption();
		$this->month->PlaceHolder = $this->month->FldCaption();
		$this->payment_date->PlaceHolder = $this->payment_date->FldCaption();
		$this->months_paid->PlaceHolder = $this->months_paid->FldCaption();
		$this->capturer->PlaceHolder = $this->capturer->FldCaption();

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
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_GM58_Payments\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_GM58_Payments',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fGM58_Paymentssummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fGM58_Paymentssummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fGM58_Paymentssummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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

		$nDtls = 9;
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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(TRUE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

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
		$this->payment_type->SelectionList = "";
		$this->payment_type->DefaultSelectionList = "";
		$this->payment_type->ValueList = "";
		$this->month->SelectionList = "";
		$this->month->DefaultSelectionList = "";
		$this->month->ValueList = "";
		$this->payment_date->SelectionList = "";
		$this->payment_date->DefaultSelectionList = "";
		$this->payment_date->ValueList = "";
		$this->months_paid->SelectionList = "";
		$this->months_paid->DefaultSelectionList = "";
		$this->months_paid->ValueList = "";
		$this->capturer->SelectionList = "";
		$this->capturer->DefaultSelectionList = "";
		$this->capturer->ValueList = "";
		$this->datestatus->SelectionList = "";
		$this->datestatus->DefaultSelectionList = "";
		$this->datestatus->ValueList = "";

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
				$this->FirstRowData['number'] = ewr_Conv($rs->fields('number'),200);
				$this->FirstRowData['cash'] = ewr_Conv($rs->fields('cash'),131);
				$this->FirstRowData['payment_type'] = ewr_Conv($rs->fields('payment_type'),200);
				$this->FirstRowData['month'] = ewr_Conv($rs->fields('month'),200);
				$this->FirstRowData['payment_date'] = ewr_Conv($rs->fields('payment_date'),200);
				$this->FirstRowData['months_paid'] = ewr_Conv($rs->fields('months_paid'),200);
				$this->FirstRowData['capturer'] = ewr_Conv($rs->fields('capturer'),200);
				$this->FirstRowData['datestatus'] = ewr_Conv($rs->fields('datestatus'),135);
				$this->FirstRowData['value_date'] = ewr_Conv($rs->fields('value_date'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->number->setDbValue($rs->fields('number'));
			$this->cash->setDbValue($rs->fields('cash'));
			$this->payment_type->setDbValue($rs->fields('payment_type'));
			$this->month->setDbValue($rs->fields('month'));
			$this->payment_date->setDbValue($rs->fields('payment_date'));
			$this->months_paid->setDbValue($rs->fields('months_paid'));
			$this->capturer->setDbValue($rs->fields('capturer'));
			$this->datestatus->setDbValue($rs->fields('datestatus'));
			$this->value_date->setDbValue($rs->fields('value_date'));
			$this->Val[1] = $this->number->CurrentValue;
			$this->Val[2] = $this->cash->CurrentValue;
			$this->Val[3] = $this->payment_type->CurrentValue;
			$this->Val[4] = $this->month->CurrentValue;
			$this->Val[5] = $this->payment_date->CurrentValue;
			$this->Val[6] = $this->months_paid->CurrentValue;
			$this->Val[7] = $this->capturer->CurrentValue;
			$this->Val[8] = $this->datestatus->CurrentValue;
		} else {
			$this->number->setDbValue("");
			$this->cash->setDbValue("");
			$this->payment_type->setDbValue("");
			$this->month->setDbValue("");
			$this->payment_date->setDbValue("");
			$this->months_paid->setDbValue("");
			$this->capturer->setDbValue("");
			$this->datestatus->setDbValue("");
			$this->value_date->setDbValue("");
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

			if ($popupname == 'GM58_Payments_number') {
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
			if ($popupname == 'GM58_Payments_cash') {
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

			// Build distinct values for payment_type
			if ($popupname == 'GM58_Payments_payment_type') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->payment_type, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->payment_type->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->payment_type->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->payment_type->setDbValue($rswrk->fields[0]);
					if (is_null($this->payment_type->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->payment_type->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->payment_type->ViewValue = $this->payment_type->CurrentValue;
						ewr_SetupDistinctValues($this->payment_type->ValueList, $this->payment_type->CurrentValue, $this->payment_type->ViewValue, FALSE, $this->payment_type->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->payment_type->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->payment_type->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->payment_type;
			}

			// Build distinct values for month
			if ($popupname == 'GM58_Payments_month') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->month, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->month->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->month->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->month->setDbValue($rswrk->fields[0]);
					if (is_null($this->month->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->month->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->month->ViewValue = $this->month->CurrentValue;
						ewr_SetupDistinctValues($this->month->ValueList, $this->month->CurrentValue, $this->month->ViewValue, FALSE, $this->month->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->month->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->month->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->month;
			}

			// Build distinct values for payment_date
			if ($popupname == 'GM58_Payments_payment_date') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->payment_date, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->payment_date->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->payment_date->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->payment_date->setDbValue($rswrk->fields[0]);
					if (is_null($this->payment_date->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->payment_date->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->payment_date->ViewValue = $this->payment_date->CurrentValue;
						ewr_SetupDistinctValues($this->payment_date->ValueList, $this->payment_date->CurrentValue, $this->payment_date->ViewValue, FALSE, $this->payment_date->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->payment_date->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->payment_date->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->payment_date;
			}

			// Build distinct values for months_paid
			if ($popupname == 'GM58_Payments_months_paid') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->months_paid, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->months_paid->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->months_paid->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->months_paid->setDbValue($rswrk->fields[0]);
					if (is_null($this->months_paid->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->months_paid->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->months_paid->ViewValue = $this->months_paid->CurrentValue;
						ewr_SetupDistinctValues($this->months_paid->ValueList, $this->months_paid->CurrentValue, $this->months_paid->ViewValue, FALSE, $this->months_paid->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->months_paid->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->months_paid->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->months_paid;
			}

			// Build distinct values for capturer
			if ($popupname == 'GM58_Payments_capturer') {
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

			// Build distinct values for datestatus
			if ($popupname == 'GM58_Payments_datestatus') {
				ewr_SetupDistinctValuesFromFilter($this->datestatus->ValueList, $this->datestatus->AdvancedFilters); // Set up popup filter
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->datestatus, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->datestatus->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->datestatus->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->datestatus->setDbValue($rswrk->fields[0]);
					if (is_null($this->datestatus->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->datestatus->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->datestatus->ViewValue = ewr_FormatDateTime($this->datestatus->CurrentValue, 1);
						ewr_SetupDistinctValues($this->datestatus->ValueList, $this->datestatus->CurrentValue, $this->datestatus->ViewValue, FALSE, $this->datestatus->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->datestatus->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->datestatus->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->datestatus;
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
				$this->ClearSessionSelection('payment_type');
				$this->ClearSessionSelection('month');
				$this->ClearSessionSelection('payment_date');
				$this->ClearSessionSelection('months_paid');
				$this->ClearSessionSelection('capturer');
				$this->ClearSessionSelection('datestatus');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get number selected values

		if (is_array(@$_SESSION["sel_GM58_Payments_number"])) {
			$this->LoadSelectionFromSession('number');
		} elseif (@$_SESSION["sel_GM58_Payments_number"] == EWR_INIT_VALUE) { // Select all
			$this->number->SelectionList = "";
		}

		// Get cash selected values
		if (is_array(@$_SESSION["sel_GM58_Payments_cash"])) {
			$this->LoadSelectionFromSession('cash');
		} elseif (@$_SESSION["sel_GM58_Payments_cash"] == EWR_INIT_VALUE) { // Select all
			$this->cash->SelectionList = "";
		}

		// Get payment_type selected values
		if (is_array(@$_SESSION["sel_GM58_Payments_payment_type"])) {
			$this->LoadSelectionFromSession('payment_type');
		} elseif (@$_SESSION["sel_GM58_Payments_payment_type"] == EWR_INIT_VALUE) { // Select all
			$this->payment_type->SelectionList = "";
		}

		// Get month selected values
		if (is_array(@$_SESSION["sel_GM58_Payments_month"])) {
			$this->LoadSelectionFromSession('month');
		} elseif (@$_SESSION["sel_GM58_Payments_month"] == EWR_INIT_VALUE) { // Select all
			$this->month->SelectionList = "";
		}

		// Get payment_date selected values
		if (is_array(@$_SESSION["sel_GM58_Payments_payment_date"])) {
			$this->LoadSelectionFromSession('payment_date');
		} elseif (@$_SESSION["sel_GM58_Payments_payment_date"] == EWR_INIT_VALUE) { // Select all
			$this->payment_date->SelectionList = "";
		}

		// Get months_paid selected values
		if (is_array(@$_SESSION["sel_GM58_Payments_months_paid"])) {
			$this->LoadSelectionFromSession('months_paid');
		} elseif (@$_SESSION["sel_GM58_Payments_months_paid"] == EWR_INIT_VALUE) { // Select all
			$this->months_paid->SelectionList = "";
		}

		// Get capturer selected values
		if (is_array(@$_SESSION["sel_GM58_Payments_capturer"])) {
			$this->LoadSelectionFromSession('capturer');
		} elseif (@$_SESSION["sel_GM58_Payments_capturer"] == EWR_INIT_VALUE) { // Select all
			$this->capturer->SelectionList = "";
		}

		// Get datestatus selected values
		if (is_array(@$_SESSION["sel_GM58_Payments_datestatus"])) {
			$this->LoadSelectionFromSession('datestatus');
		} elseif (@$_SESSION["sel_GM58_Payments_datestatus"] == EWR_INIT_VALUE) { // Select all
			$this->datestatus->SelectionList = "";
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

			// Get total from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectAgg(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$sSql = $this->getSqlAggPfx() . $sSql . $this->getSqlAggSfx();
			$rsagg = $conn->Execute($sSql);
			if ($rsagg) {
				$this->GrandCnt[1] = $this->TotCount;
				$this->GrandCnt[2] = $this->TotCount;
				$this->GrandSmry[2] = $rsagg->fields("sum_cash");
				$this->GrandCnt[3] = $this->TotCount;
				$this->GrandCnt[4] = $this->TotCount;
				$this->GrandCnt[5] = $this->TotCount;
				$this->GrandCnt[6] = $this->TotCount;
				$this->GrandCnt[7] = $this->TotCount;
				$this->GrandCnt[8] = $this->TotCount;
				$rsagg->Close();
				$bGotSummary = TRUE;
			}

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

			// cash
			$this->cash->SumViewValue = $this->cash->SumValue;
			$this->cash->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// number
			$this->number->HrefValue = "";

			// cash
			$this->cash->HrefValue = "";

			// payment_type
			$this->payment_type->HrefValue = "";

			// month
			$this->month->HrefValue = "";

			// payment_date
			$this->payment_date->HrefValue = "";

			// months_paid
			$this->months_paid->HrefValue = "";

			// capturer
			$this->capturer->HrefValue = "";

			// datestatus
			$this->datestatus->HrefValue = "";
		} else {

			// number
			$this->number->ViewValue = $this->number->CurrentValue;
			$this->number->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// cash
			$this->cash->ViewValue = $this->cash->CurrentValue;
			$this->cash->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// payment_type
			$this->payment_type->ViewValue = $this->payment_type->CurrentValue;
			$this->payment_type->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// month
			$this->month->ViewValue = $this->month->CurrentValue;
			$this->month->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// payment_date
			$this->payment_date->ViewValue = $this->payment_date->CurrentValue;
			$this->payment_date->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// months_paid
			$this->months_paid->ViewValue = $this->months_paid->CurrentValue;
			$this->months_paid->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// capturer
			$this->capturer->ViewValue = $this->capturer->CurrentValue;
			$this->capturer->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// datestatus
			$this->datestatus->ViewValue = $this->datestatus->CurrentValue;
			$this->datestatus->ViewValue = ewr_FormatDateTime($this->datestatus->ViewValue, 1);
			$this->datestatus->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// number
			$this->number->HrefValue = "";

			// cash
			$this->cash->HrefValue = "";

			// payment_type
			$this->payment_type->HrefValue = "";

			// month
			$this->month->HrefValue = "";

			// payment_date
			$this->payment_date->HrefValue = "";

			// months_paid
			$this->months_paid->HrefValue = "";

			// capturer
			$this->capturer->HrefValue = "";

			// datestatus
			$this->datestatus->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// cash
			$CurrentValue = $this->cash->SumValue;
			$ViewValue = &$this->cash->SumViewValue;
			$ViewAttrs = &$this->cash->ViewAttrs;
			$CellAttrs = &$this->cash->CellAttrs;
			$HrefValue = &$this->cash->HrefValue;
			$LinkAttrs = &$this->cash->LinkAttrs;
			$this->Cell_Rendered($this->cash, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		} else {

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

			// payment_type
			$CurrentValue = $this->payment_type->CurrentValue;
			$ViewValue = &$this->payment_type->ViewValue;
			$ViewAttrs = &$this->payment_type->ViewAttrs;
			$CellAttrs = &$this->payment_type->CellAttrs;
			$HrefValue = &$this->payment_type->HrefValue;
			$LinkAttrs = &$this->payment_type->LinkAttrs;
			$this->Cell_Rendered($this->payment_type, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// month
			$CurrentValue = $this->month->CurrentValue;
			$ViewValue = &$this->month->ViewValue;
			$ViewAttrs = &$this->month->ViewAttrs;
			$CellAttrs = &$this->month->CellAttrs;
			$HrefValue = &$this->month->HrefValue;
			$LinkAttrs = &$this->month->LinkAttrs;
			$this->Cell_Rendered($this->month, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// payment_date
			$CurrentValue = $this->payment_date->CurrentValue;
			$ViewValue = &$this->payment_date->ViewValue;
			$ViewAttrs = &$this->payment_date->ViewAttrs;
			$CellAttrs = &$this->payment_date->CellAttrs;
			$HrefValue = &$this->payment_date->HrefValue;
			$LinkAttrs = &$this->payment_date->LinkAttrs;
			$this->Cell_Rendered($this->payment_date, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// months_paid
			$CurrentValue = $this->months_paid->CurrentValue;
			$ViewValue = &$this->months_paid->ViewValue;
			$ViewAttrs = &$this->months_paid->ViewAttrs;
			$CellAttrs = &$this->months_paid->CellAttrs;
			$HrefValue = &$this->months_paid->HrefValue;
			$LinkAttrs = &$this->months_paid->LinkAttrs;
			$this->Cell_Rendered($this->months_paid, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// capturer
			$CurrentValue = $this->capturer->CurrentValue;
			$ViewValue = &$this->capturer->ViewValue;
			$ViewAttrs = &$this->capturer->ViewAttrs;
			$CellAttrs = &$this->capturer->CellAttrs;
			$HrefValue = &$this->capturer->HrefValue;
			$LinkAttrs = &$this->capturer->LinkAttrs;
			$this->Cell_Rendered($this->capturer, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// datestatus
			$CurrentValue = $this->datestatus->CurrentValue;
			$ViewValue = &$this->datestatus->ViewValue;
			$ViewAttrs = &$this->datestatus->ViewAttrs;
			$CellAttrs = &$this->datestatus->CellAttrs;
			$HrefValue = &$this->datestatus->HrefValue;
			$LinkAttrs = &$this->datestatus->LinkAttrs;
			$this->Cell_Rendered($this->datestatus, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->number->Visible) $this->DtlFldCount += 1;
		if ($this->cash->Visible) $this->DtlFldCount += 1;
		if ($this->payment_type->Visible) $this->DtlFldCount += 1;
		if ($this->month->Visible) $this->DtlFldCount += 1;
		if ($this->payment_date->Visible) $this->DtlFldCount += 1;
		if ($this->months_paid->Visible) $this->DtlFldCount += 1;
		if ($this->capturer->Visible) $this->DtlFldCount += 1;
		if ($this->datestatus->Visible) $this->DtlFldCount += 1;
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
			if ($this->ClearExtFilter == 'GM58_Payments_number')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'number');

			// Clear extended filter for field cash
			if ($this->ClearExtFilter == 'GM58_Payments_cash')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'cash');

			// Clear extended filter for field payment_type
			if ($this->ClearExtFilter == 'GM58_Payments_payment_type')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'payment_type');

			// Clear extended filter for field month
			if ($this->ClearExtFilter == 'GM58_Payments_month')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'month');

			// Clear extended filter for field payment_date
			if ($this->ClearExtFilter == 'GM58_Payments_payment_date')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'payment_date');

			// Clear extended filter for field months_paid
			if ($this->ClearExtFilter == 'GM58_Payments_months_paid')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'months_paid');

			// Clear extended filter for field capturer
			if ($this->ClearExtFilter == 'GM58_Payments_capturer')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'capturer');

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->number->SearchValue, $this->number->SearchOperator, $this->number->SearchCondition, $this->number->SearchValue2, $this->number->SearchOperator2, 'number'); // Field number
			$this->SetSessionFilterValues($this->cash->SearchValue, $this->cash->SearchOperator, $this->cash->SearchCondition, $this->cash->SearchValue2, $this->cash->SearchOperator2, 'cash'); // Field cash
			$this->SetSessionFilterValues($this->payment_type->SearchValue, $this->payment_type->SearchOperator, $this->payment_type->SearchCondition, $this->payment_type->SearchValue2, $this->payment_type->SearchOperator2, 'payment_type'); // Field payment_type
			$this->SetSessionFilterValues($this->month->SearchValue, $this->month->SearchOperator, $this->month->SearchCondition, $this->month->SearchValue2, $this->month->SearchOperator2, 'month'); // Field month
			$this->SetSessionFilterValues($this->payment_date->SearchValue, $this->payment_date->SearchOperator, $this->payment_date->SearchCondition, $this->payment_date->SearchValue2, $this->payment_date->SearchOperator2, 'payment_date'); // Field payment_date
			$this->SetSessionFilterValues($this->months_paid->SearchValue, $this->months_paid->SearchOperator, $this->months_paid->SearchCondition, $this->months_paid->SearchValue2, $this->months_paid->SearchOperator2, 'months_paid'); // Field months_paid
			$this->SetSessionFilterValues($this->capturer->SearchValue, $this->capturer->SearchOperator, $this->capturer->SearchCondition, $this->capturer->SearchValue2, $this->capturer->SearchOperator2, 'capturer'); // Field capturer

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

			// Field payment_type
			if ($this->GetFilterValues($this->payment_type)) {
				$bSetupFilter = TRUE;
			}

			// Field month
			if ($this->GetFilterValues($this->month)) {
				$bSetupFilter = TRUE;
			}

			// Field payment_date
			if ($this->GetFilterValues($this->payment_date)) {
				$bSetupFilter = TRUE;
			}

			// Field months_paid
			if ($this->GetFilterValues($this->months_paid)) {
				$bSetupFilter = TRUE;
			}

			// Field capturer
			if ($this->GetFilterValues($this->capturer)) {
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
			$this->GetSessionFilterValues($this->payment_type); // Field payment_type
			$this->GetSessionFilterValues($this->month); // Field month
			$this->GetSessionFilterValues($this->payment_date); // Field payment_date
			$this->GetSessionFilterValues($this->months_paid); // Field months_paid
			$this->GetSessionFilterValues($this->capturer); // Field capturer
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->number, $sFilter, FALSE, TRUE); // Field number
		$this->BuildExtendedFilter($this->cash, $sFilter, FALSE, TRUE); // Field cash
		$this->BuildExtendedFilter($this->payment_type, $sFilter, FALSE, TRUE); // Field payment_type
		$this->BuildExtendedFilter($this->month, $sFilter, FALSE, TRUE); // Field month
		$this->BuildExtendedFilter($this->payment_date, $sFilter, FALSE, TRUE); // Field payment_date
		$this->BuildExtendedFilter($this->months_paid, $sFilter, FALSE, TRUE); // Field months_paid
		$this->BuildExtendedFilter($this->capturer, $sFilter, FALSE, TRUE); // Field capturer

		// Save parms to session
		$this->SetSessionFilterValues($this->number->SearchValue, $this->number->SearchOperator, $this->number->SearchCondition, $this->number->SearchValue2, $this->number->SearchOperator2, 'number'); // Field number
		$this->SetSessionFilterValues($this->cash->SearchValue, $this->cash->SearchOperator, $this->cash->SearchCondition, $this->cash->SearchValue2, $this->cash->SearchOperator2, 'cash'); // Field cash
		$this->SetSessionFilterValues($this->payment_type->SearchValue, $this->payment_type->SearchOperator, $this->payment_type->SearchCondition, $this->payment_type->SearchValue2, $this->payment_type->SearchOperator2, 'payment_type'); // Field payment_type
		$this->SetSessionFilterValues($this->month->SearchValue, $this->month->SearchOperator, $this->month->SearchCondition, $this->month->SearchValue2, $this->month->SearchOperator2, 'month'); // Field month
		$this->SetSessionFilterValues($this->payment_date->SearchValue, $this->payment_date->SearchOperator, $this->payment_date->SearchCondition, $this->payment_date->SearchValue2, $this->payment_date->SearchOperator2, 'payment_date'); // Field payment_date
		$this->SetSessionFilterValues($this->months_paid->SearchValue, $this->months_paid->SearchOperator, $this->months_paid->SearchCondition, $this->months_paid->SearchValue2, $this->months_paid->SearchOperator2, 'months_paid'); // Field months_paid
		$this->SetSessionFilterValues($this->capturer->SearchValue, $this->capturer->SearchOperator, $this->capturer->SearchCondition, $this->capturer->SearchValue2, $this->capturer->SearchOperator2, 'capturer'); // Field capturer

		// Setup filter
		if ($bSetupFilter) {

			// Field number
			$sWrk = "";
			$this->BuildExtendedFilter($this->number, $sWrk);
			ewr_LoadSelectionFromFilter($this->number, $sWrk, $this->number->SelectionList);
			$_SESSION['sel_GM58_Payments_number'] = ($this->number->SelectionList == "") ? EWR_INIT_VALUE : $this->number->SelectionList;

			// Field cash
			$sWrk = "";
			$this->BuildExtendedFilter($this->cash, $sWrk);
			ewr_LoadSelectionFromFilter($this->cash, $sWrk, $this->cash->SelectionList);
			$_SESSION['sel_GM58_Payments_cash'] = ($this->cash->SelectionList == "") ? EWR_INIT_VALUE : $this->cash->SelectionList;

			// Field payment_type
			$sWrk = "";
			$this->BuildExtendedFilter($this->payment_type, $sWrk);
			ewr_LoadSelectionFromFilter($this->payment_type, $sWrk, $this->payment_type->SelectionList);
			$_SESSION['sel_GM58_Payments_payment_type'] = ($this->payment_type->SelectionList == "") ? EWR_INIT_VALUE : $this->payment_type->SelectionList;

			// Field month
			$sWrk = "";
			$this->BuildExtendedFilter($this->month, $sWrk);
			ewr_LoadSelectionFromFilter($this->month, $sWrk, $this->month->SelectionList);
			$_SESSION['sel_GM58_Payments_month'] = ($this->month->SelectionList == "") ? EWR_INIT_VALUE : $this->month->SelectionList;

			// Field payment_date
			$sWrk = "";
			$this->BuildExtendedFilter($this->payment_date, $sWrk);
			ewr_LoadSelectionFromFilter($this->payment_date, $sWrk, $this->payment_date->SelectionList);
			$_SESSION['sel_GM58_Payments_payment_date'] = ($this->payment_date->SelectionList == "") ? EWR_INIT_VALUE : $this->payment_date->SelectionList;

			// Field months_paid
			$sWrk = "";
			$this->BuildExtendedFilter($this->months_paid, $sWrk);
			ewr_LoadSelectionFromFilter($this->months_paid, $sWrk, $this->months_paid->SelectionList);
			$_SESSION['sel_GM58_Payments_months_paid'] = ($this->months_paid->SelectionList == "") ? EWR_INIT_VALUE : $this->months_paid->SelectionList;

			// Field capturer
			$sWrk = "";
			$this->BuildExtendedFilter($this->capturer, $sWrk);
			ewr_LoadSelectionFromFilter($this->capturer, $sWrk, $this->capturer->SelectionList);
			$_SESSION['sel_GM58_Payments_capturer'] = ($this->capturer->SelectionList == "") ? EWR_INIT_VALUE : $this->capturer->SelectionList;
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
		$this->GetSessionValue($fld->DropDownValue, 'sv_GM58_Payments_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_GM58_Payments_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_GM58_Payments_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_GM58_Payments_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_GM58_Payments_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_GM58_Payments_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_GM58_Payments_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_GM58_Payments_' . $parm] = $sv;
		$_SESSION['so_GM58_Payments_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_GM58_Payments_' . $parm] = $sv1;
		$_SESSION['so_GM58_Payments_' . $parm] = $so1;
		$_SESSION['sc_GM58_Payments_' . $parm] = $sc;
		$_SESSION['sv2_GM58_Payments_' . $parm] = $sv2;
		$_SESSION['so2_GM58_Payments_' . $parm] = $so2;
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
		$_SESSION["sel_GM58_Payments_$parm"] = "";
		$_SESSION["rf_GM58_Payments_$parm"] = "";
		$_SESSION["rt_GM58_Payments_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_GM58_Payments_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_GM58_Payments_$parm"];
		$fld->RangeTo = @$_SESSION["rt_GM58_Payments_$parm"];
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

		// Field payment_type
		$this->SetDefaultExtFilter($this->payment_type, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->payment_type);
		$sWrk = "";
		$this->BuildExtendedFilter($this->payment_type, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->payment_type, $sWrk, $this->payment_type->DefaultSelectionList);
		if (!$this->SearchCommand) $this->payment_type->SelectionList = $this->payment_type->DefaultSelectionList;

		// Field month
		$this->SetDefaultExtFilter($this->month, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->month);
		$sWrk = "";
		$this->BuildExtendedFilter($this->month, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->month, $sWrk, $this->month->DefaultSelectionList);
		if (!$this->SearchCommand) $this->month->SelectionList = $this->month->DefaultSelectionList;

		// Field payment_date
		$this->SetDefaultExtFilter($this->payment_date, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->payment_date);
		$sWrk = "";
		$this->BuildExtendedFilter($this->payment_date, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->payment_date, $sWrk, $this->payment_date->DefaultSelectionList);
		if (!$this->SearchCommand) $this->payment_date->SelectionList = $this->payment_date->DefaultSelectionList;

		// Field months_paid
		$this->SetDefaultExtFilter($this->months_paid, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->months_paid);
		$sWrk = "";
		$this->BuildExtendedFilter($this->months_paid, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->months_paid, $sWrk, $this->months_paid->DefaultSelectionList);
		if (!$this->SearchCommand) $this->months_paid->SelectionList = $this->months_paid->DefaultSelectionList;

		// Field capturer
		$this->SetDefaultExtFilter($this->capturer, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->capturer);
		$sWrk = "";
		$this->BuildExtendedFilter($this->capturer, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->capturer, $sWrk, $this->capturer->DefaultSelectionList);
		if (!$this->SearchCommand) $this->capturer->SelectionList = $this->capturer->DefaultSelectionList;

		/**
		* Set up default values for popup filters
		*/

		// Field number
		// $this->number->DefaultSelectionList = array("val1", "val2");
		// Field cash
		// $this->cash->DefaultSelectionList = array("val1", "val2");
		// Field payment_type
		// $this->payment_type->DefaultSelectionList = array("val1", "val2");
		// Field month
		// $this->month->DefaultSelectionList = array("val1", "val2");
		// Field payment_date
		// $this->payment_date->DefaultSelectionList = array("val1", "val2");
		// Field months_paid
		// $this->months_paid->DefaultSelectionList = array("val1", "val2");
		// Field capturer
		// $this->capturer->DefaultSelectionList = array("val1", "val2");
		// Field datestatus
		// $this->datestatus->DefaultSelectionList = array("val1", "val2");

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

		// Check payment_type text filter
		if ($this->TextFilterApplied($this->payment_type))
			return TRUE;

		// Check payment_type popup filter
		if (!ewr_MatchedArray($this->payment_type->DefaultSelectionList, $this->payment_type->SelectionList))
			return TRUE;

		// Check month text filter
		if ($this->TextFilterApplied($this->month))
			return TRUE;

		// Check month popup filter
		if (!ewr_MatchedArray($this->month->DefaultSelectionList, $this->month->SelectionList))
			return TRUE;

		// Check payment_date text filter
		if ($this->TextFilterApplied($this->payment_date))
			return TRUE;

		// Check payment_date popup filter
		if (!ewr_MatchedArray($this->payment_date->DefaultSelectionList, $this->payment_date->SelectionList))
			return TRUE;

		// Check months_paid text filter
		if ($this->TextFilterApplied($this->months_paid))
			return TRUE;

		// Check months_paid popup filter
		if (!ewr_MatchedArray($this->months_paid->DefaultSelectionList, $this->months_paid->SelectionList))
			return TRUE;

		// Check capturer text filter
		if ($this->TextFilterApplied($this->capturer))
			return TRUE;

		// Check capturer popup filter
		if (!ewr_MatchedArray($this->capturer->DefaultSelectionList, $this->capturer->SelectionList))
			return TRUE;

		// Check datestatus popup filter
		if (!ewr_MatchedArray($this->datestatus->DefaultSelectionList, $this->datestatus->SelectionList))
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
			$sWrk = ewr_JoinArray($this->cash->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->cash->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field payment_type
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->payment_type, $sExtWrk);
		if (is_array($this->payment_type->SelectionList))
			$sWrk = ewr_JoinArray($this->payment_type->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->payment_type->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field month
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->month, $sExtWrk);
		if (is_array($this->month->SelectionList))
			$sWrk = ewr_JoinArray($this->month->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->month->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field payment_date
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->payment_date, $sExtWrk);
		if (is_array($this->payment_date->SelectionList))
			$sWrk = ewr_JoinArray($this->payment_date->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->payment_date->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field months_paid
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->months_paid, $sExtWrk);
		if (is_array($this->months_paid->SelectionList))
			$sWrk = ewr_JoinArray($this->months_paid->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->months_paid->FldCaption() . "</span>" . $sFilter . "</div>";

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

		// Field datestatus
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->datestatus->SelectionList))
			$sWrk = ewr_JoinArray($this->datestatus->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->datestatus->FldCaption() . "</span>" . $sFilter . "</div>";
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

		// Field payment_type
		$sWrk = "";
		if ($this->payment_type->SearchValue <> "" || $this->payment_type->SearchValue2 <> "") {
			$sWrk = "\"sv_payment_type\":\"" . ewr_JsEncode2($this->payment_type->SearchValue) . "\"," .
				"\"so_payment_type\":\"" . ewr_JsEncode2($this->payment_type->SearchOperator) . "\"," .
				"\"sc_payment_type\":\"" . ewr_JsEncode2($this->payment_type->SearchCondition) . "\"," .
				"\"sv2_payment_type\":\"" . ewr_JsEncode2($this->payment_type->SearchValue2) . "\"," .
				"\"so2_payment_type\":\"" . ewr_JsEncode2($this->payment_type->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->payment_type->SelectionList <> EWR_INIT_VALUE) ? $this->payment_type->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_payment_type\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field month
		$sWrk = "";
		if ($this->month->SearchValue <> "" || $this->month->SearchValue2 <> "") {
			$sWrk = "\"sv_month\":\"" . ewr_JsEncode2($this->month->SearchValue) . "\"," .
				"\"so_month\":\"" . ewr_JsEncode2($this->month->SearchOperator) . "\"," .
				"\"sc_month\":\"" . ewr_JsEncode2($this->month->SearchCondition) . "\"," .
				"\"sv2_month\":\"" . ewr_JsEncode2($this->month->SearchValue2) . "\"," .
				"\"so2_month\":\"" . ewr_JsEncode2($this->month->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->month->SelectionList <> EWR_INIT_VALUE) ? $this->month->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_month\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field payment_date
		$sWrk = "";
		if ($this->payment_date->SearchValue <> "" || $this->payment_date->SearchValue2 <> "") {
			$sWrk = "\"sv_payment_date\":\"" . ewr_JsEncode2($this->payment_date->SearchValue) . "\"," .
				"\"so_payment_date\":\"" . ewr_JsEncode2($this->payment_date->SearchOperator) . "\"," .
				"\"sc_payment_date\":\"" . ewr_JsEncode2($this->payment_date->SearchCondition) . "\"," .
				"\"sv2_payment_date\":\"" . ewr_JsEncode2($this->payment_date->SearchValue2) . "\"," .
				"\"so2_payment_date\":\"" . ewr_JsEncode2($this->payment_date->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->payment_date->SelectionList <> EWR_INIT_VALUE) ? $this->payment_date->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_payment_date\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field months_paid
		$sWrk = "";
		if ($this->months_paid->SearchValue <> "" || $this->months_paid->SearchValue2 <> "") {
			$sWrk = "\"sv_months_paid\":\"" . ewr_JsEncode2($this->months_paid->SearchValue) . "\"," .
				"\"so_months_paid\":\"" . ewr_JsEncode2($this->months_paid->SearchOperator) . "\"," .
				"\"sc_months_paid\":\"" . ewr_JsEncode2($this->months_paid->SearchCondition) . "\"," .
				"\"sv2_months_paid\":\"" . ewr_JsEncode2($this->months_paid->SearchValue2) . "\"," .
				"\"so2_months_paid\":\"" . ewr_JsEncode2($this->months_paid->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->months_paid->SelectionList <> EWR_INIT_VALUE) ? $this->months_paid->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_months_paid\":\"" . ewr_JsEncode2($sWrk) . "\"";
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

		// Field datestatus
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->datestatus->SelectionList <> EWR_INIT_VALUE) ? $this->datestatus->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_datestatus\":\"" . ewr_JsEncode2($sWrk) . "\"";
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
			$_SESSION["sel_GM58_Payments_number"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "number"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "number");
			$this->number->SelectionList = "";
			$_SESSION["sel_GM58_Payments_number"] = "";
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
			$_SESSION["sel_GM58_Payments_cash"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "cash"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "cash");
			$this->cash->SelectionList = "";
			$_SESSION["sel_GM58_Payments_cash"] = "";
		}

		// Field payment_type
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_payment_type", $filter) || array_key_exists("so_payment_type", $filter) ||
			array_key_exists("sc_payment_type", $filter) ||
			array_key_exists("sv2_payment_type", $filter) || array_key_exists("so2_payment_type", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_payment_type"], @$filter["so_payment_type"], @$filter["sc_payment_type"], @$filter["sv2_payment_type"], @$filter["so2_payment_type"], "payment_type");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_payment_type", $filter)) {
			$sWrk = $filter["sel_payment_type"];
			$sWrk = explode("||", $sWrk);
			$this->payment_type->SelectionList = $sWrk;
			$_SESSION["sel_GM58_Payments_payment_type"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "payment_type"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "payment_type");
			$this->payment_type->SelectionList = "";
			$_SESSION["sel_GM58_Payments_payment_type"] = "";
		}

		// Field month
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_month", $filter) || array_key_exists("so_month", $filter) ||
			array_key_exists("sc_month", $filter) ||
			array_key_exists("sv2_month", $filter) || array_key_exists("so2_month", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_month"], @$filter["so_month"], @$filter["sc_month"], @$filter["sv2_month"], @$filter["so2_month"], "month");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_month", $filter)) {
			$sWrk = $filter["sel_month"];
			$sWrk = explode("||", $sWrk);
			$this->month->SelectionList = $sWrk;
			$_SESSION["sel_GM58_Payments_month"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "month"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "month");
			$this->month->SelectionList = "";
			$_SESSION["sel_GM58_Payments_month"] = "";
		}

		// Field payment_date
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_payment_date", $filter) || array_key_exists("so_payment_date", $filter) ||
			array_key_exists("sc_payment_date", $filter) ||
			array_key_exists("sv2_payment_date", $filter) || array_key_exists("so2_payment_date", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_payment_date"], @$filter["so_payment_date"], @$filter["sc_payment_date"], @$filter["sv2_payment_date"], @$filter["so2_payment_date"], "payment_date");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_payment_date", $filter)) {
			$sWrk = $filter["sel_payment_date"];
			$sWrk = explode("||", $sWrk);
			$this->payment_date->SelectionList = $sWrk;
			$_SESSION["sel_GM58_Payments_payment_date"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "payment_date"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "payment_date");
			$this->payment_date->SelectionList = "";
			$_SESSION["sel_GM58_Payments_payment_date"] = "";
		}

		// Field months_paid
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_months_paid", $filter) || array_key_exists("so_months_paid", $filter) ||
			array_key_exists("sc_months_paid", $filter) ||
			array_key_exists("sv2_months_paid", $filter) || array_key_exists("so2_months_paid", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_months_paid"], @$filter["so_months_paid"], @$filter["sc_months_paid"], @$filter["sv2_months_paid"], @$filter["so2_months_paid"], "months_paid");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_months_paid", $filter)) {
			$sWrk = $filter["sel_months_paid"];
			$sWrk = explode("||", $sWrk);
			$this->months_paid->SelectionList = $sWrk;
			$_SESSION["sel_GM58_Payments_months_paid"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "months_paid"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "months_paid");
			$this->months_paid->SelectionList = "";
			$_SESSION["sel_GM58_Payments_months_paid"] = "";
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
			$_SESSION["sel_GM58_Payments_capturer"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "capturer"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "capturer");
			$this->capturer->SelectionList = "";
			$_SESSION["sel_GM58_Payments_capturer"] = "";
		}

		// Field datestatus
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_datestatus", $filter)) {
			$sWrk = $filter["sel_datestatus"];
			$sWrk = explode("||", $sWrk);
			$this->datestatus->SelectionList = $sWrk;
			$_SESSION["sel_GM58_Payments_datestatus"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
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
				$sFilter = ewr_FilterSQL($this->cash, "`cash`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->cash, $sFilter, "popup");
				$this->cash->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->payment_type)) {
			if (is_array($this->payment_type->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->payment_type, "`payment_type`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->payment_type, $sFilter, "popup");
				$this->payment_type->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->month)) {
			if (is_array($this->month->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->month, "`month`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->month, $sFilter, "popup");
				$this->month->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->payment_date)) {
			if (is_array($this->payment_date->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->payment_date, "`payment_date`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->payment_date, $sFilter, "popup");
				$this->payment_date->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->months_paid)) {
			if (is_array($this->months_paid->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->months_paid, "`months_paid`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->months_paid, $sFilter, "popup");
				$this->months_paid->CurrentFilter = $sFilter;
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
			if (is_array($this->datestatus->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->datestatus, "`datestatus`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->datestatus, $sFilter, "popup");
				$this->datestatus->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		return $sWrk;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort() {
		if ($this->DrillDown)
			return "`payment_date` DESC, `number` ASC";

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for a resetsort command
		if (strlen(@$_GET["cmd"]) > 0) {
			$sCmd = @$_GET["cmd"];
			if ($sCmd == "resetsort") {
				$this->setOrderBy("");
				$this->setStartGroup(1);
				$this->number->setSort("");
				$this->cash->setSort("");
				$this->payment_type->setSort("");
				$this->month->setSort("");
				$this->payment_date->setSort("");
				$this->months_paid->setSort("");
				$this->capturer->setSort("");
				$this->datestatus->setSort("");
			}

		// Check for an Order parameter
		} elseif (@$_GET["order"] <> "") {
			$this->CurrentOrder = ewr_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->number, $bCtrl); // number
			$this->UpdateSort($this->cash, $bCtrl); // cash
			$this->UpdateSort($this->payment_type, $bCtrl); // payment_type
			$this->UpdateSort($this->month, $bCtrl); // month
			$this->UpdateSort($this->payment_date, $bCtrl); // payment_date
			$this->UpdateSort($this->months_paid, $bCtrl); // months_paid
			$this->UpdateSort($this->capturer, $bCtrl); // capturer
			$this->UpdateSort($this->datestatus, $bCtrl); // datestatus
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}

		// Set up default sort
		if ($this->getOrderBy() == "") {
			$this->setOrderBy("`payment_date` DESC, `number` ASC");
			$this->payment_date->setSort("DESC");
			$this->number->setSort("ASC");
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
if (!isset($GM58_Payments_summary)) $GM58_Payments_summary = new crGM58_Payments_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$GM58_Payments_summary;

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
var GM58_Payments_summary = new ewr_Page("GM58_Payments_summary");

// Page properties
GM58_Payments_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = GM58_Payments_summary.PageID;

// Extend page with Chart_Rendering function
GM58_Payments_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
GM58_Payments_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fGM58_Paymentssummary = new ewr_Form("fGM58_Paymentssummary");

// Validate method
fGM58_Paymentssummary.Validate = function() {
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
fGM58_Paymentssummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fGM58_Paymentssummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fGM58_Paymentssummary.ValidateRequired = false; // No JavaScript validation
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
<form name="fGM58_Paymentssummary" id="fGM58_Paymentssummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fGM58_Paymentssummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_number" class="ewCell form-group">
	<label for="sv_number" class="ewSearchCaption ewLabel"><?php echo $Page->number->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_number" id="so_number" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->number->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->number->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->number->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->number->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->number->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->number->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->number->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->number->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->number->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->number->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->number->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->number->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->number->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->number->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_number" id="sv_number" name="sv_number" size="30" maxlength="111" placeholder="<?php echo $Page->number->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->number->SearchValue) ?>"<?php echo $Page->number->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_number" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_number" style="display: none">
<?php ewr_PrependClass($Page->number->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_number" id="sv2_number" name="sv2_number" size="30" maxlength="111" placeholder="<?php echo $Page->number->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->number->SearchValue2) ?>"<?php echo $Page->number->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_cash" class="ewCell form-group">
	<label for="sv_cash" class="ewSearchCaption ewLabel"><?php echo $Page->cash->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_cash" id="so_cash" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->cash->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->cash->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->cash->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->cash->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->cash->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->cash->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->cash->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->cash->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->cash->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->cash->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_cash" id="sv_cash" name="sv_cash" size="30" placeholder="<?php echo $Page->cash->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->cash->SearchValue) ?>"<?php echo $Page->cash->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_cash" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_cash" style="display: none">
<?php ewr_PrependClass($Page->cash->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_cash" id="sv2_cash" name="sv2_cash" size="30" placeholder="<?php echo $Page->cash->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->cash->SearchValue2) ?>"<?php echo $Page->cash->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_3" class="ewRow">
<div id="c_payment_type" class="ewCell form-group">
	<label for="sv_payment_type" class="ewSearchCaption ewLabel"><?php echo $Page->payment_type->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_payment_type" id="so_payment_type" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->payment_type->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->payment_type->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->payment_type->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->payment_type->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->payment_type->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->payment_type->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->payment_type->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->payment_type->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->payment_type->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->payment_type->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->payment_type->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->payment_type->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->payment_type->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->payment_type->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_payment_type" id="sv_payment_type" name="sv_payment_type" size="30" maxlength="111" placeholder="<?php echo $Page->payment_type->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->payment_type->SearchValue) ?>"<?php echo $Page->payment_type->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_payment_type" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_payment_type" style="display: none">
<?php ewr_PrependClass($Page->payment_type->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_payment_type" id="sv2_payment_type" name="sv2_payment_type" size="30" maxlength="111" placeholder="<?php echo $Page->payment_type->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->payment_type->SearchValue2) ?>"<?php echo $Page->payment_type->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_4" class="ewRow">
<div id="c_month" class="ewCell form-group">
	<label for="sv_month" class="ewSearchCaption ewLabel"><?php echo $Page->month->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_month" id="so_month" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->month->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->month->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->month->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->month->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->month->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->month->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->month->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->month->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->month->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->month->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->month->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->month->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->month->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->month->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_month" id="sv_month" name="sv_month" size="30" maxlength="111" placeholder="<?php echo $Page->month->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->month->SearchValue) ?>"<?php echo $Page->month->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_month" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_month" style="display: none">
<?php ewr_PrependClass($Page->month->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_month" id="sv2_month" name="sv2_month" size="30" maxlength="111" placeholder="<?php echo $Page->month->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->month->SearchValue2) ?>"<?php echo $Page->month->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_5" class="ewRow">
<div id="c_payment_date" class="ewCell form-group">
	<label for="sv_payment_date" class="ewSearchCaption ewLabel"><?php echo $Page->payment_date->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_payment_date" id="so_payment_date" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->payment_date->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->payment_date->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->payment_date->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->payment_date->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->payment_date->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->payment_date->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->payment_date->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->payment_date->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->payment_date->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->payment_date->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->payment_date->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->payment_date->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->payment_date->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->payment_date->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_payment_date" id="sv_payment_date" name="sv_payment_date" size="30" maxlength="111" placeholder="<?php echo $Page->payment_date->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->payment_date->SearchValue) ?>"<?php echo $Page->payment_date->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_payment_date" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_payment_date" style="display: none">
<?php ewr_PrependClass($Page->payment_date->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_payment_date" id="sv2_payment_date" name="sv2_payment_date" size="30" maxlength="111" placeholder="<?php echo $Page->payment_date->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->payment_date->SearchValue2) ?>"<?php echo $Page->payment_date->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_6" class="ewRow">
<div id="c_months_paid" class="ewCell form-group">
	<label for="sv_months_paid" class="ewSearchCaption ewLabel"><?php echo $Page->months_paid->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_months_paid" id="so_months_paid" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->months_paid->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->months_paid->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->months_paid->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->months_paid->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->months_paid->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->months_paid->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->months_paid->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->months_paid->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->months_paid->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->months_paid->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->months_paid->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->months_paid->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->months_paid->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->months_paid->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_months_paid" id="sv_months_paid" name="sv_months_paid" size="30" maxlength="111" placeholder="<?php echo $Page->months_paid->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->months_paid->SearchValue) ?>"<?php echo $Page->months_paid->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_months_paid" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_months_paid" style="display: none">
<?php ewr_PrependClass($Page->months_paid->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_months_paid" id="sv2_months_paid" name="sv2_months_paid" size="30" maxlength="111" placeholder="<?php echo $Page->months_paid->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->months_paid->SearchValue2) ?>"<?php echo $Page->months_paid->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_7" class="ewRow">
<div id="c_capturer" class="ewCell form-group">
	<label for="sv_capturer" class="ewSearchCaption ewLabel"><?php echo $Page->capturer->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_capturer" id="so_capturer" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->capturer->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->capturer->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->capturer->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->capturer->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->capturer->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->capturer->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->capturer->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->capturer->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->capturer->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->capturer->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->capturer->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->capturer->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->capturer->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->capturer->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_capturer" id="sv_capturer" name="sv_capturer" size="30" maxlength="255" placeholder="<?php echo $Page->capturer->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->capturer->SearchValue) ?>"<?php echo $Page->capturer->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_capturer" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_capturer" style="display: none">
<?php ewr_PrependClass($Page->capturer->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="GM58_Payments" data-field="x_capturer" id="sv2_capturer" name="sv2_capturer" size="30" maxlength="255" placeholder="<?php echo $Page->capturer->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->capturer->SearchValue2) ?>"<?php echo $Page->capturer->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fGM58_Paymentssummary.Init();
fGM58_Paymentssummary.FilterList = <?php echo $Page->GetFilterList() ?>;
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
<?php include "GM58_Paymentssmrypager.php" ?>
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
<?php if ($Page->number->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="number"><div class="GM58_Payments_number"><span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="number">
<?php if ($Page->SortUrl($Page->number) == "") { ?>
		<div class="ewTableHeaderBtn GM58_Payments_number">
			<span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_number', false, '<?php echo $Page->number->RangeFrom; ?>', '<?php echo $Page->number->RangeTo; ?>');" id="x_number<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer GM58_Payments_number" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->number) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->number->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->number->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_number', false, '<?php echo $Page->number->RangeFrom; ?>', '<?php echo $Page->number->RangeTo; ?>');" id="x_number<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->cash->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="cash"><div class="GM58_Payments_cash"><span class="ewTableHeaderCaption"><?php echo $Page->cash->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="cash">
<?php if ($Page->SortUrl($Page->cash) == "") { ?>
		<div class="ewTableHeaderBtn GM58_Payments_cash">
			<span class="ewTableHeaderCaption"><?php echo $Page->cash->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_cash', false, '<?php echo $Page->cash->RangeFrom; ?>', '<?php echo $Page->cash->RangeTo; ?>');" id="x_cash<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer GM58_Payments_cash" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->cash) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->cash->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->cash->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->cash->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_cash', false, '<?php echo $Page->cash->RangeFrom; ?>', '<?php echo $Page->cash->RangeTo; ?>');" id="x_cash<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->payment_type->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="payment_type"><div class="GM58_Payments_payment_type"><span class="ewTableHeaderCaption"><?php echo $Page->payment_type->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="payment_type">
<?php if ($Page->SortUrl($Page->payment_type) == "") { ?>
		<div class="ewTableHeaderBtn GM58_Payments_payment_type">
			<span class="ewTableHeaderCaption"><?php echo $Page->payment_type->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_payment_type', false, '<?php echo $Page->payment_type->RangeFrom; ?>', '<?php echo $Page->payment_type->RangeTo; ?>');" id="x_payment_type<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer GM58_Payments_payment_type" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->payment_type) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->payment_type->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->payment_type->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->payment_type->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_payment_type', false, '<?php echo $Page->payment_type->RangeFrom; ?>', '<?php echo $Page->payment_type->RangeTo; ?>');" id="x_payment_type<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->month->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="month"><div class="GM58_Payments_month"><span class="ewTableHeaderCaption"><?php echo $Page->month->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="month">
<?php if ($Page->SortUrl($Page->month) == "") { ?>
		<div class="ewTableHeaderBtn GM58_Payments_month">
			<span class="ewTableHeaderCaption"><?php echo $Page->month->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_month', false, '<?php echo $Page->month->RangeFrom; ?>', '<?php echo $Page->month->RangeTo; ?>');" id="x_month<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer GM58_Payments_month" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->month) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->month->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->month->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->month->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_month', false, '<?php echo $Page->month->RangeFrom; ?>', '<?php echo $Page->month->RangeTo; ?>');" id="x_month<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->payment_date->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="payment_date"><div class="GM58_Payments_payment_date"><span class="ewTableHeaderCaption"><?php echo $Page->payment_date->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="payment_date">
<?php if ($Page->SortUrl($Page->payment_date) == "") { ?>
		<div class="ewTableHeaderBtn GM58_Payments_payment_date">
			<span class="ewTableHeaderCaption"><?php echo $Page->payment_date->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_payment_date', false, '<?php echo $Page->payment_date->RangeFrom; ?>', '<?php echo $Page->payment_date->RangeTo; ?>');" id="x_payment_date<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer GM58_Payments_payment_date" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->payment_date) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->payment_date->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->payment_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->payment_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_payment_date', false, '<?php echo $Page->payment_date->RangeFrom; ?>', '<?php echo $Page->payment_date->RangeTo; ?>');" id="x_payment_date<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->months_paid->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="months_paid"><div class="GM58_Payments_months_paid"><span class="ewTableHeaderCaption"><?php echo $Page->months_paid->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="months_paid">
<?php if ($Page->SortUrl($Page->months_paid) == "") { ?>
		<div class="ewTableHeaderBtn GM58_Payments_months_paid">
			<span class="ewTableHeaderCaption"><?php echo $Page->months_paid->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_months_paid', false, '<?php echo $Page->months_paid->RangeFrom; ?>', '<?php echo $Page->months_paid->RangeTo; ?>');" id="x_months_paid<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer GM58_Payments_months_paid" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->months_paid) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->months_paid->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->months_paid->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->months_paid->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_months_paid', false, '<?php echo $Page->months_paid->RangeFrom; ?>', '<?php echo $Page->months_paid->RangeTo; ?>');" id="x_months_paid<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->capturer->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="capturer"><div class="GM58_Payments_capturer"><span class="ewTableHeaderCaption"><?php echo $Page->capturer->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="capturer">
<?php if ($Page->SortUrl($Page->capturer) == "") { ?>
		<div class="ewTableHeaderBtn GM58_Payments_capturer">
			<span class="ewTableHeaderCaption"><?php echo $Page->capturer->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_capturer', false, '<?php echo $Page->capturer->RangeFrom; ?>', '<?php echo $Page->capturer->RangeTo; ?>');" id="x_capturer<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer GM58_Payments_capturer" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->capturer) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->capturer->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->capturer->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->capturer->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_capturer', false, '<?php echo $Page->capturer->RangeFrom; ?>', '<?php echo $Page->capturer->RangeTo; ?>');" id="x_capturer<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="datestatus"><div class="GM58_Payments_datestatus"><span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="datestatus">
<?php if ($Page->SortUrl($Page->datestatus) == "") { ?>
		<div class="ewTableHeaderBtn GM58_Payments_datestatus">
			<span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_datestatus', false, '<?php echo $Page->datestatus->RangeFrom; ?>', '<?php echo $Page->datestatus->RangeTo; ?>');" id="x_datestatus<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer GM58_Payments_datestatus" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->datestatus) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->datestatus->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->datestatus->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'GM58_Payments_datestatus', false, '<?php echo $Page->datestatus->RangeFrom; ?>', '<?php echo $Page->datestatus->RangeTo; ?>');" id="x_datestatus<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
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
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_GM58_Payments_number"<?php echo $Page->number->ViewAttributes() ?>><?php echo $Page->number->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->cash->Visible) { ?>
		<td data-field="cash"<?php echo $Page->cash->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_GM58_Payments_cash"<?php echo $Page->cash->ViewAttributes() ?>><?php echo $Page->cash->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->payment_type->Visible) { ?>
		<td data-field="payment_type"<?php echo $Page->payment_type->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_GM58_Payments_payment_type"<?php echo $Page->payment_type->ViewAttributes() ?>><?php echo $Page->payment_type->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->month->Visible) { ?>
		<td data-field="month"<?php echo $Page->month->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_GM58_Payments_month"<?php echo $Page->month->ViewAttributes() ?>><?php echo $Page->month->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->payment_date->Visible) { ?>
		<td data-field="payment_date"<?php echo $Page->payment_date->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_GM58_Payments_payment_date"<?php echo $Page->payment_date->ViewAttributes() ?>><?php echo $Page->payment_date->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->months_paid->Visible) { ?>
		<td data-field="months_paid"<?php echo $Page->months_paid->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_GM58_Payments_months_paid"<?php echo $Page->months_paid->ViewAttributes() ?>><?php echo $Page->months_paid->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->capturer->Visible) { ?>
		<td data-field="capturer"<?php echo $Page->capturer->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_GM58_Payments_capturer"<?php echo $Page->capturer->ViewAttributes() ?>><?php echo $Page->capturer->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_GM58_Payments_datestatus"<?php echo $Page->datestatus->ViewAttributes() ?>><?php echo $Page->datestatus->ListViewValue() ?></span></td>
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
<?php
	$Page->ResetAttrs();
	$Page->cash->Count = $Page->GrandCnt[2];
	$Page->cash->SumValue = $Page->GrandSmry[2]; // Load SUM
	$Page->RowTotalSubType = EWR_ROWTOTAL_SUM;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->cash->Visible) { ?>
		<td data-field="cash"<?php echo $Page->cash->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></span>
<span data-class="tpts_GM58_Payments_cash"<?php echo $Page->cash->ViewAttributes() ?>><?php echo $Page->cash->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->payment_type->Visible) { ?>
		<td data-field="payment_type"<?php echo $Page->payment_type->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->month->Visible) { ?>
		<td data-field="month"<?php echo $Page->month->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->payment_date->Visible) { ?>
		<td data-field="payment_date"<?php echo $Page->payment_date->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->months_paid->Visible) { ?>
		<td data-field="months_paid"<?php echo $Page->months_paid->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->capturer->Visible) { ?>
		<td data-field="capturer"<?php echo $Page->capturer->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && TRUE) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "GM58_Paymentssmrypager.php" ?>
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
<?php include "GM58_Paymentssmrypager.php" ?>
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
