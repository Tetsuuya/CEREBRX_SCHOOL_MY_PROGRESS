# Documentation: R-Student

This module manages student profile details, documents uploads, and grades list rendering for Junior High and Senior High students.

## Changes Log:

* **July 7, 2026**:
  * **Profile View (`VIEWS/studentShow.php`)**: Added `isset()` checks for `subsidized`, `if_adventist`, `year_baptism`, and `institution` indexes on the `$student` profile details page to prevent Undefined Index PHP Notices if these columns are missing in the database.
* **July 3, 2026 (Uncommitted Changes)**:
  * **Grades Excel Import (`CONTROLLER/Student.php`)**: Updated `import_junior_grades` to parse only 3 terms (columns `F`, `G`, and `H`) and map the fourth term (`fourth`) value as `null`.
  * **Grades Profile View (`VIEWS/studentShow.php`)**: Re-designed the student details grades list table to display 3 terms (**Term 1**, **Term 2**, **Term 3**) instead of 4 quarters. Adjusted the loops count to 3 (`$last_quarter = 3;`) and modified the average calculations row to display averages based on the 3 terms.

---

## Detailed Code Changes & Line Ranges:

### 1. `CONTROLLER/Student.php` (Lines 1195–1215)
Updated the Excel student grades import logic to read only 3 columns of grades (column `F`, `G`, and `H`) and map the fourth column (`fourth`) as `null` in the database:
```php
<<<< Original
$first_quarter = $import_student_value['F'];
$second_quarter = $import_student_value['G'];
$third_quarter = $import_student_value['H'];
$fourth_quarter = $import_student_value['I'];
...
if(  $first_quarter || $second_quarter || $third_quarter || $fourth_quarter && $student_data->id ){  
    ...
    $data_new = array( 
        ...
        'first' => $first_quarter,
        'second' => $second_quarter,
        'third' => $third_quarter,
        'fourth' => $fourth_quarter,
    );
==== Modified
$first_quarter = isset($import_student_value['F']) ? $import_student_value['F'] : null;
$second_quarter = isset($import_student_value['G']) ? $import_student_value['G'] : null;
$third_quarter = isset($import_student_value['H']) ? $import_student_value['H'] : null;
...
if(  (isset($first_quarter) || isset($second_quarter) || isset($third_quarter)) && !empty($student_data->id) ){  
    ...
    $data_new = array( 
        ...
        'first' => $first_quarter,
        'second' => $second_quarter,
        'third' => $third_quarter,
        'fourth' => null,
    );
>>>>
```

### 2. `VIEWS/studentShow.php`
* **Lines 952–974 (Table headers)**:
```html
<<<< Original (4 Quarters)
<th>1st Qtr</th>
<th>2nd Qtr</th>
<th>3rd Qtr</th>
<th>4th Qtr</th>
==== Modified (3 Terms)
<th>Term 1</th>
<th>Term 2</th>
<th>Term 3</th>
>>>>
```
* **Lines 986–991 (Loop limits)**:
```php
<<<< Original
$last_quarter = 4;
$get_count = 4;
==== Modified
$last_quarter = 3;
$get_count = 3;
>>>>
```
* **Lines 1043–1069 (Grades accumulation check)**:
Removed the `fourth` quarter grades parsing logic and assignment.
```php
<<<< Original
} elseif($x==4){ 
    $fourth = $fourth + $grade_per_quarter;
    $grade_per_quarter != null?$count_subjects_fourth++:'';		
    if( empty( $grade_per_quarter )){
        $complete_fourth = false;
    }																		
}
==== Modified
[Removed/Omitted]
>>>>
```
* **Lines 1142–1188 (Average calculations & display row)**:
Removed all calculations and UI columns mapping for the `fourth` quarter.
```php
<<<< Original
$fourth_ave = $count_subjects_fourth != 0?$fourth/$count_subjects_fourth:0;
$fourth_ave =  number_format((float)$fourth_ave,$decimal_average, '.', '');
...
if( $complete_fourth ){
    ?><td><b><?php echo $fourth != 0 ? $fourth_ave:'';?></b></td><?php 
} else {
    ?><td><b></b></td><?php 
}
==== Modified
[Removed/Omitted]
>>>>
```

### 3. `VIEWS/studentShow.php` (Lines 120–132)
Added `isset()` checks around Adventist parent, baptism, and institution fields:
```php
<<<< Original
                             <li class="list-group-item">
                                 <b><?php echo 'Adventist Parent'; ?></b> <a class="pull-right text-aqua"><?php echo $student['subsidized']; ?></a>
                            </li>
                             <li class="list-group-item">
                                  <b><?php echo 'Baptized Adventist'; ?></b> <a class="pull-right text-aqua"><?php echo $student['if_adventist']; ?></a>
                             </li>
                              <li class="list-group-item" id="year_baptism_display" <?php if ($student['if_adventist'] != 'yes') echo 'style="display:none;"'; ?>>
                                  <b><?php echo 'Year of Baptism'; ?></b> <a class="pull-right text-aqua"><?php echo $student['year_baptism']; ?></a>
                             </li>
                              <li class="list-group-item" id="institution_display" <?php if ($student['subsidized'] != 'yes') echo 'style="display:none;"'; ?>>
                                 <b><?php echo 'Institution'; ?></b> <a class="pull-right text-aqua"><?php echo $student['institution']; ?></a>
                            </li>
==== Modified
                             <li class="list-group-item">
                                 <b><?php echo 'Adventist Parent'; ?></b> <a class="pull-right text-aqua"><?php echo isset($student['subsidized']) ? $student['subsidized'] : ''; ?></a>
                            </li>
                             <li class="list-group-item">
                                  <b><?php echo 'Baptized Adventist'; ?></b> <a class="pull-right text-aqua"><?php echo isset($student['if_adventist']) ? $student['if_adventist'] : ''; ?></a>
                             </li>
                              <li class="list-group-item" id="year_baptism_display" <?php if (!isset($student['if_adventist']) || $student['if_adventist'] != 'yes') echo 'style="display:none;"'; ?>>
                                  <b><?php echo 'Year of Baptism'; ?></b> <a class="pull-right text-aqua"><?php echo isset($student['year_baptism']) ? $student['year_baptism'] : ''; ?></a>
                             </li>
                              <li class="list-group-item" id="institution_display" <?php if (!isset($student['subsidized']) || $student['subsidized'] != 'yes') echo 'style="display:none;"'; ?>>
                                 <b><?php echo 'Institution'; ?></b> <a class="pull-right text-aqua"><?php echo isset($student['institution']) ? $student['institution'] : ''; ?></a>
                            </li>
>>>>
```
