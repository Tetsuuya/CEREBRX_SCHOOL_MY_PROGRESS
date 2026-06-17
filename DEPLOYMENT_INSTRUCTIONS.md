# 📦 GATEPASS SYSTEM - DEPLOYMENT INSTRUCTIONS

## 🎯 CHANGES MADE

### Problem Solved:
- **Dorm Dean** should only see Regular and Emergency gatepasses
- **Principal** should only see Campus Leave gatepasses

### Solution:
Created two methods in Gatepass_model:
1. `getactiverecords()` - For Dorm Dean (excludes campus type)
2. `getactivecampusrecords()` - For Principal (shows only campus type)

---

## 📤 FILES TO UPLOAD TO LIVE SERVER

### 1️⃣ UPLOAD THE MODEL FILE (SHARED BY BOTH ROLES)

**Upload this file:**
```
LOCAL: C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\DORM_DEAN\LOCAL\Model\Gatepass_model.php
```

**To live server path:**
```
application/models/Gatepass_model.php
```

**Note:** Both Dorm Dean and Principal LOCAL models are now identical and contain both methods. Upload either one (they're the same).

---

### 2️⃣ UPLOAD THE PRINCIPAL CONTROLLER

**Upload this file:**
```
LOCAL: C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\PRINCIPAL\LOCAL\Controller\Gatepass.php
```

**To live server path:**
```
application/controllers/principal/Gatepass.php
OR
application/controllers/Principal/Gatepass.php
(check your server's folder structure)
```

---

## ✅ VERIFICATION STEPS

After uploading:

1. **Test Dorm Dean Account:**
   - Login as Dorm Dean
   - Go to Gatepass → Records
   - Should see: ✅ Regular, ✅ Emergency, ❌ Campus

2. **Test Principal Account:**
   - Login as Principal
   - Go to Gatepass → Records
   - Should see: ❌ Regular, ❌ Emergency, ✅ Campus

---

## 📋 TECHNICAL DETAILS

### Dorm Dean Controller Uses:
```php
$listofrequest = $this->gatepass_model->getactiverecords($dormitorydean_id, $session_id);
```

### Principal Controller Uses:
```php
$listofrequest = $this->gatepass_model->getactivecampusrecords($dormitorydean_id, $session_id);
```

### Model Methods:

**getactiverecords():**
```php
$this->db->where('gatepass.type !=', 'campus'); // Exclude campus
```

**getactivecampusrecords():**
```php
$this->db->where('gatepass.type', 'campus'); // Only campus
```

---

## 🔄 ROLLBACK (If Needed)

If something goes wrong, restore from BACKUP folder:
```
BACKUP: C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\DORM_DEAN\BACKUP\Model\
BACKUP: C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\PRINCIPAL\BACKUP\Controller\
```

---

## ⚠️ IMPORTANT NOTES

1. **Clear Cache:** After uploading, clear CodeIgniter cache if enabled
2. **Database Value:** Make sure the `type` field in `gatepass` table uses lowercase: 'regular', 'campus', 'emergency'
3. **Backup First:** Always backup your live files before uploading
4. **Test on Staging:** If you have a staging server, test there first

---

## 📞 SUPPORT

If issues occur after deployment:
- Check server error logs
- Verify file paths match your server structure
- Confirm database `type` column values
- Test with different user roles

---

**Date:** $(date)
**Modified By:** Kiro AI Assistant
