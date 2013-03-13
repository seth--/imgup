        <div class="container">
            <h1>imgup</h1>
            <div class="pagination-centered row">
                <div class="span1"></div>
                <div class="span10">
                    <ul class="thumbnails">
                    <?PHP
                        foreach ($gallery_images as $gallery_image) {
                            echo '  <li class="span3">';
                            echo '      <a href="' . $_CONTROLLER['BASE_DIR'] . '/f/' . urlencode($gallery_image['id']) . '/' . urlencode($gallery_image['filename']) . '" class="thumbnail">';
                            echo '          <img src="' . $_CONTROLLER['BASE_DIR'] . '/f/' . urlencode($gallery_image['id']) . '/' . urlencode($gallery_image['filename']) . '">';
                            echo '      </a>';
                            echo '  </li>';
                        }
                    ?>
                    </ul>
                </div>
                <div class="span1"></div>
            </div>


