# PHP Auto Image Resizer

PHP library to resize any image in any aspect ratio without cropping, stretching and losing the quality of the image by adding any color around the image.

## Installation

Download the files from [GitHub](https://github.com/AbdulMannaf/php-auto-image-resizer.git), put them to your project directory and require them into your PHP code.

```php
require_once "path/to/file/ImageResizer.php";
```

## Examples

Resize an image in 4:3 aspect ratio

```php
$img = new ImageResizer("path/to/sourceImage.jpg", "path/to/outputImage");
$img->setAspectRatio(4, 3); // 4:3 Aspect Ratio
```

Return PNG image string

```php
$img->getPNG();
```

Return JPG image string

```php
$img->getJPG();
```

Return GIF image string

```php
$img->getGIF();
```

Save PNG image

```php
$img->savePNG();
```

Save JPG image

```php
$img->saveJPG();
```

Save GIF image

```php
$img->saveGIF();
```

Change source image

```php
$img->setSourceImage("changeSourceImage.png");
```

Change output image

```php
$img->setOutputImage("changeOutputImage");
```

Change aspect ratio

```php
$img->setAspectRatio(16, 9); // 16:9 aspect ratio
```

Change Background Color

```php
$img->setBackgroundColor(110, 130, 150); // RGB Color Model.
```

Change Image Transparency

```php
// for PNG and GIF image
$img->setTransparentLevel(127);
// int [0-127]. default 0
```

Change Image Quality

```php
// for JPG image
$img->setImageQuality(100);
// int [0-100]. default 80
```

Change Image Compression Level

```php
// for PNG and GIF image
$img->setCompressionLevel(9);
// int [0-9]. default 5
```

Print Inline PNG

```php
echo $img->getInlinePNG();
```

Print Inline JPG

```php
echo $img->getInlineJPG();

```

Print Inline GIF

```php
echo $img->getInlineGIF();
```