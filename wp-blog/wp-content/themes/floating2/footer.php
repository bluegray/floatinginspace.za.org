<div class="clear"></div>
</div>
<div id="footer">
<span class="text">
<?php
$crawltsite=7;
$ct = include("/home/bluegray/public_html/skeptic/crawltrack/crawltrack.php");
$ct = (!empty($ct) ? '&middot;&nbsp;ζφ' : '');
$blog_name = '<a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a>';
printf(__('Copyright %s %s %s &middot; Powered by %s %s<br/>','lightword'),'&copy;',date('Y'),$blog_name,'<a href="http://www.wordpress.org" title="WordPress" target="_blank">WordPress</a>', $ct)
;?>
<?php _e('<a href="http://www.lightwordtheme.com/" target="_blank" title="Modified Lightword Theme">Lightword Theme</a> by Andrei Luca, modified by bluegray','lightword')
;?>
</em>

<a title="<?php _e('Go to top','lightword'); ?>" class="top" href="#top"><?php _e('Go to top','lightword'); ?> &uarr;</a>
</span>
</div>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/menu.js"></script>

<?php wp_footer(); ?>
</div>
</body>
</html>
