#!/usr/bin/python

import cgi, string, sys, cStringIO
import keyword, token, tokenize


#############################################################################
### Python Source Parser (does Hilighting)
#############################################################################

_KEYWORD = token.NT_OFFSET + 1
_TEXT    = token.NT_OFFSET + 2

_colors = {
    token.NUMBER:       '#0080C0',
    token.OP:           '#0000C0',
    token.STRING:       '#004080',
    tokenize.COMMENT:   '#008000',
    token.NAME:         '#000000',
    token.ERRORTOKEN:   '#FF8080',
    _KEYWORD:           '#C00000',
    _TEXT:              '#000000',
}


class Parser:
    """ Send colored python source.
    """

    def __init__(self, raw):
        """ Store the source text.
        """
        self.raw = string.strip(string.expandtabs(raw))
	self.output = ''

    def format(self):
        """ Parse and send the colored source.
        """
        # store line offsets in self.lines
        self.lines = [0, 0]
        pos = 0
        while 1:
            pos = string.find(self.raw, '\n', pos) + 1
            if not pos: break
            self.lines.append(pos)
        self.lines.append(len(self.raw))

        # parse the source and write it
        self.pos = 0
        text = cStringIO.StringIO(self.raw)
	self.output += '<pre>'
        try:
            tokenize.tokenize(text.readline, self)
        except tokenize.TokenError, ex:
            msg = ex[0]
            line = ex[1][0]
            self.output += "<h3>ERROR: %s</h3>%s\n" % (
                msg, self.raw[self.lines[line]:])
        self.output += '</pre>'
	return self.output 


    def __call__(self, toktype, toktext, (srow,scol), (erow,ecol), line):
        """ Token handler.
        """
        if 0:
            print "type", toktype, token.tok_name[toktype], "text", toktext,
            print "start", srow,scol, "end", erow,ecol, "<br />"

        # calculate new positions
        oldpos = self.pos
        newpos = self.lines[srow] + scol
        self.pos = newpos + len(toktext)

        # handle newlines
        if toktype in [token.NEWLINE, tokenize.NL]:
            self.output += '\n'
            return

        # send the original whitespace, if needed
        if newpos > oldpos:
            self.output += self.raw[oldpos:newpos]

        # skip indenting tokens
        if toktype in [token.INDENT, token.DEDENT]:
            self.pos = newpos
            return

        # map token type to a color group
        if token.LPAR <= toktype and toktype <= token.OP:
            toktype = token.OP
        elif toktype == token.NAME and keyword.iskeyword(toktext):
            toktype = _KEYWORD
        color = _colors.get(toktype, _colors[_TEXT])

        style = ''
        if toktype == token.ERRORTOKEN:
            style = 'style="border: solid 1.5pt #FF0000;"'

        # send text
	self.output += '<span style="color: %s" %s>' % (color, style)
        self.output += cgi.escape(toktext)
        self.output += '</span>'
