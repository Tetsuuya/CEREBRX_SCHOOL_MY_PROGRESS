# Deployment & Changes Guide: Attendance Summary Feature

This document outlines the changes made under `Local` compared to `Backup`, list of new/modified files, and the corresponding remote server folders where you need to upload them using FileZilla to run the new feature.

---

## 1. FileZilla Upload Checklist

Upload these files from your local workspace to the remote folders listed below:

| File Type | Local File Path | Remote Target Folder | Action / Note |
| :--- | :--- | :--- | :--- |
| **Controller** | `Attendance_PRINCIPAL/Local/CONTROLLER/Stuattendence.php` | `/application/controllers/principal/` | **Overwrite** (Updated with centered headers, date parsing fix, and text clipping) |
| **Header Layout** | `Attendance_PRINCIPAL/Local/header.php` | `/application/views/layout/principal/` | **Overwrite** (Adds sidebar sub-menus) |
| **Attendance Report View** | `Attendance_PRINCIPAL/Local/VIEW/classattendencereport.php` | `/application/views/principal/stuattendence/` | **Overwrite** (Removes export button) |
| **Attendance Summary View** | `Attendance_PRINCIPAL/Local/VIEW/attendancesummary.php` | `/application/views/principal/stuattendence/` | **New File** (Renders new date-range view) |
| **Excel Template v3** | `Attendance_PRINCIPAL/Clean_Attendace_template_v3.xlsx` | `/uploads/template_documents/Attendace_Principal/` | **New File** (Fines template with right columns deleted) |

---

## 2. Summary of Changes (Backup vs. Local)

### A. Sidebar Menus (`Local/header.php`)
* **Before**: Had a single menu item link named **"Attendance Report"** pointing directly to `principal/stuattendence/classattendencereport`.
* **After**: Converted this item into an expandable tree view menu named **"Attendance"** containing two options:
  1. **Attendance Report** (leads to `classattendencereport` page)
  2. **Attendance Summary** (leads to new `attendanceSummary` page)

### B. Attendance Report (`Local/VIEW/classattendencereport.php`)
* **Before**: Had a green "Export Fines Sheet" button inside the student attendance grid.
* **After**: The export form button has been removed from this page. Exporting by specific date ranges has been consolidated into the **Attendance Summary** section.

### C. New Attendance Summary (`Local/VIEW/attendancesummary.php`)
* Created a new web interface containing:
  * **Grade, Section, Subject** dropdown selectors.
  * Cascading AJAX loaders to dynamically fetch sections when Grade changes, and subjects when Section changes.
  * **Start Date** and **End Date** selectors using the built-in datepicker UI calendar.
  * **"Generate Attendance Summary"** button to download the calculated report.

### D. Summary Format, Clipping, and Centering (`Local/CONTROLLER/Stuattendence.php`)
* **Direct XML Row Injection**: Uses lightweight `ZipArchive` XML string manipulation for high-speed Excel generation, preventing PHP memory exhaustion.
* **Date Parsing Fix**: Resolved a date range bug where `strtotime()` was incorrectly wrapped around `datetostrtotime()`, causing all date bounds to evaluate to `1970-01-01`.
* **Centered Headers**: Restored style IDs `25` (row 3) and `28` (row 4) in the sheet XML replacements to ensure that the School Year and Report Titles are centered correctly.
* **Style Corruption Fix**: Fixed an out-of-range cell style index issue in the Female Header Row (changed invalid style IDs `57`, `53`, and `58` to valid indices `26`, `13`, `8`, `33`, `34`, `36`), preventing Excel file corruption.
* **Clean Text Clipping**: Formatted empty cells under columns C to I with a single space (`' '`). This invisible character prevents the summary text from overflowing into empty adjacent columns, forcing Excel to clip it cleanly at cell borders.
* **Fines Calculation Removal**: Changed columns C (Flag) and D (Chapel) to output the summary count of student violations (e.g. `L = 3, A = 3, E = 2, SC = 1, OC = 0, OSR = 0`) instead of charging fine amounts, and left Column I (Amount) blank.
* **Merged Cells Fix**: Replaced static string replacements with a regex match for the female header row merge configuration (`<mergeCell ref="A9:B9" />`), ensuring it updates to the dynamic row offset correctly.
