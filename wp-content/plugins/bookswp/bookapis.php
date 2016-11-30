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

function bookswp_do_goodreads_lookup($post) {
    if($post->post_type == 'book' && !$post->post_title && !$post->post_content) {
        $apikey = get_option('bookswp_goodreads_api', '');
        $books = NULL;
        if($_GET['isbn'] && $apikey) {
            $books = bookswp_get_goodreads_by_isbn($_GET['isbn'], $apikey);
        } else if($_GET['booktitle'] && $apikey) {
            $books = bookswp_get_goodreads_by_title($_GET['booktitle'], $apikey);
        }
        if(empty($books)) {
            if(empty($apikey)) {
                echo '<div>Goodreads API key required for Goodreads lookup.</div>';
            } else if($_GET['isbn']) {
                echo '<div>Goodreads lookup failed for ISBN "'. $_GET['isbn'] . '"</div>';
            } else if($_GET['booktitle']) {
                echo '<div>Goodreads lookup failed for title "'. $_GET['booktitle'] . '"</div>';
            }
        } else if($books['error']) {
            if($_GET['isbn']) {
                echo '<div>Goodreads lookup failed for ISBN "'. $_GET['isbn'] . '"' . $books['error'] . '</div>';
            } else if($_GET['booktitle']) {
                echo '<div>Goodreads lookup failed for title "'. $_GET['booktitle'] . '"' . $books['error'] . '</div>';
            }
        } else if(count($books) == 1) {
            $book = $books[0];
            $post->post_title = $book['title'];
            $post->post_content = $book['description'];
            if($book['authors']) add_post_meta($post->ID, 'author', implode('/', $book['authors']));
            if($book['isbn13']) add_post_meta($post->ID, 'isbn13', $book['isbn13']);
            if($book['isbn']) add_post_meta($post->ID, 'isbn10', $book['isbn']);
            if($book['publisher']) add_post_meta($post->ID, 'publisher', $book['publisher']);
            if($book['publication_year']) add_post_meta($post->ID, 'publication_year', $book['publication_year']);
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
                        echo '<!--' . var_dump($newterm) . '-->';
                    }
                }       
            }
            wp_set_post_terms($post->ID, $term_ids, $taxonomy='people');
            echo '<div>Goodreads lookup succeeded.</div>';
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
    <input id="booktitle" type="text" name="booktitle"/>
    <input class="button" type="submit" value="New Book">
</form>
</div>
    <?php
}

add_action( 'admin_notices', 'bookswp_quick_add_book_form' );

// We need some CSS to position the paragraph
function bookswp_quick_add_css() {
	?>
	<style type='text/css'>
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
	</style>
        <?php
}
add_action( 'admin_head', 'bookswp_quick_add_css' );
