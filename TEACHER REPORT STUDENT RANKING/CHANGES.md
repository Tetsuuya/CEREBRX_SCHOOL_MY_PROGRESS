# Student Ranking Report Changes

This document details all the changes made to the **Student Ranking** module (`TEACHER REPORT STUDENT RANKING`) compared to the original `BACKUP` code. These changes transition the module from **Quarters** to **Terms**, resolve AJAX permissions for teachers, and eliminate PHP warning banners.

---

## 1. Controller Changes (`LOCAL/CONTROLLER/Report.php`)

### 1.1 Filter Metadata Keys from Student Ranking Array
* **Type of Change**: Bug Fix
* **Method**: `student_ranking()`

#### Code Comparison
##### Before (Original `BACKUP` State)
```php
			$studentranking = $this->grade_model->getStudentAverage( $studentlist, $quarter, $semester, $combine_category );
			$getgraderanking = $this->grade_model->getgraderanking( $studentranking );
            $data['studentlist'] = $getgraderanking;
```

##### After (Today's `LOCAL` State)
```php
			$studentranking = $this->grade_model->getStudentAverage( $studentlist, $quarter, $semester, $combine_category );
			$getgraderanking = $this->grade_model->getgraderanking( $studentranking );
			if (is_array($getgraderanking)) {
				foreach ($getgraderanking as $key => $val) {
					if (!is_numeric($key)) {
						unset($getgraderanking[$key]);
					}
				}
			}
            $data['studentlist'] = $getgraderanking;
```

* **Layman's Explanation**: The database calculation appends information like `'quarter'` and `'semester'` as text keys at the end of the student list array. The system was updated to automatically filter out these non-numeric entries before sending the data to the frontend webpage.
* **Purpose**: Bypasses any view-related bugs or template discrepancies by fixing the data structure at the source. This completely eliminates PHP warnings on the webpage.

---

## 2. View Changes (`LOCAL/VIEWS/student_ranking.php`)

### 2.1 Form Action Target Route
* **Type of Change**: Routing Bug Fix
* **Lines Changed**: Line 24

#### Code Comparison
##### Before (Original `BACKUP` State)
```html
<form id='form1' action="<?php echo site_url('admin/report/student_ranking') ?>"  method="post" accept-charset="utf-8">
```

##### After (Today's `LOCAL` State)
```html
<form id='form1' action="<?php echo site_url('teacher/report/student_ranking') ?>"  method="post" accept-charset="utf-8">
```

* **Layman's Explanation**: The search form was sending requests to the admin-level page instead of the teacher-level page.
* **Purpose**: Submits the search query to the correct teacher route so logged-in teachers are not blocked or redirected.

---

### 2.2 Term Dropdown Select and Options
* **Type of Change**: Terminology & Calendar Transition
* **Lines Changed**: Lines 77-92

#### Code Comparison
##### Before (Original `BACKUP` State)
```php
<label for="exampleInputEmail1"><?php echo $this->lang->line('quarter'); ?></label>
<select  id="quarter" name="quarter" class="form-control" >
   <option value=""><?php echo $this->lang->line('select'); ?></option>
    <?php
        foreach ($getquarter as $key => $value) {
        ?>
        <option  value="<?php echo $key; ?>" <?php if($quarter == $value) echo "selected"; ?>><?php echo $value; ?></option>
        <?php
        }
    ?>
</select>
```

##### After (Today's `LOCAL` State)
```php
<label for="exampleInputEmail1">Term</label>
<select  id="quarter" name="quarter" class="form-control" >
   <option value=""><?php echo $this->lang->line('select'); ?></option>
     <?php
         foreach ($getquarter as $key => $value) {
             if ($key > 3) {
                 continue;
             }
         ?>
         <option  value="<?php echo $key; ?>" <?php if($quarter == $key || $quarter == $value) echo "selected"; ?>>Term <?php echo $key; ?></option>
         <?php
         }
     ?>
     <option  value="final" <?php if($quarter == 'final') echo "selected"; ?>>Final Grade</option>
</select>
```

* **Layman's Explanation**: The dropdown box was relabeled from "Quarter" to "Term", limits choice inputs to Term 1, Term 2, and Term 3 (removing Term 4 completely), and adds a new "Final Grade" selector option.
* **Purpose**: Completes the transition of the student ranking search criteria to the 3-term academic system.

---

### 2.3 Skip Non-Numeric Array Keys in Student Loop
* **Type of Change**: Bug Fix
* **Lines Changed**: Lines 140-144

#### Code Comparison
##### Before (Original `BACKUP` State)
```php
foreach ($studentlist as  $student_value) {
	if( !empty( $student_value["id"])){
```

##### After (Today's `LOCAL` State)
```php
foreach ($studentlist as $key => $student_value) {
	if (!is_numeric($key)) {
		continue;
	}
	if( !empty( $student_value["id"])){
```

* **Layman's Explanation**: Adds a double-check inside the loops to skip over non-student metadata items (like `'quarter'` and `'semester'` string variables).
* **Purpose**: Prevents the view from generating errors or empty table rows when rendering the student list.

---

### 2.4 AJAX Endpoint Section Selector Load
* **Type of Change**: Security & Permission Fix
* **Lines Changed**: Lines 207, 225, 246, 260

#### Code Comparison
##### Before (Original `BACKUP` State)
```javascript
url: base_url + "sections/getByClass",
url: base_url + "admin/strand/allowStrand",
```

##### After (Today's `LOCAL` State)
```javascript
url: base_url + "teacher/sections/getByClass",
url: base_url + "teacher/strand/allowStrand",
```

* **Layman's Explanation**: The webpage was trying to fetch sections and academic strands from the admin portal backend.
* **Purpose**: Resolves the broken section load issue when teachers are viewing the page by routing the request through the teacher portal controllers, which they have access permissions for.

---

### 2.5 Decimal Formatting of Average Grades
* **Type of Change**: Visual Formatting / Consistency Fix
* **Lines Changed**: Line 151

#### Code Comparison
##### Before (Original `BACKUP` State)
```php
$final_grade = round( $student_value['final_grade'], 2);
```

##### After (Today's `LOCAL` State)
```php
$final_grade = number_format((float)$student_value['final_grade'], 2, '.', '');
```

* **Layman's Explanation**: The display value for the general average grade column was changed to always show exactly 2 decimal places.
* **Purpose**: Ensures trailing zeros are kept (e.g. displaying `94.90` instead of `94.9`, and `93.00` instead of `93`), matching the format used in other areas of the system.

