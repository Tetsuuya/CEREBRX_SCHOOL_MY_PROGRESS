"""
Deep analysis - what exactly makes sheet2 so large vs sheet1?
"""
import zipfile, re

path = r'3-term_New_Grade_Template_FINAL.xlsx'

with zipfile.ZipFile(path, 'r') as z:
    for s in ['sheet1', 'sheet2']:
        data = z.read(f'xl/worksheets/{s}.xml').decode('utf-8')
        
        # Count cells with style (s=) attribute - each one PHPExcel loads as full style object
        styled_cells = len(re.findall(r'<c r="[^"]+" s="\d+"', data))
        # Count cells with value only
        cells_with_v = len(re.findall(r'<v>', data))
        # Count merged cells
        merges = len(re.findall(r'<mergeCell', data))
        # Count conditional formats
        condFmt = len(re.findall(r'<conditionalFormatting', data))
        # Count total <c tags
        total_cells = len(re.findall(r'<c r=', data))
        
        print(f'=== {s} ({len(data):,} bytes) ===')
        print(f'  Total cells:        {total_cells:>6}')
        print(f'  Styled cells (s=):  {styled_cells:>6}  <- each = 1 Style object in PHPExcel RAM')
        print(f'  Cells with values:  {cells_with_v:>6}')
        print(f'  Merged cells:       {merges:>6}')
        print(f'  Conditional fmts:   {condFmt:>6}')
        
        # Show sample of what a cell looks like
        sample = re.search(r'<c r="[^"]+"[^>]*>.*?</c>', data[:5000], re.DOTALL)
        if sample:
            print(f'  Sample cell: {sample.group()[:120]}')
        print()
