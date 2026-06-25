import zipfile, re, os

path = r'3-term_New_Grade_Template_FINAL.xlsx'

with zipfile.ZipFile(path, 'r') as z:
    for s in ['sheet1','sheet2','sheet3','sheet4','sheet5']:
        data = z.read(f'xl/worksheets/{s}.xml').decode('utf-8')
        rows = re.findall(r'<row r="(\d+)"', data)
        formulas = len(re.findall(r'<f>', data))
        values   = len(re.findall(r'<v>', data))
        if rows:
            rows_int = [int(r) for r in rows]
            print(f'{s}: rows {min(rows_int)}..{max(rows_int)} | {formulas} formulas | {values} values | uncompressed {len(data):,} bytes')

        # Find dimension tag
        dim = re.search(r'<dimension ref="([^"]+)"', data)
        if dim:
            print(f'  dimension: {dim.group(1)}')

        # Find all unique column letters used
        cell_refs = re.findall(r'<c r="([A-Z]+)\d+"', data)
        if cell_refs:
            def col2num(col):
                n = 0
                for c in col:
                    n = n * 26 + ord(c) - 64
                return n
            cols = set(cell_refs)
            max_col_letter = max(cols, key=col2num)
            print(f'  Columns: {len(cols)} unique, max = {max_col_letter} (col #{col2num(max_col_letter)})')
