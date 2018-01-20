<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(77, "mmci_GM58_Home", $ReportLanguage->MenuPhrase("77", "MenuText"), "../index.php", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(21, "mmci_Payment_Reports", $ReportLanguage->MenuPhrase("21", "MenuText"), "#", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(18, "mmi_GM58_Payments", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("18", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "GM58_Paymentssmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(23, "mmi__3Months_Paid", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("23", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "_3Months_Paidsmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(20, "mmi__3Months_Arrears", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("20", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "_3Months_Arrearssmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(45, "mmci_System_Reports", $ReportLanguage->MenuPhrase("45", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(17, "mmi_System_Logs", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("17", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "System_Logssmry.php", 45, "", TRUE, FALSE);
$RootMenu->AddMenuItem(46, "mmi_System_Users", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("46", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "System_Userssmry.php", 45, "", TRUE, FALSE);
$RootMenu->AddMenuItem(79, "mmi_Amendments_Report", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("79", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Amendments_Reportsmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(74, "mmci_Stand_Reports", $ReportLanguage->MenuPhrase("74", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(82, "mmi_Stands_Sold", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("82", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stands_Soldsmry.php", 74, "", TRUE, FALSE);
$RootMenu->AddMenuItem(48, "mmi_Stand_Owners", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("48", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stand_Ownerssmry.php", 74, "", TRUE, FALSE);
$RootMenu->AddMenuItem(75, "mmi_Stands_Created", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("75", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stands_Createdsmry.php", 74, "", TRUE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
