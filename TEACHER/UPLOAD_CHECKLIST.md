# Upload Checklist - PhpSpreadsheet Implementation

## Files to Upload via FileZilla

### ✅ 1. PhpSpreadsheet Library (ALREADY UPLOADED)
- [x] Source folder uploaded
- **Server path**: `/mnt/volume_sgp1_01/cerebrox/public_html/application/third_party/PhpSpreadsheet/src/PhpSpreadsheet/`

### 📤 2. PhpspreadsheetLoader.php (NEW FILE - UPLOAD THIS)
- **Local**: `C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Libraries\PhpspreadsheetLoader.php`
- **Server**: `/mnt/volume_sgp1_01/cerebrox/public_html/application/libraries/PhpspreadsheetLoader.php`
- **Purpose**: Autoloader for PhpSpreadsheet

### 📤 3. Excelwithspout.php (UPDATED FILE - UPLOAD THIS)
- **Local**: `C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Libraries\Excelwithspout.php`
- **Server**: `/mnt/volume_sgp1_01/cerebrox/public_html/application/libraries/Excelwithspout.php`
- **Purpose**: Main Excel generation library (now using PhpSpreadsheet)

---

## Upload Steps

1. **Open FileZilla**
2. **Connect to server**: `206.189.146.212`
3. **Upload PhpspreadsheetLoader.php**:
   - Navigate to: `/mnt/volume_sgp1_01/cerebrox/public_html/application/libraries/`
   - Upload: `PhpspreadsheetLoader.php`
4. **Upload Excelwithspout.php**:
   - Same directory: `/mnt/volume_sgp1_01/cerebrox/public_html/application/libraries/`
   - Upload: `Excelwithspout.php` (overwrite existing file)

---

## Testing Steps

### 1. Test Generation
1. Login as teacher
2. Go to: **Grades → Generate Spreadsheet**
3. Select:
   - Class
   - Section
   - Subject
   - Template: "3-term_New_Grade_Template.xlsx"
4. Click **"Generate Spreadsheet"**
5. **Wait 2-3 minutes** (should NOT timeout)
6. File should download automatically

### 2. Verify Excel File
Open the downloaded Excel file and check:
- [ ] **School name** is filled (not blank)
- [ ] **Class/Section** is filled (e.g., "Grade 10 A")
- [ ] **Subject name** is filled
- [ ] **Teacher name** is filled
- [ ] **Boys section** has student names and IDs
- [ ] **Girls section** has student names and IDs
- [ ] **All 9 sheets** have data (not just sheet 1)

### 3. Check Logs (if issues)
Server path: `/mnt/volume_sgp1_01/cerebrox/public_html/application/logs/excel_generation_debug.txt`

Look for:
```
PhpSpreadsheet Generation Started
Cell search completed
Loading Excel with PhpSpreadsheet...
Excel loaded successfully
Processing 9 sheets
Processing sheet 1/9
...
File saved successfully!
```

---

## Expected Results

✅ **Success Indicators**:
- Download completes in 2-3 minutes (not 9+ minutes)
- No 504 Gateway Timeout error
- Excel file opens correctly
- All brackets filled with data
- Student rosters populated

❌ **If Problems**:
- Check `/application/logs/excel_generation_debug.txt` for errors
- Verify PhpSpreadsheet folder exists: `/application/third_party/PhpSpreadsheet/src/PhpSpreadsheet/`
- Verify both files uploaded correctly

---

## Rollback (if needed)

If PhpSpreadsheet doesn't work, you can quickly rollback:

1. Open `Excelwithspout.php` on server
2. Find this section (around line 700):
```php
// SIMPLE DIRECT DOWNLOAD VERSION - No processing, instant download (COMMENTED OUT)
/*
public function generate_spreadsheet_DIRECT_DOWNLOAD( $parameters ){
```
3. Remove `/*` at the start and `*/` at the end
4. Rename function to `generate_spreadsheet` (remove `_DIRECT_DOWNLOAD`)
5. Comment out the PhpSpreadsheet version below it

This will restore instant download (but brackets won't be filled).

---

## Summary

**What we did**:
1. ✅ Installed PhpSpreadsheet 5.8.0
2. ✅ Created autoloader
3. ✅ Updated Excel generation code
4. ✅ Kept old code for rollback

**What you need to do**:
1. 📤 Upload 2 files (PhpspreadsheetLoader.php + Excelwithspout.php)
2. 🧪 Test Excel generation
3. ✅ Verify brackets are filled

**Expected outcome**:
- Excel generation completes in 2-3 minutes (was 9+ and timing out)
- All data filled automatically
- 100% success rate
