<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg10.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn10.php" ?>
<?php include_once "phprptinc/ewrusrfn10.php" ?>
<?php include_once "Ledger_Balancessmryinfo.php" ?>
<?php

//
// Page class
//

$Ledger_Balances_summary = NULL; // Initialize page object first

class crLedger_Balances_summary extends crLedger_Balances {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{8971DFF8-CD58-406F-905F-ABF6EB424D34}";

	// Page object name
	var $PageObjName = 'Ledger_Balances_summary';

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

		// Table object (Ledger_Balances)
		if (!isset($GLOBALS["Ledger_Balances"])) {
			$GLOBALS["Ledger_Balances"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Ledger_Balances"];
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
			define("EWR_TABLE_NAME", 'Ledger Balances', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fLedger_Balancessummary";

		// Generate report options
		$this->GenerateOptions = new crListOptions();
		$this->GenerateOptions->Tag = "div";
		$this->GenerateOptions->TagClassName = "ewGenerateOption";
	}

	//
	// Page_Init
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
		$this->datestatus->PlaceHolder = $this->datestatus->FldCaption();
		$this->lastpaymentdate->PlaceHolder = $this->lastpaymentdate->FldCaption();
		$this->price->PlaceHolder = $this->price->FldCaption();
		$this->tot->PlaceHolder = $this->tot->FldCaption();
		$this->outstand->PlaceHolder = $this->outstand->FldCaption();

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
		global $Security, $ReportLanguage, $ReportOptions;
		$exportid = session_id();
		$ReportTypes = array();

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["print"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormPrint") : "";

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["excel"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormExcel") : "";

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = TRUE;
		$item->Visible = TRUE;
		$ReportTypes["word"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormWord") : "";

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = TRUE;

		$ReportTypes["pdf"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormPdf") : "";

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_Ledger_Balances\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_Ledger_Balances',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["email"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormEmail") : "";
		$ReportOptions["ReportTypes"] = $ReportTypes;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fLedger_Balancessummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fLedger_Balancessummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";
	}

	// Set up search options
	function SetupSearchOptions() {
		global $ReportLanguage;

		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = $this->FilterApplied ? " active" : " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fLedger_Balancessummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = TRUE && $this->FilterApplied;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide options for export
		if ($this->Export <> "")
			$this->SearchOptions->HideAllOptions();
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
			if (ob_get_length())
				ob_end_clean();

			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				if (@$this->GenOptions["reporttype"] == "email") {
					$saveResponse = $this->$fn($sContent, $this->GenOptions);
					$this->WriteGenResponse($saveResponse);
				} else {
					echo $this->$fn($sContent, array());
				}
				$url = ""; // Avoid redirect
			} else {
				$saveToFile = $this->$fn($sContent, $this->GenOptions);
				if (@$this->GenOptions["reporttype"] <> "") {
					$saveUrl = ($saveToFile <> "") ? ewr_ConvertFullUrl($saveToFile) : $ReportLanguage->Phrase("GenerateSuccess");
					$this->WriteGenResponse($saveUrl);
					$url = ""; // Avoid redirect
				}
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
	var $GrpColumnCount = 0;
	var $SubGrpColumnCount = 0;
	var $DtlColumnCount = 0;
	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;
	var $GrpIdx;
	var $DetailRows = array();

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

		// Set field visibility for detail fields
		$this->number->SetVisibility();
		$this->area->SetVisibility();
		$this->datestatus->SetVisibility();
		$this->lastpaymentdate->SetVisibility();
		$this->price->SetVisibility();
		$this->tot->SetVisibility();
		$this->outstand->SetVisibility();

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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(TRUE,TRUE), array(TRUE,TRUE), array(TRUE,TRUE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();

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

		// Search options
		$this->SetupSearchOptions();

		// Get sort
		$this->Sort = $this->GetSort($this->GenOptions);

		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);
		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowHeader = ($this->TotalGrps > 0);

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
			$this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup($this->GenOptions);

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
			$this->GenerateOptions->HideAllOptions();
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
			$rs->MoveFirst(); // Move first
				$this->FirstRowData = array();
				$this->FirstRowData['number'] = ewr_Conv($rs->fields('number'), 200);
				$this->FirstRowData['area'] = ewr_Conv($rs->fields('area'), 131);
				$this->FirstRowData['datestatus'] = ewr_Conv($rs->fields('datestatus'), 135);
				$this->FirstRowData['lastpaymentdate'] = ewr_Conv($rs->fields('lastpaymentdate'), 200);
				$this->FirstRowData['price'] = ewr_Conv($rs->fields('price'), 131);
				$this->FirstRowData['tot'] = ewr_Conv($rs->fields('tot'), 131);
				$this->FirstRowData['outstand'] = ewr_Conv($rs->fields('outstand'), 131);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->number->setDbValue($rs->fields('number'));
			$this->area->setDbValue($rs->fields('area'));
			$this->datestatus->setDbValue($rs->fields('datestatus'));
			$this->lastpaymentdate->setDbValue($rs->fields('lastpaymentdate'));
			$this->price->setDbValue($rs->fields('price'));
			$this->tot->setDbValue($rs->fields('tot'));
			$this->outstand->setDbValue($rs->fields('outstand'));
			$this->Val[1] = $this->number->CurrentValue;
			$this->Val[2] = $this->area->CurrentValue;
			$this->Val[3] = $this->datestatus->CurrentValue;
			$this->Val[4] = $this->lastpaymentdate->CurrentValue;
			$this->Val[5] = $this->price->CurrentValue;
			$this->Val[6] = $this->tot->CurrentValue;
			$this->Val[7] = $this->outstand->CurrentValue;
		} else {
			$this->number->setDbValue("");
			$this->area->setDbValue("");
			$this->datestatus->setDbValue("");
			$this->lastpaymentdate->setDbValue("");
			$this->price->setDbValue("");
			$this->tot->setDbValue("");
			$this->outstand->setDbValue("");
		}
	}

	// Set up starting group
	function SetUpStartGroup($options = array()) {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;
		$startGrp = (@$options["start"] <> "") ? $options["start"] : @$_GET[EWR_TABLE_START_GROUP];
		$pageNo = (@$options["pageno"] <> "") ? $options["pageno"] : @$_GET["pageno"];

		// Check for a 'start' parameter
		if ($startGrp != "") {
			$this->StartGrp = $startGrp;
			$this->setStartGroup($this->StartGrp);
		} elseif ($pageNo != "") {
			$nPageNo = $pageNo;
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
			// Output data as Json

			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				if (ob_get_length())
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
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
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
		if (!$this->GrandSummarySetup) { // Get Grand total
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
				$this->GrandCnt[3] = $this->TotCount;
				$this->GrandCnt[4] = $this->TotCount;
				$this->GrandCnt[5] = $this->TotCount;
				$this->GrandSmry[5] = $rsagg->fields("sum_price");
				$this->GrandSmry[5] = $rsagg->fields("sum_price");
				$this->GrandMn[5] = $rsagg->fields("min_price");
				$this->GrandMx[5] = $rsagg->fields("max_price");
				$this->GrandCnt[5] = $rsagg->fields("cnt_price");
				$this->GrandCnt[6] = $this->TotCount;
				$this->GrandSmry[6] = $rsagg->fields("sum_tot");
				$this->GrandSmry[6] = $rsagg->fields("sum_tot");
				$this->GrandMn[6] = $rsagg->fields("min_tot");
				$this->GrandMx[6] = $rsagg->fields("max_tot");
				$this->GrandCnt[6] = $rsagg->fields("cnt_tot");
				$this->GrandCnt[7] = $this->TotCount;
				$this->GrandSmry[7] = $rsagg->fields("sum_outstand");
				$this->GrandSmry[7] = $rsagg->fields("sum_outstand");
				$this->GrandMn[7] = $rsagg->fields("min_outstand");
				$this->GrandMx[7] = $rsagg->fields("max_outstand");
				$this->GrandCnt[7] = $rsagg->fields("cnt_outstand");
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

		if ($this->RowType == EWR_ROWTYPE_TOTAL && !($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER)) { // Summary row
			ewr_PrependClass($this->RowAttrs["class"], ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel); // Set up row class

			// price
			$this->price->SumViewValue = $this->price->SumValue;
			$this->price->SumViewValue = ewr_FormatNumber($this->price->SumViewValue, 2, -2, -2, -2);
			$this->price->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// price
			$this->price->AvgViewValue = $this->price->AvgValue;
			$this->price->AvgViewValue = ewr_FormatNumber($this->price->AvgViewValue, 2, -2, -2, -2);
			$this->price->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// price
			$this->price->MinViewValue = $this->price->MinValue;
			$this->price->MinViewValue = ewr_FormatNumber($this->price->MinViewValue, 2, -2, -2, -2);
			$this->price->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// price
			$this->price->MaxViewValue = $this->price->MaxValue;
			$this->price->MaxViewValue = ewr_FormatNumber($this->price->MaxViewValue, 2, -2, -2, -2);
			$this->price->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// price
			$this->price->CntViewValue = $this->price->CntValue;
			$this->price->CntViewValue = ewr_FormatNumber($this->price->CntViewValue, 0, -2, -2, -2);
			$this->price->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// tot
			$this->tot->SumViewValue = $this->tot->SumValue;
			$this->tot->SumViewValue = ewr_FormatNumber($this->tot->SumViewValue, 2, -2, -2, -2);
			$this->tot->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// tot
			$this->tot->AvgViewValue = $this->tot->AvgValue;
			$this->tot->AvgViewValue = ewr_FormatNumber($this->tot->AvgViewValue, 2, -2, -2, -2);
			$this->tot->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// tot
			$this->tot->MinViewValue = $this->tot->MinValue;
			$this->tot->MinViewValue = ewr_FormatNumber($this->tot->MinViewValue, 2, -2, -2, -2);
			$this->tot->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// tot
			$this->tot->MaxViewValue = $this->tot->MaxValue;
			$this->tot->MaxViewValue = ewr_FormatNumber($this->tot->MaxViewValue, 2, -2, -2, -2);
			$this->tot->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// tot
			$this->tot->CntViewValue = $this->tot->CntValue;
			$this->tot->CntViewValue = ewr_FormatNumber($this->tot->CntViewValue, 0, -2, -2, -2);
			$this->tot->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// outstand
			$this->outstand->SumViewValue = $this->outstand->SumValue;
			$this->outstand->SumViewValue = ewr_FormatNumber($this->outstand->SumViewValue, 2, -2, -2, -2);
			$this->outstand->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// outstand
			$this->outstand->AvgViewValue = $this->outstand->AvgValue;
			$this->outstand->AvgViewValue = ewr_FormatNumber($this->outstand->AvgViewValue, 2, -2, -2, -2);
			$this->outstand->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// outstand
			$this->outstand->MinViewValue = $this->outstand->MinValue;
			$this->outstand->MinViewValue = ewr_FormatNumber($this->outstand->MinViewValue, 2, -2, -2, -2);
			$this->outstand->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// outstand
			$this->outstand->MaxViewValue = $this->outstand->MaxValue;
			$this->outstand->MaxViewValue = ewr_FormatNumber($this->outstand->MaxViewValue, 2, -2, -2, -2);
			$this->outstand->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// outstand
			$this->outstand->CntViewValue = $this->outstand->CntValue;
			$this->outstand->CntViewValue = ewr_FormatNumber($this->outstand->CntViewValue, 0, -2, -2, -2);
			$this->outstand->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// number
			$this->number->HrefValue = "";

			// area
			$this->area->HrefValue = "";

			// datestatus
			$this->datestatus->HrefValue = "";

			// lastpaymentdate
			$this->lastpaymentdate->HrefValue = "";

			// price
			$this->price->HrefValue = "";

			// tot
			$this->tot->HrefValue = "";

			// outstand
			$this->outstand->HrefValue = "";
		} else {
			if ($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER) {
			} else {
			}

			// number
			$this->number->ViewValue = $this->number->CurrentValue;
			$this->number->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// area
			$this->area->ViewValue = $this->area->CurrentValue;
			$this->area->ViewValue = ewr_FormatNumber($this->area->ViewValue, 2, -2, -2, -2);
			$this->area->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// datestatus
			$this->datestatus->ViewValue = $this->datestatus->CurrentValue;
			$this->datestatus->ViewValue = ewr_FormatDateTime($this->datestatus->ViewValue, 0);
			$this->datestatus->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// lastpaymentdate
			$this->lastpaymentdate->ViewValue = $this->lastpaymentdate->CurrentValue;
			$this->lastpaymentdate->ViewValue = ewr_FormatDateTime($this->lastpaymentdate->ViewValue, 0);
			$this->lastpaymentdate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// price
			$this->price->ViewValue = $this->price->CurrentValue;
			$this->price->ViewValue = ewr_FormatNumber($this->price->ViewValue, 2, -2, -2, -2);
			$this->price->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// tot
			$this->tot->ViewValue = $this->tot->CurrentValue;
			$this->tot->ViewValue = ewr_FormatNumber($this->tot->ViewValue, 2, -2, -2, -2);
			$this->tot->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// outstand
			$this->outstand->ViewValue = $this->outstand->CurrentValue;
			$this->outstand->ViewValue = ewr_FormatNumber($this->outstand->ViewValue, 2, -2, -2, -2);
			$this->outstand->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// number
			$this->number->HrefValue = "";

			// area
			$this->area->HrefValue = "";

			// datestatus
			$this->datestatus->HrefValue = "";

			// lastpaymentdate
			$this->lastpaymentdate->HrefValue = "";

			// price
			$this->price->HrefValue = "";

			// tot
			$this->tot->HrefValue = "";

			// outstand
			$this->outstand->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// price
			$CurrentValue = $this->price->SumValue;
			$ViewValue = &$this->price->SumViewValue;
			$ViewAttrs = &$this->price->ViewAttrs;
			$CellAttrs = &$this->price->CellAttrs;
			$HrefValue = &$this->price->HrefValue;
			$LinkAttrs = &$this->price->LinkAttrs;
			$this->Cell_Rendered($this->price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// price
			$CurrentValue = $this->price->AvgValue;
			$ViewValue = &$this->price->AvgViewValue;
			$ViewAttrs = &$this->price->ViewAttrs;
			$CellAttrs = &$this->price->CellAttrs;
			$HrefValue = &$this->price->HrefValue;
			$LinkAttrs = &$this->price->LinkAttrs;
			$this->Cell_Rendered($this->price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// price
			$CurrentValue = $this->price->MinValue;
			$ViewValue = &$this->price->MinViewValue;
			$ViewAttrs = &$this->price->ViewAttrs;
			$CellAttrs = &$this->price->CellAttrs;
			$HrefValue = &$this->price->HrefValue;
			$LinkAttrs = &$this->price->LinkAttrs;
			$this->Cell_Rendered($this->price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// price
			$CurrentValue = $this->price->MaxValue;
			$ViewValue = &$this->price->MaxViewValue;
			$ViewAttrs = &$this->price->ViewAttrs;
			$CellAttrs = &$this->price->CellAttrs;
			$HrefValue = &$this->price->HrefValue;
			$LinkAttrs = &$this->price->LinkAttrs;
			$this->Cell_Rendered($this->price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// price
			$CurrentValue = $this->price->CntValue;
			$ViewValue = &$this->price->CntViewValue;
			$ViewAttrs = &$this->price->ViewAttrs;
			$CellAttrs = &$this->price->CellAttrs;
			$HrefValue = &$this->price->HrefValue;
			$LinkAttrs = &$this->price->LinkAttrs;
			$this->Cell_Rendered($this->price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// tot
			$CurrentValue = $this->tot->SumValue;
			$ViewValue = &$this->tot->SumViewValue;
			$ViewAttrs = &$this->tot->ViewAttrs;
			$CellAttrs = &$this->tot->CellAttrs;
			$HrefValue = &$this->tot->HrefValue;
			$LinkAttrs = &$this->tot->LinkAttrs;
			$this->Cell_Rendered($this->tot, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// tot
			$CurrentValue = $this->tot->AvgValue;
			$ViewValue = &$this->tot->AvgViewValue;
			$ViewAttrs = &$this->tot->ViewAttrs;
			$CellAttrs = &$this->tot->CellAttrs;
			$HrefValue = &$this->tot->HrefValue;
			$LinkAttrs = &$this->tot->LinkAttrs;
			$this->Cell_Rendered($this->tot, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// tot
			$CurrentValue = $this->tot->MinValue;
			$ViewValue = &$this->tot->MinViewValue;
			$ViewAttrs = &$this->tot->ViewAttrs;
			$CellAttrs = &$this->tot->CellAttrs;
			$HrefValue = &$this->tot->HrefValue;
			$LinkAttrs = &$this->tot->LinkAttrs;
			$this->Cell_Rendered($this->tot, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// tot
			$CurrentValue = $this->tot->MaxValue;
			$ViewValue = &$this->tot->MaxViewValue;
			$ViewAttrs = &$this->tot->ViewAttrs;
			$CellAttrs = &$this->tot->CellAttrs;
			$HrefValue = &$this->tot->HrefValue;
			$LinkAttrs = &$this->tot->LinkAttrs;
			$this->Cell_Rendered($this->tot, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// tot
			$CurrentValue = $this->tot->CntValue;
			$ViewValue = &$this->tot->CntViewValue;
			$ViewAttrs = &$this->tot->ViewAttrs;
			$CellAttrs = &$this->tot->CellAttrs;
			$HrefValue = &$this->tot->HrefValue;
			$LinkAttrs = &$this->tot->LinkAttrs;
			$this->Cell_Rendered($this->tot, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// outstand
			$CurrentValue = $this->outstand->SumValue;
			$ViewValue = &$this->outstand->SumViewValue;
			$ViewAttrs = &$this->outstand->ViewAttrs;
			$CellAttrs = &$this->outstand->CellAttrs;
			$HrefValue = &$this->outstand->HrefValue;
			$LinkAttrs = &$this->outstand->LinkAttrs;
			$this->Cell_Rendered($this->outstand, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// outstand
			$CurrentValue = $this->outstand->AvgValue;
			$ViewValue = &$this->outstand->AvgViewValue;
			$ViewAttrs = &$this->outstand->ViewAttrs;
			$CellAttrs = &$this->outstand->CellAttrs;
			$HrefValue = &$this->outstand->HrefValue;
			$LinkAttrs = &$this->outstand->LinkAttrs;
			$this->Cell_Rendered($this->outstand, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// outstand
			$CurrentValue = $this->outstand->MinValue;
			$ViewValue = &$this->outstand->MinViewValue;
			$ViewAttrs = &$this->outstand->ViewAttrs;
			$CellAttrs = &$this->outstand->CellAttrs;
			$HrefValue = &$this->outstand->HrefValue;
			$LinkAttrs = &$this->outstand->LinkAttrs;
			$this->Cell_Rendered($this->outstand, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// outstand
			$CurrentValue = $this->outstand->MaxValue;
			$ViewValue = &$this->outstand->MaxViewValue;
			$ViewAttrs = &$this->outstand->ViewAttrs;
			$CellAttrs = &$this->outstand->CellAttrs;
			$HrefValue = &$this->outstand->HrefValue;
			$LinkAttrs = &$this->outstand->LinkAttrs;
			$this->Cell_Rendered($this->outstand, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// outstand
			$CurrentValue = $this->outstand->CntValue;
			$ViewValue = &$this->outstand->CntViewValue;
			$ViewAttrs = &$this->outstand->ViewAttrs;
			$CellAttrs = &$this->outstand->CellAttrs;
			$HrefValue = &$this->outstand->HrefValue;
			$LinkAttrs = &$this->outstand->LinkAttrs;
			$this->Cell_Rendered($this->outstand, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		} else {

			// number
			$CurrentValue = $this->number->CurrentValue;
			$ViewValue = &$this->number->ViewValue;
			$ViewAttrs = &$this->number->ViewAttrs;
			$CellAttrs = &$this->number->CellAttrs;
			$HrefValue = &$this->number->HrefValue;
			$LinkAttrs = &$this->number->LinkAttrs;
			$this->Cell_Rendered($this->number, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// area
			$CurrentValue = $this->area->CurrentValue;
			$ViewValue = &$this->area->ViewValue;
			$ViewAttrs = &$this->area->ViewAttrs;
			$CellAttrs = &$this->area->CellAttrs;
			$HrefValue = &$this->area->HrefValue;
			$LinkAttrs = &$this->area->LinkAttrs;
			$this->Cell_Rendered($this->area, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// datestatus
			$CurrentValue = $this->datestatus->CurrentValue;
			$ViewValue = &$this->datestatus->ViewValue;
			$ViewAttrs = &$this->datestatus->ViewAttrs;
			$CellAttrs = &$this->datestatus->CellAttrs;
			$HrefValue = &$this->datestatus->HrefValue;
			$LinkAttrs = &$this->datestatus->LinkAttrs;
			$this->Cell_Rendered($this->datestatus, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// lastpaymentdate
			$CurrentValue = $this->lastpaymentdate->CurrentValue;
			$ViewValue = &$this->lastpaymentdate->ViewValue;
			$ViewAttrs = &$this->lastpaymentdate->ViewAttrs;
			$CellAttrs = &$this->lastpaymentdate->CellAttrs;
			$HrefValue = &$this->lastpaymentdate->HrefValue;
			$LinkAttrs = &$this->lastpaymentdate->LinkAttrs;
			$this->Cell_Rendered($this->lastpaymentdate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// price
			$CurrentValue = $this->price->CurrentValue;
			$ViewValue = &$this->price->ViewValue;
			$ViewAttrs = &$this->price->ViewAttrs;
			$CellAttrs = &$this->price->CellAttrs;
			$HrefValue = &$this->price->HrefValue;
			$LinkAttrs = &$this->price->LinkAttrs;
			$this->Cell_Rendered($this->price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// tot
			$CurrentValue = $this->tot->CurrentValue;
			$ViewValue = &$this->tot->ViewValue;
			$ViewAttrs = &$this->tot->ViewAttrs;
			$CellAttrs = &$this->tot->CellAttrs;
			$HrefValue = &$this->tot->HrefValue;
			$LinkAttrs = &$this->tot->LinkAttrs;
			$this->Cell_Rendered($this->tot, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// outstand
			$CurrentValue = $this->outstand->CurrentValue;
			$ViewValue = &$this->outstand->ViewValue;
			$ViewAttrs = &$this->outstand->ViewAttrs;
			$CellAttrs = &$this->outstand->CellAttrs;
			$HrefValue = &$this->outstand->HrefValue;
			$LinkAttrs = &$this->outstand->LinkAttrs;
			$this->Cell_Rendered($this->outstand, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpColumnCount = 0;
		$this->SubGrpColumnCount = 0;
		$this->DtlColumnCount = 0;
		if ($this->number->Visible) $this->DtlColumnCount += 1;
		if ($this->area->Visible) $this->DtlColumnCount += 1;
		if ($this->datestatus->Visible) $this->DtlColumnCount += 1;
		if ($this->lastpaymentdate->Visible) $this->DtlColumnCount += 1;
		if ($this->price->Visible) $this->DtlColumnCount += 1;
		if ($this->tot->Visible) $this->DtlColumnCount += 1;
		if ($this->outstand->Visible) $this->DtlColumnCount += 1;
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
		global $ReportLanguage, $ReportOptions;
		$ReportTypes = $ReportOptions["ReportTypes"];
		$item =& $this->ExportOptions->GetItem("pdf");
		$item->Visible = TRUE;
		if ($item->Visible)
			$ReportTypes["pdf"] = $ReportLanguage->Phrase("ReportFormPdf");
		$exportid = session_id();
		$url = $this->ExportPdfUrl;
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $url . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$ReportOptions["ReportTypes"] = $ReportTypes;
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

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->number->SearchValue, $this->number->SearchOperator, $this->number->SearchCondition, $this->number->SearchValue2, $this->number->SearchOperator2, 'number'); // Field number
			$this->SetSessionFilterValues($this->datestatus->SearchValue, $this->datestatus->SearchOperator, $this->datestatus->SearchCondition, $this->datestatus->SearchValue2, $this->datestatus->SearchOperator2, 'datestatus'); // Field datestatus
			$this->SetSessionFilterValues($this->lastpaymentdate->SearchValue, $this->lastpaymentdate->SearchOperator, $this->lastpaymentdate->SearchCondition, $this->lastpaymentdate->SearchValue2, $this->lastpaymentdate->SearchOperator2, 'lastpaymentdate'); // Field lastpaymentdate
			$this->SetSessionFilterValues($this->price->SearchValue, $this->price->SearchOperator, $this->price->SearchCondition, $this->price->SearchValue2, $this->price->SearchOperator2, 'price'); // Field price
			$this->SetSessionFilterValues($this->tot->SearchValue, $this->tot->SearchOperator, $this->tot->SearchCondition, $this->tot->SearchValue2, $this->tot->SearchOperator2, 'tot'); // Field tot
			$this->SetSessionFilterValues($this->outstand->SearchValue, $this->outstand->SearchOperator, $this->outstand->SearchCondition, $this->outstand->SearchValue2, $this->outstand->SearchOperator2, 'outstand'); // Field outstand

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field number
			if ($this->GetFilterValues($this->number)) {
				$bSetupFilter = TRUE;
			}

			// Field datestatus
			if ($this->GetFilterValues($this->datestatus)) {
				$bSetupFilter = TRUE;
			}

			// Field lastpaymentdate
			if ($this->GetFilterValues($this->lastpaymentdate)) {
				$bSetupFilter = TRUE;
			}

			// Field price
			if ($this->GetFilterValues($this->price)) {
				$bSetupFilter = TRUE;
			}

			// Field tot
			if ($this->GetFilterValues($this->tot)) {
				$bSetupFilter = TRUE;
			}

			// Field outstand
			if ($this->GetFilterValues($this->outstand)) {
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
			$this->GetSessionFilterValues($this->datestatus); // Field datestatus
			$this->GetSessionFilterValues($this->lastpaymentdate); // Field lastpaymentdate
			$this->GetSessionFilterValues($this->price); // Field price
			$this->GetSessionFilterValues($this->tot); // Field tot
			$this->GetSessionFilterValues($this->outstand); // Field outstand
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->number, $sFilter, FALSE, TRUE); // Field number
		$this->BuildExtendedFilter($this->datestatus, $sFilter, FALSE, TRUE); // Field datestatus
		$this->BuildExtendedFilter($this->lastpaymentdate, $sFilter, FALSE, TRUE); // Field lastpaymentdate
		$this->BuildExtendedFilter($this->price, $sFilter, FALSE, TRUE); // Field price
		$this->BuildExtendedFilter($this->tot, $sFilter, FALSE, TRUE); // Field tot
		$this->BuildExtendedFilter($this->outstand, $sFilter, FALSE, TRUE); // Field outstand

		// Save parms to session
		$this->SetSessionFilterValues($this->number->SearchValue, $this->number->SearchOperator, $this->number->SearchCondition, $this->number->SearchValue2, $this->number->SearchOperator2, 'number'); // Field number
		$this->SetSessionFilterValues($this->datestatus->SearchValue, $this->datestatus->SearchOperator, $this->datestatus->SearchCondition, $this->datestatus->SearchValue2, $this->datestatus->SearchOperator2, 'datestatus'); // Field datestatus
		$this->SetSessionFilterValues($this->lastpaymentdate->SearchValue, $this->lastpaymentdate->SearchOperator, $this->lastpaymentdate->SearchCondition, $this->lastpaymentdate->SearchValue2, $this->lastpaymentdate->SearchOperator2, 'lastpaymentdate'); // Field lastpaymentdate
		$this->SetSessionFilterValues($this->price->SearchValue, $this->price->SearchOperator, $this->price->SearchCondition, $this->price->SearchValue2, $this->price->SearchOperator2, 'price'); // Field price
		$this->SetSessionFilterValues($this->tot->SearchValue, $this->tot->SearchOperator, $this->tot->SearchCondition, $this->tot->SearchValue2, $this->tot->SearchOperator2, 'tot'); // Field tot
		$this->SetSessionFilterValues($this->outstand->SearchValue, $this->outstand->SearchOperator, $this->outstand->SearchCondition, $this->outstand->SearchValue2, $this->outstand->SearchOperator2, 'outstand'); // Field outstand

		// Setup filter
		if ($bSetupFilter) {
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
				$sWrk = $this->GetCustomFilter($fld, $FldVal, $this->DBID);
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
	function GetCustomFilter(&$fld, $FldVal, $dbid = 0) {
		$sWrk = "";
		if (is_array($fld->AdvancedFilters)) {
			foreach ($fld->AdvancedFilters as $filter) {
				if ($filter->ID == $FldVal && $filter->Enabled) {
					$sFld = $fld->FldExpression;
					$sFn = $filter->FunctionName;
					$wrkid = (substr($filter->ID,0,2) == "@@") ? substr($filter->ID,2) : $filter->ID;
					if ($sFn <> "")
						$sWrk = $sFn($sFld, $dbid);
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
		$this->GetSessionValue($fld->DropDownValue, 'sv_Ledger_Balances_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Ledger_Balances_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_Ledger_Balances_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Ledger_Balances_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_Ledger_Balances_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_Ledger_Balances_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_Ledger_Balances_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_Ledger_Balances_' . $parm] = $sv;
		$_SESSION['so_Ledger_Balances_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_Ledger_Balances_' . $parm] = $sv1;
		$_SESSION['so_Ledger_Balances_' . $parm] = $so1;
		$_SESSION['sc_Ledger_Balances_' . $parm] = $sc;
		$_SESSION['sv2_Ledger_Balances_' . $parm] = $sv2;
		$_SESSION['so2_Ledger_Balances_' . $parm] = $so2;
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
		if (!ewr_CheckDateDef($this->datestatus->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->datestatus->FldErrMsg();
		}
		if (!EURODATE($this->lastpaymentdate->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->lastpaymentdate->FldErrMsg();
		}
		if (!ewr_CheckNumber($this->price->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->price->FldErrMsg();
		}
		if (!ewr_CheckNumber($this->tot->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->tot->FldErrMsg();
		}
		if (!ewr_CheckNumber($this->outstand->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->outstand->FldErrMsg();
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
		$_SESSION["sel_Ledger_Balances_$parm"] = "";
		$_SESSION["rf_Ledger_Balances_$parm"] = "";
		$_SESSION["rt_Ledger_Balances_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_Ledger_Balances_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_Ledger_Balances_$parm"];
		$fld->RangeTo = @$_SESSION["rt_Ledger_Balances_$parm"];
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

		// Field datestatus
		$this->SetDefaultExtFilter($this->datestatus, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->datestatus);

		// Field lastpaymentdate
		$this->SetDefaultExtFilter($this->lastpaymentdate, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->lastpaymentdate);

		// Field price
		$this->SetDefaultExtFilter($this->price, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->price);

		// Field tot
		$this->SetDefaultExtFilter($this->tot, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->tot);

		// Field outstand
		$this->SetDefaultExtFilter($this->outstand, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->outstand);
		/**
		* Set up default values for popup filters
		*/
	}

	// Check if filter applied
	function CheckFilter() {

		// Check number text filter
		if ($this->TextFilterApplied($this->number))
			return TRUE;

		// Check datestatus text filter
		if ($this->TextFilterApplied($this->datestatus))
			return TRUE;

		// Check lastpaymentdate text filter
		if ($this->TextFilterApplied($this->lastpaymentdate))
			return TRUE;

		// Check price text filter
		if ($this->TextFilterApplied($this->price))
			return TRUE;

		// Check tot text filter
		if ($this->TextFilterApplied($this->tot))
			return TRUE;

		// Check outstand text filter
		if ($this->TextFilterApplied($this->outstand))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList($showDate = FALSE) {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field number
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->number, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->number->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field datestatus
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->datestatus, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->datestatus->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field lastpaymentdate
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->lastpaymentdate, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->lastpaymentdate->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field price
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->price, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->price->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field tot
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->tot, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->tot->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field outstand
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->outstand, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->outstand->FldCaption() . "</span>" . $sFilter . "</div>";
		$divstyle = "";
		$divdataclass = "";

		// Show Filters
		if ($sFilterList <> "" || $showDate) {
			$sMessage = "<div" . $divstyle . $divdataclass . "><div id=\"ewrFilterList\" class=\"alert alert-info ewDisplayTable\">";
			if ($showDate)
				$sMessage .= "<div id=\"ewrCurrentDate\">" . $ReportLanguage->Phrase("ReportGeneratedDate") . ewr_FormatDateTime(date("Y-m-d H:i:s"), 1) . "</div>";
			if ($sFilterList <> "")
				$sMessage .= "<div id=\"ewrCurrentFilters\">" . $ReportLanguage->Phrase("CurrentFilters") . "</div>" . $sFilterList;
			$sMessage .= "</div></div>";
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
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field lastpaymentdate
		$sWrk = "";
		if ($this->lastpaymentdate->SearchValue <> "" || $this->lastpaymentdate->SearchValue2 <> "") {
			$sWrk = "\"sv_lastpaymentdate\":\"" . ewr_JsEncode2($this->lastpaymentdate->SearchValue) . "\"," .
				"\"so_lastpaymentdate\":\"" . ewr_JsEncode2($this->lastpaymentdate->SearchOperator) . "\"," .
				"\"sc_lastpaymentdate\":\"" . ewr_JsEncode2($this->lastpaymentdate->SearchCondition) . "\"," .
				"\"sv2_lastpaymentdate\":\"" . ewr_JsEncode2($this->lastpaymentdate->SearchValue2) . "\"," .
				"\"so2_lastpaymentdate\":\"" . ewr_JsEncode2($this->lastpaymentdate->SearchOperator2) . "\"";
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
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field tot
		$sWrk = "";
		if ($this->tot->SearchValue <> "" || $this->tot->SearchValue2 <> "") {
			$sWrk = "\"sv_tot\":\"" . ewr_JsEncode2($this->tot->SearchValue) . "\"," .
				"\"so_tot\":\"" . ewr_JsEncode2($this->tot->SearchOperator) . "\"," .
				"\"sc_tot\":\"" . ewr_JsEncode2($this->tot->SearchCondition) . "\"," .
				"\"sv2_tot\":\"" . ewr_JsEncode2($this->tot->SearchValue2) . "\"," .
				"\"so2_tot\":\"" . ewr_JsEncode2($this->tot->SearchOperator2) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field outstand
		$sWrk = "";
		if ($this->outstand->SearchValue <> "" || $this->outstand->SearchValue2 <> "") {
			$sWrk = "\"sv_outstand\":\"" . ewr_JsEncode2($this->outstand->SearchValue) . "\"," .
				"\"so_outstand\":\"" . ewr_JsEncode2($this->outstand->SearchOperator) . "\"," .
				"\"sc_outstand\":\"" . ewr_JsEncode2($this->outstand->SearchCondition) . "\"," .
				"\"sv2_outstand\":\"" . ewr_JsEncode2($this->outstand->SearchValue2) . "\"," .
				"\"so2_outstand\":\"" . ewr_JsEncode2($this->outstand->SearchOperator2) . "\"";
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
		return $this->SetupFilterList($filter);
	}

	// Setup list of filters
	function SetupFilterList($filter) {
		if (!is_array($filter))
			return FALSE;

		// Field number
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_number", $filter) || array_key_exists("so_number", $filter) ||
			array_key_exists("sc_number", $filter) ||
			array_key_exists("sv2_number", $filter) || array_key_exists("so2_number", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_number"], @$filter["so_number"], @$filter["sc_number"], @$filter["sv2_number"], @$filter["so2_number"], "number");
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "number");
		}

		// Field datestatus
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_datestatus", $filter) || array_key_exists("so_datestatus", $filter) ||
			array_key_exists("sc_datestatus", $filter) ||
			array_key_exists("sv2_datestatus", $filter) || array_key_exists("so2_datestatus", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_datestatus"], @$filter["so_datestatus"], @$filter["sc_datestatus"], @$filter["sv2_datestatus"], @$filter["so2_datestatus"], "datestatus");
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "datestatus");
		}

		// Field lastpaymentdate
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_lastpaymentdate", $filter) || array_key_exists("so_lastpaymentdate", $filter) ||
			array_key_exists("sc_lastpaymentdate", $filter) ||
			array_key_exists("sv2_lastpaymentdate", $filter) || array_key_exists("so2_lastpaymentdate", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_lastpaymentdate"], @$filter["so_lastpaymentdate"], @$filter["sc_lastpaymentdate"], @$filter["sv2_lastpaymentdate"], @$filter["so2_lastpaymentdate"], "lastpaymentdate");
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "lastpaymentdate");
		}

		// Field price
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_price", $filter) || array_key_exists("so_price", $filter) ||
			array_key_exists("sc_price", $filter) ||
			array_key_exists("sv2_price", $filter) || array_key_exists("so2_price", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_price"], @$filter["so_price"], @$filter["sc_price"], @$filter["sv2_price"], @$filter["so2_price"], "price");
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "price");
		}

		// Field tot
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_tot", $filter) || array_key_exists("so_tot", $filter) ||
			array_key_exists("sc_tot", $filter) ||
			array_key_exists("sv2_tot", $filter) || array_key_exists("so2_tot", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_tot"], @$filter["so_tot"], @$filter["sc_tot"], @$filter["sv2_tot"], @$filter["so2_tot"], "tot");
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "tot");
		}

		// Field outstand
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_outstand", $filter) || array_key_exists("so_outstand", $filter) ||
			array_key_exists("sc_outstand", $filter) ||
			array_key_exists("sv2_outstand", $filter) || array_key_exists("so2_outstand", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_outstand"], @$filter["so_outstand"], @$filter["sc_outstand"], @$filter["sv2_outstand"], @$filter["so2_outstand"], "outstand");
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "outstand");
		}
		return TRUE;
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		return $sWrk;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort($options = array()) {
		if ($this->DrillDown)
			return "";
		$bResetSort = @$options["resetsort"] == "1" || @$_GET["cmd"] == "resetsort";
		$orderBy = (@$options["order"] <> "") ? @$options["order"] : ewr_StripSlashes(@$_GET["order"]);
		$orderType = (@$options["ordertype"] <> "") ? @$options["ordertype"] : ewr_StripSlashes(@$_GET["ordertype"]);

		// Check for a resetsort command
		if ($bResetSort) {
			$this->setOrderBy("");
			$this->setStartGroup(1);
			$this->number->setSort("");
			$this->area->setSort("");
			$this->datestatus->setSort("");
			$this->lastpaymentdate->setSort("");
			$this->price->setSort("");
			$this->tot->setSort("");
			$this->outstand->setSort("");

		// Check for an Order parameter
		} elseif ($orderBy <> "") {
			$this->CurrentOrder = $orderBy;
			$this->CurrentOrderType = $orderType;
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}
		return $this->getOrderBy();
	}

	// Export email
	function ExportEmail($EmailContent, $options = array()) {
		global $gTmpImages, $ReportLanguage;
		$bGenRequest = @$options["reporttype"] == "email";
		$sFailRespPfx = $bGenRequest ? "" : "<p class=\"text-error\">";
		$sSuccessRespPfx = $bGenRequest ? "" : "<p class=\"text-success\">";
		$sRespPfx = $bGenRequest ? "" : "</p>";
		$sContentType = (@$options["contenttype"] <> "") ? $options["contenttype"] : @$_POST["contenttype"];
		$sSender = (@$options["sender"] <> "") ? $options["sender"] : @$_POST["sender"];
		$sRecipient = (@$options["recipient"] <> "") ? $options["recipient"] : @$_POST["recipient"];
		$sCc = (@$options["cc"] <> "") ? $options["cc"] : @$_POST["cc"];
		$sBcc = (@$options["bcc"] <> "") ? $options["bcc"] : @$_POST["bcc"];

		// Subject
		$sEmailSubject = (@$options["subject"] <> "") ? $options["subject"] : ewr_StripSlashes(@$_POST["subject"]);

		// Message
		$sEmailMessage = (@$options["message"] <> "") ? $options["message"] : ewr_StripSlashes(@$_POST["message"]);

		// Check sender
		if ($sSender == "")
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterSenderEmail") . $sRespPfx;
		if (!ewr_CheckEmail($sSender))
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterProperSenderEmail") . $sRespPfx;

		// Check recipient
		if (!ewr_CheckEmailList($sRecipient, EWR_MAX_EMAIL_RECIPIENT))
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterProperRecipientEmail") . $sRespPfx;

		// Check cc
		if (!ewr_CheckEmailList($sCc, EWR_MAX_EMAIL_RECIPIENT))
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterProperCcEmail") . $sRespPfx;

		// Check bcc
		if (!ewr_CheckEmailList($sBcc, EWR_MAX_EMAIL_RECIPIENT))
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterProperBccEmail") . $sRespPfx;

		// Check email sent count
		$emailcount = $bGenRequest ? 0 : ewr_LoadEmailCount();
		if (intval($emailcount) >= EWR_MAX_EMAIL_SENT_COUNT)
			return $sFailRespPfx . $ReportLanguage->Phrase("ExceedMaxEmailExport") . $sRespPfx;
		if ($sEmailMessage <> "") {
			if (EWR_REMOVE_XSS) $sEmailMessage = ewr_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		$sAttachmentContent = ewr_AdjustEmailContent($EmailContent);
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
			return $sSuccessRespPfx . $ReportLanguage->Phrase("SendEmailSuccess") . $sRespPfx; // Set up success message
		} else {

			// Sent email failure
			return $sFailRespPfx . $Email->SendErrDescription . $sRespPfx;
		}
	}

	// Export to HTML
	function ExportHtml($html, $options = array()) {

		//global $gsExportFile;
		//header('Content-Type: text/html' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		//header('Content-Disposition: attachment; filename=' . $gsExportFile . '.html');

		$folder = @$this->GenOptions["folder"];
		$fileName = @$this->GenOptions["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";

		// Save generate file for print
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
			$baseTag = "<base href=\"" . ewr_BaseUrl() . "\">";
			$html = preg_replace('/<head>/', '<head>' . $baseTag, $html);
			ewr_SaveFile($folder, $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file")
			echo $html;
		return $saveToFile;
	}

	// Export to WORD
	function ExportWord($html, $options = array()) {
		global $gsExportFile;
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
		 	ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			header('Content-Type: application/vnd.ms-word' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
			header('Content-Disposition: attachment; filename=' . $gsExportFile . '.doc');
			echo $html;
		}
		return $saveToFile;
	}

	// Export to EXCEL
	function ExportExcel($html, $options = array()) {
		global $gsExportFile;
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
		 	ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			header('Content-Type: application/vnd.ms-excel' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
			header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
			echo $html;
		}
		return $saveToFile;
	}

	// Export PDF
	function ExportPdf($html, $options = array()) {
		global $gsExportFile;
		@ini_set("memory_limit", EWR_PDF_MEMORY_LIMIT);
		set_time_limit(EWR_PDF_TIME_LIMIT);
		if (EWR_DEBUG_ENABLED) // Add debug message
			$html = str_replace("</body>", ewr_DebugMsg() . "</body>", $html);
		$dompdf = new \Dompdf\Dompdf(array("pdf_backend" => "Cpdf"));
		$doc = new DOMDocument();
		@$doc->loadHTML('<?xml encoding="uft-8">' . ewr_ConvertToUtf8($html)); // Convert to utf-8
		$spans = $doc->getElementsByTagName("span");
		foreach ($spans as $span) {
			if ($span->getAttribute("class") == "ewFilterCaption")
				$span->parentNode->insertBefore($doc->createElement("span", ":&nbsp;"), $span->nextSibling);
		}
		$html = $doc->saveHTML();
		$html = ewr_ConvertFromUtf8($html);
		$dompdf->load_html($html);
		$dompdf->set_paper("a4", "portrait");
		$dompdf->render();
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
			ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $dompdf->output());
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			$sExportFile = strtolower(substr($gsExportFile, -4)) == ".pdf" ? $gsExportFile : $gsExportFile . ".pdf";
			$dompdf->stream($sExportFile, array("Attachment" => 1)); // 0 to open in browser, 1 to download
		}
		ewr_DeleteTmpImages($html);
		return $saveToFile;
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
if (!isset($Ledger_Balances_summary)) $Ledger_Balances_summary = new crLedger_Balances_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$Ledger_Balances_summary;

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
var Ledger_Balances_summary = new ewr_Page("Ledger_Balances_summary");

// Page properties
Ledger_Balances_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = Ledger_Balances_summary.PageID;

// Extend page with Chart_Rendering function
Ledger_Balances_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
Ledger_Balances_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fLedger_Balancessummary = new ewr_Form("fLedger_Balancessummary");

// Validate method
fLedger_Balancessummary.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	var elm = fobj.sv_datestatus;
	if (elm && !ewr_CheckDateDef(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->datestatus->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_lastpaymentdate;
	if (elm && typeof(EURODATE) == "function" && !EURODATE(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->lastpaymentdate->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_price;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->price->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_tot;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->tot->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_outstand;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->outstand->FldErrMsg()) ?>"))
			return false;
	}

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fLedger_Balancessummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fLedger_Balancessummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fLedger_Balancessummary.ValidateRequired = false; // No JavaScript validation
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
<?php if (@$Page->GenOptions["showfilter"] == "1") { ?>
<?php $Page->ShowFilterList(TRUE) ?>
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
	$Page->GenerateOptions->Render("body");
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
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<!-- Search form (begin) -->
<form name="fLedger_Balancessummary" id="fLedger_Balancessummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fLedger_Balancessummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_number" class="ewCell form-group">
	<label for="sv_number" class="ewSearchCaption ewLabel"><?php echo $Page->number->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_number" id="so_number" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->number->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->number->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->number->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->number->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->number->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->number->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->number->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->number->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->number->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->number->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->number->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->number->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->number->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->number->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_number" id="sv_number" name="sv_number" size="30" maxlength="111" placeholder="<?php echo $Page->number->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->number->SearchValue) ?>"<?php echo $Page->number->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_number" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_number" style="display: none">
<?php ewr_PrependClass($Page->number->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_number" id="sv2_number" name="sv2_number" size="30" maxlength="111" placeholder="<?php echo $Page->number->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->number->SearchValue2) ?>"<?php echo $Page->number->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_datestatus" class="ewCell form-group">
	<label for="sv_datestatus" class="ewSearchCaption ewLabel"><?php echo $Page->datestatus->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_datestatus" id="so_datestatus" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->datestatus->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->datestatus->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->datestatus->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->datestatus->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->datestatus->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->datestatus->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->datestatus->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->datestatus->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->datestatus->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->datestatus->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_datestatus" id="sv_datestatus" name="sv_datestatus" placeholder="<?php echo $Page->datestatus->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->datestatus->SearchValue) ?>"<?php echo $Page->datestatus->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_datestatus" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_datestatus" style="display: none">
<?php ewr_PrependClass($Page->datestatus->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_datestatus" id="sv2_datestatus" name="sv2_datestatus" placeholder="<?php echo $Page->datestatus->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->datestatus->SearchValue2) ?>"<?php echo $Page->datestatus->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_3" class="ewRow">
<div id="c_lastpaymentdate" class="ewCell form-group">
	<label for="sv_lastpaymentdate" class="ewSearchCaption ewLabel"><?php echo $Page->lastpaymentdate->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_lastpaymentdate" id="so_lastpaymentdate" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->lastpaymentdate->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->lastpaymentdate->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->lastpaymentdate->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->lastpaymentdate->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->lastpaymentdate->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->lastpaymentdate->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->lastpaymentdate->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->lastpaymentdate->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->lastpaymentdate->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->lastpaymentdate->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->lastpaymentdate->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->lastpaymentdate->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->lastpaymentdate->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->lastpaymentdate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_lastpaymentdate" id="sv_lastpaymentdate" name="sv_lastpaymentdate" size="30" maxlength="111" placeholder="<?php echo $Page->lastpaymentdate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->lastpaymentdate->SearchValue) ?>"<?php echo $Page->lastpaymentdate->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_lastpaymentdate" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_lastpaymentdate" style="display: none">
<?php ewr_PrependClass($Page->lastpaymentdate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_lastpaymentdate" id="sv2_lastpaymentdate" name="sv2_lastpaymentdate" size="30" maxlength="111" placeholder="<?php echo $Page->lastpaymentdate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->lastpaymentdate->SearchValue2) ?>"<?php echo $Page->lastpaymentdate->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_4" class="ewRow">
<div id="c_price" class="ewCell form-group">
	<label for="sv_price" class="ewSearchCaption ewLabel"><?php echo $Page->price->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_price" id="so_price" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->price->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->price->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->price->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->price->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->price->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->price->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->price->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->price->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->price->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->price->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_price" id="sv_price" name="sv_price" size="30" placeholder="<?php echo $Page->price->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->price->SearchValue) ?>"<?php echo $Page->price->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_price" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_price" style="display: none">
<?php ewr_PrependClass($Page->price->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_price" id="sv2_price" name="sv2_price" size="30" placeholder="<?php echo $Page->price->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->price->SearchValue2) ?>"<?php echo $Page->price->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_5" class="ewRow">
<div id="c_tot" class="ewCell form-group">
	<label for="sv_tot" class="ewSearchCaption ewLabel"><?php echo $Page->tot->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_tot" id="so_tot" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->tot->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->tot->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->tot->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->tot->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->tot->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->tot->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->tot->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->tot->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->tot->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->tot->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_tot" id="sv_tot" name="sv_tot" size="30" placeholder="<?php echo $Page->tot->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->tot->SearchValue) ?>"<?php echo $Page->tot->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_tot" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_tot" style="display: none">
<?php ewr_PrependClass($Page->tot->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_tot" id="sv2_tot" name="sv2_tot" size="30" placeholder="<?php echo $Page->tot->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->tot->SearchValue2) ?>"<?php echo $Page->tot->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_6" class="ewRow">
<div id="c_outstand" class="ewCell form-group">
	<label for="sv_outstand" class="ewSearchCaption ewLabel"><?php echo $Page->outstand->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_outstand" id="so_outstand" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->outstand->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->outstand->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->outstand->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->outstand->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->outstand->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->outstand->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->outstand->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->outstand->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->outstand->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->outstand->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_outstand" id="sv_outstand" name="sv_outstand" size="30" placeholder="<?php echo $Page->outstand->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->outstand->SearchValue) ?>"<?php echo $Page->outstand->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_outstand" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_outstand" style="display: none">
<?php ewr_PrependClass($Page->outstand->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Ledger_Balances" data-field="x_outstand" id="sv2_outstand" name="sv2_outstand" size="30" placeholder="<?php echo $Page->outstand->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->outstand->SearchValue2) ?>"<?php echo $Page->outstand->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fLedger_Balancessummary.Init();
fLedger_Balancessummary.FilterList = <?php echo $Page->GetFilterList() ?>;
</script>
<!-- Search form (end) -->
<?php } ?>
<?php if ($Page->ShowCurrentFilter) { ?>
<?php $Page->ShowFilterList() ?>
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
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "Ledger_Balancessmrypager.php" ?>
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
	<td data-field="number"><div class="Ledger_Balances_number"><span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="number">
<?php if ($Page->SortUrl($Page->number) == "") { ?>
		<div class="ewTableHeaderBtn Ledger_Balances_number">
			<span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Ledger_Balances_number" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->number) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->number->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->number->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->number->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="area"><div class="Ledger_Balances_area"><span class="ewTableHeaderCaption"><?php echo $Page->area->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="area">
<?php if ($Page->SortUrl($Page->area) == "") { ?>
		<div class="ewTableHeaderBtn Ledger_Balances_area">
			<span class="ewTableHeaderCaption"><?php echo $Page->area->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Ledger_Balances_area" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->area) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->area->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->area->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->area->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="datestatus"><div class="Ledger_Balances_datestatus"><span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="datestatus">
<?php if ($Page->SortUrl($Page->datestatus) == "") { ?>
		<div class="ewTableHeaderBtn Ledger_Balances_datestatus">
			<span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Ledger_Balances_datestatus" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->datestatus) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->datestatus->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->datestatus->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->datestatus->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->lastpaymentdate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="lastpaymentdate"><div class="Ledger_Balances_lastpaymentdate"><span class="ewTableHeaderCaption"><?php echo $Page->lastpaymentdate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="lastpaymentdate">
<?php if ($Page->SortUrl($Page->lastpaymentdate) == "") { ?>
		<div class="ewTableHeaderBtn Ledger_Balances_lastpaymentdate">
			<span class="ewTableHeaderCaption"><?php echo $Page->lastpaymentdate->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Ledger_Balances_lastpaymentdate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->lastpaymentdate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->lastpaymentdate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->lastpaymentdate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->lastpaymentdate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="price"><div class="Ledger_Balances_price"><span class="ewTableHeaderCaption"><?php echo $Page->price->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="price">
<?php if ($Page->SortUrl($Page->price) == "") { ?>
		<div class="ewTableHeaderBtn Ledger_Balances_price">
			<span class="ewTableHeaderCaption"><?php echo $Page->price->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Ledger_Balances_price" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->price) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->price->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->price->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->price->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->tot->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="tot"><div class="Ledger_Balances_tot"><span class="ewTableHeaderCaption"><?php echo $Page->tot->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="tot">
<?php if ($Page->SortUrl($Page->tot) == "") { ?>
		<div class="ewTableHeaderBtn Ledger_Balances_tot">
			<span class="ewTableHeaderCaption"><?php echo $Page->tot->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Ledger_Balances_tot" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->tot) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->tot->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->tot->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->tot->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->outstand->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="outstand"><div class="Ledger_Balances_outstand"><span class="ewTableHeaderCaption"><?php echo $Page->outstand->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="outstand">
<?php if ($Page->SortUrl($Page->outstand) == "") { ?>
		<div class="ewTableHeaderBtn Ledger_Balances_outstand">
			<span class="ewTableHeaderCaption"><?php echo $Page->outstand->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Ledger_Balances_outstand" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->outstand) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->outstand->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->outstand->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->outstand->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
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
?>
<?php

		// Render detail row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Ledger_Balances_number"<?php echo $Page->number->ViewAttributes() ?>><?php echo $Page->number->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Ledger_Balances_area"<?php echo $Page->area->ViewAttributes() ?>><?php echo $Page->area->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Ledger_Balances_datestatus"<?php echo $Page->datestatus->ViewAttributes() ?>><?php echo $Page->datestatus->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->lastpaymentdate->Visible) { ?>
		<td data-field="lastpaymentdate"<?php echo $Page->lastpaymentdate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Ledger_Balances_lastpaymentdate"<?php echo $Page->lastpaymentdate->ViewAttributes() ?>><?php echo $Page->lastpaymentdate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Ledger_Balances_price"<?php echo $Page->price->ViewAttributes() ?>><?php echo $Page->price->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->tot->Visible) { ?>
		<td data-field="tot"<?php echo $Page->tot->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Ledger_Balances_tot"<?php echo $Page->tot->ViewAttributes() ?>><?php echo $Page->tot->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->outstand->Visible) { ?>
		<td data-field="outstand"<?php echo $Page->outstand->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Ledger_Balances_outstand"<?php echo $Page->outstand->ViewAttributes() ?>><?php echo $Page->outstand->ListViewValue() ?></span></td>
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
	$Page->price->Count = $Page->GrandCnt[5];
	$Page->price->SumValue = $Page->GrandSmry[5]; // Load SUM
	$Page->tot->Count = $Page->GrandCnt[6];
	$Page->tot->SumValue = $Page->GrandSmry[6]; // Load SUM
	$Page->outstand->Count = $Page->GrandCnt[7];
	$Page->outstand->SumValue = $Page->GrandSmry[7]; // Load SUM
	$Page->price->Count = $Page->GrandCnt[5];
	$Page->price->AvgValue = ($Page->price->Count > 0) ? $Page->GrandSmry[5]/$Page->price->Count : 0; // Load AVG
	$Page->tot->Count = $Page->GrandCnt[6];
	$Page->tot->AvgValue = ($Page->tot->Count > 0) ? $Page->GrandSmry[6]/$Page->tot->Count : 0; // Load AVG
	$Page->outstand->Count = $Page->GrandCnt[7];
	$Page->outstand->AvgValue = ($Page->outstand->Count > 0) ? $Page->GrandSmry[7]/$Page->outstand->Count : 0; // Load AVG
	$Page->price->Count = $Page->GrandCnt[5];
	$Page->price->MinValue = $Page->GrandMn[5]; // Load MIN
	$Page->tot->Count = $Page->GrandCnt[6];
	$Page->tot->MinValue = $Page->GrandMn[6]; // Load MIN
	$Page->outstand->Count = $Page->GrandCnt[7];
	$Page->outstand->MinValue = $Page->GrandMn[7]; // Load MIN
	$Page->price->Count = $Page->GrandCnt[5];
	$Page->price->MaxValue = $Page->GrandMx[5]; // Load MAX
	$Page->tot->Count = $Page->GrandCnt[6];
	$Page->tot->MaxValue = $Page->GrandMx[6]; // Load MAX
	$Page->outstand->Count = $Page->GrandCnt[7];
	$Page->outstand->MaxValue = $Page->GrandMx[7]; // Load MAX
	$Page->price->Count = $Page->GrandCnt[5];
	$Page->price->CntValue = $Page->GrandCnt[5]; // Load CNT
	$Page->tot->Count = $Page->GrandCnt[6];
	$Page->tot->CntValue = $Page->GrandCnt[6]; // Load CNT
	$Page->outstand->Count = $Page->GrandCnt[7];
	$Page->outstand->CntValue = $Page->GrandCnt[7]; // Load CNT
	$Page->ResetAttrs();
	$Page->RowType = EWR_ROWTYPE_TOTAL;
	$Page->RowTotalType = EWR_ROWTOTAL_GRAND;
	$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes() ?>><td colspan="<?php echo ($Page->GrpColumnCount + $Page->DtlColumnCount) ?>"><?php echo $ReportLanguage->Phrase("RptGrandSummary") ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->TotCount,0,-2,-2,-2); ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td></tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->lastpaymentdate->Visible) { ?>
		<td data-field="lastpaymentdate"<?php echo $Page->lastpaymentdate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tpts_Ledger_Balances_price"<?php echo $Page->price->ViewAttributes() ?>><?php echo $Page->price->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->tot->Visible) { ?>
		<td data-field="tot"<?php echo $Page->tot->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tpts_Ledger_Balances_tot"<?php echo $Page->tot->ViewAttributes() ?>><?php echo $Page->tot->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->outstand->Visible) { ?>
		<td data-field="outstand"<?php echo $Page->outstand->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tpts_Ledger_Balances_outstand"<?php echo $Page->outstand->ViewAttributes() ?>><?php echo $Page->outstand->SumViewValue ?></span></td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->lastpaymentdate->Visible) { ?>
		<td data-field="lastpaymentdate"<?php echo $Page->lastpaymentdate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptAvg") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tpta_Ledger_Balances_price"<?php echo $Page->price->ViewAttributes() ?>><?php echo $Page->price->AvgViewValue ?></span></td>
<?php } ?>
<?php if ($Page->tot->Visible) { ?>
		<td data-field="tot"<?php echo $Page->tot->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptAvg") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tpta_Ledger_Balances_tot"<?php echo $Page->tot->ViewAttributes() ?>><?php echo $Page->tot->AvgViewValue ?></span></td>
<?php } ?>
<?php if ($Page->outstand->Visible) { ?>
		<td data-field="outstand"<?php echo $Page->outstand->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptAvg") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tpta_Ledger_Balances_outstand"<?php echo $Page->outstand->ViewAttributes() ?>><?php echo $Page->outstand->AvgViewValue ?></span></td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->lastpaymentdate->Visible) { ?>
		<td data-field="lastpaymentdate"<?php echo $Page->lastpaymentdate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptMin") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptn_Ledger_Balances_price"<?php echo $Page->price->ViewAttributes() ?>><?php echo $Page->price->MinViewValue ?></span></td>
<?php } ?>
<?php if ($Page->tot->Visible) { ?>
		<td data-field="tot"<?php echo $Page->tot->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptMin") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptn_Ledger_Balances_tot"<?php echo $Page->tot->ViewAttributes() ?>><?php echo $Page->tot->MinViewValue ?></span></td>
<?php } ?>
<?php if ($Page->outstand->Visible) { ?>
		<td data-field="outstand"<?php echo $Page->outstand->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptMin") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptn_Ledger_Balances_outstand"<?php echo $Page->outstand->ViewAttributes() ?>><?php echo $Page->outstand->MinViewValue ?></span></td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->lastpaymentdate->Visible) { ?>
		<td data-field="lastpaymentdate"<?php echo $Page->lastpaymentdate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptMax") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptx_Ledger_Balances_price"<?php echo $Page->price->ViewAttributes() ?>><?php echo $Page->price->MaxViewValue ?></span></td>
<?php } ?>
<?php if ($Page->tot->Visible) { ?>
		<td data-field="tot"<?php echo $Page->tot->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptMax") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptx_Ledger_Balances_tot"<?php echo $Page->tot->ViewAttributes() ?>><?php echo $Page->tot->MaxViewValue ?></span></td>
<?php } ?>
<?php if ($Page->outstand->Visible) { ?>
		<td data-field="outstand"<?php echo $Page->outstand->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptMax") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptx_Ledger_Balances_outstand"<?php echo $Page->outstand->ViewAttributes() ?>><?php echo $Page->outstand->MaxViewValue ?></span></td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->number->Visible) { ?>
		<td data-field="number"<?php echo $Page->number->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->area->Visible) { ?>
		<td data-field="area"<?php echo $Page->area->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->datestatus->Visible) { ?>
		<td data-field="datestatus"<?php echo $Page->datestatus->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->lastpaymentdate->Visible) { ?>
		<td data-field="lastpaymentdate"<?php echo $Page->lastpaymentdate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->price->Visible) { ?>
		<td data-field="price"<?php echo $Page->price->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptc_Ledger_Balances_price"<?php echo $Page->price->ViewAttributes() ?>><?php echo $Page->price->CntViewValue ?></span></td>
<?php } ?>
<?php if ($Page->tot->Visible) { ?>
		<td data-field="tot"<?php echo $Page->tot->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptc_Ledger_Balances_tot"<?php echo $Page->tot->ViewAttributes() ?>><?php echo $Page->tot->CntViewValue ?></span></td>
<?php } ?>
<?php if ($Page->outstand->Visible) { ?>
		<td data-field="outstand"<?php echo $Page->outstand->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptc_Ledger_Balances_outstand"<?php echo $Page->outstand->ViewAttributes() ?>><?php echo $Page->outstand->CntViewValue ?></span></td>
<?php } ?>
	</tr>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && FALSE) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "Ledger_Balancessmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || FALSE) { // Show footer ?>
</table>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->TotalGrps > 0) { ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "Ledger_Balancessmrypager.php" ?>
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
