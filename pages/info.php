<!-- <?php
// phpinfo();
?> -->

<?php
// Create a blank image
$image = imagecreate(100, 100);

// Allocate a color for the image
$bgColor = imagecolorallocate($image, 0, 0, 0);

// Output the image as a PNG
header('Content-Type: image/png');
imagepng($image);

// Free up memory
imagedestroy($image);
?>
