import openpyxl

file_path = r'c:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template_ULTRA_OPTIMIZED.xlsx'
wb = openpyxl.load_workbook(file_path, data_only=False)

print("Sheet Names:", wb.sheetnames)

for name in wb.sheetnames:
    ws = wb[name]
    print(f"\n--- Sheet: {name} ---")
    print(f"Dimensions: {ws.dimensions}")
    # Print the first 15 rows and first 10 columns
    for row in range(1, 20):
        row_vals = []
        for col in range(1, 15):
            cell = ws.cell(row=row, column=col)
            val = cell.value
            if val is not None:
                row_vals.append(f"{cell.coordinate}:{repr(val)}")
        if row_vals:
            print(f"Row {row:02d}: {', '.join(row_vals[:10])}")

wb.close()
