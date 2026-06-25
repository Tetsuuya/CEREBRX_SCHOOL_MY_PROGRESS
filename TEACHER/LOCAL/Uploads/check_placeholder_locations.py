import zipfile
import xml.etree.ElementTree as ET

# Path to the template
template_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_ULTRA_OPTIMIZED.xlsx'

# Open the Excel file as ZIP
z = zipfile.ZipFile(template_path)

# Read shared strings
shared_strings_xml = z.read('xl/sharedStrings.xml').decode('utf-8')
root = ET.fromstring(shared_strings_xml)

# Extract all shared strings
shared_strings = []
for si in root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}t'):
    shared_strings.append(si.text if si.text else '')

# Create map of placeholder strings to indices
placeholder_indices = {}
for idx, s in enumerate(shared_strings):
    if s and '{' in s and '}' in s:
        placeholder_indices[idx] = s

print("=" * 80)
print("PLACEHOLDERS IN INPUT SHEET (sheet1.xml)")
print("=" * 80)

input_xml = z.read('xl/worksheets/sheet1.xml').decode('utf-8')
input_root = ET.fromstring(input_xml)

placeholder_cells_input = []
for cell in input_root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
    ref = cell.get('r')
    cell_type = cell.get('t')
    value = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
    
    if value is not None and cell_type == 's':
        string_idx = int(value.text)
        if string_idx in placeholder_indices:
            placeholder_cells_input.append((ref, placeholder_indices[string_idx]))

print(f"Found {len(placeholder_cells_input)} cells with placeholders:")
for ref, placeholder in sorted(placeholder_cells_input)[:20]:  # Show first 20
    print(f"  {ref}: {placeholder}")

print("\n" + "=" * 80)
print("PLACEHOLDERS IN TERM1 SHEET (sheet2.xml)")
print("=" * 80)

term1_xml = z.read('xl/worksheets/sheet2.xml').decode('utf-8')
term1_root = ET.fromstring(term1_xml)

placeholder_cells_term1 = []
for cell in term1_root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
    ref = cell.get('r')
    cell_type = cell.get('t')
    value = cell.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
    
    if value is not None and cell_type == 's':
        string_idx = int(value.text)
        if string_idx in placeholder_indices:
            placeholder_cells_term1.append((ref, placeholder_indices[string_idx]))

print(f"Found {len(placeholder_cells_term1)} cells with placeholders:")
for ref, placeholder in sorted(placeholder_cells_term1)[:20]:  # Show first 20
    print(f"  {ref}: {placeholder}")

print("\n" + "=" * 80)
print("KEY INSIGHT:")
print("=" * 80)
print("If INPUT has placeholders like {start_boys} and TERM1 also has {start_boys},")
print("it means placeholders exist in BOTH sheets.")
print("\nBUT we need to check: Do the student NAME cells (B14, C14, etc.) have")
print("placeholders or formulas?")
print("\nFrom previous analysis:")
print("  INPUT B14: EMPTY (will be populated by PHP)")
print("  INPUT C14: EMPTY (will be populated by PHP)")
print("  TERM1 B14: EMPTY (formula missing?)")
print("  TERM1 C14: =INPUT!B12 (references INPUT)")
print("\nThis suggests TERM1 references INPUT for data, so we should ONLY populate INPUT.")

z.close()
