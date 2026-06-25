import zipfile
import xml.etree.ElementTree as ET
import sys
import os

# UPDATE THIS PATH TO THE LATEST GENERATED FILE
if len(sys.argv) > 1:
    generated_file = sys.argv[1]
else:
    generated_file = r'C:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\downloads\generated_grades\TLE 7-Grade 7 CS3A-20260625015640.xlsx'

if not os.path.exists(generated_file):
    print(f"ERROR: File not found: {generated_file}")
    sys.exit(1)

print("="*70)
print(f"ANALYZING GENERATED FILE: {os.path.basename(generated_file)}")
print("="*70)

# Open as ZIP to read raw XML
with zipfile.ZipFile(generated_file, 'r') as zip_ref:
    sheets_to_check = {
        'xl/worksheets/sheet1.xml': 'INPUT',
        'xl/worksheets/sheet2.xml': 'TERM1', 
        'xl/worksheets/sheet3.xml': 'TERM2',
        'xl/worksheets/sheet4.xml': 'TERM3',
        'xl/worksheets/sheet5.xml': 'SUMMARY OF GRADES'
    }
    
    for xml_path, sheet_name in sheets_to_check.items():
        print(f"\nSheet: {sheet_name}")
        print("-" * 70)
        
        try:
            xml_content = zip_ref.read(xml_path)
            root = ET.fromstring(xml_content)
            
            # Find all row elements with hidden="1" or hidden="true"
            # Namespace for Excel XML
            ns = {'ns': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
            
            hidden_rows = []
            for row in root.findall('.//{http://schemas.openxmlformats.org/spreadsheetml/2006/main}row'):
                hidden_attr = row.get('hidden')
                row_num = row.get('r')
                
                if hidden_attr in ['1', 'true']:
                    hidden_rows.append(row_num)
            
            if hidden_rows:
                print(f"  Hidden rows found in XML: {', '.join(hidden_rows)}")
            else:
                print(f"  ✗ NO HIDDEN ROWS FOUND IN XML!")
                
        except Exception as e:
            print(f"  ERROR: {e}")

print("\n" + "="*70)
print("EXPECTED HIDDEN ROWS:")
print("="*70)
print("INPUT: 9, 11, 61, 63, 112")
print("TERM1: 12, 63, 65, 116")
print("TERM2: 12, 63, 65, 116")
print("TERM3: 12, 63, 65, 116")
print("SUMMARY OF GRADES: 13, 64, 66, 117")
