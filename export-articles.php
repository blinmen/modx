<?php
$output = [];

$resources = $modx->getCollection('modResource', array(
    'parent' => 4,  // Parent resouse ID (for example, articles place in the resource "Articles" with ID 4
    'published' => 1,  // Only published resourses
    'deleted' => 0,    // Not trashed
));

foreach ($resources as $resource) {
   
   $output[] = [
       'pagetitle' => $resource->get('pagetitle'),
       'introtext' => $resource->get('introtext'),
       'alias' => $resource->get('alias'),
       'uri' => $resource->get('uri'),
       'content' => $resource->get('content'),
       'publishedon' => $resource->get('publishedon'),
       'seo_title' => $resource->getTVValue('seo_title'),
       'seo_kw' => $resource->getTVValue('seo_KW'),
       'seo_description' => $resource->getTVValue('seo_description'),
       'post_author' => $resource->getTVValue('news_autor'),
       'news_authors' => $resource->getTVValue('news_autors'),
       'featured_image' => $resource->getTVValue('news_image'),
       'post_tags' => $resource->getTVValue('tags'),
   ];
  
}

$outputJSON = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

file_put_contents('export.json', $outputJSON);

return;
