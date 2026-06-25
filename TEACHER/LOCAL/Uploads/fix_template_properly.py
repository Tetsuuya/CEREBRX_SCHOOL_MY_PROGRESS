import zipfile
import xml.etree.ElementTree as ET
import os
import shutil
import re

# Use the original ULTRA_OPTIMIZED file
template_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_ULTRA_OPTIMIZED.xlsx'
output_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_FIXED.xlsx'
backup_path = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\BACKUP_BEFORE_FIX.xlsx'

# Backup first
import shutil as shutil_module
shutil_module.copy2(template_path, backup_path)
print(f"Backed up to: {backup_path}")

# Extract Excel to temp directory
temp_dir = 'temp_excel_fix'
if os.path.exists(temp_dir):
    shutil.rmtree(temp_dir)

with zipfile.ZipFile(template_path, 'r') as zip_ref:
    zip_ref.extractall(temp_dir)

print("="*80)
print("FIXING TEMPLATE PROPERLY")
print("="*80)
print(f"Working from: {template_path}")

# Read and modify XML as raw text (safer than ElementTree for complex Excel XML)
def update_formulas_in_sheet(sheet_file, sheet_name):
    """Update formulas by simple text replacement in XML"""
    if not os.path.exists(sheet_file):
        print(f"  {sheet_name}: File not found, skipping")
        return 0
    
    with open(sheet_file, 'r', encoding='utf-8') as f:
        xml_content = f.read()
    
    # Count and replace INPUT! with TERM1! in formulas
    original = xml_content
    xml_content = xml_content.replace('INPUT!', 'TERM1!')
    
    changes = original.count('INPUT!')
    
    if changes > 0:
        with open(sheet_file, 'w', encoding='utf-8') as f:
            f.write(xml_content)
        print(f"  {sheet_name}: Changed {changes} formula references (INPUT! → TERM1!)")
    else:
        print(f"  {sheet_name}: No INPUT! references found")
    
    return changes

def remove_formulas_from_term1(sheet_file):
    """Remove formulas from TERM1 cells so PHP can populate them"""
    if not os.path.exists(sheet_file):
        print("  TERM1: File not found!")
        return 0
    
    with open(sheet_file, 'r', encoding='utf-8') as f:
        xml_content = f.read()
    
    # Use regex to remove formula tags but keep cell structure
    # Pattern: <f ...>formula content</f>
    original = xml_content
    xml_content = re.sub(r'<f[^>]*>.*?</f>', '', xml_content)
    
    # Also remove cached values <v>value</v> after formulas
    # This ensures cells are truly empty
    xml_content = re.sub(r'<v>[^<]*</v>', '', xml_content)
    
    changes = original.count('<f')
    
    if changes > 0:
        with open(sheet_file, 'w', encoding='utf-8') as f:
            f.write(xml_content)
        print(f"  TERM1: Removed {changes} formulas (cells now empty for PHP)")
    else:
        print("  TERM1: No formulas found")
    
    return changes

# Step 1: Remove formulas from TERM1
print("\nStep 1: Clearing TERM1 formulas...")
term1_file = f'{temp_dir}/xl/worksheets/sheet2.xml'
removed = remove_formulas_from_term1(term1_file)

# Step 2: Update formulas in other sheets
print("\nStep 2: Updating formula references in other sheets...")
sheets = [
    (f'{temp_dir}/xl/worksheets/sheet1.xml', 'INPUT (sheet1)'),
    (f'{temp_dir}/xl/worksheets/sheet3.xml', 'TERM2 (sheet3)'),
    (f'{temp_dir}/xl/worksheets/sheet4.xml', 'TERM3 (sheet4)'),
    (f'{temp_dir}/xl/worksheets/sheet5.xml', 'SUMMARY (sheet5)'),
]

total_changes = 0
for sheet_file, sheet_name in sheets:
    changes = update_formulas_in_sheet(sheet_file, sheet_name)
    total_changes += changes

# Step 3: Re-zip
print("\nStep 3: Creating new template...")
with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED, compresslevel=9) as zipf:
    for root_dir, dirs, files in os.walk(temp_dir):
        for file in files:
            file_path = os.path.join(root_dir, file)
            arc_name = os.path.relpath(file_path, temp_dir)
            zipf.write(file_path, arc_name)

print(f"Created: {output_path}")

# Cleanup
shutil.rmtree(temp_dir)
print("Cleaned up temp files")

# Summary
print("\n" + "="*80)
print("SUMMARY")
print("="*80)
print(f"✓ TERM1: Removed {removed} formulas (empty for PHP to populate)")
print(f"✓ Other sheets: Updated {total_changes} formula references (INPUT! → TERM1!)")
print(f"\nNew template: {output_path}")
print("\nStructure:")
print("  PHP → TERM1 (writes IDs + Names)")
print("        ↓")
print("  INPUT/TERM2/TERM3/SUMMARY (reference TERM1)")
print("="*80)
