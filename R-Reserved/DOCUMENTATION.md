# Documentation: R-Reserved

This module handles reserved and pre-enrolled students list and search tools for the registrar portal.

## Changes Log:

* **June 23, 2026 (`267930f`)**:
  * Added the initial registrar reservation files `REGISTRAR/Reserved.php` and `REGISTRAR/studentReserved.php`.

---

## Detailed Code Changes & Line Ranges:

### 1. `studentReserved.php` Input Type Adjustments
* **Numeric LRN inputs (Spinners hidden)**:
```css
input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
}
input[type=number] {
		-moz-appearance: textfield;
}
```
* **LRN Input field type change**:
```html
<<<< Original
<input id="lrn" name="lrn" placeholder="Enter LRN" type="text" ... />
==== Modified
<input id="lrn" name="lrn" type="number" placeholder="0000 0000 0000" ... />
>>>>
```

### 2. Form Editable Fields (Removed `disabled="disabled"`)
Enabled edit access for Religion, Year Baptized, Email, and Guardians information:
```html
<<<< Original (Disabled Inputs)
<input type="text" id="display_religion" name="display_religion" class="..." disabled="disabled" />
<input type="text" id="display_email" name="display_email" class="..." disabled="disabled" />
<input type="text" id="display_guardianfirst" name="display_guardianfirst" class="..." disabled="disabled" />
==== Modified (Editable Inputs)
<input type="text" id="display_religion" name="display_religion" class="..." />
<input type="text" id="display_email" name="display_email" class="..." />
<input type="text" id="display_guardianfirst" name="display_guardianfirst" class="..." />
>>>>
```

### 3. Baptism Dropdown Conversion & State Memory
* **Dropdown Option element mapping**:
```html
<<<< Original
<input type="text" id="display_baptized" name="display_baptized" class="..." disabled="disabled" />
==== Modified
<select id="display_baptized" name="display_baptized" class="...">
    <option value="yes">Yes</option>
    <option value="no">No</option>
</select>
>>>>
```
* **Script Dynamic Event Handlers**:
```javascript
$(document).on('change', '#display_baptized', function() {
    var val = $(this).val();
    if (val === 'yes') {
        $('#display_year_baptized').prop('disabled', false);
        var originalYear = $('#display_year_baptized').data('original-year') || '';
        $('#display_year_baptized').val(originalYear);
    } else {
        $('#display_year_baptized').prop('disabled', true);
        $('#display_year_baptized').val('N/A');
    }
});
```
* **Numerical LRN Input filter**:
```javascript
$(document).on('input', '#display_lrn', function() {
    if (this.value !== 'N/A') {
        this.value = this.value.replace(/[^0-9]/g, '');
    }
});
```
