# Student Registration System Workflow

## Visual Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                    STUDENT REGISTRATION PAGE                        │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  PANEL 1: Search Students (BLUE INFO PANEL)                         │
│  Purpose: Find REGISTERED students to UNREGISTER                    │
├─────────────────────────────────────────────────────────────────────┤
│  1. Select School Year: [2024-2025 ▼]                              │
│  2. Student Name: [John Doe ▼] ← Shows ONLY allow='yes' students   │
│                                                                       │
│  Method: get_students_ajax()                                         │
│  SQL: WHERE student_session.allow = 'yes'                           │
│                                                                       │
│  ┌─────────────────────────────────────────────────────────┐        │
│  │ Selected Students for Registration (0)                  │        │
│  ├────┬──────────────┬────────┬────────┬─────────┬─────────┤        │
│  │ #  │ Name         │ Gender │ Grade  │ Section │ Action  │        │
│  ├────┼──────────────┼────────┼────────┼─────────┼─────────┤        │
│  │ 1  │ Doe, John    │ Male   │ 10     │ A       │ Remove  │        │
│  └────┴──────────────┴────────┴────────┴─────────┴─────────┘        │
│                                                                       │
│  [Unregister Selected Students] ← Changes allow='yes' to 'no'       │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ UNREGISTER
                                    ↓
                          allow='yes' → allow='no'
                                    │
                                    ↓
┌─────────────────────────────────────────────────────────────────────┐
│  PANEL 2: Search UNREGISTERED Students (YELLOW WARNING PANEL)       │
│  Purpose: Find UNREGISTERED students to REGISTER                    │
├─────────────────────────────────────────────────────────────────────┤
│  1. Select School Year: [2024-2025 ▼]                              │
│  2. Student Name: [Type name...] ← Shows ONLY allow='no' students  │
│                     └─ Autocomplete search                          │
│                                                                       │
│  Method: search_unregistered_students()                              │
│  SQL: WHERE student_session.allow = 'no'                            │
│                                                                       │
│  ┌─────────────────────────────────────────────────────────┐        │
│  │ Selected UNREGISTERED Students for Registration (1)     │        │
│  ├────┬──────────────┬────────┬────────┬─────────┬─────────┤        │
│  │ #  │ Name         │ Gender │ Grade  │ Section │ Action  │        │
│  ├────┼──────────────┼────────┼────────┼─────────┼─────────┤        │
│  │ 1  │ Doe, John    │ Male   │ 10     │ A       │ Remove  │        │
│  └────┴──────────────┴────────┴────────┴─────────┴─────────┘        │
│                                                                       │
│  [Register Selected Students] ← Changes allow='no' to 'yes'         │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ REGISTER
                                    ↓
                          allow='no' → allow='yes'
                                    │
                                    ↓
┌─────────────────────────────────────────────────────────────────────┐
│  PANEL 3: List of Registered Students (TABLE)                       │
│  Shows all students with allow='yes'                                │
├─────────────────────────────────────────────────────────────────────┤
│  # │ Student Name  │ Grade │ Section │ Total Spent │ Actions        │
│  ──┼───────────────┼───────┼─────────┼─────────────┼────────────────│
│  1 │ Doe, John     │ 10    │ A       │ ₱500.00     │ [Unregister]   │
│  2 │ Smith, Jane   │ 11    │ B       │ ₱750.00     │ [Unregister]   │
│                                                                       │
│  Unregister button → AJAX call → Row removed without page reload    │
└─────────────────────────────────────────────────────────────────────┘
```

## Data Flow

### Student States
```
┌──────────────────────────────────────────────────────────────┐
│                      STUDENT STATES                          │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  UNREGISTERED                          REGISTERED            │
│  (allow = 'no')                        (allow = 'yes')       │
│                                                              │
│  ├─ Not in cafeteria system            ├─ In cafeteria system
│  ├─ Cannot order food                  ├─ Can order food     │
│  ├─ No load balance                    ├─ Has load balance   │
│  ├─ Shown in Panel 2 search            ├─ Shown in Panel 1   │
│  └─ Shown in Panel 3 table             └─ Can be unregistered│
│                                                              │
│          ┌──────────┐              ┌──────────┐             │
│          │ REGISTER │              │UNREGISTER│             │
│          │  Button  │              │  Button  │             │
│          └────┬─────┘              └────┬─────┘             │
│               │                          │                   │
│               └────────┐        ┌────────┘                   │
│                        ↓        ↓                            │
│                    UPDATE student_session                    │
│                    SET allow = 'yes' or 'no'                 │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

## Controller Methods Map

```
┌──────────────────────────────────────────────────────────────┐
│                 CONTROLLER METHODS                           │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  get_students_ajax()                                         │
│  ├─ Returns: Students with allow='yes' (REGISTERED)         │
│  ├─ Used by: Panel 1 dropdown                               │
│  └─ Purpose: Get students to unregister                     │
│                                                              │
│  search_unregistered_students()                              │
│  ├─ Returns: Students with allow='no' (UNREGISTERED)        │
│  ├─ Used by: Panel 2 autocomplete search                    │
│  └─ Purpose: Get students to register                       │
│                                                              │
│  register_batch()                                            │
│  ├─ Action: Changes allow='no' to 'yes'                     │
│  ├─ Input: Array of student IDs                             │
│  └─ Purpose: Register multiple students                     │
│                                                              │
│  unregister_batch()                                          │
│  ├─ Action: Changes allow='yes' to 'no'                     │
│  ├─ Input: Array of student IDs                             │
│  └─ Purpose: Unregister multiple students                   │
│                                                              │
│  unregister_ajax()                                           │
│  ├─ Action: Changes allow='yes' to 'no' (AJAX)              │
│  ├─ Returns: JSON with success/error                        │
│  └─ Purpose: Unregister single student without reload       │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

## Database Schema

```sql
-- student_session table
CREATE TABLE student_session (
    id INT PRIMARY KEY,
    student_id INT,
    session_id INT,
    class_id INT,
    section_id INT,
    allow ENUM('yes', 'no'),  ← KEY COLUMN
    is_deactivate ENUM('yes', 'no'),
    -- ... other columns
);

-- Key Values:
-- allow = 'yes' → Student is REGISTERED (can use cafeteria)
-- allow = 'no'  → Student is UNREGISTERED (cannot use cafeteria)
```

## Testing Quick Reference

```
TEST 1: Panel 1 shows only allow='yes'
├─ Open Panel 1 dropdown
├─ Select school year
└─ Verify: Only registered students appear

TEST 2: Panel 2 shows only allow='no'
├─ Type in Panel 2 search box
├─ Select school year
└─ Verify: Only unregistered students appear

TEST 3: Unregister works
├─ Select registered student from Panel 1
├─ Click "Unregister Selected Students"
├─ Verify: Student removed from Panel 1
└─ Verify: Student now appears in Panel 2

TEST 4: Register works
├─ Search unregistered student in Panel 2
├─ Click "Register Selected Students"
├─ Verify: Student removed from Panel 2
└─ Verify: Student now appears in Panel 1

TEST 5: AJAX unregister works
├─ Find student in bottom table (Panel 3)
├─ Click "Unregister" button on row
├─ Verify: Confirmation dialog appears
├─ Confirm: Row fades out and disappears
├─ Verify: No page reload
└─ Verify: Student now in Panel 2 search
```
