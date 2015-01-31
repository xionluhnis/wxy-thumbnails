# wxy-thumbnails
Plugin for wxy to generate thumbnails of images, with a caching strategy

## Url routing
The plugin reroutes urls of the form `$base_url/img_route@wxh` to a thumbnail
with url `$base_url/.thumbnails/wxh@route`.

A w by h thumbnail is generated (and then cached, so that it only gets processed once).
The route is transformed so that slashes are replaced by @ characters.

## Configuration

```php
$config['thumb_dir'] = '.thumbnails'; // this is the default value
```

## Dependency
This plugin relies on ImageMagick which must therefore be installed for php.
