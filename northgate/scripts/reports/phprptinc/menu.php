<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new crMenu(EWR_MENUBAR_ID); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(77, "mci_GM58_Home", $ReportLanguage->MenuPhrase("77", "MenuText"), "../index.php", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(105, "mi_Graphs_Three_Months_Arrears", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("105", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Graphs_Three_Months_Arrearssmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(106, "mi_Graphs_Three_Months_Arrears_Three_Months_Paid_Arrears_Bar", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("106", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Graphs_Three_Months_Arrearssmry.php#cht_Graphs_Three_Months_Arrears_Three_Months_Paid_Arrears_Bar", 105, "", TRUE, FALSE);
$RootMenu->AddMenuItem(107, "mi_Graphs_Three_Months_Arrears_Three_Months_Paid_Arrears_Pie", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("107", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Graphs_Three_Months_Arrearssmry.php#cht_Graphs_Three_Months_Arrears_Three_Months_Paid_Arrears_Pie", 105, "", TRUE, FALSE);
$RootMenu->AddMenuItem(94, "mi_Graphs_Three_Months_Paid", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("94", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Graphs_Three_Months_Paidsmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(95, "mi_Graphs_Three_Months_Paid_Three_Months_Paid__Bar", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("95", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Graphs_Three_Months_Paidsmry.php#cht_Graphs_Three_Months_Paid_Three_Months_Paid__Bar", 94, "", TRUE, FALSE);
$RootMenu->AddMenuItem(96, "mi_Graphs_Three_Months_Paid_Three_Months_Paid_Pie", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("96", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Graphs_Three_Months_Paidsmry.php#cht_Graphs_Three_Months_Paid_Three_Months_Paid_Pie", 94, "", TRUE, FALSE);
$RootMenu->AddMenuItem(21, "mci_Payment_Reports", $ReportLanguage->MenuPhrase("21", "MenuText"), "#", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(18, "mi_GM58_Payments", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("18", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "GM58_Paymentssmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(98, "mi_GM58_Payments_Payments_28Entered29_Bar_Graph", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("98", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "GM58_Paymentssmry.php#cht_GM58_Payments_Payments_28Entered29_Bar_Graph", 18, "", TRUE, FALSE);
$RootMenu->AddMenuItem(103, "mi_GM58_Payments_Payments_28entered29_Pie_Graph", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("103", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "GM58_Paymentssmry.php#cht_GM58_Payments_Payments_28entered29_Pie_Graph", 18, "", TRUE, FALSE);
$RootMenu->AddMenuItem(23, "mi__3Months_Paid", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("23", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "_3Months_Paidsmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(20, "mi__3Months_Arrears", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("20", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "_3Months_Arrearssmry.php", 21, "", TRUE, FALSE);
$RootMenu->AddMenuItem(45, "mci_System_Reports", $ReportLanguage->MenuPhrase("45", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(17, "mi_System_Logs", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("17", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "System_Logssmry.php", 45, "", TRUE, FALSE);
$RootMenu->AddMenuItem(46, "mi_System_Users", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("46", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "System_Userssmry.php", 45, "", TRUE, FALSE);
$RootMenu->AddMenuItem(79, "mi_Amendments_Report", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("79", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Amendments_Reportsmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(101, "mi_Amendments_Report_Amendments_Bar", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("101", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Amendments_Reportsmry.php#cht_Amendments_Report_Amendments_Bar", 79, "", TRUE, FALSE);
$RootMenu->AddMenuItem(102, "mi_Amendments_Report_Amendments_Pie", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("102", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Amendments_Reportsmry.php#cht_Amendments_Report_Amendments_Pie", 79, "", TRUE, FALSE);
$RootMenu->AddMenuItem(74, "mci_Stand_Reports", $ReportLanguage->MenuPhrase("74", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(82, "mi_Stands_Sold", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("82", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stands_Soldsmry.php", 74, "", TRUE, FALSE);
$RootMenu->AddMenuItem(48, "mi_Stand_Owners", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("48", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stand_Ownerssmry.php", 74, "", TRUE, FALSE);
$RootMenu->AddMenuItem(75, "mi_Stands_Created", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("75", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Stands_Createdsmry.php", 74, "", TRUE, FALSE);
$RootMenu->AddMenuItem(99, "mi_Stands_Created_Stands_Created_Bar", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("99", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Stands_Createdsmry.php#cht_Stands_Created_Stands_Created_Bar", 75, "", TRUE, FALSE);
$RootMenu->AddMenuItem(100, "mi_Stands_Created_Stands_Created_Pie", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("100", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Stands_Createdsmry.php#cht_Stands_Created_Stands_Created_Pie", 75, "", TRUE, FALSE);
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
