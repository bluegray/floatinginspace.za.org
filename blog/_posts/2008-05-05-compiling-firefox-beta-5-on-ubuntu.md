---
title: Compiling Firefox Beta 5 on Ubuntu Gutsy Gibbon with nicer fonts
author: bluegray
layout: post
permalink: /2008/internet/compiling-firefox-beta-5-on-ubuntu/93/
categories:
  - computer
  - fb
  - internet
---
# 

The firefox binaries available on Mozilla's site is compiled with a version of [cairo][1] that does a different type of [subpixel rendering][2] when [anti-aliasing][3] than what is used by the Ubuntu Gutsy system - I much prefer the way fonts look in ubuntu on my LCD display. To fix this I had to compile Firefox myself with the enable-system-cairo option. But for this I also needed a more recent version of cairo than what Gutsy provides - which is probably why mozilla decided to include their own version in the binaries.

 [1]: http://en.wikipedia.org/wiki/Cairo_(graphics)
 [2]: http://en.wikipedia.org/wiki/Subpixel_rendering
 [3]: http://en.wikipedia.org/wiki/Anti_aliasing

You will need to get the source and compile the following packages yourself from [freetype's download page][4] and [ cairo's releases page][5]:

 [4]: http://freetype.sourceforge.net/download.html#stable
 [5]: http://cairographics.org/releases/

*   freetype-2.3.5
*   pixman-0.10.0
*   cairo-1.6.4

with the usual

    ./configure &#038;& make &#038;& sudo make install

You might have to install make with the following before you can compile cairo:

    sudo apt-get install build-essential

Freetype needs special attention to enable LCD sub-pixel rendering because of [these patent issues][6]. So uncomment `define FT_CONFIG_OPTION_SUBPIXEL_RENDERING` in devel/ftoption.h [to enable it][7].

 [6]: http://permalink.gmane.org/gmane.comp.fonts.freetype.user/1912
 [7]: https://sourceforge.net/project/shownotes.php?release_id=479191&group_id=3157

It is not really necessary to recompile freetype, I just included it to play around with it - and the new cairo is just so that firefox will compile. You can delete the new libraries (by default in `/usr/local/lib`) after firefox is compiled with system cairo. Then everything should be back to using the default font settings as set in the gutsy preferences. Or so I am guessing, works for me ;)

Now to compile Firefox:  
Create a ~/.mozconfig file as described [here][8].  
This is mine:

 [8]: http://developer.mozilla.org/en/docs/Configuring_Build_Options#Example_.mozconfig_Files

    . $topsrcdir/browser/config/mozconfig
    mk_add_options MOZ_OBJDIR=@TOPSRCDIR@/ff-opt
    ac_add_options --disable-tests
    ac_add_options --enable-optimize
    ac_add_options --enable-libxul
    ac_add_options --enable-system-cairo 

Make sure you have all the [build prerequisites][9]. I had to install the following packages:

 [9]: http://developer.mozilla.org/en/docs/Linux_Build_Prerequisites

    sudo apt-get install build-essential
    sudo apt-get install libdbus-glib-1-dev
    sudo apt-get install libcurl4-openssl-dev
    sudo apt-get install libxt-dev

If you have everything, start compiling with this command

    make -f client.mk build

After some time, build a tarball [as recommended][10] with:

 [10]: http://developer.mozilla.org/en/docs/Build_and_Install#Installing_Your_Build

    cd ff-opt
    make package

You will find your new firefox package in the dist/ directory. Unpack somewhere and enjoy ;)

Before shot with Mozilla cairo:  
[![With Mozilla cairo][12]][12]

 []: http://blog.floatinginspace.za.org/wp-content/uploads/2008/05/firefox-cairo.png "With Mozilla cairo"

After shot with system cairo:  
[![With system cairo][13]][13]

 []: http://blog.floatinginspace.za.org/wp-content/uploads/2008/05/system-cairo.png "With system cairo"