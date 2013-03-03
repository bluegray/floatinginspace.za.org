#!/usr/bin/python

################################################################################
#    Copyright (c) 2005  Francois du Toit.
#
#    This program is free software; you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation; either version 2 of the License, or
#    at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program; if not, write to the Free Software
#    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
################################################################################

import os, cgi, time, re, string
#import cgitb; cgitb.enable()

# Location of the apache access log file on server
logfile = '/var/log/httpd/access_log'

#host without slashes or http:// (will only be used if environment var is not available
host = 'www.floatinginspace.za.org'

# Maximum number of entries
max = 20

# Case insensitive string to filter for in logs
search_string = 'googlebot/'

# adjust time from GMT 
time_adjust = '2'

RSS_TEMPLATE = """<?xml version="1.0" encoding="utf-8"?>
<?xml-stylesheet type="text/css" href="http://www.floatinginspace.za.org/rss.css" ?>
<rss version="2.0">
    <channel>
	<title>GoogleBot log entries for %(HOST)s</title>
	<link>http://%(HOST)s/</link>
	<description>Latest crawls by GoogleBot</description>
	<lastBuildDate>%(DATE)s</lastBuildDate>
	<language>en</language>
%(ITEMS)s

    </channel>
</rss>"""

ITEM_TEMPLATE = '''
	<item>
	    <title>%(URL)s on %(LOCALDATE)s</title>
	    <link>http://%(HOST)s%(URL)s</link>
	    <pubDate>%(DATE)s</pubDate>
	    <description>%(DATA)s</description>
	</item>'''

LOG_TIME_FORMAT = "%d/%b/%Y:%H:%M:%S"

datereg = re.compile('\[([0-9]+/.../[0-9][0-9][0-9][0-9]:[0-9][0-9]:[0-9][0-9]:[0-9][0-9]) (.[0-9][0-9][0-9][0-9])\]')
logreg = re.compile('"(GET|HEAD) (\S+) \S+" \d+')

def log2local(str, adj):
    apachedate = datereg.search(str)
    dated = apachedate.group(1)
    timezoned = apachedate.group(2)

    logtime = time.strptime(dated, LOG_TIME_FORMAT) #local time in timetuple
    ucttime = time.mktime(logtime) #utc time in seconds

    heretz = adj*60*60 #the timezone you want eg. SAST = +2
    mytime = ucttime + heretz

    newdate = '[%s %+05d]' %(time.strftime(LOG_TIME_FORMAT, time.gmtime(mytime)),adj*100)
    newdatestr = datereg.sub(newdate, str)
    return newdatestr

def getlogentrydate(str):
    apachedate = datereg.search(str)
    dated = apachedate.group(1)
    timezoned = apachedate.group(2)

    logtime = time.strptime(dated, LOG_TIME_FORMAT) #local time in timetuple
    ucttime = time.mktime(logtime) #utc time in seconds
    return float(ucttime)

def getlogurl(str):
    logurl = logreg.search(str).group(2)
    return logurl

def print_rss (items, date):
    if os.getenv('HTTP_USER_AGENT', 'N/A').find('Mozilla') >= 0:
	print "content-type: application/xml\nCache-Control: no-cache\n"
    else:
	print "content-type: application/rss+xml\nCache-Control: no-cache\n"    
    print RSS_TEMPLATE % {
	'SCRIPT_NAME':os.getenv('SCRIPT_NAME',' '),
	'DATE':date, 'ITEMS':items,
	'HOST':os.getenv('HTTP_HOST',host)
	}

################################ start of main program ################################

str = ''
items = []
latest = 0.0
output = []

form = cgi.FieldStorage()
arg = form.getfirst('search', search_string)
adjt = form.getfirst('adjust', time_adjust)

norm = string.maketrans('', '') #builds list of all characters
# remove shell control characters just in case 
arg = string.translate(arg, norm, r'\;&|()')
adjt = string.translate(adjt, norm, r'\;&|()')

#first of two methods to get logfile
lfile = open(logfile,'r')
loglines = lfile.readlines()
lfile.close()
for line in loglines:
    if line.lower().find(arg) >= 0:
	output.append(line)

#second method
#cmd = 'grep -i %s %s 2>&1' %(arg, logfile)
#output = os.popen(cmd).readlines()

for x in output:
    entrytime = getlogentrydate(x)
    if entrytime > latest:
        latest = entrytime
    items.append(  ITEM_TEMPLATE %{
	    'DATA':cgi.escape(log2local(x,int(adjt)).rstrip()),
	    'DATE':time.strftime('%a, %d %b %Y %H:%M:%S GMT',time.gmtime(entrytime)),
	    'LOCALDATE':time.strftime('%a, %d %b %Y %H:%M:%S ',time.gmtime(entrytime+60*60*int(adjt))) + '%+05d' %(int(adjt)*100),
	    'HOST':os.getenv('HTTP_HOST',host),
	    'URL':getlogurl(x) }  )
 
items.reverse()
print_rss( ''.join(items[:max]) , time.strftime('%a, %d %b %Y %H:%M:%S GMT',time.gmtime(latest))   )

