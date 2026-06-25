"""
Advanced Template Analyzer
Provides detailed breakdown of what's consuming space in the Excel file
and identifies specific optimization opportunities
"""

import zipfile
import re
from collections import defaultdict

def analyze_xml_content(xml_data, filename):
    """Analyze XML content for optimization opportunities"""
    text = xml_data.decode('utf-8', errors='ignore')
    
    analysis = {
        'filename': filename,
        'total_size': len(xml_data),
        'formulas': len(re.findall(r'<f>', text)),
        'cached_values': len(re.findall(r'</f>\s*<v>', text)),
        'cells': len(re.findall(r'<c r=', text)),
        'styles': len(re.findall(r'<xf ', text)),
        'conditional_formats': len(re.findall(r'<conditionalFormatting', text)),
        'merged_cells': len(re.findall(r'<mergeCell', text)),
        'has_printer_settings': '<pageSetup' in text,
        'has_calc_always': 'ca="1"' in text,
    }
    
    # Estimate optimization potential
    cached_value_size = len(re.findall(r'</f>\s*<v>[^<]*</v>', text))
    if analysis['cached_values'] > 0:
        # Rough estimate: average 20 bytes per cached value
        analysis['cached_value_bloat'] = analysis['cached_values'] * 20
    else:
        analysis['cached_value_bloat'] = 0
    
    return analysis

def main():
    filepath = r'3-term_New_Grade_Template_FINAL.xlsx'
    
    print("=" * 100)
    print("ADVANCED TEMPLATE ANALYZER - Detailed Space Consumption Report")
    print("=" * 100)
    print()
    
    # Overall file structure
    print("FILE STRUCTURE BREAKDOWN:")
    print("-" * 100)
    
    file_sizes = []
    
    with zipfile.ZipFile(filepath, 'r') as z:
        total_compressed = 0
        total_uncompressed = 0
        
        # Group files by category
        categories = defaultdict(list)
        
        for item in z.infolist():
            compressed = item.compress_size
            uncompressed = item.file_size
            total_compressed += compressed
            total_uncompressed += uncompressed
            
            # Categorize
            if 'worksheet' in item.filename:
                category = 'Worksheets'
            elif 'style' in item.filename or 'theme' in item.filename:
                category = 'Styles/Themes'
            elif 'drawing' in item.filename or 'chart' in item.filename:
                category = 'Drawings/Charts'
            elif 'media' in item.filename:
                category = 'Media/Images'
            elif 'printerSettings' in item.filename:
                category = 'Printer Settings'
            elif 'calcChain' in item.filename:
                category = 'Calc Chain'
            elif item.filename.endswith('.xml.rels') or item.filename.endswith('.rels'):
                category = 'Relationships'
            else:
                category = 'Other/Metadata'
            
            categories[category].append({
                'filename': item.filename,
                'compressed': compressed,
                'uncompressed': uncompressed,
                'ratio': (compressed / uncompressed * 100) if uncompressed > 0 else 0
            })
        
        # Print by category
        for category in sorted(categories.keys()):
            files = categories[category]
            cat_compressed = sum(f['compressed'] for f in files)
            cat_uncompressed = sum(f['uncompressed'] for f in files)
            cat_ratio = (cat_compressed / cat_uncompressed * 100) if cat_uncompressed > 0 else 0
            
            print(f"\n{category}:")
            print(f"  Files: {len(files)}")
            print(f"  Compressed:   {cat_compressed:>10,} bytes ({cat_compressed/1024:>7.1f} KB)")
            print(f"  Uncompressed: {cat_uncompressed:>10,} bytes ({cat_uncompressed/1024:>7.1f} KB)")
            print(f"  Compression:  {cat_ratio:>6.1f}%")
            print(f"  % of total:   {(cat_uncompressed/total_uncompressed*100):>6.1f}%")
            
            # Show top files in this category
            if len(files) > 1:
                top_files = sorted(files, key=lambda x: x['uncompressed'], reverse=True)[:3]
                print(f"  Largest files:")
                for f in top_files:
                    print(f"    - {f['filename']:<50} {f['uncompressed']:>10,} bytes")
        
        print("\n" + "=" * 100)
        print("WORKSHEET ANALYSIS:")
        print("-" * 100)
        
        # Analyze each worksheet
        for i in range(1, 10):  # Assuming max 9 sheets
            sheet_file = f'xl/worksheets/sheet{i}.xml'
            try:
                data = z.read(sheet_file)
                analysis = analyze_xml_content(data, sheet_file)
                
                print(f"\nSheet {i}:")
                print(f"  Total size:           {analysis['total_size']:>10,} bytes ({analysis['total_size']/1024:>7.1f} KB)")
                print(f"  Cells:                {analysis['cells']:>10,}")
                print(f"  Formulas:             {analysis['formulas']:>10,}")
                print(f"  Cached formula values:{analysis['cached_values']:>10,}")
                print(f"  Merged cells:         {analysis['merged_cells']:>10,}")
                print(f"  Conditional formats:  {analysis['conditional_formats']:>10,}")
                print(f"  Printer settings:     {'Yes' if analysis['has_printer_settings'] else 'No'}")
                print(f"  Calc always flags:    {'Yes' if analysis['has_calc_always'] else 'No'}")
                
                if analysis['cached_value_bloat'] > 0:
                    print(f"  Est. cached bloat:    {analysis['cached_value_bloat']:>10,} bytes ({analysis['cached_value_bloat']/1024:>7.1f} KB)")
                    print(f"                        ⚠ Can be removed for {(analysis['cached_value_bloat']/analysis['total_size']*100):.1f}% size reduction")
                
            except KeyError:
                break  # No more sheets
        
        print("\n" + "=" * 100)
        print("OPTIMIZATION RECOMMENDATIONS:")
        print("-" * 100)
        
        # Check for calcChain
        try:
            calc_chain = z.read('xl/calcChain.xml')
            print(f"✓ REMOVE calcChain.xml - Save {len(calc_chain):,} bytes uncompressed")
        except KeyError:
            print(f"✓ calcChain.xml already removed")
        
        # Check for printer settings
        printer_files = [f for f in z.namelist() if 'printerSettings' in f]
        if printer_files:
            printer_size = sum(z.getinfo(f).file_size for f in printer_files)
            print(f"✓ REMOVE printer settings - Save ~{printer_size:,} bytes")
        
        # Total optimization potential
        print(f"\n✓ STRIP cached formula values - Save 20-40% of worksheet size")
        print(f"✓ MAX compression (level 9) - Additional 5-10% reduction")
        
        print("\n" + "=" * 100)
        print(f"Total File Size (compressed):   {total_compressed:>12,} bytes ({total_compressed/1024/1024:>6.2f} MB)")
        print(f"Total File Size (uncompressed): {total_uncompressed:>12,} bytes ({total_uncompressed/1024/1024:>6.2f} MB)")
        print(f"Overall compression ratio:      {(total_compressed/total_uncompressed*100):>12.1f}%")
        print("=" * 100)

if __name__ == '__main__':
    main()
