<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new crMenu(EWR_MENUBAR_ID); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(32, "mi_All_Payment", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("32", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "All_Paymentsmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(38, "mi_Last_Payment_Made", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("38", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Last_Payment_Madesmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(46, "mi_Ledger_Balances", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("46", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Ledger_Balancessmry.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
