<?php
$wgHooks['ParserFirstCallInit'][] = 'wfAddCatStyleTag';
function wfAddCatStyleTag( &$parser ) {
    $parser->setHook( 'catstyle', 'wfCatStyleTrigger' );
    return true;
}

function wfCatStyleTrigger( $input, $args, $parser, $frame = NULL ) {
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

function wfHook($that) {
    $a = new ShortCategoryPage($that->mTitle, $that->mOldId);
    $a->view();
    return false;
}

$wgAutoloadClasses['ShortCategoryPage'] = dirname(__FILE__) . '/CatStyle.body.php';
$wgAutoloadClasses['ShortCategoryViewer'] = dirname(__FILE__) . '/CatStyle.body.php';
$wgHooks['CategoryPageView'][] = 'wfHook';

$wgCategorySettings = array(
  'short'    => false,
  'captions' => true,
  'heads'    => true
  );
?>
