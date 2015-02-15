# wxy-thumbnails
Plugin for wxy to generate thumbnails of images, with a caching strategy

## Url routing
The plugin reroutes urls of the form `$base_url/img_route@wxh` to a thumbnail
with url `$base_url/.thumbnails/wxh@route`.

A `w` by `h` thumbnail is generated (and then cached, so that it only gets processed once).
The route is transformed so that slashes are replaced by @ characters.

## Access requirements
The *thumbnail directory* needs read-write access (for the page handler).

Auto-orientation for both the *image and thumbnail* (`2`) requires an additional read-write
access the original image itself.

## Configuration

```php
$config['thumbnail_dir']    = '.thumbnails'; // this is the default value
// thumbnail orientation:
//  0 = no auto-orientation
//  1 = auto-orientation of the thumbnail
//  2 = auto-orientation of both the image and thumbnail
$config['thumbnail_orient'] = 0;
```

## Dependency
This plugin relies on ImageMagick which must therefore be installed for php.
