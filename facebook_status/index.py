#!/usr/bin/python

#import cgitb; cgitb.enable()

import re
from py2html import Parser

def getfile(file):
    f = open(file, 'r')
    html = f.read()
    f.close()
    return html


print 'Content-Type: text/html; charset=utf-8\nCache-Control: no-cache\n'

html = getfile('index.pyhtml')
head = getfile('header.pyhtml')
foot = getfile('footer.pyhtml')


pyre = re.compile('<pythoncode\s*/>', re.DOTALL)
pyrehead = re.compile('<pythonheader\s*/>', re.DOTALL)
pyrefoot = re.compile('<pythonfooter\s*/>', re.DOTALL)


f = open('facebook_status.py', 'r')
lines = f.readlines()
#python = f.read()
f.close()

python = ''
for line in lines:
    python += line.rstrip().replace(r'\n', 'newline') + '\n'

python_code = Parser(python).format()
python_code = python_code.replace('newline', r'\\n')
html = pyre.sub(python_code, html)
html = pyrehead.sub(head, html)
html = pyrefoot.sub(foot, html)

import os, time, os.path

file = 'google.log'
if 'HTTP_USER_AGENT' in os.environ.keys():
    f = open(file, 'a')
    if os.path.getsize(file) < 1024*512:
	f.write('%s - %s\n'%(time.ctime(), os.environ['HTTP_USER_AGENT']))
    f.close()

print html

