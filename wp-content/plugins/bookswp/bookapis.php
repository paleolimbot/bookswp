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
        <key><![CDATA[HSkIMuOGlxFIOmfBCGFVA]]></key>    
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

function bookswp_goodreads_parse_book_node($node) {
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

function bookswp_goodreads_parse($source) {
    $dom = new DOMDocument();
    @$dom->load($source); // suppresss not found errors
    if($dom->hasChildNodes()) {
        foreach($dom->firstChild->childNodes as $domNode) {
            if($domNode->nodeName == 'book') {
                return bookswp_goodreads_parse_book_node($domNode);
            }
        }
    }
    return NULL;
}

/*
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <meta charset="utf-8">
</head>
<body>
<?php

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<pre>';
$res = bookswp_goodreads_parse('https://www.goodreads.com/book/isbn?isbn=9780439064866&key=HSkIMuOGlxFIOmfBCGFVA');
var_dump($res);
echo '</pre>';
?>
</body>
 * 
 */