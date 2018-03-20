<?php

/**
* Get the header navigation top-level items
*/
function getHeaderLinks(){
    return \App\Navigation::where(['useful_link' => 0, 'parent_id' => null])->orderBy('sort_order', 'ASC')->get();
}

/**
* Get the footer navigation top-level items (useful links)
*/
function getFooterLinks(){
    return \App\Navigation::where(['useful_link' => 1, 'parent_id' => null])->orderBy('sort_order', 'ASC')->get();
}