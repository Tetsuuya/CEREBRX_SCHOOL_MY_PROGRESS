# Attendance Module Code Changes

## 1. Controller Modifications:

* **Lines 66 and 115:** Commented out the post input reading for `$exempeted_notes`.
```diff
-            $exempeted_notes = $this->input->post('exempted_note'); 
+            // $exempeted_notes = $this->input->post('exempted_note'); 
```

* **Lines 148–150:** Commented out assignment logic for `exempted_note` when updating existing records.
```diff
                             if (!empty($excuse_notes[$value]) && $attendence_type_id == 6) {
                                 $arr['excuse_note']   = trim($excuse_notes[$value]);
                                 $arr['exempted_note'] = null;
-                            } elseif (!empty($exempeted_notes[$value]) && $attendence_type_id == 7) {
-                                $arr['exempted_note'] = trim($exempeted_notes[$value]);
-                                $arr['excuse_note']   = null;
+                            /* } elseif (!empty($exempeted_notes[$value]) && $attendence_type_id == 7) {
+                                $arr['exempted_note'] = trim($exempeted_notes[$value]);
+                                $arr['excuse_note']   = null; */
                             }
```

* **Lines 232–234:** Commented out assignment logic for `exempted_note` when inserting new records.
```diff
                             if (!empty($excuse_notes[$value]) && $attendence_type_id == 6) {
                                 $arr['excuse_note']   = trim($excuse_notes[$value]);
                                 $arr['exempted_note'] = null; // clear the other
-                            } elseif (!empty($exempeted_notes[$value]) && $attendence_type_id == 7) {
-                                $arr['exempted_note'] = trim($exempeted_notes[$value]);
-                                $arr['excuse_note']   = null; // clear the other
+                            /* } elseif (!empty($exempeted_notes[$value]) && $attendence_type_id == 7) {
+                                $arr['exempted_note'] = trim($exempeted_notes[$value]);
+                                $arr['excuse_note']   = null; // clear the other */
                             }
```

* **Line 344:** Commented out active initialization of the `$data['exempted_type_id']` variable.
```diff
             $data['excuse_type_id'] = 6; 
-            $data['exempted_type_id'] = 7; 
+            // $data['exempted_type_id'] = 7; 
```

* **Lines 50 and 342:** Added lookup logic to fetch the selected subject name and pass `$subject_name` to the view.
```diff
         $data['class_id'] = "";
         $data['section_id'] = "";
         $data['subject_id'] = "";
+        $data['subject_name'] = "";
         $data['date'] = "";
```
```diff
             $data['resultlist_male'] = $resultlist_male;
             $data['resultlist_female'] = $resultlist_female;
             // printx($data['resultlist_male']); 
+            $subject_name = "";
+            if ($subject) {
+                $subject_detail = $this->subject_model->get($subject);
+                $subject_name = isset($subject_detail['name']) ? $subject_detail['name'] : '';
+            }
+            $data['subject_name'] = $subject_name;
             $data['excuse_type_id'] = 6; 
```

---

## 2. View Modifications: 

* **Line 1 (Top of File):** Added fallback PHP definition for `$exempted_type_id` and initialized a helper boolean `$is_flag_ceremony`.
```diff
-<?php if (!isset($exempted_type_id)) { $exempted_type_id = 7; } ?>
+<?php 
+if (!isset($exempted_type_id)) { $exempted_type_id = 7; } 
+$is_flag_ceremony = (isset($subject_name) && (stripos($subject_name, 'Flag Ceremony') !== FALSE || stripos($subject_name, 'Chapel') !== FALSE));
+?>
```

* **Lines 278 (Male) and 450 (Female):** Added safety checks with `isset()` to prevent Undefined Index notices if `lastname` or other name components are not fetched by the database model.
```php
<?php 
$lastname = isset($value['lastname']) ? $value['lastname'] : '';
$firstname = isset($value['firstname']) ? $value['firstname'] : '';
$suffix = isset($value['suffix']) ? $value['suffix'] : '';
$middlename = isset($value['middlename']) ? $value['middlename'] : '';
echo "<b>".$lastname ."</b>, " . $firstname." ".$suffix." ".$middlename; 
?>
```

* **Lines 299–303 (Male) and Lines 443–447 (Female):** Commented out the rendering of the "Exempted" attendance status label.
```diff
-                                                                        if( $value['att_type'] == 'Exempted'){
-                                                                            ?>
-                                                                                <label class='label-warning label'><?php echo $value['att_type'];?></label>
-                                                                            <?php
-                                                                        } 
+                                                                        /* if( $value['att_type'] == 'Exempted'){
+                                                                            ?>
+                                                                                <label class='label-warning label'><?php echo $value['att_type'];?></label>
+                                                                            <?php
+                                                                        } */ 
```

* **Lines 305 (Male) and 448 (Female):** Added conditions to render visual info-badge labels for SC, OC, and OSR statuses.
```php
                                                                        if( strpos($value['att_type'], 'SC') !== FALSE || strpos($value['att_type'], 'OC') !== FALSE || strpos($value['att_type'], 'OSR') !== FALSE ){
                                                                            ?>
                                                                                <label class='label-info label'><?php echo $value['att_type'];?></label>
                                                                            <?php
                                                                        }
```

* **Lines 318 (Male) and 462 (Female):** Filtered out SC (8), OC (9), and OSR (10) from the radio loops.
```diff
                                                                 foreach ($attendencetypeslist as $key => $type) {
                                                                     if ($type['key_value'] != "H" && $type['id'] != $exempted_type_id) {
+                                                                        if (!$is_flag_ceremony && in_array($type['id'], [8, 9, 10])) {
+                                                                            continue;
+                                                                        }
                                                                         $att_type= str_replace(" ","_",strtolower($type['type']));
```

* **Lines 372–381 (Male) and Lines 516–525 (Female):** Commented out the **Exempted Note** input fields.
```diff
                                                                 <!-- Exempted Note -->
-                                                                <input type="text" 
-                                                                    name="exempted_note[<?php echo $value['student_session_id']; ?>]" 
-                                                                    class="form-control form-control-sm exempted-note-input" 
-                                                                    placeholder="Enter exempted note" 
-                                                                    id="exempted_input_<?php echo $value['student_session_id']; ?>" 
-                                                                    value="<?php echo !empty($value['exempted_note']) ? htmlspecialchars($value['exempted_note']) : ''; ?>"
-                                                                    <?php echo ($value['attendence_type_id'] != $exempted_type_id) ? 'style="display:none;"' : ''; ?>
-                                                                    <?php echo !empty($value['exempted_note']) ? $value['exempted_note'] : ''; ?>
-                                                                >
+                                                                <!-- <input type="text" 
+                                                                    name="exempted_note[<?php echo $value['student_session_id']; ?>]" 
+                                                                    class="form-control form-control-sm exempted-note-input" 
+                                                                    placeholder="Enter exempted note" 
+                                                                    id="exempted_input_<?php echo $value['student_session_id']; ?>" 
+                                                                    value="<?php echo !empty($value['exempted_note']) ? htmlspecialchars($value['exempted_note']) : ''; ?>"
+                                                                    <?php echo ($value['attendence_type_id'] != $exempted_type_id) ? 'style="display:none;"' : ''; ?>
+                                                                    <?php echo !empty($value['exempted_note']) ? $value['exempted_note'] : ''; ?>
+                                                                > -->
```

* **Lines 590–604:** Commented out the jQuery toggle mechanisms and assignments for `exemptedInput` and `exemptedError`.
```diff
         var excuseInput   = $('#excuse_input_'   + student_session_id);
         var excuseError   = $('#excuse_error_'   + student_session_id);
-        var exemptedInput = $('#exempted_input_' + student_session_id);
-        var exemptedError = $('#exempted_error_' + student_session_id);
+        // var exemptedInput = $('#exempted_input_' + student_session_id);
+        // var exemptedError = $('#exempted_error_' + student_session_id);
 
         // Hide both first
         excuseInput.hide().prop('disabled', true).val('');
         excuseError.text('');
-        exemptedInput.hide().prop('disabled', true).val('');
-        exemptedError.text('');
+        // exemptedInput.hide().prop('disabled', true).val('');
+        // exemptedError.text('');
 
         // Then show only the relevant one
         if (selected_type_id == excuse_type_id) {
             excuseInput.show().prop('disabled', false);
-        } else if (selected_type_id == exempted_type_id) {
-            exemptedInput.show().prop('disabled', false);
+        // } else if (selected_type_id == exempted_type_id) {
+        //     exemptedInput.show().prop('disabled', false);
         }
```

* **Radio Button Label Shortener:**
```php
<label for="attendencetype<?php echo $value['student_session_id'] . "-" . $count; ?>">
    <?php 
    $display_type = ucfirst($type['type']);
    if (strpos($display_type, ' - ') !== FALSE) {
        $display_type = explode(' - ', $display_type)[0];
    }
    echo $display_type;
    ?>
</label>
```

---

## 3. Database Insertion Script
```sql
INSERT INTO `attendence_type` (`id`, `type`, `key_value`, `is_active`, `created_at`, `updated_at`) VALUES
(8, 'SC - Commuter under Shuttler Service', '<b class="text text-info">SC</b>', 'yes', NOW(), NOW()),
(9, 'OC - Commuter under own vehicle', '<b class="text text-info">OC</b>', 'yes', NOW(), NOW()),
(10, 'OSR - Official School Representative', '<b class="text text-info">OSR</b>', 'yes', NOW(), NOW());
```
