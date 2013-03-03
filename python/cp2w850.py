---
title: cp2w850.py
---

#!/usr/bin/python
# -*- coding: utf-8 -*-

import re, os, sys, avkutil, eyeD3, getopt

def run(command, message='Success!'):
    cmd = os.popen(command)
    output = cmd.read()
    exitcode = cmd.close()
    if exitcode:
        print avkutil.color('Something happened','lred')
        print avkutil.color('exit code : ' + `exitcode` + '\n','lred')
    else:
        print avkutil.color(message + '\n','lgreen')
        exitcode = 0
    return output, exitcode
######################################################################


id3version = eyeD3.ID3_V2_4		# ID3V2.3 and ID3V2.4 works fine
id3encoding = eyeD3.LATIN1_ENCODING     # So far the best option for Sony Ericsson w850i
#id3encoding = eyeD3.UTF_8_ENCODING     # bug in w850i - does not handle UTF-8 correctly
#id3encoding = eyeD3.UTF_16_ENCODING	# bug in w850i - UTF-16 works fine, but tracknumbers are screwed up
#id3encoding = eyeD3.UTF_16BE_ENCODING  # bug in w850i - UTF-16BE will cause phone to reboot repeatedly 

debug = 0
donotask = 0
oldversion = {}
directory = ''
reencode = 0
addimage = 0
sort = 0

try:
    optlist, extra = getopt.gnu_getopt(sys.argv[1:], 'sidyC:e')
except getopt.GetoptError, msg:
    sys.stderr.write(str(msg) + '\n')
    sys.exit()

for option, value in optlist:
    if option == '-d':
        debug = 1
    elif option == '-i':
        addimage = 1	
    elif option == '-e':
        reencode = 1
    elif option == '-s':
        sort = 1
    elif option == '-C':
        directory = value	
    elif option == '-y':
        donotask = 1


# get the file list from cmdline or all in directory
if extra:
    files = extra
else:
    fileExtList = ['.mp3','.MP3','.Mp3','.mP3']
    files = [f for f in os.listdir(os.getcwd()) if os.path.isfile(f)]
    files = [f for f in files if os.path.splitext(f)[1] in fileExtList]
 
files.sort()


# get id3 tag    
tags = {}
for x in files:
    tag = eyeD3.Tag()
    try:
	#audioFile = eyeD3.Mp3AudioFile(x)
	#for key in audioFile.lameTag.keys():
	#    if key == 'replaygain':
	#	for k in audioFile.lameTag[key]:
	#	    print  k, audioFile.lameTag[key][k]
	#    print key, audioFile.lameTag[key]

        tag.link(x)
	oldversion[x] = tag.getVersionStr()
	#tag.setVersion(id3version)
	#tag.setTextEncoding(id3encoding)
    except eyeD3.tag.TagException, value:
	print avkutil.color(value,'lred')
    except UnicodeDecodeError, value:
	print avkutil.color(value,'lred')
    except:
	oldversion[x] = '????'

    tags[x] = tag

# ask if we should continue with writing the tags
if not donotask:
    confirm = raw_input(avkutil.color("\nDo you want to retag (and reencode)? ",'yellow'))
else:
    confirm = 'y'

# write new tag to files
if confirm == 'y':
    for x in files:    
	newmp3file = x

	tagx = eyeD3.Tag()
	tagx = tags[x]

	#reencode if nessacary see http://www.hydrogenaudio.org/forums/index.php?showtopic=28124 for good lame settings
	# VBR -V6 is about 115 kbit/s and --vbr-new is faster and better - more than enough for portables
	# -V7 is about 100 kbit/s - probably wont hear the difference
	if reencode:
	    if sort: 
		if not os.path.exists(directory): os.mkdir(directory)
		if not os.path.exists(os.path.join(directory, tagx.getArtist())): os.mkdir(os.path.join(directory, tagx.getArtist()))
		if not os.path.exists(os.path.join(directory, tagx.getArtist(), tagx.getAlbum())): os.mkdir(os.path.join(directory, tagx.getArtist(), tagx.getAlbum()))	    
		finaldir = os.path.join(directory, tagx.getArtist(), tagx.getAlbum())

	    old = os.path.abspath(x)
	    new = os.path.abspath(os.path.join(finaldir, x))

	    if old != new:
		newmp3file = new
	    else:
		newmp3file = new + '.vbrV6.mp3'
	    cmdstr = 'lame -V6 --vbr-new "%s" "%s"' %(old, newmp3file)
	    print avkutil.color('# '+cmdstr+'\n','white')
	    run(cmdstr)

	tagx.linkedFile.name = newmp3file
	tagx.addComment('Reencoded by lame with -V6 --vbr-new')

	fileList = ['.folder','folder','album']
	cover = [f for f in os.listdir(os.getcwd()) if os.path.isfile(f)]
	cover = [f for f in cover if os.path.splitext(f)[0] in fileList]
	if cover and addimage: tagx.addImage(eyeD3.ImageFrame.FRONT_COVER, cover[0])
	# bug in W850 - total track numebr is not recognised
	#tagx.setTrackNum([tagx.getTrackNum()[0],''])

	tagx.setVersion(id3version)
	tagx.setTextEncoding(id3encoding)
	# write a v2 tag
	tagx.update(id3version)	   
	if tagx.isV2():
	    newversion = tagx.getVersionStr()
	    #print dir(tagx)
	else:
	    newversion = '????'
	print avkutil.color('Converted from %s [%s] to %s [%s]\n' %(x, oldversion[x], newmp3file, newversion), 'yellow')
	# write a v1 tag too if you want
	tagx.update(eyeD3.ID3_V1_1)
	
else:
    print 'Did nothing'
	    

