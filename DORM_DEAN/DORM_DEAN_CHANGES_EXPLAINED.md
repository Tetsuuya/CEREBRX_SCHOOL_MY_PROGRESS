# Dorm Dean Gatepass System Changes - Complete Guide

**Date:** Today's Session  
**Problem:** Gatepass request list showing Campus Leave type (should only show Regular & Emergency)

---

## Table of Contents
1. [Overview - What We Fixed](#overview)
2. [The Main Problem](#the-main-problem)
3. [File Changes Summary](#file-changes-summary)
4. [Detailed Explanations](#detailed-explanations)
5. [Before vs After Behavior](#before-vs-after-behavior)

---

## Overview

We fixed 1 major issue in the Dorm Dean gatepass management system:

✅ **Campus Leave showing in Dorm Dean list** - Campus Leave requests should only appear in Principal's list, not Dorm Dean's list

---

## The Main Problem

### Problem: Campus Leave Appearing in Wrong List

**What happened:**
- Dorm Dean submits 3 types of gatepasses:
  - Regular Gatepass ✓
  - Campus Leave ✓
  - Emergency Gatepass ✓
- All 3 types appeared in Dorm Dean's Request List
- Campus Leave should only go to Principal for approval

**Why it happened:**
- The database query had no filter for gatepass type
- It showed ALL gatepass requests regardless of type

**How we fixed it:**
- Added a filter: `WHERE type != 'campus'`
- Now Dorm Dean only sees Regular and Emergency types
- Campus Leave requests automatically go to Principal

---

## File Changes Summary

### MODEL FILES (Database Queries)

| File | Method | Lines Changed | What Changed |
|------|--------|---------------|--------------|
| **Gatepass_model.php** | `getactiverecords()` | ~84 | Added filter: `type != 'campus'` |
| **Gatepass_model.php** | `getactivecampusrecords()` | ~91-122 (NEW) | New method for Principal: `type = 'campus'` |

### CONTROLLER FILES (Business Logic)

| File | Method | Lines Changed | What Changed |
|------|--------|---------------|--------------|
| **Gatepass.php** | `records()` | No changes | Still uses `getactiverecords()` |

### VIEW FILES (User Interface)

| File | Section | Lines Changed | What Changed |
|------|---------|---------------|--------------|
| **records.php** | No changes needed | N/A | View displays whatever model returns |

---

## Detailed Explanations

### 1. MODEL: Gatepass_model.php

#### Change #1: Filter Out Campus Leave

**File:** `LOCAL/Model/Gatepass_model.php`  
**Method:** `getactiverecords()`  
**Line:** ~84

**BEFORE:**
```php
public function getactiverecords($dormdean_id, $session_id) {
    $this->db->select('gatepass.*'); 
    $this->db->from('gatepass');
    $this->db->where('gatepass.session_id', $session_id);
    $this->db->where('gatepass.deleted', '0');
    $this->db->where('hostel.dormdean_id', $dormdean_id);
    // NO TYPE FILTER - Shows ALL types
    $query = $this->db->get();
    return $query->result_array(); 
}
```

**AFTER:**
```php
public function getactiverecords($dormdean_id, $session_id) {
    $this->db->select('gatepass.*'); 
    $this->db->from('gatepass');
    $this->db->where('gatepass.session_id', $session_id);
    $this->db->where('gatepass.deleted', '0');
    $this->db->where('hostel.dormdean_id', $dormdean_id);
    $this->db->where('gatepass.type !=', 'campus'); // Exclude Campus Leave type
    $query = $this->db->get();
    return $query->result_array(); 
}
```

**In layman's terms:**
- **Before:** "Show me ALL gatepass requests (regular, campus, emergency)"
- **After:** "Show me only regular and emergency, hide campus requests"

---

#### Change #2: New Method for Campus Leave

**File:** `LOCAL/Model/Gatepass_model.php`  
**Method:** `getactivecampusrecords()` (NEW)  
**Lines:** ~91-122

**NEW CODE:**
```php
public function getactivecampusrecords($dormdean_id, $session_id) {
    $this->db->select('gatepass.*'); 
    $this->db->from('gatepass');
    $this->db->where('gatepass.session_id', $session_id);
    $this->db->where('gatepass.deleted', '0');
    // REMOVED: $this->db->where('hostel.dormdean_id', $dormdean_id); 
    // Principal now sees Campus Leave from ALL dorms
    $this->db->where('gatepass.type', 'campus'); // Show ONLY Campus Leave type
    $query = $this->db->get();
    return $query->result_array(); 
}
```

**What this does:**
- New method specifically for Principal to use
- Shows ONLY Campus Leave type
- Shows Campus Leave from ALL dorms (not just one dorm)

**In layman's terms:**
- This is the "Principal version" of the query
- Shows opposite of what Dorm Dean sees
- Dorm Dean = Regular + Emergency
- Principal = Campus Leave only

---

### 2. CONTROLLER: Gatepass.php

#### No Changes Needed

**File:** `LOCAL/Controller/Gatepass.php`  
**Method:** `records()`  
**Line:** ~33

**EXISTING CODE (No changes):**
```php
public function records() {
    $dormitorydean_id = $this->session->userdata('student')['dormitorydean_id'];
    $session_id = $this->setting_model->getCurrentSession();
    
    $student_result = $this->dormitorydean_model->getstudentsunderthedean($dormitorydean_id, $session_id);
    $listofrequest = $this->gatepass_model->getactiverecords($dormitorydean_id, $session_id);
    
    $data['studentlist'] = $student_result;
    $data['listofrequest'] = $listofrequest;
    
    $this->load->view('layout/dormitorydean/header', $data);
    $this->load->view('dormitorydean/gatepass/records', $data);
    $this->load->view('layout/dormitorydean/footer', $data);
}
```

**Why no changes:**
- Controller still calls `getactiverecords()` method
- The filtering happens inside the model
- Controller doesn't need to know about the filter

**In layman's terms:**
- Controller is like a waiter
- Model is like the kitchen
- Waiter doesn't need to change their order
- Kitchen just prepares the food differently

---

### 3. VIEW: records.php

#### No Changes Needed

**File:** `LOCAL/View/records.php`

**Why no changes:**
- View just displays whatever data it receives
- Since Campus Leave is filtered out in the model, view never sees it
- No UI changes needed

**In layman's terms:**
- View is like a display case
- It shows whatever products are given to it
- If kitchen (model) doesn't send Campus Leave, display doesn't show it

---

## Before vs After Behavior

### Scenario: Dorm Dean Submits Different Gatepass Types

**BEFORE:**

```
Dorm Dean logs in and submits:
1. Student A - Regular Gatepass (going home for weekend)
2. Student B - Campus Leave (going to town for shopping)
3. Student C - Emergency Gatepass (family emergency)

Dorm Dean's Request List shows:
✓ Student A - Regular Gatepass
✓ Student B - Campus Leave  ← Should NOT be here!
✓ Student C - Emergency Gatepass

Principal's Request List shows:
✓ Student B - Campus Leave  ← Correct!
(Plus other campus leave from other dorms)
```

**AFTER:**

```
Dorm Dean logs in and submits:
1. Student A - Regular Gatepass (going home for weekend)
2. Student B - Campus Leave (going to town for shopping)
3. Student C - Emergency Gatepass (family emergency)

Dorm Dean's Request List shows:
✓ Student A - Regular Gatepass
✗ Student B - Campus Leave  ← Filtered out!
✓ Student C - Emergency Gatepass

Principal's Request List shows:
✓ Student B - Campus Leave  ← Correct!
(Plus other campus leave from other dorms)
```

---

## How Data Flows Through The System

### Flow: Loading Gatepass Request List

```
Dorm Dean opens Gatepass → Records page
         ↓
Browser requests gatepass list
         ↓
Controller: Gatepass.php → records()
         ↓
Model: Gatepass_model.php → getactiverecords()
         ↓
Database Query with filter: WHERE type != 'campus'
         ↓
Returns ONLY Regular and Emergency gatepasses
         ↓
View: records.php displays the filtered list
         ↓
Dorm Dean sees only Regular and Emergency requests
```

**Before:** No filter, showed all 3 types  
**After:** Filter applied, shows only 2 types (Regular, Emergency)

---

### Flow: Form Submission (Add Pass)

```
Dorm Dean fills out form:
- Student: John Doe
- Exit Date: 2024-01-15
- Return Date: 2024-01-17
- Type: Campus Leave (radio button)
         ↓
Click "Save" button
         ↓
Controller: Gatepass.php → save_data()
         ↓
Model: Gatepass_model.php → add()
         ↓
Database: INSERT gatepass (type = 'campus')
         ↓
Redirect back to records page
         ↓
Model: getactiverecords() runs with filter
         ↓
Returns only Regular and Emergency (Campus filtered out)
         ↓
View shows: "Student Gatepass Added successfully"
But Campus Leave doesn't appear in Dorm Dean's list!
         ↓
It DOES appear in Principal's list (different method)
```

**Key Point:** 
- Campus Leave gets saved to database
- Dorm Dean can submit it
- But they can't see it in their list
- Only Principal can see and approve it

---

## Real-World Analogies

### 1. Type Filtering (WHERE type != 'campus')

Think of it like a mail sorting room:

**Before (No filter):**
- All mail goes to Dorm Dean's inbox:
  - Regular letters ✓
  - Campus notices ✓
  - Emergency telegrams ✓
- Dorm Dean has to handle everything

**After (With filter):**
- Mail is sorted before delivery:
  - Regular letters → Dorm Dean
  - Campus notices → Principal
  - Emergency telegrams → Dorm Dean
- Each person only sees their relevant mail

---

### 2. Two Different Methods

Think of it like two TV channels:

**Channel 1 (Dorm Dean):** `getactiverecords()`
- Shows: Reality TV (Regular) + News (Emergency)
- Blocks: Movies (Campus Leave)

**Channel 2 (Principal):** `getactivecampusrecords()`
- Shows: Only Movies (Campus Leave)
- Blocks: Reality TV + News

Same cable box (database), different channel filters!

---

### 3. Form Submission vs Display

Think of it like a post office:

**Sending mail (Form submission):**
- Anyone can send any type of letter
- Dorm Dean can create Campus Leave requests
- All letters go into the mail system

**Receiving mail (Display list):**
- Dorm Dean's mailbox = Regular + Emergency only
- Principal's mailbox = Campus Leave only
- The mail sorter (model) decides who gets what

---

## Technical Terms Explained

### WHERE type != 'campus'

**SQL Operator:** `!=` means "not equal to"

**Full query:**
```sql
SELECT * FROM gatepass 
WHERE type != 'campus'
AND deleted = '0'
AND session_id = 19;
```

**In layman's terms:**
- "Give me all gatepasses"
- "But exclude any where type is 'campus'"
- "And not deleted"
- "And from current school year"

**Opposite query for Principal:**
```sql
SELECT * FROM gatepass 
WHERE type = 'campus'
AND deleted = '0'
AND session_id = 19;
```

---

### Model Method Design Pattern

**getactiverecords()** - For Dorm Dean
- Purpose: Show non-campus requests
- Filter: `type != 'campus'`
- Scope: One specific dorm only

**getactivecampusrecords()** - For Principal
- Purpose: Show campus requests
- Filter: `type = 'campus'`
- Scope: ALL dorms (removed dorm filter)

**Why two methods instead of parameters:**
- Clearer code - method name tells you what it does
- Principal needs different scope (all dorms vs one dorm)
- Easier to maintain and test
- Follows Single Responsibility Principle

---

## Database Structure

### gatepass Table (Relevant columns)

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Unique gatepass ID |
| student_id | INT | Which student |
| type | VARCHAR | 'regular', 'campus', or 'emergency' |
| exit_date | DATE | When student leaves |
| return_date | DATE | When student returns |
| purpose | TEXT | Reason for gatepass |
| destination | TEXT | Where student is going |
| status | VARCHAR | 'pending', 'approve', 'denied' |
| deleted | TINYINT | '0' = active, '1' = deleted |
| session_id | INT | School year |
| requestor_id | INT | Who created the request |

### Type Field Values

| Value | Display As | Who Sees It |
|-------|------------|-------------|
| 'regular' | Regular Gatepass | Dorm Dean |
| 'campus' | Campus Leave | Principal |
| 'emergency' | Emergency Gatepass | Dorm Dean |

---

## Testing Checklist

### ✅ Test 1: Dorm Dean Can Submit All Types
- [ ] Login as Dorm Dean
- [ ] Go to Gatepass → Records
- [ ] Click "Add Pass" form
- [ ] Select "Regular Gatepass" → Submit → Success message ✓
- [ ] Select "Campus Leave" → Submit → Success message ✓
- [ ] Select "Emergency Gatepass" → Submit → Success message ✓

### ✅ Test 2: Dorm Dean Only Sees Regular & Emergency
- [ ] After submitting all 3 types above
- [ ] Look at "Request List" on right side
- [ ] Should see: Regular Gatepass ✓
- [ ] Should see: Emergency Gatepass ✓
- [ ] Should NOT see: Campus Leave ✗

### ✅ Test 3: Principal Sees Only Campus Leave
- [ ] Login as Principal
- [ ] Go to Gatepass → Records
- [ ] Look at "Request List"
- [ ] Should see: Campus Leave ✓
- [ ] Should NOT see: Regular Gatepass ✗
- [ ] Should NOT see: Emergency Gatepass ✗

### ✅ Test 4: Campus Leave Shows for ALL Dorms
- [ ] Dorm Dean A (Dorm 1) submits Campus Leave for Student X
- [ ] Dorm Dean B (Dorm 2) submits Campus Leave for Student Y
- [ ] Login as Principal
- [ ] Request List should show:
  - Student X's Campus Leave (from Dorm 1) ✓
  - Student Y's Campus Leave (from Dorm 2) ✓
- [ ] Principal sees campus leave from ALL dorms, not just one

### ✅ Test 5: Status Updates Work Correctly
**For Dorm Dean (Regular/Emergency):**
- [ ] See pending Regular gatepass
- [ ] Click "Approved" button
- [ ] Status changes to "Approved" ✓
- [ ] Click "Declined" button on another request
- [ ] Status changes to "Declined" ✓

**For Principal (Campus Leave):**
- [ ] See pending Campus Leave
- [ ] Click "Approved" button
- [ ] Status changes to "Approved" ✓

---

## File Locations Reference

### Files Changed (LOCAL - Active Version)

```
DORM_DEAN/
│
├── LOCAL/
│   ├── Model/
│   │   └── Gatepass_model.php ........................ [MODIFIED]
│   │       ├── getactiverecords() → Added type filter
│   │       └── getactivecampusrecords() → New method
│   │
│   ├── Controller/
│   │   └── Gatepass.php .............................. [NO CHANGES]
│   │       └── records() → Still uses getactiverecords()
│   │
│   └── View/
│       └── records.php ............................... [NO CHANGES]
│           └── Displays whatever model returns
│
└── BACKUP/
    └── Model/
        └── Gatepass_model.php ........................ [REFERENCE ONLY]
            └── Original version without type filter
```

---

## Troubleshooting Guide

### Problem: Campus Leave Still Shows in Dorm Dean List

**Check:**
1. Are you viewing the LOCAL file or BACKUP file?
2. Clear browser cache (Ctrl+Shift+Del)
3. Check if filter is in code:
```php
$this->db->where('gatepass.type !=', 'campus');
```

**Debug query:**
```sql
-- Run this to see what Dorm Dean should see
SELECT gatepass.*, students.lastname, students.firstname
FROM gatepass
JOIN hostel_rooms ON hostel_rooms.id = gatepass.room_id
JOIN hostel ON hostel.id = hostel_rooms.hostel_id
WHERE gatepass.session_id = 19
  AND gatepass.deleted = '0'
  AND hostel.dormdean_id = 1  -- Replace with actual dorm dean ID
  AND gatepass.type != 'campus';
```

---

### Problem: Principal Sees Nothing

**Check:**
1. Is Principal controller using `getactivecampusrecords()` method?
2. Are there any Campus Leave requests in database?
3. Check if dorm filter was removed from campus query

**Debug query:**
```sql
-- Run this to see what Principal should see
SELECT gatepass.*, students.lastname, students.firstname
FROM gatepass
JOIN hostel_rooms ON hostel_rooms.id = gatepass.room_id
JOIN hostel ON hostel.id = hostel_rooms.hostel_id
WHERE gatepass.session_id = 19
  AND gatepass.deleted = '0'
  AND gatepass.type = 'campus';
  -- NOTE: No dormdean_id filter! Shows ALL dorms
```

---

### Problem: Form Submission Fails

**Check:**
1. Form still has all 3 radio buttons?
2. Radio buttons have correct values: 'regular', 'campus', 'emergency'
3. Check browser console (F12) for JavaScript errors
4. Check server logs for PHP errors

**Verify form code:**
```html
<input type="radio" name="type_of_gatepass" value="regular" checked>
<input type="radio" name="type_of_gatepass" value="campus">
<input type="radio" name="type_of_gatepass" value="emergency">
```

---

## Summary for Non-Technical People

### What Was Wrong:
- Dorm Dean was seeing Campus Leave requests in their list
- Campus Leave should only be approved by Principal, not Dorm Dean
- Both Dorm Dean and Principal were seeing the same requests

### What We Fixed:
- Split the requests into two lists:
  - Dorm Dean sees: Regular + Emergency
  - Principal sees: Campus Leave only
- Campus Leave goes straight to Principal for approval
- Dorm Dean can still create Campus Leave, but it immediately goes to Principal

### How It Works Now:
- Dorm Dean manages day-to-day gatepasses (Regular, Emergency)
- Principal manages special permissions (Campus Leave)
- Clear separation of responsibilities
- Requests automatically route to the right person

---

## Workflow Diagram

### Gatepass Request Flow

```
Student needs to leave campus
         ↓
Dorm Dean creates gatepass request
         ↓
Form has 3 options:
    ├── Regular Gatepass (weekend home visit)
    ├── Campus Leave (go to town/city)
    └── Emergency Gatepass (family emergency)
         ↓
Dorm Dean selects type and submits
         ↓
         ├─ Regular/Emergency ──→ Dorm Dean's List
         │                         └─→ Dorm Dean approves/declines
         │                              └─→ Student notified via SMS
         │
         └─ Campus Leave ──────→ Principal's List
                                └─→ Principal approves/declines
                                     └─→ Student notified via SMS
```

### Database Query Flow

```
Page Load
    ↓
Check user role
    │
    ├── Dorm Dean?
    │   └── Call getactiverecords()
    │       └── Query: WHERE type != 'campus'
    │           └── Returns: Regular + Emergency
    │
    └── Principal?
        └── Call getactivecampusrecords()
            └── Query: WHERE type = 'campus'
                └── Returns: Campus Leave from ALL dorms
```

---

## Deployment Instructions

### Files to Upload to Live Server

**1. Model File (Shared by both roles):**
```
Local: DORM_DEAN/LOCAL/Model/Gatepass_model.php
Server: application/models/Gatepass_model.php
```

**Important:** 
- Both Dorm Dean and Principal use the SAME model file
- Upload either DORM_DEAN/LOCAL or PRINCIPAL/LOCAL (they're identical now)
- One model file contains both methods:
  - `getactiverecords()` for Dorm Dean
  - `getactivecampusrecords()` for Principal

**2. No controller or view changes needed for Dorm Dean**

---

## Version History

### Version 1.0 (Original - BACKUP)
- Dorm Dean saw ALL gatepass types
- No separation between Regular, Campus, Emergency
- Principal and Dorm Dean saw duplicate requests

### Version 2.0 (Current - LOCAL)
- Dorm Dean sees only Regular + Emergency
- Principal sees only Campus Leave
- Campus Leave from ALL dorms goes to Principal
- Clear separation of responsibilities

---

## Related Documentation

- **Principal Changes:** See `PRINCIPAL/PRINCIPAL_CHANGES_EXPLAINED.md`
- **Deployment Guide:** See `DEPLOYMENT_INSTRUCTIONS.md`
- **Cafeteria Example:** See `CAFETERIA/CAFETERIA_CHANGES_EXPLAINED.md` (similar filtering approach)

---

**End of Dorm Dean Changes Documentation**
