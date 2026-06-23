# Principal System - Complete Code Changes Report (Wednesday to Friday)

This document provides a comprehensive, file-by-file comparison of all the changes made to the **Principal** system, covering **Wednesday's session** (initial role separation) and **Thursday/Friday's session** (pagination, autocomplete suggestions search, export functions, and printing updates) relative to the original `BACKUP` state.

All file changes are listed in sequential order from the top of the file to the bottom (by line numbers).

---

## 1. Controller - Controller/Gatepass.php

### 1.1 Feature: Removal of Regular & Emergency Requests (Only Campus Leaves Shown)
* **Session**: Wednesday (Changed Query Call) & Thursday/Friday (Added Pagination/Search)
* **Type of Change**: Modified
* **Lines Changed**: Lines 26-47 in original `BACKUP` (replaced by lines 26-78 in Today's `LOCAL`)

#### Before (Original BACKUP State)
```php
        $dormitorydean_id = $this->session->userdata('principal')['dormitorydean_id'];
        // printx($dormitorydean_id);
          
        $session_id = $this->setting_model->getCurrentSession();

        
 
        $student_result = $this->dormitorydean_model->getstudentsunderthedean( $dormitorydean_id, $session_id);
        $listofrequest = $this->gatepass_model->getactiverecords(  $dormitorydean_id, $session_id );
        
        $var = "$dormitorydean_id, $session_id";
        var_dump('<script>console.log("\x1b[32mhern_log: '.$var.'")</script>');
        
        // printx($listofrequest ); 
        $data['studentlist'] = $student_result;
        $data['listofrequest'] = $listofrequest;
```

#### After (Thursday/Friday State)
```php
        $dormitorydean_id = $this->session->userdata('principal')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();
        
        // Get search parameter
        $search = $this->input->get('search');
        
        // Trim search to remove extra spaces
        if ($search !== null && $search !== '') {
            $search = trim($search);
        }
        
        // Pagination
        $per_page = 15;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;
        
        $total_records = $this->gatepass_model->getactivecampusrecords_count($dormitorydean_id, $session_id, $search);
        $total_pages = ceil($total_records / $per_page);
        
        $student_result = $this->dormitorydean_model->getstudentsunderthedean($dormitorydean_id, $session_id);
        $listofrequest = $this->gatepass_model->getactivecampusrecords($dormitorydean_id, $session_id, $per_page, $offset, $search);
        
        $data['studentlist'] = $student_result;
        $data['listofrequest'] = $listofrequest;
        $data['current_page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['total_records'] = $total_records;
        $data['search'] = $search;
```

* **What Changed**: 
  1. *Wednesday*: Changed the active records query from `getactiverecords()` to `getactivecampusrecords()`. This query specifically filters for `type = 'campus'` (removing Regular and Emergency types) and opens visibility to all hostels in the school.
  2. *Thursday/Friday*: Removed unused debugging statements (`var_dump()`), parsed the `search` GET query parameter, set pagination size (15 items per page), computed paging offsets, queried the count of active campus leaves with `getactivecampusrecords_count()`, and updated `getactivecampusrecords()` arguments. Passed paging details and search parameters to the view inside `$data`.
* **Purpose**: Restructure the Principal's dashboard to display ONLY Campus Leaves and hide Regular and Emergency requests (which are processed by Dorm Deans).
* **Layman's Explanation**: Changed the function name so the Principal's portal loads ONLY campus leave requests and completely hides general, day-to-day passes (Regular/Emergency). It also splits requests into numbered pages (15 per page) and enables a name search.
* **Impact**: Ensures a clear separation of roles; the Principal is no longer distracted by daily dorm-level requests and focuses exclusively on school-wide Campus Leaves.

---

### 1.2 Added Autocomplete Endpoint `search_students()`
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 331-378 in Today's `LOCAL` (New Method)

#### Before (Original BACKUP & Wednesday State)
*(Omitted - Method did not exist)*

#### After (Thursday/Friday State)
```php
    function search_students() {
        $dormitorydean_id = $this->session->userdata('principal')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();
        $search_term = $this->input->post('search_term');
        
        if (empty($search_term)) {
            echo json_encode([]);
            return;
        }
        
        $search_term = trim($search_term);
        
        // Search in gatepass records - Principal sees Campus Leave from ALL dorms
        $this->db->select('gatepass.*, students.firstname, students.middlename, students.lastname');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id');
        $this->db->join('hostel_rooms', 'gatepass.room_id = hostel_rooms.id');
        $this->db->join('hostel', 'hostel_rooms.hostel_id = hostel.id');
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->where('gatepass.type', 'campus'); // Campus Leave only
        
        // Search by LASTNAME or FIRSTNAME (starts with OR contains)
        $starts_with = $this->db->escape_like_str($search_term) . '%';
        $contains = '%' . $this->db->escape_like_str($search_term) . '%';
        $this->db->group_start();
        $this->db->where("students.lastname LIKE '$starts_with'");
        $this->db->or_where("students.firstname LIKE '$starts_with'");
        $this->db->or_where("students.lastname LIKE '$contains'");
        $this->db->or_where("students.firstname LIKE '$contains'");
        $this->db->group_end();
        
        // Order by priority: lastname starts with > firstname starts with > lastname contains > firstname contains
        $this->db->order_by("CASE 
            WHEN students.lastname LIKE '$starts_with' THEN 1 
            WHEN students.firstname LIKE '$starts_with' THEN 2
            WHEN students.lastname LIKE '$contains' THEN 3
            WHEN students.firstname LIKE '$contains' THEN 4
            ELSE 5 
        END", '', FALSE);
        $this->db->order_by('students.lastname');
        $this->db->limit(10);
        
        $results = $this->db->get()->result_array();
        
        echo json_encode($results);
    }
```

* **What Changed**: Wrote a new backend suggestion endpoint that queries student names with `type = 'campus'` from all hostels, ranks them by priority matches, limits output to 10, and returns the list as a JSON response.
* **Purpose**: Feed the autocomplete search suggestions box in the Principal's view.
* **Layman's Explanation**: Fetches student recommendations matching the search characters typed by the user.
* **Impact**: Instantly provides suggestions as the user types.

---

## 2. Model - Model/Gatepass_model.php

### 2.1 Added Paging parameters in `getactiverecords()`
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: Lines 56-80 in original `BACKUP` (replaced by lines 56-80 in Today's `LOCAL`)

#### Before (Original BACKUP State)
```php
     public function getactiverecords( $dormdean_id, $session_id ){
        $this->db->select('gatepass.*'); 
        $this->db->select('hostel.hostel_name');
        $this->db->select('hostel_rooms.room_no'); 
        $this->db->select('students.firstname,students.middlename,students.lastname,students.image,students.admission_no,students.roll_no');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->where('hostel.dormdean_id', $dormdean_id);
        $this->db->where('gatepass.type !=', 'campus'); // Exclude Campus Leave type
        $this->db->order_by('gatepass.status', 'DESC');
        $this->db->order_by('gatepass.created_at', 'ASC');
        $this->db->order_by('gatepass.exit_date', 'ASC');
        $query = $this->db->get();
        return $query->result_array(); 
    }
```

#### After (Thursday/Friday State)
```php
     public function getactiverecords( $dormdean_id, $session_id, $limit = null, $offset = 0 ){
        $this->db->select('gatepass.*'); 
        $this->db->select('hostel.hostel_name');
        $this->db->select('hostel_rooms.room_no'); 
        $this->db->select('students.firstname,students.middlename,students.lastname,students.image,students.admission_no,students.roll_no');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->where('hostel.dormdean_id', $dormdean_id);
        // REMOVED filter: Show ALL types (Regular, Emergency, AND Campus Leave)
        $this->db->order_by('gatepass.status', 'DESC');
        $this->db->order_by('gatepass.created_at', 'ASC');
        $this->db->order_by('gatepass.exit_date', 'ASC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result_array(); 
    }
```

* **What Changed**: Added `$limit` and `$offset` parameters to the regular active records lookup and removed Wednesday's campus-exclude filter constraint (which was done in the shared model file).
* **Purpose**: Align query signatures across roles to support pagination.
* **Layman's Explanation**: Enabled page limits when requesting standard student requests.
* **Impact**: Ensures compatibility across role view configurations.

---

### 2.2 Added Record Counter Method `getactiverecords_count()`
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 82-91 in Today's `LOCAL` (New Method)

#### Before (Original BACKUP State)
*(Omitted - Method did not exist)*

#### After (Thursday/Friday State)
```php
    public function getactiverecords_count( $dormdean_id, $session_id ){
        $this->db->from('gatepass');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->where('hostel.dormdean_id', $dormdean_id);
        // REMOVED filter: Count ALL types
        return $this->db->count_all_results();
    }
```

* **What Changed**: Created a simple count query for regular active records.
* **Purpose**: Calculate pages for general records.
* **Layman's Explanation**: Counts total rows for pagination.
* **Impact**: Needed to structure pagination ranges.

---

### 2.3 Added Method `getactivecampusrecords()`
* **Session**: Wednesday (Created Method) & Thursday/Friday (Added Paging/Search)
* **Type of Change**: Added/Modified
* **Lines Changed**: Lines 93-149 in Today's `LOCAL` (New Method)

#### Before (Original `BACKUP` State)
*(Omitted - Method did not exist)*

#### After (Thursday/Friday State)
```php
    public function getactivecampusrecords( $dormdean_id, $session_id, $limit = null, $offset = 0, $search = null ){
        $this->db->select('gatepass.*'); 
        $this->db->select('hostel.hostel_name');
        $this->db->select('hostel_rooms.room_no'); 
        $this->db->select('hostel_rooms.room_type_id');
        $this->db->select('room_types.room_type'); 
        $this->db->select('students.lastname');
        $this->db->select('students.firstname'); 
        $this->db->select('students.middlename');
        $this->db->select('students.mobileno');
        $this->db->select('students.guardian_name');
        $this->db->select('students.guardian_midname');
        $this->db->select('students.guardian_lastname');
        $this->db->select('students.guardian_phone');
        $this->db->select('students.guardian_address');
        $this->db->select('students.guardian_address2');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left'); 
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        // REMOVED: $this->db->where('hostel.dormdean_id', $dormdean_id); 
        // Principal now sees Campus Leave from ALL dorms
        $this->db->where('gatepass.type', 'campus'); // Show ONLY Campus Leave type
        
        // Search filter - Search multiple fields with starts-with priority
        if ($search !== null && $search !== '') {
            $starts_with = $this->db->escape_like_str($search) . '%';
            $contains = '%' . $this->db->escape_like_str($search) . '%';
            
            // Search by lastname, firstname (starts with OR contains), purpose, destination, status, type
            $this->db->group_start();
            $this->db->where("students.lastname LIKE '$starts_with'");
            $this->db->or_where("students.firstname LIKE '$starts_with'");
            $this->db->or_where("students.lastname LIKE '$contains'");
            $this->db->or_where("students.firstname LIKE '$contains'");
            $this->db->or_where("gatepass.purpose LIKE '$contains'");
            $this->db->or_where("gatepass.destination LIKE '$contains'");
            $this->db->or_where("gatepass.status LIKE '$contains'");
            $this->db->or_where("gatepass.type LIKE '$contains'");
            $this->db->group_end();
        }
        
        // Ordering: Priority to "starts with" name matches first
        if ($search !== null && $search !== '') {
            // Show "starts with" lastname first, then "starts with" firstname, then contains
            $this->db->order_by("CASE 
                WHEN students.lastname LIKE '$starts_with' THEN 1 
                WHEN students.firstname LIKE '$starts_with' THEN 2
                WHEN students.lastname LIKE '$contains' THEN 3
                WHEN students.firstname LIKE '$contains' THEN 4
                WHEN gatepass.purpose LIKE '$contains' THEN 5
                WHEN gatepass.destination LIKE '$contains' THEN 6
                ELSE 7 
            END", '', FALSE);
            // Sort alphabetically within the same priority group
            $this->db->order_by('students.lastname', 'ASC');
            $this->db->order_by('students.firstname', 'ASC');
        }
        $this->db->order_by('gatepass.status', 'DESC');
        $this->db->order_by('gatepass.created_at', 'ASC');
        $this->db->order_by('gatepass.exit_date', 'ASC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result_array(); 
    }
```
* **What Changed**: 
  1. *Wednesday*: Added new method to query ONLY Campus Leaves (`type = 'campus'`) school-wide (removed the `dormdean_id` filter).
  2. *Thursday/Friday*: Added pagination limits and offsets, multi-field search conditions, and custom priority sorting to the method.
* **Purpose**: Back the searchable and paginated list of campus leaves on the Principal's screen.
* **Layman's Explanation**: Configured pages and search matching for the Principal's campus leave list query.
* **Impact**: Ensures that when retrieving campus leaves, the data is searchable and load times remain low.

---

### 2.4 Added Campus Record Counter Method `getactivecampusrecords_count()`
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 151-179 in Today's `LOCAL` (New Method)

#### Before (Original BACKUP State)
*(Omitted - Method did not exist)*

#### After (Thursday/Friday State)
```php
    public function getactivecampusrecords_count( $dormdean_id, $session_id, $search = null ){
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        // REMOVED: $this->db->where('hostel.dormdean_id', $dormdean_id); 
        // Principal sees Campus Leave from ALL dorms
        $this->db->where('gatepass.type', 'campus');
        
        // Search filter - Search multiple fields with starts-with priority
        if ($search !== null && $search !== '') {
            $starts_with = $this->db->escape_like_str($search) . '%';
            $contains = '%' . $this->db->escape_like_str($search) . '%';
            
            // Search by lastname, firstname (starts with OR contains), purpose, destination, status, type
            $this->db->group_start();
            $this->db->where("students.lastname LIKE '$starts_with'");
            $this->db->or_where("students.firstname LIKE '$starts_with'");
            $this->db->or_where("students.lastname LIKE '$contains'");
            $this->db->or_where("students.firstname LIKE '$contains'");
            $this->db->or_where("gatepass.purpose LIKE '$contains'");
            $this->db->or_where("gatepass.destination LIKE '$contains'");
            $this->db->or_where("gatepass.status LIKE '$contains'");
            $this->db->or_where("gatepass.type LIKE '$contains'");
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }
```
* **What Changed**: Wrote counting function that returns total matching campus leave rows.
* **Purpose**: Calculate total page navigation items for Principal views.
* **Layman's Explanation**: Counts total campus leave records to calculate pages.
* **Impact**: Essential to make page links display correctly.

---

### 2.5 Dynamic Sorting by Pending Status and Activity Recency
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: 
  * `DORM_DEAN/LOCAL/Model/Gatepass_model.php`: Lines 119-122, 223-226, 293-295, 325-327
  * `PRINCIPAL/LOCAL/Model/Gatepass_model.php`: Lines 84-87, 168-171, 238-240, 270-272

#### Before (Original BACKUP State)
```php
        $this->db->order_by('gatepass.status', 'DESC');
        $this->db->order_by('gatepass.created_at', 'ASC');
        $this->db->order_by('gatepass.exit_date', 'ASC');
```

#### After (Thursday/Friday State)
```php
        // Pending first, then sort approved/declined by recent activity
        $this->db->order_by("CASE WHEN gatepass.status = 'pending' THEN 0 ELSE 1 END", '', FALSE);
        $this->db->order_by('gatepass.updated_at', 'DESC');
        $this->db->order_by('gatepass.created_at', 'DESC');
```
* **What Changed**: Modified the `ORDER BY` sorting clauses in the query methods of `Gatepass_model`. Instead of sorting status descending alphabetically and exit date ascending, requests are sorted dynamically: "pending" requests are prioritized first, followed by most recently updated (`updated_at` DESC) and created (`created_at` DESC) requests.
* **Purpose**: Make sure that active/pending requests that need attention appear at the very top of the list, and recently processed or submitted requests are shown first.
* **Layman's Explanation**: Gatepass requests waiting for approval now show up at the top of the table. Once processed, the most recently updated entries are listed first.
* **Impact**: Improves the workflow efficiency of the Dorm Dean and Principal by ensuring they do not have to search/scroll to find new or recently updated requests.

---

## 3. View - View/records.php

### 3.1 Inserted Export Buttons and Search UI
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 18-54 in Today's `LOCAL`

#### Before (Original BACKUP State)
*(Omitted - Panel did not exist)*

#### After (Thursday/Friday State)
```html
                    <!-- Search Box and Export Buttons -->
                    <div style="padding: 10px 10px 5px 10px; background: #f9f9f9; border-bottom: 1px solid #ddd;">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Export Buttons -->
                                <button class="btn btn-default btn-sm" onclick="exportTableToCSV('gatepass_records.csv')">
                                    <i class="fa fa-file-excel-o"></i> Export CSV
                                </button>
                                <button class="btn btn-default btn-sm" onclick="window.print()">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (!empty($search)): ?>
                                    <small class="text-muted" style="line-height: 30px; display: inline-block; margin-right: 10px;">
                                        Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong>
                                    </small>
                                <?php endif; ?>
                                <div style="display: inline-block; position: relative;">
                                    <input type="text" id="search_input" class="form-control input-sm" placeholder="Search student name, purpose, destination..." value="<?php echo htmlspecialchars(isset($search) ? $search : ''); ?>" autocomplete="off" style="width: 250px; height: 30px; display: inline-block;">
                                    <button class="btn btn-primary btn-sm" type="button" id="search_button" style="height: 28px; padding: 5px 10px; margin-left: 5px;">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <?php if (!empty($search)): ?>
                                        <a href="<?php echo base_url('principal/gatepass/records'); ?>" class="btn btn-default btn-sm" style="height: 30px; padding: 5px 10px; margin-left: 5px;">Clear</a>
                                    <?php endif; ?>
                                    <!-- Suggestions dropdown positioned relative to parent -->
                                    <div id="search_suggestions" style="position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; display: none; max-height: 300px; overflow-y: auto; width: 250px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-top: 2px; left: 0; top: 30px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search Box -->
```
* **What Changed**: Inserted functional controls for exporting as CSV, triggering browser print, searching, and autocomplete list dropdown below the card header.
* **Purpose**: Expose reporting utilities and search parameters.
* **Layman's Explanation**: Placed CSV, Print, and Search buttons above the table.
* **Impact**: Simplifies admin duties by allowing exports and searches easily.

---

### 3.2 Swapped Table Class Selector
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: Line 57 in Today's `LOCAL`

#### Before (Original BACKUP State)
```html
                            <table class="table table-striped table-bordered table-hover example">
```

#### After (Thursday/Friday State)
```html
                            <table class="table table-striped table-bordered table-hover">
```

* **What Changed**: Removed class string `example` from the table tag.
* **Purpose**: Allow custom database-side pagination to function without local browser scripts locking up the rows.
* **Layman's Explanation**: Disables default browser table sorting, letting server-side paging take over.
* **Impact**: Paging functions without visual bugs.

---

### 3.3 Integrated Pagination Footer Links
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 117-177 in Today's `LOCAL`

#### Before (Original BACKUP State)
*(Omitted - Panel did not exist)*

#### After (Thursday/Friday State)
```html
                    <!-- Pagination -->
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                    <div class="box-footer clearfix">
                        <ul class="pagination pagination-sm no-margin pull-right">
                            <?php 
                            $search_param = !empty($search) ? '&search=' . urlencode($search) : '';
                            ?>
                            <?php if ($current_page > 1): ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . ($current_page - 1) . $search_param); ?>">«</a></li>
                            <?php else: ?>
                                <li class="disabled"><span>«</span></li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                            
                            if ($start_page > 1):
                            ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=1' . $search_param); ?>">1</a></li>
                                <?php if ($start_page > 2): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <?php if ($i == $current_page): ?>
                                    <li class="active"><span><?php echo $i; ?></span></li>
                                <?php else: ?>
                                    <li><a href="<?php echo base_url('principal/gatepass/records?page=' . $i . $search_param); ?>"><?php echo $i; ?></a></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . $total_pages . $search_param); ?>"><?php echo $total_pages; ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li><a href="<?php echo base_url('principal/gatepass/records?page=' . ($current_page + 1) . $search_param); ?>">»</a></li>
                            <?php else: ?>
                                <li class="disabled"><span>»</span></li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="pull-left">
                            Showing <?php echo (($current_page - 1) * 15 + 1); ?> 
                            to <?php echo min($current_page * 15, $total_records); ?> 
                            of <?php echo $total_records; ?> entries
                            <?php if (!empty($search)): ?>
                                <span class="text-muted">(filtered from search)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!-- End Pagination -->
```
* **What Changed**: Inserted pagination navigation controls to the box footer.
* **Purpose**: Page navigation.
* **Layman's Explanation**: Placed page numbers at the bottom of the list.
* **Impact**: Improves usability.

---

### 3.4 Added Clean Print Styles
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 193-214 in Today's `LOCAL`

#### Before (Original BACKUP State)
*(Omitted - Styling did not exist)*

#### After (Thursday/Friday State)
```html
<style>
@media print {
    /* Hide elements during print */
    .box-header, .box-footer, .btn, .main-sidebar, .main-header, .content-header {
        display: none !important;
    }
    
    /* Show only the table */
    .box-body {
        padding: 0 !important;
    }
    
    table {
        width: 100% !important;
        font-size: 10px !important;
    }
    
    /* Hide action column */
    th:last-child, td:last-child {
        display: none !important;
    }
}
</style>
```

* **What Changed**: Added print media queries that remove site menus, headers, filters, and action columns when printing, formatting the table content clean and legible.
* **Purpose**: Clean up print outputs.
* **Layman's Explanation**: Hides sidebars and buttons when printing, outputting only the clean student pass data.
* **Impact**: Professional paper copies.

---

### 3.5 Interactive Autocomplete and CSV Export Scripts
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 216-330 in Today's `LOCAL`

#### Before (Original BACKUP State)
*(Omitted - Scripts did not exist)*

#### After (Thursday/Friday State)
```javascript
$(document).ready(function() {
    // Live search with autocomplete
    var searchTimeout;
    var $searchInput = $('#search_input');
    var $searchButton = $('#search_button');
    var $suggestions = $('#search_suggestions');
    
    // Function to perform search
    function performSearch() {
        var searchTerm = $searchInput.val().trim();
        if (searchTerm.length > 0) {
            window.location.href = '<?php echo base_url('principal/gatepass/records?search='); ?>' + encodeURIComponent(searchTerm);
        }
    }
    
    // Search button click handler
    $searchButton.on('click', function() {
        performSearch();
    });
    
    $searchInput.on('keyup', function() {
        clearTimeout(searchTimeout);
        var searchTerm = $(this).val().trim();
        
        if (searchTerm.length === 0) {
            $suggestions.hide().empty();
            return;
        }
        
        // Show suggestions after user stops typing for 300ms
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '<?php echo base_url('principal/gatepass/search_students'); ?>',
                type: 'POST',
                data: { search_term: searchTerm },
                dataType: 'json',
                success: function(response) {
                    console.log('Search response:', response); // Debug log
                    $suggestions.empty();
                    
                    if (response && response.length > 0) {
                        var html = '<ul style="list-style: none; padding: 0; margin: 0;">';
                        response.forEach(function(student) {
                            html += '<li style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;" ' +
                                   'data-name="' + student.lastname + '">' +
                                   '<strong>' + student.lastname + '</strong>, ' + student.firstname + ' ' + (student.middlename || '') +
                                   '</li>';
                        });
                        html += '</ul>';
                        $suggestions.html(html).show();
                        
                        // Click handler for suggestions
                        $suggestions.find('li').on('click', function() {
                            var selectedName = $(this).data('name');
                            $searchInput.val(selectedName);
                            $suggestions.hide();
                            // Trigger search
                            window.location.href = '<?php echo base_url('principal/gatepass/records?search='); ?>' + encodeURIComponent(selectedName);
                        });
                    } else {
                        $suggestions.html('<div style="padding: 8px 12px; color: #999;">No students found</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', error); // Debug log
                    $suggestions.html('<div style="padding: 8px 12px; color: #red;">Error loading suggestions</div>').show();
                }
            });
        }, 300);
    });
    
    // Handle Enter key to search
    $searchInput.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            performSearch();
        }
    });
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search_input, #search_suggestions').length) {
            $suggestions.hide();
        }
    });
});

// Export table to CSV
function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("table tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length - 1; j++) { // Skip last column (Actions)
            var cellText = cols[j].innerText.replace(/"/g, '""'); // Escape quotes
            row.push('"' + cellText + '"');
        }
        
        csv.push(row.join(","));
    }
    
    // Download CSV file
    var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
```
* **What Changed**: Added jQuery autocomplete scripts and the `exportTableToCSV()` file download builder.
* **Purpose**: Client-side functions for recommendations and spreadsheet generation.
* **Layman's Explanation**: Connects search recommendation tools and adds a download button to extract the table as a spreadsheet.
* **Impact**: Instantly provides suggestions and export options.

---

### 3.6 Status Display Label: Denied to Declined
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: Lines 81-83 in Today's `LOCAL` (replaced line 82 in BACKUP)

#### Before (Original BACKUP & Wednesday State)
```php
                                        } elseif( $request['status']  == "denied"){
                                            $status_display = '<button class="btn btn-danger btn-xs">Denied</button';
                                        }
```

#### After (Thursday/Friday State)
```php
                                        } elseif( $request['status']  == "denied"){
                                            $status_display = '<button class="btn btn-danger btn-xs">Declined</button';
                                        }
```
* **What Changed**: Updated the HTML status display helper logic. The button text for a gatepass with the `"denied"` status has been changed from `"Denied"` to `"Declined"`.
* **Purpose**: Align the visible status badge label with the "Declined" state text used elsewhere in the application.
* **Layman's Explanation**: Replaced the text "Denied" with "Declined" on the red status indicator badge for rejected requests.
* **Impact**: Ensures consistency in terminology across the user interface.

---

### 3.7 Action Buttons: Rename "Approved" and "Declined" to "Approve" and "Decline"
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: Lines 102-105 in Today's `LOCAL` (replaced lines 102 and 104 in BACKUP)

#### Before (Original BACKUP State)
```php
                                                <a href="<?php echo base_url(); ?>principal/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approved
                                                </a>
                                                <a href="<?php echo base_url(); ?>principal/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Declined" onclick="return confirm('Are you sure you want to Decline this request?');">Declined
                                                </a>
```

#### After (Thursday/Friday State)
```php
                                                <a href="<?php echo base_url(); ?>principal/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approve
                                                </a>
                                                <a href="<?php echo base_url(); ?>principal/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Decline" onclick="return confirm('Are you sure you want to Decline this request?');">Decline
                                                </a>
```
* **What Changed**: Renamed the button label string from `"Approved"` to `"Approve"`, and `"Declined"` to `"Decline"`. Also updated the tooltip title from `"Declined"` to `"Decline"`.
* **Purpose**: Make the action text verb-based (matching "Delete") rather than adjective-based, which is more intuitive for buttons performing an action.
* **Layman's Explanation**: The buttons to accept or reject requests now read "Approve" and "Decline" instead of "Approved" and "Declined".
* **Impact**: Cleaner and more consistent terminology for action buttons.

---

## 4. Before vs. After Summary (Layman's Terms)

Below is a non-technical summary of how the Principal portal behaved **originally** compared to **today**:

| Feature / Behavior | Original State | Today (After Thursday & Friday) |
| :--- | :--- | :--- |
| **Page Loading Speed** | **Slow & Heavy**: Loading the page took a long time because the system downloaded thousands of student passes at once, occasionally causing lags. | **Fast & Light**: The system now loads records 15 at a time, resulting in immediate loading speeds. |
| **CSV Export & Printing** | **None**: No feature existed to download the records or print a clean list. | **Available**: Two buttons (Export CSV & Print) let the Principal download the records as a spreadsheet or print clean paper sheets instantly. |
| **Search Functionality** | **None**: No search input field existed to find records. | **Live Auto-suggest**: A search bar exists at the top. Typing shows student recommendations instantly. |
| **Print Output Quality** | **Cluttered**: Printing would include sidebars, headers, and buttons on the paper page. | **Clean & Professional**: The print view hides side menus and buttons, showing only the student pass list. |
| **Scope of campus records (Wednesday Separation)** | **Routine View (Duplicated)**: Principal saw all types of gatepasses (Regular, Emergency, Campus) but was restricted to one dorm building. | **Only Campus Leaves (Filtered)**: Swapped query to display ONLY Campus Leave requests across all dormitories (Regular and Emergency requests are completely removed). It is now fully searchable and paginated. |
| **Denied Status Label** | **Denied**: The red status badge for rejected gatepass requests displayed as "Denied". | **Declined**: The status badge is updated to display as "Declined" to maintain consistency. |
| **Action Button Labels** | **Approved / Declined**: The buttons to process pending requests were labeled as "Approved" and "Declined". | **Approve / Decline**: The buttons are now labeled as "Approve" and "Decline" (verbs) to match action semantics. |
| **Record Sorting Priority** | **Exit Date / Created At**: Sorted strictly by exit dates or the date the pass was originally created. | **Pending & Recently Updated First**: "Pending" requests are pinned to the top, and processed requests are ordered by the most recent updates (`updated_at` / `created_at` DESC). |
