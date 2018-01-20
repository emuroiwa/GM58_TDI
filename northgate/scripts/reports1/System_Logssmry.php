<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "System_Logssmryinfo.php" ?>
<?php

//
// Page class
//

$System_Logs_summary = NULL; // Initialize page object first

class crSystem_Logs_summary extends crSystem_Logs {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{3080AF49-5443-4264-8421-3510B6183D7C}";

	// Page object name
	var $PageObjName = 'System_Logs_summary';

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

		// Table object (System_Logs)
		if (!isset($GLOBALS["System_Logs"])) {
			$GLOBALS["System_Logs"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["System_Logs"];
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
			define("EWR_TABLE_NAME", 'System Logs', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fSystem_Logssummary";
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
		$this->details->PlaceHolder = $this->details->FldCaption();
		$this->detailsdate->PlaceHolder = $this->detailsdate->FldCaption();
		$this->user->PlaceHolder = $this->user->FldCaption();
		$this->name->PlaceHolder = $this->name->FldCaption();
		$this->surname->PlaceHolder = $this->surname->FldCaption();

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
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_System_Logs\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_System_Logs',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fSystem_Logssummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fSystem_Logssummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fSystem_Logssummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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

		$nDtls = 7;
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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->details->SelectionList = "";
		$this->details->DefaultSelectionList = "";
		$this->details->ValueList = "";
		$this->user->SelectionList = "";
		$this->user->DefaultSelectionList = "";
		$this->user->ValueList = "";
		$this->name->SelectionList = "";
		$this->name->DefaultSelectionList = "";
		$this->name->ValueList = "";
		$this->surname->SelectionList = "";
		$this->surname->DefaultSelectionList = "";
		$this->surname->ValueList = "";

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
				$this->FirstRowData['details'] = ewr_Conv($rs->fields('details'),200);
				$this->FirstRowData['detailsdate'] = ewr_Conv($rs->fields('detailsdate'),135);
				$this->FirstRowData['user'] = ewr_Conv($rs->fields('user'),200);
				$this->FirstRowData['id_logs'] = ewr_Conv($rs->fields('id_logs'),3);
				$this->FirstRowData['id'] = ewr_Conv($rs->fields('id'),20);
				$this->FirstRowData['name'] = ewr_Conv($rs->fields('name'),200);
				$this->FirstRowData['surname'] = ewr_Conv($rs->fields('surname'),200);
				$this->FirstRowData['sex'] = ewr_Conv($rs->fields('sex'),200);
				$this->FirstRowData['_email'] = ewr_Conv($rs->fields('email'),200);
				$this->FirstRowData['account'] = ewr_Conv($rs->fields('account'),200);
				$this->FirstRowData['address'] = ewr_Conv($rs->fields('address'),200);
				$this->FirstRowData['department'] = ewr_Conv($rs->fields('department'),200);
				$this->FirstRowData['username'] = ewr_Conv($rs->fields('username'),200);
				$this->FirstRowData['password'] = ewr_Conv($rs->fields('password'),200);
				$this->FirstRowData['idnumber'] = ewr_Conv($rs->fields('idnumber'),200);
				$this->FirstRowData['status'] = ewr_Conv($rs->fields('status'),200);
				$this->FirstRowData['date'] = ewr_Conv($rs->fields('date'),200);
				$this->FirstRowData['access'] = ewr_Conv($rs->fields('access'),200);
				$this->FirstRowData['suspend'] = ewr_Conv($rs->fields('suspend'),200);
				$this->FirstRowData['logtype'] = ewr_Conv($rs->fields('logtype'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->details->setDbValue($rs->fields('details'));
			$this->detailsdate->setDbValue($rs->fields('detailsdate'));
			$this->user->setDbValue($rs->fields('user'));
			$this->id_logs->setDbValue($rs->fields('id_logs'));
			$this->id->setDbValue($rs->fields('id'));
			$this->name->setDbValue($rs->fields('name'));
			$this->surname->setDbValue($rs->fields('surname'));
			$this->sex->setDbValue($rs->fields('sex'));
			$this->_email->setDbValue($rs->fields('email'));
			$this->account->setDbValue($rs->fields('account'));
			$this->address->setDbValue($rs->fields('address'));
			$this->department->setDbValue($rs->fields('department'));
			$this->username->setDbValue($rs->fields('username'));
			$this->password->setDbValue($rs->fields('password'));
			$this->idnumber->setDbValue($rs->fields('idnumber'));
			$this->status->setDbValue($rs->fields('status'));
			$this->date->setDbValue($rs->fields('date'));
			$this->access->setDbValue($rs->fields('access'));
			$this->suspend->setDbValue($rs->fields('suspend'));
			$this->logtype->setDbValue($rs->fields('logtype'));
			$this->Val[1] = $this->details->CurrentValue;
			$this->Val[2] = $this->detailsdate->CurrentValue;
			$this->Val[3] = $this->user->CurrentValue;
			$this->Val[4] = $this->name->CurrentValue;
			$this->Val[5] = $this->surname->CurrentValue;
			$this->Val[6] = $this->logtype->CurrentValue;
		} else {
			$this->details->setDbValue("");
			$this->detailsdate->setDbValue("");
			$this->user->setDbValue("");
			$this->id_logs->setDbValue("");
			$this->id->setDbValue("");
			$this->name->setDbValue("");
			$this->surname->setDbValue("");
			$this->sex->setDbValue("");
			$this->_email->setDbValue("");
			$this->account->setDbValue("");
			$this->address->setDbValue("");
			$this->department->setDbValue("");
			$this->username->setDbValue("");
			$this->password->setDbValue("");
			$this->idnumber->setDbValue("");
			$this->status->setDbValue("");
			$this->date->setDbValue("");
			$this->access->setDbValue("");
			$this->suspend->setDbValue("");
			$this->logtype->setDbValue("");
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
			// Build distinct values for details

			if ($popupname == 'System_Logs_details') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->details, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->details->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->details->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->details->setDbValue($rswrk->fields[0]);
					if (is_null($this->details->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->details->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->details->ViewValue = $this->details->CurrentValue;
						ewr_SetupDistinctValues($this->details->ValueList, $this->details->CurrentValue, $this->details->ViewValue, FALSE, $this->details->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->details->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->details->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->details;
			}

			// Build distinct values for user
			if ($popupname == 'System_Logs_user') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->user, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->user->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->user->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->user->setDbValue($rswrk->fields[0]);
					if (is_null($this->user->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->user->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->user->ViewValue = $this->user->CurrentValue;
						ewr_SetupDistinctValues($this->user->ValueList, $this->user->CurrentValue, $this->user->ViewValue, FALSE, $this->user->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->user->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->user->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->user;
			}

			// Build distinct values for name
			if ($popupname == 'System_Logs_name') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->name, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->name->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->name->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->name->setDbValue($rswrk->fields[0]);
					if (is_null($this->name->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->name->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->name->ViewValue = $this->name->CurrentValue;
						ewr_SetupDistinctValues($this->name->ValueList, $this->name->CurrentValue, $this->name->ViewValue, FALSE, $this->name->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->name->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->name->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->name;
			}

			// Build distinct values for surname
			if ($popupname == 'System_Logs_surname') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->surname, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->surname->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->surname->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->surname->setDbValue($rswrk->fields[0]);
					if (is_null($this->surname->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->surname->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->surname->ViewValue = $this->surname->CurrentValue;
						ewr_SetupDistinctValues($this->surname->ValueList, $this->surname->CurrentValue, $this->surname->ViewValue, FALSE, $this->surname->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->surname->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->surname->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->surname;
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
				$this->ClearSessionSelection('details');
				$this->ClearSessionSelection('user');
				$this->ClearSessionSelection('name');
				$this->ClearSessionSelection('surname');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get details selected values

		if (is_array(@$_SESSION["sel_System_Logs_details"])) {
			$this->LoadSelectionFromSession('details');
		} elseif (@$_SESSION["sel_System_Logs_details"] == EWR_INIT_VALUE) { // Select all
			$this->details->SelectionList = "";
		}

		// Get user selected values
		if (is_array(@$_SESSION["sel_System_Logs_user"])) {
			$this->LoadSelectionFromSession('user');
		} elseif (@$_SESSION["sel_System_Logs_user"] == EWR_INIT_VALUE) { // Select all
			$this->user->SelectionList = "";
		}

		// Get name selected values
		if (is_array(@$_SESSION["sel_System_Logs_name"])) {
			$this->LoadSelectionFromSession('name');
		} elseif (@$_SESSION["sel_System_Logs_name"] == EWR_INIT_VALUE) { // Select all
			$this->name->SelectionList = "";
		}

		// Get surname selected values
		if (is_array(@$_SESSION["sel_System_Logs_surname"])) {
			$this->LoadSelectionFromSession('surname');
		} elseif (@$_SESSION["sel_System_Logs_surname"] == EWR_INIT_VALUE) { // Select all
			$this->surname->SelectionList = "";
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

			// details
			$this->details->HrefValue = "";

			// detailsdate
			$this->detailsdate->HrefValue = "";

			// user
			$this->user->HrefValue = "";

			// name
			$this->name->HrefValue = "";

			// surname
			$this->surname->HrefValue = "";

			// logtype
			$this->logtype->HrefValue = "";
		} else {

			// details
			$this->details->ViewValue = $this->details->CurrentValue;
			$this->details->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// detailsdate
			$this->detailsdate->ViewValue = $this->detailsdate->CurrentValue;
			$this->detailsdate->ViewValue = ewr_FormatDateTime($this->detailsdate->ViewValue, 5);
			$this->detailsdate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// user
			$this->user->ViewValue = $this->user->CurrentValue;
			$this->user->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// name
			$this->name->ViewValue = $this->name->CurrentValue;
			$this->name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// surname
			$this->surname->ViewValue = $this->surname->CurrentValue;
			$this->surname->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// logtype
			$this->logtype->ViewValue = $this->logtype->CurrentValue;
			$this->logtype->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// details
			$this->details->HrefValue = "";

			// detailsdate
			$this->detailsdate->HrefValue = "";

			// user
			$this->user->HrefValue = "";

			// name
			$this->name->HrefValue = "";

			// surname
			$this->surname->HrefValue = "";

			// logtype
			$this->logtype->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// details
			$CurrentValue = $this->details->CurrentValue;
			$ViewValue = &$this->details->ViewValue;
			$ViewAttrs = &$this->details->ViewAttrs;
			$CellAttrs = &$this->details->CellAttrs;
			$HrefValue = &$this->details->HrefValue;
			$LinkAttrs = &$this->details->LinkAttrs;
			$this->Cell_Rendered($this->details, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// detailsdate
			$CurrentValue = $this->detailsdate->CurrentValue;
			$ViewValue = &$this->detailsdate->ViewValue;
			$ViewAttrs = &$this->detailsdate->ViewAttrs;
			$CellAttrs = &$this->detailsdate->CellAttrs;
			$HrefValue = &$this->detailsdate->HrefValue;
			$LinkAttrs = &$this->detailsdate->LinkAttrs;
			$this->Cell_Rendered($this->detailsdate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// user
			$CurrentValue = $this->user->CurrentValue;
			$ViewValue = &$this->user->ViewValue;
			$ViewAttrs = &$this->user->ViewAttrs;
			$CellAttrs = &$this->user->CellAttrs;
			$HrefValue = &$this->user->HrefValue;
			$LinkAttrs = &$this->user->LinkAttrs;
			$this->Cell_Rendered($this->user, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// name
			$CurrentValue = $this->name->CurrentValue;
			$ViewValue = &$this->name->ViewValue;
			$ViewAttrs = &$this->name->ViewAttrs;
			$CellAttrs = &$this->name->CellAttrs;
			$HrefValue = &$this->name->HrefValue;
			$LinkAttrs = &$this->name->LinkAttrs;
			$this->Cell_Rendered($this->name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// surname
			$CurrentValue = $this->surname->CurrentValue;
			$ViewValue = &$this->surname->ViewValue;
			$ViewAttrs = &$this->surname->ViewAttrs;
			$CellAttrs = &$this->surname->CellAttrs;
			$HrefValue = &$this->surname->HrefValue;
			$LinkAttrs = &$this->surname->LinkAttrs;
			$this->Cell_Rendered($this->surname, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// logtype
			$CurrentValue = $this->logtype->CurrentValue;
			$ViewValue = &$this->logtype->ViewValue;
			$ViewAttrs = &$this->logtype->ViewAttrs;
			$CellAttrs = &$this->logtype->CellAttrs;
			$HrefValue = &$this->logtype->HrefValue;
			$LinkAttrs = &$this->logtype->LinkAttrs;
			$this->Cell_Rendered($this->logtype, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->details->Visible) $this->DtlFldCount += 1;
		if ($this->detailsdate->Visible) $this->DtlFldCount += 1;
		if ($this->user->Visible) $this->DtlFldCount += 1;
		if ($this->name->Visible) $this->DtlFldCount += 1;
		if ($this->surname->Visible) $this->DtlFldCount += 1;
		if ($this->logtype->Visible) $this->DtlFldCount += 1;
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

			// Clear extended filter for field details
			if ($this->ClearExtFilter == 'System_Logs_details')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'details');

			// Clear extended filter for field user
			if ($this->ClearExtFilter == 'System_Logs_user')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'user');

			// Clear extended filter for field name
			if ($this->ClearExtFilter == 'System_Logs_name')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'name');

			// Clear extended filter for field surname
			if ($this->ClearExtFilter == 'System_Logs_surname')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'surname');

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->details->SearchValue, $this->details->SearchOperator, $this->details->SearchCondition, $this->details->SearchValue2, $this->details->SearchOperator2, 'details'); // Field details
			$this->SetSessionFilterValues($this->detailsdate->SearchValue, $this->detailsdate->SearchOperator, $this->detailsdate->SearchCondition, $this->detailsdate->SearchValue2, $this->detailsdate->SearchOperator2, 'detailsdate'); // Field detailsdate
			$this->SetSessionFilterValues($this->user->SearchValue, $this->user->SearchOperator, $this->user->SearchCondition, $this->user->SearchValue2, $this->user->SearchOperator2, 'user'); // Field user
			$this->SetSessionFilterValues($this->name->SearchValue, $this->name->SearchOperator, $this->name->SearchCondition, $this->name->SearchValue2, $this->name->SearchOperator2, 'name'); // Field name
			$this->SetSessionFilterValues($this->surname->SearchValue, $this->surname->SearchOperator, $this->surname->SearchCondition, $this->surname->SearchValue2, $this->surname->SearchOperator2, 'surname'); // Field surname

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field details
			if ($this->GetFilterValues($this->details)) {
				$bSetupFilter = TRUE;
			}

			// Field detailsdate
			if ($this->GetFilterValues($this->detailsdate)) {
				$bSetupFilter = TRUE;
			}

			// Field user
			if ($this->GetFilterValues($this->user)) {
				$bSetupFilter = TRUE;
			}

			// Field name
			if ($this->GetFilterValues($this->name)) {
				$bSetupFilter = TRUE;
			}

			// Field surname
			if ($this->GetFilterValues($this->surname)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionFilterValues($this->details); // Field details
			$this->GetSessionFilterValues($this->detailsdate); // Field detailsdate
			$this->GetSessionFilterValues($this->user); // Field user
			$this->GetSessionFilterValues($this->name); // Field name
			$this->GetSessionFilterValues($this->surname); // Field surname
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->details, $sFilter, FALSE, TRUE); // Field details
		$this->BuildExtendedFilter($this->detailsdate, $sFilter, FALSE, TRUE); // Field detailsdate
		$this->BuildExtendedFilter($this->user, $sFilter, FALSE, TRUE); // Field user
		$this->BuildExtendedFilter($this->name, $sFilter, FALSE, TRUE); // Field name
		$this->BuildExtendedFilter($this->surname, $sFilter, FALSE, TRUE); // Field surname

		// Save parms to session
		$this->SetSessionFilterValues($this->details->SearchValue, $this->details->SearchOperator, $this->details->SearchCondition, $this->details->SearchValue2, $this->details->SearchOperator2, 'details'); // Field details
		$this->SetSessionFilterValues($this->detailsdate->SearchValue, $this->detailsdate->SearchOperator, $this->detailsdate->SearchCondition, $this->detailsdate->SearchValue2, $this->detailsdate->SearchOperator2, 'detailsdate'); // Field detailsdate
		$this->SetSessionFilterValues($this->user->SearchValue, $this->user->SearchOperator, $this->user->SearchCondition, $this->user->SearchValue2, $this->user->SearchOperator2, 'user'); // Field user
		$this->SetSessionFilterValues($this->name->SearchValue, $this->name->SearchOperator, $this->name->SearchCondition, $this->name->SearchValue2, $this->name->SearchOperator2, 'name'); // Field name
		$this->SetSessionFilterValues($this->surname->SearchValue, $this->surname->SearchOperator, $this->surname->SearchCondition, $this->surname->SearchValue2, $this->surname->SearchOperator2, 'surname'); // Field surname

		// Setup filter
		if ($bSetupFilter) {

			// Field details
			$sWrk = "";
			$this->BuildExtendedFilter($this->details, $sWrk);
			ewr_LoadSelectionFromFilter($this->details, $sWrk, $this->details->SelectionList);
			$_SESSION['sel_System_Logs_details'] = ($this->details->SelectionList == "") ? EWR_INIT_VALUE : $this->details->SelectionList;

			// Field user
			$sWrk = "";
			$this->BuildExtendedFilter($this->user, $sWrk);
			ewr_LoadSelectionFromFilter($this->user, $sWrk, $this->user->SelectionList);
			$_SESSION['sel_System_Logs_user'] = ($this->user->SelectionList == "") ? EWR_INIT_VALUE : $this->user->SelectionList;

			// Field name
			$sWrk = "";
			$this->BuildExtendedFilter($this->name, $sWrk);
			ewr_LoadSelectionFromFilter($this->name, $sWrk, $this->name->SelectionList);
			$_SESSION['sel_System_Logs_name'] = ($this->name->SelectionList == "") ? EWR_INIT_VALUE : $this->name->SelectionList;

			// Field surname
			$sWrk = "";
			$this->BuildExtendedFilter($this->surname, $sWrk);
			ewr_LoadSelectionFromFilter($this->surname, $sWrk, $this->surname->SelectionList);
			$_SESSION['sel_System_Logs_surname'] = ($this->surname->SelectionList == "") ? EWR_INIT_VALUE : $this->surname->SelectionList;
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
		$this->GetSessionValue($fld->DropDownValue, 'sv_System_Logs_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_System_Logs_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_System_Logs_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_System_Logs_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_System_Logs_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_System_Logs_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_System_Logs_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_System_Logs_' . $parm] = $sv;
		$_SESSION['so_System_Logs_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_System_Logs_' . $parm] = $sv1;
		$_SESSION['so_System_Logs_' . $parm] = $so1;
		$_SESSION['sc_System_Logs_' . $parm] = $sc;
		$_SESSION['sv2_System_Logs_' . $parm] = $sv2;
		$_SESSION['so2_System_Logs_' . $parm] = $so2;
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
		$_SESSION["sel_System_Logs_$parm"] = "";
		$_SESSION["rf_System_Logs_$parm"] = "";
		$_SESSION["rt_System_Logs_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_System_Logs_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_System_Logs_$parm"];
		$fld->RangeTo = @$_SESSION["rt_System_Logs_$parm"];
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

		// Field details
		$this->SetDefaultExtFilter($this->details, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->details);
		$sWrk = "";
		$this->BuildExtendedFilter($this->details, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->details, $sWrk, $this->details->DefaultSelectionList);
		if (!$this->SearchCommand) $this->details->SelectionList = $this->details->DefaultSelectionList;

		// Field detailsdate
		$this->SetDefaultExtFilter($this->detailsdate, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->detailsdate);

		// Field user
		$this->SetDefaultExtFilter($this->user, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->user);
		$sWrk = "";
		$this->BuildExtendedFilter($this->user, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->user, $sWrk, $this->user->DefaultSelectionList);
		if (!$this->SearchCommand) $this->user->SelectionList = $this->user->DefaultSelectionList;

		// Field name
		$this->SetDefaultExtFilter($this->name, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->name);
		$sWrk = "";
		$this->BuildExtendedFilter($this->name, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->name, $sWrk, $this->name->DefaultSelectionList);
		if (!$this->SearchCommand) $this->name->SelectionList = $this->name->DefaultSelectionList;

		// Field surname
		$this->SetDefaultExtFilter($this->surname, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->surname);
		$sWrk = "";
		$this->BuildExtendedFilter($this->surname, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->surname, $sWrk, $this->surname->DefaultSelectionList);
		if (!$this->SearchCommand) $this->surname->SelectionList = $this->surname->DefaultSelectionList;

		/**
		* Set up default values for popup filters
		*/

		// Field details
		// $this->details->DefaultSelectionList = array("val1", "val2");
		// Field user
		// $this->user->DefaultSelectionList = array("val1", "val2");
		// Field name
		// $this->name->DefaultSelectionList = array("val1", "val2");
		// Field surname
		// $this->surname->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check details text filter
		if ($this->TextFilterApplied($this->details))
			return TRUE;

		// Check details popup filter
		if (!ewr_MatchedArray($this->details->DefaultSelectionList, $this->details->SelectionList))
			return TRUE;

		// Check detailsdate text filter
		if ($this->TextFilterApplied($this->detailsdate))
			return TRUE;

		// Check user text filter
		if ($this->TextFilterApplied($this->user))
			return TRUE;

		// Check user popup filter
		if (!ewr_MatchedArray($this->user->DefaultSelectionList, $this->user->SelectionList))
			return TRUE;

		// Check name text filter
		if ($this->TextFilterApplied($this->name))
			return TRUE;

		// Check name popup filter
		if (!ewr_MatchedArray($this->name->DefaultSelectionList, $this->name->SelectionList))
			return TRUE;

		// Check surname text filter
		if ($this->TextFilterApplied($this->surname))
			return TRUE;

		// Check surname popup filter
		if (!ewr_MatchedArray($this->surname->DefaultSelectionList, $this->surname->SelectionList))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList() {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field details
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->details, $sExtWrk);
		if (is_array($this->details->SelectionList))
			$sWrk = ewr_JoinArray($this->details->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->details->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field detailsdate
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->detailsdate, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->detailsdate->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field user
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->user, $sExtWrk);
		if (is_array($this->user->SelectionList))
			$sWrk = ewr_JoinArray($this->user->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->user->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field name
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->name, $sExtWrk);
		if (is_array($this->name->SelectionList))
			$sWrk = ewr_JoinArray($this->name->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->name->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field surname
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->surname, $sExtWrk);
		if (is_array($this->surname->SelectionList))
			$sWrk = ewr_JoinArray($this->surname->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->surname->FldCaption() . "</span>" . $sFilter . "</div>";
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

		// Field details
		$sWrk = "";
		if ($this->details->SearchValue <> "" || $this->details->SearchValue2 <> "") {
			$sWrk = "\"sv_details\":\"" . ewr_JsEncode2($this->details->SearchValue) . "\"," .
				"\"so_details\":\"" . ewr_JsEncode2($this->details->SearchOperator) . "\"," .
				"\"sc_details\":\"" . ewr_JsEncode2($this->details->SearchCondition) . "\"," .
				"\"sv2_details\":\"" . ewr_JsEncode2($this->details->SearchValue2) . "\"," .
				"\"so2_details\":\"" . ewr_JsEncode2($this->details->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->details->SelectionList <> EWR_INIT_VALUE) ? $this->details->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_details\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field detailsdate
		$sWrk = "";
		if ($this->detailsdate->SearchValue <> "" || $this->detailsdate->SearchValue2 <> "") {
			$sWrk = "\"sv_detailsdate\":\"" . ewr_JsEncode2($this->detailsdate->SearchValue) . "\"," .
				"\"so_detailsdate\":\"" . ewr_JsEncode2($this->detailsdate->SearchOperator) . "\"," .
				"\"sc_detailsdate\":\"" . ewr_JsEncode2($this->detailsdate->SearchCondition) . "\"," .
				"\"sv2_detailsdate\":\"" . ewr_JsEncode2($this->detailsdate->SearchValue2) . "\"," .
				"\"so2_detailsdate\":\"" . ewr_JsEncode2($this->detailsdate->SearchOperator2) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field user
		$sWrk = "";
		if ($this->user->SearchValue <> "" || $this->user->SearchValue2 <> "") {
			$sWrk = "\"sv_user\":\"" . ewr_JsEncode2($this->user->SearchValue) . "\"," .
				"\"so_user\":\"" . ewr_JsEncode2($this->user->SearchOperator) . "\"," .
				"\"sc_user\":\"" . ewr_JsEncode2($this->user->SearchCondition) . "\"," .
				"\"sv2_user\":\"" . ewr_JsEncode2($this->user->SearchValue2) . "\"," .
				"\"so2_user\":\"" . ewr_JsEncode2($this->user->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->user->SelectionList <> EWR_INIT_VALUE) ? $this->user->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_user\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field name
		$sWrk = "";
		if ($this->name->SearchValue <> "" || $this->name->SearchValue2 <> "") {
			$sWrk = "\"sv_name\":\"" . ewr_JsEncode2($this->name->SearchValue) . "\"," .
				"\"so_name\":\"" . ewr_JsEncode2($this->name->SearchOperator) . "\"," .
				"\"sc_name\":\"" . ewr_JsEncode2($this->name->SearchCondition) . "\"," .
				"\"sv2_name\":\"" . ewr_JsEncode2($this->name->SearchValue2) . "\"," .
				"\"so2_name\":\"" . ewr_JsEncode2($this->name->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->name->SelectionList <> EWR_INIT_VALUE) ? $this->name->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_name\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field surname
		$sWrk = "";
		if ($this->surname->SearchValue <> "" || $this->surname->SearchValue2 <> "") {
			$sWrk = "\"sv_surname\":\"" . ewr_JsEncode2($this->surname->SearchValue) . "\"," .
				"\"so_surname\":\"" . ewr_JsEncode2($this->surname->SearchOperator) . "\"," .
				"\"sc_surname\":\"" . ewr_JsEncode2($this->surname->SearchCondition) . "\"," .
				"\"sv2_surname\":\"" . ewr_JsEncode2($this->surname->SearchValue2) . "\"," .
				"\"so2_surname\":\"" . ewr_JsEncode2($this->surname->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->surname->SelectionList <> EWR_INIT_VALUE) ? $this->surname->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_surname\":\"" . ewr_JsEncode2($sWrk) . "\"";
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

		// Field details
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_details", $filter) || array_key_exists("so_details", $filter) ||
			array_key_exists("sc_details", $filter) ||
			array_key_exists("sv2_details", $filter) || array_key_exists("so2_details", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_details"], @$filter["so_details"], @$filter["sc_details"], @$filter["sv2_details"], @$filter["so2_details"], "details");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_details", $filter)) {
			$sWrk = $filter["sel_details"];
			$sWrk = explode("||", $sWrk);
			$this->details->SelectionList = $sWrk;
			$_SESSION["sel_System_Logs_details"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "details"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "details");
			$this->details->SelectionList = "";
			$_SESSION["sel_System_Logs_details"] = "";
		}

		// Field detailsdate
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_detailsdate", $filter) || array_key_exists("so_detailsdate", $filter) ||
			array_key_exists("sc_detailsdate", $filter) ||
			array_key_exists("sv2_detailsdate", $filter) || array_key_exists("so2_detailsdate", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_detailsdate"], @$filter["so_detailsdate"], @$filter["sc_detailsdate"], @$filter["sv2_detailsdate"], @$filter["so2_detailsdate"], "detailsdate");
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "detailsdate");
		}

		// Field user
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_user", $filter) || array_key_exists("so_user", $filter) ||
			array_key_exists("sc_user", $filter) ||
			array_key_exists("sv2_user", $filter) || array_key_exists("so2_user", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_user"], @$filter["so_user"], @$filter["sc_user"], @$filter["sv2_user"], @$filter["so2_user"], "user");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_user", $filter)) {
			$sWrk = $filter["sel_user"];
			$sWrk = explode("||", $sWrk);
			$this->user->SelectionList = $sWrk;
			$_SESSION["sel_System_Logs_user"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "user"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "user");
			$this->user->SelectionList = "";
			$_SESSION["sel_System_Logs_user"] = "";
		}

		// Field name
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_name", $filter) || array_key_exists("so_name", $filter) ||
			array_key_exists("sc_name", $filter) ||
			array_key_exists("sv2_name", $filter) || array_key_exists("so2_name", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_name"], @$filter["so_name"], @$filter["sc_name"], @$filter["sv2_name"], @$filter["so2_name"], "name");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_name", $filter)) {
			$sWrk = $filter["sel_name"];
			$sWrk = explode("||", $sWrk);
			$this->name->SelectionList = $sWrk;
			$_SESSION["sel_System_Logs_name"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "name"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "name");
			$this->name->SelectionList = "";
			$_SESSION["sel_System_Logs_name"] = "";
		}

		// Field surname
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_surname", $filter) || array_key_exists("so_surname", $filter) ||
			array_key_exists("sc_surname", $filter) ||
			array_key_exists("sv2_surname", $filter) || array_key_exists("so2_surname", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_surname"], @$filter["so_surname"], @$filter["sc_surname"], @$filter["sv2_surname"], @$filter["so2_surname"], "surname");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_surname", $filter)) {
			$sWrk = $filter["sel_surname"];
			$sWrk = explode("||", $sWrk);
			$this->surname->SelectionList = $sWrk;
			$_SESSION["sel_System_Logs_surname"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "surname"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "surname");
			$this->surname->SelectionList = "";
			$_SESSION["sel_System_Logs_surname"] = "";
		}
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		if (!$this->ExtendedFilterExist($this->details)) {
			if (is_array($this->details->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->details, "`details`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->details, $sFilter, "popup");
				$this->details->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->user)) {
			if (is_array($this->user->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->user, "`user`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->user, $sFilter, "popup");
				$this->user->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->name)) {
			if (is_array($this->name->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->name, "`name`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->name, $sFilter, "popup");
				$this->name->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->surname)) {
			if (is_array($this->surname->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->surname, "`surname`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->surname, $sFilter, "popup");
				$this->surname->CurrentFilter = $sFilter;
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
			return "`detailsdate` DESC";

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for a resetsort command
		if (strlen(@$_GET["cmd"]) > 0) {
			$sCmd = @$_GET["cmd"];
			if ($sCmd == "resetsort") {
				$this->setOrderBy("");
				$this->setStartGroup(1);
				$this->details->setSort("");
				$this->detailsdate->setSort("");
				$this->user->setSort("");
				$this->name->setSort("");
				$this->surname->setSort("");
				$this->logtype->setSort("");
			}

		// Check for an Order parameter
		} elseif (@$_GET["order"] <> "") {
			$this->CurrentOrder = ewr_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->details, $bCtrl); // details
			$this->UpdateSort($this->detailsdate, $bCtrl); // detailsdate
			$this->UpdateSort($this->user, $bCtrl); // user
			$this->UpdateSort($this->name, $bCtrl); // name
			$this->UpdateSort($this->surname, $bCtrl); // surname
			$this->UpdateSort($this->logtype, $bCtrl); // logtype
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}

		// Set up default sort
		if ($this->getOrderBy() == "") {
			$this->setOrderBy("`detailsdate` DESC");
			$this->detailsdate->setSort("DESC");
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
if (!isset($System_Logs_summary)) $System_Logs_summary = new crSystem_Logs_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$System_Logs_summary;

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
var System_Logs_summary = new ewr_Page("System_Logs_summary");

// Page properties
System_Logs_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = System_Logs_summary.PageID;

// Extend page with Chart_Rendering function
System_Logs_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
System_Logs_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fSystem_Logssummary = new ewr_Form("fSystem_Logssummary");

// Validate method
fSystem_Logssummary.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fSystem_Logssummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fSystem_Logssummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fSystem_Logssummary.ValidateRequired = false; // No JavaScript validation
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
<form name="fSystem_Logssummary" id="fSystem_Logssummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fSystem_Logssummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_details" class="ewCell form-group">
	<label for="sv_details" class="ewSearchCaption ewLabel"><?php echo $Page->details->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_details" id="so_details" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->details->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->details->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->details->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->details->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->details->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->details->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->details->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->details->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->details->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->details->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->details->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->details->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->details->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->details->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_details" id="sv_details" name="sv_details" size="30" maxlength="255" placeholder="<?php echo $Page->details->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->details->SearchValue) ?>"<?php echo $Page->details->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_details" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_details" style="display: none">
<?php ewr_PrependClass($Page->details->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_details" id="sv2_details" name="sv2_details" size="30" maxlength="255" placeholder="<?php echo $Page->details->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->details->SearchValue2) ?>"<?php echo $Page->details->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_detailsdate" class="ewCell form-group">
	<label for="sv_detailsdate" class="ewSearchCaption ewLabel"><?php echo $Page->detailsdate->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_detailsdate" id="so_detailsdate" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->detailsdate->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->detailsdate->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->detailsdate->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->detailsdate->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->detailsdate->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->detailsdate->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->detailsdate->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->detailsdate->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->detailsdate->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->detailsdate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_detailsdate" id="sv_detailsdate" name="sv_detailsdate" placeholder="<?php echo $Page->detailsdate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->detailsdate->SearchValue) ?>"<?php echo $Page->detailsdate->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_detailsdate" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_detailsdate" style="display: none">
<?php ewr_PrependClass($Page->detailsdate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_detailsdate" id="sv2_detailsdate" name="sv2_detailsdate" placeholder="<?php echo $Page->detailsdate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->detailsdate->SearchValue2) ?>"<?php echo $Page->detailsdate->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_3" class="ewRow">
<div id="c_user" class="ewCell form-group">
	<label for="sv_user" class="ewSearchCaption ewLabel"><?php echo $Page->user->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_user" id="so_user" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->user->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->user->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->user->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->user->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->user->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->user->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->user->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->user->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->user->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->user->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->user->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->user->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->user->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->user->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_user" id="sv_user" name="sv_user" size="30" maxlength="255" placeholder="<?php echo $Page->user->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->user->SearchValue) ?>"<?php echo $Page->user->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_user" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_user" style="display: none">
<?php ewr_PrependClass($Page->user->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_user" id="sv2_user" name="sv2_user" size="30" maxlength="255" placeholder="<?php echo $Page->user->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->user->SearchValue2) ?>"<?php echo $Page->user->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_4" class="ewRow">
<div id="c_name" class="ewCell form-group">
	<label for="sv_name" class="ewSearchCaption ewLabel"><?php echo $Page->name->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_name" id="so_name" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->name->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->name->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->name->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->name->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->name->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->name->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->name->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->name->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->name->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->name->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->name->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->name->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->name->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->name->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_name" id="sv_name" name="sv_name" size="30" maxlength="40" placeholder="<?php echo $Page->name->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->name->SearchValue) ?>"<?php echo $Page->name->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_name" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_name" style="display: none">
<?php ewr_PrependClass($Page->name->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_name" id="sv2_name" name="sv2_name" size="30" maxlength="40" placeholder="<?php echo $Page->name->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->name->SearchValue2) ?>"<?php echo $Page->name->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_5" class="ewRow">
<div id="c_surname" class="ewCell form-group">
	<label for="sv_surname" class="ewSearchCaption ewLabel"><?php echo $Page->surname->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_surname" id="so_surname" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->surname->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->surname->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->surname->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->surname->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->surname->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->surname->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->surname->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->surname->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->surname->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->surname->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->surname->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->surname->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->surname->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->surname->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_surname" id="sv_surname" name="sv_surname" size="30" maxlength="40" placeholder="<?php echo $Page->surname->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->surname->SearchValue) ?>"<?php echo $Page->surname->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_surname" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_surname" style="display: none">
<?php ewr_PrependClass($Page->surname->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="System_Logs" data-field="x_surname" id="sv2_surname" name="sv2_surname" size="30" maxlength="40" placeholder="<?php echo $Page->surname->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->surname->SearchValue2) ?>"<?php echo $Page->surname->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fSystem_Logssummary.Init();
fSystem_Logssummary.FilterList = <?php echo $Page->GetFilterList() ?>;
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
<?php include "System_Logssmrypager.php" ?>
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
<?php if ($Page->details->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="details"><div class="System_Logs_details"><span class="ewTableHeaderCaption"><?php echo $Page->details->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="details">
<?php if ($Page->SortUrl($Page->details) == "") { ?>
		<div class="ewTableHeaderBtn System_Logs_details">
			<span class="ewTableHeaderCaption"><?php echo $Page->details->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'System_Logs_details', false, '<?php echo $Page->details->RangeFrom; ?>', '<?php echo $Page->details->RangeTo; ?>');" id="x_details<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer System_Logs_details" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->details) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->details->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->details->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->details->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'System_Logs_details', false, '<?php echo $Page->details->RangeFrom; ?>', '<?php echo $Page->details->RangeTo; ?>');" id="x_details<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->detailsdate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="detailsdate"><div class="System_Logs_detailsdate"><span class="ewTableHeaderCaption"><?php echo $Page->detailsdate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="detailsdate">
<?php if ($Page->SortUrl($Page->detailsdate) == "") { ?>
		<div class="ewTableHeaderBtn System_Logs_detailsdate">
			<span class="ewTableHeaderCaption"><?php echo $Page->detailsdate->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer System_Logs_detailsdate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->detailsdate) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->detailsdate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->detailsdate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->detailsdate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->user->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="user"><div class="System_Logs_user"><span class="ewTableHeaderCaption"><?php echo $Page->user->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="user">
<?php if ($Page->SortUrl($Page->user) == "") { ?>
		<div class="ewTableHeaderBtn System_Logs_user">
			<span class="ewTableHeaderCaption"><?php echo $Page->user->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'System_Logs_user', false, '<?php echo $Page->user->RangeFrom; ?>', '<?php echo $Page->user->RangeTo; ?>');" id="x_user<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer System_Logs_user" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->user) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->user->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->user->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->user->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'System_Logs_user', false, '<?php echo $Page->user->RangeFrom; ?>', '<?php echo $Page->user->RangeTo; ?>');" id="x_user<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="name"><div class="System_Logs_name"><span class="ewTableHeaderCaption"><?php echo $Page->name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="name">
<?php if ($Page->SortUrl($Page->name) == "") { ?>
		<div class="ewTableHeaderBtn System_Logs_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->name->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'System_Logs_name', false, '<?php echo $Page->name->RangeFrom; ?>', '<?php echo $Page->name->RangeTo; ?>');" id="x_name<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer System_Logs_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->name) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'System_Logs_name', false, '<?php echo $Page->name->RangeFrom; ?>', '<?php echo $Page->name->RangeTo; ?>');" id="x_name<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="surname"><div class="System_Logs_surname"><span class="ewTableHeaderCaption"><?php echo $Page->surname->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="surname">
<?php if ($Page->SortUrl($Page->surname) == "") { ?>
		<div class="ewTableHeaderBtn System_Logs_surname">
			<span class="ewTableHeaderCaption"><?php echo $Page->surname->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'System_Logs_surname', false, '<?php echo $Page->surname->RangeFrom; ?>', '<?php echo $Page->surname->RangeTo; ?>');" id="x_surname<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer System_Logs_surname" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->surname) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->surname->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->surname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->surname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'System_Logs_surname', false, '<?php echo $Page->surname->RangeFrom; ?>', '<?php echo $Page->surname->RangeTo; ?>');" id="x_surname<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->logtype->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="logtype"><div class="System_Logs_logtype"><span class="ewTableHeaderCaption"><?php echo $Page->logtype->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="logtype">
<?php if ($Page->SortUrl($Page->logtype) == "") { ?>
		<div class="ewTableHeaderBtn System_Logs_logtype">
			<span class="ewTableHeaderCaption"><?php echo $Page->logtype->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer System_Logs_logtype" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->logtype) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->logtype->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->logtype->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->logtype->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
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
<?php if ($Page->details->Visible) { ?>
		<td data-field="details"<?php echo $Page->details->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_System_Logs_details"<?php echo $Page->details->ViewAttributes() ?>><?php echo $Page->details->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->detailsdate->Visible) { ?>
		<td data-field="detailsdate"<?php echo $Page->detailsdate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_System_Logs_detailsdate"<?php echo $Page->detailsdate->ViewAttributes() ?>><?php echo $Page->detailsdate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->user->Visible) { ?>
		<td data-field="user"<?php echo $Page->user->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_System_Logs_user"<?php echo $Page->user->ViewAttributes() ?>><?php echo $Page->user->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->name->Visible) { ?>
		<td data-field="name"<?php echo $Page->name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_System_Logs_name"<?php echo $Page->name->ViewAttributes() ?>><?php echo $Page->name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
		<td data-field="surname"<?php echo $Page->surname->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_System_Logs_surname"<?php echo $Page->surname->ViewAttributes() ?>><?php echo $Page->surname->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->logtype->Visible) { ?>
		<td data-field="logtype"<?php echo $Page->logtype->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_System_Logs_logtype"<?php echo $Page->logtype->ViewAttributes() ?>><?php echo $Page->logtype->ListViewValue() ?></span></td>
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
<?php include "System_Logssmrypager.php" ?>
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
<?php include "System_Logssmrypager.php" ?>
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
