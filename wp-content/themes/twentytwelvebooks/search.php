<?php
/**
 * The template for displaying Search Results pages
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

$searchterms = get_query_var('booksearch') ? get_terms($args=array(
    'taxonomy'=>array('people', 'shelves', 'keywords'), 
    'search' => get_search_query(false))) : array() ;


get_header(); ?>

	<section id="primary" class="site-content">
		<div id="content" role="main">
                
                <?php if(have_posts() || !empty($searchterms)) : ?>
                    <header class="page-header">
				<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'twentytwelve' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			        <?php 
                                $sep = '';
                                if(!empty($searchterms)) {
                                    echo '<div class="book-terms">Terms: ';
                                    foreach($searchterms as $term) {
                                        echo $sep.'<a href="' . esc_url( get_term_link( $term ) ) . '" alt="' . esc_attr( sprintf('View all post filed under %s', $term->name ) ) . '">' . $term->name . '</a>';
                                        $sep = ', ';
                                    } 
                                    echo '</div>';
                                }
                                
                                if(have_posts()) {
                                    twentytwelve_content_nav( 'nav-above' ); 
                                }
                                
                                ?>
                    </header>
                <?php endif; ?>
                
		<?php if ( have_posts() ) : ?>
                        
			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php 
                                if($post->post_type == 'book') {
                                    get_template_part( 'content', 'book' );
                                } else {
                                    get_template_part( 'content', get_post_format() );
                                }
                                ?>
			<?php endwhile; ?>

			<?php twentytwelve_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<article id="post-0" class="post no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'twentytwelve' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'twentytwelve' ); ?></p>
                                        <?php if(get_query_var('booksearch')) {
                                            bookswp_search_book_form();
                                        } else {
                                            get_search_form(); 
                                        }?>
				</div><!-- .entry-content -->
			</article><!-- #post-0 -->

		<?php endif; ?>

		</div><!-- #content -->
	</section><!-- #primary -->

<?php if(get_query_var('booksearch')) {
     get_sidebar('book');
} else {
    get_sidebar();
} ?>
<?php get_footer(); ?>