# Documentation: R-Reports

This module handles report generation, master sheets, honor rolls, ranking summaries, and printed grade sheets for the registrar portal.

## Changes Log:

* **July 7, 2026**:
  * **Batch Grade Slip (`VIEWS/report_card.php`)**: Updated the `#batch-grade-slip` click event to dynamically clone `form5` and append only the checked student IDs before submission. If no boxes are checked, it submits the original form (which includes all students).
  * **Grade Slip Controller (`CONTROLLER/Report.php`)**: Modified the `show_grade_slip()` method to check if the `$student_id` is an array when a single student is processed (due to checkbox selection), and safely extract the single ID string.
* **July 3, 2026 (Uncommitted Changes)**:
  * **Master Sheet (`VIEWS/master_sheet.php`)**: Changed the dropdown terms from 4 quarters to a 3-term system (`Term 1`, `Term 2`, `Term 3`, `Final Grade`).
  * **Grade Sheet Web View (`VIEWS/grade_sheet.php`)**: Changed headers to 3 terms (**I**, **II**, **III**, and **Final**) instead of 4 quarters. Adjusted the `Male` and `Female` section divider rows cell count from 6 cells to 5 cells and empty colspan value from 6 to 5. This resolved the javascript error (`Cannot read properties of undefined reading 'mData'`) that blocked sidebar scroll scripts from loading.
  * **Grade Sheet PDF Logic (`CONTROLLER/Report.php`)**: Redesigned the printed PDF layout inside `print_grade_sheet()` from side-by-side tables to a single full-page width table. Organized rows sequentially: male student names printed in **blue**, female student names printed in **red**. Appended teacher and principal signature rows inside the table `<tbody>` and printed the adviser's name with prefix below the table.
  * **PDF Template (`TEMPLATE/grade_sheet.php`)**: Created a template layout file to output the school year and wrap the HTML content.
* **July 2, 2026 (`bbaca60`)**:
  * Added `CONTROLLER/Report.php` to handle pdf exports and ranking calculations.
  * Added `VIEWS/report_card.php` view.
  * Added reports views: `honor_student.php`, `master_sheet.php`, `student_ranking.php`, and `grade_sheet.php`.

---

## Detailed Code Changes & Line Ranges:

### 1. `VIEWS/master_sheet.php` (Lines 183–215)
Updated the quarter selection dropdown to display 3 terms:
```html
<<<< Original (4 Quarters)
<option value="1">1st Quarter</option>
<option value="2">2nd Quarter</option>
<option value="3">3rd Quarter</option>
<option value="4">4th Quarter</option>
<option value="final">Final Grade</option>
==== Modified (3 Terms)
<option value="1">Term 1</option>
<option value="2">Term 2</option>
<option value="3">Term 3</option>
<option value="final">Final Grade</option>
>>>>
```

### 2. `VIEWS/grade_sheet.php`
* **Lines 166–172 (Table header columns loop)**:
```php
<<<< Original
for($x=1;$x<=4;$x++){
==== Modified
for($x=1;$x<=3;$x++){
>>>>
```
* **Lines 180–183 & Lines 335–338 (No record fallback colspan)**:
```php
<<<< Original
<td colspan="<?php echo $enableStrand ? $total_quarter + 2 : 6; ?>"
==== Modified
<td colspan="<?php echo $enableStrand ? $total_quarter + 2 : 5; ?>"
>>>>
```
* **Lines 195–206 & Lines 350–361 (Male/Female section divider cell count)**:
```html
<<<< Original
<tr>
    <td class="sorting_disabled"><strong>Male/Female</strong></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
==== Modified
<tr>
    <td class="sorting_disabled"><strong>Male/Female</strong></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
>>>>
```
* **Lines 290–325 & Lines 446–481 (Student grades average loop)**:
```php
<<<< Original
for( $x=1;$x<=4;$x++){
...
$average = $final_grade / 4;
==== Modified
for( $x=1;$x<=3;$x++){
...
$average = $final_grade / 3;
>>>>
```

### 3. `CONTROLLER/Report.php` (Lines 6819–7508)
* **Single Table layout inside print_grade_sheet()**:
```php
<<<< Original (Side-by-Side Tables)
<table width="100%">
    <tr>
        <td width="48%">[Males Table (4 Quarters)]</td>
        <td width="4%">&nbsp;</td>
        <td width="48%">[Females Table (4 Quarters)]</td>
    </tr>
</table>
[Adviser Signatures block with separate Principal approval table]
==== Modified (Single Table layout with 3 Terms)
<table width="100%" style="border: 1px solid black; border-collapse: collapse; font-family: sans-serif; font-size: 11px;">
    <thead>
        <tr style="background-color: #EFEFEF;">
            <th>Name of Pupils</th>
            <th text-rotate="90">TERM 1</th>
            <th text-rotate="90">TERM 2</th>
            <th text-rotate="90">TERM 3</th>
            <th>GEN. AVE</th>
            <th>REMARKS</th>
        </tr>
    </thead>
    <tbody>
        [Loop Males: output names in color: #0000FF]
        [Loop Females: output names in color: #FF0000]
        [Append Signature Rows: SUBJ. TEACHER and SCH. PRINCIPAL]
    </tbody>
</table>
[Adviser full name text colored and padded at bottom]
>>>>
```
* **Handle student_id as an Array in show_grade_slip() (Lines 7423–7433)**:
```php
<<<< Original
            } else {

                $this->m_pdf->pdf->AddPage($orientation,$size,'5','5','2','5','5','5','5','5');

                $data['student_id'] = $student_id;

                $html = $this->get_grade_slip( $data );
==== Modified
            } else {

                $this->m_pdf->pdf->AddPage($orientation,$size,'5','5','2','5','5','5','5','5');

                $data['student_id'] = is_array($student_id) ? $student_id[0] : $student_id;

                $html = $this->get_grade_slip( $data );
>>>>
```

### 4. `VIEWS/report_card.php` (Lines 357–374)
* **Filter student_id[] dynamically in batch-grade-slip click handler**:
```javascript
<<<< Original
		$("#batch-grade-slip").click(function () { 
          $('#form5').submit();
        });
==== Modified
		$("#batch-grade-slip").click(function () { 
            var checked_students = $('input[name="student_print[]"]:checked');
            if (checked_students.length > 0) {
                var form_clone = $('#form5').clone();
                form_clone.find('input[name="student_id[]"]').remove();
                checked_students.each(function () {
                    var student_id = $(this).val();
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'student_id[]',
                        value: student_id
                    }).appendTo(form_clone);
                });
                form_clone.appendTo('body').submit().remove();
            } else {
                $('#form5').submit();
            }
        });
>>>>
```

### 5. `TEMPLATE/grade_sheet.php` (New File)
Created the wrapper template layout:
```html
<!DOCTYPE html>
<html>
<head><title></title></head>
<body>
    <div style="text-align: right; font-family: sans-serif; font-size: 11px; margin-bottom: 10px; font-weight: bold;">
        School Year: <?php echo trim(str_replace('S. Y.', '', $school_year)); ?>
    </div>
    <?php if(isset($content) && $content ){ echo $content; } ?>
</body>
</html>
```
