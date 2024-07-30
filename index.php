<?php
    if (isset($_GET['comicnum'])) {
        $comicnum = $_GET['comicnum'];
    } else {
        // Handle the case where 'comicnum' is not provided
        $comicnum = null; // or set a default value if needed
    }

    // Function to create a low-resolution version of an image and save it as a JPEG
    function createLowResImage($srcPath, $destPath, $newWidth = 1024, $quality = 75) {
        // Get the image details
        list($width, $height, $type) = getimagesize($srcPath);
        
        // Calculate new dimensions
        $ratio = $width / $height;
        $newHeight = $newWidth / $ratio;
        
        // Create a new blank image with the new dimensions
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Load the source image based on its type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($srcPath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($srcPath);
                // Set the transparency of PNGs
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefill($newImage, 0, 0, $transparent);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($srcPath);
                // Set the transparency of GIFs
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefill($newImage, 0, 0, $transparent);
                break;
            default:
                return false;
        }
        
        // Resize the image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save the new image as JPEG
        imagejpeg($newImage, $destPath, $quality);
        
        // Clean up
        imagedestroy($newImage);
        imagedestroy($sourceImage);
        
        return true;
    }

    // Main script logic
    $subDir = array();
    $directories = array_filter(glob("comics/*"), 'is_dir');
    $subDir = array_merge($subDir, $directories);
    sort($subDir);

    if ($comicnum == "") $comicnum = count($subDir) - 1;
    $previous = $comicnum - 1;
    $next = $comicnum + 1;
    $comic = $subDir[$comicnum];
    
    $host_url = "https://$_SERVER[HTTP_HOST]";
    $comic_url = $host_url . "/?comicnum=" . $comicnum;
    $full_comic = $host_url . "/" . $comic . "/full.jpg";
    
    $meta_path = $comic . "/meta.php";
    @include($meta_path);

?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="All Flesh Is Like Grass - <?php echo $meta_title; ?>" />
        <meta property="og:description" content="All Flesh Is Like Grass - <?php echo $meta_description; ?>" />
        <meta property="og:url" content="<?php echo $comic_url; ?>" />
        <meta property="og:image" content="<?php echo $full_comic; ?>" />
        <meta property="og:image:alt" content="Comic Image" />
        <meta property="og:image:type" content="image/jpeg" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="All Flesh Is Like Grass" />
        
        <!-- Twitter Card data -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="<?php echo $comic_url; ?>">
        <meta name="twitter:title" content="All Flesh Is Like Grass - <?php echo $meta_title; ?>">
        <meta name="twitter:description" content="All Flesh Is Like Grass - <?php echo $meta_title; ?>">
        <meta name="twitter:image" content="<?php echo $full_comic; ?>">
        
        <!-- <meta http-equiv="Refresh" content="0; url='https://www.instagram.com/afilgcomic/'" /> -->
        <title>All Flesh Is Like Grass - <?php echo $meta_title; ?></title>
        <script src="https://kit.fontawesome.com/8c81581c71.js" crossorigin="anonymous"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <link href="css/style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <div class="container">
        <div class="image-container">
                <img src="img/title.png" alt="Title Image"/>
            </div>
            <div class="icons-container">
                <span>
                    <a href="mailto:afilgcomic@gmail.com" alt="Email Link"><i class="fas fa-envelope"></i></a>
                </span>
                <span>
                    <a href="http://www.instagram.com/afilgcomic" target="_blank" alt="Instagram Link"><i class="fab fa-instagram-square"></i></a>
                </span>
            </div>
        </div>
<?php

    $lowQualityDir = $comic . "/lowquality";
    if (!is_dir($lowQualityDir)) {
        mkdir($lowQualityDir, 0755, true);
    }
    
    $dir = @opendir($comic);
    if ($dir) {
        $comicarray = [];
        while ($file = readdir($dir)) {
            // Skip current, parent, and lowquality directory
            if (($file != "..") && ($file != ".") && ($file != "lowquality") && ($file != "full.jpg") && ($file != "meta.php")) {
                $fullPath = $comic . "/" . $file;
                $lowResPath = $lowQualityDir . "/" . pathinfo($file, PATHINFO_FILENAME) . ".jpg";

                // Check if the low-resolution image exists
                if (!file_exists($lowResPath)) {
                    // Create the low-resolution image
                    createLowResImage($fullPath, $lowResPath);
                }
                
                $comicarray[] = $file;
            }
        }
        closedir($dir);

        $container = "container3";
        if (count($comicarray) % 4 == 0) $container = "container4";
        if (count($comicarray) == 4) $container = "container2";
        sort($comicarray);
    }

    $navmen = "<div class=\"navbar\">";
    if ($comicnum > 0) $navmen = $navmen . "<a href=\"?page=comics&amp;comicnum=0\"><div class=\"nav navFirst\">First</div></a>";
    if ($comicnum > 0) $navmen = $navmen . "<a href=\"?page=comics&amp;comicnum=$previous\"><div class=\"nav navPrev\">Previous</div></a>";
    if ($comicnum < count($subDir) - 1) $navmen = $navmen . "<a href=\"?page=comics&amp;comicnum=$next\"><div class=\"nav navNext\">Next</div></a>";
    if ($comicnum < count($subDir) - 1) $navmen = $navmen . "<a href=\"?page=comics\"><div class=\"nav navLast\">Last</div></a>";
    $navmen = $navmen . "</div>";
    echo "$navmen";
    echo "<div id=\"$container\">";
    
    foreach ($comicarray as $v) {
        $fulladdress = $lowQualityDir . "/" . pathinfo($v, PATHINFO_FILENAME) . ".jpg";
        echo "<img src=\"$fulladdress\">";
    }
    echo "</div>";
?>
    <div class="parent-container">
        <div class="share-bar">
            SHARE:
            <!-- Facebook Share -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($comic_url); ?>" target="_blank" title="Share on Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <!-- Twitter Share -->
            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($meta_title); ?>&url=<?php echo urlencode($comic_url); ?>" target="_blank" title="Share on Twitter">
                <i class="fab fa-twitter"></i>
            </a>
            <!-- Reddit Share -->
            <a href="https://www.reddit.com/submit?url=<?php echo urlencode($comic_url); ?>&title=<?php echo urlencode($meta_title); ?>" target="_blank" title="Share on Reddit">
                <i class="fab fa-reddit"></i>
            </a>
            <!-- LinkedIn Share -->
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($comic_url); ?>&title=<?php echo urlencode($meta_title); ?>&summary=<?php echo urlencode($meta_description); ?>" target="_blank" title="Share on LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <!-- Pinterest Share -->
            <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode($comic_url); ?>&media=<?php echo urlencode($full_comic); ?>&description=<?php echo urlencode($meta_description); ?>" target="_blank" title="Pin on Pinterest">
                <i class="fab fa-pinterest"></i>
            </a>
            <!-- Email Share -->
            <a href="mailto:?subject=Check%20out%20this%20webcomic&body=<?php echo urlencode($comic_url); ?>" target="_blank" title="Share via Email">
                <i class="fas fa-envelope"></i>
            </a>
        </div>
    </div>

<?php
    echo "$navmen";
?>
        <hr style="width:80%;text-align:center;margin:15px auto 15px auto;color:#444;background-color:#444;border-width:0px; height:2px" />
        <p>&nbsp;</p>
    </body>
</html>