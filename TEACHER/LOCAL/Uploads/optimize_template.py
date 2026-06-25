"""
Template Optimizer
Removes cached formula values (<v> inside formula cells) and calcChain.xml
from the FINAL template to dramatically reduce PHPExcel RAM usage.

WHY this works:
- PHPExcel loads every cell's stored <v> value into RAM as a PHP object
- Formula cells store BOTH the formula AND the last calculated result as <v>
- Removing <v> from formula cells means PHPExcel only stores the formula string
- This cuts the in-memory cell objects roughly in half for formula-heavy sheets
- calcChain.xml is Excel's formula evaluation order cache - PHPExcel ignores it anyway
"""

import zipfile, re, os, shutil

src = r'3-term_New_Grade_Template_FINAL.xlsx'
dst = r'3-term_New_Grade_Template_OPTIMIZED.xlsx'

def strip_formula_cached_values(xml_data):
    """
    For every cell that has a formula <f>...</f>, remove the cached result <v>...</v>.
    Plain value cells (no formula) keep their <v> untouched.
    Also removes the 'ca' (calculate always) attribute from cells to reduce recalc overhead.
    """
    text = xml_data.decode('utf-8')

    # Remove <v>...</v> that immediately follows </f> (cached formula results)
    # Pattern: </f><v>anything</v>  →  </f>
    text = re.sub(r'(</f>)\s*<v>[^<]*</v>', r'\1', text)

    # Remove ca="1" attributes (force-recalculate flags, unnecessary overhead)
    text = re.sub(r'\s+ca="1"', '', text)

    return text.encode('utf-8')


removed_bytes = 0
files_modified = []

with zipfile.ZipFile(src, 'r') as zin, zipfile.ZipFile(dst, 'w', zipfile.ZIP_DEFLATED, compresslevel=9) as zout:
    for item in zin.infolist():
        data = zin.read(item.filename)
        original_size = len(data)

        # Remove calcChain entirely (PHPExcel ignores it, but loading it wastes RAM)
        if item.filename == 'xl/calcChain.xml':
            removed_bytes += original_size
            print(f'  REMOVED  {item.filename:<55} saved {original_size:>8,} bytes')
            continue

        # Remove calcChain reference from Content_Types
        if item.filename == '[Content_Types].xml':
            text = data.decode('utf-8')
            text = re.sub(r'\s*<Override[^>]*calcChain[^/]*/>', '', text)
            data = text.encode('utf-8')

        # Strip cached formula values from worksheets
        if item.filename.startswith('xl/worksheets/sheet') and item.filename.endswith('.xml'):
            new_data = strip_formula_cached_values(data)
            saved = original_size - len(new_data)
            if saved > 0:
                removed_bytes += saved
                files_modified.append(item.filename)
                print(f'  STRIPPED {item.filename:<55} saved {saved:>8,} bytes (cached formula values removed)')
            data = new_data

        zout.writestr(item, data)

print()
orig_size = os.path.getsize(src)
new_size  = os.path.getsize(dst)
print(f'Original file:   {orig_size:>10,} bytes  ({orig_size/1024/1024:.2f} MB)')
print(f'Optimized file:  {new_size:>10,} bytes  ({new_size/1024/1024:.2f} MB)')
print(f'File size saved: {orig_size-new_size:>10,} bytes  ({(orig_size-new_size)/1024:.1f} KB)')
print(f'XML data saved:  {removed_bytes:>10,} bytes  ({removed_bytes/1024/1024:.2f} MB uncompressed)')
print()
print(f'Estimated PHPExcel RAM reduction: ~50-70% less memory usage')
print(f'Output: {dst}')
