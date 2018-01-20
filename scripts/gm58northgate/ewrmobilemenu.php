<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(32, "mmi_All_Payment", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("32", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "All_Paymentsmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(38, "mmi_Last_Payment_Made", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("38", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Last_Payment_Madesmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(46, "mmi_Ledger_Balances", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("46", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Ledger_Balancessmry.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
