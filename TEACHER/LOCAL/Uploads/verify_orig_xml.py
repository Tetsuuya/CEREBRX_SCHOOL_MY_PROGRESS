import zipfile
import os

file_path = r"c:\Users\Rhenel Jhon Sajol\Desktop\CEREB_SCHOOL_BACKUP\TEACHER\LOCAL\Uploads\3-term_New_Grade_Template.xlsx"

with zipfile.ZipFile(file_path, "r") as z:
    for name in z.namelist():
        if "sheet1.xml" in name:
            print(f"Found {name} inside original zip.")
            xml_content = z.read(name).decode("utf-8")
            print("--- First 1000 characters of original sheet1.xml ---")
            print(xml_content[:1000])
            break
