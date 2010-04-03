<?php

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}


$wgHooks['ParserFirstCallInit'][] = 'efAddCatStyleTag';
function efAddCatStyleTag( &$parser ) {
    $parser->setHook( 'catstyle', 'efCatStyleTrigger' );
    return true;
}

function efCatStyleTrigger( $input, $args, $parser, $frame = NULL ) {
    global $wgHooks, $wgCategorySettings;
    $parser->disableCache();
    if(!empty($args['nocols']))
        $wgCategorySettings['short'] = true;
    if(!empty($args['nocaps']))
        $wgCategorySettings['captions'] = false;
    if(!empty($args['noheads']))
        $wgCategorySettings['heads'] = false;
    return '';
}

function efCatStyleArticleFromTitle( &$title, &$article ) {
    if(is_subclass_of($article, "CategoryPage")) {
        global $wgCatStyleBase;
        $tmp = str_replace('CategoryPage', 'CategoryViewer', get_class($article));
        if(class_exists($tmp))
            $wgCatStyleBase = $tmp;
    }
    if ( $title->getNamespace() == NS_CATEGORY ) {
        $article = new CatStyleCategoryPage( $title );
    }
    return true;
}

$wgAutoloadClasses['CatStyleCategoryPage'] = dirname(__FILE__) . '/CatStyle.body.php';
$wgAutoloadClasses['CatStyleCategoryViewer'] = dirname(__FILE__) . '/CatStyle.body.php';
$wgHooks['ArticleFromTitle'][] = 'efCatStyleArticleFromTitle';

$wgCatStyleBase = 'CategoryViewer';
$wgCategorySettings = array(
  'short'    => false,
  'captions' => true,
  'heads'    => true
  );
?>
