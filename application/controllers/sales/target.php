<?php
$QArea = new Application_Model_Area();
$this->view->areas = $QArea->get_cache();