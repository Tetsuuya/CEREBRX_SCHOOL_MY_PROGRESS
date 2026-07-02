# Changes: Backup vs Local for Teacher Report Grade Sheet

This document outlines the modifications made to transition the Grade Sheet report from a 4-Quarter system to a 3-Term system, matching the new styling template for Cagayan de Oro Christian School (COCS).

---

## 1. Web Layout Updates (Grade Sheet View)
* **File Modified**: `application/views/teacher/reports/grade_sheet.php`
* **Changes**:
  * Relabeled table headers to display Term 1, Term 2, and Term 3 (skipping Term 4 completely).
  * Changed loop calculations from repeating 4 times to repeating 3 times.
  * Updated average calculations to sum the three terms and divide by `3` instead of `4`.
  * Adjusted spacer row columns to prevent table breaking and header misalignments.

---

## 2. Print Template wrapper (PDF Layout Wrapper)
* **File Modified**: `application/views/template/grade_sheet.php`
* **Changes**:
  * Updated layout wrapper style using clean, modern typography (Inter font).
  * Positioned the school logo (`uploads/school_content/logo/report-gradesheet/COCS.png`) on the top-left side.
  * Configured all school details, grading sheet title, and subject meta information to align neatly on the right side.

---

## 3. PDF Table Generation and Averages (Report Controller)
* **File Modified**: `application/controllers/teacher/Report.php`
* **Changes**:
  * Unified male and female student lists into one full-width table instead of two side-by-side tables.
  * Applied color-coding to student names (Blue for boys, Red for girls).
  * Added columns for **Term 1**, **Term 2**, **Term 3**, **General Average**, and **Remarks** (Passed/Failed).
  * Removed the redundant certification text block and moved the **Subject Teacher** and **School Principal** signature rows directly into the bottom cells of the table itself.
  * Placed the class adviser name at the very bottom left below the table.
