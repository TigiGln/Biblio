#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Created on Wed May 12 2021
Updated on Wed May 12 2021
@author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
@dependency python >= 3.6, pdfminer.six (included)
"""

import io
import sys
from pdfminer.converter import TextConverter, XMLConverter, HTMLConverter
from pdfminer.pdfinterp import PDFPageInterpreter
from pdfminer.pdfinterp import PDFResourceManager
from pdfminer.pdfpage import PDFPage

def extract_text_by_page(pdf_path):
    with open(pdf_path, 'rb') as fh:
        for page in PDFPage.get_pages(fh, 
                                      caching=True,
                                      check_extractable=True):
            resource_manager = PDFResourceManager()
            fake_file_handle = io.StringIO()
            converter = HTMLConverter(resource_manager, fake_file_handle)
            page_interpreter = PDFPageInterpreter(resource_manager, converter)
            page_interpreter.process_page(page)
            
            text = fake_file_handle.getvalue()
            yield text
    
            # close open handles
            converter.close()
            fake_file_handle.close()
    
def extract_text(pdf_path):
    for page in extract_text_by_page(pdf_path):
        with io.open('testpython.html', "a", encoding="utf-8") as f:
            f.write(page)
        
if __name__ == '__main__':
    if(len(sys.argv) != 3):
        print("Please provide two parameters: the pdf file and the format to convert (text, xml, html)")
    else:
        print(extract_text(sys.argv[1]))
        
