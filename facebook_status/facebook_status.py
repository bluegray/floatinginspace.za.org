# This class adds python functions to change your Facebook status message - v0.1
# Copyright (C) 2007  Francois du Toit <bluegraydragon@gmail.com>

# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

import os.path, sys, cookielib, urllib2, urllib, re


class fbStatus:

    def __init__(self):
	self.COOKIEFILE = 'cookies.lwp'
	self.urlopen = urllib2.urlopen
	self.cj = cookielib.LWPCookieJar()
	self.Request = urllib2.Request        

	if self.cj != None:                                 
	    if os.path.isfile(self.COOKIEFILE):
		self.cj.load(self.COOKIEFILE)
	    opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(self.cj))
	    urllib2.install_opener(opener)

    def get_url(self, url, data=None, headers={'User-agent' : 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0.666b1'}, read=0):
	try:
	    req = self.Request(url, data, headers)            
	    handle = self.urlopen(req)                         
	except IOError, e:
	    print 'We failed to open "%s".' % url
	    if hasattr(e, 'code'):
		print 'We failed with error code - %s.' % e.code
	    elif hasattr(e, 'reason'):
		print "The error object has the following 'reason' attribute :", e.reason
		print "This usually means the server doesn't exist, is down, or we don't have an internet connection."
		sys.exit()
	else:
	    print 'Here are the headers of the page :'
	    print handle.code, handle.msg
	    #print handle.info() 
	    if read and handle.code == 200:
		return handle.read()
	    else:
		return handle.code

    def change_status(self, email, password, status):

	# log in and get cookies, yum yum
	print 'changing Facebook status message'
	url = 'https://login.facebook.com/login.php?m&amp;next=http%3A%2F%2Fm.facebook.com%2Fhome.php'
	data = urllib.urlencode({'email': email, 'pass': password, 'login': 'Login'})
	self.get_url(url, data)
	# get post_form_id from page
	url = 'http://m.facebook.com/home.php'
	html = self.get_url(url, read=1)
	try:
	    id = re.search('post_form_id" value="([^"]*)"', html).group(1)
	except AttributeError:
	    print 'Could not find post_form_id in the page, maybe wrong user/pass?'
	    return 10
	print 'Got post_form_id:', id
	# POST status message
	data = urllib.urlencode({'post_form_id':id, 'status': status, 'update':'Update'})
	code = self.get_url(url, data)
	if code == 200:
	    print '>>> status successfully changed to: ', status
	else:
	    print '>>> got status code:', code
	print 
	self.print_cookies()

    def print_cookies(self):
	if self.cj == None:
	    print "We don't have a cookie library available - sorry."
	    print "I can't show you any cookies."
	else:
	    print 'These are the cookies we have received so far :'
	    for index, cookie in enumerate(self.cj):
		print index, '  :  ', cookie        
	    self.cj.save(self.COOKIEFILE) 
