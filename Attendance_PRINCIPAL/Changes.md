# Deployment & Changes Guide: Attendance Summary Feature

This document outlines the changes made under `Local` compared to `Backup`, list of new/modified files, and the corresponding remote server folders where you need to upload them using FileZilla to run the new feature.

---

## 1. FileZilla Upload Checklist

Upload these files from your local workspace to the remote folders listed below:

| File Type | Local File Path | Remote Target Folder | Action / Note |
| :--- | :--- | :--- | :--- |
| **Controller** | `Attendance_PRINCIPAL/Local/CONTROLLER/Stuattendence.php` | `/application/controllers/principal/` | **Overwrite** |
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
  * Cascading AJAX loaders to dynamically fetch sections when Grade changes, and subjects when Section changes (using correct system routes).
  * **Start Date** and **End Date** selectors using the built-in datepicker UI calendar.
  * **"Generate Attendance Summary"** button to download the calculated report.

### D. Fine Calculations & Fast XML Streaming (`Local/CONTROLLER/Stuattendence.php`)
* **Memory Exhaustion Fix**: Removed memory-heavy `PHPExcel` workbook object building. Replaced it with lightweight `ZipArchive` extraction and inline XML string injection (`sheet1.xml` and `sharedStrings.xml`), matching the high-speed method used by the Teacher grading module.
* **Date Range Query**: Built `export_attendance_summary()` method which dynamically extracts student session records, counts absences, multiplies by 50, and maps them directly into columns C (Flag) and D (Chapel) without using column formulas.
* **Merged Cells fix**: Replaced static string replacements for merged row headers with regex patterns matching `/ <mergeCell ref="A9:B9" /> /i` to ensure that row index index 2 and others display correctly without being hidden or merged under column B.
