<?php
/**
 * DataProvider.php
 */

namespace Examples\Blog\Schema;

class DataProvider
{
    public static function getPost($id): array
    {
        return [
            "id"        => "post-" . $id,
            "title"     => "Post " . $id . " title",
            "summary"   => "This new GraphQL library for PHP works really well",
            "status"    => 1,
            "likeCount" => 2
        ];
    }

    public static function getBanner($id): array
    {
        return [
            'id'        => "banner-" . $id,
            'title'     => "Banner " . $id,
            'imageLink' => "banner" . $id . ".jpg"
        ];
    }
}
