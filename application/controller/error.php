<?php

/**
 * 	Class Error
 */
class Error extends Controller
{
    /**
     * PAGE: index (404 by default)
     */
    public function index()
    {
        // load views
        Functions::render(get_class(), __FUNCTION__);
    }
}
