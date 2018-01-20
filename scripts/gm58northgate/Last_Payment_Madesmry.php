<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg10.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn10.php" ?>
<?php include_once "phprptinc/ewrusrfn10.php" ?>
<?php include_once "Last_Payment_Madesmryinfo.php" ?>
<?php

//
// Page class
//

$Last_Payment_Made_summary = NULL; // Initialize page object first

class crLast_Payment_Made_summary extends crLast_Payment_Made {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{8971DFF8-CD58-406F-905F-ABF6EB424D34}";

	// Page object name
	var $PageObjName = 'Last_Payment_Made_summary';

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

		// Table object (Last_Payment_Made)
		if (!isset($GLOBALS["Last_Payment_Made"])) {
			$GLOBALS["Last_Payment_Made"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Last_Payment_Made"];
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
			define("EWR_TABLE_NAME", 'Last Payment Made', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fLast_Payment_Madesummary";

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
		$this->NAME->PlaceHolder = $this->NAME->FldCaption();
		$this->surname->PlaceHolder = $this->surname->FldCaption();
		$this->standnumber->PlaceHolder = $this->standnumber->FldCaption();
		$this->balance->PlaceHolder = $this->balance->FldCaption();
		$this->instalments->PlaceHolder = $this->instalments->FldCaption();
		$this->paymentnumbermonth->PlaceHolder = $this->paymentnumbermonth->FldCaption();
		$this->paymentmonth->PlaceHolder = $this->paymentmonth->FldCaption();

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
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_Last_Payment_Made\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_Last_Payment_Made',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fLast_Payment_Madesummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fLast_Payment_Madesummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fLast_Payment_Madesummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$this->NAME->SetVisibility();
		$this->surname->SetVisibility();
		$this->standnumber->SetVisibility();
		$this->balance->SetVisibility();
		$this->instalments->SetVisibility();
		$this->paymentnumbermonth->SetVisibility();
		$this->paymentmonth->SetVisibility();

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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(TRUE,TRUE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->NAME->SelectionList = "";
		$this->NAME->DefaultSelectionList = "";
		$this->NAME->ValueList = "";
		$this->surname->SelectionList = "";
		$this->surname->DefaultSelectionList = "";
		$this->surname->ValueList = "";
		$this->standnumber->SelectionList = "";
		$this->standnumber->DefaultSelectionList = "";
		$this->standnumber->ValueList = "";
		$this->balance->SelectionList = "";
		$this->balance->DefaultSelectionList = "";
		$this->balance->ValueList = "";
		$this->instalments->SelectionList = "";
		$this->instalments->DefaultSelectionList = "";
		$this->instalments->ValueList = "";
		$this->paymentnumbermonth->SelectionList = "";
		$this->paymentnumbermonth->DefaultSelectionList = "";
		$this->paymentnumbermonth->ValueList = "";
		$this->paymentmonth->SelectionList = "";
		$this->paymentmonth->DefaultSelectionList = "";
		$this->paymentmonth->ValueList = "";

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
		$this->ShowHeader = TRUE;

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
				$this->FirstRowData['NAME'] = ewr_Conv($rs->fields('NAME'), 200);
				$this->FirstRowData['surname'] = ewr_Conv($rs->fields('surname'), 200);
				$this->FirstRowData['standnumber'] = ewr_Conv($rs->fields('standnumber'), 200);
				$this->FirstRowData['balance'] = ewr_Conv($rs->fields('balance'), 131);
				$this->FirstRowData['instalments'] = ewr_Conv($rs->fields('instalments'), 131);
				$this->FirstRowData['paymentnumbermonth'] = ewr_Conv($rs->fields('paymentnumbermonth'), 3);
				$this->FirstRowData['paymentmonth'] = ewr_Conv($rs->fields('paymentmonth'), 200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->NAME->setDbValue($rs->fields('NAME'));
			$this->surname->setDbValue($rs->fields('surname'));
			$this->standnumber->setDbValue($rs->fields('standnumber'));
			$this->balance->setDbValue($rs->fields('balance'));
			$this->instalments->setDbValue($rs->fields('instalments'));
			$this->paymentnumbermonth->setDbValue($rs->fields('paymentnumbermonth'));
			$this->paymentmonth->setDbValue($rs->fields('paymentmonth'));
			$this->Val[1] = $this->NAME->CurrentValue;
			$this->Val[2] = $this->surname->CurrentValue;
			$this->Val[3] = $this->standnumber->CurrentValue;
			$this->Val[4] = $this->balance->CurrentValue;
			$this->Val[5] = $this->instalments->CurrentValue;
			$this->Val[6] = $this->paymentnumbermonth->CurrentValue;
			$this->Val[7] = $this->paymentmonth->CurrentValue;
		} else {
			$this->NAME->setDbValue("");
			$this->surname->setDbValue("");
			$this->standnumber->setDbValue("");
			$this->balance->setDbValue("");
			$this->instalments->setDbValue("");
			$this->paymentnumbermonth->setDbValue("");
			$this->paymentmonth->setDbValue("");
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
			// Build distinct values for NAME

			if ($popupname == 'Last_Payment_Made_NAME') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->NAME, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->NAME->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->NAME->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->NAME->setDbValue($rswrk->fields[0]);
					$this->NAME->ViewValue = @$rswrk->fields[1];
					if (is_null($this->NAME->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->NAME->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						ewr_SetupDistinctValues($this->NAME->ValueList, $this->NAME->CurrentValue, $this->NAME->ViewValue, FALSE, $this->NAME->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->NAME->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->NAME->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->NAME;
			}

			// Build distinct values for surname
			if ($popupname == 'Last_Payment_Made_surname') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->surname, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->surname->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->surname->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->surname->setDbValue($rswrk->fields[0]);
					$this->surname->ViewValue = @$rswrk->fields[1];
					if (is_null($this->surname->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->surname->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
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

			// Build distinct values for standnumber
			if ($popupname == 'Last_Payment_Made_standnumber') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->standnumber, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->standnumber->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->standnumber->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->standnumber->setDbValue($rswrk->fields[0]);
					$this->standnumber->ViewValue = @$rswrk->fields[1];
					if (is_null($this->standnumber->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->standnumber->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						ewr_SetupDistinctValues($this->standnumber->ValueList, $this->standnumber->CurrentValue, $this->standnumber->ViewValue, FALSE, $this->standnumber->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->standnumber->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->standnumber->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->standnumber;
			}

			// Build distinct values for balance
			if ($popupname == 'Last_Payment_Made_balance') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->balance, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->balance->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->balance->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->balance->setDbValue($rswrk->fields[0]);
					$this->balance->ViewValue = @$rswrk->fields[1];
					if (is_null($this->balance->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->balance->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						ewr_SetupDistinctValues($this->balance->ValueList, $this->balance->CurrentValue, $this->balance->ViewValue, FALSE, $this->balance->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->balance->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->balance->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->balance;
			}

			// Build distinct values for instalments
			if ($popupname == 'Last_Payment_Made_instalments') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->instalments, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->instalments->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->instalments->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->instalments->setDbValue($rswrk->fields[0]);
					$this->instalments->ViewValue = @$rswrk->fields[1];
					if (is_null($this->instalments->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->instalments->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
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

			// Build distinct values for paymentnumbermonth
			if ($popupname == 'Last_Payment_Made_paymentnumbermonth') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->paymentnumbermonth, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->paymentnumbermonth->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->paymentnumbermonth->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->paymentnumbermonth->setDbValue($rswrk->fields[0]);
					$this->paymentnumbermonth->ViewValue = @$rswrk->fields[1];
					if (is_null($this->paymentnumbermonth->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->paymentnumbermonth->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						ewr_SetupDistinctValues($this->paymentnumbermonth->ValueList, $this->paymentnumbermonth->CurrentValue, $this->paymentnumbermonth->ViewValue, FALSE, $this->paymentnumbermonth->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->paymentnumbermonth->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->paymentnumbermonth->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->paymentnumbermonth;
			}

			// Build distinct values for paymentmonth
			if ($popupname == 'Last_Payment_Made_paymentmonth') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->paymentmonth, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->paymentmonth->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->paymentmonth->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->paymentmonth->setDbValue($rswrk->fields[0]);
					$this->paymentmonth->ViewValue = @$rswrk->fields[1];
					if (is_null($this->paymentmonth->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->paymentmonth->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						ewr_SetupDistinctValues($this->paymentmonth->ValueList, $this->paymentmonth->CurrentValue, $this->paymentmonth->ViewValue, FALSE, $this->paymentmonth->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->paymentmonth->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->paymentmonth->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->paymentmonth;
			}

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
				$this->ClearSessionSelection('NAME');
				$this->ClearSessionSelection('surname');
				$this->ClearSessionSelection('standnumber');
				$this->ClearSessionSelection('balance');
				$this->ClearSessionSelection('instalments');
				$this->ClearSessionSelection('paymentnumbermonth');
				$this->ClearSessionSelection('paymentmonth');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get NAME selected values

		if (is_array(@$_SESSION["sel_Last_Payment_Made_NAME"])) {
			$this->LoadSelectionFromSession('NAME');
		} elseif (@$_SESSION["sel_Last_Payment_Made_NAME"] == EWR_INIT_VALUE) { // Select all
			$this->NAME->SelectionList = "";
		}

		// Get surname selected values
		if (is_array(@$_SESSION["sel_Last_Payment_Made_surname"])) {
			$this->LoadSelectionFromSession('surname');
		} elseif (@$_SESSION["sel_Last_Payment_Made_surname"] == EWR_INIT_VALUE) { // Select all
			$this->surname->SelectionList = "";
		}

		// Get standnumber selected values
		if (is_array(@$_SESSION["sel_Last_Payment_Made_standnumber"])) {
			$this->LoadSelectionFromSession('standnumber');
		} elseif (@$_SESSION["sel_Last_Payment_Made_standnumber"] == EWR_INIT_VALUE) { // Select all
			$this->standnumber->SelectionList = "";
		}

		// Get balance selected values
		if (is_array(@$_SESSION["sel_Last_Payment_Made_balance"])) {
			$this->LoadSelectionFromSession('balance');
		} elseif (@$_SESSION["sel_Last_Payment_Made_balance"] == EWR_INIT_VALUE) { // Select all
			$this->balance->SelectionList = "";
		}

		// Get instalments selected values
		if (is_array(@$_SESSION["sel_Last_Payment_Made_instalments"])) {
			$this->LoadSelectionFromSession('instalments');
		} elseif (@$_SESSION["sel_Last_Payment_Made_instalments"] == EWR_INIT_VALUE) { // Select all
			$this->instalments->SelectionList = "";
		}

		// Get paymentnumbermonth selected values
		if (is_array(@$_SESSION["sel_Last_Payment_Made_paymentnumbermonth"])) {
			$this->LoadSelectionFromSession('paymentnumbermonth');
		} elseif (@$_SESSION["sel_Last_Payment_Made_paymentnumbermonth"] == EWR_INIT_VALUE) { // Select all
			$this->paymentnumbermonth->SelectionList = "";
		}

		// Get paymentmonth selected values
		if (is_array(@$_SESSION["sel_Last_Payment_Made_paymentmonth"])) {
			$this->LoadSelectionFromSession('paymentmonth');
		} elseif (@$_SESSION["sel_Last_Payment_Made_paymentmonth"] == EWR_INIT_VALUE) { // Select all
			$this->paymentmonth->SelectionList = "";
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
				$this->GrandSmry[4] = $rsagg->fields("sum_balance");
				$this->GrandSmry[4] = $rsagg->fields("sum_balance");
				$this->GrandMn[4] = $rsagg->fields("min_balance");
				$this->GrandMx[4] = $rsagg->fields("max_balance");
				$this->GrandCnt[4] = $rsagg->fields("cnt_balance");
				$this->GrandCnt[5] = $this->TotCount;
				$this->GrandCnt[6] = $this->TotCount;
				$this->GrandCnt[7] = $this->TotCount;
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

			// balance
			$this->balance->SumViewValue = $this->balance->SumValue;
			$this->balance->SumViewValue = ewr_FormatNumber($this->balance->SumViewValue, 2, -2, -2, -2);
			$this->balance->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// balance
			$this->balance->AvgViewValue = $this->balance->AvgValue;
			$this->balance->AvgViewValue = ewr_FormatNumber($this->balance->AvgViewValue, 2, -2, -2, -2);
			$this->balance->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// balance
			$this->balance->MinViewValue = $this->balance->MinValue;
			$this->balance->MinViewValue = ewr_FormatNumber($this->balance->MinViewValue, 2, -2, -2, -2);
			$this->balance->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// balance
			$this->balance->MaxViewValue = $this->balance->MaxValue;
			$this->balance->MaxViewValue = ewr_FormatNumber($this->balance->MaxViewValue, 2, -2, -2, -2);
			$this->balance->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// balance
			$this->balance->CntViewValue = $this->balance->CntValue;
			$this->balance->CntViewValue = ewr_FormatNumber($this->balance->CntViewValue, 0, -2, -2, -2);
			$this->balance->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// NAME
			$this->NAME->HrefValue = "";

			// surname
			$this->surname->HrefValue = "";

			// standnumber
			$this->standnumber->HrefValue = "";

			// balance
			$this->balance->HrefValue = "";

			// instalments
			$this->instalments->HrefValue = "";

			// paymentnumbermonth
			$this->paymentnumbermonth->HrefValue = "";

			// paymentmonth
			$this->paymentmonth->HrefValue = "";
		} else {
			if ($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER) {
			} else {
			}

			// NAME
			$this->NAME->ViewValue = $this->NAME->CurrentValue;
			$this->NAME->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// surname
			$this->surname->ViewValue = $this->surname->CurrentValue;
			$this->surname->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// standnumber
			$this->standnumber->ViewValue = $this->standnumber->CurrentValue;
			$this->standnumber->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// balance
			$this->balance->ViewValue = $this->balance->CurrentValue;
			$this->balance->ViewValue = ewr_FormatNumber($this->balance->ViewValue, 2, -2, -2, -2);
			$this->balance->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// instalments
			$this->instalments->ViewValue = $this->instalments->CurrentValue;
			$this->instalments->ViewValue = ewr_FormatNumber($this->instalments->ViewValue, 2, -2, -2, -2);
			$this->instalments->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// paymentnumbermonth
			$this->paymentnumbermonth->ViewValue = $this->paymentnumbermonth->CurrentValue;
			$this->paymentnumbermonth->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// paymentmonth
			$this->paymentmonth->ViewValue = $this->paymentmonth->CurrentValue;
			$this->paymentmonth->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// NAME
			$this->NAME->HrefValue = "";

			// surname
			$this->surname->HrefValue = "";

			// standnumber
			$this->standnumber->HrefValue = "";

			// balance
			$this->balance->HrefValue = "";

			// instalments
			$this->instalments->HrefValue = "";

			// paymentnumbermonth
			$this->paymentnumbermonth->HrefValue = "";

			// paymentmonth
			$this->paymentmonth->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// balance
			$CurrentValue = $this->balance->SumValue;
			$ViewValue = &$this->balance->SumViewValue;
			$ViewAttrs = &$this->balance->ViewAttrs;
			$CellAttrs = &$this->balance->CellAttrs;
			$HrefValue = &$this->balance->HrefValue;
			$LinkAttrs = &$this->balance->LinkAttrs;
			$this->Cell_Rendered($this->balance, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// balance
			$CurrentValue = $this->balance->AvgValue;
			$ViewValue = &$this->balance->AvgViewValue;
			$ViewAttrs = &$this->balance->ViewAttrs;
			$CellAttrs = &$this->balance->CellAttrs;
			$HrefValue = &$this->balance->HrefValue;
			$LinkAttrs = &$this->balance->LinkAttrs;
			$this->Cell_Rendered($this->balance, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// balance
			$CurrentValue = $this->balance->MinValue;
			$ViewValue = &$this->balance->MinViewValue;
			$ViewAttrs = &$this->balance->ViewAttrs;
			$CellAttrs = &$this->balance->CellAttrs;
			$HrefValue = &$this->balance->HrefValue;
			$LinkAttrs = &$this->balance->LinkAttrs;
			$this->Cell_Rendered($this->balance, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// balance
			$CurrentValue = $this->balance->MaxValue;
			$ViewValue = &$this->balance->MaxViewValue;
			$ViewAttrs = &$this->balance->ViewAttrs;
			$CellAttrs = &$this->balance->CellAttrs;
			$HrefValue = &$this->balance->HrefValue;
			$LinkAttrs = &$this->balance->LinkAttrs;
			$this->Cell_Rendered($this->balance, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// balance
			$CurrentValue = $this->balance->CntValue;
			$ViewValue = &$this->balance->CntViewValue;
			$ViewAttrs = &$this->balance->ViewAttrs;
			$CellAttrs = &$this->balance->CellAttrs;
			$HrefValue = &$this->balance->HrefValue;
			$LinkAttrs = &$this->balance->LinkAttrs;
			$this->Cell_Rendered($this->balance, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		} else {

			// NAME
			$CurrentValue = $this->NAME->CurrentValue;
			$ViewValue = &$this->NAME->ViewValue;
			$ViewAttrs = &$this->NAME->ViewAttrs;
			$CellAttrs = &$this->NAME->CellAttrs;
			$HrefValue = &$this->NAME->HrefValue;
			$LinkAttrs = &$this->NAME->LinkAttrs;
			$this->Cell_Rendered($this->NAME, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// surname
			$CurrentValue = $this->surname->CurrentValue;
			$ViewValue = &$this->surname->ViewValue;
			$ViewAttrs = &$this->surname->ViewAttrs;
			$CellAttrs = &$this->surname->CellAttrs;
			$HrefValue = &$this->surname->HrefValue;
			$LinkAttrs = &$this->surname->LinkAttrs;
			$this->Cell_Rendered($this->surname, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// standnumber
			$CurrentValue = $this->standnumber->CurrentValue;
			$ViewValue = &$this->standnumber->ViewValue;
			$ViewAttrs = &$this->standnumber->ViewAttrs;
			$CellAttrs = &$this->standnumber->CellAttrs;
			$HrefValue = &$this->standnumber->HrefValue;
			$LinkAttrs = &$this->standnumber->LinkAttrs;
			$this->Cell_Rendered($this->standnumber, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// balance
			$CurrentValue = $this->balance->CurrentValue;
			$ViewValue = &$this->balance->ViewValue;
			$ViewAttrs = &$this->balance->ViewAttrs;
			$CellAttrs = &$this->balance->CellAttrs;
			$HrefValue = &$this->balance->HrefValue;
			$LinkAttrs = &$this->balance->LinkAttrs;
			$this->Cell_Rendered($this->balance, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// instalments
			$CurrentValue = $this->instalments->CurrentValue;
			$ViewValue = &$this->instalments->ViewValue;
			$ViewAttrs = &$this->instalments->ViewAttrs;
			$CellAttrs = &$this->instalments->CellAttrs;
			$HrefValue = &$this->instalments->HrefValue;
			$LinkAttrs = &$this->instalments->LinkAttrs;
			$this->Cell_Rendered($this->instalments, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// paymentnumbermonth
			$CurrentValue = $this->paymentnumbermonth->CurrentValue;
			$ViewValue = &$this->paymentnumbermonth->ViewValue;
			$ViewAttrs = &$this->paymentnumbermonth->ViewAttrs;
			$CellAttrs = &$this->paymentnumbermonth->CellAttrs;
			$HrefValue = &$this->paymentnumbermonth->HrefValue;
			$LinkAttrs = &$this->paymentnumbermonth->LinkAttrs;
			$this->Cell_Rendered($this->paymentnumbermonth, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// paymentmonth
			$CurrentValue = $this->paymentmonth->CurrentValue;
			$ViewValue = &$this->paymentmonth->ViewValue;
			$ViewAttrs = &$this->paymentmonth->ViewAttrs;
			$CellAttrs = &$this->paymentmonth->CellAttrs;
			$HrefValue = &$this->paymentmonth->HrefValue;
			$LinkAttrs = &$this->paymentmonth->LinkAttrs;
			$this->Cell_Rendered($this->paymentmonth, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->NAME->Visible) $this->DtlColumnCount += 1;
		if ($this->surname->Visible) $this->DtlColumnCount += 1;
		if ($this->standnumber->Visible) $this->DtlColumnCount += 1;
		if ($this->balance->Visible) $this->DtlColumnCount += 1;
		if ($this->instalments->Visible) $this->DtlColumnCount += 1;
		if ($this->paymentnumbermonth->Visible) $this->DtlColumnCount += 1;
		if ($this->paymentmonth->Visible) $this->DtlColumnCount += 1;
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

			// Clear extended filter for field NAME
			if ($this->ClearExtFilter == 'Last_Payment_Made_NAME')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'NAME');

			// Clear extended filter for field surname
			if ($this->ClearExtFilter == 'Last_Payment_Made_surname')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'surname');

			// Clear extended filter for field standnumber
			if ($this->ClearExtFilter == 'Last_Payment_Made_standnumber')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'standnumber');

			// Clear extended filter for field balance
			if ($this->ClearExtFilter == 'Last_Payment_Made_balance')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'balance');

			// Clear extended filter for field instalments
			if ($this->ClearExtFilter == 'Last_Payment_Made_instalments')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'instalments');

			// Clear extended filter for field paymentnumbermonth
			if ($this->ClearExtFilter == 'Last_Payment_Made_paymentnumbermonth')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'paymentnumbermonth');

			// Clear extended filter for field paymentmonth
			if ($this->ClearExtFilter == 'Last_Payment_Made_paymentmonth')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'paymentmonth');

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->NAME->SearchValue, $this->NAME->SearchOperator, $this->NAME->SearchCondition, $this->NAME->SearchValue2, $this->NAME->SearchOperator2, 'NAME'); // Field NAME
			$this->SetSessionFilterValues($this->surname->SearchValue, $this->surname->SearchOperator, $this->surname->SearchCondition, $this->surname->SearchValue2, $this->surname->SearchOperator2, 'surname'); // Field surname
			$this->SetSessionFilterValues($this->standnumber->SearchValue, $this->standnumber->SearchOperator, $this->standnumber->SearchCondition, $this->standnumber->SearchValue2, $this->standnumber->SearchOperator2, 'standnumber'); // Field standnumber
			$this->SetSessionFilterValues($this->balance->SearchValue, $this->balance->SearchOperator, $this->balance->SearchCondition, $this->balance->SearchValue2, $this->balance->SearchOperator2, 'balance'); // Field balance
			$this->SetSessionFilterValues($this->instalments->SearchValue, $this->instalments->SearchOperator, $this->instalments->SearchCondition, $this->instalments->SearchValue2, $this->instalments->SearchOperator2, 'instalments'); // Field instalments
			$this->SetSessionFilterValues($this->paymentnumbermonth->SearchValue, $this->paymentnumbermonth->SearchOperator, $this->paymentnumbermonth->SearchCondition, $this->paymentnumbermonth->SearchValue2, $this->paymentnumbermonth->SearchOperator2, 'paymentnumbermonth'); // Field paymentnumbermonth
			$this->SetSessionFilterValues($this->paymentmonth->SearchValue, $this->paymentmonth->SearchOperator, $this->paymentmonth->SearchCondition, $this->paymentmonth->SearchValue2, $this->paymentmonth->SearchOperator2, 'paymentmonth'); // Field paymentmonth

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field NAME
			if ($this->GetFilterValues($this->NAME)) {
				$bSetupFilter = TRUE;
			}

			// Field surname
			if ($this->GetFilterValues($this->surname)) {
				$bSetupFilter = TRUE;
			}

			// Field standnumber
			if ($this->GetFilterValues($this->standnumber)) {
				$bSetupFilter = TRUE;
			}

			// Field balance
			if ($this->GetFilterValues($this->balance)) {
				$bSetupFilter = TRUE;
			}

			// Field instalments
			if ($this->GetFilterValues($this->instalments)) {
				$bSetupFilter = TRUE;
			}

			// Field paymentnumbermonth
			if ($this->GetFilterValues($this->paymentnumbermonth)) {
				$bSetupFilter = TRUE;
			}

			// Field paymentmonth
			if ($this->GetFilterValues($this->paymentmonth)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionFilterValues($this->NAME); // Field NAME
			$this->GetSessionFilterValues($this->surname); // Field surname
			$this->GetSessionFilterValues($this->standnumber); // Field standnumber
			$this->GetSessionFilterValues($this->balance); // Field balance
			$this->GetSessionFilterValues($this->instalments); // Field instalments
			$this->GetSessionFilterValues($this->paymentnumbermonth); // Field paymentnumbermonth
			$this->GetSessionFilterValues($this->paymentmonth); // Field paymentmonth
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->NAME, $sFilter, FALSE, TRUE); // Field NAME
		$this->BuildExtendedFilter($this->surname, $sFilter, FALSE, TRUE); // Field surname
		$this->BuildExtendedFilter($this->standnumber, $sFilter, FALSE, TRUE); // Field standnumber
		$this->BuildExtendedFilter($this->balance, $sFilter, FALSE, TRUE); // Field balance
		$this->BuildExtendedFilter($this->instalments, $sFilter, FALSE, TRUE); // Field instalments
		$this->BuildExtendedFilter($this->paymentnumbermonth, $sFilter, FALSE, TRUE); // Field paymentnumbermonth
		$this->BuildExtendedFilter($this->paymentmonth, $sFilter, FALSE, TRUE); // Field paymentmonth

		// Save parms to session
		$this->SetSessionFilterValues($this->NAME->SearchValue, $this->NAME->SearchOperator, $this->NAME->SearchCondition, $this->NAME->SearchValue2, $this->NAME->SearchOperator2, 'NAME'); // Field NAME
		$this->SetSessionFilterValues($this->surname->SearchValue, $this->surname->SearchOperator, $this->surname->SearchCondition, $this->surname->SearchValue2, $this->surname->SearchOperator2, 'surname'); // Field surname
		$this->SetSessionFilterValues($this->standnumber->SearchValue, $this->standnumber->SearchOperator, $this->standnumber->SearchCondition, $this->standnumber->SearchValue2, $this->standnumber->SearchOperator2, 'standnumber'); // Field standnumber
		$this->SetSessionFilterValues($this->balance->SearchValue, $this->balance->SearchOperator, $this->balance->SearchCondition, $this->balance->SearchValue2, $this->balance->SearchOperator2, 'balance'); // Field balance
		$this->SetSessionFilterValues($this->instalments->SearchValue, $this->instalments->SearchOperator, $this->instalments->SearchCondition, $this->instalments->SearchValue2, $this->instalments->SearchOperator2, 'instalments'); // Field instalments
		$this->SetSessionFilterValues($this->paymentnumbermonth->SearchValue, $this->paymentnumbermonth->SearchOperator, $this->paymentnumbermonth->SearchCondition, $this->paymentnumbermonth->SearchValue2, $this->paymentnumbermonth->SearchOperator2, 'paymentnumbermonth'); // Field paymentnumbermonth
		$this->SetSessionFilterValues($this->paymentmonth->SearchValue, $this->paymentmonth->SearchOperator, $this->paymentmonth->SearchCondition, $this->paymentmonth->SearchValue2, $this->paymentmonth->SearchOperator2, 'paymentmonth'); // Field paymentmonth

		// Setup filter
		if ($bSetupFilter) {

			// Field NAME
			$sWrk = "";
			$this->BuildExtendedFilter($this->NAME, $sWrk);
			ewr_LoadSelectionFromFilter($this->NAME, $sWrk, $this->NAME->SelectionList);
			$_SESSION['sel_Last_Payment_Made_NAME'] = ($this->NAME->SelectionList == "") ? EWR_INIT_VALUE : $this->NAME->SelectionList;

			// Field surname
			$sWrk = "";
			$this->BuildExtendedFilter($this->surname, $sWrk);
			ewr_LoadSelectionFromFilter($this->surname, $sWrk, $this->surname->SelectionList);
			$_SESSION['sel_Last_Payment_Made_surname'] = ($this->surname->SelectionList == "") ? EWR_INIT_VALUE : $this->surname->SelectionList;

			// Field standnumber
			$sWrk = "";
			$this->BuildExtendedFilter($this->standnumber, $sWrk);
			ewr_LoadSelectionFromFilter($this->standnumber, $sWrk, $this->standnumber->SelectionList);
			$_SESSION['sel_Last_Payment_Made_standnumber'] = ($this->standnumber->SelectionList == "") ? EWR_INIT_VALUE : $this->standnumber->SelectionList;

			// Field balance
			$sWrk = "";
			$this->BuildExtendedFilter($this->balance, $sWrk);
			ewr_LoadSelectionFromFilter($this->balance, $sWrk, $this->balance->SelectionList);
			$_SESSION['sel_Last_Payment_Made_balance'] = ($this->balance->SelectionList == "") ? EWR_INIT_VALUE : $this->balance->SelectionList;

			// Field instalments
			$sWrk = "";
			$this->BuildExtendedFilter($this->instalments, $sWrk);
			ewr_LoadSelectionFromFilter($this->instalments, $sWrk, $this->instalments->SelectionList);
			$_SESSION['sel_Last_Payment_Made_instalments'] = ($this->instalments->SelectionList == "") ? EWR_INIT_VALUE : $this->instalments->SelectionList;

			// Field paymentnumbermonth
			$sWrk = "";
			$this->BuildExtendedFilter($this->paymentnumbermonth, $sWrk);
			ewr_LoadSelectionFromFilter($this->paymentnumbermonth, $sWrk, $this->paymentnumbermonth->SelectionList);
			$_SESSION['sel_Last_Payment_Made_paymentnumbermonth'] = ($this->paymentnumbermonth->SelectionList == "") ? EWR_INIT_VALUE : $this->paymentnumbermonth->SelectionList;

			// Field paymentmonth
			$sWrk = "";
			$this->BuildExtendedFilter($this->paymentmonth, $sWrk);
			ewr_LoadSelectionFromFilter($this->paymentmonth, $sWrk, $this->paymentmonth->SelectionList);
			$_SESSION['sel_Last_Payment_Made_paymentmonth'] = ($this->paymentmonth->SelectionList == "") ? EWR_INIT_VALUE : $this->paymentmonth->SelectionList;
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
		$this->GetSessionValue($fld->DropDownValue, 'sv_Last_Payment_Made_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Last_Payment_Made_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_Last_Payment_Made_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Last_Payment_Made_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_Last_Payment_Made_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_Last_Payment_Made_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_Last_Payment_Made_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_Last_Payment_Made_' . $parm] = $sv;
		$_SESSION['so_Last_Payment_Made_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_Last_Payment_Made_' . $parm] = $sv1;
		$_SESSION['so_Last_Payment_Made_' . $parm] = $so1;
		$_SESSION['sc_Last_Payment_Made_' . $parm] = $sc;
		$_SESSION['sv2_Last_Payment_Made_' . $parm] = $sv2;
		$_SESSION['so2_Last_Payment_Made_' . $parm] = $so2;
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
		if (!ewr_CheckNumber($this->balance->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->balance->FldErrMsg();
		}
		if (!ewr_CheckNumber($this->instalments->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->instalments->FldErrMsg();
		}
		if (!ewr_CheckInteger($this->paymentnumbermonth->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->paymentnumbermonth->FldErrMsg();
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
		$_SESSION["sel_Last_Payment_Made_$parm"] = "";
		$_SESSION["rf_Last_Payment_Made_$parm"] = "";
		$_SESSION["rt_Last_Payment_Made_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_Last_Payment_Made_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_Last_Payment_Made_$parm"];
		$fld->RangeTo = @$_SESSION["rt_Last_Payment_Made_$parm"];
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

		// Field NAME
		$this->SetDefaultExtFilter($this->NAME, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->NAME);
		$sWrk = "";
		$this->BuildExtendedFilter($this->NAME, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->NAME, $sWrk, $this->NAME->DefaultSelectionList);
		if (!$this->SearchCommand) $this->NAME->SelectionList = $this->NAME->DefaultSelectionList;

		// Field surname
		$this->SetDefaultExtFilter($this->surname, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->surname);
		$sWrk = "";
		$this->BuildExtendedFilter($this->surname, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->surname, $sWrk, $this->surname->DefaultSelectionList);
		if (!$this->SearchCommand) $this->surname->SelectionList = $this->surname->DefaultSelectionList;

		// Field standnumber
		$this->SetDefaultExtFilter($this->standnumber, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->standnumber);
		$sWrk = "";
		$this->BuildExtendedFilter($this->standnumber, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->standnumber, $sWrk, $this->standnumber->DefaultSelectionList);
		if (!$this->SearchCommand) $this->standnumber->SelectionList = $this->standnumber->DefaultSelectionList;

		// Field balance
		$this->SetDefaultExtFilter($this->balance, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->balance);
		$sWrk = "";
		$this->BuildExtendedFilter($this->balance, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->balance, $sWrk, $this->balance->DefaultSelectionList);
		if (!$this->SearchCommand) $this->balance->SelectionList = $this->balance->DefaultSelectionList;

		// Field instalments
		$this->SetDefaultExtFilter($this->instalments, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->instalments);
		$sWrk = "";
		$this->BuildExtendedFilter($this->instalments, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->instalments, $sWrk, $this->instalments->DefaultSelectionList);
		if (!$this->SearchCommand) $this->instalments->SelectionList = $this->instalments->DefaultSelectionList;

		// Field paymentnumbermonth
		$this->SetDefaultExtFilter($this->paymentnumbermonth, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->paymentnumbermonth);
		$sWrk = "";
		$this->BuildExtendedFilter($this->paymentnumbermonth, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->paymentnumbermonth, $sWrk, $this->paymentnumbermonth->DefaultSelectionList);
		if (!$this->SearchCommand) $this->paymentnumbermonth->SelectionList = $this->paymentnumbermonth->DefaultSelectionList;

		// Field paymentmonth
		$this->SetDefaultExtFilter($this->paymentmonth, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->paymentmonth);
		$sWrk = "";
		$this->BuildExtendedFilter($this->paymentmonth, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->paymentmonth, $sWrk, $this->paymentmonth->DefaultSelectionList);
		if (!$this->SearchCommand) $this->paymentmonth->SelectionList = $this->paymentmonth->DefaultSelectionList;
		/**
		* Set up default values for popup filters
		*/

		// Field NAME
		// $this->NAME->DefaultSelectionList = array("val1", "val2");
		// Field surname
		// $this->surname->DefaultSelectionList = array("val1", "val2");
		// Field standnumber
		// $this->standnumber->DefaultSelectionList = array("val1", "val2");
		// Field balance
		// $this->balance->DefaultSelectionList = array("val1", "val2");
		// Field instalments
		// $this->instalments->DefaultSelectionList = array("val1", "val2");
		// Field paymentnumbermonth
		// $this->paymentnumbermonth->DefaultSelectionList = array("val1", "val2");
		// Field paymentmonth
		// $this->paymentmonth->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check NAME text filter
		if ($this->TextFilterApplied($this->NAME))
			return TRUE;

		// Check NAME popup filter
		if (!ewr_MatchedArray($this->NAME->DefaultSelectionList, $this->NAME->SelectionList))
			return TRUE;

		// Check surname text filter
		if ($this->TextFilterApplied($this->surname))
			return TRUE;

		// Check surname popup filter
		if (!ewr_MatchedArray($this->surname->DefaultSelectionList, $this->surname->SelectionList))
			return TRUE;

		// Check standnumber text filter
		if ($this->TextFilterApplied($this->standnumber))
			return TRUE;

		// Check standnumber popup filter
		if (!ewr_MatchedArray($this->standnumber->DefaultSelectionList, $this->standnumber->SelectionList))
			return TRUE;

		// Check balance text filter
		if ($this->TextFilterApplied($this->balance))
			return TRUE;

		// Check balance popup filter
		if (!ewr_MatchedArray($this->balance->DefaultSelectionList, $this->balance->SelectionList))
			return TRUE;

		// Check instalments text filter
		if ($this->TextFilterApplied($this->instalments))
			return TRUE;

		// Check instalments popup filter
		if (!ewr_MatchedArray($this->instalments->DefaultSelectionList, $this->instalments->SelectionList))
			return TRUE;

		// Check paymentnumbermonth text filter
		if ($this->TextFilterApplied($this->paymentnumbermonth))
			return TRUE;

		// Check paymentnumbermonth popup filter
		if (!ewr_MatchedArray($this->paymentnumbermonth->DefaultSelectionList, $this->paymentnumbermonth->SelectionList))
			return TRUE;

		// Check paymentmonth text filter
		if ($this->TextFilterApplied($this->paymentmonth))
			return TRUE;

		// Check paymentmonth popup filter
		if (!ewr_MatchedArray($this->paymentmonth->DefaultSelectionList, $this->paymentmonth->SelectionList))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList($showDate = FALSE) {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field NAME
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->NAME, $sExtWrk);
		if (is_array($this->NAME->SelectionList))
			$sWrk = ewr_JoinArray($this->NAME->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->NAME->FldCaption() . "</span>" . $sFilter . "</div>";

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

		// Field standnumber
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->standnumber, $sExtWrk);
		if (is_array($this->standnumber->SelectionList))
			$sWrk = ewr_JoinArray($this->standnumber->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->standnumber->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field balance
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->balance, $sExtWrk);
		if (is_array($this->balance->SelectionList))
			$sWrk = ewr_JoinArray($this->balance->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->balance->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field instalments
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->instalments, $sExtWrk);
		if (is_array($this->instalments->SelectionList))
			$sWrk = ewr_JoinArray($this->instalments->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->instalments->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field paymentnumbermonth
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->paymentnumbermonth, $sExtWrk);
		if (is_array($this->paymentnumbermonth->SelectionList))
			$sWrk = ewr_JoinArray($this->paymentnumbermonth->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->paymentnumbermonth->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field paymentmonth
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->paymentmonth, $sExtWrk);
		if (is_array($this->paymentmonth->SelectionList))
			$sWrk = ewr_JoinArray($this->paymentmonth->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->paymentmonth->FldCaption() . "</span>" . $sFilter . "</div>";
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

		// Field NAME
		$sWrk = "";
		if ($this->NAME->SearchValue <> "" || $this->NAME->SearchValue2 <> "") {
			$sWrk = "\"sv_NAME\":\"" . ewr_JsEncode2($this->NAME->SearchValue) . "\"," .
				"\"so_NAME\":\"" . ewr_JsEncode2($this->NAME->SearchOperator) . "\"," .
				"\"sc_NAME\":\"" . ewr_JsEncode2($this->NAME->SearchCondition) . "\"," .
				"\"sv2_NAME\":\"" . ewr_JsEncode2($this->NAME->SearchValue2) . "\"," .
				"\"so2_NAME\":\"" . ewr_JsEncode2($this->NAME->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->NAME->SelectionList <> EWR_INIT_VALUE) ? $this->NAME->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_NAME\":\"" . ewr_JsEncode2($sWrk) . "\"";
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

		// Field standnumber
		$sWrk = "";
		if ($this->standnumber->SearchValue <> "" || $this->standnumber->SearchValue2 <> "") {
			$sWrk = "\"sv_standnumber\":\"" . ewr_JsEncode2($this->standnumber->SearchValue) . "\"," .
				"\"so_standnumber\":\"" . ewr_JsEncode2($this->standnumber->SearchOperator) . "\"," .
				"\"sc_standnumber\":\"" . ewr_JsEncode2($this->standnumber->SearchCondition) . "\"," .
				"\"sv2_standnumber\":\"" . ewr_JsEncode2($this->standnumber->SearchValue2) . "\"," .
				"\"so2_standnumber\":\"" . ewr_JsEncode2($this->standnumber->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->standnumber->SelectionList <> EWR_INIT_VALUE) ? $this->standnumber->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_standnumber\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field balance
		$sWrk = "";
		if ($this->balance->SearchValue <> "" || $this->balance->SearchValue2 <> "") {
			$sWrk = "\"sv_balance\":\"" . ewr_JsEncode2($this->balance->SearchValue) . "\"," .
				"\"so_balance\":\"" . ewr_JsEncode2($this->balance->SearchOperator) . "\"," .
				"\"sc_balance\":\"" . ewr_JsEncode2($this->balance->SearchCondition) . "\"," .
				"\"sv2_balance\":\"" . ewr_JsEncode2($this->balance->SearchValue2) . "\"," .
				"\"so2_balance\":\"" . ewr_JsEncode2($this->balance->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->balance->SelectionList <> EWR_INIT_VALUE) ? $this->balance->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_balance\":\"" . ewr_JsEncode2($sWrk) . "\"";
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

		// Field paymentnumbermonth
		$sWrk = "";
		if ($this->paymentnumbermonth->SearchValue <> "" || $this->paymentnumbermonth->SearchValue2 <> "") {
			$sWrk = "\"sv_paymentnumbermonth\":\"" . ewr_JsEncode2($this->paymentnumbermonth->SearchValue) . "\"," .
				"\"so_paymentnumbermonth\":\"" . ewr_JsEncode2($this->paymentnumbermonth->SearchOperator) . "\"," .
				"\"sc_paymentnumbermonth\":\"" . ewr_JsEncode2($this->paymentnumbermonth->SearchCondition) . "\"," .
				"\"sv2_paymentnumbermonth\":\"" . ewr_JsEncode2($this->paymentnumbermonth->SearchValue2) . "\"," .
				"\"so2_paymentnumbermonth\":\"" . ewr_JsEncode2($this->paymentnumbermonth->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->paymentnumbermonth->SelectionList <> EWR_INIT_VALUE) ? $this->paymentnumbermonth->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_paymentnumbermonth\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field paymentmonth
		$sWrk = "";
		if ($this->paymentmonth->SearchValue <> "" || $this->paymentmonth->SearchValue2 <> "") {
			$sWrk = "\"sv_paymentmonth\":\"" . ewr_JsEncode2($this->paymentmonth->SearchValue) . "\"," .
				"\"so_paymentmonth\":\"" . ewr_JsEncode2($this->paymentmonth->SearchOperator) . "\"," .
				"\"sc_paymentmonth\":\"" . ewr_JsEncode2($this->paymentmonth->SearchCondition) . "\"," .
				"\"sv2_paymentmonth\":\"" . ewr_JsEncode2($this->paymentmonth->SearchValue2) . "\"," .
				"\"so2_paymentmonth\":\"" . ewr_JsEncode2($this->paymentmonth->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->paymentmonth->SelectionList <> EWR_INIT_VALUE) ? $this->paymentmonth->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_paymentmonth\":\"" . ewr_JsEncode2($sWrk) . "\"";
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

		// Field NAME
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_NAME", $filter) || array_key_exists("so_NAME", $filter) ||
			array_key_exists("sc_NAME", $filter) ||
			array_key_exists("sv2_NAME", $filter) || array_key_exists("so2_NAME", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_NAME"], @$filter["so_NAME"], @$filter["sc_NAME"], @$filter["sv2_NAME"], @$filter["so2_NAME"], "NAME");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_NAME", $filter)) {
			$sWrk = $filter["sel_NAME"];
			$sWrk = explode("||", $sWrk);
			$this->NAME->SelectionList = $sWrk;
			$_SESSION["sel_Last_Payment_Made_NAME"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "NAME"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "NAME");
			$this->NAME->SelectionList = "";
			$_SESSION["sel_Last_Payment_Made_NAME"] = "";
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
			$_SESSION["sel_Last_Payment_Made_surname"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "surname"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "surname");
			$this->surname->SelectionList = "";
			$_SESSION["sel_Last_Payment_Made_surname"] = "";
		}

		// Field standnumber
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_standnumber", $filter) || array_key_exists("so_standnumber", $filter) ||
			array_key_exists("sc_standnumber", $filter) ||
			array_key_exists("sv2_standnumber", $filter) || array_key_exists("so2_standnumber", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_standnumber"], @$filter["so_standnumber"], @$filter["sc_standnumber"], @$filter["sv2_standnumber"], @$filter["so2_standnumber"], "standnumber");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_standnumber", $filter)) {
			$sWrk = $filter["sel_standnumber"];
			$sWrk = explode("||", $sWrk);
			$this->standnumber->SelectionList = $sWrk;
			$_SESSION["sel_Last_Payment_Made_standnumber"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "standnumber"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "standnumber");
			$this->standnumber->SelectionList = "";
			$_SESSION["sel_Last_Payment_Made_standnumber"] = "";
		}

		// Field balance
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_balance", $filter) || array_key_exists("so_balance", $filter) ||
			array_key_exists("sc_balance", $filter) ||
			array_key_exists("sv2_balance", $filter) || array_key_exists("so2_balance", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_balance"], @$filter["so_balance"], @$filter["sc_balance"], @$filter["sv2_balance"], @$filter["so2_balance"], "balance");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_balance", $filter)) {
			$sWrk = $filter["sel_balance"];
			$sWrk = explode("||", $sWrk);
			$this->balance->SelectionList = $sWrk;
			$_SESSION["sel_Last_Payment_Made_balance"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "balance"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "balance");
			$this->balance->SelectionList = "";
			$_SESSION["sel_Last_Payment_Made_balance"] = "";
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
			$_SESSION["sel_Last_Payment_Made_instalments"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "instalments"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "instalments");
			$this->instalments->SelectionList = "";
			$_SESSION["sel_Last_Payment_Made_instalments"] = "";
		}

		// Field paymentnumbermonth
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_paymentnumbermonth", $filter) || array_key_exists("so_paymentnumbermonth", $filter) ||
			array_key_exists("sc_paymentnumbermonth", $filter) ||
			array_key_exists("sv2_paymentnumbermonth", $filter) || array_key_exists("so2_paymentnumbermonth", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_paymentnumbermonth"], @$filter["so_paymentnumbermonth"], @$filter["sc_paymentnumbermonth"], @$filter["sv2_paymentnumbermonth"], @$filter["so2_paymentnumbermonth"], "paymentnumbermonth");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_paymentnumbermonth", $filter)) {
			$sWrk = $filter["sel_paymentnumbermonth"];
			$sWrk = explode("||", $sWrk);
			$this->paymentnumbermonth->SelectionList = $sWrk;
			$_SESSION["sel_Last_Payment_Made_paymentnumbermonth"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "paymentnumbermonth"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "paymentnumbermonth");
			$this->paymentnumbermonth->SelectionList = "";
			$_SESSION["sel_Last_Payment_Made_paymentnumbermonth"] = "";
		}

		// Field paymentmonth
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_paymentmonth", $filter) || array_key_exists("so_paymentmonth", $filter) ||
			array_key_exists("sc_paymentmonth", $filter) ||
			array_key_exists("sv2_paymentmonth", $filter) || array_key_exists("so2_paymentmonth", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_paymentmonth"], @$filter["so_paymentmonth"], @$filter["sc_paymentmonth"], @$filter["sv2_paymentmonth"], @$filter["so2_paymentmonth"], "paymentmonth");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_paymentmonth", $filter)) {
			$sWrk = $filter["sel_paymentmonth"];
			$sWrk = explode("||", $sWrk);
			$this->paymentmonth->SelectionList = $sWrk;
			$_SESSION["sel_Last_Payment_Made_paymentmonth"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "paymentmonth"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "paymentmonth");
			$this->paymentmonth->SelectionList = "";
			$_SESSION["sel_Last_Payment_Made_paymentmonth"] = "";
		}
		return TRUE;
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		if (!$this->ExtendedFilterExist($this->NAME)) {
			if (is_array($this->NAME->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->NAME, "`NAME`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->NAME, $sFilter, "popup");
				$this->NAME->CurrentFilter = $sFilter;
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
		if (!$this->ExtendedFilterExist($this->standnumber)) {
			if (is_array($this->standnumber->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->standnumber, "`standnumber`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->standnumber, $sFilter, "popup");
				$this->standnumber->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->balance)) {
			if (is_array($this->balance->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->balance, "`balance`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->balance, $sFilter, "popup");
				$this->balance->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->instalments)) {
			if (is_array($this->instalments->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->instalments, "`instalments`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->instalments, $sFilter, "popup");
				$this->instalments->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->paymentnumbermonth)) {
			if (is_array($this->paymentnumbermonth->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->paymentnumbermonth, "`paymentnumbermonth`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->paymentnumbermonth, $sFilter, "popup");
				$this->paymentnumbermonth->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->paymentmonth)) {
			if (is_array($this->paymentmonth->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->paymentmonth, "`paymentmonth`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->paymentmonth, $sFilter, "popup");
				$this->paymentmonth->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
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
			$this->NAME->setSort("");
			$this->surname->setSort("");
			$this->standnumber->setSort("");
			$this->balance->setSort("");
			$this->instalments->setSort("");
			$this->paymentnumbermonth->setSort("");
			$this->paymentmonth->setSort("");

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
if (!isset($Last_Payment_Made_summary)) $Last_Payment_Made_summary = new crLast_Payment_Made_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$Last_Payment_Made_summary;

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
var Last_Payment_Made_summary = new ewr_Page("Last_Payment_Made_summary");

// Page properties
Last_Payment_Made_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = Last_Payment_Made_summary.PageID;

// Extend page with Chart_Rendering function
Last_Payment_Made_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
Last_Payment_Made_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fLast_Payment_Madesummary = new ewr_Form("fLast_Payment_Madesummary");

// Validate method
fLast_Payment_Madesummary.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	var elm = fobj.sv_balance;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->balance->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_instalments;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->instalments->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_paymentnumbermonth;
	if (elm && !ewr_CheckInteger(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->paymentnumbermonth->FldErrMsg()) ?>"))
			return false;
	}

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fLast_Payment_Madesummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fLast_Payment_Madesummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fLast_Payment_Madesummary.ValidateRequired = false; // No JavaScript validation
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
<form name="fLast_Payment_Madesummary" id="fLast_Payment_Madesummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fLast_Payment_Madesummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_NAME" class="ewCell form-group">
	<label for="sv_NAME" class="ewSearchCaption ewLabel"><?php echo $Page->NAME->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_NAME" id="so_NAME" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->NAME->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->NAME->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->NAME->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->NAME->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->NAME->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->NAME->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->NAME->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->NAME->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->NAME->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->NAME->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->NAME->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->NAME->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->NAME->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->NAME->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_NAME" id="sv_NAME" name="sv_NAME" size="30" maxlength="255" placeholder="<?php echo $Page->NAME->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->NAME->SearchValue) ?>"<?php echo $Page->NAME->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_NAME" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_NAME" style="display: none">
<?php ewr_PrependClass($Page->NAME->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_NAME" id="sv2_NAME" name="sv2_NAME" size="30" maxlength="255" placeholder="<?php echo $Page->NAME->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->NAME->SearchValue2) ?>"<?php echo $Page->NAME->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_surname" class="ewCell form-group">
	<label for="sv_surname" class="ewSearchCaption ewLabel"><?php echo $Page->surname->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_surname" id="so_surname" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->surname->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->surname->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->surname->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->surname->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->surname->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->surname->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->surname->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->surname->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->surname->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->surname->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->surname->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->surname->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->surname->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->surname->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_surname" id="sv_surname" name="sv_surname" size="30" maxlength="255" placeholder="<?php echo $Page->surname->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->surname->SearchValue) ?>"<?php echo $Page->surname->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_surname" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_surname" style="display: none">
<?php ewr_PrependClass($Page->surname->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_surname" id="sv2_surname" name="sv2_surname" size="30" maxlength="255" placeholder="<?php echo $Page->surname->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->surname->SearchValue2) ?>"<?php echo $Page->surname->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_3" class="ewRow">
<div id="c_standnumber" class="ewCell form-group">
	<label for="sv_standnumber" class="ewSearchCaption ewLabel"><?php echo $Page->standnumber->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_standnumber" id="so_standnumber" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->standnumber->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->standnumber->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->standnumber->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->standnumber->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->standnumber->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->standnumber->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->standnumber->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->standnumber->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->standnumber->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->standnumber->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->standnumber->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->standnumber->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->standnumber->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->standnumber->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_standnumber" id="sv_standnumber" name="sv_standnumber" size="30" maxlength="111" placeholder="<?php echo $Page->standnumber->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->standnumber->SearchValue) ?>"<?php echo $Page->standnumber->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_standnumber" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_standnumber" style="display: none">
<?php ewr_PrependClass($Page->standnumber->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_standnumber" id="sv2_standnumber" name="sv2_standnumber" size="30" maxlength="111" placeholder="<?php echo $Page->standnumber->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->standnumber->SearchValue2) ?>"<?php echo $Page->standnumber->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_4" class="ewRow">
<div id="c_balance" class="ewCell form-group">
	<label for="sv_balance" class="ewSearchCaption ewLabel"><?php echo $Page->balance->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_balance" id="so_balance" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->balance->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->balance->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->balance->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->balance->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->balance->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->balance->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->balance->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->balance->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->balance->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->balance->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_balance" id="sv_balance" name="sv_balance" size="30" placeholder="<?php echo $Page->balance->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->balance->SearchValue) ?>"<?php echo $Page->balance->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_balance" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_balance" style="display: none">
<?php ewr_PrependClass($Page->balance->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_balance" id="sv2_balance" name="sv2_balance" size="30" placeholder="<?php echo $Page->balance->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->balance->SearchValue2) ?>"<?php echo $Page->balance->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_5" class="ewRow">
<div id="c_instalments" class="ewCell form-group">
	<label for="sv_instalments" class="ewSearchCaption ewLabel"><?php echo $Page->instalments->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_instalments" id="so_instalments" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->instalments->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->instalments->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->instalments->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->instalments->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->instalments->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->instalments->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->instalments->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->instalments->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->instalments->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->instalments->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_instalments" id="sv_instalments" name="sv_instalments" size="30" placeholder="<?php echo $Page->instalments->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->instalments->SearchValue) ?>"<?php echo $Page->instalments->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_instalments" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_instalments" style="display: none">
<?php ewr_PrependClass($Page->instalments->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_instalments" id="sv2_instalments" name="sv2_instalments" size="30" placeholder="<?php echo $Page->instalments->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->instalments->SearchValue2) ?>"<?php echo $Page->instalments->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_6" class="ewRow">
<div id="c_paymentnumbermonth" class="ewCell form-group">
	<label for="sv_paymentnumbermonth" class="ewSearchCaption ewLabel"><?php echo $Page->paymentnumbermonth->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_paymentnumbermonth" id="so_paymentnumbermonth" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->paymentnumbermonth->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->paymentnumbermonth->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->paymentnumbermonth->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->paymentnumbermonth->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->paymentnumbermonth->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->paymentnumbermonth->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->paymentnumbermonth->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->paymentnumbermonth->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->paymentnumbermonth->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->paymentnumbermonth->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_paymentnumbermonth" id="sv_paymentnumbermonth" name="sv_paymentnumbermonth" size="30" placeholder="<?php echo $Page->paymentnumbermonth->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->paymentnumbermonth->SearchValue) ?>"<?php echo $Page->paymentnumbermonth->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_paymentnumbermonth" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_paymentnumbermonth" style="display: none">
<?php ewr_PrependClass($Page->paymentnumbermonth->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_paymentnumbermonth" id="sv2_paymentnumbermonth" name="sv2_paymentnumbermonth" size="30" placeholder="<?php echo $Page->paymentnumbermonth->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->paymentnumbermonth->SearchValue2) ?>"<?php echo $Page->paymentnumbermonth->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_7" class="ewRow">
<div id="c_paymentmonth" class="ewCell form-group">
	<label for="sv_paymentmonth" class="ewSearchCaption ewLabel"><?php echo $Page->paymentmonth->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_paymentmonth" id="so_paymentmonth" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->paymentmonth->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->paymentmonth->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->paymentmonth->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->paymentmonth->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->paymentmonth->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->paymentmonth->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->paymentmonth->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->paymentmonth->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->paymentmonth->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->paymentmonth->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->paymentmonth->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->paymentmonth->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->paymentmonth->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->paymentmonth->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_paymentmonth" id="sv_paymentmonth" name="sv_paymentmonth" size="30" maxlength="111" placeholder="<?php echo $Page->paymentmonth->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->paymentmonth->SearchValue) ?>"<?php echo $Page->paymentmonth->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_paymentmonth" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_paymentmonth" style="display: none">
<?php ewr_PrependClass($Page->paymentmonth->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Last_Payment_Made" data-field="x_paymentmonth" id="sv2_paymentmonth" name="sv2_paymentmonth" size="30" maxlength="111" placeholder="<?php echo $Page->paymentmonth->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->paymentmonth->SearchValue2) ?>"<?php echo $Page->paymentmonth->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fLast_Payment_Madesummary.Init();
fLast_Payment_Madesummary.FilterList = <?php echo $Page->GetFilterList() ?>;
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
<?php include "Last_Payment_Madesmrypager.php" ?>
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
<?php if ($Page->NAME->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="NAME"><div class="Last_Payment_Made_NAME"><span class="ewTableHeaderCaption"><?php echo $Page->NAME->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="NAME">
<?php if ($Page->SortUrl($Page->NAME) == "") { ?>
		<div class="ewTableHeaderBtn Last_Payment_Made_NAME">
			<span class="ewTableHeaderCaption"><?php echo $Page->NAME->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_NAME', false, '<?php echo $Page->NAME->RangeFrom; ?>', '<?php echo $Page->NAME->RangeTo; ?>');" id="x_NAME<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Last_Payment_Made_NAME" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->NAME) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->NAME->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->NAME->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->NAME->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_NAME', false, '<?php echo $Page->NAME->RangeFrom; ?>', '<?php echo $Page->NAME->RangeTo; ?>');" id="x_NAME<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="surname"><div class="Last_Payment_Made_surname"><span class="ewTableHeaderCaption"><?php echo $Page->surname->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="surname">
<?php if ($Page->SortUrl($Page->surname) == "") { ?>
		<div class="ewTableHeaderBtn Last_Payment_Made_surname">
			<span class="ewTableHeaderCaption"><?php echo $Page->surname->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_surname', false, '<?php echo $Page->surname->RangeFrom; ?>', '<?php echo $Page->surname->RangeTo; ?>');" id="x_surname<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Last_Payment_Made_surname" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->surname) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->surname->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->surname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->surname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_surname', false, '<?php echo $Page->surname->RangeFrom; ?>', '<?php echo $Page->surname->RangeTo; ?>');" id="x_surname<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->standnumber->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="standnumber"><div class="Last_Payment_Made_standnumber"><span class="ewTableHeaderCaption"><?php echo $Page->standnumber->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="standnumber">
<?php if ($Page->SortUrl($Page->standnumber) == "") { ?>
		<div class="ewTableHeaderBtn Last_Payment_Made_standnumber">
			<span class="ewTableHeaderCaption"><?php echo $Page->standnumber->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_standnumber', false, '<?php echo $Page->standnumber->RangeFrom; ?>', '<?php echo $Page->standnumber->RangeTo; ?>');" id="x_standnumber<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Last_Payment_Made_standnumber" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->standnumber) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->standnumber->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->standnumber->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->standnumber->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_standnumber', false, '<?php echo $Page->standnumber->RangeFrom; ?>', '<?php echo $Page->standnumber->RangeTo; ?>');" id="x_standnumber<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->balance->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="balance"><div class="Last_Payment_Made_balance"><span class="ewTableHeaderCaption"><?php echo $Page->balance->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="balance">
<?php if ($Page->SortUrl($Page->balance) == "") { ?>
		<div class="ewTableHeaderBtn Last_Payment_Made_balance">
			<span class="ewTableHeaderCaption"><?php echo $Page->balance->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_balance', false, '<?php echo $Page->balance->RangeFrom; ?>', '<?php echo $Page->balance->RangeTo; ?>');" id="x_balance<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Last_Payment_Made_balance" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->balance) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->balance->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->balance->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->balance->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_balance', false, '<?php echo $Page->balance->RangeFrom; ?>', '<?php echo $Page->balance->RangeTo; ?>');" id="x_balance<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="instalments"><div class="Last_Payment_Made_instalments"><span class="ewTableHeaderCaption"><?php echo $Page->instalments->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="instalments">
<?php if ($Page->SortUrl($Page->instalments) == "") { ?>
		<div class="ewTableHeaderBtn Last_Payment_Made_instalments">
			<span class="ewTableHeaderCaption"><?php echo $Page->instalments->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_instalments', false, '<?php echo $Page->instalments->RangeFrom; ?>', '<?php echo $Page->instalments->RangeTo; ?>');" id="x_instalments<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Last_Payment_Made_instalments" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->instalments) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->instalments->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->instalments->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->instalments->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_instalments', false, '<?php echo $Page->instalments->RangeFrom; ?>', '<?php echo $Page->instalments->RangeTo; ?>');" id="x_instalments<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->paymentnumbermonth->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="paymentnumbermonth"><div class="Last_Payment_Made_paymentnumbermonth"><span class="ewTableHeaderCaption"><?php echo $Page->paymentnumbermonth->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="paymentnumbermonth">
<?php if ($Page->SortUrl($Page->paymentnumbermonth) == "") { ?>
		<div class="ewTableHeaderBtn Last_Payment_Made_paymentnumbermonth">
			<span class="ewTableHeaderCaption"><?php echo $Page->paymentnumbermonth->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_paymentnumbermonth', false, '<?php echo $Page->paymentnumbermonth->RangeFrom; ?>', '<?php echo $Page->paymentnumbermonth->RangeTo; ?>');" id="x_paymentnumbermonth<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Last_Payment_Made_paymentnumbermonth" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->paymentnumbermonth) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->paymentnumbermonth->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->paymentnumbermonth->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->paymentnumbermonth->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_paymentnumbermonth', false, '<?php echo $Page->paymentnumbermonth->RangeFrom; ?>', '<?php echo $Page->paymentnumbermonth->RangeTo; ?>');" id="x_paymentnumbermonth<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->paymentmonth->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="paymentmonth"><div class="Last_Payment_Made_paymentmonth"><span class="ewTableHeaderCaption"><?php echo $Page->paymentmonth->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="paymentmonth">
<?php if ($Page->SortUrl($Page->paymentmonth) == "") { ?>
		<div class="ewTableHeaderBtn Last_Payment_Made_paymentmonth">
			<span class="ewTableHeaderCaption"><?php echo $Page->paymentmonth->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_paymentmonth', false, '<?php echo $Page->paymentmonth->RangeFrom; ?>', '<?php echo $Page->paymentmonth->RangeTo; ?>');" id="x_paymentmonth<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Last_Payment_Made_paymentmonth" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->paymentmonth) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->paymentmonth->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->paymentmonth->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->paymentmonth->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Last_Payment_Made_paymentmonth', false, '<?php echo $Page->paymentmonth->RangeFrom; ?>', '<?php echo $Page->paymentmonth->RangeTo; ?>');" id="x_paymentmonth<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
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
<?php if ($Page->NAME->Visible) { ?>
		<td data-field="NAME"<?php echo $Page->NAME->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Last_Payment_Made_NAME"<?php echo $Page->NAME->ViewAttributes() ?>><?php echo $Page->NAME->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
		<td data-field="surname"<?php echo $Page->surname->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Last_Payment_Made_surname"<?php echo $Page->surname->ViewAttributes() ?>><?php echo $Page->surname->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->standnumber->Visible) { ?>
		<td data-field="standnumber"<?php echo $Page->standnumber->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Last_Payment_Made_standnumber"<?php echo $Page->standnumber->ViewAttributes() ?>><?php echo $Page->standnumber->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->balance->Visible) { ?>
		<td data-field="balance"<?php echo $Page->balance->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Last_Payment_Made_balance"<?php echo $Page->balance->ViewAttributes() ?>><?php echo $Page->balance->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Last_Payment_Made_instalments"<?php echo $Page->instalments->ViewAttributes() ?>><?php echo $Page->instalments->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->paymentnumbermonth->Visible) { ?>
		<td data-field="paymentnumbermonth"<?php echo $Page->paymentnumbermonth->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Last_Payment_Made_paymentnumbermonth"<?php echo $Page->paymentnumbermonth->ViewAttributes() ?>><?php echo $Page->paymentnumbermonth->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->paymentmonth->Visible) { ?>
		<td data-field="paymentmonth"<?php echo $Page->paymentmonth->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Last_Payment_Made_paymentmonth"<?php echo $Page->paymentmonth->ViewAttributes() ?>><?php echo $Page->paymentmonth->ListViewValue() ?></span></td>
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
	$Page->balance->Count = $Page->GrandCnt[4];
	$Page->balance->SumValue = $Page->GrandSmry[4]; // Load SUM
	$Page->balance->Count = $Page->GrandCnt[4];
	$Page->balance->AvgValue = ($Page->balance->Count > 0) ? $Page->GrandSmry[4]/$Page->balance->Count : 0; // Load AVG
	$Page->balance->Count = $Page->GrandCnt[4];
	$Page->balance->MinValue = $Page->GrandMn[4]; // Load MIN
	$Page->balance->Count = $Page->GrandCnt[4];
	$Page->balance->MaxValue = $Page->GrandMx[4]; // Load MAX
	$Page->balance->Count = $Page->GrandCnt[4];
	$Page->balance->CntValue = $Page->GrandCnt[4]; // Load CNT
	$Page->ResetAttrs();
	$Page->RowType = EWR_ROWTYPE_TOTAL;
	$Page->RowTotalType = EWR_ROWTOTAL_GRAND;
	$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes() ?>><td colspan="<?php echo ($Page->GrpColumnCount + $Page->DtlColumnCount) ?>"><?php echo $ReportLanguage->Phrase("RptGrandSummary") ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->TotCount,0,-2,-2,-2); ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td></tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->NAME->Visible) { ?>
		<td data-field="NAME"<?php echo $Page->NAME->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
		<td data-field="surname"<?php echo $Page->surname->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->standnumber->Visible) { ?>
		<td data-field="standnumber"<?php echo $Page->standnumber->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->balance->Visible) { ?>
		<td data-field="balance"<?php echo $Page->balance->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tpts_Last_Payment_Made_balance"<?php echo $Page->balance->ViewAttributes() ?>><?php echo $Page->balance->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentnumbermonth->Visible) { ?>
		<td data-field="paymentnumbermonth"<?php echo $Page->paymentnumbermonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentmonth->Visible) { ?>
		<td data-field="paymentmonth"<?php echo $Page->paymentmonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->NAME->Visible) { ?>
		<td data-field="NAME"<?php echo $Page->NAME->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
		<td data-field="surname"<?php echo $Page->surname->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->standnumber->Visible) { ?>
		<td data-field="standnumber"<?php echo $Page->standnumber->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->balance->Visible) { ?>
		<td data-field="balance"<?php echo $Page->balance->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptAvg") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tpta_Last_Payment_Made_balance"<?php echo $Page->balance->ViewAttributes() ?>><?php echo $Page->balance->AvgViewValue ?></span></td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentnumbermonth->Visible) { ?>
		<td data-field="paymentnumbermonth"<?php echo $Page->paymentnumbermonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentmonth->Visible) { ?>
		<td data-field="paymentmonth"<?php echo $Page->paymentmonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->NAME->Visible) { ?>
		<td data-field="NAME"<?php echo $Page->NAME->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
		<td data-field="surname"<?php echo $Page->surname->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->standnumber->Visible) { ?>
		<td data-field="standnumber"<?php echo $Page->standnumber->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->balance->Visible) { ?>
		<td data-field="balance"<?php echo $Page->balance->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptMin") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptn_Last_Payment_Made_balance"<?php echo $Page->balance->ViewAttributes() ?>><?php echo $Page->balance->MinViewValue ?></span></td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentnumbermonth->Visible) { ?>
		<td data-field="paymentnumbermonth"<?php echo $Page->paymentnumbermonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentmonth->Visible) { ?>
		<td data-field="paymentmonth"<?php echo $Page->paymentmonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->NAME->Visible) { ?>
		<td data-field="NAME"<?php echo $Page->NAME->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
		<td data-field="surname"<?php echo $Page->surname->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->standnumber->Visible) { ?>
		<td data-field="standnumber"<?php echo $Page->standnumber->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->balance->Visible) { ?>
		<td data-field="balance"<?php echo $Page->balance->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptMax") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptx_Last_Payment_Made_balance"<?php echo $Page->balance->ViewAttributes() ?>><?php echo $Page->balance->MaxViewValue ?></span></td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentnumbermonth->Visible) { ?>
		<td data-field="paymentnumbermonth"<?php echo $Page->paymentnumbermonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentmonth->Visible) { ?>
		<td data-field="paymentmonth"<?php echo $Page->paymentmonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->NAME->Visible) { ?>
		<td data-field="NAME"<?php echo $Page->NAME->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->surname->Visible) { ?>
		<td data-field="surname"<?php echo $Page->surname->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->standnumber->Visible) { ?>
		<td data-field="standnumber"<?php echo $Page->standnumber->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->balance->Visible) { ?>
		<td data-field="balance"<?php echo $Page->balance->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span><?php echo $ReportLanguage->Phrase("AggregateColon") ?>
<span data-class="tptc_Last_Payment_Made_balance"<?php echo $Page->balance->ViewAttributes() ?>><?php echo $Page->balance->CntViewValue ?></span></td>
<?php } ?>
<?php if ($Page->instalments->Visible) { ?>
		<td data-field="instalments"<?php echo $Page->instalments->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentnumbermonth->Visible) { ?>
		<td data-field="paymentnumbermonth"<?php echo $Page->paymentnumbermonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->paymentmonth->Visible) { ?>
		<td data-field="paymentmonth"<?php echo $Page->paymentmonth->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && TRUE) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "Last_Payment_Madesmrypager.php" ?>
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
<?php include "Last_Payment_Madesmrypager.php" ?>
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
<a id="cht_Last_Payment_Made_Bar_Graph"></a>
<div class="">
<div id="div_ctl_Last_Payment_Made_Bar_Graph" class="ewChart">
<div id="div_Last_Payment_Made_Bar_Graph" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_Last_Payment_Made_Bar_Graph_grid" class="ewChartGrid"></div>
</div>
</div>
<?php

// Set up chart object
$Chart = &$Table->Bar_Graph;

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
<?php include_once "Last_Payment_Made_Bar_Graphchart.php" ?>
<?php if ($Page->Export <> "email" && !$Page->DrillDown) { ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<a href="javascript:void(0);" class="ewTopLink" onclick="$(document).scrollTop($('#top').offset().top);"><?php echo $ReportLanguage->Phrase("Top") ?></a>
<?php } ?>
<?php } ?>
<a id="cht_Last_Payment_Made_Pie_Chart"></a>
<div class="">
<div id="div_ctl_Last_Payment_Made_Pie_Chart" class="ewChart">
<div id="div_Last_Payment_Made_Pie_Chart" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_Last_Payment_Made_Pie_Chart_grid" class="ewChartGrid"></div>
</div>
</div>
<?php

// Set up chart object
$Chart = &$Table->Pie_Chart;

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
<?php include_once "Last_Payment_Made_Pie_Chartchart.php" ?>
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
