<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new crMenu(EWR_MENUBAR_ID); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(77, "mci_GM58_Home", $ReportLanguage->MenuPhrase("77", "MenuText"), "../index.php", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(21, "mci_Payment_Reports", $ReportLanguage->MenuPhrase("21", "MenuText"), "#", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(18, "mi_GM58_Payments", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("18", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "GM58_Paymentssmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(23, "mi__3Months_Paid", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("23", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "_3Months_Paidsmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(20, "mi__3Months_Arrears", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("20", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "_3Months_Arrearssmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(45, "mci_System_Reports", $ReportLanguage->MenuPhrase("45", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(17, "mi_System_Logs", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("17", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "System_Logssmry.php", 45, "", TRUE, FALSE);
$RootMenu->AddMenuItem(46, "mi_System_Users", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("46", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "System_Userssmry.php", 45, "", TRUE, FALSE);
$RootMenu->AddMenuItem(79, "mi_Amendments_Report", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("79", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Amendments_Reportsmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(74, "mci_Stand_Reports", $ReportLanguage->MenuPhrase("74", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(82, "mi_Stands_Sold", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("82", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stands_Soldsmry.php", 74, "", TRUE, FALSE);
$RootMenu->AddMenuItem(48, "mi_Stand_Owners", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("48", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stand_Ownerssmry.php", 74, "", TRUE, FALSE);
$RootMenu->AddMenuItem(75, "mi_Stands_Created", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("75", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stands_Createdsmry.php", 74, "", TRUE, FALSE);
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
