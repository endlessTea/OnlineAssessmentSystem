<?php

/**
 * 	Home Controller
 */
class Home extends Controller
{
    /**
     * 	PAGE: index
     */
    public function index() {
	
        // load views & pass resources
        Functions::render(get_class(), __FUNCTION__);
    }

}
