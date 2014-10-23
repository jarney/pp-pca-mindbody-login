<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>


	<div id="primary" class="site-content">
		<div id="content" role="main">

<?php
if (isset($pp_pca_mindbody_error)) {
    echo "<div style=\"color:red\" class=\"mindbody-error\">" . $pp_pca_mindbody_error . "</div>";
}
$option = get_option("pp_pca_options");
$siteID = $option['PP_PCA_MINDBODY_SITE_ID'];
?>
<h1>Please log in to access this content</h1>
<br/>
            <form action="." method="POST">
                <table style="margin: 3px; padding: 3px;">
		<tr>
		<td style="margin: 3px; padding: 3px;">
	                <label for="pp_pca_mindbody_username">Username</label>
		</td>
		<td style="margin: 3px; padding: 3px;">
			<input id="pp_pca_mindbody_username" name="pp_pca_mindbody_username" type="text"/>
		</td>
		</tr>
		<tr>
		<td style="margin: 3px; padding: 3px;">
	                <label for="pp_pca_mindbody_password">Password</label>
		</td>
		<td style="margin: 3px; padding: 3px;">
			<input id="pp_pca_mindbody_password" name="pp_pca_mindbody_password" type="password"/>
		</td>
		</tr>
		<tr>
		<td colspan="2">
	                <input name="pp_pca_mindbody_submit" type="submit" value='Log In'/>
		</td>
		</tr>
		<tr>
		<td colspan="2" style="margin: 10px; padding: 10px;">
			Don't have an account?  You can always 
<?php
echo "<a href=\"https://clients.mindbodyonline.com/classic/home?studioid=" . $siteID . "\">";
?>register for an account</a>.
		</td>
		</tr>
		</table>
            </form>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>