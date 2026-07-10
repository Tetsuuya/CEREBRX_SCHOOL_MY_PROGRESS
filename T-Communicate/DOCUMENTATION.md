# Documentation: T-Communicate

This module manages notifications and the SMS gateway utilities for sending announcements.

## Changes Log:

* **July 7, 2026**:
  * **VIEWS/notificationAdd.php & VIEWS/notificationEdit.php**: Added real-time SMS character countdown widget and segment estimator, with dynamic critical warnings when emails, domains, or complex characters are entered. Modified to count all spaces/newlines and prepended a non-editable `"CBX School\n\n"` header.
  * **VIEWS/notificationList.php**: Updated notice board parent column to show detailed lists of specific targeted parents (guardian names and phone numbers) or targeted class-sections instead of a generic "Parent: Yes" text.
  * **CONTROLLER/Notification.php**: Modified `CONTROLLER/Notification.php` to clean, strip HTML tags, preserve newlines, decode HTML entities in SMS messages, and prepend `"CBX School\n\n"` header before inserting into `sms_que`.
* **July 2, 2026 (`bbaca60`)**:
  * Updated controller `Notification.php` and model `Notification_model.php` to handle direct database logs.
  * Updated `VIEWS/notificationList.php` view.
* **June 30, 2026 (`9d9f997`)**:
  * Modified `CONTROLLER/Notification.php`
* **June 30, 2026 (`dbb0b3c`)**:
  * Created the `T-Communicate` module directory.
  * Added `CONTROLLER/Notification.php` to route SMS actions.
  * Added model file `Notification_model.php`.
  * Added views for notifications CRUD actions: `notificationAdd.php`, `notificationEdit.php`, `notificationList.php`

---

## Detailed Code Changes & Line Ranges:

### 1. `CONTROLLER/Notification.php` 
* **Add Batch Queue Insert for SMS Notifications**:
```php
<<<< Original (Direct API Call)
$this->load->library('smsgateway');
foreach ($students_contact as $s_value) {
    $phone = $s_value['guardian_phone'];
    $phone = str_replace(' ', '', $phone);
    $phone = str_replace('-', '', $phone);
    if (strlen($phone) > 10) {
        $this->smsgateway->sentNotificationSMS($sms_message, $phone);
    }
}
==== Modified (Queued Batch Database Insertion)
$parent_sms_data = array();
foreach ($students_contact as $s_value) {
    $phone = $s_value['guardian_phone'];
    $phone = str_replace(' ', '', $phone);
    $phone = str_replace('-', '', $phone);
    if (strlen($phone) > 10) {
        $parent_sms_data[] = array(
            'notification_id' => $insert_id,
            'sms_msg' => $sms_message,
            'sms_number' => $phone,
            'sms_status' => 'idle',
            'sms_teacher' => 'no',
            'sms_parent' => 'yes'
        );
    }
}
if (!empty($parent_sms_data)) {
    $this->db->insert_batch('sms_que', $parent_sms_data);
}
>>>>
```

* **Direct SMS Block in Edit/Delete Methods**:
```php
<<<< Original
// No validation checks
==== Modified (Bypassed / Blocked for Direct SMS)
if (!empty($notification['custom_student_ids'])) {
     $this->session->set_flashdata('msg', '<div class="alert alert-danger">Direct SMS notifications cannot be edited!</div>');
     redirect('teacher/notification/index');
}
>>>>
```

* **Format and Clean SMS Message Content (Strip HTML tags, Convert breaks/paragraphs, Replace non-breaking spaces, and Decode HTML entities)**:
```php
<<<< Original
            $message = $this->input->post('message');

            // $sms_message = htmlentities($message);

            $sms_message = strip_tags($message);
==== Modified
            $message = $this->input->post('message');

            // convert HTML line breaks and paragraph ends to plain-text newlines
            $sms_message = preg_replace('/<(br|br\s*\/)>/i', "\n", $message);
            $sms_message = preg_replace('/<\/(p|div|li|h[1-6])>/i', "\n", $sms_message);

            // strip remaining HTML tags
            $sms_message = strip_tags($sms_message);

            // decode HTML entities
            $sms_message = html_entity_decode($sms_message, ENT_QUOTES, 'UTF-8');

            // replace UTF-8 non-breaking spaces with standard spaces
            $sms_message = str_replace(array("\xc2\xa0", "\xa0"), ' ', $sms_message);

            // strip carriage returns
            $sms_message = str_replace("\r", '', $sms_message);

            // reduce multiple consecutive blank lines to at most 2 newlines
            $sms_message = preg_replace("/\n{3,}/", "\n\n", $sms_message);

            // trim leading and trailing spaces/newlines
            $sms_message = trim($sms_message);
>>>>
```

### 2. `VIEWS/notificationList.php` (Lines 134–138)
* **Detailed "Message To" display for targeted Parents and Sections**:
```html
<<<< Original (Generic Yes/No)
                                                                    <li>

                                                                        <i class="fa fa-user" aria-hidden="true"></i>
                                                                        <?php echo $this->lang->line('parent'); ?> : <?php echo $notification['visible_parent']; ?>
                                                                    </li>
==== Modified (Detailed lists)
                                                                    <li>
                                                                        <i class="fa fa-user" aria-hidden="true"></i>
                                                                        <?php echo $this->lang->line('parent'); ?> : 
                                                                        <?php 
                                                                        if ($notification['visible_parent'] == 'Yes' || $notification['visible_parent'] == 'yes') {
                                                                            if (!empty($notification['custom_student_ids'])) {
                                                                                $student_ids = explode(',', $notification['custom_student_ids']);
                                                                                $this->db->select('students.firstname, students.lastname, students.guardian_name, students.guardian_phone, classes.class, sections.section');
                                                                                $this->db->from('students');
                                                                                $this->db->join('student_session', 'student_session.student_id = students.id AND student_session.session_id = (SELECT session_id FROM sch_settings LIMIT 1)', 'left', FALSE);
                                                                                $this->db->join('classes', 'classes.id = student_session.class_id', 'left');
                                                                                $this->db->join('sections', 'sections.id = student_session.section_id', 'left');
                                                                                $this->db->where_in('students.id', $student_ids);
                                                                                $query = $this->db->get();
                                                                                $students = $query->result_array();
                                                                                
                                                                                echo 'Specific Parent';
                                                                                foreach ($students as $student) {
                                                                                    $class_sec = '';
                                                                                    if (!empty($student['class']) && !empty($student['section'])) {
                                                                                        $class_sec = $student['class'] . ' - ' . $student['section'] . ' - ';
                                                                                    }
                                                                                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $class_sec . $student['guardian_name'] . ' (' . $student['guardian_phone'] . ')';
                                                                                }
                                                                            } elseif (!empty($notification['custom_parent'])) {
                                                                                $pairs = explode(',', $notification['custom_parent']);
                                                                                $section_info = array();
                                                                                foreach ($pairs as $pair) {
                                                                                    $parts = explode('-', $pair);
                                                                                    if (count($parts) === 2) {
                                                                                        $class_id = $parts[0];
                                                                                        $section_id = $parts[1];
                                                                                        
                                                                                        $this->db->select('classes.class, sections.section');
                                                                                        $this->db->from('class_sections');
                                                                                        $this->db->join('classes', 'classes.id = class_sections.class_id');
                                                                                        $this->db->join('sections', 'sections.id = class_sections.section_id');
                                                                                        $this->db->where('class_sections.class_id', $class_id);
                                                                                        $this->db->where('class_sections.section_id', $section_id);
                                                                                        $query = $this->db->get();
                                                                                        $res = $query->row_array();
                                                                                        if ($res) {
                                                                                            $section_info[] = $res['class'] . ' ' . $res['section'];
                                                                                        }
                                                                                    }
                                                                                }
                                                                                if (!empty($section_info)) {
                                                                                    echo implode(', ', $section_info);
                                                                                } else {
                                                                                    echo 'Yes';
                                                                                }
                                                                            } else {
                                                                                echo 'Yes';
                                                                            }
                                                                        } else {
                                                                            echo 'No';
                                                                        }
                                                                        ?>
                                                                    </li>
>>>>
```

### 3. SMS Counter, Warnings & Non-Editable Header

* **Wysihtml5 Editor Header CSS Injection (In both `notificationAdd.php` and `notificationEdit.php`)**:
```javascript
// Inside wysihtml5 initialization events:
"load": function() {
    var editorInstance = this;
    
    var injectStyle = function() {
        try {
            var iframeDoc = editorInstance.composer.element.ownerDocument;
            if (!iframeDoc.getElementById('sms-header-style')) {
                var style = iframeDoc.createElement('style');
                style.id = 'sms-header-style';
                style.innerHTML = 'body::before { content: "CBX School:\\a"; font-weight: bold; display: block; white-space: pre; color: #333; margin-bottom: 5px; }';
                iframeDoc.head.appendChild(style);
            }
        } catch (e) {
            console.error("Error injecting editor header style:", e);
        }
    };
    
    var updateSMSCounter = function() {
        injectStyle();
        var html = editorInstance.getValue();
        var result = checkSMSContent(html);
        // ... updates UI ...
    };
    
    editorInstance.on("change", updateSMSCounter);
    var composerElem = editorInstance.composer.element;
    composerElem.addEventListener('keyup', updateSMSCounter);
    composerElem.addEventListener('paste', updateSMSCounter);
    composerElem.addEventListener('input', updateSMSCounter);
    
    updateSMSCounter();
},
"cancel:dialog": function() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
},
"close:dialog": function() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
},
"save:dialog": function() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
}
```

* **Dynamic Character and Encoding Calculator (`checkSMSContent`)**:
```javascript
function checkSMSContent(html) {
    var text = html;
    
    // Convert tags to newlines without creating double/trailing spaces
    text = text.replace(/<(br|br\s*\/)>/ig, "\n");
    text = text.replace(/<\/(p|div|li|h[1-6])>\s*<(p|div|li|h[1-6])[^>]*>/ig, "\n");
    text = text.replace(/<\/(p|div|li|h[1-6])>/ig, "");
    text = text.replace(/<[^>]*>/g, "");
    
    // Decode entities
    var temp = document.createElement("div");
    temp.innerHTML = text;
    text = temp.textContent || temp.innerText || "";
    
    text = text.replace(/\xa0/g, ' ').replace(/\u00a0/g, ' ');
    text = text.replace(/\r/g, '');
    text = text.replace(/\n{3,}/g, "\n\n");
    
    // Prepend CBX School header
    text = "CBX School:\n" + text;
    
    // ... GSM-7 / Unicode length and warning checks ...
}
```

* **Controller Formatting, Prepending, and 4-Segment Validation (In `Notification.php`)**:
```php
    // Form validation rule:
    $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean|callback_check_sms_segments');

    // Validation callback:
    public function check_sms_segments($message) {
        $clean_html = preg_replace('/<(div|span)[^>]*contenteditable="false"[^>]*>(.*?)<\/\1>/is', '', $message);
        
        $sms_message = preg_replace('/<(br|br\s*\/)>/i', "\n", $clean_html);
        $sms_message = preg_replace('/<\/(p|div|li|h[1-6])>\s*<(p|div|li|h[1-6])[^>]*>/i', "\n", $sms_message);
        $sms_message = preg_replace('/<\/(p|div|li|h[1-6])>/i', '', $sms_message);
        
        $sms_message = strip_tags($sms_message);
        $sms_message = html_entity_decode($sms_message, ENT_QUOTES, 'UTF-8');
        $sms_message = str_replace(array("\xc2\xa0", "\xa0"), ' ', $sms_message);
        $sms_message = str_replace("\r", '', $sms_message);
        $sms_message = preg_replace("/\n{3,}/", "\n\n", $sms_message);
        $sms_message = preg_replace('/^[\s\p{Z}\r\n]+|[\s\p{Z}\r\n]+$/u', '', $sms_message);

        if (strpos($sms_message, "CBX School") !== 0) {
            $sms_message = "CBX School:\n" . $sms_message;
        }

        if ($this->get_sms_segments($sms_message) > 4) {
            $this->form_validation->set_message('check_sms_segments', 'The {field} exceeds the maximum limit of 4 SMS segments. Please shorten your message.');
            return FALSE;
        }
        return TRUE;
    }
```
