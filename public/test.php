<?php
if (is_writable('/Applications/XAMPP/xamppfiles/htdocs/cms_project/uploads')) {
    echo "The directory is writable.";
} else {
    echo "The directory is NOT writable.";
}
?>