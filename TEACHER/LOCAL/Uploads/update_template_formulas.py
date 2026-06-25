import zipfile
import xml.etree.ElementTree as ET
import os
import shutil
from datetime import datetime

# Paths
template_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_ULTRA_OPTIMIZED.xlsx'
output_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_ULTRA_OPTIMIZED_V2.xlsx'
backup_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_ULTRA_OPTIMIZED_BACKUP.xlsx'

# Backup original
shutil.copy2(template_path, backup_path)
print(f"Backed up original to: {backup_path}")

# Extract Excel to temp directory
temp_dir = 'temp_excel_edit'
if os.path.exists(temp_dir):
    shutil.rmtree(temp_dir)

with zipfile.ZipFile(template_path, 'r') as zip_ref:
    zip_ref.extractall(temp_dir)

print("Extracted Excel ZIP")

# Define namespace
ns = {'main': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
ET.register_namespace('', ns['main'])

def update_sheet_formulas(sheet_num, sheet_name):
    """Update formulas in a sheet to reference TERM1 instead of INPUT"""
    sheet_file = f'{temp_dir}/xl/worksheets/sheet{sheet_num}.xml'
    
    if not os.path.exists(sheet_file):
        print(f"  Sheet {sheet_num} not found, skipping")
        return 0
    
    tree = ET.parse(sheet_file)
    root = tree.getroot()
    
    count = 0
    
    # Find all formula elements
    for formula in root.findall('.//main:f', ns):
        if formula.text and 'INPUT!' in formula.text:
            # Replace INPUT! with TERM1!
            old_formula = formula.text
            new_formula = formula.text.replace('INPUT!', 'TERM1!')
            formula.text = new_formula
            count += 1
            if count <= 5:  # Show first 5 changes
                print(f"    Changed: {old_formula} → {new_formula}")
    
    if count > 0:
        # Save modified XML
        tree.write(sheet_file, encoding='utf-8', xml_declaration=True)
        print(f"  Sheet {sheet_num} ({sheet_name}): Updated {count} formulas")
    else:
        print(f"  Sheet {sheet_num} ({sheet_name}): No INPUT! formulas found")
    
    return count

def remove_term1_formulas():
    """Remove formulas from TERM1 so PHP can populate it"""
    sheet_file = f'{temp_dir}/xl/worksheets/sheet2.xml'
    
    if not os.path.exists(sheet_file):
        print("  TERM1 sheet not found!")
        return 0
    
    tree = ET.parse(sheet_file)
    root = tree.getroot()
    
    count = 0
    
    # Find all cells with formulas
    for cell in root.findall('.//main:c', ns):
        formula = cell.find('main:f', ns)
        if formula is not None:
            # Remove the formula element
            cell.remove(formula)
            
            # Also remove the value element so cell is truly empty
            value = cell.find('main:v', ns)
            if value is not None:
                cell.remove(value)
            
            count += 1
    
    if count > 0:
        tree.write(sheet_file, encoding='utf-8', xml_declaration=True)
        print(f"  TERM1: Removed {count} formulas (cells now empty for PHP)")
    else:
        print("  TERM1: No formulas to remove")
    
    return count

print("\n" + "="*80)
print("UPDATING TEMPLATE FORMULAS")
print("="*80)

# Step 1: Remove formulas from TERM1 (PHP will populate it)
print("\nStep 1: Clearing TERM1 formulas...")
removed = remove_term1_formulas()

# Step 2: Update other sheets to reference TERM1
print("\nStep 2: Updating formulas in other sheets...")
total_updated = 0

sheets_to_update = [
    (1, 'INPUT'),
    (3, 'TERM2'),
    (4, 'TERM3'),
    (5, 'SUMMARY OF GRADES')
]

for sheet_num, sheet_name in sheets_to_update:
    updated = update_sheet_formulas(sheet_num, sheet_name)
    total_updated += updated

# Step 3: Re-zip the Excel file
print("\nStep 3: Re-zipping Excel file...")
with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
    for root_dir, dirs, files in os.walk(temp_dir):
        for file in files:
            file_path = os.path.join(root_dir, file)
            arc_name = os.path.relpath(file_path, temp_dir)
            zipf.write(file_path, arc_name)

print(f"Created new template: {output_path}")

# Cleanup
shutil.rmtree(temp_dir)
print("Cleaned up temp directory")

print("\n" + "="*80)
print("SUMMARY")
print("="*80)
print(f"Original template: {template_path}")
print(f"Backup saved to: {backup_path}")
print(f"New template: {output_path}")
print(f"\nChanges made:")
print(f"  - TERM1: Removed {removed} formulas (now empty for PHP)")
print(f"  - Other sheets: Updated {total_updated} formulas (INPUT! → TERM1!)")
print(f"\nResult:")
print(f"  ✓ TERM1 is now the master sheet (no formulas, PHP will populate)")
print(f"  ✓ INPUT/TERM2/TERM3/SUMMARY now reference TERM1")
print(f"\nNext steps:")
print(f"  1. Replace old template with new one (or rename)")
print(f"  2. Upload PHP code changes to server")
print(f"  3. Test Excel generation")
print("="*80)
