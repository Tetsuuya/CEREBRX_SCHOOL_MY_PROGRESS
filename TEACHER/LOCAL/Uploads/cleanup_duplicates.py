"""
Cleanup Script - Remove Duplicate Excel Templates
Keeps only the necessary files and removes intermediate versions
"""

import os
import shutil
from datetime import datetime

# Files to keep
KEEP_FILES = [
    '3-term_New_Grade_Template_FINAL.xlsx',  # Original template
    '3-term_New_Grade_Template_OPTIMIZED.xlsx',  # First optimization
    'GradingSheetTemplate.xlsx',  # Legacy template
]

# Files to archive (move to backup folder)
ARCHIVE_FILES = [
    '3-term_New_Grade_Template.xlsx',
    '3-term_New_Grade_Template_CLEAN.xlsx',
    '3-term_New_Grade_Template_CLEAN2.xlsx',
    '3-term_New_Grade_Template_UPLOAD_THIS.xlsx',
    'GradingTemplate.xlsx',
]

# Python scripts to keep (useful for future optimization)
KEEP_SCRIPTS = [
    'ultra_optimize.py',
    'optimize_template.py',
    'cleanup_duplicates.py',
]

def get_file_size(filepath):
    """Get file size in MB"""
    if os.path.exists(filepath):
        return os.path.getsize(filepath) / 1024 / 1024
    return 0

def main():
    print("=" * 80)
    print("CLEANUP SCRIPT - Duplicate Template Remover")
    print("=" * 80)
    print()
    
    # Create backup folder with timestamp
    backup_folder = f"BACKUP_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
    
    if not os.path.exists(backup_folder):
        os.makedirs(backup_folder)
        print(f"[CREATED] Backup folder: {backup_folder}")
    
    total_freed = 0
    archived_count = 0
    
    # List all Excel files
    excel_files = [f for f in os.listdir('.') if f.endswith('.xlsx')]
    
    print(f"\nFound {len(excel_files)} Excel files\n")
    print("Current Excel files:")
    print("-" * 80)
    
    for f in excel_files:
        size_mb = get_file_size(f)
        status = "KEEP" if f in KEEP_FILES else "ARCHIVE" if f in ARCHIVE_FILES else "UNKNOWN"
        print(f"  {status:<10} {f:<55} {size_mb:>6.2f} MB")
    
    print()
    proceed = input("Proceed with archiving duplicate files? (yes/no): ").strip().lower()
    
    if proceed != 'yes':
        print("Cancelled.")
        return
    
    print("\nArchiving files...")
    print("-" * 80)
    
    # Archive duplicate files
    for filename in ARCHIVE_FILES:
        if os.path.exists(filename):
            size_mb = get_file_size(filename)
            dest = os.path.join(backup_folder, filename)
            shutil.move(filename, dest)
            total_freed += size_mb
            archived_count += 1
            print(f"[ARCHIVED] {filename:<50} {size_mb:>6.2f} MB")
    
    # List Python scripts
    print("\n" + "=" * 80)
    print("Python Scripts Status:")
    print("-" * 80)
    
    py_files = [f for f in os.listdir('.') if f.endswith('.py')]
    for f in py_files:
        size_kb = os.path.getsize(f) / 1024
        status = "KEEP" if f in KEEP_SCRIPTS else "INFO"
        print(f"  {status:<10} {f:<55} {size_kb:>6.1f} KB")
    
    print("\n" + "=" * 80)
    print("CLEANUP SUMMARY")
    print("=" * 80)
    print(f"Files archived:     {archived_count}")
    print(f"Space freed:        {total_freed:.2f} MB")
    print(f"Backup location:    {backup_folder}/")
    print(f"\nKept files:")
    for f in KEEP_FILES:
        if os.path.exists(f):
            size_mb = get_file_size(f)
            print(f"  ✓ {f:<55} {size_mb:>6.2f} MB")
    print("=" * 80)

if __name__ == '__main__':
    main()
