"""
Find out WHY sheet2 XML is 611KB vs sheet1 at 113KB.
Look at the actual per-cell XML size and find the bulk.
"""
import zipfile, re

path = r'3-term_New_Grade_Template_FINAL.xlsx'

with zipfile.ZipFile(path, 'r') as z:
    s1 = z.read('xl/worksheets/sheet1.xml').decode('utf-8')
    s2 = z.read('xl/worksheets/sheet2.xml').decode('utf-8')

# Extract all individual cell XML blocks from sheet2
cells_s2 = re.findall(r'<c r="[^"]*"[^/]*>.*?</c>', s2, re.DOTALL)
cells_s1 = re.findall(r'<c r="[^"]*"[^/]*>.*?</c>', s1, re.DOTALL)

print(f'Sheet1: {len(cells_s1)} cells, avg {sum(len(c) for c in cells_s1)//len(cells_s1) if cells_s1 else 0} bytes/cell')
print(f'Sheet2: {len(cells_s2)} cells, avg {sum(len(c) for c in cells_s2)//len(cells_s2) if cells_s2 else 0} bytes/cell')

# Show the biggest cells in sheet2
cells_by_size = sorted(cells_s2, key=len, reverse=True)[:5]
print(f'\nTop 5 largest cells in sheet2:')
for c in cells_by_size:
    ref = re.search(r'r="([^"]*)"', c)
    print(f'  {ref.group(1) if ref else "?"}: {len(c)} bytes | {c[:200]}')

# Check if there are long formula strings
formulas = re.findall(r'<f>([^<]+)</f>', s2)
if formulas:
    long_formulas = sorted(formulas, key=len, reverse=True)[:3]
    print(f'\nLongest formulas in sheet2:')
    for f in long_formulas:
        print(f'  [{len(f)} chars]: {f[:300]}')

# What's the non-cell content size?
non_cell_s2 = re.sub(r'<c r="[^"]*"[^/]*>.*?</c>', '', s2, flags=re.DOTALL)
print(f'\nNon-cell XML in sheet2: {len(non_cell_s2):,} bytes')
print(f'All cell XML in sheet2: {sum(len(c) for c in cells_s2):,} bytes')
