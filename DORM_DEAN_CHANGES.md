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
*(See Gatepass_model.php source code: counts total active records filtering by search parameter)*
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
*(See Gatepass_model.php source code: filters for `type = 'campus'` and removes the `hostel.dormdean_id` constraint, plus paging and search)*
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
*(See Gatepass_model.php count query: counts total active campus leaves filtering by search parameter)*
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
*(See records.php code change)*
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
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/approved_data/<?php echo $request['id']; ?>"class="btn btn-success btn-xs"  data-toggle="tooltip" title="Approve" onclick="return confirm('Are you sure you want to Approve this request?');">Approved
                                                </a>
                                                <a href="<?php echo base_url(); ?>dormitorydean/gatepass/declined_data/<?php echo $request['id']; ?>"class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Declined" onclick="return confirm('Are you sure you want to Decline this request?');">Declined
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
*(See Today's LOCAL View code: renders HTML pagination links that preserve current search settings)*
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
*(See Today's LOCAL View javascript blocks: destroys the DataTable library to avoid paging conflicts, registers suggestions triggers, and hooks click handlers)*
* **What Changed**: Added jQuery autocomplete scripts.
* **Purpose**: Capture typing triggers and query matching results.
* **Layman's Explanation**: Captures user input to fetch matching names dynamically.
* **Impact**: Smooth autocomplete search dropdown.

---

## 4. Before vs. After Summary (Layman's Terms)

Below is a non-technical summary of how the Dorm Dean portal behaved **originally** compared to **today**:

| Feature / Behavior | Original Wednesday State | Today (After Thursday & Friday) |
| :--- | :--- | :--- |
| **Page Loading Speed** | **Slow & Heavy**: Loading the page took a long time because the system downloaded thousands of student passes at once, occasionally causing lags. | **Fast & Light**: The system now loads records 15 at a time, resulting in immediate loading speeds. |
| **Campus Passes Visibility** | **Hidden**: Campus pass requests were completely invisible to the Dorm Dean. | **Visible**: Campus passes are now listed alongside regular requests so the Dorm Dean has a complete view of student whereabouts. |
| **Campus Passes Action** | **N/A** (Since they were completely hidden). | **Read-Only (Removed Action)**: The Dorm Dean can see Campus passes but cannot approve, decline, or delete them. Action buttons are hidden and replaced with the label: *"Managed by Principal"*. |
| **Search Functionality** | **None**: No search input field existed to find records. | **Live Auto-suggest**: A search bar exists at the top. Typing shows student recommendations instantly. |
