<?php
class ShortCategoryPage extends CategoryPage {
    function view() {
        global $wgHooks;
        $wgHooks['CategoryPageView'] = array_filter($wgHooks['CategoryPageView'], create_function('$val', 'return $val != "wfHook";'));
        parent::view();
        return false;
    }

    function closeShowCategory() {
        global $wgOut, $wgRequest;
        $from = $wgRequest->getVal( 'from' );
        $until = $wgRequest->getVal( 'until' );

        $viewer = new ShortCategoryViewer( $this->mTitle, $from, $until );
        $wgOut->addHTML( $viewer->getHTML() );
    }
}

class ShortCategoryViewer extends CategoryViewer {
    protected $cat;

    function __construct( $title, $from = '', $until = '' ) {
        parent::__construct( $title, $from, $until );
        $this->cat = Category::newFromTitle( $title );
    }

    function formatList( $articles, $articles_start_char, $cutoff = 6 ) {
        global $wgCategorySettings;
        if( !$wgCategorySettings['short'] )
            return parent::formatList( $articles, $articles_start_char, $cutoff );

        if( count($articles) > 0 )
            return $this->shortList( $articles, $articles_start_char );
        return '';
    }

    function getPagesSection() {
        global $wgCategorySettings;
        if($wgCategorySettings['heads'])
            return parent::getPagesSection();

        $ti = htmlspecialchars( $this->title->getText() );
        # Don't show articles section if there are none.
        $r = '';

        $rescnt = count( $this->articles );

        if( $rescnt > 0 ) {
            $r = "<div id=\"mw-pages\">\n";
            $r = $this->formatList( $this->articles, $this->articles_start_char );
            $r .= "\n</div>";
        }
        return $r;
    }

    function getSubcategorySection() {
        global $wgCategorySettings;
        if($wgCategorySettings['heads'])
            return parent::getSubcategorySection();

        # Don't show subcategories section if there are none.
        $r = '';
        $rescnt = count( $this->children );
        if( $rescnt > 0 ) {
            $r .= "<div id=\"mw-subcategories\">\n";
            $r .= $this->formatList( $this->children, $this->children_start_char );
            $r .= "\n</div>";
        }
        return $r;
    }

    function getImageSection() {
        if( $this->showGallery && ! $this->gallery->isEmpty() ) {
            return "<div id=\"mw-category-media\">\n" .
            $this->gallery->toHTML() . "\n</div>";
        } else {
            return '';
        }
    }

    function columnList( $articles, $articles_start_char ) {
        global $wgCategorySettings;
        if($wgCategorySettings['captions'])
            return parent::columnList($articles, $articles_start_char);

        // divide list into three equal chunks
        $chunk = (int) (count ( $articles ) / 3);

        // get and display header
        $r = '<table width="100%"><tr valign="top">';

        $prev_start_char = 'none';

        // loop through the chunks
        for($startChunk = 0, $endChunk = $chunk, $chunkIndex = 0;
            $chunkIndex < 3;
            $chunkIndex++, $startChunk = $endChunk, $endChunk += $chunk + 1)
        {
            $r .= "<td>\n";
            $atColumnTop = true;

            // output all articles in category
            for ($index = $startChunk ;
                $index < $endChunk && $index < count($articles);
                $index++ )
            {
                if($atColumnTop)
                {
                    $r .= "<ul>";
                    $atColumnTop = false;
                }
                $r .= "<li>{$articles[$index]}</li>";
            }
            if( !$atColumnTop ) {
                $r .= "</ul>\n";
            }
            $r .= "</td>\n";


        }
        $r .= '</tr></table>';
        return $r;
    }

    function shortList( $articles, $articles_start_char ) {
        global $wgCategorySettings;
        if($wgCategorySettings['captions'])
            return parent::shortList($articles, $articles_start_char);

        $r = '<ul><li>'.$articles[0].'</li>';
        for ($index = 1; $index < count($articles); $index++ )
            $r .= "<li>{$articles[$index]}</li>";
        $r .= '</ul>';
        return $r;
    }
}

?>
