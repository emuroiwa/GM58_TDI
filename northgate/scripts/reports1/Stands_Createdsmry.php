<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "Stands_Createdsmryinfo.php" ?>
<?php

//
// Page class
//

$Stands_Created_summary = NULL; // Initialize page object first

class crStands_Created_summary extends crStands_Created {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{3080AF49-5443-4264-8421-3510B6183D7C}";

	// Page object name
	var $PageObjName = 'Stands_Created_summary';

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

		// Table object (Stands_Created)
		if (!isset($GLOBALS["Stands_Created"])) {
			$GLOBALS["Stands_Created"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Stands_Created"];
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
			define("EWR_TABLE_NAME", 'Stands Created', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fStands_Createdsummary";
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
		$this->location->PlaceHolder = $this->location->FldCaption();
		$this->area->PlaceHolder = $this->area->FldCaption();
		$this->number->PlaceHolder = $this->number->FldCaption();
		$this->status->PlaceHolder = $this->status->FldCaption();
		$this->price->PlaceHolder = $this->price->FldCaption();
		$this->deposit->PlaceHolder = $this->deposit->FldCaption();
		$this->instalments->PlaceHolder = $this->instalments->FldCaption();
		$this->datestatus->PlaceHolder = $this->datestatus->FldCaption();
		$this->months_paid->PlaceHolder = $this->months_paid->FldCaption();

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
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_Stands_Created\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_Stands_Created',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fStands_Createdsummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fStands_Createdsummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fStands_Createdsummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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

		$nDtls = 12;
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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(TRUE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(TRUE,FALSE), array(TRUE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->location->SelectionList = "";
		$this->location->DefaultSelectionList = "";
		$this->location->ValueList = "";
		$this->area->SelectionList = "";
		$this->area->DefaultSelectionList = "";
		$this->area->ValueList = "";
		$this->number->SelectionList = "";
		$this->number->DefaultSelectionList = "";
		$this->number->ValueList = "";
		$this->status->SelectionList = "";
		$this->status->DefaultSelectionList = "";
		$this->status->ValueList = "";
		$this->price->SelectionList = "";
		$this->price->DefaultSelectionList = "";
		$this->price->ValueList = "";
		$this->deposit->SelectionList = "";
		$this->deposit->DefaultSelectionList = "";
		$this->deposit->ValueList = "";
		$this->instalments->SelectionList = "";
		$this->instalments->DefaultSelectionList = "";
		$this->instalments->ValueList = "";
		$this->datestatus->SelectionList = "";
		$this->datestatus->DefaultSelectionList = "";
		$this->datestatus->ValueList = "";
		$this->months_paid->SelectionList = "";
		$this->months_paid->DefaultSelectionList = "";
		$this->months_paid->ValueList = "";

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
				$this->FirstRowData['location'] = ewr_Conv($rs->fields('location'),200);
				$this->FirstRowData['area'] = ewr_Conv($rs->fields('area'),131);
				$this->FirstRowData['number'] = ewr_Conv($rs->fields('number'),200);
				$this->FirstRowData['status'] = ewr_Conv($rs->fields('status'),200);
				$this->FirstRowData['id_stand'] = ewr_Conv($rs->fields('id_stand'),3);
				$this->FirstRowData['price'] = ewr_Conv($rs->fields('price'),131);
				$this->FirstRowData['date'] = ewr_Conv($rs->fields('date'),135);
				$this->FirstRowData['deposit'] = ewr_Conv($rs->fields('deposit'),131);
				$this->FirstRowData['instalments'] = ewr_Conv($rs->fields('instalments'),200);
				$this->FirstRowData['datestatus'] = ewr_Conv($rs->fields('datestatus'),135);
				$this->FirstRowData['months_paid'] = ewr_Conv($rs->fields('months_paid'),200);
				$this->FirstRowData['start_instalment'] = ewr_Conv($rs->fields('start_instalment'),200);
				$this->FirstRowData['vatdate'] = ewr_Conv($rs->fields('vatdate'),135);
				$this->FirstRowData['vat'] = ewr_Conv($rs->fields('vat'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->location->setDbValue($rs->fields('location'));
			$this->area->setDbValue($rs->fields('area'));
			$this->number->setDbValue($rs->fields('number'));
			$this->status->setDbValue($rs->fields('status'));
			$this->id_stand->setDbValue($rs->fields('id_stand'));
			$this->price->setDbValue($rs->fields('price'));
			$this->date->setDbValue($rs->fields('date'));
			$this->deposit->setDbValue($rs->fields('deposit'));
			$this->instalments->setDbValue($rs->fields('instalments'));
			$this->datestatus->setDbValue($rs->fields('datestatus'));
			$this->months_paid->setDbValue($rs->fields('months_paid'));
			$this->start_instalment->setDbValue($rs->fields('start_instalment'));
			$this->vatdate->setDbValue($rs->fields('vatdate'));
			$this->vat->setDbValue($rs->fields('vat'));
			$this->Val[1] = $this->location->CurrentValue;
			$this->Val[2] = $this->area->CurrentValue;
			$this->Val[3] = $this->number->CurrentValue;
			$this->Val[4] = $this->status->CurrentValue;
			$this->Val[5] = $this->price->CurrentValue;
			$this->Val[6] = $this->deposit->CurrentValue;
			$this->Val[7] = $this->instalments->CurrentValue;
			$this->Val[8] = $this->datestatus->CurrentValue;
			$this->Val[9] = $this->months_paid->CurrentValue;
			$this->Val[10] = $this->vatdate->CurrentValue;
			$this->Val[11] = $this->vat->CurrentValue;
		} else {
			$this->location->setDbValue("");
			$this->area->setDbValue("");
			$this->number->setDbValue("");
			$this->status->setDbValue("");
			$this->id_stand->setDbValue("");
			$this->price->setDbValue("");
			$this->date->setDbValue("");
			$this->deposit->setDbValue("");
			$this->instalments->setDbValue("");
			$this->datestatus->setDbValue("");
			$this->months_paid->setDbValue("");
			$this->start_instalment->setDbValue("");
			$this->vatdate->setDbValue("");
			$this->vat->setDbValue("");
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
			// Build distinct values for location

			if ($popupname == 'Stands_Created_location') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->location, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->location->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->location->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->location->setDbValue($rswrk->fields[0]);
					if (is_null($this->location->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->location->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->location->ViewValue = $this->location->CurrentValue;
						ewr_SetupDistinctValues($this->location->ValueList, $this->location->CurrentValue, $this->location->ViewValue, FALSE, $this->location->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->location->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->location->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->location;
			}

			// Build distinct values for area
			if ($popupname == 'Stands_Created_area') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->area, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->area->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->area->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->area->setDbValue($rswrk->fields[0]);
					if (is_null($this->area->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->area->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->area->ViewValue = $this->area->CurrentValue;
						ewr_SetupDistinctValues($this->area->ValueList, $this->area->CurrentValue, $this->area->ViewValue, FALSE, $this->area->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->area->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->area->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->area;
			}

			// Build distinct values for number
			if ($popupname == 'Stands_Created_number') {
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

			// Build distinct values for status
			if ($popupname == 'Stands_Created_status') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->status, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->status->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->status->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->status->setDbValue($rswrk->fields[0]);
					if (is_null($this->status->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->status->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->status->ViewValue = $this->status->CurrentValue;
						ewr_SetupDistinctValues($this->status->ValueList, $this->status->CurrentValue, $this->status->ViewValue, FALSE, $this->status->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->status->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->status->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->status;
			}

			// Build distinct values for price
			if ($popupname == 'Stands_Created_price') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->price, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->price->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->price->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->price->setDbValue($rswrk->fields[0]);
					if (is_null($this->price->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->price->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->price->ViewValue = $this->price->CurrentValue;
						ewr_SetupDistinctValues($this->price->ValueList, $this->price->CurrentValue, $this->price->ViewValue, FALSE, $this->price->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->price->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->price->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->price;
			}

			// Build distinct values for deposit
			if ($popupname == 'Stands_Created_deposit') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->deposit, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->deposit->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->deposit->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->deposit->setDbValue($rswrk->fields[0]);
					if (is_null($this->deposit->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->deposit->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->deposit->ViewValue = $this->deposit->CurrentValue;
						ewr_SetupDistinctValues($this->deposit->ValueList, $this->deposit->CurrentValue, $this->deposit->ViewValue, FALSE, $this->deposit->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->deposit->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->deposit->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->deposit;
			}

			// Build distinct values for instalments
			if ($popupname == 'Stands_Created_instalments') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->instalments, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->instalments->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->instalments->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->instalments->setDbValue($rswrk->fields[0]);
					if (is_null($this->instalments->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->instalments->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->instalments->ViewValue = $this->instalments->CurrentValue;
						ewr_SetupDistinctValues($this->instalments->ValueList, $this->instalments->CurrentValue, $this->instalments->ViewValue, FALSE, $this->instalments->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->instalments->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->instalments->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->instalments;
			}

			// Build distinct values for datestatus
			if ($popupname == 'Stands_Created_datestatus') {
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
						$this->datestatus->ViewValue = $this->datestatus->CurrentValue;
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

			// Build distinct values for months_paid
			if ($popupname == 'Stands_Created_months_paid') {
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
				$this->ClearSessionSelection('location');
				$this->ClearSessionSelection('area');
				$this->ClearSessionSelection('number');
				$this->ClearSessionSelection('status');
				$this->ClearSessionSelection('price');
				$this->ClearSessionSelection('deposit');
				$this->ClearSessionSelection('instalments');
				$this->ClearSessionSelection('datestatus');
				$this->ClearSessionSelection('months_paid');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get location selected values

		if (is_array(@$_SESSION["sel_Stands_Created_location"])) {
			$this->LoadSelectionFromSession('location');
		} elseif (@$_SESSION["sel_Stands_Created_location"] == EWR_INIT_VALUE) { // Select all
			$this->location->SelectionList = "";
		}

		// Get area selected values
		if (is_array(@$_SESSION["sel_Stands_Created_area"])) {
			$this->LoadSelectionFromSession('area');
		} elseif (@$_SESSION["sel_Stands_Created_area"] == EWR_INIT_VALUE) { // Select all
			$this->area->SelectionList = "";
		}

		// Get number selected values
		if (is_array(@$_SESSION["sel_Stands_Created_number"])) {
			$this->LoadSelectionFromSession('number');
		} elseif (@$_SESSION["sel_Stands_Created_number"] == EWR_INIT_VALUE) { // Select all
			$this->number->SelectionList = "";
		}

		// Get status selected values
		if (is_array(@$_SESSION["sel_Stands_Created_status"])) {
			$this->LoadSelectionFromSession('status');
		} elseif (@$_SESSION["sel_Stands_Created_status"] == EWR_INIT_VALUE) { // Select all
			$this->status->SelectionList = "";
		}

		// Get price selected values
		if (is_array(@$_SESSION["sel_Stands_Created_price"])) {
			$this->LoadSelectionFromSession('price');
		} elseif (@$_SESSION["sel_Stands_Created_price"] == EWR_INIT_VALUE) { // Select all
			$this->price->SelectionList = "";
		}

		// Get deposit selected values
		if (is_array(@$_SESSION["sel_Stands_Created_deposit"])) {
			$this->LoadSelectionFromSession('deposit');
		} elseif (@$_SESSION["sel_Stands_Created_deposit"] == EWR_INIT_VALUE) { // Select all
			$this->deposit->SelectionList = "";
		}

		// Get instalments selected values
		if (is_array(@$_SESSION["sel_Stands_Created_instalments"])) {
			$this->LoadSelectionFromSession('instalments');
		} elseif (@$_SESSION["sel_Stands_Created_instalments"] == EWR_INIT_VALUE) { // Select all
			$this->instalments->SelectionList = "";
		}

		// Get datestatus selected values
		if (is_array(@$_SESSION["sel_Stands_Created_datestatus"])) {
			$this->LoadSelectionFromSession('datestatus');
		} elseif (@$_SESSION["sel_Stands_Created_datestatus"] == EWR_INIT_VALUE) { // Select all
			$this->datestatus->SelectionList = "";
		}

		// Get months_paid selected values
		if (is_array(@$_SESSION["sel_Stands_Created_months_paid"])) {
			$this->LoadSelectionFromSession('months_paid');
		} elseif (@$_SESSION["sel_Stands_Created_months_paid"] == EWR_INIT_VALUE) { // Select all
			$this->months_paid->SelectionList = "";
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
				$this->GrandSmry[2] = $rsagg->fields("sum_area");
				$this->GrandCnt[3] = $this->TotCount;
				$this->GrandCnt[4] = $this->TotCount;
				$this->GrandCnt[5] = $this->TotCount;
				$this->GrandSmry[5] = $rsagg->fields("sum_price");
				$this->GrandCnt[6] = $this->TotCount;
				$this->GrandCnt[6] = $rsagg->fields("cnt_deposit");
				$this->GrandCnt[7] = $this->TotCount;
				$this->GrandCnt[8] = $this->TotCount;
				$this->GrandCnt[9] = $this->TotCount;
				$this->GrandCnt[10] = $this->TotCount;
				$this->GrandCnt[11] = $this->TotCount;
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

			// area
			$this->area->SumViewValue = $this->area->SumValue;
			$this->area->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// price
			$this->price->SumViewValue = $this->price->SumValue;
			$this->price->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// deposit
			$this->deposit->CntViewValue = $this->deposit->CntValue;
			$this->deposit->CntViewValue = ewr_FormatNumber($this->deposit->CntViewValue, 0, -2, -2, -2);
			$this->deposit->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// location
			$this->location->HrefValue = "";

			// area
			$this->area->HrefValue = "";

			// number
			$this->number->HrefValue = "";

			// status
			$this->status->HrefValue = "";
			if ($this->Export == "") {
				$drillurl = $this->status->DrillDownUrl;
				$drillurl = str_replace("f0", ewr_Encrypt($this->GetDrillDownSQL($this->status, "status", $this->RowTotalType, -1)), $drillurl);
				$this->status->LinkAttrs["title"] = ewr_JsEncode($GLOBALS["ReportLanguage"]->Phrase("ClickToDrillDown"));
				$this->status->LinkAttrs["class"] = "ewDrillLink";
				$this->status->LinkAttrs["onclick"] = ewr_DrillDownJs($drillurl, 'Stands_Created_status', $GLOBALS["ReportLanguage"]->TablePhrase('stand', 'TblCaption'), $this->UseDrillDownPanel);
			}

			// price
			$this->price->HrefValue = "";

			// deposit
			$this->deposit->HrefValue = "";

			// instalments
			$this->instalments->HrefValue = "";

			// datestatus
			$this->datestatus->HrefValue = "";

			// months_paid
			$this->months_paid->HrefValue = "";

			// vatdate
			$this->vatdate->HrefValue = "";

			// vat
			$this->vat->HrefValue = "";
		} else {

			// location
			$this->location->ViewValue = $this->location->CurrentValue;
			$this->location->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// area
			$this->area->ViewValue = $this->area->CurrentValue;
			$this->area->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// number
			$this->number->ViewValue = $this->number->CurrentValue;
			$this->number->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// status
			$this->status->ViewValue = $this->status->CurrentValue;
			$this->status->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// price
			$this->price->ViewValue = $this->price->CurrentValue;
			$this->price->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// deposit
			$this->deposit->ViewValue = $this->deposit->CurrentValue;
			$this->deposit->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// instalments
			$this->instalments->ViewValue = $this->instalments->CurrentValue;
			$this->instalments->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// datestatus
			$this->datestatus->ViewValue = $this->datestatus->CurrentValue;
			$this->datestatus->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// months_paid
			$this->months_paid->ViewValue = $this->months_paid->CurrentValue;
			$this->months_paid->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// vatdate
			$this->vatdate->ViewValue = $this->vatdate->CurrentValue;
			$this->vatdate->ViewValue = ewr_FormatDateTime($this->vatdate->ViewValue, 1);
			$this->vatdate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// vat
			$this->vat->ViewValue = $this->vat->CurrentValue;
			$this->vat->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// location
			$this->location->HrefValue = "";

			// area
			$this->area->HrefValue = "";

			// number
			$this->number->HrefValue = "";

			// status
			$this->status->HrefValue = "";
			if ($this->Export == "") {
				$drillurl = $this->status->DrillDownUrl;
				$drillurl = str_replace("f0", ewr_Encrypt($this->GetDrillDownSQL($this->status, "status", 0)), $drillurl);
				$this->status->LinkAttrs["title"] = ewr_JsEncode($ReportLanguage->Phrase("ClickToDrillDown"));
				$this->status->LinkAttrs["class"] = "ewDrillLink";
				$this->status->LinkAttrs["onclick"] = ewr_DrillDownJs($drillurl, 'Stands_Created_status', $GLOBALS["ReportLanguage"]->TablePhrase('stand', 'TblCaption'), $this->UseDrillDownPanel);
			}

			// price
			$this->price->HrefValue = "";

			// deposit
			$this->deposit->HrefValue = "";

			// instalments
			$this->instalments->HrefValue = "";

			// datestatus
			$this->datestatus->HrefValue = "";

			// months_paid
			$this->months_paid->HrefValue = "";

			// vatdate
			$this->vatdate->HrefValue = "";

			// vat
			$this->vat->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// area
			$CurrentValue = $this->area->SumValue;
			$ViewValue = &$this->area->SumViewValue;
			$ViewAttrs = &$this->area->ViewAttrs;
			$CellAttrs = &$this->area->CellAttrs;
			$HrefValue = &$this->area->HrefValue;
			$LinkAttrs = &$this->area->LinkAttrs;
			$this->Cell_Rendered($this->area, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// price
			$CurrentValue = $this->price->SumValue;
			$ViewValue = &$this->price->SumViewValue;
			$ViewAttrs = &$this->price->ViewAttrs;
			$CellAttrs = &$this->price->CellAttrs;
			$HrefValue = &$this->price->HrefValue;
			$LinkAttrs = &$this->price->LinkAttrs;
			$this->Cell_Rendered($this->price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// deposit
			$CurrentValue = $this->deposit->CntValue;
			$ViewValue = &$this->deposit->CntViewValue;
			$ViewAttrs = &$this->deposit->ViewAttrs;
			$CellAttrs = &$this->deposit->CellAttrs;
			$HrefValue = &$this->deposit->HrefValue;
			$LinkAttrs = &$this->deposit->LinkAttrs;
			$this->Cell_Rendered($this->deposit, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		} else {

			// location
			$CurrentValue = $this->location->CurrentValue;
			$ViewValue = &$this->location->ViewValue;
			$ViewAttrs = &$this->location->ViewAttrs;
			$CellAttrs = &$this->location->CellAttrs;
			$HrefValue = &$this->location->HrefValue;
			$LinkAttrs = &$this->location->LinkAttrs;
			$this->Cell_Rendered($this->location, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// area
			$CurrentValue = $this->area->CurrentValue;
			$ViewValue = &$this->area->ViewValue;
			$ViewAttrs = &$this->area->ViewAttrs;
			$CellAttrs = &$this->area->CellAttrs;
			$HrefValue = &$this->area->HrefValue;
			$LinkAttrs = &$this->area->LinkAttrs;
			$this->Cell_Rendered($this->area, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// number
			$CurrentValue = $this->number->CurrentValue;
			$ViewValue = &$this->number->ViewValue;
			$ViewAttrs = &$this->number->ViewAttrs;
			$CellAttrs = &$this->number->CellAttrs;
			$HrefValue = &$this->number->HrefValue;
			$LinkAttrs = &$this->number->LinkAttrs;
			$this->Cell_Rendered($this->number, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// status
			$CurrentValue = $this->status->CurrentValue;
			$ViewValue = &$this->status->ViewValue;
			$ViewAttrs = &$this->status->ViewAttrs;
			$CellAttrs = &$this->status->CellAttrs;
			$HrefValue = &$this->status->HrefValue;
			$LinkAttrs = &$this->status->LinkAttrs;
			$this->Cell_Rendered($this->status, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// price
			$CurrentValue = $this->price->CurrentValue;
			$ViewValue = &$this->price->ViewValue;
			$ViewAttrs = &$this->price->ViewAttrs;
			$CellAttrs = &$this->price->CellAttrs;
			$HrefValue = &$this->price->HrefValue;
			$LinkAttrs = &$this->price->LinkAttrs;
			$this->Cell_Rendered($this->price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// deposit
			$CurrentValue = $this->deposit->CurrentValue;
			$ViewValue = &$this->deposit->ViewValue;
			$ViewAttrs = &$this->deposit->ViewAttrs;
			$CellAttrs = &$this->deposit->CellAttrs;
			$HrefValue = &$this->deposit->HrefValue;
			$LinkAttrs = &$this->deposit->LinkAttrs;
			$this->Cell_Rendered($this->deposit, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// instalments
			$CurrentValue = $this->instalments->CurrentValue;
			$ViewValue = &$this->instalments->ViewValue;
			$ViewAttrs = &$this->instalments->ViewAttrs;
			$CellAttrs = &$this->instalments->CellAttrs;
			$HrefValue = &$this->instalments->HrefValue;
			$LinkAttrs = &$this->instalments->LinkAttrs;
			$this->Cell_Rendered($this->instalments, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// datestatus
			$CurrentValue = $this->datestatus->CurrentValue;
			$ViewValue = &$this->datestatus->ViewValue;
			$ViewAttrs = &$this->datestatus->ViewAttrs;
			$CellAttrs = &$this->datestatus->CellAttrs;
			$HrefValue = &$this->datestatus->HrefValue;
			$LinkAttrs = &$this->datestatus->LinkAttrs;
			$this->Cell_Rendered($this->datestatus, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// months_paid
			$CurrentValue = $this->months_paid->CurrentValue;
			$ViewValue = &$this->months_paid->ViewValue;
			$ViewAttrs = &$this->months_paid->ViewAttrs;
			$CellAttrs = &$this->months_paid->CellAttrs;
			$HrefValue = &$this->months_paid->HrefValue;
			$LinkAttrs = &$this->months_paid->LinkAttrs;
			$this->Cell_Rendered($this->months_paid, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// vatdate
			$CurrentValue = $this->vatdate->CurrentValue;
			$ViewValue = &$this->vatdate->ViewValue;
			$ViewAttrs = &$this->vatdate->ViewAttrs;
			$CellAttrs = &$this->vatdate->CellAttrs;
			$HrefValue = &$this->vatdate->HrefValue;
			$LinkAttrs = &$this->vatdate->LinkAttrs;
			$this->Cell_Rendered($this->vatdate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// vat
			$CurrentValue = $this->vat->CurrentValue;
			$ViewValue = &$this->vat->ViewValue;
			$ViewAttrs = &$this->vat->ViewAttrs;
			$CellAttrs = &$this->vat->CellAttrs;
			$HrefValue = &$this->vat->HrefValue;
			$LinkAttrs = &$this->vat->LinkAttrs;
			$this->Cell_Rendered($this->vat, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->location->Visible) $this->DtlFldCount += 1;
		if ($this->area->Visible) $this->DtlFldCount += 1;
		if ($this->number->Visible) $this->DtlFldCount += 1;
		if ($this->status->Visible) $this->DtlFldCount += 1;
		if ($this->price->Visible) $this->DtlFldCount += 1;
		if ($this->deposit->Visible) $this->DtlFldCount += 1;
		if ($this->instalments->Visible) $this->DtlFldCount += 1;
		if ($this->datestatus->Visible) $this->DtlFldCount += 1;
		if ($this->months_paid->Visible) $this->DtlFldCount += 1;
		if ($this->vatdate->Visible) $this->DtlFldCount += 1;
		if ($this->vat->Visible) $this->DtlFldCount += 1;
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

			// Clear extended filter for field location
			if ($this->ClearExtFilter == 'Stands_Created_location')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'location');

			// Clear extended filter for field area
			if ($this->ClearExtFilter == 'Stands_Created_area')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'area');

			// Clear extended filter for field number
			if ($this->ClearExtFilter == 'Stands_Created_number')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'number');

			// Clear extended filter for field status
			if ($this->ClearExtFilter == 'Stands_Created_status')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'status');

			// Clear extended filter for field price
			if ($this->ClearExtFilter == 'Stands_Created_price')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'price');

			// Clear extended filter for field deposit
			if ($this->ClearExtFilter == 'Stands_Created_deposit')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'deposit');

			// Clear extended filter for field instalments
			if ($this->ClearExtFilter == 'Stands_Created_instalments')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'instalments');

			// Clear extended filter for field datestatus
			if ($this->ClearExtFilter == 'Stands_Created_datestatus')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'datestatus');

			// Clear extended filter for field months_paid
			if ($this->ClearExtFilter == 'Stands_Created_months_paid')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'months_paid');

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->location->SearchValue, $this->location->SearchOperator, $this->location->SearchCondition, $this->location->SearchValue2, $this->location->SearchOperator2, 'location'); // Field location
			$this->SetSessionFilterValues($this->area->SearchValue, $this->area->SearchOperator, $this->area->SearchCondition, $this->area->SearchValue2, $this->area->SearchOperator2, 'area'); // Field area
			$this->SetSessionFilterValues($this->number->SearchValue, $this->number->SearchOperator, $this->number->SearchCondition, $this->number->SearchValue2, $this->number->SearchOperator2, 'number'); // Field number
			$this->SetSessionFilterValues($this->status->SearchValue, $this->status->SearchOperator, $this->status->SearchCondition, $this->status->SearchValue2, $this->status->SearchOperator2, 'status'); // Field status
			$this->SetSessionFilterValues($this->price->SearchValue, $this->price->SearchOperator, $this->price->SearchCondition, $this->price->SearchValue2, $this->price->SearchOperator2, 'price'); // Field price
			$this->SetSessionFilterValues($this->deposit->SearchValue, $this->deposit->SearchOperator, $this->deposit->SearchCondition, $this->deposit->SearchValue2, $this->deposit->SearchOperator2, 'deposit'); // Field deposit
			$this->SetSessionFilterValues($this->instalments->SearchValue, $this->instalments->SearchOperator, $this->instalments->SearchCondition, $this->instalments->SearchValue2, $this->instalments->SearchOperator2, 'instalments'); // Field instalments
			$this->SetSessionFilterValues($this->datestatus->SearchValue, $this->datestatus->SearchOperator, $this->datestatus->SearchCondition, $this->datestatus->SearchValue2, $this->datestatus->SearchOperator2, 'datestatus'); // Field datestatus
			$this->SetSessionFilterValues($this->months_paid->SearchValue, $this->months_paid->SearchOperator, $this->months_paid->SearchCondition, $this->months_paid->SearchValue2, $this->months_paid->SearchOperator2, 'months_paid'); // Field months_paid

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field location
			if ($this->GetFilterValues($this->location)) {
				$bSetupFilter = TRUE;
			}

			// Field area
			if ($this->GetFilterValues($this->area)) {
				$bSetupFilter = TRUE;
			}

			// Field number
			if ($this->GetFilterValues($this->number)) {
				$bSetupFilter = TRUE;
			}

			// Field status
			if ($this->GetFilterValues($this->status)) {
				$bSetupFilter = TRUE;
			}

			// Field price
			if ($this->GetFilterValues($this->price)) {
				$bSetupFilter = TRUE;
			}

			// Field deposit
			if ($this->GetFilterValues($this->deposit)) {
				$bSetupFilter = TRUE;
			}

			// Field instalments
			if ($this->GetFilterValues($this->instalments)) {
				$bSetupFilter = TRUE;
			}

			// Field datestatus
			if ($this->GetFilterValues($this->datestatus)) {
				$bSetupFilter = TRUE;
			}

			// Field months_paid
			if ($this->GetFilterValues($this->months_paid)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionFilterValues($this->location); // Field location
			$this->GetSessionFilterValues($this->area); // Field area
			$this->GetSessionFilterValues($this->number); // Field number
			$this->GetSessionFilterValues($this->status); // Field status
			$this->GetSessionFilterValues($this->price); // Field price
			$this->GetSessionFilterValues($this->deposit); // Field deposit
			$this->GetSessionFilterValues($this->instalments); // Field instalments
			$this->GetSessionFilterValues($this->datestatus); // Field datestatus
			$this->GetSessionFilterValues($this->months_paid); // Field months_paid
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->location, $sFilter, FALSE, TRUE); // Field location
		$this->BuildExtendedFilter($this->area, $sFilter, FALSE, TRUE); // Field area
		$this->BuildExtendedFilter($this->number, $sFilter, FALSE, TRUE); // Field number
		$this->BuildExtendedFilter($this->status, $sFilter, FALSE, TRUE); // Field status
		$this->BuildExtendedFilter($this->price, $sFilter, FALSE, TRUE); // Field price
		$this->BuildExtendedFilter($this->deposit, $sFilter, FALSE, TRUE); // Field deposit
		$this->BuildExtendedFilter($this->instalments, $sFilter, FALSE, TRUE); // Field instalments
		$this->BuildExtendedFilter($this->datestatus, $sFilter, FALSE, TRUE); // Field datestatus
		$this->BuildExtendedFilter($this->months_paid, $sFilter, FALSE, TRUE); // Field months_paid

		// Save parms to session
		$this->SetSessionFilterValues($this->location->SearchValue, $this->location->SearchOperator, $this->location->SearchCondition, $this->location->SearchValue2, $this->location->SearchOperator2, 'location'); // Field location
		$this->SetSessionFilterValues($this->area->SearchValue, $this->area->SearchOperator, $this->area->SearchCondition, $this->area->SearchValue2, $this->area->SearchOperator2, 'area'); // Field area
		$this->SetSessionFilterValues($this->number->SearchValue, $this->number->SearchOperator, $this->number->SearchCondition, $this->number->SearchValue2, $this->number->SearchOperator2, 'number'); // Field number
		$this->SetSessionFilterValues($this->status->SearchValue, $this->status->SearchOperator, $this->status->SearchCondition, $this->status->SearchValue2, $this->status->SearchOperator2, 'status'); // Field status
		$this->SetSessionFilterValues($this->price->SearchValue, $this->price->SearchOperator, $this->price->SearchCondition, $this->price->SearchValue2, $this->price->SearchOperator2, 'price'); // Field price
		$this->SetSessionFilterValues($this->deposit->SearchValue, $this->deposit->SearchOperator, $this->deposit->SearchCondition, $this->deposit->SearchValue2, $this->deposit->SearchOperator2, 'deposit'); // Field deposit
		$this->SetSessionFilterValues($this->instalments->SearchValue, $this->instalments->SearchOperator, $this->instalments->SearchCondition, $this->instalments->SearchValue2, $this->instalments->SearchOperator2, 'instalments'); // Field instalments
		$this->SetSessionFilterValues($this->datestatus->SearchValue, $this->datestatus->SearchOperator, $this->datestatus->SearchCondition, $this->datestatus->SearchValue2, $this->datestatus->SearchOperator2, 'datestatus'); // Field datestatus
		$this->SetSessionFilterValues($this->months_paid->SearchValue, $this->months_paid->SearchOperator, $this->months_paid->SearchCondition, $this->months_paid->SearchValue2, $this->months_paid->SearchOperator2, 'months_paid'); // Field months_paid

		// Setup filter
		if ($bSetupFilter) {

			// Field location
			$sWrk = "";
			$this->BuildExtendedFilter($this->location, $sWrk);
			ewr_LoadSelectionFromFilter($this->location, $sWrk, $this->location->SelectionList);
			$_SESSION['sel_Stands_Created_location'] = ($this->location->SelectionList == "") ? EWR_INIT_VALUE : $this->location->SelectionList;

			// Field area
			$sWrk = "";
			$this->BuildExtendedFilter($this->area, $sWrk);
			ewr_LoadSelectionFromFilter($this->area, $sWrk, $this->area->SelectionList);
			$_SESSION['sel_Stands_Created_area'] = ($this->area->SelectionList == "") ? EWR_INIT_VALUE : $this->area->SelectionList;

			// Field number
			$sWrk = "";
			$this->BuildExtendedFilter($this->number, $sWrk);
			ewr_LoadSelectionFromFilter($this->number, $sWrk, $this->number->SelectionList);
			$_SESSION['sel_Stands_Created_number'] = ($this->number->SelectionList == "") ? EWR_INIT_VALUE : $this->number->SelectionList;

			// Field status
			$sWrk = "";
			$this->BuildExtendedFilter($this->status, $sWrk);
			ewr_LoadSelectionFromFilter($this->status, $sWrk, $this->status->SelectionList);
			$_SESSION['sel_Stands_Created_status'] = ($this->status->SelectionList == "") ? EWR_INIT_VALUE : $this->status->SelectionList;

			// Field price
			$sWrk = "";
			$this->BuildExtendedFilter($this->price, $sWrk);
			ewr_LoadSelectionFromFilter($this->price, $sWrk, $this->price->SelectionList);
			$_SESSION['sel_Stands_Created_price'] = ($this->price->SelectionList == "") ? EWR_INIT_VALUE : $this->price->SelectionList;

			// Field deposit
			$sWrk = "";
			$this->BuildExtendedFilter($this->deposit, $sWrk);
			ewr_LoadSelectionFromFilter($this->deposit, $sWrk, $this->deposit->SelectionList);
			$_SESSION['sel_Stands_Created_deposit'] = ($this->deposit->SelectionList == "") ? EWR_INIT_VALUE : $this->deposit->SelectionList;

			// Field instalments
			$sWrk = "";
			$this->BuildExtendedFilter($this->instalments, $sWrk);
			ewr_LoadSelectionFromFilter($this->instalments, $sWrk, $this->instalments->SelectionList);
			$_SESSION['sel_Stands_Created_instalments'] = ($this->instalments->SelectionList == "") ? EWR_INIT_VALUE : $this->instalments->SelectionList;

			// Field datestatus
			$sWrk = "";
			$this->BuildExtendedFilter($this->datestatus, $sWrk);
			ewr_LoadSelectionFromFilter($this->datestatus, $sWrk, $this->datestatus->SelectionList);
			$_SESSION['sel_Stands_Created_datestatus'] = ($this->datestatus->SelectionList == "") ? EWR_INIT_VALUE : $this->datestatus->SelectionList;

			// Field months_paid
			$sWrk = "";
			$this->BuildExtendedFilter($this->months_paid, $sWrk);
			ewr_LoadSelectionFromFilter($this->months_paid, $sWrk, $this->months_paid->SelectionList);
			$_SESSION['sel_Stands_Created_months_paid'] = ($this->months_paid->SelectionList == "") ? EWR_INIT_VALUE : $this->months_paid->SelectionList;
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
		$this->GetSessionValue($fld->DropDownValue, 'sv_Stands_Created_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Stands_Created_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_Stands_Created_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Stands_Created_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_Stands_Created_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_Stands_Created_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_Stands_Created_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_Stands_Created_' . $parm] = $sv;
		$_SESSION['so_Stands_Created_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_Stands_Created_' . $parm] = $sv1;
		$_SESSION['so_Stands_Created_' . $parm] = $so1;
		$_SESSION['sc_Stands_Created_' . $parm] = $sc;
		$_SESSION['sv2_Stands_Created_' . $parm] = $sv2;
		$_SESSION['so2_Stands_Created_' . $parm] = $so2;
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
		$_SESSION["sel_Stands_Created_$parm"] = "";
		$_SESSION["rf_Stands_Created_$parm"] = "";
		$_SESSION["rt_Stands_Created_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_Stands_Created_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_Stands_Created_$parm"];
		$fld->RangeTo = @$_SESSION["rt_Stands_Created_$parm"];
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

		// Field location
		$this->SetDefaultExtFilter($this->location, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->location);
		$sWrk = "";
		$this->BuildExtendedFilter($this->location, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->location, $sWrk, $this->location->DefaultSelectionList);
		if (!$this->SearchCommand) $this->location->SelectionList = $this->location->DefaultSelectionList;

		// Field area
		$this->SetDefaultExtFilter($this->area, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->area);
		$sWrk = "";
		$this->BuildExtendedFilter($this->area, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->area, $sWrk, $this->area->DefaultSelectionList);
		if (!$this->SearchCommand) $this->area->SelectionList = $this->area->DefaultSelectionList;

		// Field number
		$this->SetDefaultExtFilter($this->number, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->number);
		$sWrk = "";
		$this->BuildExtendedFilter($this->number, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->number, $sWrk, $this->number->DefaultSelectionList);
		if (!$this->SearchCommand) $this->number->SelectionList = $this->number->DefaultSelectionList;

		// Field status
		$this->SetDefaultExtFilter($this->status, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->status);
		$sWrk = "";
		$this->BuildExtendedFilter($this->status, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->status, $sWrk, $this->status->DefaultSelectionList);
		if (!$this->SearchCommand) $this->status->SelectionList = $this->status->DefaultSelectionList;

		// Field price
		$this->SetDefaultExtFilter($this->price, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->price);
		$sWrk = "";
		$this->BuildExtendedFilter($this->price, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->price, $sWrk, $this->price->DefaultSelectionList);
		if (!$this->SearchCommand) $this->price->SelectionList = $this->price->DefaultSelectionList;

		// Field deposit
		$this->SetDefaultExtFilter($this->deposit, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->deposit);
		$sWrk = "";
		$this->BuildExtendedFilter($this->deposit, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->deposit, $sWrk, $this->deposit->DefaultSelectionList);
		if (!$this->SearchCommand) $this->deposit->SelectionList = $this->deposit->DefaultSelectionList;

		// Field instalments
		$this->SetDefaultExtFilter($this->instalments, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->instalments);
		$sWrk = "";
		$this->BuildExtendedFilter($this->instalments, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->instalments, $sWrk, $this->instalments->DefaultSelectionList);
		if (!$this->SearchCommand) $this->instalments->SelectionList = $this->instalments->DefaultSelectionList;

		// Field datestatus
		$this->SetDefaultExtFilter($this->datestatus, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->datestatus);
		$sWrk = "";
		$this->BuildExtendedFilter($this->datestatus, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->datestatus, $sWrk, $this->datestatus->DefaultSelectionList);
		if (!$this->SearchCommand) $this->datestatus->SelectionList = $this->datestatus->DefaultSelectionList;

		// Field months_paid
		$this->SetDefaultExtFilter($this->months_paid, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->months_paid);
		$sWrk = "";
		$this->BuildExtendedFilter($this->months_paid, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->months_paid, $sWrk, $this->months_paid->DefaultSelectionList);
		if (!$this->SearchCommand) $this->months_paid->SelectionList = $this->months_paid->DefaultSelectionList;

		/**
		* Set up default values for popup filters
		*/

		// Field location
		// $this->location->DefaultSelectionList = array("val1", "val2");
		// Field area
		// $this->area->DefaultSelectionList = array("val1", "val2");
		// Field number
		// $this->number->DefaultSelectionList = array("val1", "val2");
		// Field status
		// $this->status->DefaultSelectionList = array("val1", "val2");
		// Field price
		// $this->price->DefaultSelectionList = array("val1", "val2");
		// Field deposit
		// $this->deposit->DefaultSelectionList = array("val1", "val2");
		// Field instalments
		// $this->instalments->DefaultSelectionList = array("val1", "val2");
		// Field datestatus
		// $this->datestatus->DefaultSelectionList = array("val1", "val2");
		// Field months_paid
		// $this->months_paid->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check location text filter
		if ($this->TextFilterApplied($this->location))
			return TRUE;

		// Check location popup filter
		if (!ewr_MatchedArray($this->location->DefaultSelectionList, $this->location->SelectionList))
			return TRUE;

		// Check area text filter
		if ($this->TextFilterApplied($this->area))
			return TRUE;

		// Check area popup filter
		if (!ewr_MatchedArray($this->area->DefaultSelectionList, $this->area->SelectionList))
			return TRUE;

		// Check number text filter
		if ($this->TextFilterApplied($this->number))
			return TRUE;

		// Check number popup filter
		if (!ewr_MatchedArray($this->number->DefaultSelectionList, $this->number->SelectionList))
			return TRUE;

		// Check status text filter
		if ($this->TextFilterApplied($this->status))
			return TRUE;

		// Check status popup filter
		if (!ewr_MatchedArray($this->status->DefaultSelectionList, $this->status->SelectionList))
			return TRUE;

		// Check price text filter
		if ($this->TextFilterApplied($this->price))
			return TRUE;

		// Check price popup filter
		if (!ewr_MatchedArray($this->price->DefaultSelectionList, $this->price->SelectionList))
			return TRUE;

		// Check deposit text filter
		if ($this->TextFilterApplied($this->deposit))
			return TRUE;

		// Check deposit popup filter
		if (!ewr_MatchedArray($this->deposit->DefaultSelectionList, $this->deposit->SelectionList))
			return TRUE;

		// Check instalments text filter
		if ($this->TextFilterApplied($this->instalments))
			return TRUE;

		// Check instalments popup filter
		if (!ewr_MatchedArray($this->instalments->DefaultSelectionList, $this->instalments->SelectionList))
			return TRUE;

		// Check datestatus text filter
		if ($this->TextFilterApplied($this->datestatus))
			return TRUE;

		// Check datestatus popup filter
		if (!ewr_MatchedArray($this->datestatus->DefaultSelectionList, $this->datestatus->SelectionList))
			return TRUE;

		// Check months_paid text filter
		if ($this->TextFilterApplied($this->months_paid))
			return TRUE;

		// Check months_paid popup filter
		if (!ewr_MatchedArray($this->months_paid->DefaultSelectionList, $this->months_paid->SelectionList))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList() {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field location
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->location, $sExtWrk);
		if (is_array($this->location->SelectionList))
			$sWrk = ewr_JoinArray($this->location->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->location->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field area
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->area, $sExtWrk);
		if (is_array($this->area->SelectionList))
			$sWrk = ewr_JoinArray($this->area->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->area->FldCaption() . "</span>" . $sFilter . "</div>";

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

		// Field status
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->status, $sExtWrk);
		if (is_array($this->status->SelectionList))
			$sWrk = ewr_JoinArray($this->status->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->status->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field price
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->price, $sExtWrk);
		if (is_array($this->price->SelectionList))
			$sWrk = ewr_JoinArray($this->price->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->price->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field deposit
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->deposit, $sExtWrk);
		if (is_array($this->deposit->SelectionList))
			$sWrk = ewr_JoinArray($this->deposit->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->deposit->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field instalments
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->instalments, $sExtWrk);
		if (is_array($this->instalments->SelectionList))
			$sWrk = ewr_JoinArray($this->instalments->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->instalments->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field datestatus
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->datestatus, $sExtWrk);
		if (is_array($this->datestatus->SelectionList))
			$sWrk = ewr_JoinArray($this->datestatus->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->datestatus->FldCaption() . "</span>" . $sFilter . "</div>";

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

		// Field location
		$sWrk = "";
		if ($this->location->SearchValue <> "" || $this->location->SearchValue2 <> "") {
			$sWrk = "\"sv_location\":\"" . ewr_JsEncode2($this->location->SearchValue) . "\"," .
				"\"so_location\":\"" . ewr_JsEncode2($this->location->SearchOperator) . "\"," .
				"\"sc_location\":\"" . ewr_JsEncode2($this->location->SearchCondition) . "\"," .
				"\"sv2_location\":\"" . ewr_JsEncode2($this->location->SearchValue2) . "\"," .
				"\"so2_location\":\"" . ewr_JsEncode2($this->location->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->location->SelectionList <> EWR_INIT_VALUE) ? $this->location->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_location\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field area
		$sWrk = "";
		if ($this->area->SearchValue <> "" || $this->area->SearchValue2 <> "") {
			$sWrk = "\"sv_area\":\"" . ewr_JsEncode2($this->area->SearchValue) . "\"," .
				"\"so_area\":\"" . ewr_JsEncode2($this->area->SearchOperator) . "\"," .
				"\"sc_area\":\"" . ewr_JsEncode2($this->area->SearchCondition) . "\"," .
				"\"sv2_area\":\"" . ewr_JsEncode2($this->area->SearchValue2) . "\"," .
				"\"so2_area\":\"" . ewr_JsEncode2($this->area->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->area->SelectionList <> EWR_INIT_VALUE) ? $this->area->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_area\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

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

		// Field status
		$sWrk = "";
		if ($this->status->SearchValue <> "" || $this->status->SearchValue2 <> "") {
			$sWrk = "\"sv_status\":\"" . ewr_JsEncode2($this->status->SearchValue) . "\"," .
				"\"so_status\":\"" . ewr_JsEncode2($this->status->SearchOperator) . "\"," .
				"\"sc_status\":\"" . ewr_JsEncode2($this->status->SearchCondition) . "\"," .
				"\"sv2_status\":\"" . ewr_JsEncode2($this->status->SearchValue2) . "\"," .
				"\"so2_status\":\"" . ewr_JsEncode2($this->status->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->status->SelectionList <> EWR_INIT_VALUE) ? $this->status->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_status\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field price
		$sWrk = "";
		if ($this->price->SearchValue <> "" || $this->price->SearchValue2 <> "") {
			$sWrk = "\"sv_price\":\"" . ewr_JsEncode2($this->price->SearchValue) . "\"," .
				"\"so_price\":\"" . ewr_JsEncode2($this->price->SearchOperator) . "\"," .
				"\"sc_price\":\"" . ewr_JsEncode2($this->price->SearchCondition) . "\"," .
				"\"sv2_price\":\"" . ewr_JsEncode2($this->price->SearchValue2) . "\"," .
				"\"so2_price\":\"" . ewr_JsEncode2($this->price->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->price->SelectionList <> EWR_INIT_VALUE) ? $this->price->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_price\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field deposit
		$sWrk = "";
		if ($this->deposit->SearchValue <> "" || $this->deposit->SearchValue2 <> "") {
			$sWrk = "\"sv_deposit\":\"" . ewr_JsEncode2($this->deposit->SearchValue) . "\"," .
				"\"so_deposit\":\"" . ewr_JsEncode2($this->deposit->SearchOperator) . "\"," .
				"\"sc_deposit\":\"" . ewr_JsEncode2($this->deposit->SearchCondition) . "\"," .
				"\"sv2_deposit\":\"" . ewr_JsEncode2($this->deposit->SearchValue2) . "\"," .
				"\"so2_deposit\":\"" . ewr_JsEncode2($this->deposit->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->deposit->SelectionList <> EWR_INIT_VALUE) ? $this->deposit->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_deposit\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field instalments
		$sWrk = "";
		if ($this->instalments->SearchValue <> "" || $this->instalments->SearchValue2 <> "") {
			$sWrk = "\"sv_instalments\":\"" . ewr_JsEncode2($this->instalments->SearchValue) . "\"," .
				"\"so_instalments\":\"" . ewr_JsEncode2($this->instalments->SearchOperator) . "\"," .
				"\"sc_instalments\":\"" . ewr_JsEncode2($this->instalments->SearchCondition) . "\"," .
				"\"sv2_instalments\":\"" . ewr_JsEncode2($this->instalments->SearchValue2) . "\"," .
				"\"so2_instalments\":\"" . ewr_JsEncode2($this->instalments->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->instalments->SelectionList <> EWR_INIT_VALUE) ? $this->instalments->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_instalments\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field datestatus
		$sWrk = "";
		if ($this->datestatus->SearchValue <> "" || $this->datestatus->SearchValue2 <> "") {
			$sWrk = "\"sv_datestatus\":\"" . ewr_JsEncode2($this->datestatus->SearchValue) . "\"," .
				"\"so_datestatus\":\"" . ewr_JsEncode2($this->datestatus->SearchOperator) . "\"," .
				"\"sc_datestatus\":\"" . ewr_JsEncode2($this->datestatus->SearchCondition) . "\"," .
				"\"sv2_datestatus\":\"" . ewr_JsEncode2($this->datestatus->SearchValue2) . "\"," .
				"\"so2_datestatus\":\"" . ewr_JsEncode2($this->datestatus->SearchOperator2) . "\"";
		}
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

		// Field location
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_location", $filter) || array_key_exists("so_location", $filter) ||
			array_key_exists("sc_location", $filter) ||
			array_key_exists("sv2_location", $filter) || array_key_exists("so2_location", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_location"], @$filter["so_location"], @$filter["sc_location"], @$filter["sv2_location"], @$filter["so2_location"], "location");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_location", $filter)) {
			$sWrk = $filter["sel_location"];
			$sWrk = explode("||", $sWrk);
			$this->location->SelectionList = $sWrk;
			$_SESSION["sel_Stands_Created_location"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "location"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "location");
			$this->location->SelectionList = "";
			$_SESSION["sel_Stands_Created_location"] = "";
		}

		// Field area
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_area", $filter) || array_key_exists("so_area", $filter) ||
			array_key_exists("sc_area", $filter) ||
			array_key_exists("sv2_area", $filter) || array_key_exists("so2_area", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_area"], @$filter["so_area"], @$filter["sc_area"], @$filter["sv2_area"], @$filter["so2_area"], "area");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_area", $filter)) {
			$sWrk = $filter["sel_area"];
			$sWrk = explode("||", $sWrk);
			$this->area->SelectionList = $sWrk;
			$_SESSION["sel_Stands_Created_area"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "area"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "area");
			$this->area->SelectionList = "";
			$_SESSION["sel_Stands_Created_area"] = "";
		}

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
			$_SESSION["sel_Stands_Created_number"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "number"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "number");
			$this->number->SelectionList = "";
			$_SESSION["sel_Stands_Created_number"] = "";
		}

		// Field status
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_status", $filter) || array_key_exists("so_status", $filter) ||
			array_key_exists("sc_status", $filter) ||
			array_key_exists("sv2_status", $filter) || array_key_exists("so2_status", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_status"], @$filter["so_status"], @$filter["sc_status"], @$filter["sv2_status"], @$filter["so2_status"], "status");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_status", $filter)) {
			$sWrk = $filter["sel_status"];
			$sWrk = explode("||", $sWrk);
			$this->status->SelectionList = $sWrk;
			$_SESSION["sel_Stands_Created_status"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "status"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "status");
			$this->status->SelectionList = "";
			$_SESSION["sel_Stands_Created_status"] = "";
		}

		// Field price
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_price", $filter) || array_key_exists("so_price", $filter) ||
			array_key_exists("sc_price", $filter) ||
			array_key_exists("sv2_price", $filter) || array_key_exists("so2_price", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_price"], @$filter["so_price"], @$filter["sc_price"], @$filter["sv2_price"], @$filter["so2_price"], "price");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_price", $filter)) {
			$sWrk = $filter["sel_price"];
			$sWrk = explode("||", $sWrk);
			$this->price->SelectionList = $sWrk;
			$_SESSION["sel_Stands_Created_price"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "price"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "price");
			$this->price->SelectionList = "";
			$_SESSION["sel_Stands_Created_price"] = "";
		}

		// Field deposit
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_deposit", $filter) || array_key_exists("so_deposit", $filter) ||
			array_key_exists("sc_deposit", $filter) ||
			array_key_exists("sv2_deposit", $filter) || array_key_exists("so2_deposit", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_deposit"], @$filter["so_deposit"], @$filter["sc_deposit"], @$filter["sv2_deposit"], @$filter["so2_deposit"], "deposit");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_deposit", $filter)) {
			$sWrk = $filter["sel_deposit"];
			$sWrk = explode("||", $sWrk);
			$this->deposit->SelectionList = $sWrk;
			$_SESSION["sel_Stands_Created_deposit"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "deposit"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "deposit");
			$this->deposit->SelectionList = "";
			$_SESSION["sel_Stands_Created_deposit"] = "";
		}

		// Field instalments
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_instalments", $filter) || array_key_exists("so_instalments", $filter) ||
			array_key_exists("sc_instalments", $filter) ||
			array_key_exists("sv2_instalments", $filter) || array_key_exists("so2_instalments", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_instalments"], @$filter["so_instalments"], @$filter["sc_instalments"], @$filter["sv2_instalments"], @$filter["so2_instalments"], "instalments");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_instalments", $filter)) {
			$sWrk = $filter["sel_instalments"];
			$sWrk = explode("||", $sWrk);
			$this->instalments->SelectionList = $sWrk;
			$_SESSION["sel_Stands_Created_instalments"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "instalments"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "instalments");
			$this->instalments->SelectionList = "";
			$_SESSION["sel_Stands_Created_instalments"] = "";
		}

		// Field datestatus
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_datestatus", $filter) || array_key_exists("so_datestatus", $filter) ||
			array_key_exists("sc_datestatus", $filter) ||
			array_key_exists("sv2_datestatus", $filter) || array_key_exists("so2_datestatus", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_datestatus"], @$filter["so_datestatus"], @$filter["sc_datestatus"], @$filter["sv2_datestatus"], @$filter["so2_datestatus"], "datestatus");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_datestatus", $filter)) {
			$sWrk = $filter["sel_datestatus"];
			$sWrk = explode("||", $sWrk);
			$this->datestatus->SelectionList = $sWrk;
			$_SESSION["sel_Stands_Created_datestatus"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "datestatus"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "datestatus");
			$this->datestatus->SelectionList = "";
			$_SESSION["sel_Stands_Created_datestatus"] = "";
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
			$_SESSION["sel_Stands_Created_months_paid"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "months_paid"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "months_paid");
			$this->months_paid->SelectionList = "";
			$_SESSION["sel_Stands_Created_months_paid"] = "";
		}
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		if (!$this->ExtendedFilterExist($this->location)) {
			if (is_array($this->location->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->location, "`location`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->location, $sFilter, "popup");
				$this->location->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->area)) {
			if (is_array($this->area->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->area, "`area`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->area, $sFilter, "popup");
				$this->area->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->number)) {
			if (is_array($this->number->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->number, "`number`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->number, $sFilter, "popup");
				$this->number->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->status)) {
			if (is_array($this->status->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->status, "`status`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->status, $sFilter, "popup");
				$this->status->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->price)) {
			if (is_array($this->price->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->price, "`price`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->price, $sFilter, "popup");
				$this->price->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->deposit)) {
			if (is_array($this->deposit->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->deposit, "`deposit`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->deposit, $sFilter, "popup");
				$this->deposit->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->instalments)) {
			if (is_array($this->instalments->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->instalments, "`instalments`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->instalments, $sFilter, "popup");
				$this->instalments->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->datestatus)) {
			if (is_array($this->datestatus->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->datestatus, "`datestatus`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->datestatus, $sFilter, "popup");
				$this->datestatus->CurrentFilter = $sFilter;
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
		return $sWrk;
	}

	// Return drill down SQL
	// - fld = source field object
	// - target = target field name
	// - rowtype = row type
	//  * 0 = detail
	//  * 1 = group
	//  * 2 = page
	//  * 3 = grand
	// - parm = filter/column index
	//  * -1  = use field filter value / current/old value
	//  * 0   = use grouping/column field value
	//  * > 0 = use column index
	function GetDrillDownSQL($fld, $target, $rowtype, $parm = 0) {
		$sql = "";

		// Handle group/row/column field
		if ($parm >= 0 && $sql == "") {
			switch (substr($fld->FldVar,2)) {
			}
		}

		// Detail field
		if ($sql == "" && $rowtype == 0)
			if ($fld->CurrentFilter <> "") // Use current filter
				$sql = str_replace($fld->FldExpression, "@" . $target, $fld->CurrentFilter);
			elseif ($fld->CurrentValue <> "") // Use current value for detail row
				$sql = "@" . $target . "=" . ewr_QuotedValue($fld->CurrentValue, $fld->FldDataType, $this->DBID);
		return $sql;
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
				$this->location->setSort("");
				$this->area->setSort("");
				$this->number->setSort("");
				$this->status->setSort("");
				$this->price->setSort("");
				$this->deposit->setSort("");
				$this->instalments->setSort("");
				$this->datestatus->setSort("");
				$this->months_paid->setSort("");
				$this->vatdate->setSort("");
				$this->vat->setSort("");
			}

		// Check for an Order parameter
		} elseif (@$_GET["order"] <> "") {
			$this->CurrentOrder = ewr_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->location, $bCtrl); // location
			$this->UpdateSort($this->area, $bCtrl); // area
			$this->UpdateSort($this->number, $bCtrl); // number
			$this->UpdateSort($this->status, $bCtrl); // status
			$this->UpdateSort($this->price, $bCtrl); // price
			$this->UpdateSort($this->deposit, $bCtrl); // deposit
			$this->UpdateSort($this->instalments, $bCtrl); // instalments
			$this->UpdateSort($this->datestatus, $bCtrl); // datestatus
			$this->UpdateSort($this->months_paid, $bCtrl); // months_paid
			$this->UpdateSort($this->vatdate, $bCtrl); // vatdate
			$this->UpdateSort($this->vat, $bCtrl); // vat
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
if (!isset($Stands_Created_summary)) $Stands_Created_summary = new crStands_Created_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$Stands_Created_summary;

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
var Stands_Created_summary = new ewr_Page("Stands_Created_summary");

// Page properties
Stands_Created_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = Stands_Created_summary.PageID;

// Extend page with Chart_Rendering function
Stands_Created_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
Stands_Created_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fStands_Createdsummary = new ewr_Form("fStands_Createdsummary");

// Validate method
fStands_Createdsummary.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fStands_Createdsummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fStands_Createdsummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fStands_Createdsummary.ValidateRequired = false; // No JavaScript validation
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
<form name="fStands_Createdsummary" id="fStands_Createdsummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fStands_Createdsummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_location" class="ewCell form-group">
	<label for="sv_location" class="ewSearchCaption ewLabel"><?php echo $Page->location->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_location" id="so_location" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->location->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->location->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->location->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->location->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->location->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->location->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->location->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->location->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->location->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->location->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->location->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->location->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->location->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->location->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_location" id="sv_location" name="sv_location" size="30" maxlength="111" placeholder="<?php echo $Page->location->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->location->SearchValue) ?>"<?php echo $Page->location->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_location" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_location" style="display: none">
<?php ewr_PrependClass($Page->location->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_location" id="sv2_location" name="sv2_location" size="30" maxlength="111" placeholder="<?php echo $Page->location->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->location->SearchValue2) ?>"<?php echo $Page->location->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_area" class="ewCell form-group">
	<label for="sv_area" class="ewSearchCaption ewLabel"><?php echo $Page->area->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_area" id="so_area" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->area->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->area->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->area->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->area->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->area->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->area->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->area->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->area->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->area->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->area->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_area" id="sv_area" name="sv_area" size="30" maxlength="111" placeholder="<?php echo $Page->area->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->area->SearchValue) ?>"<?php echo $Page->area->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_area" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_area" style="display: none">
<?php ewr_PrependClass($Page->area->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_area" id="sv2_area" name="sv2_area" size="30" maxlength="111" placeholder="<?php echo $Page->area->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->area->SearchValue2) ?>"<?php echo $Page->area->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_3" class="ewRow">
<div id="c_number" class="ewCell form-group">
	<label for="sv_number" class="ewSearchCaption ewLabel"><?php echo $Page->number->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_number" id="so_number" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->number->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->number->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->number->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->number->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->number->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->number->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->number->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->number->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->number->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->number->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->number->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->number->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->number->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->number->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_number" id="sv_number" name="sv_number" size="30" maxlength="111" placeholder="<?php echo $Page->number->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->number->SearchValue) ?>"<?php echo $Page->number->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_number" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_number" style="display: none">
<?php ewr_PrependClass($Page->number->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_number" id="sv2_number" name="sv2_number" size="30" maxlength="111" placeholder="<?php echo $Page->number->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->number->SearchValue2) ?>"<?php echo $Page->number->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_4" class="ewRow">
<div id="c_status" class="ewCell form-group">
	<label for="sv_status" class="ewSearchCaption ewLabel"><?php echo $Page->status->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_status" id="so_status" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->status->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->status->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->status->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->status->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->status->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->status->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->status->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->status->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->status->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->status->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->status->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->status->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->status->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->status->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_status" id="sv_status" name="sv_status" size="30" maxlength="111" placeholder="<?php echo $Page->status->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->status->SearchValue) ?>"<?php echo $Page->status->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_status" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_status" style="display: none">
<?php ewr_PrependClass($Page->status->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_status" id="sv2_status" name="sv2_status" size="30" maxlength="111" placeholder="<?php echo $Page->status->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->status->SearchValue2) ?>"<?php echo $Page->status->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_5" class="ewRow">
<div id="c_price" class="ewCell form-group">
	<label for="sv_price" class="ewSearchCaption ewLabel"><?php echo $Page->price->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_price" id="so_price" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->price->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->price->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->price->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->price->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->price->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->price->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->price->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->price->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->price->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->price->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_price" id="sv_price" name="sv_price" size="30" maxlength="111" placeholder="<?php echo $Page->price->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->price->SearchValue) ?>"<?php echo $Page->price->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_price" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_price" style="display: none">
<?php ewr_PrependClass($Page->price->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_price" id="sv2_price" name="sv2_price" size="30" maxlength="111" placeholder="<?php echo $Page->price->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->price->SearchValue2) ?>"<?php echo $Page->price->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_6" class="ewRow">
<div id="c_deposit" class="ewCell form-group">
	<label for="sv_deposit" class="ewSearchCaption ewLabel"><?php echo $Page->deposit->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_deposit" id="so_deposit" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->deposit->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->deposit->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->deposit->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->deposit->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->deposit->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->deposit->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->deposit->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->deposit->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->deposit->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->deposit->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_deposit" id="sv_deposit" name="sv_deposit" size="30" maxlength="111" placeholder="<?php echo $Page->deposit->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->deposit->SearchValue) ?>"<?php echo $Page->deposit->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_deposit" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_deposit" style="display: none">
<?php ewr_PrependClass($Page->deposit->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_deposit" id="sv2_deposit" name="sv2_deposit" size="30" maxlength="111" placeholder="<?php echo $Page->deposit->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->deposit->SearchValue2) ?>"<?php echo $Page->deposit->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_7" class="ewRow">
<div id="c_instalments" class="ewCell form-group">
	<label for="sv_instalments" class="ewSearchCaption ewLabel"><?php echo $Page->instalments->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_instalments" id="so_instalments" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->instalments->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->instalments->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->instalments->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->instalments->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->instalments->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->instalments->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->instalments->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->instalments->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->instalments->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->instalments->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->instalments->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->instalments->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->instalments->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->instalments->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_instalments" id="sv_instalments" name="sv_instalments" size="30" maxlength="111" placeholder="<?php echo $Page->instalments->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->instalments->SearchValue) ?>"<?php echo $Page->instalments->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_instalments" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_instalments" style="display: none">
<?php ewr_PrependClass($Page->instalments->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_instalments" id="sv2_instalments" name="sv2_instalments" size="30" maxlength="111" placeholder="<?php echo $Page->instalments->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->instalments->SearchValue2) ?>"<?php echo $Page->instalments->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_8" class="ewRow">
<div id="c_datestatus" class="ewCell form-group">
	<label for="sv_datestatus" class="ewSearchCaption ewLabel"><?php echo $Page->datestatus->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_datestatus" id="so_datestatus" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->datestatus->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->datestatus->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->datestatus->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->datestatus->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->datestatus->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->datestatus->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->datestatus->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->datestatus->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->datestatus->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->datestatus->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_datestatus" id="sv_datestatus" name="sv_datestatus" size="30" maxlength="111" placeholder="<?php echo $Page->datestatus->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->datestatus->SearchValue) ?>"<?php echo $Page->datestatus->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_datestatus" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_datestatus" style="display: none">
<?php ewr_PrependClass($Page->datestatus->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_datestatus" id="sv2_datestatus" name="sv2_datestatus" size="30" maxlength="111" placeholder="<?php echo $Page->datestatus->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->datestatus->SearchValue2) ?>"<?php echo $Page->datestatus->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_9" class="ewRow">
<div id="c_months_paid" class="ewCell form-group">
	<label for="sv_months_paid" class="ewSearchCaption ewLabel"><?php echo $Page->months_paid->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_months_paid" id="so_months_paid" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->months_paid->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->months_paid->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->months_paid->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->months_paid->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->months_paid->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->months_paid->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->months_paid->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->months_paid->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->months_paid->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->months_paid->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->months_paid->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->months_paid->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->months_paid->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->months_paid->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_months_paid" id="sv_months_paid" name="sv_months_paid" size="30" maxlength="111" placeholder="<?php echo $Page->months_paid->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->months_paid->SearchValue) ?>"<?php echo $Page->months_paid->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_months_paid" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_months_paid" style="display: none">
<?php ewr_PrependClass($Page->months_paid->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Stands_Created" data-field="x_months_paid" id="sv2_months_paid" name="sv2_months_paid" size="30" maxlength="111" placeholder="<?php echo $Page->months_paid->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->months_paid->SearchValue2) ?>"<?php echo $Page->months_paid->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fStands_Createdsummary.Init();
fStands_Createdsummary.FilterList = <?php echo $Page->GetFilterList() ?>;
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
<?php include "Stands_Createdsmrypager.php" ?>
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
<?php if ($Page->location->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="location"><div class="Stands_Created_location"><span class="ewTableHeaderCaption"><?php echo $Page->location->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="location">
<?php if ($Page->SortUrl($Page->location) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_location">
			<span class="ewTableHeaderCaption"><?php echo $Page->location->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_location', false, '<?php echo $Page->location->RangeFrom; ?>', '<?php echo $Page->location->RangeTo; ?>');" id="x_location<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_location" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->location) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->location->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->location->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->location->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_location', false, '<?php echo $Page->location->RangeFrom; ?>', '<?php echo $Page->location->RangeTo; ?>');" id="x_location<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="area"><div class="Stands_Created_area"><span class="ewTableHeaderCaption"><?php echo $Page->area->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="area">
<?php if ($Page->SortUrl($Page->area) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_area">
			<span class="ewTableHeaderCaption"><?php echo $Page->area->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_area', false, '<?php echo $Page->area->RangeFrom; ?>', '<?php echo $Page->area->RangeTo; ?>');" id="x_area<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_area" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->area) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->area->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->area->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->area->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_area', false, '<?php echo $Page->area->RangeFrom; ?>', '<?php echo $Page->area->RangeTo; ?>');" id="x_area<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->number->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="number"><div class="Stands_Created_number"><span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="number">
<?php if ($Page->SortUrl($Page->number) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_number">
			<span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_number', false, '<?php echo $Page->number->RangeFrom; ?>', '<?php echo $Page->number->RangeTo; ?>');" id="x_number<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_number" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->number) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->number->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->number->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_number', false, '<?php echo $Page->number->RangeFrom; ?>', '<?php echo $Page->number->RangeTo; ?>');" id="x_number<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->status->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="status"><div class="Stands_Created_status"><span class="ewTableHeaderCaption"><?php echo $Page->status->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="status">
<?php if ($Page->SortUrl($Page->status) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_status">
			<span class="ewTableHeaderCaption"><?php echo $Page->status->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_status', false, '<?php echo $Page->status->RangeFrom; ?>', '<?php echo $Page->status->RangeTo; ?>');" id="x_status<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_status" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->status) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->status->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_status', false, '<?php echo $Page->status->RangeFrom; ?>', '<?php echo $Page->status->RangeTo; ?>');" id="x_status<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="price"><div class="Stands_Created_price"><span class="ewTableHeaderCaption"><?php echo $Page->price->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="price">
<?php if ($Page->SortUrl($Page->price) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_price">
			<span class="ewTableHeaderCaption"><?php echo $Page->price->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_price', false, '<?php echo $Page->price->RangeFrom; ?>', '<?php echo $Page->price->RangeTo; ?>');" id="x_price<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_price" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->price) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->price->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->price->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->price->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_price', false, '<?php echo $Page->price->RangeFrom; ?>', '<?php echo $Page->price->RangeTo; ?>');" id="x_price<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->deposit->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="deposit"><div class="Stands_Created_deposit"><span class="ewTableHeaderCaption"><?php echo $Page->deposit->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="deposit">
<?php if ($Page->SortUrl($Page->deposit) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_deposit">
			<span class="ewTableHeaderCaption"><?php echo $Page->deposit->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_deposit', false, '<?php echo $Page->deposit->RangeFrom; ?>', '<?php echo $Page->deposit->RangeTo; ?>');" id="x_deposit<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_deposit" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->deposit) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->deposit->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->deposit->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->deposit->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_deposit', false, '<?php echo $Page->deposit->RangeFrom; ?>', '<?php echo $Page->deposit->RangeTo; ?>');" id="x_deposit<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="instalments"><div class="Stands_Created_instalments"><span class="ewTableHeaderCaption"><?php echo $Page->instalments->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="instalments">
<?php if ($Page->SortUrl($Page->instalments) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_instalments">
			<span class="ewTableHeaderCaption"><?php echo $Page->instalments->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_instalments', false, '<?php echo $Page->instalments->RangeFrom; ?>', '<?php echo $Page->instalments->RangeTo; ?>');" id="x_instalments<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_instalments" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->instalments) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->instalments->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->instalments->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->instalments->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_instalments', false, '<?php echo $Page->instalments->RangeFrom; ?>', '<?php echo $Page->instalments->RangeTo; ?>');" id="x_instalments<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="datestatus"><div class="Stands_Created_datestatus"><span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="datestatus">
<?php if ($Page->SortUrl($Page->datestatus) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_datestatus">
			<span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_datestatus', false, '<?php echo $Page->datestatus->RangeFrom; ?>', '<?php echo $Page->datestatus->RangeTo; ?>');" id="x_datestatus<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_datestatus" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->datestatus) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->datestatus->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->datestatus->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_datestatus', false, '<?php echo $Page->datestatus->RangeFrom; ?>', '<?php echo $Page->datestatus->RangeTo; ?>');" id="x_datestatus<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->months_paid->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="months_paid"><div class="Stands_Created_months_paid"><span class="ewTableHeaderCaption"><?php echo $Page->months_paid->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="months_paid">
<?php if ($Page->SortUrl($Page->months_paid) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_months_paid">
			<span class="ewTableHeaderCaption"><?php echo $Page->months_paid->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_months_paid', false, '<?php echo $Page->months_paid->RangeFrom; ?>', '<?php echo $Page->months_paid->RangeTo; ?>');" id="x_months_paid<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_months_paid" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->months_paid) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->months_paid->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->months_paid->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->months_paid->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Stands_Created_months_paid', false, '<?php echo $Page->months_paid->RangeFrom; ?>', '<?php echo $Page->months_paid->RangeTo; ?>');" id="x_months_paid<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->vatdate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="vatdate"><div class="Stands_Created_vatdate"><span class="ewTableHeaderCaption"><?php echo $Page->vatdate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="vatdate">
<?php if ($Page->SortUrl($Page->vatdate) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_vatdate">
			<span class="ewTableHeaderCaption"><?php echo $Page->vatdate->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_vatdate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->vatdate) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->vatdate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->vatdate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->vatdate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->vat->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="vat"><div class="Stands_Created_vat"><span class="ewTableHeaderCaption"><?php echo $Page->vat->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="vat">
<?php if ($Page->SortUrl($Page->vat) == "") { ?>
		<div class="ewTableHeaderBtn Stands_Created_vat">
			<span class="ewTableHeaderCaption"><?php echo $Page->vat->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Stands_Created_vat" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->vat) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->vat->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->vat->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->vat->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
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
<?php if ($Page->location->Visible) { ?>
		<td data-field="location"<?php echo $Page->location->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_location"<?php echo $Page->location->ViewAttributes() ?>><?php echo $Page->location->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_area"<?php echo $Page->area->ViewAttributes() ?>><?php echo $Page->area->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_number"<?php echo $Page->number->ViewAttributes() ?>><?php echo $Page->number->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->status->Visible) { ?>
		<td data-field="status"<?php echo $Page->status->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_status"<?php echo $Page->status->ViewAttributes() ?>>
<?php if ($Page->status->HrefValue <> "" || @$Page->status->LinkAttrs["onclick"] <> "") { ?>
<?php if ($Page->status->ListViewValue() <> "" && $Page->status->ListViewValue() <> "&nbsp;") { ?>
<a<?php echo $Page->status->LinkAttributes() ?>><?php echo $Page->status->ListViewValue() ?></a>
<?php } else { echo "&nbsp;"; } ?>
<?php } else { ?>
<?php if ($Page->status->ListViewValue() <> "" && $Page->status->ListViewValue() <> "&nbsp;") { ?>
<?php echo $Page->status->ListViewValue() ?>
<?php } else { echo "&nbsp;"; } ?>
<?php } ?>
</span></td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_price"<?php echo $Page->price->ViewAttributes() ?>><?php echo $Page->price->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->deposit->Visible) { ?>
		<td data-field="deposit"<?php echo $Page->deposit->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_deposit"<?php echo $Page->deposit->ViewAttributes() ?>><?php echo $Page->deposit->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_instalments"<?php echo $Page->instalments->ViewAttributes() ?>><?php echo $Page->instalments->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_datestatus"<?php echo $Page->datestatus->ViewAttributes() ?>><?php echo $Page->datestatus->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->months_paid->Visible) { ?>
		<td data-field="months_paid"<?php echo $Page->months_paid->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_months_paid"<?php echo $Page->months_paid->ViewAttributes() ?>><?php echo $Page->months_paid->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->vatdate->Visible) { ?>
		<td data-field="vatdate"<?php echo $Page->vatdate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_vatdate"<?php echo $Page->vatdate->ViewAttributes() ?>><?php echo $Page->vatdate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->vat->Visible) { ?>
		<td data-field="vat"<?php echo $Page->vat->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Stands_Created_vat"<?php echo $Page->vat->ViewAttributes() ?>><?php echo $Page->vat->ListViewValue() ?></span></td>
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
	$Page->area->Count = $Page->GrandCnt[2];
	$Page->area->SumValue = $Page->GrandSmry[2]; // Load SUM
	$Page->RowTotalSubType = EWR_ROWTOTAL_SUM;
	$Page->price->Count = $Page->GrandCnt[5];
	$Page->price->SumValue = $Page->GrandSmry[5]; // Load SUM
	$Page->RowTotalSubType = EWR_ROWTOTAL_SUM;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->location->Visible) { ?>
		<td data-field="location"<?php echo $Page->location->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></span>
<span data-class="tpts_Stands_Created_area"<?php echo $Page->area->ViewAttributes() ?>><?php echo $Page->area->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->status->Visible) { ?>
		<td data-field="status"<?php echo $Page->status->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></span>
<span data-class="tpts_Stands_Created_price"<?php echo $Page->price->ViewAttributes() ?>><?php echo $Page->price->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->deposit->Visible) { ?>
		<td data-field="deposit"<?php echo $Page->deposit->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->months_paid->Visible) { ?>
		<td data-field="months_paid"<?php echo $Page->months_paid->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->vatdate->Visible) { ?>
		<td data-field="vatdate"<?php echo $Page->vatdate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->vat->Visible) { ?>
		<td data-field="vat"<?php echo $Page->vat->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
<?php
	$Page->ResetAttrs();
	$Page->deposit->Count = $Page->GrandCnt[6];
	$Page->deposit->CntValue = $Page->GrandCnt[6]; // Load CNT
	$Page->RowTotalSubType = EWR_ROWTOTAL_CNT;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->location->Visible) { ?>
		<td data-field="location"<?php echo $Page->location->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->status->Visible) { ?>
		<td data-field="status"<?php echo $Page->status->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->deposit->Visible) { ?>
		<td data-field="deposit"<?php echo $Page->deposit->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span>
<span data-class="tptc_Stands_Created_deposit"<?php echo $Page->deposit->ViewAttributes() ?>><?php echo $Page->deposit->CntViewValue ?></span></td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->months_paid->Visible) { ?>
		<td data-field="months_paid"<?php echo $Page->months_paid->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->vatdate->Visible) { ?>
		<td data-field="vatdate"<?php echo $Page->vatdate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->vat->Visible) { ?>
		<td data-field="vat"<?php echo $Page->vat->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && TRUE) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "Stands_Createdsmrypager.php" ?>
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
<?php include "Stands_Createdsmrypager.php" ?>
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
