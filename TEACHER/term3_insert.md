# Term 3 Grade Insert — Grade 8 Diamond
**Date:** 2026-07-03  
**School Year:** 2023-2024  
**Purpose:** Manually inserted Quarter 3 grades for all Grade 8 Diamond students across all 15 subjects with mixed passing and failing grades.

---

## Key IDs Used

| Variable     | Value | Description              |
|--------------|-------|--------------------------|
| `class_id`   | 11    | Grade 8                  |
| `section_id` | 3     | Diamond                  |
| `session_id` | 19    | School Year 2023-2024    |
| `quarter`    | 3     | Term 3 / Quarter 3       |

---

## Subjects Inserted (15 total)

| Subject ID | Subject Name           |
|------------|------------------------|
| 47         | Araling Panlipunan 8   |
| 93         | Arts 8                 |
| 65         | Computer 8             |
| 82         | English 8              |
| 14         | Filipino 8             |
| 99         | Health 8               |
| 114        | Homeroom 8             |
| 35         | Mathematics 8          |
| 89         | Music 8                |
| 108        | Pathfindering 8        |
| 95         | Physical Education 8   |
| 1          | Science 8              |
| 20         | TLE 8                  |
| 6          | Values Education 8     |
| 132        | Work Education 8       |

---

## Grade Distribution

- **75% of grades** -> Passing: **75 to 98** (PASS)
- **25% of grades** -> Failing: **60 to 74** (FAIL)

---

## SQL Commands (Run in Order)

---

### STEP 1 - Create Batch Records (1 per subject)

Creates a grade sheet folder for each subject in Quarter 3.

```sql
INSERT INTO import_grades_batch 
    (class_id, section_id, subject_id, quarter, session_id, teacher_id, published, created_at)
VALUES
    (11, 3, 47,  3, 19, 1, 1, NOW()),
    (11, 3, 93,  3, 19, 1, 1, NOW()),
    (11, 3, 65,  3, 19, 1, 1, NOW()),
    (11, 3, 82,  3, 19, 1, 1, NOW()),
    (11, 3, 14,  3, 19, 1, 1, NOW()),
    (11, 3, 99,  3, 19, 1, 1, NOW()),
    (11, 3, 114, 3, 19, 1, 1, NOW()),
    (11, 3, 35,  3, 19, 1, 1, NOW()),
    (11, 3, 89,  3, 19, 1, 1, NOW()),
    (11, 3, 108, 3, 19, 1, 1, NOW()),
    (11, 3, 95,  3, 19, 1, 1, NOW()),
    (11, 3, 1,   3, 19, 1, 1, NOW()),
    (11, 3, 20,  3, 19, 1, 1, NOW()),
    (11, 3, 6,   3, 19, 1, 1, NOW()),
    (11, 3, 132, 3, 19, 1, 1, NOW());
```

---

### STEP 2 - Insert Grades into import_grades_details

Inserts one grade row for every student x every subject.

```sql
INSERT INTO import_grades_details
    (batch_id, student_id, subject_id, class_id, section_id, quarter, grades, initial_grade, approved, post)
SELECT 
    igb.id,
    ss.student_id,
    igb.subject_id,
    11,
    3,
    3,
    CASE 
        WHEN RAND() < 0.25 
            THEN FLOOR(RAND() * 14 + 60)
        ELSE 
            FLOOR(RAND() * 24 + 75)
    END,
    CASE 
        WHEN RAND() < 0.25 
            THEN FLOOR(RAND() * 14 + 60)
        ELSE 
            FLOOR(RAND() * 24 + 75)
    END,
    1,
    1
FROM student_session ss
JOIN import_grades_batch igb 
    ON igb.class_id    = 11
    AND igb.section_id = 3
    AND igb.session_id = 19
    AND igb.quarter    = 3
WHERE ss.class_id   = 11
  AND ss.section_id = 3
  AND ss.session_id = 19;
```

---

### STEP 3 - Insert Grades into exam_results

The web app reads grades from exam_results, not import_grades_details.
This copies the grades into the table the app actually displays.

```sql
INSERT INTO exam_results
    (student_id, subject_id, session_id, quarter, get_marks, attendence, postgrade, is_active)
SELECT DISTINCT
    igd.student_id,
    igd.subject_id,
    19,
    3,
    igd.grades,
    'present',
    'yes',
    'yes'
FROM import_grades_details igd
JOIN import_grades_batch igb ON igd.batch_id = igb.id
WHERE igb.class_id    = 11
  AND igb.section_id  = 3
  AND igb.session_id  = 19
  AND igb.quarter     = 3;
```

---

### STEP 4 - Verify (Read Only)

Just checks the data, nothing is changed.

```sql
SELECT 
    CONCAT(students.firstname, ' ', students.lastname) AS student_name,
    subjects.name AS subject,
    import_grades_details.grades,
    CASE WHEN import_grades_details.grades >= 75 THEN 'PASS' ELSE 'FAIL' END AS status
FROM import_grades_details
JOIN students  ON import_grades_details.student_id = students.id
JOIN subjects  ON import_grades_details.subject_id = subjects.id
JOIN classes   ON import_grades_details.class_id   = classes.id
LEFT JOIN sections ON import_grades_details.section_id = sections.id
WHERE classes.class    = 'Grade 8'
  AND sections.section = 'Diamond'
  AND import_grades_details.quarter = 3
ORDER BY students.lastname ASC, subjects.name ASC;
```

---

## UNDO - How to Reverse Everything

Run in this exact order if you need to remove everything.

### UNDO Step 3 - Remove from exam_results

```sql
DELETE FROM exam_results
WHERE session_id = 19
  AND quarter    = 3
  AND student_id IN (
      SELECT student_id FROM student_session
      WHERE class_id = 11 AND section_id = 3 AND session_id = 19
  );
```

### UNDO Step 2 - Remove from import_grades_details

```sql
DELETE import_grades_details
FROM import_grades_details
JOIN import_grades_batch igb ON import_grades_details.batch_id = igb.id
WHERE igb.class_id    = 11
  AND igb.section_id  = 3
  AND igb.session_id  = 19
  AND igb.quarter     = 3;
```

### UNDO Step 1 - Remove batch records

```sql
DELETE FROM import_grades_batch
WHERE class_id   = 11
  AND section_id = 3
  AND session_id = 19
  AND quarter    = 3;
```

---

## Notes

- `import_grades_details` stores the uploaded/imported grade records
- `import_grades_batch` is the batch/folder that groups grades per subject per quarter
- `exam_results` is what the web app reads to display grades on the student profile
- Always delete from `exam_results` FIRST before deleting from `import_grades_details` or `import_grades_batch`
- Grades were randomly generated: 75% passing (75-98), 25% failing (60-74)
