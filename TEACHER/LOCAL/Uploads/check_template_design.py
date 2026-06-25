import zipfile
import xml.etree.ElementTree as ET
import re

# Path to the template
template_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_ULTRA_OPTIMIZED.xlsx'

# Open the Excel file as ZIP
z = zipfile.ZipFile(template_path)

# Read shared strings (where cell text is stored)
shared_strings_xml = z.read('xl/sharedStrings.xml').decode('utf-8')
root = ET.fromstring(shared_strings_xml)

# Extract all shared strings
shared_strings = []
for si in root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}t'):
    shared_strings.append(si.text if si.text else '')

print("=" * 80)
print("SHARED STRINGS CONTAINING PLACEHOLDERS:")
print("=" * 80)
for idx, s in enumerate(shared_strings):
    if s and ('{' in s or 'INPUT' in s or '=' in s):
        print(f"[{idx}] {s}")

# Check TERM1 sheet (sheet2.xml)
print("\n" + "=" * 80)
print("ANALYZING TERM1 SHEET (sheet2.xml)")
print("=" * 80)

sheet_xml = z.read('xl/worksheets/sheet2.xml').decode('utf-8')
root = ET.fromstring(sheet_xml)

# Find cells in row 14 (student name row)
print("\nCells in Row 14 (first student name row):")
for cell in root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
    ref = cell.get('r')
    if ref and ref.startswith('B14') or ref.startswith('C14'):
        # Check if it has a formula
        formula = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}f')
        # Check if it has a value
        value = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
        
        if formula is not None:
            print(f"  {ref}: FORMULA = {formula.text}")
        elif value is not None:
            val_text = value.text
            # If t="s", it's a shared string index
            cell_type = cell.get('t')
            if cell_type == 's':
                string_idx = int(val_text)
                print(f"  {ref}: SHARED STRING [{string_idx}] = {shared_strings[string_idx]}")
            else:
                print(f"  {ref}: VALUE = {val_text}")
        else:
            print(f"  {ref}: EMPTY")

# Count formulas in TERM1
formulas = []
for cell in root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
    formula = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}f')
    if formula is not None:
        formulas.append((cell.get('r'), formula.text))

print(f"\nTotal formulas in TERM1: {len(formulas)}")
if formulas:
    print("First 10 formulas:")
    for i, (cell_ref, formula_text) in enumerate(formulas[:10]):
        print(f"  {cell_ref}: {formula_text}")

# Check INPUT sheet (sheet1.xml) for comparison
print("\n" + "=" * 80)
print("ANALYZING INPUT SHEET (sheet1.xml)")
print("=" * 80)

input_xml = z.read('xl/worksheets/sheet1.xml').decode('utf-8')
input_root = ET.fromstring(input_xml)

print("\nCells in Row 14 (first student name row):")
for cell in input_root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
    ref = cell.get('r')
    if ref and (ref.startswith('B14') or ref.startswith('C14')):
        # Check if it has a formula
        formula = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}f')
        # Check if it has a value
        value = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
        
        if formula is not None:
            print(f"  {ref}: FORMULA = {formula.text}")
        elif value is not None:
            val_text = value.text
            # If t="s", it's a shared string index
            cell_type = cell.get('t')
            if cell_type == 's':
                string_idx = int(val_text)
                print(f"  {ref}: SHARED STRING [{string_idx}] = {shared_strings[string_idx]}")
            else:
                print(f"  {ref}: VALUE = {val_text}")
        else:
            print(f"  {ref}: EMPTY")

z.close()

print("\n" + "=" * 80)
print("CONCLUSION:")
print("=" * 80)
print("If TERM1 cells contain formulas like '=INPUT!B14', then the template is designed")
print("to reference INPUT sheet and we should ONLY populate INPUT sheet.")
print("\nIf TERM1 cells contain placeholders like '{start_boys}', then we need to")
print("populate ALL sheets with student data (current implementation is correct).")
