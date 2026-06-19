# Dorm Dean System - Complete Code Changes Report (Wednesday to Friday)

This document provides a comprehensive, file-by-file comparison of all the changes made to the **Dorm Dean** system, covering **Wednesday's session** (initial role separation) and **Thursday/Friday's session** (performance pagination, search, and visibility adjustments) relative to the original `BACKUP` state.

All file changes are listed in sequential order from the top of the file to the bottom (by line numbers).

---

## 1. Controller - Controller/Gatepass.php

### 1.1 Pagination and Search Integration in `records()`
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: Lines 26-45 in original `BACKUP` (replaced by lines 26-77 in Today's `LOCAL`)

#### Before (Original BACKUP & Wednesday State)
```php
        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
          
        $session_id = $this->setting_model->getCurrentSession();
      

 
        $student_result = $this->dormitorydean_model->getstudentsunderthedean( $dormitorydean_id, $session_id);
        $listofrequest = $this->gatepass_model->getactiverecords(  $dormitorydean_id, $session_id );
        // printx($listofrequest ); 
        $data['studentlist'] = $student_result;
        $data['listofrequest'] = $listofrequest;
```

#### After (Thursday/Friday State)
```php
        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
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
        
        $total_records = $this->gatepass_model->getactiverecords_count($dormitorydean_id, $session_id, $search);
        $total_pages = ceil($total_records / $per_page);
        
        $student_result = $this->dormitorydean_model->getstudentsunderthedean($dormitorydean_id, $session_id);
        $listofrequest = $this->gatepass_model->getactiverecords($dormitorydean_id, $session_id, $per_page, $offset, $search);
        
        $data['studentlist'] = $student_result;
        $data['listofrequest'] = $listofrequest;
        $data['current_page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['total_records'] = $total_records;
        $data['search'] = $search;
```

* **What Changed**: Added search parameter parsing (getting 'search' from URL GET, trimming whitespace), calculated pagination limits/offsets (15 items per page), retrieved total record count via `getactiverecords_count()`, calculated total pages, updated the `getactiverecords()` method call to accept limit, offset, and search string parameters. All pagination and search variables were passed to the view inside `$data`.
* **Purpose**: Integrate search and database-side pagination to prevent overloading the system when listing student gatepass requests.
* **Layman's Explanation**: Instead of fetching all gatepass requests from the database at once, the system now requests them 15 at a time (like looking at page 1, 2, 3...) and allows filtering them by name.
* **Impact**: Dramatically speeds up loading times for the Dorm Dean's list and allows them to search for a specific student's request.

---

### 1.2 Added Autocomplete Endpoint `search_students()`
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 330-380 in Today's `LOCAL` (New Method)

#### Before (Original BACKUP & Wednesday State)
*(Omitted - Method did not exist)*

#### After (Thursday/Friday State)
```php
    // AJAX search for students with live suggestions
    function search_students() {
        $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
        $session_id = $this->setting_model->getCurrentSession();
        $search_term = $this->input->post('search_term');
        
        if (empty($search_term)) {
            echo json_encode([]);
            return;
        }
        
        $search_term = trim($search_term);
        
        // Search in gatepass records for this dorm dean
        $this->db->select('gatepass.*, students.firstname, students.middlename, students.lastname');
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id');
        $this->db->join('hostel_rooms', 'gatepass.room_id = hostel_rooms.id');
        $this->db->join('hostel', 'hostel_rooms.hostel_id = hostel.id');
        $this->db->where('hostel.dormdean_id', $dormitorydean_id);
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        
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

* **What Changed**: Created a new server endpoint that listens for live user inputs via AJAX, searches for matches in the student database, ranks results using a custom sorting priority (last names starting with search term first, first names starting with search term second, etc.), limits output to 10 entries, and returns them as a JSON response.
* **Purpose**: Back the live suggestion dropdown element in the search bar.
* **Layman's Explanation**: Added a feature that allows the system to look up matching student names dynamically as the user types and show a clickable list of matches.
* **Impact**: Enhances user experience by removing the need to type out full names to find a record.

---

## 2. Model - Model/Gatepass_model.php

### 2.1 Reverting Campus Filter, Adding Paging/Search to `getactiverecords()`
* **Session**: Wednesday (Added Campus Filter) & Thursday/Friday (Reverted Campus Filter, Added Paging/Search)
* **Type of Change**: Modified
* **Lines Changed**: Lines 59-71 in original `BACKUP` (replaced by lines 59-113 in Today's `LOCAL`)

#### Before (Original `BACKUP` State)
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
     public function getactiverecords( $dormdean_id, $session_id, $limit = null, $offset = 0, $search = null ){
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
  1. *Wednesday*: Added `$this->db->where('gatepass.type !=', 'campus');` to hide Campus Leaves from Dorm Dean.
  2. *Thursday/Friday*: Reverted that exclude check (Campus Leaves now display again). Added `$limit`, `$offset`, and `$search` parameters. Implemented multi-field `LIKE` filters (student name, purpose, destination, status, type) with priority ordering (starts-with first).
* **Purpose**: Bring Campus Leave records back into the list as read-only (instead of hidden), and implement database-level pagination/searching.
* **Layman's Explanation**: The system now loads requests 15 at a time, enables multi-field searching, and restores Campus Leaves to the list (which Wednesday's change had completely hidden).
* **Impact**: Ensures rapid page load speed for the database query and displays Campus Leaves.

---

### 2.2 Added Record Counter Method `getactiverecords_count()`
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 115-143 in Today's `LOCAL` (New Method)

#### Before (Original BACKUP & Wednesday State)
*(Omitted - Method did not exist)*

#### After (Thursday/Friday State)
```php
    public function getactiverecords_count( $dormdean_id, $session_id, $search = null ){
        $this->db->from('gatepass');
        $this->db->join('students', 'gatepass.student_id = students.id ', 'left');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
        $this->db->where('gatepass.session_id', $session_id);
        $this->db->where('gatepass.deleted', '0');
        $this->db->where('hostel.dormdean_id', $dormdean_id);
        // REMOVED filter: Count ALL types
        
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
* **What Changed**: Implemented a matching method that counts the total quantity of active requests (applying search filters) without loading the full row contents.
* **Purpose**: Provide total match counts to the pagination system to compute total pages.
* **Layman's Explanation**: Added a helper that counts matching database rows so the system knows how many total pages to show.
* **Impact**: Essential to calculate the pagination navigation controls correctly.

---

### 2.3 Added Method `getactivecampusrecords()`
* **Session**: Wednesday (Created Method) & Thursday/Friday (Added Paging/Search)
* **Type of Change**: Added/Modified
* **Lines Changed**: Lines 145-200 in Today's `LOCAL` (New Method)

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
* **Lines Changed**: Lines 202-230 in Today's `LOCAL` (New Method)

#### Before (Original BACKUP & Wednesday State)
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
* **What Changed**: Created a matching counter method for the Campus Leave query.
* **Purpose**: Calculate total pages specifically for the Campus Leave pagination bar.
* **Layman's Explanation**: Counts total matching campus leaves to calculate the page count.
* **Impact**: Essential for rendering page links in the Principal's view.

---

## 3. View - View/records.php

### 3.1 Inserted Search Bar Interface
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 105-131 in Today's `LOCAL`

#### Before (Original BACKUP & Wednesday State)
*(Omitted - Panel did not exist)*

#### After (Thursday/Friday State)
```html
                    <!-- Search Box Below Title -->
                    <div style="padding: 10px 10px 5px 10px; background: #f9f9f9; border-bottom: 1px solid #ddd;">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($search)): ?>
                                    <small class="text-muted" style="line-height: 30px;">Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <div style="display: inline-block;">
                                    <input type="text" id="search_input" class="form-control input-sm" placeholder="Search by lastname..." value="<?php echo htmlspecialchars(isset($search) ? $search : ''); ?>" autocomplete="off" style="width: 250px; height: 30px; display: inline-block;">
                                    <button class="btn btn-primary btn-sm" type="button" id="search_button" style="height: 28px; padding: 5px 10px; margin-left: 5px;">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <?php if (!empty($search)): ?>
                                        <a href="<?php echo base_url('dormitorydean/gatepass/records'); ?>" class="btn btn-default btn-sm" style="height: 30px; padding: 5px 10px; margin-left: 5px;">Clear</a>
                                    <?php endif; ?>
                                </div>
                                <div id="search_suggestions" style="position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; display: none; max-height: 300px; overflow-y: auto; width: 250px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-top: 2px; right: 10px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search Box -->
```
* **What Changed**: Inserted search layout controls (input element, search button, clear button, and suggestions list dropdown) below the card header.
* **Purpose**: Collect search queries.
* **Layman's Explanation**: Placed a search bar at the top of the table.
* **Impact**: Improves usability when searching.

---

### 3.2 Feature: Removal of Dorm Dean Actions on Campus Leaves
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: Lines 148-154 in BACKUP (replaced by lines 173-195 in Today's `LOCAL`)

#### Before (Original BACKUP & Wednesday State)
```php
                                                <?php if( $request['status'] == "pending"){  ?>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/delete_data/<?php echo $request['id']; ?>"class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to Delete this request?');">Delete
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approved
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Declined" onclick="return confirm('Are you sure you want to Decline this request?');">Declined
                                                </a>
                                                <?php }  ?>
```

#### After (Thursday/Friday State)
```php
                                                <?php 
                                                // Show actions ONLY for Regular and Emergency (NOT for Campus Leave)
                                                if( $request['status'] == "pending" && $request['type'] != "campus" ){  
                                                ?>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/delete_data/<?php echo $request['id']; ?>"class="btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('Are you sure you want to Delete this request?');">Delete
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approve
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Decline" onclick="return confirm('Are you sure you want to Decline this request?');">Decline
                                                </a>
                                                <?php 
                                                } elseif( $request['type'] == "campus" ) {
                                                    // Campus Leave: Read-only, no actions
                                                    echo '<span class="text-muted"><i>Managed by Principal</i></span>';
                                                }
                                                ?>
```

* **What Changed**: Restricted action links (Approve/Decline/Delete) so they do not show if the type is `'campus'`. Added an `elseif` block displaying `"Managed by Principal"` as plain text instead.
* **Purpose**: Prevent Dorm Deans from managing Principal-level Campus Leaves.
* **Layman's Explanation**: Hides the Delete, Approve, and Decline buttons for campus leave requests in the Dorm Dean's screen, replacing them with a message saying "Managed by Principal".
* **Impact**: Ensures that only the Principal can approve or decline Campus Leaves, while still allowing the Dorm Dean to view them.

---

### 3.3 Integrated Pagination Footer Links
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 202-262 in Today's `LOCAL`

#### Before (Original BACKUP & Wednesday State)
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
                                <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=' . ($current_page - 1) . $search_param); ?>">«</a></li>
                            <?php else: ?>
                                <li class="disabled"><span>«</span></li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                            
                            if ($start_page > 1):
                            ?>
                                <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=1' . $search_param); ?>">1</a></li>
                                <?php if ($start_page > 2): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <?php if ($i == $current_page): ?>
                                    <li class="active"><span><?php echo $i; ?></span></li>
                                <?php else: ?>
                                    <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=' . $i . $search_param); ?>"><?php echo $i; ?></a></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="disabled"><span>...</span></li>
                                <?php endif; ?>
                                <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=' . $total_pages . $search_param); ?>"><?php echo $total_pages; ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li><a href="<?php echo base_url('dormitorydean/gatepass/records?page=' . ($current_page + 1) . $search_param); ?>">»</a></li>
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

### 3.4 Interactive Autocomplete Scripts
* **Session**: Thursday/Friday
* **Type of Change**: Added
* **Lines Changed**: Lines 278-378 in Today's `LOCAL`

#### Before (Original BACKUP & Wednesday State)
*(Omitted - Scripts did not exist)*

#### After (Thursday/Friday State)
```javascript
// Destroy DataTables for this specific table to use server-side pagination
$(document).ready(function() {
    var table = $('.example');
    if ($.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy();
    }
    
    // Live search with autocomplete
    var searchTimeout;
    var $searchInput = $('#search_input');
    var $searchButton = $('#search_button');
    var $suggestions = $('#search_suggestions');
    
    // Function to perform search
    function performSearch() {
        var searchTerm = $searchInput.val().trim();
        if (searchTerm.length > 0) {
            window.location.href = '<?php echo base_url('dormitorydean/gatepass/records?search='); ?>' + encodeURIComponent(searchTerm);
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
                url: '<?php echo base_url('dormitorydean/gatepass/search_students'); ?>',
                type: 'POST',
                data: { search_term: searchTerm },
                dataType: 'json',
                success: function(response) {
                    $suggestions.empty();
                    
                    if (response && response.length > 0) {
                        var html = '<ul class="list-group" style="margin-bottom: 0;">';
                        $.each(response, function(index, student) {
                            var fullname = student.lastname + ', ' + student.firstname + ' ' + student.middlename;
                            var exitDate = student.exit_date || 'N/A';
                            var status = student.status || 'N/A';
                            var type = student.type || 'N/A';
                            
                            html += '<li class="list-group-item" style="cursor: pointer; padding: 10px;" data-name="' + student.lastname + '">';
                            html += '<strong>' + fullname + '</strong><br>';
                            html += '<small>Exit: ' + exitDate + ' | Status: ' + status + ' | Type: ' + type + '</small>';
                            html += '</li>';
                        });
                        html += '</ul>';
                        
                        $suggestions.html(html).show();
                        
                        // Handle suggestion click
                        $suggestions.find('li').on('click', function() {
                            var selectedName = $(this).data('name');
                            $searchInput.val(selectedName);
                            $suggestions.hide();
                            // Trigger search
                            window.location.href = '<?php echo base_url('dormitorydean/gatepass/records?search='); ?>' + encodeURIComponent(selectedName);
                        });
                    } else {
                        $suggestions.html('<div class="alert alert-info" style="margin: 5px;">No students found</div>').show();
                    }
                },
                error: function() {
                    $suggestions.html('<div class="alert alert-danger" style="margin: 5px;">Error loading suggestions</div>').show();
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
```
* **What Changed**: Added jQuery autocomplete scripts.
* **Purpose**: Capture typing triggers and query matching results.
* **Layman's Explanation**: Captures user input to fetch matching names dynamically.
* **Impact**: Smooth autocomplete search dropdown.

---

### 3.5 Status Display Label: Denied to Declined
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: Lines 158-160 in Today's `LOCAL` (replaced line 159 in BACKUP)

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

### 3.6 Action Buttons: Rename "Approved" and "Declined" to "Approve" and "Decline"
* **Session**: Thursday/Friday
* **Type of Change**: Modified
* **Lines Changed**: Lines 182-185 in Today's `LOCAL` (replaced lines 182 and 184 in BACKUP)

#### Before (Original BACKUP State)
```php
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approved
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Declined" onclick="return confirm('Are you sure you want to Decline this request?');">Declined
                                                </a>
```

#### After (Thursday/Friday State)
```php
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approve
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Decline" onclick="return confirm('Are you sure you want to Decline this request?');">Decline
                                                </a>
```
* **What Changed**: Renamed the button label string from `"Approved"` to `"Approve"`, and `"Declined"` to `"Decline"`. Also updated the tooltip title from `"Declined"` to `"Decline"`.
* **Purpose**: Make the action text verb-based (matching "Delete") rather than adjective-based, which is more intuitive for buttons performing an action.
* **Layman's Explanation**: The buttons to accept or reject requests now read "Approve" and "Decline" instead of "Approved" and "Declined".
* **Impact**: Cleaner and more consistent terminology for action buttons.

---

## 4. Before vs. After Summary (Layman's Terms)

Below is a non-technical summary of how the Dorm Dean portal behaved **originally** compared to **today**:

| Feature / Behavior | Original Wednesday State | Today (After Thursday & Friday) |
| :--- | :--- | :--- |
| **Page Loading Speed** | **Slow & Heavy**: Loading the page took a long time because the system downloaded thousands of student passes at once, occasionally causing lags. | **Fast & Light**: The system now loads records 15 at a time, resulting in immediate loading speeds. |
| **Campus Passes Visibility** | **Hidden**: Campus pass requests were completely invisible to the Dorm Dean. | **Visible**: Campus passes are now listed alongside regular requests so the Dorm Dean has a complete view of student whereabouts. |
| **Campus Passes Action** | **N/A** (Since they were completely hidden). | **Read-Only (Removed Action)**: The Dorm Dean can see Campus passes but cannot approve, decline, or delete them. Action buttons are hidden and replaced with the label: *"Managed by Principal"*. |
| **Search Functionality** | **None**: No search input field existed to find records. | **Live Auto-suggest**: A search bar exists at the top. Typing shows student recommendations instantly. |
| **Denied Status Label** | **Denied**: The red status badge for rejected gatepass requests displayed as "Denied". | **Declined**: The status badge is updated to display as "Declined" to maintain consistency. |
| **Action Button Labels** | **Approved / Declined**: The buttons to process pending requests were labeled as "Approved" and "Declined". | **Approve / Decline**: The buttons are now labeled as "Approve" and "Decline" (verbs) to match action semantics. |
