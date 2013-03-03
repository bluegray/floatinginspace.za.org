<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

$crawltsite=7;
$ct = include("/home/bluegray/public_html/skeptic/crawltrack/crawltrack.php");

?>

<hr />
<div id="footer" role="contentinfo">
<!-- If you'd like to support WordPress, having the "powered by" link somewhere on your blog is the best way; it's our only promotion or advertising. -->
	<p>
		<?php bloginfo('name'); ?> is proudly powered by
		<a href="http://wordpress.org/">WordPress</a>
		<br /><a href="<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a>
		and <a href="<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a>.
                <!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
                <?php if (!empty($ct)) echo '&nbsp;&nbsp;ζφ'; ?>
	</p>
</div>
</div>

<!-- Gorgeous design by Michael Heilemann - http://binarybonsai.com/kubrick/ -->
<?php /* "Just what do you think you're doing Dave?" */ ?>

                <?php wp_footer(); ?>

    <script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
    <script type="text/javascript">
	_uacct = "UA-374538-2";
	urchinTracker();
    </script>

</body>
</html>
