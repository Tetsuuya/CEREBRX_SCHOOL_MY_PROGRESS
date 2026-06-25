import zipfile
import xml.etree.ElementTree as ET

template_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_ULTRA_OPTIMIZED.xlsx'
z = zipfile.ZipFile(template_path)

# Read shared strings
shared_strings_xml = z.read('xl/sharedStrings.xml').decode('utf-8')
root = ET.fromstring(shared_strings_xml)
shared_strings = []
for si in root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}t'):
    shared_strings.append(si.text if si.text else '')

print("=" * 80)
print("INPUT SHEET - ROW 11 (where {start_boys} placeholder is)")
print("=" * 80)

input_xml = z.read('xl/worksheets/sheet1.xml').decode('utf-8')
input_root = ET.fromstring(input_xml)

for cell in input_root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
    ref = cell.get('r')
    if ref and ref.startswith('A11') or ref.startswith('B11') or ref.startswith('C11'):
        cell_type = cell.get('t')
        value = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
        formula = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}f')
        
        if formula is not None:
            print(f"{ref}: FORMULA = {formula.text}")
        elif value is not None:
            if cell_type == 's':
                string_idx = int(value.text)
                print(f"{ref}: {shared_strings[string_idx]}")
            else:
                print(f"{ref}: {value.text}")

print("\n" + "=" * 80)
print("INPUT SHEET - ROWS 12-15 (actual student data rows)")
print("=" * 80)

for row_num in [12, 13, 14, 15]:
    print(f"\nRow {row_num}:")
    for cell in input_root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
        ref = cell.get('r')
        if ref and ref[1:] == str(row_num) and ref[0] in ['A', 'B', 'C']:
            cell_type = cell.get('t')
            value = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
            formula = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}f')
            
            if formula is not None:
                print(f"  {ref}: FORMULA = {formula.text}")
            elif value is not None:
                if cell_type == 's':
                    string_idx = int(value.text)
                    print(f"  {ref}: {shared_strings[string_idx]}")
                else:
                    print(f"  {ref}: {value.text}")
            else:
                print(f"  {ref}: EMPTY")

print("\n" + "=" * 80)
print("TERM1 SHEET - ROW 12 (where {start_boys} placeholder is)")
print("=" * 80)

term1_xml = z.read('xl/worksheets/sheet2.xml').decode('utf-8')
term1_root = ET.fromstring(term1_xml)

for cell in term1_root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
    ref = cell.get('r')
    if ref and ref.startswith('A12') or ref.startswith('B12') or ref.startswith('C12'):
        cell_type = cell.get('t')
        value = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
        formula = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}f')
        
        if formula is not None:
            print(f"{ref}: FORMULA = {formula.text}")
        elif value is not None:
            if cell_type == 's':
                string_idx = int(value.text)
                print(f"{ref}: {shared_strings[string_idx]}")
            else:
                print(f"{ref}: {value.text}")

print("\n" + "=" * 80)
print("TERM1 SHEET - ROWS 13-16 (actual student data rows)")
print("=" * 80)

for row_num in [13, 14, 15, 16]:
    print(f"\nRow {row_num}:")
    for cell in term1_root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
        ref = cell.get('r')
        if ref and ref[1:] == str(row_num) and ref[0] in ['A', 'B', 'C']:
            cell_type = cell.get('t')
            value = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
            formula = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}f')
            
            if formula is not None:
                print(f"  {ref}: FORMULA = {formula.text}")
            elif value is not None:
                if cell_type == 's':
                    string_idx = int(value.text)
                    print(f"  {ref}: {shared_strings[string_idx]}")
                else:
                    print(f"  {ref}: {value.text}")
            else:
                print(f"  {ref}: EMPTY")

z.close()

print("\n" + "=" * 80)
print("CONCLUSION:")
print("=" * 80)
print("Row with {start_boys} is the HEADER/MARKER row (not actual data).")
print("Rows AFTER that are actual student name rows.")
print("\nIn INPUT: PHP should populate rows starting from the row after {start_boys}")
print("In TERM1: PHP should NOT touch anything - formulas reference INPUT")
