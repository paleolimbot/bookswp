<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
		<div class="featured-post">
			<?php _e( 'Featured post', 'twentytwelve' ); ?>
		</div>
		<?php endif; ?>
		<header class="entry-header">

			<?php if ( is_single() ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php else : ?>
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
			<?php endif; // is_single() ?>
                        
                        <div class="book-terms"><?php the_terms($post->ID, 'people', 'by ');?> <?php the_terms($post->ID, 'shelves', '<span class="shelved">shelved under ', '</span>') ?></div>
			<?php if ( comments_open() ) : ?>
				<div class="comments-link">
					<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'twentytwelve' ) . '</span>', __( '1 Reply', 'twentytwelve' ), __( '% Replies', 'twentytwelve' ) ); ?>
				</div><!-- .comments-link -->
			<?php endif; // comments_open() ?>
		</header><!-- .entry-header -->
		<div class="entry-summary">
                    <?php if ( ! post_password_required() && ! is_attachment() ) : ?>
                    <a href="<?php the_permalink() ?>"><?php the_post_thumbnail(); ?></a>
		    <?php endif; ?>
                    <?php 
                    if ( is_single() ) {
                        the_content();
                    } else {
                        the_excerpt();
                    } ?>
		</div><!-- .entry-summary -->
		<footer class="entry-meta">
                    <div></div>
                    <?php the_terms($post->ID, 'genres', '<span class="book-terms">Genres: ', $sep=', ', $after='</span>') ?>
                    <?php the_terms($post->ID, 'keywords', '<span class="book-terms">Keywords: ', $sep=', ', $after='</span>') ?>
                    <div>
			<?php twentytwelve_entry_meta(); ?>
			<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
                    </div>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
