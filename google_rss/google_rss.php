<pre>
<span class="token_comment">#!/usr/bin/python
</span>
<span class="keyword">import</span> <span class="token_name">os</span><span class="token_op">,</span> <span class="token_name">cgi</span><span class="token_op">,</span> <span class="token_name">time</span><span class="token_op">,</span> <span class="token_name">re</span><span class="token_op">,</span> <span class="token_name">string</span>
<span class="token_comment">#import cgitb; cgitb.enable()
</span>
<span class="token_comment"># Location of the apache access log file on server
</span><span class="token_name">logfile</span> <span class="token_op">=</span> <span class="token_string">'/var/log/httpd/access_log'</span>

<span class="token_comment">#host without slashes or http:// (will only be used if environment var is not available
</span><span class="token_name">host</span> <span class="token_op">=</span> <span class="token_string">'www.floatinginspace.za.org'</span>

<span class="token_comment"># Maximum number of entries
</span><span class="token_name">max</span> <span class="token_op">=</span> <span class="token_number">20</span>

<span class="token_comment"># Case insensitive string to filter for in logs
</span><span class="token_name">search_string</span> <span class="token_op">=</span> <span class="token_string">'googlebot/'</span>

<span class="token_comment"># adjust time from GMT 
</span><span class="token_name">time_adjust</span> <span class="token_op">=</span> <span class="token_string">'2'</span>

<span class="token_name">RSS_TEMPLATE</span> <span class="token_op">=</span> <span class="token_string">"""&lt;?xml version="1.0" encoding="utf-8"?&gt;
&lt;?xml-stylesheet type="text/css" href="http://www.floatinginspace.za.org/rss.css" ?&gt;
&lt;rss version="2.0"&gt;
    &lt;channel&gt;
        &lt;title&gt;GoogleBot log entries for %(HOST)s&lt;/title&gt;
        &lt;link&gt;http://%(HOST)s/&lt;/link&gt;
        &lt;description&gt;Latest crawls by GoogleBot&lt;/description&gt;
        &lt;lastBuildDate&gt;%(DATE)s&lt;/lastBuildDate&gt;
        &lt;language&gt;en&lt;/language&gt;
%(ITEMS)s

    &lt;/channel&gt;
&lt;/rss&gt;"""</span>

<span class="token_name">ITEM_TEMPLATE</span> <span class="token_op">=</span> <span class="token_string">'''
        &lt;item&gt;
            &lt;title&gt;%(URL)s on %(LOCALDATE)s&lt;/title&gt;
            &lt;link&gt;http://%(HOST)s%(URL)s&lt;/link&gt;
            &lt;pubDate&gt;%(DATE)s&lt;/pubDate&gt;
            &lt;description&gt;%(DATA)s&lt;/description&gt;
        &lt;/item&gt;'''</span>

<span class="token_name">LOG_TIME_FORMAT</span> <span class="token_op">=</span> <span class="token_string">"%d/%b/%Y:%H:%M:%S"</span>

<span class="token_name">datereg</span> <span class="token_op">=</span> <span class="token_name">re</span><span class="token_op">.</span><span class="token_name">compile</span><span class="token_op">(</span><span class="token_string">'\[([0-9]+/.../[0-9][0-9][0-9][0-9]:[0-9][0-9]:[0-9][0-9]:[0-9][0-9]) (.[0-9][0-9][0-9][0-9])\]'</span><span class="token_op">)</span>
<span class="token_name">logreg</span> <span class="token_op">=</span> <span class="token_name">re</span><span class="token_op">.</span><span class="token_name">compile</span><span class="token_op">(</span><span class="token_string">'"(GET|HEAD) (\S+) \S+" \d+'</span><span class="token_op">)</span>

<span class="keyword">def</span> <span class="token_name">log2local</span><span class="token_op">(</span><span class="token_name">str</span><span class="token_op">,</span> <span class="token_name">adj</span><span class="token_op">)</span><span class="token_op">:</span>
    <span class="token_name">apachedate</span> <span class="token_op">=</span> <span class="token_name">datereg</span><span class="token_op">.</span><span class="token_name">search</span><span class="token_op">(</span><span class="token_name">str</span><span class="token_op">)</span>
    <span class="token_name">dated</span> <span class="token_op">=</span> <span class="token_name">apachedate</span><span class="token_op">.</span><span class="token_name">group</span><span class="token_op">(</span><span class="token_number">1</span><span class="token_op">)</span>
    <span class="token_name">timezoned</span> <span class="token_op">=</span> <span class="token_name">apachedate</span><span class="token_op">.</span><span class="token_name">group</span><span class="token_op">(</span><span class="token_number">2</span><span class="token_op">)</span>

    <span class="token_name">logtime</span> <span class="token_op">=</span> <span class="token_name">time</span><span class="token_op">.</span><span class="token_name">strptime</span><span class="token_op">(</span><span class="token_name">dated</span><span class="token_op">,</span> <span class="token_name">LOG_TIME_FORMAT</span><span class="token_op">)</span> <span class="token_comment">#local time in timetuple</span>
    <span class="token_name">ucttime</span> <span class="token_op">=</span> <span class="token_name">time</span><span class="token_op">.</span><span class="token_name">mktime</span><span class="token_op">(</span><span class="token_name">logtime</span><span class="token_op">)</span> <span class="token_comment">#utc time in seconds</span>

    <span class="token_name">heretz</span> <span class="token_op">=</span> <span class="token_name">adj</span><span class="token_op">*</span><span class="token_number">60</span><span class="token_op">*</span><span class="token_number">60</span> <span class="token_comment">#the timezone you want eg. SAST = +2</span>
    <span class="token_name">mytime</span> <span class="token_op">=</span> <span class="token_name">ucttime</span> <span class="token_op">+</span> <span class="token_name">heretz</span>

    <span class="token_name">newdate</span> <span class="token_op">=</span> <span class="token_string">'[%s %+05d]'</span> <span class="token_op">%</span><span class="token_op">(</span><span class="token_name">time</span><span class="token_op">.</span><span class="token_name">strftime</span><span class="token_op">(</span><span class="token_name">LOG_TIME_FORMAT</span><span class="token_op">,</span> <span class="token_name">time</span><span class="token_op">.</span><span class="token_name">gmtime</span><span class="token_op">(</span><span class="token_name">mytime</span><span class="token_op">)</span><span class="token_op">)</span><span class="token_op">,</span><span class="token_name">adj</span><span class="token_op">*</span><span class="token_number">100</span><span class="token_op">)</span>
    <span class="token_name">newdatestr</span> <span class="token_op">=</span> <span class="token_name">datereg</span><span class="token_op">.</span><span class="token_name">sub</span><span class="token_op">(</span><span class="token_name">newdate</span><span class="token_op">,</span> <span class="token_name">str</span><span class="token_op">)</span>
    <span class="keyword">return</span> <span class="token_name">newdatestr</span>

<span class="keyword">def</span> <span class="token_name">getlogentrydate</span><span class="token_op">(</span><span class="token_name">str</span><span class="token_op">)</span><span class="token_op">:</span>
    <span class="token_name">apachedate</span> <span class="token_op">=</span> <span class="token_name">datereg</span><span class="token_op">.</span><span class="token_name">search</span><span class="token_op">(</span><span class="token_name">str</span><span class="token_op">)</span>
    <span class="token_name">dated</span> <span class="token_op">=</span> <span class="token_name">apachedate</span><span class="token_op">.</span><span class="token_name">group</span><span class="token_op">(</span><span class="token_number">1</span><span class="token_op">)</span>
    <span class="token_name">timezoned</span> <span class="token_op">=</span> <span class="token_name">apachedate</span><span class="token_op">.</span><span class="token_name">group</span><span class="token_op">(</span><span class="token_number">2</span><span class="token_op">)</span>

    <span class="token_name">logtime</span> <span class="token_op">=</span> <span class="token_name">time</span><span class="token_op">.</span><span class="token_name">strptime</span><span class="token_op">(</span><span class="token_name">dated</span><span class="token_op">,</span> <span class="token_name">LOG_TIME_FORMAT</span><span class="token_op">)</span> <span class="token_comment">#local time in timetuple</span>
    <span class="token_name">ucttime</span> <span class="token_op">=</span> <span class="token_name">time</span><span class="token_op">.</span><span class="token_name">mktime</span><span class="token_op">(</span><span class="token_name">logtime</span><span class="token_op">)</span> <span class="token_comment">#utc time in seconds</span>
    <span class="keyword">return</span> <span class="token_name">float</span><span class="token_op">(</span><span class="token_name">ucttime</span><span class="token_op">)</span>

<span class="keyword">def</span> <span class="token_name">getlogurl</span><span class="token_op">(</span><span class="token_name">str</span><span class="token_op">)</span><span class="token_op">:</span>
    <span class="token_name">logurl</span> <span class="token_op">=</span> <span class="token_name">logreg</span><span class="token_op">.</span><span class="token_name">search</span><span class="token_op">(</span><span class="token_name">str</span><span class="token_op">)</span><span class="token_op">.</span><span class="token_name">group</span><span class="token_op">(</span><span class="token_number">2</span><span class="token_op">)</span>
    <span class="keyword">return</span> <span class="token_name">logurl</span>

<span class="keyword">def</span> <span class="token_name">print_rss</span> <span class="token_op">(</span><span class="token_name">items</span><span class="token_op">,</span> <span class="token_name">date</span><span class="token_op">)</span><span class="token_op">:</span>
    <span class="keyword">if</span> <span class="token_name">os</span><span class="token_op">.</span><span class="token_name">getenv</span><span class="token_op">(</span><span class="token_string">'HTTP_USER_AGENT'</span><span class="token_op">,</span> <span class="token_string">'N/A'</span><span class="token_op">)</span><span class="token_op">.</span><span class="token_name">find</span><span class="token_op">(</span><span class="token_string">'Mozilla'</span><span class="token_op">)</span> <span class="token_op">&gt;=</span> <span class="token_number">0</span><span class="token_op">:</span>
        <span class="keyword">print</span> <span class="token_string">"content-type: application/xml\nCache-Control: no-cache\n"</span>
    <span class="keyword">else</span><span class="token_op">:</span>
        <span class="keyword">print</span> <span class="token_string">"content-type: application/rss+xml\nCache-Control: no-cache\n"</span>
    <span class="keyword">print</span> <span class="token_name">RSS_TEMPLATE</span> <span class="token_op">%</span> <span class="token_op">{</span>
        <span class="token_string">'SCRIPT_NAME'</span><span class="token_op">:</span><span class="token_name">os</span><span class="token_op">.</span><span class="token_name">getenv</span><span class="token_op">(</span><span class="token_string">'SCRIPT_NAME'</span><span class="token_op">,</span><span class="token_string">' '</span><span class="token_op">)</span><span class="token_op">,</span>
        <span class="token_string">'DATE'</span><span class="token_op">:</span><span class="token_name">date</span><span class="token_op">,</span> <span class="token_string">'ITEMS'</span><span class="token_op">:</span><span class="token_name">items</span><span class="token_op">,</span>
        <span class="token_string">'HOST'</span><span class="token_op">:</span><span class="token_name">os</span><span class="token_op">.</span><span class="token_name">getenv</span><span class="token_op">(</span><span class="token_string">'HTTP_HOST'</span><span class="token_op">,</span><span class="token_name">host</span><span class="token_op">)</span>
        <span class="token_op">}</span>

<span class="token_comment">################################ start of main program ################################
</span>
<span class="token_name">str</span> <span class="token_op">=</span> <span class="token_string">''</span>
<span class="token_name">items</span> <span class="token_op">=</span> <span class="token_op">[</span><span class="token_op">]</span>
<span class="token_name">latest</span> <span class="token_op">=</span> <span class="token_number">0.0</span>
<span class="token_name">output</span> <span class="token_op">=</span> <span class="token_op">[</span><span class="token_op">]</span>

<span class="token_name">form</span> <span class="token_op">=</span> <span class="token_name">cgi</span><span class="token_op">.</span><span class="token_name">FieldStorage</span><span class="token_op">(</span><span class="token_op">)</span>
<span class="token_name">arg</span> <span class="token_op">=</span> <span class="token_name">form</span><span class="token_op">.</span><span class="token_name">getfirst</span><span class="token_op">(</span><span class="token_string">'search'</span><span class="token_op">,</span> <span class="token_name">search_string</span><span class="token_op">)</span>
<span class="token_name">adjt</span> <span class="token_op">=</span> <span class="token_name">form</span><span class="token_op">.</span><span class="token_name">getfirst</span><span class="token_op">(</span><span class="token_string">'adjust'</span><span class="token_op">,</span> <span class="token_name">time_adjust</span><span class="token_op">)</span>

<span class="token_name">norm</span> <span class="token_op">=</span> <span class="token_name">string</span><span class="token_op">.</span><span class="token_name">maketrans</span><span class="token_op">(</span><span class="token_string">''</span><span class="token_op">,</span> <span class="token_string">''</span><span class="token_op">)</span> <span class="token_comment">#builds list of all characters</span>
<span class="token_comment"># remove shell control characters just in case 
</span><span class="token_name">arg</span> <span class="token_op">=</span> <span class="token_name">string</span><span class="token_op">.</span><span class="token_name">translate</span><span class="token_op">(</span><span class="token_name">arg</span><span class="token_op">,</span> <span class="token_name">norm</span><span class="token_op">,</span> <span class="token_string">r'\;&amp;|()'</span><span class="token_op">)</span>
<span class="token_name">adjt</span> <span class="token_op">=</span> <span class="token_name">string</span><span class="token_op">.</span><span class="token_name">translate</span><span class="token_op">(</span><span class="token_name">adjt</span><span class="token_op">,</span> <span class="token_name">norm</span><span class="token_op">,</span> <span class="token_string">r'\;&amp;|()'</span><span class="token_op">)</span>

<span class="token_comment">#first of two methods to get logfile
</span><span class="token_name">lfile</span> <span class="token_op">=</span> <span class="token_name">open</span><span class="token_op">(</span><span class="token_name">logfile</span><span class="token_op">,</span><span class="token_string">'r'</span><span class="token_op">)</span>
<span class="token_name">loglines</span> <span class="token_op">=</span> <span class="token_name">lfile</span><span class="token_op">.</span><span class="token_name">readlines</span><span class="token_op">(</span><span class="token_op">)</span>
<span class="token_name">lfile</span><span class="token_op">.</span><span class="token_name">close</span><span class="token_op">(</span><span class="token_op">)</span>
<span class="keyword">for</span> <span class="token_name">line</span> <span class="keyword">in</span> <span class="token_name">loglines</span><span class="token_op">:</span>
    <span class="keyword">if</span> <span class="token_name">line</span><span class="token_op">.</span><span class="token_name">lower</span><span class="token_op">(</span><span class="token_op">)</span><span class="token_op">.</span><span class="token_name">find</span><span class="token_op">(</span><span class="token_name">arg</span><span class="token_op">)</span> <span class="token_op">&gt;=</span> <span class="token_number">0</span><span class="token_op">:</span>
        <span class="token_name">output</span><span class="token_op">.</span><span class="token_name">append</span><span class="token_op">(</span><span class="token_name">line</span><span class="token_op">)</span>

<span class="token_comment">#second method
</span><span class="token_comment">#cmd = 'grep -i %s %s 2&gt;&amp;1' %(arg, logfile)
</span><span class="token_comment">#output = os.popen(cmd).readlines()
</span>
<span class="keyword">for</span> <span class="token_name">x</span> <span class="keyword">in</span> <span class="token_name">output</span><span class="token_op">:</span>
    <span class="token_name">entrytime</span> <span class="token_op">=</span> <span class="token_name">getlogentrydate</span><span class="token_op">(</span><span class="token_name">x</span><span class="token_op">)</span>
    <span class="keyword">if</span> <span class="token_name">entrytime</span> <span class="token_op">&gt;</span> <span class="token_name">latest</span><span class="token_op">:</span>
        <span class="token_name">latest</span> <span class="token_op">=</span> <span class="token_name">entrytime</span>
    <span class="token_name">items</span><span class="token_op">.</span><span class="token_name">append</span><span class="token_op">(</span>  <span class="token_name">ITEM_TEMPLATE</span> <span class="token_op">%</span><span class="token_op">{</span>
            <span class="token_string">'DATA'</span><span class="token_op">:</span><span class="token_name">cgi</span><span class="token_op">.</span><span class="token_name">escape</span><span class="token_op">(</span><span class="token_name">log2local</span><span class="token_op">(</span><span class="token_name">x</span><span class="token_op">,</span><span class="token_name">int</span><span class="token_op">(</span><span class="token_name">adjt</span><span class="token_op">)</span><span class="token_op">)</span><span class="token_op">.</span><span class="token_name">rstrip</span><span class="token_op">(</span><span class="token_op">)</span><span class="token_op">)</span><span class="token_op">,</span>
            <span class="token_string">'DATE'</span><span class="token_op">:</span><span class="token_name">time</span><span class="token_op">.</span><span class="token_name">strftime</span><span class="token_op">(</span><span class="token_string">'%a, %d %b %Y %H:%M:%S GMT'</span><span class="token_op">,</span><span class="token_name">time</span><span class="token_op">.</span><span class="token_name">gmtime</span><span class="token_op">(</span><span class="token_name">entrytime</span><span class="token_op">)</span><span class="token_op">)</span><span class="token_op">,</span>
            <span class="token_string">'LOCALDATE'</span><span class="token_op">:</span><span class="token_name">time</span><span class="token_op">.</span><span class="token_name">strftime</span><span class="token_op">(</span><span class="token_string">'%a, %d %b %Y %H:%M:%S '</span><span class="token_op">,</span><span class="token_name">time</span><span class="token_op">.</span><span class="token_name">gmtime</span><span class="token_op">(</span><span class="token_name">entrytime</span><span class="token_op">+</span><span class="token_number">60</span><span class="token_op">*</span><span class="token_number">60</span><span class="token_op">*</span><span class="token_name">int</span><span class="token_op">(</span><span class="token_name">adjt</span><span class="token_op">)</span><span class="token_op">)</span><span class="token_op">)</span> <span class="token_op">+</span> <span class="token_string">'%+05d'</span> <span class="token_op">%</span><span class="token_op">(</span><span class="token_name">int</span><span class="token_op">(</span><span class="token_name">adjt</span><span class="token_op">)</span><span class="token_op">*</span><span class="token_number">100</span><span class="token_op">)</span><span class="token_op">,</span>
            <span class="token_string">'HOST'</span><span class="token_op">:</span><span class="token_name">os</span><span class="token_op">.</span><span class="token_name">getenv</span><span class="token_op">(</span><span class="token_string">'HTTP_HOST'</span><span class="token_op">,</span><span class="token_name">host</span><span class="token_op">)</span><span class="token_op">,</span>
            <span class="token_string">'URL'</span><span class="token_op">:</span><span class="token_name">getlogurl</span><span class="token_op">(</span><span class="token_name">x</span><span class="token_op">)</span> <span class="token_op">}</span>  <span class="token_op">)</span>

<span class="token_name">items</span><span class="token_op">.</span><span class="token_name">reverse</span><span class="token_op">(</span><span class="token_op">)</span>
<span class="token_name">print_rss</span><span class="token_op">(</span> <span class="token_string">''</span><span class="token_op">.</span><span class="token_name">join</span><span class="token_op">(</span><span class="token_name">items</span><span class="token_op">[</span><span class="token_op">:</span><span class="token_name">max</span><span class="token_op">]</span><span class="token_op">)</span> <span class="token_op">,</span> <span class="token_name">time</span><span class="token_op">.</span><span class="token_name">strftime</span><span class="token_op">(</span><span class="token_string">'%a, %d %b %Y %H:%M:%S GMT'</span><span class="token_op">,</span><span class="token_name">time</span><span class="token_op">.</span><span class="token_name">gmtime</span><span class="token_op">(</span><span class="token_name">latest</span><span class="token_op">)</span><span class="token_op">)</span>   <span class="token_op">)</span>
</pre>
