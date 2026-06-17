# Principal Gatepass System Changes - Complete Guide

**Date:** Today's Session  
**Problem:** Principal needs to see ALL Campus Leave requests from ALL dorms, not just one dorm

---

## Table of Contents
1. [Overview - What We Fixed](#overview)
2. [The Main Problem](#the-main-problem)
3. [File Changes Summary](#file-changes-summary)
4. [Detailed Explanations](#detailed-explanations)
5. [Before vs After Behavior](#before-vs-after-behavior)

---

## Overview

We fixed 2 major issues in the Principal gatepass management system:

1. ✅ **Wrong gatepass types showing** - Principal was seeing Regular and Emergency (should only see Campus Leave)
2. ✅ **Limited scope** - Principal was only seeing Campus Leave from ONE dorm (should see ALL dorms)

---

## The Main Problem

### Problem #1: Seeing Wrong Gatepass Types

**What happened:**
- Principal's Request List showed:
  - Regular Gatepass ✓
  - Campus Leave ✓
  - Emergency Gatepass ✓
- Should only show Campus Leave (the others are handled by Dorm Deans)

**Why it happened:**
- Controller was calling `getactiverecords()` method (Dorm Dean's method)
- This method doesn't filter by type

**How we fixed it:**
- Created new method: `getactivecampusrecords()`
- Changed Principal controller to use the new method
- New method filters: `WHERE type = 'campus'`

---

### Problem #2: Only Seeing One Dorm's Requests

**What happened:**
- School has 3 dorms (Dorm A, B, C)
- Each dorm has a Dorm Dean
- Principal could only see Campus Leave from ONE dorm
- Campus Leave from other dorms were invisible

**Why it happened:**
- Query had filter: `WHERE hostel.dormdean_id = Principal's ID`
- This limited results to one specific dorm

**How we fixed it:**
- Removed the dorm filter from `getactivecampusrecords()` method
- Now query shows Campus Leave from ALL dorms
- Principal has school-wide visibility

---

## File Changes Summary

### MODEL FILES (Database Queries)

| File | Method | Lines Changed | What Changed |
|------|--------|---------------|--------------|
| **Gatepass_model.php** | `getactiverecords()` | ~84 | Added filter: `type != 'campus'` (for Dorm Dean) |
| **Gatepass_model.php** | `getactivecampusrecords()` | ~91-122 (NEW) | New method: `type = 'campus'`, no dorm filter |

### CONTROLLER FILES (Business Logic)

| File | Method | Lines Changed | What Changed |
|------|--------|---------------|--------------|
| **Gatepass.php** | `records()` | ~35 | Changed to use `getactivecampusrecords()` |

### VIEW FILES (User Interface)

| File | Section | Lines Changed | What Changed |
|------|---------|---------------|--------------|
| **records.php** | No changes needed | N/A | View displays whatever model returns |

---

## Detailed Explanations

### 1. MODEL: Gatepass_model.php

#### Change: New Method for Principal

**File:** `LOCAL/Model/Gatepass_model.php`  
**Method:** `getactivecampusrecords()` (NEW)  
**Lines:** ~91-122

**NEW CODE:**
```php
public function getactivecampusrecords($dormdean_id, $session_id) {
    $this->db->select('gatepass.*'); 
    $this->db->select('hostel.hostel_name');
    $this->db->select('hostel_rooms.room_no'); 
    $this->db->select('students.lastname');
    $this->db->select('students.firstname'); 
    $this->db->from('gatepass');
    $this->db->join('students', 'gatepass.student_id = students.id ', 'left'); 
    $this->db->join('hostel_rooms', 'hostel_rooms.id = gatepass.room_id', 'left'); 
    $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');  
    $this->db->where('gatepass.session_id', $session_id);
    $this->db->where('gatepass.deleted', '0');
    // REMOVED: $this->db->where('hostel.dormdean_id', $dormdean_id); 
    // Principal now sees Campus Leave from ALL dorms
    $this->db->where('gatepass.type', 'campus'); // Show ONLY Campus Leave type
    $this->db->order_by('gatepass.created_at', 'ASC');
    $query = $this->db->get();
    return $query->result_array(); 
}
```

**Key differences from Dorm Dean version:**

| Feature | Dorm Dean (`getactiverecords`) | Principal (`getactivecampusrecords`) |
|---------|--------------------------------|--------------------------------------|
| Type filter | `type != 'campus'` (exclude) | `type = 'campus'` (only show) |
| Dorm scope | `dormdean_id = X` (one dorm) | No dorm filter (all dorms) |
| Purpose | Day-to-day gatepasses | Special permission requests |

**In layman's terms:**
- **Dorm Dean method:** "Show me Regular and Emergency from my dorm only"
- **Principal method:** "Show me Campus Leave from the entire school"

---

### 2. CONTROLLER: Gatepass.php

#### Change: Use New Model Method

**File:** `LOCAL/Controller/Gatepass.php`  
**Method:** `records()`  
**Line:** ~35

**BEFORE:**
```php
public function records() {
    $dormitorydean_id = $this->session->userdata('principal')['dormitorydean_id'];
    $session_id = $this->setting_model->getCurrentSession();
    
    $student_result = $this->dormitorydean_model->getstudentsunderthedean($dormitorydean_id, $session_id);
    $listofrequest = $this->gatepass_model->getactiverecords($dormitorydean_id, $session_id);
    // ^ Using Dorm Dean's method - WRONG!
    
    $data['studentlist'] = $student_result;
    $data['listofrequest'] = $listofrequest;
    
    $this->load->view('layout/principal/header', $data);
    $this->load->view('principal/gatepass/records', $data);
    $this->load->view('layout/principal/footer', $data);
}
```

**AFTER:**
```php
public function records() {
    $dormitorydean_id = $this->session->userdata('principal')['dormitorydean_id'];
    $session_id = $this->setting_model->getCurrentSession();
    
    $student_result = $this->dormitorydean_model->getstudentsunderthedean($dormitorydean_id, $session_id);
    $listofrequest = $this->gatepass_model->getactivecampusrecords($dormitorydean_id, $session_id);
    // ^ Using Principal's method - CORRECT!
    
    $data['studentlist'] = $student_result;
    $data['listofrequest'] = $listofrequest;
    
    $this->load->view('layout/principal/header', $data);
    $this->load->view('principal/gatepass/records', $data);
    $this->load->view('layout/principal/footer', $data);
}
```

**What changed:**
- Line ~35: `getactiverecords()` → `getactivecampusrecords()`
- One word change, big difference in results!

**In layman's terms:**
- **Before:** Using wrong phone number (calling Dorm Dean's method)
- **After:** Using correct phone number (calling Principal's method)

---

### 3. VIEW: records.php

#### No Changes Needed

**File:** `LOCAL/View/records.php`

**Why no changes:**
- View is "display only" - shows whatever data it receives
- Since we changed what data is fetched (in model/controller), view automatically shows correct data
- No HTML or JavaScript changes needed

**In layman's terms:**
- View is like a TV screen
- Model/Controller is like the cable box choosing the channel
- We changed the channel in the cable box
- TV screen now shows the correct channel automatically

---

## Before vs After Behavior

### Scenario 1: Multiple Dorms Submit Campus Leave

**BEFORE:**

```
School has 3 dorms:
├── Dorm A (Dorm Dean A)
│   └── Student X submits Campus Leave
├── Dorm B (Dorm Dean B)
│   └── Student Y submits Campus Leave
└── Dorm C (Dorm Dean C)
    └── Student Z submits Campus Leave

Principal logs in (assigned to Dorm A)
Request List shows:
✓ Student X's Campus Leave (from Dorm A)
✗ Student Y's Campus Leave (from Dorm B) ← MISSING!
✗ Student Z's Campus Leave (from Dorm C) ← MISSING!
✓ Regular/Emergency requests from Dorm A ← WRONG TYPE!

Principal can only manage Dorm A's Campus Leave
Students in Dorm B and C can't get Campus Leave approved
```

**AFTER:**

```
School has 3 dorms:
├── Dorm A (Dorm Dean A)
│   └── Student X submits Campus Leave
├── Dorm B (Dorm Dean B)
│   └── Student Y submits Campus Leave
└── Dorm C (Dorm Dean C)
    └── Student Z submits Campus Leave

Principal logs in
Request List shows:
✓ Student X's Campus Leave (from Dorm A)
✓ Student Y's Campus Leave (from Dorm B) ← NOW VISIBLE!
✓ Student Z's Campus Leave (from Dorm C) ← NOW VISIBLE!
✗ No Regular/Emergency requests ← CORRECT!

Principal can manage ALL Campus Leave requests school-wide
All students can get Campus Leave approved by Principal
```

---

### Scenario 2: Principal Tries to Approve Different Types

**BEFORE:**

```
Principal's Request List shows:
1. Student A - Regular Gatepass (Pending)
2. Student B - Campus Leave (Pending)
3. Student C - Emergency Gatepass (Pending)
4. Student D - Campus Leave (Pending)

Principal clicks "Approved" on Regular Gatepass
→ Works, but Principal shouldn't be handling this
→ Overlap with Dorm Dean's responsibilities
→ Confusion about who approves what
```

**AFTER:**

```
Principal's Request List shows:
1. Student B - Campus Leave (Pending)
2. Student D - Campus Leave (Pending)

Principal clicks "Approved" on Campus Leave
→ Works perfectly
→ Clear separation: Principal handles ONLY Campus Leave
→ Dorm Deans handle Regular and Emergency
→ No confusion or overlap
```

---

## How Data Flows Through The System

### Flow: Principal Views Campus Leave Requests

```
Principal logs in and opens Gatepass → Records page
         ↓
Browser requests gatepass list
         ↓
Controller: Gatepass.php → records()
         ↓
Gets Principal's ID from session (not actually used for filtering!)
         ↓
Model: Gatepass_model.php → getactivecampusrecords()
         ↓
Database Query:
    SELECT gatepass.*, hostel.hostel_name, students.*
    FROM gatepass
    JOIN students, hostel_rooms, hostel
    WHERE session_id = 19
      AND deleted = '0'
      AND type = 'campus'
    -- NO DORM FILTER!
         ↓
Returns ALL Campus Leave from ALL dorms
         ↓
View: records.php displays the complete list
         ↓
Principal sees Campus Leave from entire school
```

**Key Point:**  
Even though Principal has a `dormitorydean_id` in their account, we don't use it for filtering Campus Leave. This gives Principal school-wide visibility.

---

### Flow: Comparison with Dorm Dean

```
DORM DEAN FLOW:
    Controller → getactiverecords()
        ↓
    WHERE type != 'campus'
    AND dormdean_id = Dean's ID
        ↓
    Returns: Regular + Emergency from ONE dorm
        ↓
    Dorm Dean sees: Limited scope, specific types

PRINCIPAL FLOW:
    Controller → getactivecampusrecords()
        ↓
    WHERE type = 'campus'
    (no dormdean_id filter)
        ↓
    Returns: Campus Leave from ALL dorms
        ↓
    Principal sees: School-wide scope, one type
```

---

## Real-World Analogies

### 1. Dorm Filter Removal

Think of it like security camera access:

**Before (With dorm filter):**
- Principal can view: Building A's cameras only
- Buildings B and C are blind spots
- Campus Leave in other buildings invisible

**After (No dorm filter):**
- Principal can view: All campus cameras
- Buildings A, B, and C all visible
- Complete visibility across campus

---

### 2. Method Change (getactiverecords → getactivecampusrecords)

Think of it like calling different departments:

**Before (wrong method):**
- Principal dials: Local Office (Dorm Dean's line)
- Gets: Day-to-day requests from one building
- Misses: Special permission requests from campus

**After (correct method):**
- Principal dials: Central Administration (Principal's line)
- Gets: Special permissions from entire campus
- Correct: School-wide oversight

---

### 3. Type Filtering

Think of it like email folders:

**Dorm Dean's Inbox:**
- Shows: "Routine" and "Urgent" emails
- Hides: "Requires Admin Approval" emails
- Those go straight to Principal

**Principal's Inbox:**
- Shows: Only "Requires Admin Approval" emails
- Hides: "Routine" and "Urgent" emails
- Those stay with Dorm Deans

Same email server (database), different folder filters!

---

## Technical Terms Explained

### WHERE Clause Comparison

**Dorm Dean Query:**
```sql
SELECT * FROM gatepass 
WHERE type != 'campus'          -- Exclude Campus Leave
  AND hostel.dormdean_id = 1    -- Only this dorm
  AND deleted = '0'
  AND session_id = 19;
```

**Principal Query:**
```sql
SELECT * FROM gatepass 
WHERE type = 'campus'            -- Only Campus Leave
  -- NO dormdean_id filter!     -- All dorms
  AND deleted = '0'
  AND session_id = 19;
```

**Key differences:**
1. Type filter: `!=` vs `=` (opposite logic)
2. Scope filter: Present vs Absent (one dorm vs all dorms)

---

### Method Naming Convention

**getactiverecords():**
- "active" = not deleted
- "records" = general gatepasses
- Used by: Dorm Dean
- Returns: Regular + Emergency from one dorm

**getactivecampusrecords():**
- "active" = not deleted
- "campus" = specifically Campus Leave type
- "records" = gatepass records
- Used by: Principal
- Returns: Campus Leave from all dorms

**Why not use parameters?**
```php
// Could do this:
getactiverecords($dormdean_id, $session_id, $type_filter, $scope)

// But this is clearer:
getactiverecords()        // For Dorm Dean
getactivecampusrecords()  // For Principal
```

Benefits:
- Self-documenting code
- Harder to call wrong method by mistake
- Easier to test
- Clear intent

---

## Database Structure

### gatepass Table (Key columns)

| Column | Type | Description | Example |
|--------|------|-------------|---------|
| id | INT | Unique ID | 142 |
| student_id | INT | Which student | 89 |
| room_id | INT | Student's room | 23 |
| type | VARCHAR | Request type | 'campus' |
| exit_date | DATE | Leave date | 2024-01-15 |
| return_date | DATE | Return date | 2024-01-17 |
| purpose | TEXT | Reason | 'Buy school supplies' |
| destination | TEXT | Where going | 'Town Center' |
| status | VARCHAR | Approval status | 'pending' |
| session_id | INT | School year | 19 |
| requestor_id | INT | Who created it | 5 (Dorm Dean) |
| deleted | TINYINT | Soft delete flag | 0 |

### hostel Table (Links rooms to dorms)

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Hostel/Dorm ID |
| hostel_name | VARCHAR | Dorm name ('Dorm A') |
| dormdean_id | INT | Assigned Dorm Dean |

### Relationship Chain

```
gatepass → hostel_rooms → hostel → staff (Dorm Dean)

Example:
Gatepass ID 142
  → Room ID 23
    → Hostel ID 2 (Dorm B)
      → Dorm Dean ID 5 (Dean Smith)
```

**Before:** Principal query stopped at Dorm Dean ID (filtered to one dorm)  
**After:** Principal query ignores Dorm Dean ID (shows all dorms)

---

## Testing Checklist

### ✅ Test 1: Principal Sees Only Campus Leave
- [ ] Login as Principal
- [ ] Go to Gatepass → Records
- [ ] Request List should show ONLY Campus Leave
- [ ] Should NOT show Regular Gatepass
- [ ] Should NOT show Emergency Gatepass

### ✅ Test 2: Principal Sees ALL Dorms
- [ ] Have Dorm Dean A submit Campus Leave for Student X
- [ ] Have Dorm Dean B submit Campus Leave for Student Y
- [ ] Have Dorm Dean C submit Campus Leave for Student Z
- [ ] Login as Principal
- [ ] Request List should show:
  - Student X (from Dorm A) ✓
  - Student Y (from Dorm B) ✓
  - Student Z (from Dorm C) ✓

### ✅ Test 3: Dorm Deans Don't See Campus Leave
- [ ] Login as Dorm Dean A
- [ ] Submit Campus Leave for Student X
- [ ] Check Request List
- [ ] Campus Leave should NOT appear in Dorm Dean's list
- [ ] Login as Principal
- [ ] Campus Leave SHOULD appear in Principal's list

### ✅ Test 4: Approval Workflow
- [ ] Dorm Dean submits Campus Leave (goes to Principal)
- [ ] Principal sees request with status "Pending"
- [ ] Principal clicks "Approved"
- [ ] Status changes to "Approved"
- [ ] SMS sent to student and guardian
- [ ] Request moves to "Approved" section

### ✅ Test 5: Multiple Principals (if applicable)
- [ ] If school has multiple Principal accounts
- [ ] Login as Principal A
- [ ] Should see Campus Leave from ALL dorms
- [ ] Login as Principal B
- [ ] Should see same Campus Leave requests
- [ ] All Principals have same visibility

---

## File Locations Reference

### Files Changed (LOCAL - Active Version)

```
PRINCIPAL/
│
├── LOCAL/
│   ├── Model/
│   │   └── Gatepass_model.php ........................ [MODIFIED]
│   │       ├── getactiverecords() → Added type filter (for Dorm Dean)
│   │       └── getactivecampusrecords() → New method (for Principal)
│   │
│   ├── Controller/
│   │   └── Gatepass.php .............................. [MODIFIED]
│   │       └── records() → Changed to getactivecampusrecords()
│   │
│   └── View/
│       └── records.php ............................... [NO CHANGES]
│           └── Displays whatever model returns
│
└── BACKUP/
    ├── Model/
    │   └── Gatepass_model.php ........................ [REFERENCE ONLY]
    │       └── Original without campus method
    │
    └── Controller/
        └── Gatepass.php .............................. [REFERENCE ONLY]
            └── Original using getactiverecords()
```

---

## Troubleshooting Guide

### Problem: Principal Still Sees Regular/Emergency

**Check:**
1. Is Controller using `getactivecampusrecords()` or `getactiverecords()`?
2. Clear browser cache
3. Check uploaded files on server

**Verify controller line ~35:**
```php
$listofrequest = $this->gatepass_model->getactivecampusrecords($dormitorydean_id, $session_id);
// Should be getactivecampusrecords(), not getactiverecords()
```

---

### Problem: Principal Only Sees One Dorm

**Check:**
1. Is `getactivecampusrecords()` method missing the dorm filter?
2. Should NOT have: `$this->db->where('hostel.dormdean_id', $dormdean_id);`

**Debug query:**
```sql
-- Run this to see what Principal should see
SELECT gatepass.*, hostel.hostel_name, students.lastname
FROM gatepass
JOIN hostel_rooms ON hostel_rooms.id = gatepass.room_id
JOIN hostel ON hostel.id = hostel_rooms.hostel_id
JOIN students ON students.id = gatepass.student_id
WHERE gatepass.type = 'campus'
  AND gatepass.deleted = '0'
  AND gatepass.session_id = 19;
  -- Should NOT have dormdean_id filter!
```

**Expected results:** All Campus Leave from all dorms

---

### Problem: No Requests Showing at All

**Check:**
1. Are there any Campus Leave requests in database?
2. Are they from current session?
3. Are they marked as deleted?

**Debug queries:**
```sql
-- Check if any Campus Leave exists
SELECT COUNT(*) FROM gatepass WHERE type = 'campus';

-- Check current session requests
SELECT * FROM gatepass 
WHERE type = 'campus' 
  AND session_id = 19 
  AND deleted = '0';

-- Check what session Principal is viewing
SELECT * FROM sessions WHERE is_active = 1;
```

---

## Deployment Instructions

### Files to Upload to Live Server

**1. Model File (Shared with Dorm Dean):**
```
Local: PRINCIPAL/LOCAL/Model/Gatepass_model.php
Server: application/models/Gatepass_model.php
```

**Note:** This is the SAME model file used by Dorm Dean. Upload EITHER:
- DORM_DEAN/LOCAL/Model/Gatepass_model.php, OR
- PRINCIPAL/LOCAL/Model/Gatepass_model.php

Both are identical and contain both methods.

---

**2. Controller File (Principal-specific):**
```
Local: PRINCIPAL/LOCAL/Controller/Gatepass.php
Server: application/controllers/principal/Gatepass.php
```

**Important:** This is different from Dorm Dean's controller!
- Dorm Dean controller uses `getactiverecords()`
- Principal controller uses `getactivecampusrecords()`

---

### Backup Before Deploying

```bash
# Backup current live files
cp application/models/Gatepass_model.php application/models/Gatepass_model.php.backup
cp application/controllers/principal/Gatepass.php application/controllers/principal/Gatepass.php.backup
```

---

### Deploy Steps

1. **Upload Model:**
   - Upload `Gatepass_model.php` to `application/models/`
   - Overwrites existing file
   - Used by both Dorm Dean and Principal

2. **Upload Principal Controller:**
   - Upload `Gatepass.php` to `application/controllers/principal/`
   - Only affects Principal, not Dorm Dean

3. **Clear Cache (if applicable):**
   ```bash
   rm -rf application/cache/*
   ```

4. **Test Principal Account:**
   - Login as Principal
   - Go to Gatepass → Records
   - Verify: Only Campus Leave shows
   - Verify: Requests from all dorms show

5. **Test Dorm Dean Account:**
   - Login as Dorm Dean
   - Go to Gatepass → Records
   - Verify: Only Regular + Emergency show
   - Verify: No Campus Leave visible

---

## Summary for Non-Technical People

### What Was Wrong:
1. Principal was seeing all gatepass types (Regular, Campus, Emergency)
2. Principal could only see requests from one dorm building
3. Campus Leave requests from other dorms were invisible
4. Principal couldn't approve Campus Leave for whole school

### What We Fixed:
1. Principal now sees ONLY Campus Leave (removed Regular and Emergency)
2. Principal sees Campus Leave from ALL dorms (not just one)
3. Created separate "view" for Principal vs Dorm Dean
4. Principal has school-wide oversight for Campus Leave

### How It Works Now:
- **Dorm Deans:** Handle day-to-day gatepasses (Regular, Emergency) for their dorm
- **Principal:** Handles special permissions (Campus Leave) for entire school
- Clear separation of duties
- No overlap or confusion
- Every Campus Leave request reaches the Principal, regardless of which dorm it came from

---

## Workflow Comparison

### Before: Overlapping Responsibilities

```
Dorm Dean A                Principal (Dorm A)
├── Regular (Dorm A)       ├── Regular (Dorm A) ← Duplicate!
├── Emergency (Dorm A)     ├── Emergency (Dorm A) ← Duplicate!
└── Campus Leave (Dorm A)  └── Campus Leave (Dorm A only) ← Limited!

Dorm Dean B                Campus Leave (Dorm B) → LOST! Nobody sees it!
├── Regular (Dorm B)       
├── Emergency (Dorm B)     
└── Campus Leave (Dorm B) ← Principal can't see this!
```

### After: Clear Separation

```
Dorm Dean A                Principal
├── Regular (Dorm A)       ├── Campus Leave (Dorm A)
└── Emergency (Dorm A)     ├── Campus Leave (Dorm B)
                           └── Campus Leave (Dorm C)

Dorm Dean B                All Campus Leave requests
├── Regular (Dorm B)       go to ONE place (Principal)
└── Emergency (Dorm B)     for school-wide approval

Dorm Dean C                No duplication
├── Regular (Dorm C)       Clear oversight
└── Emergency (Dorm C)     Nothing gets lost
```

---

## Version History

### Version 1.0 (Original - BACKUP)
- Principal used same method as Dorm Dean (`getactiverecords`)
- Saw all gatepass types
- Limited to one dorm scope
- Confusion about role boundaries

### Version 2.0 (Current - LOCAL)
- Principal uses new method (`getactivecampusrecords`)
- Sees only Campus Leave type
- School-wide scope (all dorms)
- Clear separation from Dorm Dean role

---

## Related Documentation

- **Dorm Dean Changes:** See `DORM_DEAN/DORM_DEAN_CHANGES_EXPLAINED.md`
- **Deployment Guide:** See `DEPLOYMENT_INSTRUCTIONS.md`
- **Model Documentation:** See comments in `Gatepass_model.php`

---

**End of Principal Changes Documentation**
