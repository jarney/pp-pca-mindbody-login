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
?>
<h1>Please log in to access this content</h1>
            <form action="." method="POST">
                <input name="pp_pca_mindbody_username" type="text"/><br/>
                <input name="pp_pca_mindbody_password" type="password"/><br/>
                <input name="pp_pca_mindbody_submit" type="submit" value='Log In'/><br/>
            </form>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>