<?php

/* 
 * Provides methods for querying goodreads by ISBN, by title, and by author
 * https://www.goodreads.com/book/isbn?isbn=...&key=... for ISBN lookup
 * https://www.goodreads.com/book/title?title=...&key=... for title lookup
 * 
 * response:
<?xml version="1.0" encoding="UTF-8"?>
<GoodreadsResponse>  
    <Request>    
        <authentication>true</authentication>      
        <key><![CDATA[APIKEY]]></key>    
        <method><![CDATA[book_title]]></method>  
    </Request>  
    <book>  
        <id>798048</id>  
        <title>April&apos;s Kittens</title>  
        <isbn><![CDATA[0060244003]]></isbn>  
        <isbn13><![CDATA[9780060244002]]></isbn13>  
        <asin><![CDATA[]]></asin>  
        <kindle_asin><![CDATA[]]></kindle_asin>  
        <marketplace_id><![CDATA[]]></marketplace_id>  
        <country_code><![CDATA[CA]]></country_code>  
        <image_url>https://images.gr-assets.com/books/1362494230m/798048.jpg</image_url>  
        <small_image_url>https://images.gr-assets.com/books/1362494230s/798048.jpg</small_image_url>  
        <publication_year>1940</publication_year>  
        <publication_month>10</publication_month>  
        <publication_day>2</publication_day>  
        <publisher>HarperCollins</publisher>  
        <language_code></language_code>  
        <is_ebook>false</is_ebook>  
        <description><![CDATA[...]]></description>  
        <work>  
            <id type="integer">784013</id>  
            <books_count type="integer">4</books_count>  
            <best_book_id type="integer">798048</best_book_id>  
            <reviews_count type="integer">755</reviews_count>  
            <ratings_sum type="integer">1529</ratings_sum>  
            <ratings_count type="integer">412</ratings_count>  
            <text_reviews_count type="integer">61</text_reviews_count>  
            <original_publication_year type="integer">1940</original_publication_year>  
            <original_publication_month type="integer">10</original_publication_month>  
            <original_publication_day type="integer">2</original_publication_day>  
            <original_title>April's Kittens</original_title>  
            <original_language_id type="integer" nil="true"/>  
            <media_type>book</media_type>  
            <rating_dist>5:111|4:130|3:122|2:39|1:10|total:412</rating_dist>  
            <desc_user_id type="integer">-51</desc_user_id>  
            <default_chaptering_book_id type="integer" nil="true"/>  
            <default_description_language_code nil="true"/></work> 
        <average_rating>3.71</average_rating>  
        <num_pages><![CDATA[40]]></num_pages>  
        <format><![CDATA[Hardcover]]></format>  
        <edition_information><![CDATA[]]></edition_information>  
        <ratings_count><![CDATA[406]]></ratings_count>  
        <text_reviews_count><![CDATA[57]]></text_reviews_count>  
        <url><![CDATA[https://www.goodreads.com/book/show/798048.April_s_Kittens]]></url>  
        <link><![CDATA[https://www.goodreads.com/book/show/798048.April_s_Kittens]]></link>  
        <authors>
            <author>
                <id>280154</id>
                <name>Clare Turlay Newberry</name>
                <role></role>
                <image_url nophoto='false'><![CDATA[https://images.gr-assets.com/authors/1392326641p5/280154.jpg]]></image_url>
                <small_image_url nophoto='false'><![CDATA[https://images.gr-assets.com/authors/1392326641p2/280154.jpg]]></small_image_url>
                <link><![CDATA[https://www.goodreads.com/author/show/280154.Clare_Turlay_Newberry]]></link>
                <average_rating>3.92</average_rating>
                <ratings_count>1448</ratings_count>
                <text_reviews_count>259</text_reviews_count>
            </author>
        </authors>   
        <reviews_widget></reviews_widget>  
        <popular_shelves>      
            ...
        </popular_shelves>  
        <book_links>    
            ... 
        </book_links>  
        <buy_links>    
            ...
        </buy_links>  
        <series_works>
            ...
        </series_works>  
        <similar_books>          
            ...
        </similar_books>
    </book>
</GoodreadsResponse>
 */

function _stripisbn($isbn) {
    return preg_replace('/[\s-]/', '', $isbn);
}

function _bookswp_is_trying_isbn($isbn) {
    $allisnum = array_map('is_numeric', $isbn);
    $ntrue = count(array_filter($allisnum));
    if(($ntrue / (float)count($isbn)) > 0.75 && $ntrue > 5) {
        return false;
    } else {
        return NULL;
    }
}

function _bookswp_isbn_check($isbn) {
    $isbn = str_split(_stripisbn($isbn));
    if(count($isbn) == 10) {
        $sum = 0;
        foreach($isbn as $i => $d) {
            if($i == 9) break;
            if(!is_numeric($d)) _bookswp_is_trying_isbn($isbn);
            $sum += intval($d) * (10-$i);
        }
        if($isbn[9] == "X" || $isbn[9]=="x") {
            return (($sum+10) % 11) == 0;
        } else if(is_numeric($isbn[9])) {
            return (($sum + intval($isbn[9])) % 11) == 0;
        } else {
            return _bookswp_is_trying_isbn($isbn);
        }
    } else if(count($isbn) == 13) {
        $sum = 0;
        foreach($isbn as $i=>$d) {
            if($i==12) break;
            if(!is_numeric($d)) return _bookswp_is_trying_isbn($isbn);
            $int = intval($d);
            $sum  += (($i%2) == 0) ? $int : $int*3;
        }
        if(is_numeric($isbn[12])) {
            return (($sum+intval($isbn[12])) % 10) == 0;
        } else {
            return _bookswp_is_trying_isbn($isbn);
        }
    } else {
        return _bookswp_is_trying_isbn($isbn);
    }
}

function _bookswp_goodreads_parse_book_node($node) {
    $book = array();
    foreach($node->childNodes as $n) {
        if($n->hasChildNodes()) {
            if ($n->childNodes->length == 1) { // is childless tag
                $book[$n->nodeName] = $n->firstChild->nodeValue;
            } else if($n->nodeName == 'authors') {
                $authors = array();
                foreach($n->childNodes as $authorNode) {
                    if($authorNode->hasChildNodes()) {
                        foreach($authorNode->childNodes as $authorItem) {
                            if ($authorItem->nodeName == 'name') {
                                $authors[] = $authorItem->firstChild->nodeValue;
                                break;
                            }
                        }
                    }
                }
                $book['authors'] = $authors;
            }
        }
    }
    return $book;
}

function _bookswp_goodreads_parse($source) {
    $dom = new DOMDocument();
    // parsing the <error> tag is not possible without some magic, because
    // goodreads returns 404 NOT FOUND and the XML parser does parse
    @$dom->load($source);
    if($dom->hasChildNodes()) {
        $books = array();
        foreach($dom->firstChild->childNodes as $domNode) {
            if($domNode->nodeName == 'book') {
                $books[] = _bookswp_goodreads_parse_book_node($domNode);
            }
        }
        return $books;
    }
    return NULL;
}

function bookswp_get_goodreads_by_isbn($isbn, $apikey) {
    return _bookswp_goodreads_parse(sprintf('https://www.goodreads.com/book/isbn?isbn=%s&key=%s',
             urlencode($isbn), urlencode($apikey)));
}

function bookswp_get_goodreads_by_title($title, $apikey) {
    return _bookswp_goodreads_parse(sprintf('https://www.goodreads.com/book/title?title=%s&key=%s',
             urlencode($title), urlencode($apikey)));
}

function _bookswp_insert_goodreads_thumbnail($url, $parent_post_id) {
    //strpos($a, 'are') !== false
    //https://s.gr-assets.com/assets/nophoto/book/111x148-bcc042a9c91a29c1d680899eff700a03.png
    //is usually the nophoto url, but this may change
    if(!empty($url) && strpos($url, 'nophoto') === false) {
        $upload_dir = wp_upload_dir();
        $filename = path_join($upload_dir['path'] , md5($url) . '.jpg');
        @copy($url, $filename);
        if(file_exists($filename)) {
            // Check the type of file. We'll use this as the 'post_mime_type'.
            $filetype = wp_check_filetype( basename( $filename ), null );

            // Prepare an array of post data for the attachment.
            $attachment = array(
                    'guid'           => $upload_dir['url'] . '/' . basename( $filename ), 
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
            );

            // Insert the attachment.
            $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

            // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
            require_once( ABSPATH . 'wp-admin/includes/image.php' );

            // Generate the metadata for the attachment, and update the database record.
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            set_post_thumbnail( $parent_post_id, $attach_id );
            
            return $attach_id;
        } else {
            //failed
            return 0;
        }
    } else {
        return 0;
    }
}

function bookswp_do_goodreads_lookup($post) {
    if($post->post_type == 'book' && !$post->post_title && !$post->post_content) {
        $apikey = get_option('bookswp_goodreads_api', '');
        $books = NULL;
        $validisbn = NULL;
        if($_GET['isbn'] && $apikey) {
            $validisbn = _bookswp_isbn_check($_GET['isbn']);
            $books = bookswp_get_goodreads_by_isbn(_stripisbn($_GET['isbn']), $apikey);
        } else if($_GET['booktitle'] && $apikey) {
            $validisbn = _bookswp_isbn_check($_GET['booktitle']);
            if($validisbn) {
                $books = bookswp_get_goodreads_by_isbn(_stripisbn($_GET['booktitle']), $apikey);
            } else if($validisbn === NULL) {
                $books = bookswp_get_goodreads_by_title($_GET['booktitle'], $apikey);
            }
        }
        if(empty($books)) {
            $isbnerror = ($validisbn === false) ? " Invalid ISBN!": "";
            if(empty($apikey)) {
                echo '<div>Goodreads API key required for Goodreads lookup.</div>';
            } else if($_GET['isbn']) {
                echo '<div>Goodreads lookup failed for ISBN "'. $_GET['isbn'] . '".'. $isbnerror . '</div>';
            } else if($_GET['booktitle']) {
                echo '<div>Goodreads lookup failed for title "'. $_GET['booktitle'] . '".'. $isbnerror . '</div>';
            }
        } else if($books['error']) {
            if($_GET['isbn']) {
                echo '<div>Goodreads lookup failed for ISBN "'. $_GET['isbn'] . '"' . $books['error'] . '</div>';
            } else if($_GET['booktitle']) {
                echo '<div>Goodreads lookup failed for title "'. $_GET['booktitle'] . '"' . $books['error'] . '</div>';
            }
        } else if(count($books) == 1) {
            $book = $books[0];
            //update the content in the $post object (which does not save to the DB)
            $post->post_title = $book['title'];
            $post->post_content = $book['description'];
            if($book['authors']) add_post_meta($post->ID, 'author', implode('/', $book['authors']));
            if($book['isbn13']) add_post_meta($post->ID, 'isbn13', $book['isbn13']);
            if($book['isbn']) add_post_meta($post->ID, 'isbn10', $book['isbn']);
            if($book['publisher']) add_post_meta($post->ID, 'publisher', $book['publisher']);
            if($book['publication_year']) add_post_meta($post->ID, 'publication_year', $book['publication_year']);
            //add authors as terms in the 'people' taxonomy
            $term_ids = array();
            foreach($book['authors'] as $author) {
                $existing_term = get_terms($args=array(
                                    'taxonomy'=>array('people'), 
                                    'name' => $author,
                                    'hide_empty' => false,)) ;
                if(!empty($existing_term)) {
                    $term_ids[] = $existing_term[0]->term_id;
                } else {
                    //add term
                    $newterm = wp_insert_term($author, 'people');
                    if(!is_wp_error($newterm)) {
                        $term_ids[] = $newterm['term_id'];
                    } else {
                        echo '<!--';
                        var_dump($newterm);
                        echo '-->';
                    }
                }       
            }
            wp_set_post_terms($post->ID, $term_ids, $taxonomy='people');
            
            //add the post thumbnail
            _bookswp_insert_goodreads_thumbnail($book['image_url'], $post->ID);
            
            //echo success
            echo '<div>Goodreads lookup succeeded.</div>';
            
            //echo possible duplicate books
            //check books to see if book currently exists
            $dcq = new WP_Query($args=array('title'=>$book['title'], 'post_type'=>'book'));
            if($dcq->have_posts()) {
                $backup = $post;
                echo '<div>Also, the following possibly identical books in your collection were found:</div>'
                     . '<ul style="list-style: initial; list-style-position: inside; margin-top: 5px; margin-bottom: 5px;">';
                while($dcq->have_posts()) {
                    $dcq->the_post(); // calling this overrides global $post
                    $term_list = wp_get_post_terms($dcq->post->ID, 'people', array("fields" => "names"));
                    $authors = empty($term_list) ? '' : ' (' . implode(', ', $term_list) . ')';
                    echo '<li><a href="'. get_edit_post_link($dcq->post->ID) . '">' . 
                            $dcq->post->post_title . '</a>' . $authors . '</li>';
                }
                global $post;
                $post = $backup;
                echo '</ul>';
            }
        } else {
            // multiple book results (does not currently happen with current results)
            echo '<div>Goodreads found the following books:</div>';
            echo '<ul>';
            foreach($books as $book) {
                echo '<li><a href="?post_new.php?post_type=book&isbn=' . $book['isbn'] . '">' . $book['title'] . '</a> by ' . implode(', ', $book['authors']) . '</li>';
            }
            echo '</ul>';
        }
    }
}

function bookswp_goodreads_link($post, $linktext='View on Goodreads') {
    $isbn13 = get_post_meta($post->ID, 'isbn13', true);
    $q = NULL;
    if(!empty($isbn13)) {
        $q = $isbn13;
    } else {
        $q = $post->post_title;
    }
    return '<a class="goodreads-link" href="https://www.goodreads.com/search?q=' . 
                urlencode($q) . '">' . $linktext . '</a>';
}


// echo the quick add form
function bookswp_quick_add_book_form() {
    ?>
<div id="bookswp-quickadd">
<form action="<?php echo admin_url('post-new.php') ?>" method="GET">
    <input type="hidden" name="post_type" value="book"/>
    <label class="prompt" for="booktitle">Title or ISBN</label>
    <input id="booktitle" type="text" name="booktitle" 
           value="<?php echo esc_attr($_GET['booktitle']); ?>"
           oninput="isbn13check();"/>
    <input class="button" type="submit" value="Add Book">
    <script type="text/javascript">
        isbn13check();
    </script>
</form>
</div>
    <?php
}

// We need some CSS to position the paragraph
function bookswp_quick_add_css() {
	?>
	<style type='text/css'>
        <?php if(is_admin()) : ?>
	#bookswp-quickadd {
		float: right;
		padding-right: 15px;
		padding-top: 2px;		
		margin: 0;
		font-size: 12px;
	}
        #bookswp-quickadd label {
                vertical-align: middle;
		padding: 4px;		
		margin: 0;
		font-size: 12px;
                color: #72777c;
	}
        #bookswp-quickadd .button {
		font-size: 12px;
                padding: 0 5px 1px;
	}
        #bookswp-quickadd input {
            vertical-align: middle;
            font-size: 12px;
        }
        <?php else: ?>
        #bookswp-quickadd label {
             display: none;
	}
        <?php endif; ?>
	</style>
        <script type="text/javascript">
        function isbn13check() {
            var e = document.getElementById("booktitle");
            var isbn = e.value.replace(/[\s-]/g, '');
            var correct = false;
            if(isbn.length == 10) {
                var sum=0;
                for(var i=0; i<9; i++) {
                    sum += parseInt(isbn.substring(i, i+1))*(10-i);
                }
                var check = isbn.substring(9, 10).toUpperCase();
                if(sum == 0) {
                    correct = false;
                } else if(check == "X") {
                    correct = (sum+10) % 11 == 0;
                } else if(!isNaN(parseInt(check))) {
                    correct = (sum+parseInt(check)) % 11 == 0;
                }
            } else if(isbn.length == 13) {
                var sum=0;
                for(var i=0; i<12; i++) {
                    if(i%2==0) {
                        sum += parseInt(isbn.substring(i, i+1))*1;
                    } else {
                        sum += parseInt(isbn.substring(i, i+1))*3;
                    }
                }
                var check = parseInt(isbn.substring(12, 13));
                correct = !isNaN(check) && (sum != 0) && ((sum + check) % 10 == 0);
            } 
            
            if(correct) {
                e.style.color = 'green';
            } else {
                e.style.color = 'initial';
            }
        }
        </script>
        <?php
}

class QuickAdd_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'quickaddbook',
			'description' => 'Quickly add a book by title or ISBN',
		);
		parent::__construct( 'quickaddbook', 'Book Quick Add', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
	    // outputs the content of the widget only if user is an admin
            if ( current_user_can('edit_posts') ) {
                //The user has the "administrator" role
                echo $args['before_widget'];
                if ( ! empty( $instance['title'] ) ) {
                        echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
                }		
                bookswp_quick_add_book_form();
                echo $args['after_widget'];
            }
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
            // outputs the options form on admin
            $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
            ?>
            <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
	    // processes widget options to be saved
            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

            return $instance;
	}
}
