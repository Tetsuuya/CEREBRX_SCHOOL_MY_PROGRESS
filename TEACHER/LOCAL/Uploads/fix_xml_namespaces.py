import zipfile
import re
import os
import shutil
import xml.etree.ElementTree as ET

src_path = r"c:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template.xlsx"
dest_path = r"c:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_CLEAN.xlsx"
temp_dir = r"c:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\temp_xlsx_extract"

if os.path.exists(temp_dir):
    shutil.rmtree(temp_dir)
os.makedirs(temp_dir)

# Helper functions for column conversion
def col_to_num(col_str):
    num = 0
    for c in col_str:
        num = num * 26 + (ord(c.upper()) - ord('A') + 1)
    return num

def split_ref(cell_ref):
    match = re.match(r"^([A-Z]+)([0-9]+)$", cell_ref, re.I)
    if match:
        return match.group(1), int(match.group(2))
    return None, None

# 1. Unzip the file
print("Extracting XLSX...")
with zipfile.ZipFile(src_path, "r") as z:
    z.extractall(temp_dir)

# Register namespaces
namespaces = {
    "": "http://schemas.openxmlformats.org/spreadsheetml/2006/main",
    "r": "http://schemas.openxmlformats.org/officeDocument/2006/relationships",
    "mc": "http://schemas.openxmlformats.org/markup-compatibility/2006",
    "x14ac": "http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac",
    "xr": "http://schemas.microsoft.com/office/spreadsheetml/2014/revision",
    "xr2": "http://schemas.microsoft.com/office/spreadsheetml/2015/revision2",
    "xr3": "http://schemas.microsoft.com/office/spreadsheetml/2016/revision3"
}
for prefix, uri in namespaces.items():
    ET.register_namespace(prefix, uri)

# 2. Process each worksheet XML file
ws_dir = os.path.join(temp_dir, "xl", "worksheets")
for filename in os.listdir(ws_dir):
    if filename.endswith(".xml") and filename.startswith("sheet"):
        file_path = os.path.join(ws_dir, filename)
        print(f"Cleaning {filename}...")
        
        tree = ET.parse(file_path)
        root = tree.getroot()
        ns = "{http://schemas.openxmlformats.org/spreadsheetml/2006/main}"
        
        # A. Update dimension ref (e.g. A1:XFD118 -> A1:BH118)
        dim = root.find(f"{ns}dimension")
        if dim is not None:
            ref = dim.get("ref", "")
            if ":" in ref:
                start, end = ref.split(":")
                col_letter, row_num = split_ref(end)
                if col_letter and col_to_num(col_letter) > 60:
                    dim.set("ref", f"{start}:BH{row_num}")
                    print(f"  Updated dimension to BH{row_num}")
        
        # B. Clean <cols> column definitions
        cols_elem = root.find(f"{ns}cols")
        if cols_elem is not None:
            cols_to_remove = []
            for col in cols_elem.findall(f"{ns}col"):
                c_min = int(col.get("min", "0"))
                c_max = int(col.get("max", "0"))
                if c_min > 60:
                    cols_to_remove.append(col)
                elif c_max > 60:
                    col.set("max", "60")
            for col in cols_to_remove:
                cols_elem.remove(col)
            
        # C. Clean rows and cells
        sheet_data = root.find(f"{ns}sheetData")
        if sheet_data is not None:
            for row in sheet_data.findall(f"{ns}row"):
                spans = row.get("spans", "")
                if spans and ":" in spans:
                    s_start, s_end = spans.split(":")
                    if int(s_end) > 60:
                        row.set("spans", f"{s_start}:60")
                
                cells_to_remove = []
                for cell in row.findall(f"{ns}c"):
                    r_ref = cell.get("r", "")
                    col_let, _ = split_ref(r_ref)
                    if col_let and col_to_num(col_let) > 60:
                        cells_to_remove.append(cell)
                for cell in cells_to_remove:
                    row.remove(cell)
        
        # E. Clean <mergeCells> definitions
        merge_cells = root.find(f"{ns}mergeCells")
        if merge_cells is not None:
            mc_to_remove = []
            for mc in merge_cells.findall(f"{ns}mergeCell"):
                ref = mc.get("ref", "")
                if ":" in ref:
                    start, end = ref.split(":")
                    c_let, _ = split_ref(end)
                    if c_let and col_to_num(c_let) > 60:
                        mc_to_remove.append(mc)
            for mc in mc_to_remove:
                merge_cells.remove(mc)
            # Update the count attribute of mergeCells
            merge_cells.set("count", str(len(merge_cells.findall(f"{ns}mergeCell"))))
            print(f"  Cleaned merge cells (removed {len(mc_to_remove)} bloated merges).")
            
        # Save XML file
        tree.write(file_path, encoding="utf-8", xml_declaration=True)
        
        # D. Post-process XML file to inject only missing namespaces to <worksheet>
        with open(file_path, "r", encoding="utf-8") as f:
            content = f.read()
        
        # Find where <worksheet tag starts
        worksheet_tag_match = re.search(r"<worksheet([^>]*)>", content)
        if worksheet_tag_match:
            orig_attrs = worksheet_tag_match.group(1)
            new_attrs = orig_attrs
            
            # Register namespaces to inject
            for ns_prefix, ns_uri in [
                ("r", "http://schemas.openxmlformats.org/officeDocument/2006/relationships"),
                ("x14ac", "http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac"),
                ("xr2", "http://schemas.microsoft.com/office/spreadsheetml/2015/revision2"),
                ("xr3", "http://schemas.microsoft.com/office/spreadsheetml/2016/revision3")
            ]:
                if f"xmlns:{ns_prefix}=" not in orig_attrs:
                    new_attrs = f' xmlns:{ns_prefix}="{ns_uri}"' + new_attrs
            
            content = content.replace(worksheet_tag_match.group(0), f"<worksheet{new_attrs}>")
        
        with open(file_path, "w", encoding="utf-8") as f:
            f.write(content)
        print(f"  Processed namespaces for {filename}")

# 3. Zip everything back
print("Re-packing cleaned files into XLSX...")
if os.path.exists(dest_path):
    os.remove(dest_path)

with zipfile.ZipFile(dest_path, "w", zipfile.ZIP_DEFLATED) as z:
    for root_dir, dirs, files in os.walk(temp_dir):
        for file in files:
            full_path = os.path.join(root_dir, file)
            rel_path = os.path.relpath(full_path, temp_dir)
            z.write(full_path, rel_path)

shutil.rmtree(temp_dir)
print("XLSX re-packing complete.")
print(f"Cleaned file size: {os.path.getsize(dest_path) / 1024:.2f} KB")
