# Security Branch - Pre-Merge Testing Checklist

## ⚠️ CRITICAL: Do NOT Merge Until All Tests Pass

This branch handles **sensitive personal information** (PII) including:
- OSCA IDs
- Contact numbers
- Addresses
- Photos
- Personal health information

**Merging untested security code could expose sensitive data!**

---

## 🧪 Testing Priority Levels

### 🔴 **CRITICAL** (Must Test Before Merge)
These tests verify core security functionality.

### 🟡 **IMPORTANT** (Should Test Before Merge)
These tests verify security doesn't break existing functionality.

### 🟢 **RECOMMENDED** (Test When Possible)
These tests verify edge cases and user experience.

---

## 🔴 CRITICAL Tests (Must Complete)

### 1. Photo Security Testing ✅

#### Test 1.1: Direct Photo Access Prevention
- [ ] **Action**: Try accessing photo directly via URL
  - URL: `http://localhost/storage/senior-photos/photo.jpg`
  - Expected: Should return 404 or access denied
  - Status: ⬜ Not Tested

#### Test 1.2: Secure Photo Route Access
- [ ] **Action**: Access photo via secure route
  - URL: `http://localhost/seniors/{id}/photo`
  - Expected: Photo should display correctly
  - Status: ⬜ Not Tested

#### Test 1.3: Photo Upload to Private Storage
- [ ] **Action**: Upload a new photo via form
  - Expected: Photo saved to `storage/app/private/senior-photos/`
  - Verify: Check file system location
  - Status: ⬜ Not Tested

#### Test 1.4: Photo Display in Views
- [ ] **Action**: Check if photos display correctly in:
  - Senior list view
  - Edit profile view
  - Profile view
  - Expected: Photos load via secure route
  - Status: ⬜ Not Tested

---

### 2. PII Protection Testing ✅

#### Test 2.1: Log File Security
- [ ] **Action**: Trigger login attempts and check log files
  - Expected: OSCA IDs should show as `[REDACTED]`
  - Check: `storage/logs/laravel.log`
  - Status: ⬜ Not Tested

#### Test 2.2: Error Log Security
- [ ] **Action**: Trigger validation errors
  - Expected: Logs should NOT contain:
    - Full request data
    - Stack traces
    - Sensitive input data
  - Check: `storage/logs/laravel.log`
  - Status: ⬜ Not Tested

#### Test 2.3: Browser Console Security
- [ ] **Action**: Open browser console during form submission
  - Expected: No sensitive data in console.log
  - Check: Developer Tools → Console
  - Status: ⬜ Not Tested

---

### 3. API Data Minimization Testing ✅

#### Test 3.1: Profile API Response
- [ ] **Action**: Call profile API endpoint
  - Endpoint: `/api/profile` or similar
  - Expected: Response should NOT include:
    - `contact_number`
    - `residence` (specific address)
    - `street` (specific address)
  - Expected: Photo path should be secure route
  - Status: ⬜ Not Tested

#### Test 3.2: Form Data Loading
- [ ] **Action**: Load senior data in form_seniorID.blade.php
  - Expected: Contact numbers NOT loaded in JavaScript
  - Check: View page source for JavaScript data
  - Status: ⬜ Not Tested

---

### 4. SQL Injection Prevention Testing ✅

#### Test 4.1: Malicious Sort Parameter
- [ ] **Action**: Try malicious sort parameter
  - URL: `?sort=1; DROP TABLE seniors;--`
  - Expected: Should default to safe sort (`created_at`)
  - Expected: No SQL error, page loads normally
  - Status: ⬜ Not Tested

#### Test 4.2: Path Traversal Attempt
- [ ] **Action**: Try path traversal
  - URL: `?sort=../../../etc/passwd`
  - Expected: Should default to safe sort
  - Status: ⬜ Not Tested

---

### 5. CSRF Protection Testing ✅

#### Test 5.1: Form Submission Without CSRF Token
- [ ] **Action**: Submit form without CSRF token
  - Expected: Should return 419 error
  - Expected: Form should NOT submit
  - Status: ⬜ Not Tested

#### Test 5.2: CSRF Token in All Forms
- [ ] **Action**: Check all forms have `@csrf` directive
  - Forms to check:
    - [ ] Add new senior form
    - [ ] Edit senior form
    - [ ] Pension form
    - [ ] Senior ID form
    - [ ] Benefits form
  - Status: ⬜ Not Tested

---

### 6. File Upload Security Testing ✅

#### Test 6.1: Non-Image File Upload
- [ ] **Action**: Try uploading non-image file (e.g., .pdf, .exe)
  - Expected: Validation error
  - Expected: File should NOT be uploaded
  - Status: ⬜ Not Tested

#### Test 6.2: Large File Upload
- [ ] **Action**: Try uploading file > 2MB
  - Expected: Validation error
  - Expected: File should NOT be uploaded
  - Status: ⬜ Not Tested

#### Test 6.3: Valid Image Upload
- [ ] **Action**: Upload valid image file (.jpg, .png)
  - Expected: File should upload successfully
  - Expected: File saved to private storage
  - Status: ⬜ Not Tested

---

## 🟡 IMPORTANT Tests (Should Complete)

### 7. Form Functionality Testing ✅

#### Test 7.1: Add New Senior Form
- [ ] **Action**: Complete and submit "Add New Senior" form
  - Expected: Form submits successfully
  - Expected: Data saved correctly
  - Expected: Success message displays
  - Status: ⬜ Not Tested

#### Test 7.2: Edit Senior Form
- [ ] **Action**: Edit existing senior profile
  - Expected: Form loads with existing data
  - Expected: Updates save correctly
  - Expected: Success message displays
  - Status: ⬜ Not Tested

#### Test 7.3: Required Field Validation
- [ ] **Action**: Submit form with missing required fields
  - Fields to test:
    - [ ] Religion (now required)
    - [ ] Ethnic origin (now required)
    - [ ] Photo upload validation
  - Expected: Validation errors display
  - Expected: Form does NOT submit
  - Status: ⬜ Not Tested

---

### 8. Error Handling Testing ✅

#### Test 8.1: Generic Error Messages
- [ ] **Action**: Trigger system error (e.g., database error)
  - Expected: Generic error message shown
  - Expected: NO system details exposed
  - Expected: Error message: "An error occurred..."
  - Status: ⬜ Not Tested

#### Test 8.2: Validation Error Display
- [ ] **Action**: Submit invalid form data
  - Expected: Validation errors display clearly
  - Expected: User can fix errors and resubmit
  - Status: ⬜ Not Tested

---

## 🟢 RECOMMENDED Tests (When Possible)

### 9. User Experience Testing ✅

#### Test 9.1: Photo Display Performance
- [ ] **Action**: Load pages with photos
  - Expected: Photos load at reasonable speed
  - Expected: No broken image links
  - Status: ⬜ Not Tested

#### Test 9.2: Form Auto-fill Functionality
- [ ] **Action**: Use auto-fill in Senior ID form
  - Expected: Auto-fill works correctly
  - Expected: Contact numbers NOT exposed
  - Status: ⬜ Not Tested

---

## 📋 Testing Execution Plan

### Phase 1: Security Tests (Do First) 🔴
1. Photo Security (Tests 1.1 - 1.4)
2. PII Protection (Tests 2.1 - 2.3)
3. SQL Injection (Tests 4.1 - 4.2)
4. CSRF Protection (Tests 5.1 - 5.2)
5. File Upload Security (Tests 6.1 - 6.3)

### Phase 2: Functionality Tests (Do Second) 🟡
6. Form Functionality (Tests 7.1 - 7.3)
7. Error Handling (Tests 8.1 - 8.2)

### Phase 3: User Experience (Do Last) 🟢
8. User Experience (Tests 9.1 - 9.2)

---

## ✅ Pre-Merge Checklist

Before merging to main, verify:

- [ ] All 🔴 CRITICAL tests pass
- [ ] All 🟡 IMPORTANT tests pass
- [ ] No sensitive data exposed in logs
- [ ] No sensitive data exposed in API responses
- [ ] Photos are secure and accessible
- [ ] Forms work correctly
- [ ] Error handling is secure
- [ ] No console errors in browser
- [ ] Code review completed
- [ ] Documentation updated

---

## 🚨 STOP Criteria

**DO NOT MERGE IF:**
- ❌ Any CRITICAL test fails
- ❌ Sensitive data appears in logs
- ❌ Photos are directly accessible
- ❌ Forms submit without CSRF tokens
- ❌ SQL injection vulnerabilities exist
- ❌ File uploads accept non-images

---

## 📝 Test Results Template

```
TEST ID: [e.g., 1.1]
TEST NAME: [e.g., Direct Photo Access Prevention]
DATE: [Date]
TESTER: [Your name]
RESULT: ⬜ PASS ⬜ FAIL ⬜ BLOCKED
NOTES: [Any observations]
```

---

## 🎯 Recommendation

**Status: 🔴 NOT READY FOR MERGE**

**Action Required:**
1. Complete all 🔴 CRITICAL tests
2. Complete all 🟡 IMPORTANT tests
3. Fix any failing tests
4. Re-test after fixes
5. Then proceed with merge

**Estimated Testing Time:** 2-4 hours

---

## 📞 Need Help?

If you encounter issues during testing:
1. Document the issue
2. Note the test ID
3. Check error logs
4. Verify the security implementation
5. Fix before merging


