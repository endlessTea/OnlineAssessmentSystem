<?php

/**
 * 	Visualisation Controller
 */
class Visualisation extends Controller
{
    /**
     * 	PAGE: index
     */
    public function index() {

        // load views & pass resources
        Functions::render(get_class(), __FUNCTION__);
    }

    /**
     * 	PAGE: sample 1
     *  Pie chart with sample transitions
     */
    public function sample1() {

        // load views & pass resources
        Functions::render(get_class(), __FUNCTION__);
    }

}
