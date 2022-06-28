Render a 3d view of a Minecraft skin using PHP and adding cosmetic overlays (such as sunglasses).
To add a cosmetic, create a "cosmetics" folder and put the skin that you want to use as an overlay in that directory.

Example use:
Add a transparent skin with sunglasses to the "cosmetics" folder and use the "cosmetic=sunglasses" parameter to apply the sunglasses as a overlay to your skin.

forked from https://github.com/Gyzie/php-Minecraft-3D-Skin-Renderer

php Minecraft 3D Skin Renderer
=====================

Render a 3D view of a Minecraft skin using PHP.

Project first developed by <a href="https://github.com/supermamie/php-Minecraft-3D-skin" target="_blank">supermamie</a>. Later transalated to English by <a href="https://github.com/cajogos/php-Minecraft-3D-Skin-Renderer" target="_blank">cajogos</a>.
My goal was to fix some issues and hopefully create full support for the 1.8 skins (1.8 support is partly done).

*I'm no longer working on this project. I will however look into your pull-requests.*

### Example of URL:
The URL containing most of the options:<br/>
`http://example.com/3d.php?vr=-25&hr=-25&hrh=10&vrla=5&vrra=-2&vrll=-20&vrrl=2&ratio=12&format=png&displayHair=true&headOnly=false&user=Notch`<br/>
Note: The old parameters by supermamie will still work.

With less parameters:<br/>
`http://example.com/3d.php?user=Notch&hrh=-20&aa=true`<br/>
This example will only set the user to Notch, head rotation to -20 and AA (image smoothing) to true. You can add parameters by adding `&<parameter>=<value>` to the end of your URL.

### Parameters
Supermamie's old parameters will still work.

Parameters are now optional (exept for `user`), so you can now only add those you need.

- `user` = Minecraft's username for the skin to be rendered. Required
- `vr` = Vertical Rotation `-25 by default`
- `hr` = Horizontal Rotation `35 by default`
- `hrh` = Horizontal Rotation Head `0 by default`
- `vrll` = Vertical Rotation Left Leg `0 by default`
- `vrrl` = Vertical Rotation Right Leg `0 by default`
- `vrla` = Vertical Rotation Left Arm `0 by default`
- `vrra` = Vertical Rotation Right Arm `0 by default`
- `displayHair` = Either or not to display hairs. Set to "false" to NOT display hairs. `true by default`
- `headOnly` = Either or not to display the ONLY the head. Set to "true" to display ONLY the head (and the hair, based on displayHair). `false by default`
- `format` = The format in which the image is to be rendered. PNG ("png") is used by default. Set to "svg" to use a vector version and "base64" for an encoded base64 string of the png image. `png by default`
- `ratio` = The size of the "png" image. The default and minimum value is 2. `12 by default`
- `aa` = Anti-aliasing (Not real AA, fake AA). When set to "true" the image will be smoother. `false by default`
- `layers` = Apply extra skin layers. `true by default`

### Using it as class
You can use the script for direct browser output (via the URL method as mentioned above), but also as a class for your scripts. Example:

```php
include_once realpath(dirname(__FILE__) . '/3d.php');
	
$player = new render3DPlayer('Notch', '-25', '-25', '10', '5', '-2', '-20', '2', 'true', 'false', 'png', '12', 'true', 'true'); //render3DPlayer(user, vr, hr, hrh, vrll, vrrl, vrla, vrra, displayHair, headOnly, format, ratio, aa, layers)
$png = $player->get3DRender();
echo "<br/>====<br/>PNG:<br/>====<br/>";
echo $png; // TrueColor image

$player = new render3DPlayer('Notch', '-25', '-25', '10', '5', '-2', '-20', '2', 'true', 'false', 'base64', '12', 'true', 'true'); //render3DPlayer(user, vr, hr, hrh, vrll, vrrl, vrla, vrra, displayHair, headOnly, format, ratio, aa, layers)
$base64 = $player->get3DRender();
echo "<br/>========<br/>Base 64:<br/>========<br/>";
echo $base64; // Base64 string

$player = new render3DPlayer('Notch', '-25', '-25', '10', '5', '-2', '-20', '2', 'true', 'false', 'svg', '12', 'true', 'true'); //render3DPlayer(user, vr, hr, hrh, vrll, vrrl, vrla, vrra, displayHair, headOnly, format, ratio, aa, layers)
$svg = $player->get3DRender();
echo "<br/>====<br/>SVG:<br/>====<br/>";
echo $svg; // SVG String

// As above (svg example) but with a locally provided file
$player = new render3DPlayer('', '-25', '-25', '10', '5', '-2', '-20', '2', 'true', 'false', 'svg', '12', 'true', 'true', 'someskinfile.png');
$svg = $player->get3DRender();
echo "<br/>====<br/>SVG:<br/>====<br/>";
echo $svg; // SVG String

```

### Changes Made
- Fixed dark blue skins;
- Fixed not working SVG images (Bug in cajogos fork);
- Fixed non-transparent PNG images rendering incorrect (Fix is a bit experimental);
- Fixed incorrect rendering texture parts;
- Made the old parameters by supermamie work again;
- Made 1.8 skins work;
- Made 1.8 skins base layers render;
- Added QUICK fix for 1.8 extra skin layers;
- Added ability to output an encoded base64 string of the image;
- Added optional AA (image smoothing) parameter;
- Added UUID support. (Mojang does not want too many UUID requests so it might fail when you use it a lot);
- Reformatted the entire code;
- Made it possible to use the script as class;
- Made all parameters optional;
- Made Steve the fallback image.
